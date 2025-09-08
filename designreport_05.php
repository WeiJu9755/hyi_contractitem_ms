<?php

//error_reporting(E_ALL); 
//ini_set('display_errors', '1');

session_start();

$memberID = $_SESSION['memberID'];
$powerkey = $_SESSION['powerkey'];


require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if (!($detect->isMobile() && !$detect->isTablet())) {
	$isMobile = "0";
} else {
	$isMobile = "1";
}


$m_location = "/website/smarty/templates/" . $site_db . "/" . $templates;
$m_pub_modal = "/website/smarty/templates/" . $site_db . "/pub_modal";



//載入公用函數
@include_once '/website/include/pub_function.php';

@include_once("/website/class/eshop_info_class.php");

//連結資料
@include_once("/website/class/".$site_db."_info_class.php");

/* 使用xajax */
@include_once '/website/xajax/xajax_core/xajax.inc.php';
$xajax = new xajax();



$auto_seq = $_GET['auto_seq'];
$fm = $_GET['fm'];



$mDB = "";
$mDB = new MywebDB();

// 載入狀態	1
$selected_status1 = $_GET['status1'] ?? ''; 

$Qry = "SELECT caption FROM pjclass WHERE small_class = '0' ORDER BY orderby";
$mDB->query($Qry);

$select_status1  = "<select class=\"inline form-select\" name=\"status1\" id=\"status1\" style=\"width:auto;\">";
$select_status1 .= "<option value='' ".($selected_status1 == '' ? 'selected' : '')."></option>";

if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$caption = $row['caption'];
		$selected = ($caption == $selected_status1) ? 'selected' : '';
		$select_status1 .= "<option value='$caption' $selected>$caption</option>";
	}
}
$select_status1 .= "</select>";

// 載入狀態 2
$selected_status2 = $_GET['status2'] ?? ''; 

$Qry = "SELECT caption FROM pjclass WHERE small_class != '0' ORDER BY orderby";
$mDB->query($Qry);

$select_status2  = "<select class=\"inline form-select\" name=\"status2\" id=\"status2\" style=\"width:auto;\">";
$select_status2 .= "<option value='' ".($selected_status2 == '' ? 'selected' : '')."></option>";

if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$caption = $row['caption'];
		$selected = ($caption == $selected_status2) ? 'selected' : '';
		$select_status2 .= "<option value='$caption' $selected>$caption</option>";
	}
}
$select_status2 .= "</select>";

//區域
$Qry = "SELECT caption FROM `items` WHERE pro_id = 'region'";
$mDB->query($Qry);

$get_region_dropdown = isset($_GET['region']) ? $_GET['region'] : '';

$region_dropdown = "<select class=\"inline form-select\" name=\"region\" id=\"region\" style=\"width:auto;\">";
$region_dropdown .= "<option></option>";

if ($mDB->rowCount() > 0) {
    while ($row = $mDB->fetchRow(2)) {
        $select_region = $row['caption']; // ← 正確應該取 caption
        $selected = ($get_region_dropdown == $select_region) ? "selected" : "";

        $region_dropdown .= "<option value='$select_region' $selected>$select_region</option>";
    }
}

$region_dropdown .= "</select>";

//承攬模式
$Qry = "SELECT caption AS ContractingModel FROM items 
WHERE pro_id = 'ContractingModel'";

$mDB->query($Qry);


$get_ContractingModel_dropdown = isset($_GET['ContractingModel']) ? $_GET['ContractingModel'] : '';

$ContractingModel_dropdown = "<select class=\"inline form-select\" name=\"ContractingModel\" id=\"ContractingModel\" style=\"width:auto;\">";
$ContractingModel_dropdown .= "<option></option>";

if ($mDB->rowCount() > 0) {
    while ($row = $mDB->fetchRow(2)) {
        $select_ContractingModel = $row['ContractingModel'];
        $selected = ($get_ContractingModel_dropdown == $select_ContractingModel) ? "selected" : "";

        $ContractingModel_dropdown .= "<option value='$select_ContractingModel' $selected>$select_ContractingModel</option>";
    }
}

