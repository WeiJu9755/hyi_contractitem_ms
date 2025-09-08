<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2012 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.8, 2012-10-12
 */

/** Error reporting */
/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set("Asia/Taipei");
*/
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set("Asia/Taipei");


//載入公用函數
@include_once '/website/include/pub_function.php';


$site_db = "eshop";
$web_id = "sales.eshop";
$today = date("Y-m-d");
$get_status1 =  isset($_GET['status1']) ? $_GET['status1'] : '';
$get_status2 =  isset($_GET['status2']) ? $_GET['status2'] : '';
$ContractingModel = isset($_GET['ContractingModel']) ? $_GET['ContractingModel'] : '';
$region = isset($_GET['region']) ? $_GET['region'] : '';
$company_id = isset($_GET['company_id']) ? $_GET['company_id'] : '';

$mDB2 = "";
$mDB2 = new MywebDB();

if (!empty($company_id)) {
   $Qry2 = "SELECT company_id,company_name FROM company where company_id = '$company_id'";

    $mDB2->query($Qry2);
    while($row2 = $mDB2->fetchRow(2)){
        $company_name = $row2['company_name'];
    }
}




/*
//檢查是否為管理員及進階會員
$super_admin = "N";
$super_advanced = "N";
$mem_row = getkeyvalue2('memberinfo','member',"member_no = '$memberID'",'admin,advanced,checked,luck,admin_readonly,advanced_readonly');
$super_admin = $mem_row['admin'];
$super_advanced = $mem_row['advanced'];
*/

@include_once("/website/class/" . $site_db . "_info_class.php");


if (PHP_SAPI == 'cli')
    die('This programe should only be run from a Web Browser');

/** Include PHPExcel */
require_once '/website/os/PHPExcel-1.8.1/Classes/PHPExcel.php';
// require_once 'vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
// require_once 'vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// Set document properties
$objPHPExcel->getProperties()->setCreator("PowerSales")
    ->setLastModifiedBy("PowerSales")
    ->setTitle("Office 2007 XLSX Document")
    ->setSubject("Office 2007 XLSX Document")
    ->setDescription("The document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("協力廠商作業進度表");




// Title

// ===== 主標題 =====
$sheet->mergeCells('A1:AB1');
$sheet->setCellValue('A1', '協力廠商作業進度表');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['size' => 14, 'bold' => true],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
]);



// 設定篩選條件（每個跨 3 格，排列整齊）
$filterData = [
    ['A2', 'C3', '製表日期: ' . $today],
    ['D2', 'F3', '狀態(1): ' . $get_status1],
    ['G2', 'I3', '狀態(2):' . $get_status2],
    ['J2', 'L3', '區域: ' . $region],
    ['M2', 'O3', '承攬模式: ' . $ContractingModel],
    ['P2', 'S3', '所屬公司: ' . $company_name],
];

foreach ($filterData as [$start, $end, $text]) {
    set_mergeCells_style_border($sheet, $start, $end, 'left', 'bottom');
    $sheet->setCellValue($start, $text);
}

// 第三列設定高度
$sheet->getRowDimension(3)->setRowHeight(25);

