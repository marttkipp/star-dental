<?php
$start_date = '2018-01-01';
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
		$service_invoice = $this->company_financial_model->get_service_invoice_total($service_id,$start_date);
		$service_payment = $this->company_financial_model->get_service_payments_total($service_id);
		$service_balance = abs($service_payment - $service_invoice);

		$total_service_invoice = $total_service_invoice + $service_invoice;
		$total_service_payment = $total_service_payment + $service_payment;
		$total_service_balance = $total_service_balance + $service_invoice;
		
		$grand_total += $service_invoice;

		$service_result .='<tr>
								<td>
									'.strtoupper($service_name).'
								</td>
								<td>
									 Ksh. '.number_format($service_invoice,2).'
								</td>
							<tr>';

	}

	$service_result .='<tr>
								<td>
									<strong>TOTAL INCOME</strong>
								</td>
								<td>
									<strong style="border-top: 2px solid #000">Ksh. '.number_format($total_service_invoice,2).'</strong>
								</td>
							</tr>

							';
}


$closing_stock =  $this->company_financial_model->get_closing_stock();
$total_purchases = $this->company_financial_model->get_total_purchases($start_date);
$total_stock_value = $this->company_financial_model->get_stock_value();

$expenses_query = $this->company_financial_model->get_child_accounts("Expense Accounts");
$total_expenses = 0;
$expenses_list ='';

foreach($expenses_query->result() AS $key_old) 
{ 
	$account_id = $key_old->account_id;
	// $account_id = $key_old->account_id;
// var_dump($account_id); die();
	$amount_value = $this->company_financial_model->get_total_expense_amount($account_id,$start_date);
	$amount_value += $this->company_financial_model->get_total_payment_amount($account_id,$start_date);

	$expenses_list .='<tr>
						<td>
							'.strtoupper($key_old->account_name).'
						</td>
						<td>
							Ksh. '.number_format($amount_value,2).'
						</td>
					</td>';
	$total_expenses += $amount_value;			
}

$expenses_list .='<tr>
							<td><strong>TOTAL EXPENSE</strong></td>
							<td><strong style="border-top: 2px solid #000">Ksh. '.number_format($total_expenses,2).'</strong></td>
						</tr>';

$current_stock = (($total_purchases+$closing_stock) - $total_stock_value);

$total_gross_profit = ($total_service_invoice + $undefined_payment) - (($total_purchases+$closing_stock)-$current_stock);

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
            	<strong>PROFIT AND LOSS STATEMENT</strong><br>

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
							<h5> <strong>INCOME</strong></h5>							
						</div>	
						<table class="table">
							<thead>
								<th style="width: 60%"> ITEM </th>
								<th style="width: 40%">AMOUNT</th>
							</thead>
							
							<tbody>
								<?php echo $service_result;?>
							</tbody>
						</table>					
						</div>
						<div class="col-md-12">
							<div class="row">
								
							<h5> <strong>COST OF GOOD SOLD</strong></h5>
							</div>
							<table class="table">
								<thead>
									<th style="width: 60%"> ITEM </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								<tbody>
									<tr>
										<td>CLOSING STOCK</td>
										<td>Ksh. <?php echo number_format($closing_stock,2);?></td>
									</tr>
									<tr>
										<td>PURCHASES</td>
										<td>Ksh. <?php echo number_format($total_purchases,2);?></td>
									</tr>
									<tr>
										<td>CURRENT STOCK</td>
										<td>Ksh. <?php echo number_format($current_stock,2);?></td>
									</tr>
								</tbody>
							</table>	
							

						</div>
						<div class="col-md-12">
								<table class="table">
								<tbody>
									<tr>
										<td style="width: 60%"><strong>GROSS PROFIT</strong></td>
										<td style="width: 40%"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_gross_profit,2);?></strong></td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<div class="col-md-12">
							<div class="row">								
								<h5> <strong>EXPENSES</strong></h5>
							</div>		
							<table class="table">
								<thead>
									<th style="width: 60%"> ITEM </th>
									<th style="width: 40%">AMOUNT</th>
								</thead>
								<tbody>
									<?php echo $expenses_list;?>
								</tbody>
							</table>		
						</div>
						<div class="col-md-12">
								<table class="table">
								<tbody>
									<tr>
										<td style="width: 60%"><strong>NET PROFIT</strong></td>
										<td style="width: 40%"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_gross_profit - $total_expenses,2)?></strong></td>
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