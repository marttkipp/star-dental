<?php
//unset the sessions set\
$search = $this->session->userdata('accounts_search');
$search_title = $this->session->userdata('accounts_search_title');//echo $account;die();

$ledger_search = $this->session->userdata('expense_ledger_search');
$search_title  = '';
if($ledger_search == 1)
{
	$account = $this->session->userdata('expense_account_id');
	$search_title = $this->session->userdata('expense_search_title');
	$opening_bal = $this->petty_cash_model->get_account_opening_bal($account);
}
// else
// {
// 	$opening_bal = $this->petty_cash_model->get_total_opening_bal();
// }

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
    	
        
     
        
    	<div class="col-md-12">
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
	            	<strong><?php echo $this->session->userdata('expense_account_name');?></strong><br>
	            </div>
	        </div>
    		<table class="table table-hover table-bordered ">
			 	<thead>
					<tr>
					  <th>Transaction Date</th>						  
					  <th>Account To</th>
					  <th>Account From</th>
					  <th>Description</th>
					  <th>Voucher</th>
					  <th>Amount</th>						
					</tr>
				 </thead>
			  	<tbody>
			  		<?php
			  			$expense_ledger_search = $this->session->userdata('expense_ledger_search');
						if($expense_ledger_search == 1)
						{
							$account = $this->session->userdata('expense_account_id');
							$account_name = $this->session->userdata('expense_account_name');
							
							$statement_result = $this->petty_cash_model->get_expense_ledger_statement($account,$account_name);
							echo $statement_result['result'];
						}
						else
						{
							echo '<tr><td colspan="5">Please select an account</td></tr>';
						}

			  		?>
				</tbody>
			</table>

        	
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