// ===== 表頭設定（從第 4 列開始）=====
$headers = [
    'A' => ['title'=>'狀態(1)', 'width'=>10],
    'B' => ['title'=>'狀態(2)', 'width'=>10],
    'C' => ['title'=>"工程名稱\n案件編號", 'width'=>15],
    'D' => ['title'=>"合約號碼\n(ERP專案代號)", 'width'=>15],
    'E' => ['title'=>"區域", 'width'=>10],
    'F' => ['title'=>"承攬模式", 'width'=>15],
    'G' => ['title'=>"所屬公司", 'width'=>15],
    'H' => ['title'=>"上包\n公司名稱", 'width'=>15],
    'I' => ['title'=>"上包\n訂約日期", 'width'=>10],
    'J' => ['title'=>"上包簽約金額(含稅)", 'width'=>15],
    'K' => ['title'=>"預收款\n已請款期程", 'width'=>50],
    'L' => ['title'=>"預計\n進場日", 'width'=>10],
    'M' => ['title'=>"預計\n完工日", 'width'=>10],
    'N' => ['title'=>"鋁模材料\n利舊/新購", 'width'=>15],
    'O' => ['title'=>"標準層模板數量\n(M2)", 'width'=>15],
    'P' => ['title'=>"屋突層模板數量\n(M2)", 'width'=>15],
    'Q' => ['title'=>"材料用量\n(M2)", 'width'=>15],
    'R' => ['title'=>"通知志特\n報價日期", 'width'=>10],
    'S' => ['title'=>"志特編號", 'width'=>10],
    'T' => ['title'=>"下單志特\n預定日期", 'width'=>15],
    'U' => ['title'=>"志特材料\n採購主合約進度", 'width'=>25],
    'V' => ['title'=>"與志特\n訂約日期", 'width'=>15],
    'W' => ['title'=>"志特\n合約金額(RMB)", 'width'=>15],
    'X' => ['title'=>"第一批大貨\n到港日期", 'width'=>10],
    'Y' => ['title'=>"代工下包\n發包進度", 'width'=>20],
    'Z' => ['title'=>"放樣\n發包進度", 'width'=>15],
    'AA' => ['title'=>"下包公司名稱", 'width'=>20],
    'AB'=> ['title'=>"下包簽約金額", 'width'=>20],
    'AC'=> ['title'=>"實際進場日", 'width'=>15],
];

$wrapCells = [];
foreach ($headers as $col => $info) {
    $sheet->setCellValue($col.'4', $info['title']);
    $sheet->getColumnDimension($col)->setWidth($info['width']);
    if (strpos($info['title'], "\n") !== false) {
        $sheet->getStyle($col.'4')->getAlignment()->setWrapText(true);
        $wrapCells[] = $col.'4';
    }
}

// 套用表頭樣式（框線、對齊、字型）
$sheet->getStyle('A4:AC4')->applyFromArray([
    'borders' => [
        'outline' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
    'font' => [
        'size' => 8,
        'bold' => true,
    ],
]);

// A4:P4 - 淺綠色 #E2EFDA
$sheet->getStyle('A4:P4')->applyFromArray([
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'E2EFDA'],
    ],
]);

// Q4:V4 - 淺黃色 #FFF2CC
$sheet->getStyle('Q4:V4')->applyFromArray([
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'FFF2CC'],
    ],
]);

// W4:AB4 - 淺藍色 #DDEBF7
$sheet->getStyle('W4:AB4')->applyFromArray([
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'DDEBF7'],
    ],
]);









$mDB = "";
$mDB = new MywebDB();


$Qry = "SELECT 
   a.status1,
   a.status2,
   a.builder_id,
   c.builder_name,
   a.construction_id,
   a.contractor_id,
   d.contractor_name,
   a.case_id,
   a.region,
   a.ContractingModel,
   a.contract_date,
   a.total_contract_amt,
   a.advance_payment1,
   a.request_date1,
   a.advance_payment2,
   a.request_date2,
   a.advance_payment3,
   a.request_date3,
   a.estimated_arrival_date,
   a.estimated_completion_date,
   a.completion_date,
   a.geto_formwork,
   a.std_layer_template_qty,
   a.roof_protrusion_template_qty,
   a.material_purchase_progress,
   a.geto_order_date,
   a.geto_no,
   a.geto_quotation,
   a.geto_contract_date,
   a.material_import_date,
   a.subcontracting_progress,
   a.subcontracting_progress2,
  
   a.subcontractor_id1,
   f1.subcontractor_name AS subcontractor_name1,
   a.total_contract_amt1,
   a.subcontractor_id2,
   f2.subcontractor_name AS subcontractor_name2,
   a.total_contract_amt2,
   a.subcontractor_id3,
   f3.subcontractor_name AS subcontractor_name3,
   a.total_contract_amt3,
   a.subcontractor_id4,
   f4.subcontractor_name AS subcontractor_name4,
   a.total_contract_amt4,
   a.subcontractor_id5,
   f5.subcontractor_name AS subcontractor_name5,
   a.total_contract_amt5,
   a.subcontractor_id6,
   f6.subcontractor_name AS subcontractor_name6,
   a.total_contract_amt6,
   a.actual_entry_date,

   a.ERP_no,
   a.company_id,
   b.short_name

 FROM CaseManagement a
 LEFT JOIN builder c ON c.builder_id = a.builder_id
 LEFT JOIN company b ON b.company_id = a.company_id
 LEFT JOIN contractor d ON d.contractor_id = a.contractor_id
 LEFT JOIN subcontractor f1 ON f1.subcontractor_id = a.subcontractor_id1
 LEFT JOIN subcontractor f2 ON f2.subcontractor_id = a.subcontractor_id2
 LEFT JOIN subcontractor f3 ON f3.subcontractor_id = a.subcontractor_id3
 LEFT JOIN subcontractor f4 ON f4.subcontractor_id = a.subcontractor_id4
 LEFT JOIN subcontractor f5 ON f5.subcontractor_id = a.subcontractor_id5
 LEFT JOIN subcontractor f6 ON f6.subcontractor_id = a.subcontractor_id6
 WHERE 1=1 ";
