<?php echo $this->load->view('search/income_search','', true);?>

<?php
$all_properties = $this->company_financial_model->get_all_visit_types();
$receivables = '';
$total_balances = 0;
if($all_properties->num_rows() > 0)
{

	foreach ($all_properties->result() as $key => $value) {
		// code...
		$visit_type_id = $value->visit_type_id;
		$visit_type_name = $value->visit_type_name;
		$property_balance = $this->company_financial_model->get_receivable_balances($visit_type_id);
		$receivables .= '	<tr>
													<td class="text-left">'.strtoupper($visit_type_name).'</td>
											<td class="text-right"><a href="'.site_url().'customer-invoices/'.$visit_type_id.'" >'.number_format($property_balance,2).'</a></td>
										</tr>';
		$total_balances += $property_balance;
	}


}
$receivables .= '	<tr>
											<td class="text-left"><b>Total Expense</b></td>
									<td class="text-right"><b class="match">'.number_format($total_balances,2).'</b></td>
								</tr>';

	$search = $this->session->userdata('customer_income_title_search');
	// var_dump($search);die();
	if(!empty($search))
	{
		$customer_income_search = ucfirst($search);
	}
	else {
		$customer_income_search = 'Reporting as of: '.date('M j, Y', strtotime(date('Y-01-01'))).' to '.date('M j, Y', strtotime(date('Y-m-d')));
	}
?>
<style>
	td .match
	{
		border-top: #000 2px solid !important;
	}
</style>

<div class="text-center">
	<h3 class="box-title">Income by Customer</h3>
	<h5 class="box-title"><?php echo $customer_income_search?></h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">
		<header class="panel-heading">
				<h3 class="panel-title">Receivable </h3>
		</header>
		<div class="panel-body">
    	<!-- <h3 class="box-title">Revenue</h3> -->
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Customer</th>
					<th class="text-right">Income</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $receivables?>
			</tbody>
		</table>


    </div>
</section>
