<?php echo $this->load->view('search/search_creditor_account', '', TRUE);?>

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
		$total_unallocated = $this->company_financial_model->get_unallocated_funds($creditor_id);
		$income_result .='<tr>
							<td class="text-left">'.strtoupper($payables).'</td>
							<td class="text-right">'.number_format($coming_due,2).'</td>
							<td class="text-right">'.number_format($thirty_days,2).'</td>
							<td class="text-right">'.number_format($sixty_days,2).'</td>
							<td class="text-right">'.number_format($ninety_days,2).'</td>
							<td class="text-right">'.number_format($over_ninety_days,2).'</td>
							<td class="text-right">'.number_format($total_unallocated,2).'</td>
							<td class="text-right">'.number_format($Total,2).'</td>
							<td class="text-right"><a href="'.site_url().'creditor-statement/'.$creditor_id.'" class="btn btn-warning btn-xs"> Open Account</a></td>
							<td class="text-right"><a href="'.site_url().'finance/edit-creditor/'.$creditor_id.'" class="btn btn-success btn-xs"> Edit Account</a></td>
							</tr>';
	}

}
?>

<section class="panel">
		<header class="panel-heading">
				<h3 class="panel-title">Payables </h3>
				<div class="box-tools pull-right">
                    <a href="<?php echo site_url();?>finance/add-creditor" class="btn btn-sm btn-primary" ><i class="fa fa-plus"></i> Add Creditor</a>
                </div>
		</header>
		<div class="panel-body">
		<?php
      		$search = $this->session->userdata('creditor_search');
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'finance/creditors/close_creditor_creditor_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
                <?php
			}
			?>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Payables</th>
					<th class="text-right">Coming Due</th>
					<th class="text-right">1 - 30 Days</th>
					<th class="text-right">31 - 60 Days</th>
					<th class="text-right">61 - 90 Days</th>
					<th class="text-right">Over 90 Days</th>
					<th class="text-right">Unallocated Funds</th>
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
