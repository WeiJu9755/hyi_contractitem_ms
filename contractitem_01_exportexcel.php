<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', false);
ini_set('display_startup_errors', false);
date_default_timezone_set('Asia/Taipei');

@include_once '/website/include/pub_function.php';
$site_db = 'eshop';
@include_once('/website/class/' . $site_db . '_info_class.php');
require_once __DIR__ . '/contractitem_report_data.php';
require_once '/website/os/PHPExcel-1.8.1/Classes/PHPExcel.php';

$contract_id = $_GET['contract_id'] ?? '';
$contract_id = is_string($contract_id) ? $contract_id : '';
$annual_month = contractitem_report_month($_GET['annual_month'] ?? '');

$mDB = new MywebDB();
$rows = contractitem_report_rows($mDB, $contract_id, $annual_month);
$contract_caption = '';
if ($contract_id !== '') {
    $contract_id_sql = "X'" . bin2hex($contract_id) . "'";
    $mDB->query("SELECT contract_caption FROM contract WHERE contract_id = $contract_id_sql");
    if ($contract = $mDB->fetchRow(2)) {
        $contract_caption = $contract['contract_caption'];
    }
}
$mDB->remove();

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()
    ->setCreator('PowerSales')
    ->setLastModifiedBy('PowerSales')
    ->setTitle('Contract item monthly report')
    ->setSubject('Contract item monthly report')
    ->setCategory('Contract items');
$sheet = $objPHPExcel->getActiveSheet();
$sheet->setTitle('合約工項月報表');
$sheet->getDefaultStyle()->getFont()->setName('微軟正黑體')->setSize(10);
$sheet->mergeCells('A1:O1');
$sheet->setCellValue('A1', '合約工項月報表');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getRowDimension(1)->setRowHeight(30);

$sheet->mergeCells('A2:C2');
$sheet->setCellValue('A2', '月份：' . $annual_month);
$sheet->mergeCells('D2:J2');
$sheet->setCellValueExplicit('D2', '合約：' . $contract_caption, PHPExcel_Cell_DataType::TYPE_STRING);
$sheet->mergeCells('K2:O2');
$sheet->setCellValue('K2', '製表日期：' . date('Y-m-d'));
$sheet->getRowDimension(2)->setRowHeight(30);

// 資料索引 11（出勤天數）沒有顯示於月報表，因此略過。
$columns = [
    ['A', '日期', 14, 0, false],
    ['B', '實際工作內容', 36, 1, false],
    ['C', '項次', 8, 2, false],
    ['D', '合約項目', 36, 3, false],
    ['E', '數量', 12, 4, true],
    ['F', '單位', 8, 5, false],
    ['G', '單價', 14, 6, true],
    ['H', '複價', 16, 7, true],
    ['I', '工作狀態', 12, 8, false],
    ['J', '施工人員', 28, 9, false],
    ['K', '施工人數', 12, 10, true],
    ['L', '出工日薪', 16, 12, true],
    ['M', '加班時數/hr', 14, 13, true],
    ['N', '加班費', 16, 14, true],
    ['O', '出工小計', 16, 15, true],
];
foreach ($columns as $column) {
    $sheet->setCellValue($column[0] . '4', $column[1]);
    $sheet->getColumnDimension($column[0])->setWidth($column[2]);
}
$sheet->getRowDimension(4)->setRowHeight(30);
$sheet->getStyle('A4:O4')->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'E2EFDA'],
    ],
]);

$totals = ['H' => 0, 'K' => 0, 'L' => 0, 'M' => 0, 'N' => 0, 'O' => 0];
$rowNumber = 5;
foreach ($rows as $row) {
    foreach ($columns as $column) {
        list($letter, $title, $width, $index, $numeric) = $column;
        $value = $row[$index];
        $cell = $letter . $rowNumber;
        if ($numeric && is_numeric($value)) {
            $sheet->setCellValueExplicit($cell, (float)$value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            if (array_key_exists($letter, $totals)) {
                $totals[$letter] += (float)$value;
            }
        } else {
            // 文字以字串寫入，保留項次前導零，也避免工作內容被解讀成公式。
            $text = (string)$value;
            $sheet->setCellValueExplicit($cell, $text, PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }
    $sheet->getRowDimension($rowNumber)->setRowHeight(-1);
    $rowNumber++;
}

$sheet->setCellValue('A' . $rowNumber, '總計：');
foreach ($totals as $letter => $total) {
    $sheet->setCellValueExplicit($letter . $rowNumber, $total, PHPExcel_Cell_DataType::TYPE_NUMERIC);
}
$sheet->getRowDimension($rowNumber)->setRowHeight(30);
$sheet->getStyle('A' . $rowNumber . ':O' . $rowNumber)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'FFEBAC'],
    ],
]);
$sheet->getStyle('A4:O' . $rowNumber)->applyFromArray([
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
]);
$sheet->getStyle('A1:O' . $rowNumber)->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
    ->setWrapText(true);
foreach ($columns as $column) {
    if ($column[4]) {
        for ($numberRow = 5; $numberRow <= $rowNumber; $numberRow++) {
            $cell = $column[0] . $numberRow;
            $value = $sheet->getCell($cell)->getValue();
            // 整數使用無小數點格式，避免 Excel 顯示尾端的「.」。
            $format = is_numeric($value) && round((float)$value, 2) != round((float)$value)
                ? '#,##0.##'
                : '#,##0';
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($format);
        }
    }
}
$sheet->freezePane('D5');
$sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3)
    ->setFitToWidth(1)
    ->setFitToHeight(0)
    ->setRowsToRepeatAtTopByStartAndEnd(1, 4)
    ->setPrintArea('A1:O' . $rowNumber);

$filename = '合約工項月報表_' . $annual_month . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="contractitem_' . $annual_month . '.xls"; filename*=UTF-8\'\'' . rawurlencode($filename));
header('Cache-Control: max-age=0');
header('Pragma: public');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
