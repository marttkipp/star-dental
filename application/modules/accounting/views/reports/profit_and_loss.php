<?php
$services_result = $this->company_financial_model->get_all_service_types();
$service_result = '';
$total_service_invoice = 0;
$total_service_payment = 0;
$total_service_balance = 0;
if($services_result->num_rows() > 0)
{
	$result = $services_result->result();
	$grand_total = 0;			
	foreach($result as $res)
	{
		$service_id = $res->service_id;
		$service_name = $res->service_name;
		$count++;
		
		//get service total
		$service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
		$service_payment = $this->company_financial_model->get_service_payments_total($service_id);
		$service_balance = abs($service_payment - $service_invoice);

		$total_service_invoice = $total_service_invoice + $service_invoice;
		$total_service_payment = $total_service_payment + $service_payment;
		$total_service_balance = $total_service_balance + $total_service_payment;
		
		$grand_total += $service_invoice;

		$service_result .='<div class="row">
								<div class="col-md-7">
									'.strtoupper($service_name).'
								</div>
								<div class="col-md-5">
									Ksh. '.number_format($service_invoice,2).'
								</div>
							</div>';

	}

	// $undefined_payment = $this->company_financial_model->get_service_payments_total(0);

	// $service_result .='<div class="row">
	// 						<div class="col-md-7">
	// 							OTHER INCOME
	// 						</div>
	// 						<div class="col-md-5">
	// 							Ksh. '.number_format($undefined_payment,2).'
	// 						</div>
	// 					</div>';

	$service_result .='<div class="row">
								<div class="col-md-7">
									TOTAL INCOME
								</div>
								<div class="col-md-5">
									<strong style="border-top: 2px solid #000">Ksh. '.number_format($total_service_invoice,2).'</strong>
								</div>
							</div>

							';
}


$total_purchases = $this->company_financial_model->get_total_purchases();
$total_stock_value = $this->company_financial_model->get_stock_value();

$expenses_query = $this->company_financial_model->get_child_accounts("Expense Accounts");
$total_expenses = 0;
$expenses_list ='';

foreach($expenses_query->result() AS $key_old) 
{ 
	$account_id = $key_old->account_id;
// var_dump($account_id); die();
	$amount_value = $this->company_financial_model->get_total_expense_amount($account_id);
	$expenses_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_old->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($amount_value,2).'
						</div>
					</div>';
	$total_expenses += $amount_value;			
}
?>
<section class="panel panel-primary">
    <header class="panel-heading">        
        <h2 class="panel-title center-align">PROFIT AND LOSS STATEMENT</h2>       
     </header>
    <div class="panel-body">        
		<div >
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 ">

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
						 	$search_title = "PROFIT AND LOSS REPORT ";
						 }
						 else
						 {
						 	$search_title =$search_title;
						 }
			            ?>
			            <hr>
			            <h3 class="center-align"><?php echo $search_title;?></h3> 
					</div>
					<div class="col-md-6 ">
						<div class="row">								
							<h5> <strong>ORDINARY INCOME/EXPENSE STATEMENT</strong></h5>
						</div>
						<div class="col-md-12">	
						<div class="row">							
							<h5> <strong>INCOME</strong></h5>							
						</div>						
							<?php echo $service_result;?>
						</div>
						<div class="col-md-12">
							<div class="row">
								
							<h5> <strong>COST OF GOOD SOLD</strong></h5>
							</div>
							<div class="row">
								<div class="col-md-7">
									CLOSING STOCK
								</div>
								<div class="col-md-5">
									Ksh. 0
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									PURCHASES
								</div>
								<div class="col-md-5">
									Ksh. <?php echo number_format($total_purchases,2);?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									CURRENT STOCK
								</div>
								<div class="col-md-5">
									Ksh. <?php echo number_format($total_stock_value,2);?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-7">
									TOTAL GOODS SOLD
								</div>
								<div class="col-md-5">
									<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_stock_value,2);?></strong>
								</div>
							</div>

						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-7">
								<h5> <strong>GROSS PROFIT</strong></h5>
							</div>
							<div class="col-md-5">
								<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_stock_value+$total_service_balance + $undefined_payment,2);?></strong>
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="row">								
								<h5> <strong>EXPENSES</strong></h5>
							</div>							
							<?php echo $expenses_list;?>
							<div class="row">
								<div class="col-md-7">
									TOTAL EXPENSES
								</div>
								<div class="col-md-5">
									<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_expenses,2)?></strong>
								</div>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-7">
								<h5> <strong>NET PROFIT</strong></h5>
							</div>
							<div class="col-md-5">
								<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_stock_value+$total_service_balance + $undefined_payment - $total_expenses,2)?></strong>
							</div>
						</div>
					</div>
				
				</div>
				
			</div>
		</div>
  	</div>
</section>
	    