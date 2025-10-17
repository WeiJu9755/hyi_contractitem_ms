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

// 載入合約
$selected_contract = $_GET['contract_id'] ?? '';

$Qry = "SELECT contract_id, contract_caption FROM contract ORDER BY contract_caption";
$mDB->query($Qry);

$select_contract  = '<select class="inline form-select" name="contract_id" id="contract_id" style="width:auto;">';
$select_contract .= '<option value=""' . ($selected_contract === '' ? ' selected' : '') . '></option>';

if ($mDB->rowCount() > 0) {
	while ($row = $mDB->fetchRow(2)) {
		$contract_id       = $row['contract_id'];
    	$contract_caption  = $row['contract_caption'];
		$selected = ($contract_id === $selected_contract) ? ' selected' : '';
		$select_contract .= "<option value='$contract_id' $selected>$contract_caption</option>";
	}
}
$select_contract .= '</select>';





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
include $m_location."/sub_modal/project/func08/contractitem_ms/contractitem_report.php";


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
			<h3>合約工項月報表</h3>
		</div>

		<!-- 固定位置按鈕 -->
		<div class="mycell text-end p-2 vbottom" style="width:20%;">
			
			<div class="btn-group print" role="group" style="position:fixed; top:10px; right:10px; z-index:9999;">
				
				<!-- <button type="button" class="btn btn-success" onclick="excel_export();"><i class="bi bi-filetype-xls me-1"></i>匯出Excel</button> -->
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



<div class="d-flex justify-content-center align-items-end flex-wrap gap-3">

    <!-- 月份 -->
    <div class="d-inline-block text-center me-3">
        <div class="size12 weight text-nowrap pt-2 vtop mb-2">月份: </div>
        <div class="input-group" id="annualyear" style="max-width:180px; margin:0 auto;">
            <input type="text" class="form-control" id="annual_month" 
                   name="annual annual_month" placeholder="請輸入月份"
                   aria-describedby="annual_month" value="$annual_month">
            <button class="btn btn-outline-secondary input-group-append input-group-addon" 
                    type="button" data-target="#annualyear" data-toggle="datetimepicker">
                <i class="bi bi-calendar"></i>
            </button>
        </div>
    </div>

    <!-- 合約 -->
    <div class="d-inline-block text-center me-3">
        <div class="form-label fw-bold">合約:</div>
        <div>$select_contract</div>
    </div>

    <!-- 查詢按鈕 -->
    <div class="d-inline-block">
        <button type="button" class="btn btn-success" onclick="search();">
            <i class="fas fa-check"></i>&nbsp;查詢
        </button>
    </div>
</div>

<!-- datetimepicker -->
<script type="text/javascript">
    $(function () {
        $('#annualyear').datetimepicker({
            locale: 'zh-tw',
            format: "YYYY-MM",
            allowInputToggle: true
        });
    });
</script>
<style>
.bootstrap-datetimepicker-widget {
    z-index: 1050 !important; /* 保證浮在其他元件上 */
    position: absolute !important;
}
</style>

	

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
		

		window.location = '/index.php?ch=contractitem_report_01_exportexcel&status1='+status1+'&status2='+status2+'&region='+region+'&ContractingModel='+ContractingModel+'&company_id='+company_id+'&fm=$fm';
		return false;

	}	

</script>

EOT;

?>