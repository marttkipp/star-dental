<?php echo $this->load->view('search/search_stock_report','', true);?>
<?php

$stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
if(!empty($stock_search))
{
	$statement = $this->session->userdata('stock_title_search'.$report_id);
}
else
{
	$statement = $this->session->userdata('income_statement_title_search');
}


// var_dump($statement);die();

if(!empty($statement))
{
	$checked = $statement;
}
else {
	$checked = 'Reporting period: '.date('F j, Y', strtotime(date('Y-01-01'))).' to ' .date('F j, Y', strtotime(date('Y-m-d')));
}


$result = '';
if($query->num_rows() > 0)
{
	$x=0;
	$total_dr_quantity = 0;
	$total_dr_amount = 0;
	foreach ($query->result() as $key => $value) {
		// code...
<<<<<<< HEAD
		$transaction_id = $value->transactionId;
=======
		$transaction_id = $value->product_deductions_stock_id;
>>>>>>> f38c31802b19fcacb78172da7aea17ed3759c8fb
		$product_id = $value->product_id;
		$store_id = $value->store_id;
		$product_name = $value->product_name;
		$store_name = $value->store_name;
<<<<<<< HEAD
		$transactionDescription = $value->transactionDescription;
		$dr_quantity = $value->dr_quantity;
		$cr_quantity = $value->cr_quantity;
		$dr_amount = $value->dr_amount;
		$cr_amount = $value->cr_amount;
		$transactionDate = $value->transactionDate;
=======
		$transactionDescription = $value->deduction_description;
		$dr_quantity = 0;
		$cr_quantity = ($value->product_deductions_stock_quantity * $value->product_deductions_stock_pack_size);
		$dr_amount = 0;
		$cr_amount = $value->product_unitprice * ($value->product_deductions_stock_quantity * $value->product_deductions_stock_pack_size);
		$transactionDate = $value->product_deductions_stock_date;
>>>>>>> f38c31802b19fcacb78172da7aea17ed3759c8fb
		$transactionDate = date('F j, Y', strtotime($transactionDate));

		$total_dr_quantity += $dr_quantity;
		$total_dr_amount += $dr_amount;
		$x++;
		$result .='
								<tr>
											<td class="text-left">'.$x.'</td>
											<td class="text-left">'.$transactionDate.'</td>
											<td class="text-left">'.$product_name.'</td>
											<td class="text-left">'.$store_name.'</td>
											<td class="text-left">'.$transactionDescription.'</td>
<<<<<<< HEAD
											<td class="text-center">'.$dr_quantity.'</td>
											<td class="text-center">'.number_format($dr_amount,2).'</td>
=======
											<td class="text-center">'.$cr_quantity.'</td>
											<td class="text-center">'.number_format($cr_amount,2).'</td>
>>>>>>> f38c31802b19fcacb78172da7aea17ed3759c8fb
								</tr>
						';

	}
	$result .='
							<tr>
										<th class="text-left"></th>
										<th class="text-left"></th>
										<th class="text-left"></th>
										<th class="text-left"></th>
										<th class="text-left">Total Stock</th>
										<th class="text-center">'.$total_dr_quantity.'</th>
										<th class="text-center">'.number_format($total_dr_amount,2).'</th>
							</tr>
					';
}
?>

<div class="text-center">
	<h3 class="box-title"><?php echo $title;?></h3>
	<h5 class="box-title"><?php echo $checked?></h5>
	<h6 class="box-title">Created <?php echo date('F j, Y', strtotime(date('Y-m-d')));?></h6>
</div>
<div class="text-right">
	<a href="<?php echo site_url();?>company-financials/profit-and-loss"  class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;margin-left:5px" > <i class="fa fa-arrow-left"></i> Back to P & L </a>
</div>



<section class="panel">

		<div class="panel-body">
		<?php
			$stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
			if(!empty($stock_search) AND $stock_search == $report_id)
			{				
				
				?>
			    <a href="<?php echo base_url().'financials/company_financial/close_stock_search/'.$report_id.'/'.$category_id;?>" class="btn btn-sm btn-danger"><i class="fa fa-cancel"></i> Close Search</a>
			    <?php
				
			}
	    	

		?>
    	<!-- <h3 class="box-title">Revenue</h3> -->
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">#</th>
        			<th class="text-left">Transaction Date</th>
							<th class="text-left">Product</th>
							<th class="text-left">Affected Store</th>
        			<th class="text-left">Description</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Value</th>
				</tr>
			</thead>
			<tbody>
						<?php echo $result?>
			</tbody>
		</table>
		<div class="widget-foot">                                
			<?php if(isset($links)){echo $links;}?>
	    
	        <div class="clearfix"></div> 
	    
	    </div>


    </div>
</section>
