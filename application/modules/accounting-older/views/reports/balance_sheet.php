<?php


$asset_query = $this->company_financial_model->get_all_fixed_categories();
$total_asset_value = 0;
$asset_list ='';

foreach($asset_query->result() AS $key_old) 
{ 
	$asset_category_id = $key_old->asset_category_id;
	$asset_category_name = $key_old->asset_category_name;


	$asset_category_value = $this->company_financial_model->get_category_value($asset_category_id);

	$asset_list .='<div class="row">
						<div class="col-md-7">
							'.ucfirst(strtoupper($asset_category_name)).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($asset_category_value,2).'
						</div>
					</div>';
	$total_asset_value += $asset_category_value;			
}
	$asset_list .='<div class="row">
							<div class="col-md-7">
								TOTAL FIXED ASSETS
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. '.number_format($total_asset_value,2).'</strong>
							</div>
						</div>';




$visit_types_rs = $this->company_financial_model->get_visit_details();
$visit_results = '';
$total_balance = 0;
$total_invoices = 0;
$total_payments = 0;
$total_patients = 0;
if($visit_types_rs->num_rows() > 0)
{
	foreach ($visit_types_rs->result() as $key => $value) {
		# code...

		$visit_type_name = $value->visit_type_name;
		$visit_type_id = $value->visit_type_id;


		$table = 'visit';
		$where = 'visit.visit_delete = 0 AND visit_type = '.$visit_type_id.' ';
		$total_visit_type_patients = $this->company_financial_model->count_items($table,$where);

		// calculate invoiced amounts
		$report_response = $this->company_financial_model->get_visit_type_invoice($visit_type_id);

		$invoice_amount = $report_response['invoice_total'];
		$payments_value = $report_response['payments_value'];
		$balance = $report_response['balance'];

		// calculate amounts paid
		$visit_results .='<div class="row">
							<div class="col-md-7" style="font-size:14px;">
								'.ucfirst(strtoupper($visit_type_name)).' 
							</div>
							<div class="col-md-5">
								<a href="'.site_url().'visit-transactions/'.$visit_type_id.'">Ksh. '.number_format($balance,2).'</a>
							</div>
						</div>';
		$total_patients = $total_patients + $total_visit_type_patients;
		$total_invoices = $total_invoices + $invoice_amount;
		$total_payments = $total_payments + $payments_value;
		$total_balance = $total_balance + $balance;


	}

	$visit_results .='	<div class="row">
							<div class="col-md-7">
								APPROPRIATION  FUND
							</div>
							<div class="col-md-5">
								Ksh. 0
							</div>
						</div>
							
						<div class="row">
							<div class="col-md-7">
								TOTAL ACCOUNTS PAYABLES
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. '.number_format($total_balance,2).'</strong>
							</div>
						</div>';
}
$total_stock_value = $this->company_financial_model->get_stock_value();


// bank 

$query = $this->petty_cash_model->get_child_accounts("Bank");

$options2 = $query;
$bank_list = '';
$bank_total = 0;
foreach($options2->result() AS $key_old) 
{ 

	$account_id = $key_old->account_id;


	// calculate invoiced amounts


	$account_name = $key_old->account_name;

	if($account_name == "Cash Account")
	{
		//  get values of collection for the period stated

		$total_income = $this->company_financial_model->get_cash_collected($account_id,2);
		//normal payments
		$total_balance = $total_income['total_balance'];
		
	}
	else if($account_name =="Mpesa")
	{

        $total_income = $this->company_financial_model->get_cash_collected($account_id,5);
		//normal payments
		$total_balance = $total_income['total_balance'];

    }
	
	else
	{
		$report_response = $this->company_financial_model->get_account_balances($account_id);

		$total_payments = $report_response['total_payments'];
		$total_disbursed = $report_response['total_disbursed'];
		$total_balance = $report_response['total_balance'];
	}
$bank_list .=  '<div class="row">
						<div class="col-md-7">
							'.ucfirst(strtoupper($key_old->account_name)).'
						</div>
						<div class="col-md-5">
							<a href="'.site_url().'accounts-transactions/'.$account_id.'"> Ksh. '.number_format($total_balance,2).'</a>
						</div>
					</div>';	
	$bank_total = $bank_total + $total_balance;		
}
// var_dump($asset_list); die();
$suppliers_response = $this->company_financial_model->get_suppliers_balances();
$providers_response = $this->company_financial_model->get_providers_balances();

$capital = $this->petty_cash_model->get_account_deposit("Capital");
$amount_profit = $this->company_financial_model->get_profit_and_loss();

