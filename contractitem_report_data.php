<?php

// 畫面與 Excel 使用相同的月份、查詢與薪資計算。
function contractitem_report_month($month)
{
    if (!is_string($month) || !preg_match('/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D', $month)) {
        return date('Y-m');
    }

    return $month;
}

function contractitem_report_rows($mDB, $contract_id, $annual_month)
{
    $annual_month = contractitem_report_month($annual_month);
    $start = new DateTime($annual_month . '-01');
    $end = clone $start;
    $end->modify('last day of this month');
    $start_date = $start->format('Y-m-d');
    $end_date = $end->format('Y-m-d');

    // 以十六進位字串傳入合約編號，避免引號改變 SQL 條件。
    $contract_id_sql = "X'" . bin2hex(is_string($contract_id) ? $contract_id : '') . "'";
    $Qry = "SELECT
        a.dispatch_date,
        a.contract_id,
        b.seq,
        f.work_project,
        b.status,
        b.actual_qty,
        b.remark,
        f.unit_price,
        f.unit,
        COUNT(DISTINCT c.employee_id) AS employee_count,
        SUM(LEAST(COALESCE(c.attendance_hours, 0), 8)) AS attendance_hours,
        SUM(GREATEST(COALESCE(c.attendance_hours, 0) - 8, 0)) AS attendance_overhours,
        GROUP_CONCAT(DISTINCT d.employee_name ORDER BY d.employee_name SEPARATOR '、') AS employees,
        GROUP_CONCAT(DISTINCT JSON_OBJECT(
            'employee_id', c.employee_id,
            'hours', COALESCE(c.attendance_hours, 0),
            'type', d.SalaryType,
            'salary', d.salary
        )) AS salary_detail
    FROM dispatch a
    LEFT JOIN dispatch_contract_details b
           ON b.dispatch_id = a.dispatch_id AND b.contract_id = a.contract_id
    LEFT JOIN dispatch_attendance_sub c
           ON c.dispatch_id = b.dispatch_id AND c.contract_id = b.contract_id AND c.seq = b.seq
    LEFT JOIN employee d ON d.employee_id = c.employee_id
    LEFT JOIN contract_details f ON f.contract_id = b.contract_id AND f.seq = b.seq
    WHERE a.dispatch_date BETWEEN '$start_date' AND '$end_date'
      AND a.contract_id = $contract_id_sql
    GROUP BY a.dispatch_date, a.contract_id, b.seq, b.status, b.actual_qty,
             b.remark, f.unit_price, f.unit, f.work_project
    ORDER BY a.dispatch_date, b.seq";

    $mDB->query($Qry);
    $rowsByDate = [];
    while ($row = $mDB->fetchRow(2)) {
        $actual_qty = is_numeric($row['actual_qty']) ? (float)$row['actual_qty'] : '';
        $unit_price = is_numeric($row['unit_price']) ? (float)$row['unit_price'] : '';
        $attendance_overhours = is_numeric($row['attendance_overhours']) ? (float)$row['attendance_overhours'] : '';
        $regular_pay = 0;
        $overtime_pay = 0;

        $details = json_decode('[' . ($row['salary_detail'] ?? '') . ']', true);
        if (!is_array($details)) {
            $details = [];
        }

        foreach ($details as $employee) {
            $salaryType = $employee['type'] ?? '日薪';
            $salary = max((float)($employee['salary'] ?? 0), 0);
            $hours = max((float)($employee['hours'] ?? 0), 0);
            $day_rate = $salaryType === '月薪' ? round($salary / 30, 2) : $salary;
            $days = round($hours / 8, 2);
            $regular_pay += round($days * $day_rate, 2);
            $overtime_rate = round($day_rate / 6, 2);
            $overtime_pay += round(max($hours - 8, 0) * $overtime_rate, 2);
        }

        // 保留 DataTable 原有的 16 欄資料格式；第 12 欄為未顯示的出勤天數。
        $rowsByDate[$row['dispatch_date']][] = [
            $row['dispatch_date'],
            $row['remark'],
            $row['seq'],
            $row['work_project'],
            $actual_qty,
            $row['unit'],
            $unit_price,
            round((float)$actual_qty * (float)$unit_price, 2),
            $row['status'] ?? '施工中',
            $row['employees'],
            (int)$row['employee_count'],
            null,
            $regular_pay,
            $attendance_overhours,
            $overtime_pay,
            $regular_pay + $overtime_pay,
        ];
    }

    $rows = [];
    for ($current = clone $start; $current <= $end; $current->modify('+1 day')) {
        $date = $current->format('Y-m-d');
        if (!empty($rowsByDate[$date])) {
            foreach ($rowsByDate[$date] as $row) {
                $rows[] = $row;
            }
        } else {
            $row = array_fill(0, 16, '');
            $row[0] = $date;
            $rows[] = $row;
        }
    }

    return $rows;
}
