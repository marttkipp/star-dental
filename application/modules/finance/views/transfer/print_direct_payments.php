
<?php
//unset the sessions set\
$search = $this->session->userdata('accounts_search');
$search_title = $this->session->userdata('accounts_search_title');//echo $account;die();

$search_title = $this->session->userdata('title_direct_payments');
// $search_title  = '';
// if($account_ledger_search == 1)
// {
// 	$account = $this->session->userdata('account_id');
// 	$search_title = $this->session->userdata('search_title');

// 	$account_date_from = $this->session->userdata('account_date_from');
//     $account_date_to = $this->session->userdata('account_date_to');
// 	if(!empty($account_date_from) AND !empty($account_date_to))
// 	{
// 		$search_title .= ' FROM PERIOD BETWEEN '.$account_date_from.'  AND '.$account_date_to.'';
// 	}
// 	else if(!empty($account_date_from) AND empty($account_date_to))
// 	{
// 		$search_title .= ' FOR "'.$account_date_from.'"';
// 	}
// 	else if(empty($account_date_from) AND !empty($account_date_to))
// 	{
// 		$search_title .= ' FOR "'.$account_date_to.'"';
// 	}
// 	else
// 	{
// 		$search_title .= '';
// 	}
// 	// $opening_bal = $this->petty_cash_model->get_account_opening_bal($account);
// }
// else
// {
// 	$opening_bal = $this->petty_cash_model->get_total_opening_bal();
// }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | DIRECT PAYMENTS</title>
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
			.col-print-1 {width:8%;  float:left;}
			.col-print-2 {width:16%; float:left;}
			.col-print-3 {width:25%; float:left;}
			.col-print-4 {width:33%; float:left;}
			.col-print-5 {width:42%; float:left;}
			.col-print-6 {width:50%; float:left;}
			.col-print-7 {width:58%; float:left;}
			.col-print-8 {width:66%; float:left;}
			.col-print-9 {width:75%; float:left;}
			.col-print-10{width:83%; float:left;}
			.col-print-11{width:92%; float:left;}
			.col-print-12{width:100%; float:left;}

			thead {display: table-header-group;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row receipt_bottom_border">
    		<div class="col-md-12">

	        	<div class="col-print-6">
	            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo pull-left" />
	            </div>
	        	<div class="col-print-6">
	            	<strong>
	                	<?php echo $contacts['company_name'];?><br/>
	                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
	                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
	                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
	                </strong>
	            </div>
	         </div>
        </div>

      <div class="row receipt_bottom_border" >
        	<div class="col-md-12">
            	<h4><strong>DERECT PAYMENTS</strong></h4>

            	<h5><?php echo strtoupper($search_title);?></h5>
            

            </div>
        </div>

    	<div class="row">
			<div class="col-md-12">	
				<table class="table table-hover table-bordered table-condensed table-stripped ">
				 	<thead>
						<tr>
                          <th>#</th>   
                          <th>Date</th>      
                          <th>Document No.</th>              
                          <th>Account From</th>
                          <th>Description</th>
                          <th>Amount</th>                      
                        </tr>
					 </thead>
				  	<tbody>
				  		<?php
				  			
				  			$result = '';
                            // var_dump($query); die();
                           if($query->num_rows() > 0)
                           {
                             $x=0;
                                foreach ($query->result() as $key => $value) {
                                    # code...
                                    $account_from_id = $value->account_from_id;
                                    $account_to_type = $value->account_to_type;
                                    $account_to_id = $value->account_to_id;
                                    $receipt_number = $value->receipt_number;
                                    $account_payment_id = $value->account_payment_id;
                                     $payment_date = $value->payment_date;
                                     $created = $value->created;
                                    $amount_paid = $value->amount_paid;
                                    $payment_to = $value->payment_to;

                                   $property_beneficiary_id = $value->property_beneficiary_id;

                                        $account_from_name = $this->transfer_model->get_account_name($account_from_id);
                                        if($account_to_type == 1 AND $account_to_id > 0)
                                        {
                                            $payment_type = 'Transfer';
                                            $account_to_name = $this->transfer_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3 AND $payment_to > 0)
                                        {
                                            // doctor payments
                                            $payment_type = "Landlord Payment";
                                            $account_to_name = $this->transfer_model->get_owner_name($payment_to);
                                        }
                                        else if($account_to_type == 2 AND $account_to_id > 0)
                                        {
                                            // creditor
                                            $payment_type = "Creditor Payment";
                                            $account_to_name = $this->transfer_model->get_creditor_name($account_to_id);
                                        }
                                        else if($account_to_type == 4 AND $account_to_id > 0)
                                        {
                                            // expense account
                                            $payment_type = "Direct Expense Payment";
                                            $account_to_name = $this->transfer_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3 AND $property_beneficiary_id > 0)
                                        {
                                            // doctor payments
                                            $payment_type = "Landlord Payment";
                                            $account_to_name = $this->transfer_model->get_beneficiary_name($property_beneficiary_id);
                                        }
                                        else
                                        {
                                          $account_to_name ='';
                                        }



                                    $x++;

                                    $result .= '<tr>
                                                    <td>'.$x.'</td>
                                                    <td>'.$payment_date.'</td>
                                                    <td>'.strtoupper($receipt_number).'</td>
                                                    <td>'.$account_from_name.'</td>
                                                    <td>'.$payment_type.' '.$account_to_name.'</td>
                                                    <td>'.number_format($amount_paid,2).'</td>
                                                </tr>';

                                }
                           }
                           echo $result;
				  		?>
					</tbody>
				</table>

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
