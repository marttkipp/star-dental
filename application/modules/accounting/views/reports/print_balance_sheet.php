<?php

$query = $this->petty_cash_model->get_child_accounts("Bank");

$options2 = $query;
$bank_list = '';
$bank_total = 0;
foreach($options2->result() AS $key_old) 
{ 

	$account_id = $key_old->account_id;


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
	$bank_list .=  '<tr>
							<td>
								'.ucfirst(strtoupper($key_old->account_name)).'
							</td>
							<td>
								 Ksh. '.number_format($total_balance,2).'
							</td>
						</tr>';	
	$bank_total = $bank_total + $total_balance;		
}

$bank_list .='<tr>
							<td><strong>TOTAL BANK TOTAL</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($bank_total,2).'</strong></td>
						</tr>';
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
		$visit_results .='<tr>
							<td>
								'.ucfirst(strtoupper($visit_type_name)).' 
							</td>
							<td>
								Ksh. '.number_format($balance,2).'
							</td>
						</tr>';
		$total_patients = $total_patients + $total_visit_type_patients;
		$total_invoices = $total_invoices + $invoice_amount;
		$total_payments = $total_payments + $payments_value;
		$total_balance = $total_balance + $balance;


	}

	$visit_results .='<tr>
							<td><strong>TOTAL ACCOUNTS RECEIVABLES</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($total_balance,2).'</strong></td>
						</tr>';
}


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
	$other_current_list .='<tr>
								<td>
									'.ucfirst(strtoupper($key_new->account_name)).' 
								</td>
								<td>
									Ksh. '.number_format($cost_of_sale,2).'
								</td>
							</tr>';
	$total_other_current_assets += $cost_of_sale;			
}
$other_current_list .='<tr>
							<td><strong>TOTAL OTHER CURRENT ASSETS</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($total_other_current_assets,2).'</strong></td>
						</tr>';



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
	$other_other_list .='<tr>
							<td>
								'.strtoupper($key_new->account_name).'
							</td>
							<td>
							Ksh. '.number_format($cost_of_sale,2).'
							</td>
					</tr>';
	$total_other_assets += $cost_of_sale;			
}
$other_other_list .='<tr>
							<td><strong>TOTAL OTHER ASSETS</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($total_other_assets,2).'</strong></td>
						</tr>';
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
	$asset_list .='<tr>
							<td>
							'.strtoupper($key_new->account_name).'
						</td>
						<td>
							Ksh. '.number_format($cost_of_sale,2).'
						</td>
					</tr>';
	$total_asset_value += $cost_of_sale;			
}
$asset_list .='<tr>
							<td><strong>TOTAL FIXED ASSETS</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($total_asset_value,2).'</strong></td>
						</tr>';


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
	$liability_list .='<tr>
							<td>
							'.strtoupper($key_new->account_name).'
							</td>
							<td>
							Ksh. '.number_format($cost_of_sale,2).'
							</td>
					</tr>';
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
	$equity_list .='<tr>
						<td>
							'.strtoupper($key_new->account_name).'
						</td>
						<td>
							Ksh. '.number_format($cost_of_sale,2).'
						</td>
					<tr>';
	$total_equity += $cost_of_sale;			
}
$suppliers_response = $this->company_financial_model->get_suppliers_balances();
$providers_response = $this->company_financial_model->get_providers_balances();
$net_asset = $total_asset_value + $bank_total + $total_payments+$total_other_assets+$total_other_current_assets;
$liability_total = $suppliers_response['total_balance'] + $providers_response['total_balance'] + $total_liability;

$total_asset = $net_asset - $liability_total;

$accounts_payable ='<tr>
							<td><strong>TOTAL ACCOUNTS PAYABLE</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($suppliers_response['total_balance'] + $providers_response['total_balance'] + $total_liability,2).'</strong></td>
						</tr>';
