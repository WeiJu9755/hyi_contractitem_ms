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

@include_once("/website/class/" . $site_db . "_info_class.php");


//檢查是否為管理員及進階會員
$super_admin = "N";
$super_advanced = "N";
$mem_row = getkeyvalue2('memberinfo', 'member', "member_no = '$memberID'", 'admin,advanced');
$super_admin = $mem_row['admin'];
$super_advanced = $mem_row['advanced'];

$annual_day = isset($_GET['annual_day']) ? $_GET['annual_day'] : '';
$now = date("Y-m-d");


$mDB = "";
$mDB = new MywebDB();
$mDB2 = "";
$mDB2 = new MywebDB();

$mDB3 = "";
$mDB3 = new MywebDB();


$Qry = "SELECT contract_id,contract_caption FROM contract";

$mDB->query($Qry);
$casereport_list = "";

$casereport_list .= <<<EOT
<div class="m-auto" style="width:60%;min-height:300px;margin-bottom:100px;">
	<div class="w-100">
		<div class="w-100" style="min-width:1000px;">
EOT;

$total = $mDB->rowCount();
if ($total > 0) {

	while ($row = $mDB->fetchRow(2)) {
		$contract_id     = $row['contract_id'];
		$contract_caption = $row['contract_caption'];

		$casereport_list .= <<<EOT

<div class="w-100" style="overflow-x: auto;">
		<div class="w-100" style="min-width:1000px;">
			<div class="text-start size16 weight">$contract_caption</div>
			<hr class="style_b">

			<table class="table table-bordered border-dark" style="width:100%;">
				<thead class="table-light border-dark">
					<tr style="border-bottom: 1px solid #000;">
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">日期</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">實際工作內容</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">項次</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">合約項目</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">數量</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">單位</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">單價</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">複價/$</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">工作狀態</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施工人員</th>
						<th class="text-center text-nowrap vmiddle" style="width:5%;padding: 10px;background-color: #CBF3FC;">施工時間/hr</th>
					</tr>
				</thead>
				<tbody class="table-group-divider">
EOT;

		// 第二層查詢
	$Qry2 = "SELECT
        a.dispatch_id,
        a.dispatch_date,
        a.contract_id,
        b.seq,
        f.work_project AS work_project,
        b.status,
        b.actual_qty,
        b.remark,
        f.unit_price,
        f.unit
    FROM dispatch a
    LEFT JOIN dispatch_contract_details b
        ON b.dispatch_id = a.dispatch_id
        AND b.contract_id = a.contract_id
    LEFT JOIN contract_details f
        ON f.contract_id = b.contract_id AND f.seq = b.seq
    WHERE a.ConfirmSending = 'Y'
        AND a.contract_id = '$contract_id'
";

// $Qry2 .= ($annual_day != "") 
//     ? " AND a.dispatch_date = '$annual_day'" 
//     : " AND a.dispatch_date = '$now'";

$Qry2 .=" GROUP BY a.dispatch_date, a.contract_id, b.seq
    ORDER BY a.dispatch_date, b.seq";

$mDB2->query($Qry2);
$total2 = $mDB2->rowCount();
$SUM_total_price = 0;
$format_SUM_total_price = '0';
$SUM_attendance_hours = 0.0;

if ($total2 > 0) {
    while ($row2 = $mDB2->fetchRow(2)) {
        $dispatch_id   = $row2['dispatch_id'];
        $dispatch_date = $row2['dispatch_date'];
        $seq           = $row2['seq'];
        $work_project  = $row2['work_project'];
        $status        = $row2['status'];
        $actual_qty    = is_numeric($row2['actual_qty']) ? (float)$row2['actual_qty'] : 0.0;
        $unit_price    = is_numeric($row2['unit_price']) ? (float)$row2['unit_price'] : 0.0;
        $format_unit_price    = is_numeric($row2['unit_price']) 
                        ? number_format((float)$row2['unit_price'], 0, '.', ',') 
                        : '0';
        $unit          = $row2['unit'];
        $remark        = $row2['remark'];

        // 取得合約項目員工（使用 $mDB3 並用不同變數 $row3）
        $Qry3 = "SELECT a.dispatch_id, b.employee_name, a.attendance_hours, a.is_overtime
                 FROM dispatch_attendance_sub a
                 LEFT JOIN employee b ON b.employee_id = a.employee_id
                 WHERE a.dispatch_id = '$dispatch_id'
                   AND a.seq = '$seq'";

        $mDB3->query($Qry3);

        $employee_list = "";
        $row_count = 1;


        while ($row3 = $mDB3->fetchRow(2)) {
            $employee_name = $row3['employee_name'];
            $attendance_hours = is_numeric($row3['attendance_hours']) ? (float)$row3['attendance_hours'] : 0.0;
            $row_count++;
            $SUM_attendance_hours += $attendance_hours;
            if ($row_count  > 1) {
                $employee_list .= "";
            
            $employee_list .= <<<EOT
             <tr>
            <td class="text-center text-nowrap vmiddle" style="padding: 10px;">$employee_name</td>
            <td class="text-center text-nowrap vmiddle" style="padding: 10px;">$attendance_hours</td>
            </tr>

EOT;
}else{
                $employee_list .= <<<EOT
             <tr>
            <td class="text-center text-nowrap vmiddle" style="padding: 10px;"></td>
            <td class="text-center text-nowrap vmiddle" style="padding: 10px;"></td>
            </tr>
EOT;
}
        }
        // 金額
        $total_price = round($actual_qty * $unit_price, 2);
        $format_total_price = number_format((float)$total_price, 0, '.', ',');
        $SUM_total_price += $total_price;
        $format_SUM_total_price = number_format((float)$SUM_total_price, 0, '.', ',');


        $casereport_list .= <<<EOT
        <tr>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$dispatch_date</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$remark</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$seq</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$work_project</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$actual_qty</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$unit</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$format_unit_price</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$format_total_price</td>
            <td rowspan ="$row_count" class="text-center text-nowrap vmiddle" style="padding: 10px;">$status</td>
        </tr>
         $employee_list

EOT;
    } 
} else {
			$casereport_list .= <<<EOT
				<tr>
					<td colspan="11" class="text-center p-3">無任何符合查詢的資料</td>
				</tr>
EOT;
		} 

		$casereport_list .= <<<EOT
        <tfoot>
          <tr>
              <td class="text-center text-nowrap vmiddle" 
                  style="padding: 10px;background-color: #FFE699;font-weight:bold;font-size:16px;font-style:italic;">
                  合計
              </td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;font-weight:bold;font-size:16px;font-style:italic;">$format_SUM_total_price</td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;"></td>
              <td class="text-center text-nowrap vmiddle" style="padding: 10px;background-color: #FFE699;font-weight:bold;font-size:16px;font-style:italic;">$SUM_attendance_hours</td>
          </tr>
        </tfoot>
			</tbody>
		</table>
	</div>
</div>
EOT;

	} 

} else {
	$casereport_list .= <<<EOT
	<div class="size16 weight p-5 text-center">無任何符合查詢的資料</div>
EOT;
}

