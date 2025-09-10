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



$contractId = '320130016201';
$startDate  = '2025-07-01';
$endDate    = '2025-07-31';

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
  AND a.dispatch_date BETWEEN '2025-07-01' AND '2025-07-31'
  AND a.contract_id = '320130016201'
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

    $retval[] = [
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
        $attendance_days, // 換算天數
        $total_price,
        $sub_total
    ];
}



	
$mDB->remove();

echo json_encode([
    "data" => $retval
]);

?>