$amount_profit = $this->company_financial_model->get_profit_and_loss();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | P & L</title>
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
				font-size:10px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 2px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align" style="padding: 5px;">
            	<strong>BALANCE SHEET STATEMENT</strong><br>

            	<?php
            	$search_title = $this->session->userdata('balance_sheet_title_search');

				 if(empty($search_title))
				 {
				 	$search_title = "";
				 }
				 else
				 {
				 	$search_title =$search_title;
				 }
				 echo $search_title;
            	?>
            	
            </div>
        </div>
        
    	<div class="row">
        	<div style="margin: auto;max-width: 500px;">
						<div class="col-md-12">	
						<div class="row">							
							<h5> <strong>ASSETS</strong></h5>	
							<h6> <strong>A) Bank Accounts</strong></h6>							
						</div>	
						<table class="table">
							<thead>
								<th style="width: 60%"> ACCOUNT NAME </th>
								<th style="width: 40%">AMOUNT</th>
							</thead>
							
							<tbody>
								<?php echo $bank_list;?>
							</tbody>
						</table>					
						</div>
						<div class="col-md-12">
							<div class="row">						
								<h6> <strong>B) Accounts Receivables</strong></h6>							
							</div>	
							<table class="table">
								<thead>
									<th style="width: 60%"> NAME </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								
								<tbody>
									<?php echo $visit_results;?>
								</tbody>
							</table>
						</div>

						<div class="col-md-12">
							<div class="row">						
								<h6> <strong>C) Other Current Assets</strong></h6>							
							</div>	
							<table class="table">
								<thead>
									<th style="width: 60%"> NAME </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								
								<tbody>
									<?php echo $other_current_list;?>
								</tbody>
							</table>
						</div>
						<div class="col-md-12">
							<div class="row">						
								<h6> <strong>D) Other Assets</strong></h6>							
							</div>	
							<table class="table">
								<thead>
									<th style="width: 60%"> NAME </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								
								<tbody>
									<?php echo $other_other_list;?>
								</tbody>
							</table>
						</div>
						<div class="col-md-12">
							<div class="row">						
								<h6> <strong>E) Fixed Assets</strong></h6>							
							</div>	
							<table class="table">
								<thead>
									<th style="width: 60%"> NAME </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								
								<tbody>
									<?php echo $asset_list;?>
								</tbody>
							</table>
						</div>
						<div class="col-md-12">
								<table class="table">
								<tbody>
									<tr>
										<td style="width: 60%"><strong>NET ASSETS</strong></td>
										<td style="width: 40%"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_asset_value + $bank_total + $total_payments+$total_other_assets+$total_other_current_assets,2)?></strong></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-12">	
						<div class="row">							
							<h5> <strong>CURRENT LIABILITIES</strong></h5>	
							<h6> <strong>Accounts Payables</strong></h6>							
						</div>	
						<table class="table">
							<thead>
								<th style="width: 60%"> ACCOUNT NAME </th>
								<th style="width: 40%">AMOUNT</th>
							</thead>
							
							<tbody>
								<tr>
									<td>
										SUPPLIERS
									</td>
									<td>
										Ksh. <?php echo number_format($suppliers_response['total_balance'],2);?>
									</td>
								</tr>
								<tr>
									<td>
										DOCTORS
									</td>
									<td>
										Ksh. <?php echo number_format($providers_response['total_balance'],2);?>
									</td>
								</tr>
								<?php echo $liability_list?>
								<?php echo $accounts_payable?>
							</tbody>
						</table>					
						</div>
						<div class="col-md-12">
								<table class="table">
								<tbody>
									<tr>
										<td style="width: 60%"><strong>TOTAL ASSETS</strong></td>
										<td style="width: 40%"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_asset,2)?></strong></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-12">
							<div class="row">						
								<h5> <strong>EQUITY</strong></h5>							
							</div>	
							<table class="table">
								<thead>
									<th style="width: 60%"> NAME </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								
								<tbody>
									<?php echo $equity_list;?>
									<tr>
										<td>
											RETAINED EARNINGS B/F
										</td>
										<td>
											Ksh. 0
										</td>
									</tr>
									<tr>
										<td>
											CURRENT PROFIT
										</td>
										<td>
											Ksh. <?php echo number_format($amount_profit,2);?>
										</td>
									</tr>
									
									<tr>
										<td>
											RETAINED EARNINGS C/F
										</td>
										<td>
											Ksh. 0
										</td>
									</tr>
								</tbody>
							</table>
						</div>

            </div>
        </div>
        
    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-sm-12">
                <div class="col-sm-10 pull-left">
                    <strong>Prepared by: </strong><?php echo $served_by;?> 
                </div>
                <div class="col-sm-2 pull-right">
                    <?php echo date('jS M Y H:i a'); ?>
                </div>
            </div>
        	
        </div>
    </body>
    
</html>