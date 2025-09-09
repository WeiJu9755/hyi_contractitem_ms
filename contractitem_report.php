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
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">狀態(1)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">狀態(2)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">工程名稱<br>案件編號</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">合約號碼<br>ERP專案代號</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">區域</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">承攬模式</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">所屬公司</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:7%;">上包<br>公司名稱</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">上包<br>訂約日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">上包簽約金額(含稅)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">預收款<br>已請款期程</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">預計<br>進場日</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">預計<br>完工日</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">鋁模材料<br>利舊/新購</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">標準層模板數量<br>(M2)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">屋突層模板數量<br>(M2)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #E2EFDA;width:3%;">材料用量<br>(M2)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">通知志特<br>報價日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">志特編號</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">下單志特<br>預定日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">志特材料<br>採購主合約進度</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">與志特<br>訂約日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">志特<br>合約金額(RMB)</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #FFF2CC;width:3%;">第一批大貨<br>到港日期</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #DDEBF7;width:3%;">代工下包<br>發包進度</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #DDEBF7;width:3%;">放樣<br>發包進度</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #DDEBF7;width:15%;">下包公司名稱</th>
				<th scope="col" class="text-center text-nowrap" style="background-color: #DDEBF7;width:3%;">下包簽約金額</th>
				<th scope="col" class="text-center text-nowrap" style="width:3%;">實際進場日</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			<tr>
				<td colspan="28" class="dataTables_empty">資料載入中...</td>
			</tr>
		</tbody>
		<tfoot class="table-light border-dark">
			<tr style="border-bottom: 1px solid #000;">
				<th colspan = "9"class="text-center size14 weight" style="background-color: #FFEBAC;">總計 : </th>
			
				
				
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>			
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>
				<th scope="col" class="text-center" style="background-color: #FFEBAC;"></th>

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
            url: "/smarty/templates/$site_db/$templates/sub_modal/project/func02/designreport_ms/ajax_report_05.php",
            type: "GET",
            data: function (d) {
                d.status1 = $('#status1').val(); 
				d.status2 = $('#status2').val(); 
				d.region = $('#region').val(); 
				d.ContractingModel = $('#ContractingModel').val();
				d.company_id = $('#company_id').val();
            }
        },
			"rowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {

				
				//狀態(1)
				var status1 = "";
				if (aData[0] != null && aData[0] != "")
					status1 = '<div class="text-center text-nowrap">'+aData[0]+'</div>';

				$('td:eq(0)', nRow).html( status1 );

				//狀態(2)
				var status2 = "";
				if (aData[1] != null && aData[1] != "")
					status2 = '<div class="text-center text-nowrap">'+aData[1]+'</div>';

				$('td:eq(1)', nRow).html( status2 );


				//案件名稱
				var construction_id = "";
				if (aData[4] != null && aData[4] != "")
					construction_id = '<div class="text-center">'+aData[4]+'</div>';
				//案件編號
				var case_id = "";
				if (aData[5] != null && aData[5] != "")
					case_id = '<div class="text-center text-nowrap">'+aData[5]+'</div>';

				$('td:eq(2)', nRow).html( construction_id+case_id );

				//合約號碼(ERP專案代號)
				var ERP_no = "";
				if (aData[49] != null && aData[49] != "")
					ERP_no = '<div class="text-center text-nowrap">'+aData[49]+'</div>';

				$('td:eq(3)', nRow).html( ERP_no );

				//區域
				var region = "";
				if (aData[6] != null && aData[6] != "")
					region = '<div class="text-center text-nowrap">'+aData[6]+'</div>';

				$('td:eq(4)', nRow).html( region );

				// 承攬模式
				var ContractingModel = "";
				if (aData[7] != null && aData[7] != "")
					ContractingModel = '<div class="text-center text-nowrap">'+aData[7]+'</div>';

				$('td:eq(5)', nRow).html( ContractingModel );

				// 所屬公司
				var company_id = "";
				if (aData[50] != null && aData[50] != "")
					ContractingModel = '<div class="text-center text-nowrap">'+aData[50]+'</div>';

				$('td:eq(6)', nRow).html( ContractingModel );

				

				//上包公司名稱
				var builder_name = "";
				if (aData[3] != null && aData[3] != "")
					builder_name = '<div class="text-center">'+aData[3]+'</div>';

				// 營造商名稱
				var contractor_name = "";
				if (aData[52] != null && aData[52] != "")
					contractor_name = '<div class="text-center text-nowrap">'+aData[52]+'</div>';

				$('td:eq(7)', nRow).html( builder_name+contractor_name );

				// 上包訂約日期
				var contract_date = "";
				if (aData[8] != null && aData[8] != "")
					contract_date = '<div class="text-center text-nowrap">'+aData[8]+'</div>';

				$('td:eq(8)', nRow).html( contract_date );

				// 上包簽約金額(含稅)
				var total_contract_amt = "";
				if (aData[9] && parseFloat(aData[9]) !== 0) {
					var formatted = parseFloat(aData[9]).toLocaleString('zh-TW', {
						minimumFractionDigits: 0
					});
					total_contract_amt = '<div class="text-center text-nowrap">' + formatted + '</div>';
				}

				$('td:eq(9)', nRow).html(total_contract_amt);

				// 預收款已請款期程
				var advance_payment_schedule_parts = [];

					if (aData[10]) advance_payment_schedule_parts.push('第一期預收款請款 : ' + aData[10]);
					if (aData[11] && aData[11] !== '0000-00-00') advance_payment_schedule_parts.push('第一期請款日期 : ' + aData[11]);
					if (aData[12]) advance_payment_schedule_parts.push('第二期預收款請款 : ' + aData[12]);
					if (aData[13] && aData[13] !== '0000-00-00') advance_payment_schedule_parts.push('第二期請款日期 : ' + aData[13]);
					if (aData[14]) advance_payment_schedule_parts.push('第三期預收款請款 : ' + aData[14]);
					if (aData[15] && aData[15] !== '0000-00-00') advance_payment_schedule_parts.push('第三期請款日期 : ' + aData[15]);

					var advance_payment_schedule = "";
					if (advance_payment_schedule_parts.length > 0) {
						advance_payment_schedule = '<div class="text-center text-nowrap">' + advance_payment_schedule_parts.join('<br>') + '</div>';
					}

				$('td:eq(10)', nRow).html(advance_payment_schedule);

				// 預計進場日
				var estimated_arrival_date = "";
				if (aData[16] != null && aData[16] != "0000-00-00")
					estimated_arrival_date = '<div class="text-center text-nowrap">'+aData[16]+'</div>';

				$('td:eq(11)', nRow).html( estimated_arrival_date );

				// 預計完工日
				var completion_date = "";
				if (aData[51] != null && aData[51] != "")
					completion_date = '<div class="text-center text-nowrap">'+aData[51]+'</div>';

				$('td:eq(12)', nRow).html( completion_date );

				// 鋁模材料利舊/新購
				var geto_formwork = "";
				if (aData[18] != null && aData[18] != "")
					geto_formwork = '<div class="text-center text-nowrap">'+aData[18]+'</div>';

				$('td:eq(13)', nRow).html( geto_formwork );

				// 標準層模板數量(M2)
				var std_layer_template_qty = "";
				if (aData[19] && parseFloat(aData[19]) !== 0) {
					var formatted = parseFloat(aData[19]).toLocaleString('zh-TW', {
						minimumFractionDigits: 2
					});
					std_layer_template_qty = '<div class="text-center text-nowrap">' + formatted + '</div>';
				}

				$('td:eq(14)', nRow).html( std_layer_template_qty );

				// 屋突層模板數量(M2)
				var roof_protrusion_template_qty = "";
				if (aData[20] && parseFloat(aData[20]) !== 0) {
					var formatted = parseFloat(aData[20]).toLocaleString('zh-TW', {
						minimumFractionDigits: 0
					});
					roof_protrusion_template_qty = '<div class="text-center text-nowrap">' + formatted + '</div>';
				}

				$('td:eq(15)', nRow).html( roof_protrusion_template_qty );

				// 材料用量(M2)
				var material_use_qty = "";

				// 後端傳來的值
				var qty1 = Number(aData[19]) || 0; // std_layer_template_qty
				var qty2 = Number(aData[20]) || 0; // roof_protrusion_template_qty

				var total_qty = qty1 + qty2;

				if (total_qty !== 0) {
					var formatted_qty = total_qty.toLocaleString('zh-TW', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
					material_use_qty = '<div class="text-center text-nowrap">' + formatted_qty + '</div>';
				} else {
					material_use_qty = ''; // 等於 0 → 不顯示
				}

				$('td:eq(16)', nRow).html(material_use_qty);

				// 通知志特報價日期
				var geto_order_date = "";

				if (aData[22] != null && aData[22] != "") {
					geto_order_date = '<div class="text-center text-nowrap">'+aData[22]+'</div>';
				}

				   $('td:eq(17)', nRow).html( geto_order_date );
				
				// 志特編號
				var geto_no = "";

				if (aData[23] != null && aData[23] != "") {	
					geto_no = '<div class="text-center text-nowrap">'+aData[23]+'</div>';
				}

				   $('td:eq(18)', nRow).html( geto_no );

				// 下單志特預定日期
				var estimated_geto_order_date = "";

				if (aData[48] && aData[48] !== "0000-00-00") {
					estimated_geto_order_date = '<div class="text-center text-nowrap">' + aData[48] + '</div>';
				}
					$('td:eq(19)', nRow).html( estimated_geto_order_date );
				
				// 志特材料採購主合約進度

				var material_purchase_progress = "";

				if (aData[21] != null && aData[21] != "") {
					material_purchase_progress = '<div class="text-center text-nowrap">'+aData[21]+'</div>';
				}

				   $('td:eq(20)', nRow).html( material_purchase_progress );
				
				// 志特預定交付日期
				var geto_contract_date = "";

				if (aData[25] && aData[25] !== "0000-00-00") {
					geto_contract_date = '<div class="text-center text-nowrap">' + aData[25] + '</div>';
				}

				$('td:eq(21)', nRow).html(geto_contract_date);
				

				// 志特合約金額(RMB)
				var geto_quotation = "";
				if (aData[24] != null && aData[24] != "") {
					var formatted = parseFloat(aData[24]).toLocaleString('zh-TW', {
						minimumFractionDigits: 0
					});
					geto_quotation = '<div class="text-center text-nowrap">' + formatted + '</div>';
					}

				   $('td:eq(22)', nRow).html( geto_quotation );

				
				
				// 第一批大貨到港日期
				var material_import_date = "";

				if (aData[26] != null && aData[26] != "0000-00-00") {
					material_import_date = '<div class="text-center text-nowrap">'+aData[26]+'</div>';
				}

				   $('td:eq(23)', nRow).html( material_import_date );

				// 代工下包發包進度
				var subcontract_progress = "";

				if (aData[27] != null && aData[27] != "") {
					subcontract_progress = '<div class="text-center text-nowrap">'+aData[27]+'</div>';
				}

				   $('td:eq(24)', nRow).html( subcontract_progress );
				
				// 放樣發包進度
				var subcontracting_progress2 = "";
				if (aData[28] != null && aData[28] != "") {
					subcontracting_progress2 = '<div class="text-center text-nowrap">'+aData[28]+'</div>';
				}
					$('td:eq(25)', nRow).html( subcontracting_progress2 );

				// 下包公司名稱
				var subcontractor_name_group = [];

				if (aData[30]) subcontractor_name_group.push(aData[30]);
				if (aData[33]) subcontractor_name_group.push(aData[33]);
				if (aData[36]) subcontractor_name_group.push(aData[36]);
				if (aData[39]) subcontractor_name_group.push(aData[39]);


				var subcontractor_name = "";
				if (subcontractor_name_group.length > 0) {
					subcontractor_name = '<div class="text-center text-nowrap">下包商 : <br>' + subcontractor_name_group.join('<br>') + '</div>';
				}

				// 放樣公司名稱
				var stakeout_subcontractor_name_group = [];

				if (aData[42]) stakeout_subcontractor_name_group.push(aData[42]);
				if (aData[54]) stakeout_subcontractor_name_group.push(aData[54]);
				if (aData[57]) stakeout_subcontractor_name_group.push(aData[57]);

				var stakeout_subcontractor_name = "";

				if (stakeout_subcontractor_name_group.length > 0) {
				stakeout_subcontractor_name =
					'<div class="text-center text-nowrap">放樣 : <br>' +
					stakeout_subcontractor_name_group.join('<br>') +
					'</div>';
				}


				// 檢核公司名稱
				var inspection_subcontractor_name = "";
				if (aData[45] != null && aData[45] != "") {
					inspection_subcontractor_name = '<div class="text-center text-nowrap">檢核 : <br>'+aData[45]+'</div>';
				}

				
				// 合併下包商資訊
				var subcontractor_full_info = "";

				if (stakeout_subcontractor_name || subcontractor_name || inspection_subcontractor_name) {
					subcontractor_full_info =  subcontractor_name + stakeout_subcontractor_name + inspection_subcontractor_name;
					$('td:eq(26)', nRow).html(subcontractor_full_info);
				} else {
					$('td:eq(26)', nRow).html(""); // 或顯示 "無"
				}
				

				// 下包簽約金額
				var total_contract_amt_group = [];

				function formatNumber(n) {
					return parseFloat(n).toLocaleString('zh-TW', {
						minimumFractionDigits: 0
					});
				}

				if (aData[31] && parseFloat(aData[31]) !== 0) total_contract_amt_group.push(formatNumber(aData[31]));
				if (aData[34] && parseFloat(aData[34]) !== 0) total_contract_amt_group.push(formatNumber(aData[34]));
				if (aData[37] && parseFloat(aData[37]) !== 0) total_contract_amt_group.push(formatNumber(aData[37]));
				if (aData[40] && parseFloat(aData[40]) !== 0) total_contract_amt_group.push(formatNumber(aData[40]));


				// 放樣合約總價
				var stakeout_contract_amt_group = [];
				if (aData[43] && parseFloat(aData[43]) !== 0) stakeout_contract_amt_group.push(formatNumber(aData[43]));
				if (aData[55] && parseFloat(aData[55]) !== 0) stakeout_contract_amt_group.push(formatNumber(aData[55]));
				if (aData[58] && parseFloat(aData[58]) !== 0) stakeout_contract_amt_group.push(formatNumber(aData[58]));


				// 檢核合約總價
				var inspection_contract_amt = "";
				if (aData[46] != null && aData[46] !== "" && parseFloat(aData[46]) !== 0) {
					inspection_contract_amt = '<div class="text-center text-nowrap">檢核 : <br>' + formatNumber(aData[46]) + '</div>';
				}
					
				var total_contract_amt_str = "";
				if (total_contract_amt_group.length > 0) {
					total_contract_amt_str = '<div class="text-center">下包商 : <br>' + total_contract_amt_group.join('<br>') + '</div>';
					
				}

				var stakeout_contract_amt = "";
				if (stakeout_contract_amt_group.length > 0) {
					total_contract_amt_str = '<div class="text-center">放樣 : <br>' + stakeout_contract_amt_group.join('<br>') + '</div>';
				}
				
				var contract_amt_full_info = "";
				if (stakeout_contract_amt || total_contract_amt_str || inspection_contract_amt) {
					contract_amt_full_info = total_contract_amt_str  + stakeout_contract_amt + inspection_contract_amt;
					$('td:eq(27)', nRow).html(contract_amt_full_info);
					} else {
					$('td:eq(27)', nRow).html(""); // 或顯示 "無"
				}


				

				// 實際進場日
				var actual_entry_date = "";

				if (aData[47] && aData[47] !== "0000-00-00") {
					actual_entry_date = '<div class="text-center text-nowrap">' + aData[47] + '</div>';
				}

				$('td:eq(28)', nRow).html(actual_entry_date);

				return nRow;
			},
			"footerCallback": function( row, data, start, end, display ) {
				var api = this.api();
				
				// 1. 定義合計函數，使用純數字避免 NaN
				var sumColumn = function(i) {
					var colData = api.column(i).data().toArray();
					console.log("第 " + i + " 欄原始資料：", colData);
					return colData.reduce(function (a, b) {
						var x = parseFloat(String(a).replace(/<[^>]*>/g, '').replace(/,/g, '')) || 0;
						var y = parseFloat(String(b).replace(/<[^>]*>/g, '').replace(/,/g, '')) || 0;
						return x + y;
					}, 0);
				};

			

					
				var SUM_total_contract_amt = number_format(sumColumn(9));
				var SUM_std_layer_template_qty = number_format(sumColumn(19));
				var SUM_roof_protrusion_template_qty = number_format(sumColumn(20));
				var SUM_material_use_qty = number_format(sumColumn(19) + sumColumn(20));
				var SUM_geto_quotation = number_format(sumColumn(24));
				
				var sumSubcontractAmt = 0;
				data.forEach(function(row) {
					[31, 34, 37, 40, 43, 46, 55, 58].forEach(function(idx) {
						var val = parseFloat(String(row[idx]).replace(/<[^>]*>/g, '').replace(/,/g, '')) || 0;
						sumSubcontractAmt += val;
					});
				});

				var SUM_total_contract_amt_group = number_format(sumSubcontractAmt);
				

				
			
				// 3. 將合計結果插入到 footer 中
				//$(api.column(2).footer()).html(total.toFixed(2)); // 保留兩位小數
				$(api.column(9).footer()).html('<div class="text-center size14 weight text-nowrap"> ' + SUM_total_contract_amt + '</div>');
				$(api.column(14).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_std_layer_template_qty + '</div>');
				$(api.column(15).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_roof_protrusion_template_qty + '</div>');
				$(api.column(16).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_material_use_qty + '</div>');
				$(api.column(22).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_geto_quotation + '</div>');
				$(api.column(27).footer()).html('<div class="text-center size14 weight text-nowrap">' + SUM_total_contract_amt_group + '</div>');
				


				
				
				
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