<?php
// var_dump($order_number); die();
$order_approval_status = $this->orders_model->get_order_approval_status($order_id);
$result_order = '<table class="example table-autosort:0 table-stripeclass:alternate table  table-bordered " id="TABLE_2">
					  <thead>
						<tr>	
						  <th >#</th>
						  <th >Product Name</th>
						  <th >Requested Quantity</th>
						  <th >Current Stock</th>
						</tr>
					  </thead>
					  <tbody>';
if($order_item_query->num_rows() > 0)
{
	$col = '';
	$message = '';

	$count = 0;
	$invoice_total = 0;
	// var_dump($order_item_query->result()); die();

	
	foreach($order_item_query->result() as $res => $value)
	{
		$order_id = $value->order_id;
		$product_name = $value->product_name;
		$product_id = $value->product_id;
		$order_item_quantity = $value->order_item_quantity;
		$in_stock = $value->in_stock;
		$order_item_id = $value->order_item_id;
		$supplier_unit_price = $value->supplier_unit_price;
        $count++;
        // var_dump($order_approval_status); die();

        // var_dump($product_id); die();

        $inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		
		
        $store_id = 5;
	    $parent_store_stock = $this->inventory_management_model->parent_stock_store($inventory_start_date, $product_id,$store_id);

	    $total_stock = $parent_store_stock;
		// echo $child_store_stock; die();
        $result_order .= '<tr>
				 				<td>'.$count.'</td>
				 				<td>'.$product_name.'</td>		 
				 				<td>	    
				                    '.$order_item_quantity.'
				                </td>
				                 <td>	    
				                   '.$parent_store_stock.'
				                </td>
				 					
				 			</tr>';


			
	}
	$result_order .='<tbody>
			 </table>'; 

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Order Details</title>
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
    	<div class="row receipt_bottom_border">
        	<div class="col-md-6 pull-left">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo" style="margin-left:0px;"/>
            </div>
        
        	<div class="col-md-6 pull-right ">
        		<p style="font-size: 13px;">
	            	<strong>
	                	<?php echo $contacts['company_name'];?><br/>
	                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
	                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
	                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
	                </strong>
	            </p>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<h4><strong>GOODS REQUEST NOTE FOR ORDER <?php echo $order_number;?></strong></h4>
            </div>
					
        </div>
        
    	<div class="row">
        	<div class="col-md-12">
            	<?php echo $result_order;?>
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