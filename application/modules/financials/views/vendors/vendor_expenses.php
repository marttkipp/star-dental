<?php echo $this->load->view('search/search_vendor_expenses','', true);?>

<?php
$all_vendors = $this->company_financial_model->get_all_vendors();
$creditors = '';
$total_expenses = 0;
if($all_vendors->num_rows() > 0)
{

	foreach ($all_vendors->result() as $key => $value) {
		// code...
		$creditor_id = $value->creditor_id;
		$creditor_name = $value->creditor_name;
		$creditor_expense = $this->company_financial_model->get_creditor_expenses($creditor_id);
		$creditors .= '	<tr>
													<td class="text-left">'.$creditor_name.'</td>
											<td class="text-right">'.number_format($creditor_expense,2).'</td>
										</tr>';
		$total_expenses += $creditor_expense;
	}


}
$creditors .= '	<tr>
											<td class="text-left"><b>Total Expense</b></td>
									<td class="text-right"><b class="match">'.number_format($total_expenses,2).'</b></td>
								</tr>';
?>
<style>
	td .match
	{
		border-top: #000 2px solid !important;
	}
</style>
<div class="text-center">
	<h3 class="box-title">Vendor Expenses</h3>
	<h5 class="box-title">Reporting period: <?php echo date('M j, Y', strtotime(date('Y-01-01')));?> to <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">

		<div class="panel-body">
    	<!-- <h3 class="box-title">Revenue</h3> -->
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Vendor</th>
					<th class="text-right">Expense</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $creditors?>
			</tbody>
		</table>


    </div>
</section>
