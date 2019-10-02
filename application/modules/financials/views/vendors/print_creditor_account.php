<?php
$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
//served by
$served_by = '';//$this->accounts_model->get_personnel($this->session->userdata('personnel_id'));
$creditor_result = $this->company_financial_model->get_creditor_statement($creditor_id);

$result = '';
$total_dr_amount =0;
$total_cr_amount =0;
$balance = 0;
$cr_amount = 0;
$dr_amount = 0;

$search = $this->session->userdata('vendor_expense_search');
$opening_balance_rs = $this->company_financial_model->get_creditor_statement_balance($creditor_id);
$rows = $opening_balance_rs->row();

// var_dump($rows)die();

if(!empty($search))
{
    
    $balance += $rows->dr_amount;
    $balance -= $rows->cr_amount;
    $total_dr_amount += $dr_amount;
    $total_cr_amount += $cr_amount;
    $result .= '<tr>
                    <td colspan="3">Balance B/F</td>
                    <td>'.number_format($rows->dr_amount,2).'</td>
                    <td>'.number_format($rows->cr_amount,2).'</td>
                    <td>'.number_format($balance,2).'</td>
                </tr>';
}
$button = '';
if($creditor_result->num_rows() > 0)
{


  foreach ($creditor_result->result() as $key => $value) {
    # code...
    $referenceCode = $value->referenceCode;
    $transactionCode = $value->transactionCode;
    $dr_amount = $value->dr_amount;
    $cr_amount = $value->cr_amount;
    $transactionDescription = $value->transactionDescription;
    $transactionClassification = $value->transactionClassification;
    $transactionId = $value->transactionId;
    $referenceId = $value->referenceId;

    $transactionDate = $value->transactionDate;
    $balance += $dr_amount;
    $balance -= $cr_amount;
    $total_dr_amount += $dr_amount;
    $total_cr_amount += $cr_amount;

    if($transactionClassification == "Supplies Invoices" OR $transactionClassification == "Creditors Invoices")
    {
      $button = '<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$referenceId.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>';

      $transactionCode = $referenceCode;
    }

    if($transactionClassification == "Supplies Credit Note")
    {
      $button = '<td><a href="'.site_url().'print-suppliers-credit-note/'.$referenceId.'" class="btn btn-xs btn-warning" target="_blank"> View Note </a></td>';
    }



    if($transactionClassification == 'Creditor Opening Balance')
    {
      $result .= '<tr>
            <td colspan="3">'.$transactionDescription.'</td>
            <td>'.number_format($dr_amount,2).'</td>
            <td>'.number_format($cr_amount,2).'</td>
            <td>'.number_format($balance,2).'</td>

          </tr>';

    }
    else
    {
      $result .= '<tr>
            <td>'.$transactionDate.'</td>
            <td>'.$transactionCode.'</td>
            <td>'.$transactionDescription.'</td>
            <td>'.number_format($dr_amount,2).'</td>
            <td>'.number_format($cr_amount,2).'</td>
            <td>'.number_format($balance,2).'</td>
          </tr>';
    }

  }

  $result .= '<tr>
            <td colspan="3" >Totals</td>
            <td><b>'.number_format($total_dr_amount,2).'</b></td>
            <td><b>'.number_format($total_cr_amount,2).'</b></td>
            <td><b>'.number_format($balance,2).'</b></td>
          </tr>';
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
            <table class="table table-hover table-bordered ">
    				 	<thead>
    						<tr>
    						  <th>Transaction Date</th>
    						  <th>Document number</th>
    						  <th>Description</th>
    						  <th>Debit</th>
    						  <th>Credit</th>
                              <th>Balance</th>
    						</tr>
    					 </thead>
    				  	<tbody>
            	<?php echo $result;?>
            </tbody>
          </table>
            </div>

            <?php


            ?>
            <div class="col-md-12">
            	<h4>AGING REPORT</h4>
            	<table class="table table-hover table-bordered ">
					<thead>
						<tr>
						  <th class="center-align">Current Month</th>
						  <th class="center-align">1 - 30 Days</th>
						  <th class="center-align">31 - 60 Days</th>
						  <th class="center-align">61 - 90 Days</th>
						  <th class="center-align">Over 90 Days</th>
						  <th class="center-align">Total</th>
						</tr>
					</thead>
					<tbody>

            <?php

            $income_rs = $this->company_financial_model->get_payables_aging_report_by_creditor($creditor_id);
            $income_result = '';
            $total_income = 0;
            if($income_rs->num_rows() > 0)
            {
            	foreach ($income_rs->result() as $key => $value) {
            		# code...
            		// $total_amount = $value->total_amount;
            		$payables = $value->payables;
            		$thirty_days = $value->thirty_days;
            		$sixty_days = $value->sixty_days;
            		$ninety_days = $value->ninety_days;
            		$over_ninety_days = $value->over_ninety_days;
            		$coming_due = $value->coming_due;
            		$creditor_id = $value->recepientId;
            		$Total = $value->Total;
            		$income_result .='<tr>
            							<td class="text-right">'.number_format($coming_due,2).'</td>
            							<td class="text-right">'.number_format($thirty_days,2).'</td>
            							<td class="text-right">'.number_format($sixty_days,2).'</td>
            							<td class="text-right">'.number_format($ninety_days,2).'</td>
            							<td class="text-right">'.number_format($over_ninety_days,2).'</td>
            							<td class="text-right">'.number_format($Total,2).'</td>
            							</tr>';
            	}

            }
            echo $income_result;
            ?>

					</tbody>
				</table>
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
