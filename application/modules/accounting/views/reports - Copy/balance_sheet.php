<?php


// $asset_query = $this->company_financial_model->get_all_fixed_categories();
// $total_asset_value = 0;
// $asset_list ='';

// foreach($asset_query->result() AS $key_old) 
// { 
// 	$asset_category_id = $key_old->asset_category_id;
// 	$asset_category_name = $key_old->asset_category_name;


// 	$asset_category_value = $this->company_financial_model->get_category_value($asset_category_id);

// 	$asset_list .='<div class="row">
// 						<div class="col-md-7">
// 							'.ucfirst(strtoupper($asset_category_name)).'
// 						</div>
// 						<div class="col-md-5">
// 							Ksh. '.number_format($asset_category_value,2).'
// 						</div>
// 					</div>';
// 	$total_asset_value += $asset_category_value;			
// }
// 	$asset_list .='<div class="row">
// 							<div class="col-md-7">
// 								<strong>TOTAL FIXED ASSETS</strong>
// 							</div>
// 							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
// 								<strong >Ksh. '.number_format($total_asset_value,2).'</strong>
// 							</div>
// 						</div>';

$fixed_query = $this->company_financial_model->get_child_accounts("Fixed Assets");
$total_asset_value = 0;
$asset_list ='';

foreach($fixed_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	$cost_of_sale = $this->company_financial_model->get_total_billed_amount($account_id);
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$asset_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_asset_value += $cost_of_sale;			
}
$asset_list .='<div class="row">
							<div class="col-md-7">
								<strong>TOTAL FIXED ASSETS</strong>
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

	$visit_results .='	
							
						<div class="row">
							<div class="col-md-7">
								<strong>TOTAL ACCOUNTS RECEIVABLES</strong>
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

// $capital = $this->petty_cash_model->get_account_deposit("Capital");
$amount_profit = $this->company_financial_model->get_profit_and_loss();

$other_currents_query = $this->company_financial_model->get_child_accounts("Other Current Assets");
$total_other_current_assets = 0;
$other_current_list ='';

foreach($other_currents_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	if($inventory_status == 0)
	{

		$cost_of_sale = $this->company_financial_model->get_total_expense_amount($account_id);
	}
	else
	{
		$cost_of_sale = $this->company_financial_model->get_service_invoice_total_products($service_id);
	}
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$other_current_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_other_current_assets += $cost_of_sale;			
}
$other_current_list .='<div class="row">
							<div class="col-md-7">
								<strong>TOTAL OTHER CURRENT ASSETS</strong>
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. '.number_format($total_other_current_assets,2).'</strong>
							</div>
						</div>';



$other_assets_query = $this->company_financial_model->get_child_accounts("Other Assets");
$total_other_assets = 0;
$other_other_list ='';

foreach($other_assets_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	
	$cost_of_sale = $this->company_financial_model->get_store_stock_values($service_id);
	
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$other_other_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_other_assets += $cost_of_sale;			
}
$other_other_list .='<div class="row">
							<div class="col-md-7">
								<strong>TOTAL OTHER ASSETS</strong>
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. '.number_format($total_other_assets,2).'</strong>
							</div>
						</div>';


$liability_query = $this->company_financial_model->get_child_accounts("Liabilities");
$total_liability = 0;
$liability_list ='';

foreach($liability_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	if($inventory_status == 0)
	{

		$cost_of_sale = $this->company_financial_model->get_total_expense_amount($account_id);
	}
	else
	{
		$cost_of_sale = $this->company_financial_model->get_service_invoice_total_products($service_id);
	}
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$liability_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_liability += $cost_of_sale;			
}



$equity_query = $this->company_financial_model->get_child_accounts("Equity");
$total_equity = 0;
$equity_list ='';

foreach($equity_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	

	$cost_of_sale = $this->company_financial_model->get_account_payments($account_id);
	
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$equity_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_equity += $cost_of_sale;			
}

$net_asset = $total_asset_value + $bank_total + $total_payments+$total_other_assets+$total_other_current_assets;
$liability_total = $suppliers_response['total_balance'] + $providers_response['total_balance'] + $total_liability;

$total_asset = $net_asset - $liability_total;

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
						<!-- <div class="form-group">
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
	                    </div> -->

	                    <div class="form-group">
	                        <label class="col-md-4 control-label">YEAR FROM: </label>
	                        
	                        <div class="col-md-8">
	                             <select id="date_from" name="date_from" class="form-control">
                                   <option value="all">ALL</option>
                                   <option value="2018">2018</option>
                                   <option value="2019">2019</option>
                                   <option value="2020">2020</option>
                                   <option value="2021">2021</option>
                                </select>
	                        </div>
	                    </div>
	                    <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">
	                   <!--  <div class="form-group">
	                        <label class="col-md-4 control-label">YEAR TO: </label>
	                        
	                        <div class="col-md-8">
	                            <select id="year_from" name="year_from" class="form-control">
                                   <option value="all">ALL</option>
                                   <option value="2018">2018</option>
                                   <option value="2019">2019</option>
                                   <option value="2020">2020</option>
                                   <option value="2021">2021</option>
                                </select>
	                        </div>
	                    </div> -->

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
								<h6> <strong>ASSETS</strong></h6>
							</div>

							
							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px"> <strong>A) BANK ACCOUNTS</strong></h6>
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
							
							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px">  <strong>B) ACCOUNTS RECEIVABLES</strong></h6>
								</div>
							</div>
							<?php echo $visit_results;?>	
							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px">  <strong>C) OTHER CURRENT ASSETS </strong></h6>
								</div>
							</div>
							<?php echo $other_current_list;?>

							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px">  <strong>D) OTHER ASSETS </strong></h6>
								</div>
							</div>
							<?php echo $other_other_list;?>
							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px">  <strong>E) FIXED ASSETS</strong> </h6>
								</div>
							</div>
							<?php echo $asset_list;?>	
							
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-7">
									<strong>NET ASSETS</strong>
								</div>
								<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
									<strong >Ksh. <?php echo number_format($total_asset_value + $bank_total + $total_payments+$total_other_assets+$total_other_current_assets,2);?></strong>
								</div>
							</div>
							

						</div>
					
					
						

						
						<div class="col-md-12">
							<div class="row">								
								<h6 style="text-decoration: underline;padding-top: 10px"> <strong>CURRENT LIABILITIES</strong></h6>
							</div>							
							<div class="col-md-12">		
								<div class="row">						
									<h6 style="text-decoration: underline;padding: 10px 0px 10px"><strong>ACCOUNTS PAYABLES</strong></h6>
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
							<?php echo $liability_list;?>
							
							<div class="row">
								<div class="col-md-7">
									<strong>TOTAL ACCOUNTS PAYABLES</strong>
								</div>
								<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
									<strong >Ksh. <?php echo number_format($liability_total,2);?></strong>
								</div>
							</div>
						
						</div>
						<div class="row">	
						<div class="col-md-12" style="margin-top: 10px">
							<div class="col-md-7">
								<strong>TOTAL ASSETS</strong>
							</div>
							<div class="col-md-5" style="border-top: 1px solid #000;border-bottom: 1px solid #000">
								<strong >Ksh. <?php echo number_format($total_asset,2);?></strong>
							</div>
						</div>
					</div>
					


						<div class="col-md-12">
							<div class="row">								
								<h6 style="text-decoration: underline;padding: 10px 0px 10px"> <strong>CAPITAL / EQUITIES</strong></h6>
							</div>							
							<?php echo $equity_list;?>
							<div class="row">
								<div class="col-md-7">
									RETAINED EARNINGS B/F
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