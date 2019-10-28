<?php
$order_details = $this->orders_model->get_order_details($order_id);
$orders_date = date('Y-m-d');
if($order_details->num_rows() > 0)
{
	foreach ($order_details->result() as $key => $value) {
		# code...
		$orders_date = $value->orders_date;
	}
}
$result ='';
if($order_item_query->num_rows() > 0)
{
	$col = '';
	$message = '';

	$result = '';
	$count = 0;
	$invoice_total = 0;
	// var_dump($order_item_query->num_rows()); die();
	foreach($order_item_query->result() as $res)
	{
		$order_id = $res->order_id;
		$product_name = $res->product_name;
		$order_item_quantity = $res->order_item_quantity;
		$in_stock = $res->in_stock;
		$order_item_id = $res->order_item_id;
		$supplier_unit_price = $res->supplier_unit_price;
        $count++;
        // var_dump($order_approval_status); die();
			
	}
}
	
$result .= ' 
	 <tr>
	 	<div class="col-md-12">';
	 	$creditor_items = $this->orders_model->get_ordered_list($order_id);
	 	// var_dump($creditor_items->num_rows()); die();
	 	$result .= '
		 				<table class="example table-autosort:0 table-stripeclass:alternate table  table-bordered " id="TABLE_2">
				  <thead>
					<tr>
					  <th >#</th>
					  <th >Item Name</th>
					  <th >Pack size</th>
					  <th >QTY</th>
					  <th >T.Units</th>
					  <th >Selling Price Unit Price</th>
					  <th >VAT</th>	
						<th >Amount</th>		
					
					</tr>
				  </thead>
				  <tbody>';
				  		if($creditor_items->num_rows() > 0)
						{	$counters = 0;
							$total_price_items =0;
							$total_balance_top =0 ;
						foreach ($creditor_items->result() as $creditoritems){

								$product_idd = $creditoritems->product_id;
								$product_deductions_id = $creditoritems->product_deductions_id;
								$product_name1 = $creditoritems->product_name;
								$quantity1 = $creditoritems->supplying;
								$unit_price1 = $creditoritems->product_unitprice;
								$product_deductions_id = $creditoritems->product_deductions_id;
								$invoice_number = '';//$creditoritems->invoice_number;
								$quantity_given = $creditoritems->quantity_given;
								$quantity_requested = $creditoritems->quantity_requested;
								$discount = 0.00;//$creditoritems->discount;
								$vat = $creditoritems->vatable;

								$pack_size = $creditoritems->pack_size;
								$item_id = $creditoritems->item_id;
								$vatable = $creditoritems->vatable;
								$product_id = $creditoritems->product_id;

								$amount = $quantity_given * $pack_size;
								 $total_price_items = $total_price_items + ($quantity1 * $unit_price1);
								 // var_dump($unit_price1); die();

								 if(empty($unit_price1) || empty($pack_size) || empty($quantity_given) )
								 {
								 	$buy_units = 0;
								 }
								 else
								 {

								 $buy_units = $unit_price1/($pack_size*$quantity_given);	
								 }
								 $total_items_price = ($quantity_given) * $unit_price1;
								 if($discount > 0)
								 {

								 	$current_price = $total_items_price - (($discount/100)*$total_items_price);
								 }
								 else
								 {
								 	$current_price = $total_items_price;
								 }
								 
								
								$counters++;

								if($quantity1 == $amount)
								{
									$color = 'default';
								}
								else
								{
									$color = 'default';
								}

								if($vatable)
								{
									$vat = 'Yes';
								}
								else{
									$vat = 'No';
								}


								$counters++;

								if($quantity1 == $amount)
								{
									$color = 'success';
								}
								else
								{
									$color = 'default';
								}
								$total_price = ($quantity_given*$pack_size) * $unit_price1;
								$total_balance_top +=$total_price;

								$result .='<tr class="'.$color.'">
								 				<td>'.$counters.'</td>
								 				<td>'.$product_name1.'</td>
								 				<td>	    
								                    '.$pack_size.'
								                </td>
								                <td>	    
								                    '.$quantity_given.'
								                </td>
								 				<td>'.$quantity_given*$pack_size.'</td>
						                        <td>'.$unit_price1.'</td>
								 				<td>'.$vat.'</td>
								 				<td>'.number_format($total_price,2).'</td>
								 				
								 				
								 			</tr>';
							}
							$result .= '<tr>
											<td colspan="7">Total</td>
											<td>'.number_format($total_balance_top,2).'</td>
										</tr>';
						}
				  $result .=' 	
				<tbody>
			</table>
	 		</div>
	<tr>'; 
	
			


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
				font-size:12px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 10px;
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
            	<h4><strong>GOODS NOTE FOR INVOICE <?php echo $order_number;?></strong></h4>
            	<h5><strong><?php echo strtoupper($creditor_name);?></strong> </h5>
            	<h5><strong>DATE: <?php echo date('jS M Y',strtotime($orders_date));?></strong> </h5>
            </div>
					
        </div>
        
    	<div class="row">
        	<div class="col-md-12">
            	<?php echo $result;?>
            </div>

        </div>
        <div class="row">
        	<div class="col-md-12">
		    	<div class="col-md-12" style="margin-bottom: 30px; margin-top: 20px;">
		        	<div class="col-md-4 pull-left">
		            	Prepared by : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Signature : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Date : ......................................................
		          	</div>
		        </div>
		        <div class="col-md-12" style="margin-bottom: 30px;">
		        	<div class="col-md-4 pull-left">
		            	Approved by : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Signature : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Date : ......................................................
		          	</div>
		        </div>
		        <div class="col-md-12" style="margin-bottom: 30px;">
		        	<div class="col-md-4 pull-left">
		            	Recorded by : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Signature : ......................................................
		          	</div>
		          	<div class="col-md-4 pull-left">
		            	Date : ......................................................
		          	</div>
		        </div>
		    </div>
		</div>
    </body>
    
</html>