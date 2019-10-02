<?php
$drug_result = '';
$count = 0;
if($query->num_rows() > 0)
{
	
	$drug_result .='
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th>Visit Date</th>
						<th>Drug</th>
						<th>Selling Price P.U</th>
						<th>Sold @ P.U</th>
						<th>Variance</th>
						<th>Units Sold</th>
						<th>Total Collection</th>
						<th>Stock</th>
						<th>Stock Value</th>
					</tr>
				</thead>
				<tbody>
			';
	foreach($query->result() as $visit_drug_result)
	{
		$visit_id = $visit_drug_result->visit_id;
		$patient_id = $visit_drug_result->patient_id;
		$service_charge = $visit_drug_result->service_charge_name;
		$service_amount = $visit_drug_result->service_charge_amount;
		$product_id = $visit_drug_result->product_id;
		$branch_code = $visit_drug_result->branch_code;
		$department_name = $visit_drug_result->department_name;
		$visit_date = date('jS M Y',strtotime($visit_drug_result->visit_date));
		$qty_given = $visit_drug_result->units_given;
		$starting_stock= $visit_drug_result->starting_stock;
		$visit_charge_amount = $visit_drug_result->visit_charge_amount ;


		$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id);

		$purchases = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id);
			                       

		$procurred = $this->inventory_management_model->item_proccured($inventory_start_date, $product_id);
		$deductions = $this->inventory_management_model->item_deductions($inventory_start_date, $product_id);

        $in_stock = ($starting_stock + $purchases + $procurred) - $sales - $deductions;

        $variance = $service_amount - $visit_charge_amount;

		$count++;
		
		
		$drug_result .='
					<tr>
						<td>'.$count.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$service_charge.'</td>
						<td>'.number_format($service_amount,2).'</td>
						<td>'.number_format($visit_charge_amount,2).'</td>
						<td>'.number_format($variance,2).'</td>
						<td>'.$qty_given.'</td>
						<td>'.number_format(($visit_charge_amount * $qty_given),2).'</td>
						<td>'.$in_stock.'</td>
						<td>'.number_format(($service_amount * $in_stock),2).'</td>
					</tr>';
	}
	$drug_result.='
				</tbody>
			</table>';
}
else
{
	$drug_result.= 'No drugs have been dispensed';
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Creditors</title>
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
				font-size:12px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 10px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">    	       
      <div class="row" >
      		
        	<div class="col-md-12 center-align">
            	<h4><strong>DRUGS SALES FOR  <?php echo date('Y-m-d');?></strong></h4>
            </div>
					
        </div>
        
    	<div class="row">
        	<div class="col-md-12">
            	<?php echo $drug_result;?>
            </div>

        </div>
       
    </body>
    
</html>