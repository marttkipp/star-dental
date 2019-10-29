<section class="panel">
	<!-- <div class="panel-body"> -->
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-12">
				<div class="col-md-6">
					<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>

				</div>
				<div class="col-md-2">
				</div>
				<div class="col-md-4">
					<div class="col-md-12">
					<?php echo $creditor_name;?><br>
                    E-mail: <?php echo $creditor_email;?>. Tel : <?php echo $creditor_phone;?><br/>
					</div>
					<div class="col-md-12">
					<?php
					$order_approval_status = $this->orders_model->get_order_approval_status($order_id);

					?>
						<strong>Date</strong> <?php echo $created;?> <br>
						<strong> Order No # </strong>  <?php echo $order_number;?>
					
					</div>

				</div>
			</div>
		</div>
	<!-- </div> -->
</section>


<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-left">Order Items for <?php echo $store_name;?> Order <?php echo $order_number;?></h2>
         <div class="widget-icons pull-right">
         		<?php
         		if($order_approval_status != 7)
				{
         		?>
         		<a href="<?php echo base_url();?>goods-transfered-notes/<?php echo $order_id;?>" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Print Order Details</a>
         		<?php

         		}
         		?>
         	
         		<?php
         		if($order_approval_status != 7)
				{
         		?>
         		<a  class="btn btn-primary btn-sm"  data-toggle='modal' data-target='#add_provider_items'><i class="fa fa-plus"></i> Add Item</a>
         		<?php
         		}
         		?>
            	<a href="<?php echo base_url();?>procurement/drug-transfers" class="btn btn-info btn-sm"><i class="fa fa-arrow-left"></i> Back to Orders</a>
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">

    	
    	<?php
    		$error = $this->session->userdata('error_message');
			$success = $this->session->userdata('success_message');
			$search_result ='';
			$search_result2  ='';
			if(!empty($error))
			{
				echo $search_result2 = '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}
			
			if(!empty($success))
			{
				echo $search_result2 ='<div class="alert alert-success">'.$success.'</div>';
				$this->session->unset_userdata('success_message');
			}


    	?>
		<br>
    	<?php
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
					$order_item_quantity = $res->quantity_requested;
					$in_stock = 0;//$res->in_stock;
					$order_item_id = $res->product_deductions_id;
					$supplier_unit_price = $res->product_unitprice;
                    $count++;
                    // var_dump($order_approval_status); die();
						
				}
				if($order_approval_status == 7)
				{
							 $result .= ' 
							 			 <tr>
							 			 	<div class="col-md-12">';
							 			 	$creditor_items = $this->orders_model->get_ordered_list($order_id);
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
																		// $expiry_date = $creditoritems->expiry_date;
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
																		 
																		 $total_balance_top +=$current_price;
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
																		 				<td>'.number_format($current_price,2).'</td>
																		 				
																		 				
																		 			</tr>';
																	}
																	$result .= '<tr>
																					<td colspan="7">Total</td>
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
				}
				else
				{
						
							 $result .= ' 
							 			 <tr>
							 			 	<div class="col-md-12">';
							 			 	$creditor_items = $this->orders_model->get_ordered_list($order_id);
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
															  <th colspan="2">Action</th>
															
															</tr>
														  </thead>
														  <tbody>';
														  		if($creditor_items->num_rows() > 0)
																{	$counters = 0;
																	$total_price_items =0;
																	$total_balance = 0;
																foreach ($creditor_items->result() as $creditoritems){
																	// var_dump($creditor_items->result()); die();
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
																		// $expiry_date = $creditoritems->expiry_date;
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
																		 
																		 $total_balance +=$current_price;
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
																		if($pack_size == 0)
																		{
																			$pack_size = 1;
																		}
																		$result .='<tr class="'.$color.'">'.
																					form_open("inventory/orders/award_order_products/".$product_deductions_id."/".$product_id."/".$order_id, array("class" => "form-horizontal")).'
																		 				<td>'.$counters.'</td>
																		 				<td>'.$product_name1.'</td>
																		 				
																		 				<td>	    
																		                    <input type="number" class="form-control" name="pack_size" placeholder="Park Size"  value="'.$pack_size.'">
																		                </td>
																		                <td>	    
																		                    <input type="number" class="form-control" name="quantity_given" placeholder="Quantity"  value="'.$quantity_given.'">
																		                    <input type="hidden" name="product_deductions_id" value="'.$product_deductions_id.'" >
																		                    <input type="hidden" name="order_item_id" value="'.$item_id.'" >
																		                    <input type="hidden" name="product_name" value="'.$product_name1.'" >
																		                    <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="'.$this->uri->uri_string().'">
																		 				</td>
																		 				<td>'.$pack_size*$quantity_given.'</td>
																		 				<td><input type="text" class="form-control" name="total_amount" placeholder="Amount" value="'.$unit_price1.'">
																		 					<input type="hidden" class="form-control" name="form_id"  value="1">
																		 				</td>
																		 				<td>	    
																		 					'.$vat.'
																		                   
																		 				</td>
																		 				
																		 				<td>'.number_format($current_price,2).'</td>
																		 				<td><button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button>
																		 					
																		 				</td>
																		 				<td><a href="'.site_url("delete-transfer-item/".$product_deductions_id.'/'.$order_id).'" onclick="return confirm(\'Do you want to delete ?\')" title="Delete '.$product_name1.'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a></td>
																		 				'.form_close().'
																		 			</tr>';
																	}
																	$result .= '<tr>
																					<td colspan="7">Total</td>
																					<td>'.number_format($total_balance,2).'</td>
																				</tr>';
																}
														  $result .=' 	
														  <tbody>
														  </table>';
														  $result .= '
							 			 	</div>

							 			 <tr>

										';
				}
									
						
							
                ?>
                <?php
			

			
			
			$result .= '
			';

			echo $result;
		}
	?>

	<div class="row">
		<div class="col-md-12">
			<div class="center-align">
			<?php
            	$order_approval_status = $this->orders_model->get_order_approval_status($order_id);
            	// var_dump($order_approval_status); die();
            	$personnel_id = $this->session->userdata('personnel_id');
				if ($order_approval_status == 0 )
				{
					?>
					<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/finish-transfer-order/<?php echo $order_id;?>" onclick="return confirm('Do you want to close procurement ? ')" >CLOSE PROCUREMENT</a>
					<?php
				}
				else if($order_approval_status == 7 AND $personnel_id == 0)
				{
					?>
					<a class="btn btn-danger btn-sm" href="<?php echo base_url();?>inventory/open-transfer-order/<?php echo $order_id;?>" onclick="return confirm('Do you want to open procurement ? ')" >OPEN PROCUREMENT</a>
					<?php
				}
				?>
            </div>
		</div>
	</div>
    </div>
    <div class="modal fade bs-example-modal-lg" id="add_provider_items" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Item</h4>
                </div>
                <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
                <div class="modal-body">
			        <div class="row">
			        	<div class="col-md-12">
			                <div class="form-group">
			                	<label class="col-lg-3 control-label">Product</label>
			                    <div class="col-lg-8">
			                    	<select class="form-control custom-select" name="product_id" id="product_id" onchange="get_current_stock()">
			                    		<option>SELECT A PRODUCT</option>
			                    		<?php
			                    		if($products_query->num_rows() > 0)
			                    		{
			                    			foreach ($products_query->result() as $key ) {
			                    				# code...
			                    				$product_id = $key->product_id;
			                    				$product_name = $key->product_name;

			                    				echo '<option value="'.$product_id.'">'.$product_name.'</option>';
			                    			}
			                    		}
			                    		?>

			                    	</select>
			                       
			                    </div>
			                </div>
			             </div>
			             <div class="col-md-12" style="margin-top: 20px;display:none">
			              	<div class="col-md-12">
			              		<div class="form-group">
				                	<label class="col-lg-3 control-label">In Stock</label>
				                    <div class="col-lg-8">
				                    	  <input type="number" class="form-control" name="in_stock" id="in-stock" placeholder="">

				                <input type="hidden" class="form-control" name="creditor_id" placeholder="creditor_id" value="<?php echo $creditor_id_value?>">
				                    </div>
				                </div>
				            </div>
				           
			            </div>
			            <div class="col-md-12 center-align" style="margin-top: 20px">
			            		<span id="total_quantity"></span>
			            </div>
			        </div>
                </div>
                <div class="modal-footer">
                	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Supplied Item</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    
                </div>
                <?php echo form_close();?>
            </div>
        </div>