$ContractingModel_dropdown .= "</select>";

//所屬公司
$Qry = "SELECT company_id,company_name FROM company";

$mDB->query($Qry);


$get_company_id_dropdown = isset($_GET['company_id']) ? $_GET['company_id'] : '';

$company_id_dropdown = "<select class=\"inline form-select\" name=\"company_id\" id=\"company_id\" style=\"width:auto;\">";
$company_id_dropdown .= "<option></option>";

if ($mDB->rowCount() > 0) {
    while ($row = $mDB->fetchRow(2)) {
		if ($row['company_id'] == '83186869'||$row['company_id'] == '93530861') {
        $select_company_id = $row['company_id'];
        $select_company_name = $row['company_name'];
        $selected = ($get_company_id_dropdown == $select_company_id) ? "selected" : "";

        $company_id_dropdown .= "<option value='$select_company_id' $selected>$select_company_name</option>";
		}
    }
}

$company_id_dropdown .= "</select>";



$mDB->remove();



//檢查是否為管理員及進階會員
$super_admin = "N";
$super_advanced = "N";
$mem_row = getkeyvalue2('memberinfo', 'member', "member_no = '$memberID'", 'admin,advanced');
$super_admin = $mem_row['admin'];
$super_advanced = $mem_row['advanced'];





$show_savebtn=<<<EOT
<div class="btn-group vbottom" role="group" style="margin-top:5px;">
	<button id="close" class="btn btn-danger" type="button" onclick="parent.myDraw();parent.$.fancybox.close();" style="padding: 5px 15px;"><i class="bi bi-power"></i>&nbsp;關閉</button>
</div>
EOT;


//取得使用者員工身份
$member_picture = getmemberpict50($makeby);

$member_row = getkeyvalue2("memberinfo","member","member_no = '$makeby'","member_name");
$member_name = $member_row['member_name'];

$employee_row = getkeyvalue2($site_db."_info","employee","member_no = '$makeby'","count(*) as manager_count,employee_name,employee_type");
$manager_count =$employee_row['manager_count'];
if ($manager_count > 0) {
	$employee_name = $employee_row['employee_name'];
	$employee_type = $employee_row['employee_type'];
} else {
	$employee_name = $member_name;
	$employee_type = "未在員工名單";
}




$m_location		= "/website/smarty/templates/".$site_db."/".$templates;
include $m_location."/sub_modal/project/func02/designreport_ms/bulider_report.php";


$now = date('Y-m-d  H:i');

$show_center=<<<EOT

$style_css

<style>

    .tooltip-box {
        position: absolute;
        top: 50px; /* 調整為按鈕正下方 */
        left: 30%;
        transform: translateX(-30%);
        padding: 10px;
        background-color: #333;
        color: white;
        border-radius: 5px;
        display: none;
		/*
        white-space: nowrap;
		*/
        z-index: 1000;
    }

    .tooltip-box::after {
        content: '';
        position: absolute;
        top: -8px;
        left: 30%;
        transform: translateX(-30%);
        border-width: 8px;
        border-style: solid;
        border-color: transparent transparent #333 transparent;
    }
</style>

<div class="mytable w-100 bg-white p-3 mt-3">
	<div class="myrow">
		<!-- 左側空白欄 -->
		<div class="mycell" style="width:20%;"></div>

		<!-- 中央標題 -->
		<div class="mycell weight pt-5 pb-4 text-center">
			<h3>協力廠商作業進度表</h3>
		</div>

		<!-- 固定位置按鈕 -->
		<div class="mycell text-end p-2 vbottom" style="width:20%;">
			
			<div class="btn-group print" role="group" style="position:fixed; top:10px; right:10px; z-index:9999;">
				
				<button type="button" class="btn btn-success" onclick="excel_export();"><i class="bi bi-filetype-xls me-1"></i>匯出Excel</button>
				<button id="print" class="btn btn-info btn-lg" type="button" onclick="window.print();">
					<i class="bi bi-printer"></i>&nbsp;列印
				</button>
				<button id="close" class="btn btn-danger btn-lg" type="button" onclick="window.close();">
					<i class="bi bi-power"></i>&nbsp;關閉
				</button>
			</div>
		</div>
	</div>
