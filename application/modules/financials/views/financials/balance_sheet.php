<?php echo $this->load->view('search/search_balance_sheet','', true);?>
<?php
$bank_balances_rs = $this->company_financial_model->get_account_value();
$bank_balances_result = '';
$total_income = 0;
// var_dump($bank_balances_rs);die();

if($bank_balances_rs->num_rows() > 0)
{
	foreach ($bank_balances_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->accountName;
		$account_id = $value->account_id;
		$total_income += $total_amount;
		$bank_balances_result .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right">
							<a href="'.site_url().'account-transactions/'.$account_id.'" >'.number_format($total_amount,2).'</a>
							</td>
							</tr>';
	}
	$bank_balances_result .='<tr>
							<td class="text-left"><b>TOTAL BANK BALANCE</b></td>
							<td class="text-right"><b class="match">'.number_format($total_income,2).'</b></td>
							</tr>';
}

$fixed_asset_rs = $this->company_financial_model->get_asset_categories();
$fixed_asset_result = '';
$total_fixed = 0;
// var_dump($fixed_asset_rs);die();

if($fixed_asset_rs->num_rows() > 0)
{
	foreach ($fixed_asset_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->asset_value;
		$asset_category_name = $value->asset_category_name;
		$asset_category_id = $value->asset_category_id;
		$total_fixed += $total_amount;
		$fixed_asset_result .='<tr>
								<td class="text-left">'.strtoupper($asset_category_name).'</td>
								<td class="text-right">
								'.number_format($total_amount,2).'
								</td>
								</tr>';
	}

}
$accounts_receivable = $this->company_financial_model->get_accounts_receivables();
$accounts_payable = $this->company_financial_model->get_accounts_payable();
$cash_on_hand = $this->company_financial_model->get_cash_on_hand();
$wht_payable = $this->company_financial_model->get_total_wht_tax();
$vat_payable = $this->company_financial_model->get_total_vat_tax();

$total_assets = $accounts_receivable+$total_income;

$total_liability = $accounts_payable + $wht_payable +$wht_payable;
$current_year_earnings = $total_assets + $total_fixed - $total_liability;

$search = $this->session->userdata('balance_sheet_title_search');

if(!empty($search))
{
	$balance_sheet_search = ucfirst($search);
}
else {
	$balance_sheet_search = 'Reporting as of: '.date('M j, Y', strtotime(date('Y-m-d')));
}
?>
<style>
	td .match
	{
		border-top: #000 2px solid !important;
	}
</style>
<div class="text-center">
	<h3 class="box-title">Balance Sheet</h3>
	<h5 class="box-title"> <?php echo $balance_sheet_search?> </h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">
		<div class="panel-body">
    	<h4 class="box-title">Asssets</h4>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">BANK</th>
				</tr>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $bank_balances_result?>
			</tbody>

			<thead>
				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">Current Asset</th>
				</tr>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
					<tr>
								<td >CASH ON HAND</td>
						<td class="text-right"><?php echo number_format($cash_on_hand,2);?></td>
					</tr>
					<tr>
								<td class="text-left">ACCOUNTS RECEIVABLES</td>
						<td class="text-right"><a href="<?php echo site_url().'accounts-receivables'?>" ><?php echo number_format($accounts_receivable,2);?></a></td>
					</tr>
					<tr>
								<td class="text-left ">TOTAL CURRENT ASSETS</td>
						<td class="text-right"><b class="match"><?php echo number_format($accounts_receivable,2);?></b></td>
					</tr>
					

			</tbody>

			<thead>
				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">Fixed Asset</th>
				</tr>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
					<?php
					echo $fixed_asset_result;

					?>
					
					<tr>
						<td class="text-left ">TOTAL FIXED ASSETS</td>
						<td class="text-right"><b class="match"><?php echo number_format($total_fixed,2);?></b></td>
					</tr>
					<tr>
								<td class="text-left ">TOTAL ASSETS</td>
						<td class="text-right"><b class="match"><?php echo number_format($total_assets + $total_fixed,2);?></b></td>
					</tr>

			</tbody>
		</table>


		<h3 class="box-title">Liabilities</h3>
		<table class="table  table-striped table-condensed">
			<thead>
				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">Current Liability</th>
				</tr>
				<tr>
        			<th class="text-left">Account</th>
					<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
					<tr>
								<td class="text-left">ACCOUNTS PAYABLE</td>
						<td class="text-right"><a href="<?php echo site_url().'accounts-payables'?>" ><?php echo number_format($accounts_payable,2)?></a> </td>
					</tr>
					<tr>
								<td class="text-left">VAT PAYABLE</td>
						<td class="text-right"><?php echo number_format($vat_payable,2);?></td>
					</tr>
					<tr>
								<td class="text-left">WHT PAYABLE</td>
						<td class="text-right"><?php echo number_format($wht_payable,2);?></td>
					</tr>
					<tr>
								<td class="text-left">TOTAL CURRENT LIABILITIES</td>
						<td class="text-right"><b class="match"><?php echo number_format($total_liability,2);?></b></td>
					</tr>
					<tr>
								<td class="text-left">TOTAL LIABILITIES</td>
								<td class="text-right"><b class="match"><?php echo number_format($total_liability,2);?></b></td>
					</tr>

			</tbody>
		</table>

		<h3 class="box-title">Equity</h3>
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Account</th>
							<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				<tr>
        			<td class="text-left">OWNER INVESTMENTS / DRAWINGS</td>
							<td class="text-right">0.00</td>
				</tr>
				<tr>
        			<td class="text-left">PREVIOUS YEAR(s) EARNINGS</td>
					<td class="text-right">0.00</td>
				</tr>
				<tr>
							<td class="text-left">CURRENT YEAR(s) EARNINGS</td>
					<td class="text-right">0.00</td>
				</tr>
				<tr>
							<td class="text-left">TOTAL EQUITY</td>
					<td class="text-right"><?php echo number_format($current_year_earnings,2)?></td>
				</tr>
				<tr>
        			<td class="text-left">TOTAL LIABILITY AND EARNINGS</td>
							<td class="text-right"><?php echo number_format($total_liability,2)?></td>
				</tr>
			</tbody>
		</table>
    </div>
</section>