</div>

</section>


<script type="text/javascript">
 $(function() {
       $("#product_id").customselect();
       $("#supplier_id").customselect();
       $("#creditor_id").customselect();
       $("#order_product_id").customselect();

   });
function get_visit_trail(visit_id){

	var myTarget2 = document.getElementById("visit_trail"+visit_id);
	var button = document.getElementById("open_visit"+visit_id);
	var button2 = document.getElementById("close_visit"+visit_id);

	myTarget2.style.display = '';
	button.style.display = 'none';
	button2.style.display = '';
}
function close_visit_trail(visit_id){

	var myTarget2 = document.getElementById("visit_trail"+visit_id);
	var button = document.getElementById("open_visit"+visit_id);
	var button2 = document.getElementById("close_visit"+visit_id);

	myTarget2.style.display = 'none';
	button.style.display = '';
	button2.style.display = 'none';
}
function get_current_stock()
{
    var product_id = document.getElementById("product_id").value;
    var quantity = 0;//document.getElementById("quantity").value;

    var url = "<?php echo site_url();?>inventory/orders/get_stock_quantity/"+product_id;
   // alert(url);
	$.ajax({
	type:'POST',
	url: url,
	data:{product_id: product_id,quantity : quantity},
	dataType: 'text',
	   success:function(data){
	    var data = jQuery.parseJSON(data);
	     var in_stock = data.in_stock;
	     var total_quantity = data.total_quantity;

         document.getElementById("in-stock").value = in_stock;
         $( "#total_quantity" ).html("<h2>"+ total_quantity +" units</h2>");
	   },
	   error: function(xhr, status, error) {
	    alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	   
	   }
	});
	return false;
 }
</script>