</div>

<hr class="style_a m-2 p-0">

<!-- 查詢條件區塊（Bootstrap 樣式） -->
<div class="container-fluid p-3 text-center">
	<div class="row justify-content-center g-2">
		<div class="col-auto">
			<div class="form-label fw-bold">狀態(1):</div>
			<div>$select_status1</div>
		</div>

		<div class="col-auto">
			<div class="form-label fw-bold">狀態(2):</div>
			<div>$select_status2</div>
		</div>

		<div class="col-auto">
			<div class="form-label fw-bold">區域:</div>
			<div>$region_dropdown</div>
		</div>

		<div class="col-auto">
			<div class="form-label fw-bold">承攬模式:</div>
			<div>$ContractingModel_dropdown</div>
		</div>

		<div class="col-auto">
			<div class="form-label fw-bold">所屬公司:</div>
			<div>$company_id_dropdown</div>
		</div>

		<div class="col-auto align-self-end">
			<button type="button" class="btn btn-success" onclick="search();">
				<i class="fas fa-check"></i>&nbsp;查詢
			</button>
		</div>
	</div>
</div>
<div style="margin-bottom: 150px;">
	$show_bulider_report
</div>
<script>



function CheckValue(thisform) {
	xajax_processform(xajax.getFormValues('modifyForm'));
	thisform.submit();
}

function SaveValue(thisform) {
	xajax_SaveValue(xajax.getFormValues('modifyForm'));
	//thisform.submit();
}

function setEdit() {
	$('#close', window.document).addClass("display_none");
	$('#cancel', window.document).removeClass("display_none");
	$('#myConfirmSending').prop('disabled', true);
}

function setCancel() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
	document.forms[0].reset();
}

function setSave() {
	$('#close', window.document).removeClass("display_none");
	$('#cancel', window.document).addClass("display_none");
}

function ch_team_member(thisform) {

	var salary_sheet_id = thisform.salary_sheet_id.value;
	var team_id = thisform.team_id.value;
	//alert(team_id);

	openfancybox_edit('/index.php?ch=ch_team_member&salary_sheet_id='+salary_sheet_id+'&team_id='+team_id+'&fm=$fm',800,'96%','');
}


$(document).ready(function() {
	$("#content").autoGrow({
		extraLine: true // Adds an extra line at the end of the textarea. Try both and see what works best for you.
	});
});




var SendPushNotices = function(tb,salary_sheet_id,io_content){
	var site_db = '$site_db';
	var web_id = '$web_id';
	var fm = '$fm';
	var templates = '$templates';
	var memberID = '$memberID';
	var project_id = '$project_id';
	var auth_id = '$auth_id';
	var caption = '$caption';
	var member_name = '$employee_name';
	var salary_year_month = '$salary_year_month';
	var now = '$now';
	//存入訊息
	$.post("/smarty/templates/"+site_db+"/"+templates+"/sub_modal/project/func08/dispatch_ms/ajax_PushNotice.php",{
			"site_db": site_db,
			"web_id": web_id,
			"project_id": project_id,
			"auth_id": auth_id,
			"from_id": memberID,
			"tb": tb,
			"salary_sheet_id": salary_sheet_id,
			"PushContent": io_content
			},
		function(data){

			var dispatch_desc = "日期："+salary_year_month+" (#"+salary_sheet_id+")";
			//var PushContent = member_name+" 於 "+now+" 發出了通知訊息<br>"+caption+"<br>"+dispatch_desc+"<br>"+io_content;

			var url = "/index.php?ch=view&pjt="+caption+"&salary_sheet_id="+salary_sheet_id+"&project_id="+project_id+"&auth_id="+auth_id+"&fm="+fm+"#myScrollspy";

			var mynotices_message = caption+"<div class=\"mytable\"><div class=\"myrow\"><div class=\"mycell w-auto px-1\"><div><div class=\"size12 weight\">"+member_name+" 於 <span class=\"red\">"+now+"</span> 發出了通知訊息</div></div><div style=\"padding: 0 3px 3px 0;\"><div class=\"size12 blue weight\">出工任務內容</div><div class=\"size12 weight\">"+dispatch_desc+"</div><div class=\"block-with-text\" style=\"max-height: 7.2em;\">"+io_content+"</div></div></div></div></div>";


			io.connect('$FOREVER').emit('sendnotice', '$web_id', data, mynotices_message);
			art.dialog.tips('已發佈通知!',1);
		},
		"json"
	);
}

