<?php

$income_rs = $this->company_financial_model->get_income_value_new('Revenue');

// var_dump($income_rs); die();

$array_counter = count($income_rs);

$income_result = '';
$total_income = 0;
for ($i=0; $i < $array_counter ; $i++) { 

	$total_income += $income_rs[$i]['value'];
	$department_id = $income_rs[$i]['department_id'];
	$income_result .='<tr>
							<td class="text-left">'.strtoupper($income_rs[$i]['name']).'</td>
							<td class="text-right">
							'.number_format($income_rs[$i]['value'],2).'</td>
							</tr>';

}

$income_result .='<tr>
							<td class="text-left"><b>Total Income</b></td>
							<td class="text-right"><b>'.number_format($total_income,2).'</b></td>
							</tr>';

// $income_result = '';
// $total_income = 0;
// if($income_rs->num_rows() > 0)
// {
// 	foreach ($income_rs->result() as $key => $value) {
// 		# code...
// 		$total_amount = $value->total_amount;
// 		$transactionName = $value->parent_service;
// 		$service_id = $value->service_id;
// 		$total_income += $total_amount;
// 		$income_result .='<tr>
// 							<td class="text-left">'.strtoupper($transactionName).'</td>
// 							<td class="text-right">
// 							<a href="'.site_url().'company-financials/services-bills/'.$service_id.'" >'.number_format($total_amount,2).'</a></td>
// 							</tr>';
// 	}
	
// }




// $operation_rs = $this->company_financial_model->get_cog_value('Expense');





$cog_result = '';
$total_cog = 0;
$start_date = $this->company_financial_model->get_inventory_start_date();

$closing_stock =  $this->company_financial_model->get_opening_stock_value();
$stock_list = $this->company_financial_model->get_product_purchases_new($start_date);
$array_count = count($stock_list);

// var_dump($array_count);die();
$total_other_purchases = 0;//$this->company_financial_model->get_product_other_purchases($start_date);
$total_return_outwards = 0;//$this->company_financial_model->get_product_return_outwards($start_date);
$total_sales = 0;//$this->company_financial_model->get_product_sales();
$total_other_deductions = 0;//$this->company_financial_model->get_total_other_deductions();
$total_purchases = 0;
for ($i=0; $i < $array_count ; $i++) { 
	# code...
	$name = $stock_list[$i]['name'];

	if($name === "Additions")
	{
		$total_other_purchases = $stock_list[$i]['value'];
	}
	else if($name === "Sales")
	{
		$total_sales = -$stock_list[$i]['value'];
	}
	else if($name === "Purchases")
	{
		$total_purchases = $stock_list[$i]['value'];
	}
	else if($name === "Deductions")
	{
		$total_other_deductions = -$stock_list[$i]['value'];
	}
	else if($name === "Return Outwards")
	{
		$total_return_outwards = -$stock_list[$i]['value'];
	}
}
// var_dump($stock_list);die();
$current_stock = (($total_purchases+$closing_stock+$total_other_purchases) - ($total_sales + $total_return_outwards + $total_other_deductions));
$total_cog = $total_purchases+$closing_stock-$current_stock;


$non_pharm_query = $this->company_financial_model->get_non_pharm_purchases();
$non_pharm_purchases = 0;
$non_pharm = '';

if($non_pharm_query->num_rows() > 0)
{
	foreach ($non_pharm_query->result() as $key => $value_category) {
		# code...

		$category_name = $value_category->transactionCategory;
		$category_id = $value_category->category_id;
		$category_value = $value_category->cr_amount;

		$non_pharm .='<tr>
							<td class="text-left">'.strtoupper($category_name).'</td>
							<td class="text-right">'.number_format($category_value,2).'</td>
							</tr>';
		$non_pharm_purchases += $category_value;
	}
}

$current_stock -= $non_pharm_purchases;
// var_dump($non_pharm);die();


$operation_rs = $this->company_financial_model->get_operational_cost_value('Expense');
// 
$operation_result = $non_pharm;

$total_operational_amount = '';
if($operation_rs->num_rows() > 0)
{
	foreach ($operation_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->accountName;
		$account_id = $value->accountId;
		$total_operational_amount += $total_amount;
		$operation_result .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right">'.number_format($total_amount,2).'</td>
							</tr>';
	}
	
}

$salary = 0;// $this->company_financial_model->get_salary_expenses();
// $nssf = $this->company_financial_model->get_statutories(1);
// $nhif = $this->company_financial_model->get_statutories(2);
// $paye_amount = $this->company_financial_model->get_statutories(3);
$relief =0;// $this->company_financial_model->get_statutories(4);
$loans = 0;//$this->company_financial_model->get_statutories(5);

// $paye = $paye_amount - $relief;

$salary -= $relief;
$other_deductions = $salary;// - ($nssf+$nhif+$paye_amount+$relief);

