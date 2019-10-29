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
	
				 $result .= ' 
							 			 <tr>
							 			 	<div class="col-md-12">';
							 			 	$creditor_items = $this->orders_model->get_supplied_list($order_id);
							 			 	$result .= '
					 			 		 				<table class="example table-autosort:0 table-stripeclass:alternate table  table-bordered " id="TABLE_2">
														  <thead>
															<tr>	
															  <th >#</th>

															  <th >Item Name</th>
															  <th >Pack size</th>
															  <th >QTY</th>
															  <th >T.Units</th>
															  <th >Expiry Date</th>
															  <th >Buying Price</th>
															  <th >Discount %</th>
															  <th >VAT</th>	
															   <th >Amount</th>			
															</tr>
														  </thead>
														  <tbody>';
														  		if($creditor_items->num_rows() > 0)
																{	$counters = 0;
																	$total_price_items =0;
																	$total_balance_top =0 ;
																	$total_vat = 0;
																foreach ($creditor_items->result() as $creditoritems){

																	$vat_capture = 0;
																		$product_idd = $creditoritems->product_id;
																		$creditor_id = $creditoritems->creditor_id;
																		$order_supplier_id = $creditoritems->order_supplier_id;
																		$product_name1 = $creditoritems->product_name;
																		$quantity1 = $creditoritems->supplying;
																		$unit_price1 = $creditoritems->single_price;
																		$mark_up = $creditoritems->mark_up;
																		$selling_unit_price = $creditoritems->selling_unit_price;
																		$order_supplier_id = $creditoritems->order_supplier_id;
																		$creditor_name = $creditoritems->creditor_name;
																		$invoice_number = $creditoritems->invoice_number;
																		$quantity_received = $creditoritems->quantity_received;
																		$vat = $creditoritems->vat;
																		$discount = $creditoritems->discount;
																		$total_amount = $creditoritems->less_vat;

																		$pack_size = $creditoritems->pack_size;

																		$amount = $quantity_received * $pack_size;
																		$expiry_date = $creditoritems->expiry_date;
																		 $total_price_items = $total_price_items + ($quantity1 * $unit_price1);
																		  $buy_units = $unit_price1/($pack_size*$quantity_received);

																		   $total_items_price = ($quantity_received) * $unit_price1;
																		 $buying_price_vat = $creditoritems->buying_price_vat;


																		 if($buying_price_vat == 0)
																		{
																			$unit_price2 = $unit_price1/1.16;
																		}
																		else
																		{
																			$unit_price2 = $unit_price1;
																		}

																		 if(empty($unit_price1) || empty($pack_size) || empty($quantity_received) )
																		 {
																		 	$buy_units = 0;
																		 }
																		 else
																		 {

																		 $buy_units = $unit_price1/($pack_size*$quantity_received);	
																		 }
																		 $total_items_price = ($quantity_received) * $unit_price1;
																		 if($discount > 0)
																		 {

																		 	$current_price = $total_items_price - (($discount/100)*$total_items_price);
																		 }
																		 else
																		 {
																		 	$current_price = $total_items_price;
																		 }
																		 if($vat > 0)
																		 {

																			$vat_capture = (16 *$current_price)/116;
																			// $current_price += $vat_capture;
																			$total_vat += $vat_capture;
																		 }



																		 $total_balance_top +=$current_price;



																		$counters++;

																		if($quantity1 == $amount)
																		{
																			$color = 'success';
																		}
																		else
																		{
																			$color = 'default';
																		}

																		$result .='<tr class="'.$color.'">
																		 				<td>'.$counters.'</td>
																		 				<td>'.$product_name1.'</td>
																		               
																		 				<td>	    
																		                    '.$pack_size.'
																		                </td>
																		                 <td>	    
																		                   '.$quantity_received.'
																		                </td>
																		 				<td>'.$quantity_received*$pack_size.'</td>
																		 				<td>
																                            '.$expiry_date.'
																                        </td>
																                        <td>'.number_format($unit_price2,4).'</td>
																		 				<td>'.$discount.'</td>
																		 				<td>'.$vat.'</td>
																		 				<td>'.number_format($total_amount,2).'</td>
																		 				
																		 				
																		 			</tr>';
																	}
																	$result .= '
																				<tr>
																					<td colspan="9">Invoice Amount</td>
																					<td>'.number_format($total_balance_top -$total_vat ,2).'</td>
																				</tr>
																				<tr>
																					<td colspan="9">VAT</td>
																					<td>'.number_format($total_vat,2).'</td>
																				</tr>
																					<tr>
																					<td colspan="9">Total Amount</td>
																					<td>'.number_format($total_balance_top,2).'</td>
																				</tr>';
																
																}
														  $result .=' 	
														  <tbody>
														  </table>';
														  $result .= '
							 			 	</div>

							 			 <tr>

										'; 
	
						
			
				
    ?>
    <?php




$result .= '
';

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
			.receipt_spacing{letter-spacing:0px; font-size: 9px;}
			.center-align{margin:0 auto; text-align:center;}
			
			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:9px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 3px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 10px;}
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
            	<h4><strong>GOODS RECEIVED NOTE FOR INVOICE <?php echo $invoice_number;?></strong></h4>
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