// var_dump($total_suppliers_balances); die();
?>
<section class="panel panel-primary">
    <header class="panel-heading ">        
        <h2 class="panel-title center-align"><?php echo strtoupper($title);?></h2>       
     </header>
    <div class="panel-body">        
		<div >
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6">
						<?php
			            echo form_open("company-financials/balance-sheet-search", array("class" => "form-horizontal"));
			            ?>
						<div class="form-group">
	                        <label class="col-md-4 control-label">DATE FROM: </label>
	                        
	                        <div class="col-md-8">
	                            <div class="input-group">
	                                <span class="input-group-addon">
	                                    <i class="fa fa-calendar"></i>
	                                </span>
	                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Date from">
	                            </div>
	                        </div>
	                    </div>
	                    <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">
	                    <div class="form-group">
	                        <label class="col-md-4 control-label">DATE TO: </label>
	                        
	                        <div class="col-md-8">
	                            <div class="input-group">
	                                <span class="input-group-addon">
	                                    <i class="fa fa-calendar"></i>
	                                </span>
	                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to" placeholder="Date from">
	                            </div>
	                        </div>
	                    </div>

	                    <br>
			            <div class="center-align">
			                <button type="submit" class="btn btn-info btn-sm">Search</button>
			            </div>

			            <?php
			            echo form_close();
			            ?>

			            <?php
			            echo form_open(site_url()."accounting/company_financial/close_balance_sheet_search", array("class" => "form-horizontal"));
			            ?>
			            <?php
			            $search = $this->session->userdata('balance_sheet_search');
		
						if(!empty($search))
						{
							echo '<button type="submit" class="btn btn-warning btn-sm">Close Search</button><input type="hidden" name="redirect_url" value="'.$this->uri->uri_string().'">';
						}
						 echo form_close();
						 $search_title = $this->session->userdata('balance_sheet_title_search');

						 if(empty($search_title))
						 {
						 	$search_title = "";
						 }
						 else
						 {
						 	$search_title =$search_title;
						 }
			            ?>
			            <hr>
			            <h3 class="center-align"><?php echo $search_title;?></h3> 
			            
					</div>
					<div class="col-md-6">
						<div class="row center-align">								
							<h6> <strong>BALANCE SHEET</strong></h6>
						</div>
						<div class="col-md-12">	
							<div class="row">							
								<h6> <strong>FIXED ASSETS</strong></h6>							
							</div>						
							<?php echo $asset_list;?>
						</div>
						<div class="col-md-12">
							<div class="row">								
								<h6> <strong>CURRENT ASSETS</strong></h6>
							</div>
							<div class="col-md-12">		
								<div class="row">						
									<h6> <strong>a) ACCOUNTS RECEIVABLES</strong></h6>
								</div>
							</div>
							<?php echo $visit_results;?>				
							

							<div class="row">		
								<div class="col-md-7">
									<h6> <strong>b) CLOSING STOCK</strong></h6>
								</div>
								<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
									<strong >Ksh. <?php echo number_format($total_stock_value,2);?></strong>
								</div>
							</div>
							<div class="col-md-12">		
								<div class="row">						
									<h6> <strong>c) BANK ACCOUNTS</strong></h6>
								</div>
							</div>
							<?php echo $bank_list;?>
							
							
							<div class="row">
								<div class="col-md-7">
									TOTAL ACCOUNT BALANCES 
								</div>
								<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
									<strong >Ksh. <?php echo number_format($bank_total,2);?></strong>
								</div>
							</div>

						</div>
					
						<div class="row" style="margin-top: 10px !important;">
							<div class="col-md-7">
								<h6> <strong>TOTAL ASSETS</strong></h6>
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. <?php echo number_format($total_asset_value + $bank_total + $total_payments,2);?></strong>
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="row">								
								<h6> <strong>CURRENT LIABILITIES</strong></h6>
							</div>							
							<div class="col-md-12">		
								<div class="row">						
									<h6> <strong>ACCOUNTS PAYABLES</strong></h6>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									SUPPLIERS
								</div>
								<div class="col-md-5">
									<a href="<?php echo site_url().'accounting/creditors'?>">Ksh. <?php echo number_format($suppliers_response['total_balance'],2);?></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									DOCTORS
								</div>
								<div class="col-md-5">
									<a href="<?php echo site_url().'accounting/providers'?>">Ksh. <?php echo number_format($providers_response['total_balance'],2);?></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									SHORT TERM LOAN
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									LONG TERM LOANS
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									TOTAL ACCOUNTS PAYABLES
								</div>
								<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
									<strong >Ksh. <?php echo number_format($suppliers_response['total_balance'] + $providers_response['total_balance'],2);?></strong>
								</div>
							</div>
							<!-- <div class="col-md-12">		
								<div class="row">						
									<h6> <strong>Other Liabilities</strong></h6>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									Suppliers
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>							
							<div class="row">
								<div class="col-md-7">
									Total 
								</div>
								<div class="col-md-5">
									<strong style="border-top: 1px solid #000">Ksh. <?php echo number_format(0,2);?></strong>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									Total Expenses
								</div>
								<div class="col-md-5">
									<strong style="border-top: opx solid #000">Ksh. <?php echo number_format(0,2)?></strong>
								</div>
							</div> -->
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-7">
								<h6> <strong>NET ASSET</strong></h6>
							</div>
							<div class="col-md-5">

								<strong style="border-top: 1px solid #000;border-bottom: 1px solid #000">Ksh.  <?php echo number_format(($total_asset_value + $bank_total + $total_payments) - ($suppliers_response['total_balance'] + $providers_response['total_balance']),2);?></strong>
							</div>
						</div>


						<div class="col-md-12">
							<div class="row">								
								<h6> <strong>CAPITAL</strong></h6>
							</div>							
						
							<div class="row">
								<div class="col-md-7">
									RETURNED EARNINGS B/F
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									CURRENT PROFIT
								</div>
								<div class="col-md-5">
									<a href="<?php echo site_url()?>company-financials/profit-and-loss">Ksh. <?php echo number_format($amount_profit,2);?></a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									OWNERS EQUITY
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									SHARE CAPITAL
								</div>
								<div class="col-md-5">
									Ksh. <?php echo number_format($capital,2);?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									RETAINED EARNINGS C/F
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
						</div>
					</div>
					
				
				</div>
				
			</div>
		</div>
  	</div>
</section>