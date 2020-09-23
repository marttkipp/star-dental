<?php
$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));

$creditor_result = $this->creditors_model->get_creditor_statement_print($creditor_id);
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
						  <th>Invoice Number</th>
						  <th>Description</th>
						  <th>Debit</th>
						  <th>Credit</th>						
						</tr>
					 </thead>
				  	<tbody>
				  		<?php  echo $creditor_result['result'];?>
					</tbody>
				</table>
            </div>
            <?php
            $date = date('Y-m-d');
            $this_month = $this->creditors_model->get_statement_value($creditor_id,$date,1);
            $three_months = $this->creditors_model->get_statement_value($creditor_id,$date,2);
            $six_months = $this->creditors_model->get_statement_value($creditor_id,$date,3);
            $nine_months = $this->creditors_model->get_statement_value($creditor_id,$date,4);
            $over_nine_months = $this->creditors_model->get_statement_value($creditor_id,$date,5);

            ?>
            <div class="col-md-12">
            	<h4>AGING REPORT</h4>
            	<table class="table table-hover table-bordered ">
					<thead>
						<tr>
						  <th class="center-align">This Month</th>
						  <th class="center-align">3 Months</th>
						  <th class="center-align">6 Months</th>
						  <th class="center-align">9 Months</th>
						  <th class="center-align">Over 9 Months</th>
						</tr>					
					</thead>
					<tbody>
						<tr>
						  <td style="text-align:center;"><strong>KES. <?php echo number_format($this_month,2);?></strong></td>
						  <td style="text-align:center"><strong>KES. <?php echo number_format($three_months,2);?></strong></td>
						  <td style="text-align:center"><strong>KES. <?php echo number_format($six_months,2);?></strong></td>
						  <td style="text-align:center"><strong>KES. <?php echo number_format($nine_months,2);?></strong></td>
						  <td style="text-align:center"><strong>KES. <?php echo number_format($over_nine_months,2);?></strong></td>
						</tr>
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