var Checkall = function(thisform) {	
	xajax_Checkall(xajax.getFormValues('modifyForm'));
};



function join_foreign_worker(salary_sheet_id,def_attendance_start) {
	xajax_join_foreign_worker(salary_sheet_id,def_attendance_start);
	return false;
}

function join_seconded_staff(salary_sheet_id,def_attendance_start) {
	xajax_join_seconded_staff(salary_sheet_id,def_attendance_start);
	return false;
}



var verify_all = function(salary_sheet_id,check) {	
	xajax_verify_all(salary_sheet_id,check);
};

</script>

<script>
	// 取得按鈕和提示框元素
	const tooltipButton1 = document.getElementById('tooltipButton1');
	const tooltipContent1 = document.getElementById('tooltipContent1');

	let timeoutId; // 記錄計時器的ID

	// 設定按鈕點擊事件，控制提示框的顯示與隱藏
	tooltipButton1.addEventListener('click', function() {
		if (tooltipContent1.style.display === 'none' || tooltipContent1.style.display === '') {
			tooltipContent1.style.display = 'block';

			// 清除之前的計時器，避免重複計時
			clearTimeout(timeoutId);

			// 設定7秒後自動隱藏
			timeoutId = setTimeout(function() {
			tooltipContent1.style.display = 'none';
			}, 7000); // 7000毫秒 = 7秒
		} else {
			tooltipContent1.style.display = 'none';
			clearTimeout(timeoutId); // 如果手動關閉，清除計時器
		}
	});

	// 取得按鈕和提示框元素
	const tooltipButton2 = document.getElementById('tooltipButton2');
	const tooltipContent2 = document.getElementById('tooltipContent2');

	// 設定按鈕點擊事件，控制提示框的顯示與隱藏
	tooltipButton2.addEventListener('click', function() {
		if (tooltipContent2.style.display === 'none' || tooltipContent2.style.display === '') {
			tooltipContent2.style.display = 'block';

			// 清除之前的計時器，避免重複計時
			clearTimeout(timeoutId);

			// 設定7秒後自動隱藏
			timeoutId = setTimeout(function() {
			tooltipContent2.style.display = 'none';
			}, 7000); // 7000毫秒 = 7秒
		} else {
			tooltipContent2.style.display = 'none';
			clearTimeout(timeoutId); // 如果手動關閉，清除計時器
		}
	});


function excel_export() {
		var status1 = $('#status1').val();
		var status2 = $('#status2').val();
		var region = $('#region').val();
		var ContractingModel = $('#ContractingModel').val();
		var company_id = $('#company_id').val();
		var fm = '$fm';
		

		window.location = '/index.php?ch=designreport_05_exportexcel&status1='+status1+'&status2='+status2+'&region='+region+'&ContractingModel='+ContractingModel+'&company_id='+company_id+'&fm=$fm';
		return false;

	}	

</script>

EOT;

?>