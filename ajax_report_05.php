<?php


header('Content-Type: application/json; charset=utf-8');


//$site_db = $_GET['site_db'];
$site_db = "eshop";


//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/".$site_db."_info_class.php");


$mDB = "";
$mDB = new MywebDB();



$get_status1 =  isset($_GET['status1']) ? $_GET['status1'] : '';
$get_status2 =  isset($_GET['status2']) ? $_GET['status2'] : '';
$ContractingModel = isset($_GET['ContractingModel']) ? $_GET['ContractingModel'] : '';
$region = isset($_GET['region']) ? $_GET['region'] : '';
$company_id = isset($_GET['company_id']) ? $_GET['company_id'] : '';


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
   a.subcontractor_id7,
   f7.subcontractor_name AS subcontractor_name7,
   a.total_contract_amt7,
   a.subcontractor_id8,
   f8.subcontractor_name AS subcontractor_name8,
   a.total_contract_amt8,


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
 LEFT JOIN subcontractor f7 ON f7.subcontractor_id = a.subcontractor_id7
 LEFT JOIN subcontractor f8 ON f8.subcontractor_id = a.subcontractor_id8

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

$retval = array();

if ($mDB->rowCount() > 0) {
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
		$contract_date = $row['contract_date'];
		$total_contract_amt = $row['total_contract_amt'];
		$advance_payment1 = $row['advance_payment1'];
		$request_date1 = $row['request_date1'];
		$advance_payment2 = $row['advance_payment2'];
		$request_date2 = $row['request_date2'];
		$advance_payment3 = $row['advance_payment3'];
		$request_date3 = $row['request_date3'];
		$estimated_arrival_date = $row['estimated_arrival_date'];
		$estimated_completion_date = $row['estimated_completion_date'];
		$completion_date = ($row['completion_date'] == '0000-00-00' || $row['completion_date'] == null) ? '' : $row['completion_date'];
		$geto_formwork = $row['geto_formwork'];
		$std_layer_template_qty = intval($row['std_layer_template_qty']);
		$roof_protrusion_template_qty = intval($row['roof_protrusion_template_qty']);
		$material_purchase_progress = $row['material_purchase_progress'];
		$geto_order_date = ($row['geto_order_date'] == '0000-00-00' || $row['geto_order_date'] == null) ? '' : $row['geto_order_date'];
		$geto_no = $row['geto_no'];
		$geto_quotation = $row['geto_quotation'];
		$geto_contract_date = $row['geto_contract_date'];
		$material_import_date = $row['material_import_date'];
		$subcontracting_progress = $row['subcontracting_progress'];
		$subcontracting_progress2 = $row['subcontracting_progress2'];
		
		$subcontractor_id1 = $row['subcontractor_id1'];
		$subcontractor_name1 = $row['subcontractor_name1'];
		$total_contract_amt1 = $row['total_contract_amt1'];
		$subcontractor_id2 = $row['subcontractor_id2'];
		$subcontractor_name2 = $row['subcontractor_name2'];
		$total_contract_amt2 = $row['total_contract_amt2'];
		$subcontractor_id3 = $row['subcontractor_id3'];
		$subcontractor_name3 = $row['subcontractor_name3'];
		$total_contract_amt3 = $row['total_contract_amt3'];
		$subcontractor_id4 = $row['subcontractor_id4'];
		$subcontractor_name4 = $row['subcontractor_name4'];
		$total_contract_amt4 = $row['total_contract_amt4'];
		$subcontractor_id5 = $row['subcontractor_id5'];
		$subcontractor_name5 = $row['subcontractor_name5'];
		$total_contract_amt5 = $row['total_contract_amt5'];
		$subcontractor_id6 = $row['subcontractor_id6'];
		$subcontractor_name6 = $row['subcontractor_name6'];
		$total_contract_amt6 = $row['total_contract_amt6'];
		$subcontractor_id7 = $row['subcontractor_id7'];
		$subcontractor_name7 = $row['subcontractor_name7'];
		$total_contract_amt7 = $row['total_contract_amt7'];
		$subcontractor_id8 = $row['subcontractor_id8'];
		$subcontractor_name8 = $row['subcontractor_name8'];
		$total_contract_amt8 = $row['total_contract_amt8'];
		
		$actual_entry_date = $row['actual_entry_date'];
		if (empty($estimated_arrival_date) || $estimated_arrival_date == '0000-00-00') {
					$estimated_geto_order_date = "";
				} else {
					$date = new DateTime($estimated_arrival_date);
					$date->modify('-7 months');
					$estimated_geto_order_date = $date->format('Y-m-d');
			}
		$ERP_no = $row['ERP_no'];
		$company_id = $row['short_name'];
		

		/*
		$retval[] = array(
		    "status1"=>$status1
		   ,"status2"=>$status2
		   ,"builder_id"=>$builder_id
		   ,"builder_name"=>$builder_name
		   ,"construction_id"=>$construction_id
		   ,"case_id"=>$case_id
		   ,"region"=>$region
		   ,"ContractingModel"=>$ContractingModel
		   ,"contract_date"=>$contract_date
		   ,"total_contract_amt"=>$total_contract_amt
		   ,"advance_payment1"=>$advance_payment1
		   ,"request_date1"=>$request_date1
		   ,"advance_payment2"=>$advance_payment2
		   ,"request_date2"=>$request_date2
		   ,"advance_payment3"=>$advance_payment3
		   ,"request_date3"=>$request_date3
		   ,"estimated_arrival_date"=>$estimated_arrival_date
		   ,"estimated_completion_date"=>$estimated_completion_date
		   ,"geto_formwork"=>$geto_formwork
		   ,"std_layer_template_qty"=>$std_layer_template_qty
		   ,"roof_protrusion_template_qty"=>$roof_protrusion_template_qty
		   ,"material_purchase_progress"=>$material_purchase_progress
		   ,"geto_order_date"=>$geto_order_date
		   ,"geto_no"=>$geto_no
		   ,"geto_quotation"=>$geto_quotation
		   ,"geto_contract_date"=>$geto_contract_date
		   ,"material_import_date"=>$material_import_date
		   ,"subcontracting_progress"=>$subcontracting_progress
		   ,"subcontracting_progress2"=>$subcontracting_progress2
		   ,"subcontractor_id1"=>$subcontractor_id1
		   ,"subcontractor_name1"=>$subcontractor_name1
		   ,"total_contract_amt1"=>$total_contract_amt1
		   ,"subcontractor_id2"=>$subcontractor_id2
		   ,"subcontractor_name2"=>$subcontractor_name2
		   ,"total_contract_amt2"=>$total_contract_amt2
		   ,"subcontractor_id3"=>$subcontractor_id3
		   ,"subcontractor_name3"=>$subcontractor_name3
		   ,"total_contract_amt3"=>$total_contract_amt3
		   ,"subcontractor_id4"=>$subcontractor_id4
		   ,"subcontractor_name4"=>$subcontractor_name4
		   ,"total_contract_amt4"=>$total_contract_amt4
		   ,"actual_entry_date"=>$actual_entry_date
		);
		*/

		$retval[] = array(
		    $status1
		   ,$status2
		   ,$builder_id
		   ,$builder_name
		   ,$construction_id
		   ,$case_id
		   ,$region
		   ,$ContractingModel
		   ,$contract_date
		   ,$total_contract_amt
		   ,$advance_payment1
		   ,$request_date1
		   ,$advance_payment2
		   ,$request_date2
		   ,$advance_payment3
		   ,$request_date3
		   ,$estimated_arrival_date
		   ,$estimated_completion_date
		   ,$geto_formwork
		   ,$std_layer_template_qty
		   ,$roof_protrusion_template_qty
		   ,$material_purchase_progress
		   ,$geto_order_date
		   ,$geto_no
		   ,$geto_quotation
		   ,$geto_contract_date
		   ,$material_import_date
		   ,$subcontracting_progress
		   ,$subcontracting_progress2
		   ,$subcontractor_id1
		   ,$subcontractor_name1
		   ,$total_contract_amt1
		   ,$subcontractor_id2
		   ,$subcontractor_name2
		   ,$total_contract_amt2
		   ,$subcontractor_id3
		   ,$subcontractor_name3
		   ,$total_contract_amt3
		   ,$subcontractor_id4
		   ,$subcontractor_name4
		   ,$total_contract_amt4
		    ,$subcontractor_id5
		   ,$subcontractor_name5
		   ,$total_contract_amt5
		   ,$subcontractor_id6
		   ,$subcontractor_name6
		   ,$total_contract_amt6
		   ,$actual_entry_date
		   ,$estimated_geto_order_date
		   ,$ERP_no
		   ,$company_id
		   ,$completion_date
		   ,$contractor_name
		   ,$subcontractor_id7
		   ,$subcontractor_name7
		   ,$total_contract_amt7
		   ,$subcontractor_id8
		   ,$subcontractor_name8
		   ,$total_contract_amt8
		);





	}
}

$mDB->remove();

echo json_encode([
    "data" => $retval
]);

?>