<?php


header('Content-Type: application/json; charset=utf-8');


//$site_db = $_GET['site_db'];
$site_db = "eshop";


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


$mDB = "";
$mDB = new MywebDB();
$mDB2 = "";
$mDB2 = new MywebDB();


$now = date("Y-m");

$select_contract_id = $_GET['contract_id'] ?? '';
$annual_month = $_GET['annual_month'] ?? date("Y-m");

// 建立起始日期（當月1號）
$start_date = new DateTime($annual_month . '-01');

// 複製一份用來算月底
$end_date = clone $start_date;
$end_date->modify('last day of this month');

// 轉成字串格式 Y-m-d
$start_date = $start_date->format('Y-m-d');
$end_date   = $end_date->format('Y-m-d');

// $start_date  = '2025-07-01';
// $end_date    = '2025-07-31';

// 一條 SQL 解決：以 dispatch 為日維度，左連到派工明細與出勤，再 group by
$Qry = "SELECT
  a.dispatch_date,
  a.contract_id,
  b.seq,
  f.work_project AS work_project,
  b.status,
  b.actual_qty,
  b.remark,
  f.unit_price,
  f.unit AS unit,
  COUNT(DISTINCT c.employee_id) AS employee_count,
  SUM(c.attendance_hours) AS attendance_hours,
  GROUP_CONCAT(DISTINCT d.employee_name ORDER BY d.employee_name SEPARATOR '、') AS employees
FROM dispatch a
LEFT JOIN dispatch_contract_details b
       ON b.dispatch_id = a.dispatch_id
      AND b.contract_id = a.contract_id
LEFT JOIN dispatch_attendance_sub c
       ON c.dispatch_id = b.dispatch_id
      AND c.contract_id = b.contract_id
      AND c.seq = b.seq
LEFT JOIN employee d
       ON d.employee_id = c.employee_id
LEFT JOIN contract_details f
       ON f.contract_id = b.contract_id AND f.seq = b.seq
WHERE a.ConfirmSending = 'Y'
  AND a.dispatch_date BETWEEN '$start_date' AND '$end_date'
  AND a.contract_id = '$select_contract_id'
GROUP BY a.dispatch_date, a.contract_id, b.seq
ORDER BY a.dispatch_date, b.seq;

";

$mDB->query($Qry);
// 常數
$HOURS_PER_DAY = 8;     // 每天 8 小時
$DAY_RATE      = 3000;  // 你要的天數*單價

$retval = [];
while ($row = $mDB->fetchRow(2)) {
    $dispatch_date    = $row['dispatch_date'];
    $contract_id      = $row['contract_id'];
    $seq              = $row['seq'];
    $work_project     = $row['work_project'];
    $status           = $row['status'];
    $actual_qty       = is_numeric($row['actual_qty']) ? (float)$row['actual_qty'] : 0.0;
    $unit_price       = is_numeric($row['unit_price']) ? (float)$row['unit_price'] : 0.0;
    $unit             = $row['unit'];
    $employee_count   = (int)$row['employee_count'];
    $employees        = $row['employees'];
    $remark           = $row['remark'];
    $attendance_hours = is_numeric($row['attendance_hours']) ? (float)$row['attendance_hours'] : 0.0;

    // 小時 -> 天數（四捨五入到小數 2 位）
    $attendance_days = round($attendance_hours / $HOURS_PER_DAY, 2);

    // 金額
    $total_price = round($actual_qty * $unit_price, 2);
    $sub_total   = round($attendance_days * $DAY_RATE, 2);

    $rowsByDate[$dispatch_date][] = [
        $dispatch_date,
        $contract_id,
        $seq,
        $work_project,
        $status,
        $actual_qty,
        $unit_price,
        $unit,
        $employee_count,
        $employees,
        $remark,
        $attendance_days,
        $total_price,
        $sub_total
    ];
}

// 依日期範圍逐日輸出；有幾筆就列幾筆，沒資料就補一筆只有日期
$retval = [];
$cur    = new DateTime($start_date);
$end    = new DateTime($end_date);

while ($cur <= $end) {
    $d = $cur->format('Y-m-d');

    if (!empty($rowsByDate[$d])) {
        foreach ($rowsByDate[$d] as $r) {
            $retval[] = $r; // 同日多筆都列出
        }
    } else {
        // 保持 14 欄：只有日期、其他空白
        $retval[] = [
            $d,  // dispatch_date
            '',  // contract_id
            '',  // seq
            '',  // work_project
            '',  // status
            '',  // actual_qty
            '',  // unit_price
            '',  // unit
            '',  // employee_count
            '',  // employees
            '',  // remark
            '',  // attendance_days
            '',  // total_price
            ''   // sub_total
        ];
    }

    $cur->modify('+1 day');
}



	
$mDB->remove();

echo json_encode([
    "data" => $retval
]);

?>