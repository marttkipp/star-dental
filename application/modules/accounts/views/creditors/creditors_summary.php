<?php
$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));
$result = '';

if ($query->num_rows() > 0)
{
	
	$result .= '
			<table class="table table-hover table-bordered ">
			  <thead>
				<tr>
				  <th>Company Name</th>
				  <th>Invoice Number</th>
				  <th>Date</th>
				  <th>Amount</th>
				</tr>
			  </thead>
			  <tbody>
		';
		$checker_creditor = '';
		$company_balance  = 0;
		$total_amount = 0;
		$result_tow = '';
	
	foreach ($query->result() as $row)
	{
		$count++;
		$creditor_id = $row->creditor_id;
		$creditor_name = $row->creditor_name;
		$opening_balance = $row->opening_balance;
		$debit_id = $row->debit_id;
		$creditor_account_amount = $row->creditor_account_amount;
		$creditor_account_date = $row->creditor_account_date;
		$transaction_code = $row->transaction_code;
		
		$start_creditor = $creditor_name;

		if($checker_creditor == $creditor_name)
		{


			$empty = '<td></td>';
		}
		else
		{
			$empty = '<td>'.$creditor_name.'</td>';
			$company_balance =0;
		}
		
		$result .= 
			'
				<tr>
					'.$empty.'
					<td>'.$transaction_code.'</td>
					<td>'.$creditor_account_date.'</td>
					<td>KES. '.number_format($creditor_account_amount, 2).'</td>
				</tr>
					';
		$total_amount = $total_amount + $creditor_account_amount;
		$company_balance  = $company_balance + $creditor_account_amount;
		if($creditor_name == $checker_creditor )
		{

			$result .= 
				'
					<tr>
						<td colspan=3><strong>TOTAL</strong></td>
						<td><strong>KES. '.number_format($company_balance, 2).'</strong></td>
					</tr>
						';

		}

		$checker_creditor = $creditor_name;

		
	}
	$result .= 
				'
					<tr>
						<td colspan=3><strong>GRAND TOTAL</strong></td>
						<td><strong>KES. '.number_format($total_amount, 2).'</strong></td>
					</tr>
						';
	
	$result .= 
		'
					  </tbody>
					</table>
		';
}

else
{
	$result .= "There are no creditors";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Creditors</title>
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
        	<div class="col-md-12 center-align">
            	<strong><?php echo $title;?></strong>
            </div>
        </div>
        
    	<div class="row">
        	<div class="col-md-12">
            	<?php echo $result;?>
            </div>
        </div>
        
    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-md-10 pull-left">
            	Prepared by: <?php echo $served_by;?> 
          	</div>
        	<div class="col-md-2 pull-right">
            	<?php echo date('jS M Y H:i a'); ?>
            </div>
        </div>
    </body>
    
</html>