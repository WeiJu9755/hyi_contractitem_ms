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
	<table class="table dataTable table-bordered border-dark w-100" id="report_05_table" style="min-width:1720px;">
		<thead class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">工作項目</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:2%;">項次</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">合約項目</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">數量</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">單位</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">單價</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">複價</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">實際工作內容</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:1%;">工作狀態</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">施工人員</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:1%;">施工人數</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:1%;">施工時間</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">出工小計<br>(出工+加班)</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="28" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
		
	</table>
</div>
EOT;

$scroll = true;
if (!($detect->isMobile() && !$detect->isTablet())) {
	$scroll = false;
}

$show_bulider_report=<<<EOT
<style>
#report_05_table {
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
    $('#report_05_table').DataTable().ajax.reload();
}	
	
	$(document).ready(function () {
    var scrollY = $(window).height() - getOffsetByDevice();

    $('#report_05_table').DataTable({
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

				//工作項目
				var work_project = "";
				if (aData[3] != null && aData[3] != "")
					work_project = '<div class="text-center text-nowrap">'+aData[3]+'</div>';

				$('td:eq(1)', nRow).html( work_project );


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

				//數量
				var qty = (aData[5] != null && aData[5] !== "") ? Number(aData[5]) : 0;
				var actual_qty = '<div class="text-center text-nowrap">' + qty.toLocaleString() + '</div>';

				$('td:eq(4)', nRow).html(actual_qty);

				// 單位
				var unit = "";
				if (aData[7] != null && aData[7] != "")
					unit = '<div class="text-center text-nowrap">'+aData[7]+'</div>';

				$('td:eq(5)', nRow).html( unit );

				// 單價
				var price = (aData[6] != null && aData[6] !== "") ? Number(aData[6]) : 0;
				var unit_price = '<div class="text-center text-nowrap">' + price.toLocaleString() + '</div>';

				$('td:eq(6)', nRow).html(unit_price);

				

				//複價
				var total_price = "";

				var total = (aData[12] != null && aData[12] !== "") ? Number(aData[12]) : 0;
				var total_price = '<div class="text-center text-nowrap">' + total.toLocaleString() + '</div>';

				$('td:eq(7)', nRow).html( total_price );


				// 實際工作內容
				var remark = "";
				if (aData[10] != null && aData[10] != "")
					remark = '<div class="text-center text-nowrap">'+aData[10]+'</div>';

				$('td:eq(8)', nRow).html( remark );

				// 工作狀態
				var remark = "";
				if (aData[4] != null && aData[4] != "")
					remark = '<div class="text-center text-nowrap">'+aData[4]+'</div>';

				$('td:eq(9)', nRow).html( remark );

				// 施工人員
				var employees = "";
				if (aData[9] != null && aData[9] != "")
					employees = '<div class="text-center text-nowrap">'+aData[9]+'</div>';

				$('td:eq(10)', nRow).html( employees );


				// 施工人數
				var employee_count = "";
				if (aData[8] != null && aData[8] != "")
					employee_count = '<div class="text-center text-nowrap">'+aData[8]+'</div>';

				$('td:eq(11)', nRow).html( employee_count );

				// 施工時間
				var attendance_days = "";
				if (aData[11] != null && aData[11] != "")
					attendance_days = '<div class="text-center text-nowrap">'+aData[11]+'</div>';

				$('td:eq(12)', nRow).html( attendance_days );


				// 出工小計
				var val = (aData[13] != null && aData[13] !== "") ? parseFloat(aData[13]) : 0;
				var formatted = val.toLocaleString('zh-TW', { minimumFractionDigits: 0 });
				var sub_total = '<div class="text-center text-nowrap">' + formatted + '</div>';

				$('td:eq(13)', nRow).html( sub_total );

				

				return nRow;
			},
		
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