//   a.status1 = '已簽約' AND a.ContractingModel != '租賃(RH)'
//  ORDER BY a.auto_seq";

// 如果有指定 status1，加上條件
if ($get_status1 !== '') {
    $Qry .= " AND a.status1 = '$get_status1'";
}
if ($get_status2 !== '') {
    $Qry .= " AND a.status2 = '$get_status2'";
}
if ($ContractingModel !== '') {
    $Qry .= " AND a.ContractingModel = '$ContractingModel'";
}
if ($region !== '') {
    $Qry .= " AND a.region = '$region'";
}
if ($company_id !== '') {
    $Qry .= " AND a.company_id = '$company_id'";
}

$Qry .= " ORDER BY a.auto_seq";

$mDB->query($Qry);








$total = $mDB->rowCount();
if ($total > 0) {
//  $material_use_qty_tmp = 0;
    $SUM_total_contract_amt = 0;
    $SUM_geto_quotation = 0;
    $SUM_total_contract_amt_group = 0;
 while ($row=$mDB->fetchRow(2)) {
		$status1 = $row['status1'];
		$status2 = $row['status2'];
		$builder_id = $row['builder_id'];
		$builder_name = $row['builder_name'];
		$contractor_id = $row['contractor_id'];
		$contractor_name = $row['contractor_name'];
		$construction_id = $row['construction_id'];
		$case_id = $row['case_id'];
		$region = $row['region'];
		$ContractingModel = $row['ContractingModel'];
		$contract_date = ($row['contract_date'] == '0000-00-00' || $row['contract_date'] == null) ? '' : $row['contract_date'];
		$total_contract_amt = number_format($row['total_contract_amt']);
        $advance_payment1 = isset($row['advance_payment1']) && $row['advance_payment1'] !== '' ? "第一期預收款請款:{$row['advance_payment1']}" : "";
        $request_date1 = ($row['request_date1'] ?? '') && $row['request_date1'] !== '0000-00-00' ? "第一期請款日期 :{$row['request_date1']}": '';
		$advance_payment2 = isset($row['advance_payment2']) && $row['advance_payment2'] !== '' ? "第二期預收款請款:{$row['advance_payment2']}" : "";
		$request_date2 = ($row['request_date2'] ?? '') && $row['request_date2'] !== '0000-00-00' ? "第二期請款日期 :{$row['request_date2']}": '';
		$advance_payment3 = isset($row['advance_payment3']) && $row['advance_payment3'] !== '' ? "第三期預收款請款:{$row['advance_payment3']}" : "";
		$request_date3 = ($row['request_date3'] ?? '') && $row['request_date3'] !== '0000-00-00' ? "第三期請款日期 :{$row['request_date3']}": '';
		$estimated_arrival_date = ($row['estimated_arrival_date'] == '0000-00-00' || $row['estimated_arrival_date'] == null) ? '' : $row['estimated_arrival_date'];
		$estimated_completion_date = ($row['estimated_completion_date'] == '0000-00-00' || $row['estimated_completion_date'] == null) ? '' : $row['estimated_completion_date'];
		$completion_date = ($row['completion_date'] == '0000-00-00' || $row['completion_date'] == null) ? '' : $row['completion_date'];
		$geto_formwork = $row['geto_formwork'];
		$std_layer_template_qty = number_format($row['std_layer_template_qty']);
		$roof_protrusion_template_qty = number_format($row['roof_protrusion_template_qty']);
		$material_purchase_progress = $row['material_purchase_progress'];
		$geto_order_date = ($row['geto_order_date'] == '0000-00-00' || $row['geto_order_date'] == null) ? '' : $row['geto_order_date'];
		$geto_no = $row['geto_no'];
		$geto_quotation = $row['geto_quotation'];
		$geto_contract_date = ($row['geto_contract_date'] == '0000-00-00' || $row['geto_contract_date'] == null) ? '' : $row['geto_contract_date'];
		$material_import_date = ($row['material_import_date'] == '0000-00-00' || $row['material_import_date'] == null) ? '' : $row['material_import_date'];
		$subcontracting_progress = $row['subcontracting_progress'];
		$subcontracting_progress2 = $row['subcontracting_progress2'];
		
		$subcontractor_id1 = $row['subcontractor_id1'];
		$subcontractor_name1 = $row['subcontractor_name1'];
		$total_contract_amt1 = number_format($row['total_contract_amt1']);
		$subcontractor_id2 = $row['subcontractor_id2'];
		$subcontractor_name2 = $row['subcontractor_name2'];
		$total_contract_amt2 = number_format($row['total_contract_amt2']);
		$subcontractor_id3 = $row['subcontractor_id3'];
		$subcontractor_name3 = $row['subcontractor_name3'];
		$total_contract_amt3 = number_format($row['total_contract_amt3']);
		$subcontractor_id4 = $row['subcontractor_id4'];
		$subcontractor_name4 = $row['subcontractor_name4'];
		$total_contract_amt4 = number_format($row['total_contract_amt4']);
		$subcontractor_id5 = $row['subcontractor_id5'];
		$subcontractor_name5 = $row['subcontractor_name5'];
		$total_contract_amt5 = number_format($row['total_contract_amt5']);
		$subcontractor_id6 = $row['subcontractor_id6'];
		$subcontractor_name6 = $row['subcontractor_name6'];
		$total_contract_amt6 = number_format($row['total_contract_amt6']);
		
		$actual_entry_date = ($row['actual_entry_date'] == '0000-00-00' || $row['actual_entry_date'] == null) ? '' : $row['actual_entry_date'];
		if (empty($estimated_arrival_date) || $estimated_arrival_date == '0000-00-00') {
					$estimated_geto_order_date = "";
				} else {
					$date = new DateTime($estimated_arrival_date);
					$date->modify('-7 months');
					$estimated_geto_order_date = $date->format('Y-m-d');
			}
		$ERP_no = $row['ERP_no'];
		$company_id = $row['short_name'];
        $material_use_qty_tmp = $row['std_layer_template_qty'] + $row['roof_protrusion_template_qty'];
        $material_use_qty = number_format($material_use_qty_tmp);

        // 下包商字串
        $subcontractor_group = '';

        if (!empty($subcontractor_name1)) $subcontractor_group .= $subcontractor_name1."\n";
        if (!empty($subcontractor_name2)) $subcontractor_group .= $subcontractor_name2."\n";
        if (!empty($subcontractor_name3)) $subcontractor_group .= $subcontractor_name3."\n";
        if (!empty($subcontractor_name4)) $subcontractor_group .= $subcontractor_name4."\n";

        // 只有有下包商才顯示標題
        if (!empty($subcontractor_group)) {
            $subcontractor_group = "下包商:\n".$subcontractor_group;
        }

        if (!empty($subcontractor_name5)) {
            $subcontractor_group .= "放樣:\n".$subcontractor_name5."\n";
        }

        if (!empty($subcontractor_name6)) {
            $subcontractor_group .= "檢核:\n".$subcontractor_name6."\n";
        }

        // 下包簽約金額
        $total_contract_amt_group = '';

        if (!empty($total_contract_amt1)) $total_contract_amt_group .= "{$total_contract_amt1}\n";
        if (!empty($total_contract_amt2)) $total_contract_amt_group .= "{$total_contract_amt2}\n";
        if (!empty($total_contract_amt3)) $total_contract_amt_group .= "{$total_contract_amt3}\n";
        if (!empty($total_contract_amt4)) $total_contract_amt_group .= "{$total_contract_amt4}\n";
        if (!empty($total_contract_amt5)) $total_contract_amt_group .= "{$total_contract_amt5}\n";
        if (!empty($total_contract_amt6)) $total_contract_amt_group .= "{$total_contract_amt6}\n";

        // 只有有下包商才顯示標題
        if (!empty($total_contract_amt_group)) {
            $total_contract_amt_group = "下包商:\n".$total_contract_amt_group;
        }

        if (!empty($total_contract_amt5)) {
            $total_contract_amt_group .= "放樣:\n{$total_contract_amt5}\n";
        }

        if (!empty($total_contract_amt6)) {
            $total_contract_amt_group .= "檢核:\n{$total_contract_amt6}\n";
        }


        // 寫入資料到 Excel
        $sheet->setCellValue('A'.($i+5), $status1);                         
        $sheet->setCellValue('B'.($i+5), $status2);
        $sheet->setCellValue('C'.($i+5), $ERP_no);

        $sheet->setCellValue('D'.($i+5), $construction_id."\n".$case_id);
        $sheet->getStyle('D'.($i+5))->getAlignment()->setWrapText(true);

        $sheet->setCellValue('E'.($i+5), $region);
        $sheet->setCellValue('F'.($i+5), $ContractingModel);
        $sheet->setCellValue('G'.($i+5), $company_id);

        $sheet->setCellValue('H'.($i+5), $builder_name."\n".$contractor_name);
        $sheet->getStyle('H'.($i+5))->getAlignment()->setWrapText(true);

        $sheet->setCellValue('I'.($i+5), $contract_date);
        $sheet->setCellValue('J'.($i+5), $total_contract_amt);

        $sheet->setCellValue('K'.($i+5), $advance_payment1."\n".$request_date1."\n".$advance_payment2."\n".$request_date2."\n".$advance_payment3."\n".$request_date3);
        $sheet->getStyle('K'.($i+5))->getAlignment()->setWrapText(true);

        $sheet->setCellValue('L'.($i+5), $estimated_arrival_date);
        $sheet->setCellValue('M'.($i+5), $estimated_completion_date);
        $sheet->setCellValue('N'.($i+5), $geto_formwork);
        $sheet->setCellValue('O'.($i+5), $std_layer_template_qty);
        $sheet->setCellValue('P'.($i+5), $roof_protrusion_template_qty);
        $sheet->setCellValue('Q'.($i+5), $material_use_qty);
        $sheet->setCellValue('R'.($i+5), $geto_order_date);
        $sheet->setCellValue('S'.($i+5), $geto_no);
        $sheet->setCellValue('T'.($i+5), $estimated_geto_order_date);
        $sheet->setCellValue('U'.($i+5), $material_purchase_progress);
        $sheet->setCellValue('V'.($i+5), $geto_contract_date);
        $sheet->setCellValue('W'.($i+5), $geto_quotation);
        $sheet->setCellValue('X'.($i+5), $material_import_date);
        $sheet->setCellValue('Y'.($i+5), $subcontracting_progress);
        $sheet->setCellValue('Z'.($i+5), $subcontracting_progress2);

        $sheet->setCellValue('AA'.($i+5),$subcontractor_group);
        $sheet->getStyle('AA'.($i+5))->getAlignment()->setWrapText(true);
        $sheet->setCellValue('AB'.($i+5),$total_contract_amt_group);
        $sheet->getStyle('AB'.($i+5))->getAlignment()->setWrapText(true);

        $sheet->setCellValue('AC'.($i+5), $actual_entry_date);


        $i++;
        $SUM_total_contract_amt += $row['total_contract_amt'];
        $SUM_geto_quotation += $row['geto_quotation'];
        $SUM_total_contract_amt_group += $row['total_contract_amt1'] + $row['total_contract_amt2'] + $row['total_contract_amt3'] + $row['total_contract_amt4'] + $row['total_contract_amt5'] + $row['total_contract_amt6'];

}

   $sheet->getStyle('A5:AC'.($i+4))
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $sheet->getStyle('A5:AC'.($i+4))
        ->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    // 新增黑色細線邊框
    $sheet->getStyle('A5:AC'.($i+4))->applyFromArray([
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // 黑色
            ],
        ],
    ]);
