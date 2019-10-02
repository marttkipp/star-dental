<?php
$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));

$result =  '';

// echo $result;
$result = '';
$count = 0;
$balance = 0;
// get account opening balance
$opening_balance_query = $this->purchases_model->get_account_opening_balance('Petty Cash');
$cr_amount = 0;
$dr_amount = 0;
if($opening_balance_query->num_rows() > 0)
{
$row = $opening_balance_query->row();
$cr_amount = $row->cr_amount;
$dr_amount = $row->dr_amount;
$balance += $dr_amount;
$balance -=  $cr_amount;
$result .='
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>Balance B/F</td>
          <td >'.number_format($dr_amount,2).' </td>
          <td >'.number_format($cr_amount,2).' </td>
          <td >'.number_format($balance,2).' </td>

        </tr>
        ';
}
else {
$result .='
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>Balance B/F</td>
          <td>0.00</td>
          <td>0.00</td>
          <td>0.00 </td>

        </tr>
        ';
}



if($query_purchases->num_rows() > 0)
{
foreach ($query_purchases->result() as $key => $value) {
// code...
  $transactionClassification = $value->transactionClassification;

  $document_number = '';
  $transaction_number = '';
  $finance_purchase_description = '';
  $finance_purchase_amount = 0 ;
  if($transactionClassification == 'Purchase Payment')
  {
    $referenceId = $value->referenceId;

    // get purchase details
    if(empty($finance_purchase_id))
    {

      $document_number = '';
      $transaction_number = '';
      $finance_purchase_description = '';
    }
    else {
      $detail = $this->purchases_model->get_purchases_details($referenceId);
      $row = $detail->row();
      $document_number = $row->document_number;
      $transaction_number = $row->transaction_number;
      $finance_purchase_description = $row->finance_purchase_description;
    }

  }

   $referenceId = $value->referenceId;
  $document_number =$transaction_number = $value->referenceCode;
  $finance_purchase_description = $value->transactionName;
$cr_amount = $value->cr_amount;
$dr_amount = $value->dr_amount;


$transaction_date = $value->transactionDate;
$transaction_date = date('jS M Y',strtotime($transaction_date));
$creditor_name = '';//$value->creditor_name;
$creditor_id = 0;//$value->creditor_id;
$account_name = '';//$value->account_name;
$finance_purchase_id = '';//$value->finance_purchase_id;


$balance += $dr_amount;
$balance -=  $cr_amount;
$count++;
$result .='
          <tr>
            <td>'.$count.'</td>
            <td>'.$transaction_date.'</td>
            <td>'.$transaction_number.'</td>
            <td>'.$finance_purchase_description.' '.$creditor_name.'</td>
            <td>'.number_format($dr_amount,2).' </td>
            <td>'.number_format($cr_amount,2).'</td>
            <td>'.number_format($balance,2).'</td>

          </tr>
          ';
}


}

$result .='
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Balance</td>
        <td colspan="3" class="center-align"><strong>KES '.number_format($balance,2).' </strong></td>

      </tr>
      ';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Petty cash</title>
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
        <?php
        // $search_title = $this->session->userdata('accounts_search_title');
        ?>
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>PETTY CASH <br> <?php echo $search_title?></strong>
            </div>
        </div>

    	<div class="row">
        	<div class="col-md-12">
        	<?php
        		// var_dump($account); die();


			?>
			<table class="table table-hover table-bordered ">
				 	<thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Ref Number</th>
              <th>Description</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Bal</th>
              <!-- <th>Action</th> -->
            </tr>
					 </thead>
				  	<tbody>
				  		<?php  echo $result;?>
					</tbody>
				</table>
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
        	<div class="col-sm-12" style="margin-top:60px;">
                <div class="col-sm-2">
                	<strong>Checked by: </strong>
                </div>
                <div class="col-sm-4">

                </div>
            </div>
        	<div class="col-sm-12" style="margin-top:60px;">
                <div class="col-sm-2">
                	<strong>Approved by: </strong>
                </div>
                <div class="col-sm-4">

                </div>
            </div>
        </div>
    </body>

</html>