$casereport_list .= <<<EOT
		</div>
	</div>
</div>
EOT;


$mDB->remove();



$show_SmoothDivScroll = "";
if (!($detect->isMobile() || $detect->isTablet())) {

$show_SmoothDivScroll=<<<EOT
<script src="/os/SmoothDivScroll/js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="/os/SmoothDivScroll/js/jquery.mousewheel.min.js" type="text/javascript"></script>
<script src="/os/SmoothDivScroll/js/jquery.kinetic.min.js" type="text/javascript"></script>
<script src="/os/SmoothDivScroll/js/jquery.smoothdivscroll-1.3-min.js" type="text/javascript"></script>

<script type="text/javascript">
	// Initialize the plugin with no custom options
	$(document).ready(function () {
		// None of the options are set
		$("div#makeMeScrollable").smoothDivScroll({
			autoScrollingMode: ""
		});
	});
</script>
EOT;

}




$show_report = <<<EOT
<div class="container-fluid p-3 text-center bg-white mt-3">
  <h3 class="mb-4">合約日總表</h3>

  <div class="row justify-content-center g-2">

    <!-- 預計進場年份 -->
    <div class="col-auto">
      <div class="form-label fw-bold">日期：</div>
      <div class="input-group" id="annualdate" style="max-width: 180px;">
        <input type="text" class="form-control" id="annual_day" name="annual_day"
          placeholder="請輸入日期" value="$annual_day">
        <button class="btn btn-outline-secondary" type="button" data-target="#annualdate"
          data-toggle="datetimepicker">
          <i class="bi bi-calendar"></i>
        </button>
      </div>
    </div>

    

    <!-- 查詢按鈕 -->
    <div class="col-auto align-self-end">
      <button type="button" class="btn btn-success mt-2" onclick="chdatetime();">
        <i class="fas fa-check"></i>&nbsp;查詢
      </button>
    </div>
  </div>

  <!-- 固定在右上角的列印與關閉按鈕 -->
  <div class="btn-group print" role="group"
    style="position: fixed; top: 10px; right: 10px; z-index: 9999;">
    <button class="btn btn-info btn-lg" type="button" onclick="window.print();">
      <i class="bi bi-printer"></i>&nbsp;列印
    </button>
    <button class="btn btn-danger btn-lg" type="button" onclick="window.close();">
      <i class="bi bi-power"></i>&nbsp;關閉
    </button>
  </div>

<!-- DateTime Picker Script -->
<script type="text/javascript">
  $(function () {
    $('#annualdate').datetimepicker({
      locale: 'zh-tw',
      format: "YYYY-MM-DD",
      viewMode: "days",
      allowInputToggle: true
    });
  });
</script>

<style>
  .bootstrap-datetimepicker-widget {
    z-index: 1050 !important;
    position: absolute !important;
  }
</style>
</div>
<div style="margin-bottom: 150px;">
	$casereport_list
</div>
EOT;

$show_center = <<<EOT
<link rel="Stylesheet" type="text/css" href="/os/SmoothDivScroll/css/smoothDivScroll.css" />

<style>
#makeMeScrollable {
	width:100%;
	position: relative;
}

table.table-bordered {
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > th {
	border:1px solid black;
}
table.table-bordered > tbody > tr > td {
	border:1px solid black;
}

@media print {
	.print {
		display: none !important;
	}
}

</style>

$show_report

<script>

document.addEventListener("DOMContentLoaded", function () {
    const yearDropdown = document.getElementById("case_year");


    if (yearDropdown) {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedYear = urlParams.get("case_year");
        if (selectedYear) {
            yearDropdown.value = selectedYear;
        }
    }
});

function chdatetime() {

	var annual_day = $('#annual_day').val();
    window.location = '?ch=contractitem_02&fm=contractitem&annual_day=' + annual_day ;
    return false;
	// 導向查詢（保留參數查資料）
	window.location.href = newUrl;

	// 接著在載入後使用 JS 清掉 input 顯示
	// 加在頁面載入後：
	// $('#annual_day').val('');

}


</script>

$show_SmoothDivScroll

EOT;


?>