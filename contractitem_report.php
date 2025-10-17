<?php


//error_reporting(E_ALL); 
//ini_set('display_errors', '1');



require_once '/website/os/Mobile-Detect-2.8.34/Mobile_Detect.php';
$detect = new Mobile_Detect;

if( $detect->isMobile() && !$detect->isTablet() ){
	$isMobile = 1;
} else {
	$isMobile = 0;
}


$fm = $_GET['fm'];
$mDB = "";
$mDB = new MywebDB();




$mDB2 = "";
$mDB2 = new MywebDB();


$mDB2->remove();

$list_view=<<<EOT
<div class="w-100 px-3 py-2">
	<table class="table dataTable table-bordered border-dark w-100" id="report_01_table" style="min-width:1720px;">
    <colgroup>
        <col style="width:3%">
        <col style="width:7%">
        <col style="width:2%">
        <col style="width:7%">
        <col style="width:3%">
        <col style="width:3%">
        <col style="width:3%">
        <col style="width:3%">
        <col style="width:1%">
        <col style="width:3%">
        <col style="width:1%">
        <col style="width:1%">
		<col style="width:1%">
		<col style="width:1%">
		<col style="width:1%">
        <col style="width:3%">
    </colgroup>
    
    <thead class="table-light border-dark">
        <tr>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">日期</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">實際工作內容</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">項次</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">合約項目</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">數量</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">單位</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">單價</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">複價</th>  
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">工作狀態</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">施工人員</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">施工人數</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">施工天數/day</th>
            <th class="text-center text-nowrap" style="background-color:#E2EFDA;">出工日薪</th>
			<th class="text-center text-nowrap" style="background-color:#E2EFDA;">加班時數/hr</th>
			<th class="text-center text-nowrap" style="background-color:#E2EFDA;">加班費</th>
			<th class="text-center text-nowrap" style="background-color:#E2EFDA;">出工小計</th>



        </tr>
    </thead>

    <tbody class="table-group-divider">
        <tr>
            <td colspan="14" class="dataTables_empty">資料載入中...</td>
        </tr>
    </tbody>

    <tfoot class="table-light border-dark">
        <tr>
            <th class="text-center size14 weight" style="background-color:#FFEBAC;">總計 :</th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
            <th class="text-center" style="background-color:#FFEBAC;"></th>
			<th class="text-center" style="background-color:#FFEBAC;"></th>
			<th class="text-center" style="background-color:#FFEBAC;"></th>
			<th class="text-center" style="background-color:#FFEBAC;"></th>
        </tr>
    </tfoot>
</table>
</div>
EOT;

$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}

$show_bulider_report=<<<EOT
<style>
#report_01_table {
	width: 100% !Important;
	margin: 5px 0 0 0 !Important;
}
.dataTable td, .dataTable th {
    padding: 2px; /* 設定框格內的 padding */
	vertical-align: middle; /* 垂直置中 */
}
</style>

$list_view