// 寫入加總列
$sheet->getRowDimension($i+5)->setRowHeight(45); // 設定行高
$sheet->setCellValue('I'.($i+5), '合計'); // 在合約日期欄 (I 欄) 標示「合計」
$sheet->setCellValue('J'.($i+5), number_format($SUM_total_contract_amt)); // 總合約金額加總
$sheet->setCellValue('W'.($i+5), number_format($SUM_geto_quotation));     // GETO 報價金額加總
$sheet->setCellValue('AB'.($i+5), number_format($SUM_total_contract_amt_group)); // 下包金額加總

// 套用「總表小計」樣式
$sheet->getStyle('A'.($i+5).':AC'.($i+5))->applyFromArray([
    'fill' => [
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['argb' => 'FFFFF2CC'], // 淡黃色
    ],
    'font' => [
        'bold' => true
    ],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'outline' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
]);

// 針對合計金額欄 (J, W, AB) 加粗框線
foreach (['J','W','AB'] as $col) {
    $sheet->getStyle($col.($i+5))->applyFromArray([
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ]);
}

}


$mDB->remove();

// 計算日期範圍的天數


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle("協力廠商作業進度表");


$xlsx_filename = "協力廠商作業進度表.xls";


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=' . $xlsx_filename);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


// 表頭設置
function setHeaderStyle($sheet, $cell) {
    $sheet->getStyle($cell)->getFont()
        ->setBold(true)
        ->setSize(22);
    $sheet->getStyle($cell)->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
}
// 範例
// setHeaderStyle($sheet, 'A3');
// $sheet->setCellValue('A3', '狀態(1)');

