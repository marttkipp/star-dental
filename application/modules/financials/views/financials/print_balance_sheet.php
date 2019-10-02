<?php
$bank_balances_rs = $this->company_financial_model->get_account_value();
$bank_balances_result = '';
$total_income = 0;
if($bank_balances_rs->num_rows() > 0)
{
	foreach ($bank_balances_rs->result() as $key => $value) {
		# code...
		$total_amount = $value->total_amount;
		$transactionName = $value->accountName;
		$total_income += $total_amount;
		$bank_balances_result .='<tr>
							<td class="text-left">'.$transactionName.'</td>
							<td class="text-right">'.number_format($total_amount,2).'</td>
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
	$balance_sheet_search = 'REPORTING AS OF : '.date('M j, Y', strtotime(date('Y-01-01'))).' TO '.date('M j, Y', strtotime(date('Y-m-d')));
}
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
            	
				 echo $balance_sheet_search;
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
						<?php echo $bank_balances_result;?>
					</tbody>
				</table>					
				</div>
				<div class="col-md-12">
					<div class="row">						
						<h6> <strong>B) Current Assets</strong></h6>							
					</div>	
					<table class="table">
						<thead>
							<th style="width: 60%"> NAME </th>
							<th style="width: 40%">AMOUNT</th>
						</thead>
						
						<tbody>
							<tr>
								<td >CASH ON HAND</td>
									<td class="text-right"><?php echo number_format($cash_on_hand,2);?></td>
								</tr>
								<tr>
											<td class="text-left">ACCOUNTS RECEIVABLES</td>
									<td class="text-right"><?php echo number_format($accounts_receivable,2);?></td>
								</tr>
								<tr>
									<td class="text-left ">TOTAL CURRENT ASSETS</td>

									<td class="text-right"><b class="match"><?php echo number_format($total_assets,2);?></b></td>
								</tr>
						</tbody>
					</table>
				</div>

				<div class="col-md-12">
					<div class="row">						
						<h6> <strong>C) Fixed Assets</strong></h6>	
					</div>	
					<table class="table">
						<thead>
							<th style="width: 60%"> NAME </th>
							<th style="width: 40%">AMOUNT</th>
						</thead>
						
						<tbody>
							<?php echo $fixed_asset_result?>
							<tr>
								<td class="text-left ">TOTAL FIXED ASSETS</td>
									<td class="text-right"><b class="match"><?php echo number_format($total_fixed,2);?></b></td>
								</tr>
								
						</tbody>
					</table>
				</div> 
				<!-- <div class="col-md-12">
					<div class="row">						
						<h6> <strong>D) Other Assets</strong></h6>							
					</div>	
					<table class="table">
						<thead>
							<th style="width: 60%"> NAME </th>
							<th style="width: 40%">AMOUNT</th>
						</thead>
						
						<tbody>
						</tbody>
					</table>
				</div> -->
				<!-- <div class="col-md-12">
					<div class="row">						
						<h6> <strong>E) Fixed Assets</strong></h6>							
					</div>	
					<table class="table">
						<thead>
							<th style="width: 60%"> NAME </th>
							<th style="width: 40%">AMOUNT</th>
						</thead>
						
						<tbody>
						</tbody>
					</table>
				</div> -->
				<div class="col-md-12">
						<table class="table">
						<tbody>
							<tr>
								<td style="width: 60%"><strong>NET ASSETS</strong></td>
								<td style="width: 40%" class="text-right"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_assets + $total_fixed,2)?></strong></td>
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
								<td class="text-left">ACCOUNTS PAYABLE</td>
							<td class="text-right"><?php echo number_format($accounts_payable,2)?></td>
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
						
					</tbody>
				</table>					
				</div>
				<div class="col-md-12">
						<table class="table">
						<tbody>
							<tr>
								<td style="width: 60%"><strong>TOTAL LIABILITIES</strong></td>
								<td style="width: 40%" class="text-right"><strong style="border-top: 2px solid #000">Ksh. <?php echo number_format($total_liability,2)?></strong></td>
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

            </div>
        </div>

    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-sm-12">
                <div class="col-sm-10 pull-left">
                    <strong>Prepared by: </strong>
                </div>
                <div class="col-sm-2 pull-right">
                    <?php echo date('jS M Y H:i a'); ?>
                </div>
            </div>

        </div>
    </body>

</html>
