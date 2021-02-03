<?php echo $this->load->view('search/search_balance_sheet','', true);?>
<?php
$bank_balances_rs = $this->company_financial_model->get_account_value_by_type('Current Assets');
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



$bank_balances_rs = $this->company_financial_model->get_account_value_by_type('Bank');
$cash_in_bank = '';
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
		$cash_in_bank .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right">
							<a href="'.site_url().'account-transactions/'.$account_id.'" >'.number_format($total_amount,2).'</a>
							</td>
							</tr>';
	}
	$cash_in_bank .='<tr>
							<td class="text-left"><b>TOTAL BANK BALANCE</b></td>
							<td class="text-right"><b class="match">'.number_format($total_income,2).'</b></td>
							</tr>';
}



$bank_balances_rs = $this->company_financial_model->get_account_share_capital_by_type('Capital');
$share_capital_list = '';
$total_share_capital = 0;
// var_dump($bank_balances_rs);die();

if($bank_balances_rs->num_rows() > 0)
{
	foreach ($bank_balances_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->accountName;
		$account_id = $value->account_id;
		$total_share_capital += $total_amount;
		$share_capital_list .='<tr>
							<td class="text-left">'.strtoupper($transactionName).'</td>
							<td class="text-right">
							<a href="'.site_url().'account-transactions/'.$account_id.'" >'.number_format($total_amount,2).'</a>
							</td>
							</tr>';
	}
	
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
    	<h3 class="box-title">Asssets</h3>
    	<!-- <h4 class="box-title">Fixed Assets</h4> -->
    	<table class="table  table-striped table-condensed" style="margin-left: 10px">

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
					
					

			</tbody>
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>

				<tr>
					<th class="text-left" colspan="" >TOTAL FIXED ASSETS</th>
					<th class="text-right" colspan="" ><?php echo number_format($total_fixed,2)?></th>
				</tr>
			</thead>
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>

				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">Current Assets</th>
				</tr>
			</thead>
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>
				<tr>
					<th class="text-left" colspan="2" >Other Current Assets</th>
				</tr>
				
			</thead>
			<tbody>
				<?php echo $bank_balances_result?>
			</tbody>
			<thead>

				<tr>
					<th class="text-left" colspan="2" >Cash in at Bank and in hand</th>
				</tr>
				
			</thead>

			<tbody>
				<?php echo $cash_in_bank?>
			</tbody>
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>

				<tr>
					<th class="text-left" colspan="" >TOTAL CURRENT ASSETS</th>
					<th class="text-right" colspan="" >0.00</th>
				</tr>
			</thead>
		</table>


		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>

				<tr>
					<th class="text-left" colspan="2" style="background-color:#3c8dbc;color:#fff;">Current Liabilities</th>
				</tr>
			</thead>
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>
				<tr>
					<th class="text-left" colspan="2" >Accounts Payable</th>
				</tr>
				
			</thead>
			<tbody>
				<tr>
					<td class="text-left">ACCOUNTS PAYABLE</td>
					<td class="text-right"><a href="<?php echo site_url().'accounts-payables'?>" ><?php echo number_format($accounts_payable,2)?></a> </td>
				</tr>
				<tr>
					 <td class="text-left">TOTAL CURRENT LIABILITIES</td>
					<td class="text-right"><b class="match"><?php echo number_format($total_liability,2);?></b></td>
				</tr>
			</tbody>
			
		</table>
		<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>

				<tr>
					<th class="text-left" colspan="" >TOTAL ACCOUNTS PAYABLE</th>
					<th class="text-right" colspan="" ><?php echo number_format($total_liability,2);?></th>
				</tr>
			</thead>
		</table>
		<table class="table  table-striped table-condensed" >
			<thead>

				<tr>
					<th class="text-left" colspan="" >NET CURRENT ASSETS</th>
					<th class="text-right" colspan="" ><?php echo number_format($total_liability,2);?></th>
				</tr>
				<tr>
					<th class="text-left" colspan="" >TOTAL ASSETS LESS CURRENT LIABILITIES</th>
					<th class="text-right" colspan="" ><?php echo number_format($total_liability,2);?></th>
				</tr>
				<tr>
					<th class="text-left" colspan="" >NET ASSETS</th>
					<th class="text-right" colspan="" ><?php echo number_format($total_liability,2);?></th>
				</tr>
			</thead>
		</table>
		
		
		<h3 class="box-title">Capital and Reserves</h3>
    	<table class="table  table-striped table-condensed" style="margin-left: 10px">
			<thead>
				<tr>
        			<th class="text-left">Account</th>
							<th class="text-right">Balance</th>
				</tr>
			</thead>
			<tbody>
				<tr>
        			<td class="text-left">Share Capital Account</td>
							<td class="text-right"><?php echo number_format($total_share_capital,2)?></td>
				</tr>
				<tr>
        			<td class="text-left">Profit of the Year</td>
					<td class="text-right">0.00</td>
				</tr>
				<tr>
							<td class="text-left">Share Holder Funds</td>
					<td class="text-right">0.00</td>
				</tr>
				
			</tbody>
		</table>
    </div>
</section>