// 水平合併與外框線
function set_mergeCells_style_border($sheet, $cell, $cell2 = "", $hAlign = "center", $vAlign = "middle")
{
    // 對齊映射
    $hMap = [
        'left'   => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'center' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'right'  => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    ];
    $vMap = [
        'top'    => PHPExcel_Style_Alignment::VERTICAL_TOP,
        'middle' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'bottom' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
    ];

    // 如果參數不存在，使用預設置中
    $hAlign = $hMap[$hAlign] ?? PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
    $vAlign = $vMap[$vAlign] ?? PHPExcel_Style_Alignment::VERTICAL_CENTER;

    // 合併儲存格（如有）
    $range = ($cell2 != "") ? "$cell:$cell2" : $cell;
    if ($cell2 != "") $sheet->mergeCells($range);

    // 套用邊框與對齊
    $sheet->getStyle($range)->applyFromArray([
        'alignment' => [
            'horizontal' => $hAlign,
            'vertical'   => $vAlign,
        ],
    ]);
}

// set_mergeCells_style_border(範例)

// 預設置中
// set_mergeCells_style_border($sheet, 'A1', 'C1');

// // 水平靠左、垂直靠上
// set_mergeCells_style_border($sheet, 'A2', 'C2', 'left', 'top');

// // 水平靠右、垂直靠下
// set_mergeCells_style_border($sheet, 'A3', 'C3', 'right', 'bottom');

function set_style_border_only_outline($sheet, $cell, $cell2 = "")
{
    $range = ($cell2 != "") ? "$cell:$cell2" : $cell;

    $sheet->getStyle($range)->applyFromArray([
        'borders' => [
            'outline' => [ // 只設定外框
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ],
        'font' => [
            'size' => 8,
        ],
    ]);
}
