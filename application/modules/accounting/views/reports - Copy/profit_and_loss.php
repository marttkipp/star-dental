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
		$service_payment = 0;//$this->company_financial_model->get_service_payments_total($service_id);
		$service_balance = abs($service_payment - $service_invoice);

		$total_service_invoice = $total_service_invoice + $service_invoice;
		$total_service_payment = $total_service_payment + $service_payment;
		$total_service_balance = $total_service_balance + $service_invoice;
		
		$grand_total += $service_invoice;

		$service_result .='<div class="row">
								<div class="col-md-7">
									'.strtoupper($service_name).'
								</div>
								<div class="col-md-5">
									<a href="'.site_url().'company-financials/services-bills/'.$service_id.'" > Ksh. '.number_format($service_invoice,2).'</a>
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
	$amount_value += $this->company_financial_model->get_expense_account_payments($account_id);

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




$cost_expenses_query = $this->company_financial_model->get_child_accounts("Cost of Sales");
$total_cgs = 0;
$cost_of_goods_list ='';

foreach($cost_expenses_query->result() AS $key_new) 
{ 
	$account_id = $key_new->account_id;
	$service_id = $key_new->service_id;
	$inventory_status = $key_new->inventory_status;
// var_dump($account_id); die();
	// if($inventory_status == 0)
	// {

		
	// }
	// else
	// {
		$cost_of_sale = $this->company_financial_model->get_total_expense_amount($account_id);
		$cost_of_sale += $this->company_financial_model->get_service_invoice_total_products($service_id);
	// }
	// $service_invoice = $this->company_financial_model->get_service_invoice_total($service_id);
	// $balance =  $cost_of_sale - $service_invoice;
	$cost_of_goods_list .='<div class="row">
						<div class="col-md-7">
							'.strtoupper($key_new->account_name).'
						</div>
						<div class="col-md-5">
							Ksh. '.number_format($cost_of_sale,2).'
						</div>
					</div>';
	$total_cgs += $cost_of_sale;			
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
							
							<?php echo $cost_of_goods_list;?>
							

						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-7">
								<h5> <strong>TOTAL COGS</strong></h5>
							</div>
							<div class="col-md-5">
								<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_cgs,2);?></strong>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-7">
								<h5> <strong>GROSS PROFIT</strong></h5>
							</div>
							<div class="col-md-5">
								<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_service_balance - $total_cgs,2);?></strong>
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
								<strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_service_balance - $total_cgs - $total_expenses,2)?></strong>
							</div>
						</div>
					</div>
				
				</div>
				
			</div>
		</div>
  	</div>
</section>
	    