// $total_operational_amount += $salary+$nssf+$nhif+$paye_amount;
$total_operational_amount += $salary;
// $operation_result .= $non_pharm;
$operation_result .='<tr>
							<td class="text-left"><b>Total Operation Cost</b></td>
							<td class="text-right" style="border-top:#3c8dbc solid 2px;"><b>'.number_format($total_operational_amount,2).'</b></td>
							</tr>';


$statement = $this->session->userdata('income_statement_title_search');

if(empty($statement))
{
	$checked = $statement;
}
else {
	$checked = date('M j, Y', strtotime(date('Y-01-01'))).' to ' .date('M j, Y', strtotime(date('Y-m-d')));
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | P & L</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <style type="text/css">
			.receipt_spacing{letter-spacing:0px; font-size: 12px;}
			.center-align{margin:0 auto; text-align:center;}

			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:10px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 2px;
			}

			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
                </strong>
            </div>
        </div>

      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align" style="padding: 5px;">
            	<strong>PROFIT AND LOSS STATEMENT</strong><br>

            	<?php
            	$search_title = $this->session->userdata('income_statement_title_search');

      				 if(empty($search_title))
      				 {
      				 	$search_title = "";
      				 }
      				 else
      				 {
      				 	$search_title =$search_title;
      				 }
				 echo $search_title;
            	?>

            </div>
        </div>

    	<div class="row">
        	<div style="margin: auto;max-width: 500px;">
						<div class="col-md-12" style="margin-top: 10px;">
						
						<table class="table" id="testTable">
							<tr>
								<th style="width: 100%" colspan="2"> <h6> <strong>INCOME</strong></h6> </th>
							</tr>
							<tr>
								<th style="width: 60%"> ITEM </th>
								<th style="width: 40%">AMOUNT</th>
							</tr>

							<tbody>
								<?php echo $income_result;?>
							</tbody>

							<tr>
								<th style="width: 100%" colspan="2"> <h6> <strong>COST OF GOODS SOLD</strong></h6> </th>
							</tr>
							<tr>
								<th style="width: 60%"> ITEM </th>
								<th style="width: 40%">AMOUNT</th>
							</tr>
							<tbody>
									<tr>
										<td>OPENING STOCK</td>
										<td class="text-right"> <?php echo number_format($closing_stock,2);?></td>
									</tr>
									<tr>
										<td>PURCHASES</td>
										<td class="text-right">  <?php echo number_format($total_purchases,2);?></td>
									</tr>
									<tr>
										<td>OTHER ADDITIONS</td>
										<td class="text-right">  <?php echo number_format($total_other_purchases,2);?></td>
									</tr>
									<tr>
										<td>RETURN OUTWARDS</td>
										<td class="text-right">  ( <?php echo number_format($total_return_outwards,2);?> )</td>
									</tr>
									<tr>
										<td>OTHER DEDUCTIONS</td>
										<td class="text-right"> ( <?php echo number_format($total_other_deductions,2);?> )</td>
									</tr>
									<tr>
										<td>TOTAL STOCK EXPENSES</td>
										<td class="text-right"> ( <?php echo number_format($non_pharm_purchases,2);?> )</td>
									</tr>
									<tr>
										<td>CURRENT STOCK</td>
										<td class="text-right"><?php echo number_format($current_stock,2);?></td>
									</tr>
									<tr>
										<td >TOTAL GOODS SOLD</td>
										<td class="text-right"> ( <?php echo number_format($total_purchases+$closing_stock-$current_stock,2);?> )</td>
									</tr>
									<tr>
										<td style="width: 60%"><strong>GROSS PROFIT : (INCOME - TGS )</strong></td>
										<td style="width: 40%" class="text-right"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_income - $total_cog,2);?></strong></td>
									</tr>
							</tbody>
							<tr>
								<th style="width: 100%" colspan="2"> <h6> <strong>EXPENSES</strong></h6> </th>
							</tr>
							<tr>
								<th style="width: 60%"> ITEM </th>
								<th style="width: 40%">AMOUNT</th>
							</tr>
							<tbody>									
								<tr>
									<td class="text-left">SALARIES</td>
									<td class="text-right"> <?php echo number_format($salary,2);?> </td>
								</tr>								
								<?php echo $operation_result;?>
							</tbody>
							<tbody>
								<tr>
									<td style="width: 60%"><strong>NET PROFIT</strong></td>
									<td style="width: 40%" class="text-right"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_income - $total_cog - $total_operational_amount,2)?></strong></td>
								</tr>
							</tbody>
						</table>
						<a href="#" onclick="javascript:xport.toCSV('testTable');">XLS</a>
						</div>
            </div>
        </div>

    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-sm-12">
                <div class="col-sm-10 pull-left">
                    <strong>Prepared by: </strong>
                </div>
                <div class="col-sm-2 pull-right">
                    <?php echo date('jS M Y H:i a'); ?>
                </div>
            </div>

        </div>
    </body>

</html>

<script type="text/javascript">
	var xport = {
  _fallbacktoCSV: true,
  toXLS: function(tableId, filename) {
    this._filename = (typeof filename == 'undefined') ? tableId : filename;

    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls');
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }

    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it

      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};

</script>
