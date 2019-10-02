<?php echo $this->load->view('search/search_aged_payables','', true);?>

<?php

$income_rs = $this->company_financial_model->get_payables_aging_report();
$income_result = '';
$total_income = 0;
if($income_rs->num_rows() > 0)
{
	foreach ($income_rs->result() as $key => $value) {
		# code...
		// $total_amount = $value->total_amount;
		$payables = $value->payables;
		$thirty_days = $value->thirty_days;
		$sixty_days = $value->sixty_days;
		$ninety_days = $value->ninety_days;
		$over_ninety_days = $value->over_ninety_days;
		$coming_due = $value->coming_due;
		$creditor_id = $value->recepientId;
		$Total = $value->Total;
		$income_result .='<tr>
							<td class="text-left">'.strtoupper($payables).'</td>
							<td class="text-right">'.number_format($coming_due,2).'</td>
							<td class="text-right">'.number_format($thirty_days,2).'</td>
							<td class="text-right">'.number_format($sixty_days,2).'</td>
							<td class="text-right">'.number_format($ninety_days,2).'</td>
							<td class="text-right">'.number_format($over_ninety_days,2).'</td>
							<td class="text-right">'.number_format($Total,2).'</td>
							<td class="text-right"><a href="'.site_url().'creditor-statement/'.$creditor_id.'" class="btn btn-warning btn-xs"> Open Account</a></td>
							</tr>';
	}

}
?>

<div class="text-center">
	<h3 class="box-title">Aged Payables</h3>
	<h5 class="box-title">Reporting period: <?php echo date('M j, Y', strtotime(date('Y-01-01')));?></h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">
		<header class="panel-heading">
				<h3 class="panel-title">Payables </h3>
		</header>
		<div class="panel-body">
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Payables</th>
					<th class="text-right">Coming Due</th>
					<th class="text-right">1-30</th>
					<th class="text-right">31-60</th>
					<th class="text-right">61-90</th>
					<th class="text-right">Over 90 Days</th>
					<th class="text-right">Total</th>
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $income_result?>
				<!-- <tr>
        			<td >Web Development</td>
					<td class="text-right">200,000.00</td>
					<td class="text-right">200,000.00</td>
					<td class="text-right">200,000.00</td>
					<td class="text-right">200,000.00</td>
					<td class="text-right">200,000.00</td>
					<td class="text-right">200,000.00</td>
				</tr> -->

			</tbody>
		</table>
    </div>
</section>