<script>
	
	function getOffsetByDevice() {
		const screenWidth = window.innerWidth;

		if (screenWidth <= 768) {
			// 手機裝置
			return 250;
		} else if (screenWidth <= 1024) {
			// 平板裝置
			return 300;
		} else {
			// 桌機裝置
			return 350;
		}
	}

	// 按鍵附值
	function search() {
    $('#report_01_table').DataTable().ajax.reload();
}	
	
	$(document).ready(function () {
    var scrollY = $(window).height() - getOffsetByDevice();

    $('#report_01_table').DataTable({
        processing: false,
        responsive: {
            details: true
        },
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        language: {
            "sUrl": "$dataTable_de"
        },
        scrollY: scrollY + "px",
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: true,
        fixedColumns: {
            leftColumns: 3
        },
        deferRender: true,
        ajax: {
            url: "/smarty/templates/$site_db/$templates/sub_modal/project/func08/contractitem_ms/ajax_report_01.php",
            type: "GET",
           data: function (d) {
                d.contract_id   = $('#contract_id').val()   || '';
				d.annual_month  = $('#annual_month').val()  || ''; 
				console.log('sent params:', d); 
				
            }
        },
			"rowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

				
				//日期
				var dispatch_date = "";
				if (aData[0] != null && aData[0] != "")
					dispatch_date = '<div class="text-center text-nowrap">'+aData[0]+'</div>';

				$('td:eq(0)', nRow).html( dispatch_date );

				// 實際工作內容
				var remark = "";
				if (aData[1] != null && aData[1] != "")
					remark = '<div class="text-center text-nowrap">'+aData[1]+'</div>';

				$('td:eq(1)', nRow).html( remark );


				//項次
				var seq = "";
				if (aData[2] != null && aData[2] != "")
					seq = '<div class="text-center">'+aData[2]+'</div>';
		
				$('td:eq(2)', nRow).html( seq );

				//合約項目
				var contract_item = "";
				if (aData[3] != null && aData[3] != "")
					contract_item = '<div class="text-center text-nowrap">'+aData[3]+'</div>';

				$('td:eq(3)', nRow).html( contract_item );

				// 數量
				var qty = (aData[4] != null && aData[4] !== "" && Number(aData[4]) !== 0) ? Number(aData[4]) : "";
				var actual_qty = '<div class="text-center text-nowrap">' + (qty !== "" ? qty.toLocaleString() : "") + '</div>';

				$('td:eq(4)', nRow).html(actual_qty);

				// 單位
				var unit = "";
				if (aData[5] != null && aData[5] != "")
					unit = '<div class="text-center text-nowrap">'+aData[5]+'</div>';

				$('td:eq(5)', nRow).html( unit );

				// 單價
				var price = (aData[6] != null && aData[6] !== "") ? Number(aData[6]) : 0;
				var unit_price = '<div class="text-center text-nowrap">' + price.toLocaleString() + '</div>';

				$('td:eq(6)', nRow).html(unit_price);

				

				//複價
				var total_price = "";

				var total = (aData[7] != null && aData[7] !== "") ? Number(aData[7]) : 0;
				var total_price = '<div class="text-center text-nowrap">' + total.toLocaleString() + '</div>';

				$('td:eq(7)', nRow).html( total_price );


				

				// 工作狀態
				var status = "";
				if (aData[8] != null && aData[8] != "")
					status = '<div class="text-center text-nowrap">'+aData[8]+'</div>';

				$('td:eq(8)', nRow).html( status );

				// 施工人員
				var employees = "";
				if (aData[9] != null && aData[9] != "")
					employees = '<div class="text-center text-nowrap">'+aData[9]+'</div>';

				$('td:eq(9)', nRow).html( employees );


				// 施工人數
				var employee_count = "";
				if (aData[10] != null && aData[10] != "")
					employee_count = '<div class="text-center text-nowrap">'+aData[10]+'</div>';

				$('td:eq(10)', nRow).html( employee_count );

				// 施工時間
				var attendance_days = "";
				if (aData[11] != null && aData[11] != "")
					attendance_days = '<div class="text-center text-nowrap">'+aData[11]+'</div>';

				$('td:eq(11)', nRow).html( attendance_days );


				// 日薪
				var val = (aData[12] != null && aData[12] !== "") ? parseFloat(aData[12]) : 0;
				var formatted = val.toLocaleString('zh-TW', { minimumFractionDigits: 0 });
				var regular_pay = '<div class="text-center text-nowrap">' + formatted + '</div>';

				$('td:eq(12)', nRow).html( regular_pay );

				// 加班時數
				var val = (aData[13] != null && aData[13] !== "") ? parseFloat(aData[13]) : 0;
				var formatted = val.toLocaleString('zh-TW', { minimumFractionDigits: 0 });
				var attendance_overhours = '<div class="text-center text-nowrap">' + formatted + '</div>';

				$('td:eq(13)', nRow).html( attendance_overhours );

				// 加班費
				var val = (aData[14] != null && aData[14] !== "") ? parseFloat(aData[14]) : 0;
				var formatted = val.toLocaleString('zh-TW', { minimumFractionDigits: 0 });
				var overtime_pay = '<div class="text-center text-nowrap">' + formatted + '</div>';

				$('td:eq(14)', nRow).html( overtime_pay );

				// 加班費
				var val = (aData[15] != null && aData[15] !== "") ? parseFloat(aData[15]) : 0;
				var formatted = val.toLocaleString('zh-TW', { minimumFractionDigits: 0 });
				var work_summary_pay = '<div class="text-center text-nowrap">' + formatted + '</div>';

				$('td:eq(15)', nRow).html( work_summary_pay );

				

				

				return nRow;
			},
			"footerCallback": function(row, data, start, end, display) {
				var api = this.api();

				// 確保 number_format 可用
				function number_format(number, decimals = 0) {
					return number.toLocaleString('zh-TW', { minimumFractionDigits: decimals });
				}

				// 累加指定欄位
				var sumColumn = function(i) {
					var colData = api.column(i).data().toArray();
					return colData.reduce(function (a, b) {
						var x = parseFloat(String(a).replace(/<[^>]*>/g, '').replace(/,/g, '')) || 0;
						var y = parseFloat(String(b).replace(/<[^>]*>/g, '').replace(/,/g, '')) || 0;
						return x + y;
					}, 0);
					};



				var SUM_total_price = number_format(sumColumn(7));
				var SUM_employee_count = number_format(sumColumn(10));
				var SUM_attendance_days = number_format(sumColumn(11));
				var SUM_regular_pay = number_format(sumColumn(12));
				var SUM_attendance_overhours = number_format(sumColumn(13));
				var SUM_overtime_pay = number_format(sumColumn(14));
				var SUM_work_summary_pay = number_format(sumColumn(15));

				
				$(api.column(7).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_total_price + '</div>');
				$(api.column(10).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_employee_count + '</div>');
				$(api.column(11).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_attendance_days + '</div>');
				$(api.column(12).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_regular_pay + '</div>');
				$(api.column(13).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_attendance_overhours + '</div>');
				$(api.column(14).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_overtime_pay + '</div>');
				$(api.column(15).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_work_summary_pay + '</div>');
				}

				


		
		});
	
	} );

	$(document).ready(function () {
		initTableWithDynamicHeight();

		// 若視窗尺寸改變，也重新計算 scrollY
		$(window).resize(function () {
		initTableWithDynamicHeight();
		});
	});
	
</script>

EOT;

?>