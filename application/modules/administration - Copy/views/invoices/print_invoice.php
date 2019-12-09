<?php

$row = $custom_invoice_query->row();
							
$created =  date('jS M Y H:i a',strtotime($row->custom_invoice_created));
$personnel_id = $row->custom_invoice_created_by;
$invoice_number = $row->custom_invoice_number;
$debtor = $row->custom_invoice_debtor;
$contacts = $row->custom_invoice_debtor_contacts;	
$status = $row->custom_invoice_status;
$payable_by = date('jS M Y',strtotime($row->payable_by));	

//creators and editors
if($personnel_query->num_rows() > 0)
{
	$personnel_result = $personnel_query->result();
	
	foreach($personnel_result as $adm)
	{
		$personnel_id2 = $adm->personnel_id;
		
		if($personnel_id == $personnel_id2)
		{
			$created_by = $adm->personnel_onames.' '.$adm->personnel_fname;
			break;
		}
		
		else
		{
			$created_by = '-';
		}
	}
}

else
{
	$created_by = '-';
}
?>

<!DOCTYPE html>
<html lang="en">
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
		
		.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
		.title-img{float:left; padding-left:30px;}
	</style>
    <head>
        <title>SUMC | Invoice</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link href="<?php echo base_url();?>assets/bluish/style/bootstrap.css" rel="stylesheet" media="all">
    </head>
    <body class="receipt_spacing">
    	<div class="row" >
        	<img src="<?php echo base_url();?>images/strathmore.gif" class="title-img"/>
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	Strathmore University Medical Center<br/>
                    P.O. Box 59857 00200, Nairobi, Kenya<br/>
                    E-mail: sumedicalcentre@strathmore.edu. Tel : +254703034001<br/>
                    Madaraka Estate<br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>INVOICE</strong>
            </div>
        </div>
        
        <!-- Patient Details -->
    	<div class="row receipt_bottom_border" style="margin-bottom: 10px;">
        	<div class="col-md-6 pull-left">
            	<div class="row">
                	<div class="col-md-12">
                    	
                    	<div class="title-item">Invoice to:</div>
                        
                    	<?php echo $debtor; ?>
                    </div>
                </div>
            	
            	<!--<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Patient Number:</div> 
                        
                    	<?php //echo $patient_number; ?>
                    </div>
                </div>-->
            
            </div>
            
        	<div class="col-md-6 pull-right">
            	<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Invoice Number:</div>
                        
                    	<?php echo $invoice_number; ?>
                    </div>
                </div>
            	
            	<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Invoice date:</div> 
                        
                    	<?php echo $created; ?>
                    </div>
                </div>
            </div>
        </div>
        
    	<div class="row receipt_bottom_border">
        	<div class="col-md-12 center-align">
            	<strong>BILLED ITEMS</strong>
            </div>
        </div>
        
    	<div class="row">
        	<div class="col-md-12">
                <table class="table table-hover table-bordered col-md-12">
                    <thead>
                        <tr>
                              <th>#</th>
                              <th>Description</th>
                              <th>Quantity</th>
                              <th>Cost (Ksh)</th>
                              <th>Total (Ksh)</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    <?php
                    //invoice items
					if($query->num_rows() > 0)
					{
						$total = $count = 0;
						foreach($query->result() as $res)
						{
							$description = $res->custom_invoice_item_description;
							$cost = $res->custom_invoice_item_cost;
							$quantity = $res->custom_invoice_item_quantity;
							$total += ($cost*$quantity);
							$count++;
							
							?>
							<tr>
								<td><?php echo $count;?></td>
								<td><?php echo $description;?></td>
								<td><?php echo $quantity;?></td>
								<td><?php echo number_format($cost, 2);?></td>
								<td><?php echo number_format(($cost*$quantity), 2);?></td>
							</tr>
							<?php
						}
						?>
							<tr>
								<th colspan="4">Total</th>
								<td><?php echo number_format($total, 2);?></td>
							</tr>
						<?php
					}
					
					else
					{
						echo '<tr>
								<th colspan="4">This invoice does not contain particulars</th>
							</tr>';
					}
				?>
                    </tbody>
                  </table>
            </div>
        </div>
        
    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-md-8 pull-left">
                <div class="col-md-4 pull-left">
                	<div class="row">
                    	<div class="col-md-12">
                  			Raised by: <?php echo $created_by;?>
                        </div>
                    </div>
                  	
                	<div class="row" style="margin-top:20px;">
                    	<div class="col-md-12">
                  			Signature: .....................................................................
                        </div>
                    </div>
                </div>
                <div class="col-md-8 center-align">
                	This invoice is payable on or before <?php echo $payable_by;?>
                </div>
            
          	</div>
            
        	<div class="col-md-4 pull-right">
            	<?php echo date('jS M Y H:i a'); ?> Thank you
            </div>
        </div>
    </body>
    
</html>