<?php
$order_approval_status = $this->orders_model->get_order_approval_status($order_id);



$check_level_approval = $this->orders_model->check_assigned_next_approval($order_approval_status);
// $order_approval_status = $order_approval_status - 1;
if($order_approval_status == 0 AND $check_level_approval == TRUE)
{
?>	
	<section class="panel">
	    <header class="panel-heading">
	        <h2 class="panel-title pull-left">Add Order Items for <?php echo $store_name;?></h2>
	        <div class="widget-icons pull-right">
	            	<a class='btn btn-sm btn-info ' data-toggle='modal' data-target='#add_provider' ><i class="fa fa-plus"></i> Add Provider</a>
	            	<a href="<?php echo base_url();?>procurement/general-orders" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> Back to Orders</a>
	          </div>
	          <div class="clearfix"></div>
	    </header>
	    <div class="panel-body">
	    	<?php
				$success = $this->session->userdata('success_message');
				$error = $this->session->userdata('error_message');
				
				if(!empty($success))
				{
					echo '
						<div class="alert alert-success">'.$success.'</div>
					';
					
					$this->session->unset_userdata('success_message');
				}
				
				if(!empty($error))
				{
					echo '
						<div class="alert alert-danger">'.$error.'</div>
					';
					
					$this->session->unset_userdata('error_message');
				}
				
			?>
			
				    	<?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
				        <div class="row">
				        	<div class="col-md-4">
				                <div class="form-group">
				                	<label class="col-lg-3 control-label">Product</label>
				                    <div class="col-lg-8">
				                    	<select class="form-control custom-select" name="product_id" id="product_id">
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
				              <div class="col-md-4">
					                <div class="form-group">
					                	<label class="col-lg-4 control-label">QTY in Stock</label>
					                    <div class="col-lg-8">
					                    	 <input type="number" class="form-control" name="in_stock" placeholder="Quantity">
					                    </div>
					                </div>
					           </div>
				              <div class="col-md-4">
					                <div class="form-group">
					                	<label class="col-lg-4 control-label">Request Quantity</label>
					                    <div class="col-lg-8">
					                    	 <input type="number" class="form-control" name="quantity" placeholder="Quantity">
					                    </div>
					                </div>
					            </div>
				            </div>
				            <div class="row" style="margin-top: 10px;">
					              <div class="center-align">
					            	<button class="btn btn-primary btn-sm" type="submit">Add Order Item</button>
					            </div>
				        	</div>
				        
				        <?php echo form_close();?>
				    
	  	</div>
	</section>
<?php
} 

else if($order_approval_status == 2 || $order_approval_status == 3)
{
// var_dump($check_level_approval); die();
	if($order_approval_status == 2 AND $check_level_approval == TRUE)
	{
	?>	
		<section class="panel">
		    <header class="panel-heading">
		        <h2 class="panel-title pull-left">Request for Quotation for <?php echo $store_name;?> </h2>
		        <div class="widget-icons pull-right">
		            	<a class="btn btn-success btn-sm " data-toggle='modal' data-target='#add_provider'> <i class="fa fa-plus"></i> Add Supplier </a>
		          </div>
		          <div class="clearfix"></div>
		    </header>
		    <div class="panel-body">
		    	<?php
					$success = $this->session->userdata('success_message');
					$error = $this->session->userdata('error_message');
					
					if(!empty($success))
					{
						echo '
							<div class="alert alert-success">'.$success.'</div>
						';
						
						$this->session->unset_userdata('success_message');
					}
					
					if(!empty($error))
					{
						echo '
							<div class="alert alert-danger">'.$error.'</div>
						';
						
						$this->session->unset_userdata('error_message');
					}
					
				?>
				
		    	<?php echo form_open('inventory/submit-supplier/'.$order_id.'/'.$order_number, array("class" => "form-horizontal", "role" => "form"));?>
		        <div class="row">
		        	<div class="col-md-12 center-align">
		                <div class="form-group">
		                	<label class="col-lg-4 control-label">Supplier Name</label>
		                    <div class="col-lg-8">
		                    	<select class="form-control custom-select" name="supplier_id" id="supplier_id">
		                    		<option>SELECT A SUPPLIER</option>
		                    		<?php
		                    		if($suppliers_query->num_rows() > 0)
		                    		{
		                    			foreach ($suppliers_query->result() as $key_supplier_items ) {
		                    				# code...
		                    				$creditor_id = $key_supplier_items->creditor_id;
		                    				$creditor_name = $key_supplier_items->creditor_name;

		                    				echo '<option value="'.$creditor_id.'">'.$creditor_name.'</option>';
		                    			}
		                    		}
		                    		?>

		                    	</select>
		                       
		                    </div>
		                </div>
		              </div>
		            </div>
		            <br>
		            <div class="row">
			              <div class="center-align">
			            	<button class="btn btn-primary btn-sm" type="submit">Request Supplier for quotation</button>
			            </div>
		        	</div>
		        
		        <?php echo form_close();?>
					    
		        <div class="modal fade bs-example-modal-lg" id="add_provider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			        <div class="modal-dialog modal-lg" role="document">
			            <div class="modal-content">
			                <div class="modal-header">
			                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			                    <h4 class="modal-title" id="myModalLabel">Add New Suppliers</h4>
			                </div>
			                 <?php echo form_open("accounts/creditors/add_creditor", array("class" => "form-horizontal"));?>
			                <div class="modal-body">
			                	<div class="row">
									<div class="col-md-6">
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Creditor Name: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_name" placeholder="Creditor Name" >
								            </div>
								        </div>
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Email: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_email" placeholder="Email" >
								            </div>
								        </div>
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Phone: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_phone" placeholder="Phone">
								            </div>
								        </div>
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Opening Balance: </label>
								            
								            <div class="col-lg-7">
								                <input type="text" class="form-control" name="opening_balance" placeholder="Opening Balance" >
								            </div>
								        </div>
								        <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
								        <input type="hidden" class="form-control" name="creditor_type_id" placeholder="" autocomplete="off" value="1">
								        <div class="form-group">
											<label class="col-lg-5 control-label">Prepayment ?</label>
											<div class="col-lg-3">
												<div class="radio">
													<label>
													<input id="optionsRadios5" type="radio" value="1" name="debit_id">
													Yes
													</label>
												</div>
											</div>
											<div class="col-lg-3">
												<div class="radio">
													<label>
													<input id="optionsRadios6" type="radio" value="2" name="debit_id" checked="checked">
													No
													</label>
												</div>
											</div>
										</div>
								        
								        
									</div>
								    
								    <div class="col-md-6">
								        
								   
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Contact First Name: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_contact_person_name" placeholder="Contact First Name" >
								            </div>
								        </div>
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Contact Other Names: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_contact_person_onames" placeholder="Contact Other Names" >
								            </div>
								        </div>
								        
								        <div class="form-group">
								            <label class="col-lg-5 control-label">Contact Phone 1: </label>
								            
								            <div class="col-lg-7">
								            	<input type="text" class="form-control" name="creditor_phone" placeholder="Contact Phone 1" >
								            </div>
								        </div>
								        
								       
								        
								    </div>
								</div>

			                </div>
			                <div class="modal-footer">
			                	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Supplier</button>
			                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			                    
			                </div>
			                <?php echo form_close();?>
			            </div>
			        </div>
			</div>
		  	</div>
		</section>
	<?php
	}
	


	?>
		<section class="panel">
		    <header class="panel-heading">
		        <h2 class="panel-title pull-left">Order Suppliers</h2>
		        <div class="widget-icons pull-right">
		          </div>
		          <div class="clearfix"></div>
		    </header>
		    <div class="panel-body">
		    	<?php
		    	$result_suppliers = '';

        		if($order_suppliers->num_rows() > 0)
        		{
        			
        			$result_suppliers .= 
        				'<div class="row">
								<div class="col-md-12">
									<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
									  <thead>
										<tr>
										  <th class="table-sortable:default table-sortable" title="Click to sort">#</th>
										  <th class="table-sortable:default table-sortable" title="Click to sort">Supplier Name</th>
										  <th class="table-sortable:default table-sortable" title="Click to sort">Contact Person</th>
										  <th class="table-sortable:default table-sortable" title="Click to sort">Supplier Phone</th>
										  <th>Supplier Status</th>
										  <th colspan="2">Actions</th>
										 
										</tr>
									  </thead>
									  <tbody>';
									$counter = 0;

				        			foreach ($order_suppliers->result() as $key_supplier) {
				        				# code...
				        				$creditor_id = $key_supplier->creditor_id;
				        				$supplier_order_id = $key_supplier->supplier_order_id;
				        				$creditor_name = $key_supplier->creditor_name;
				        				$creditor_phone = $key_supplier->creditor_phone;
				        				$creditor_contact_person = $key_supplier->creditor_contact_person_name;
				        				$supplier_order_status = $key_supplier->supplier_order_status;

				        				if($supplier_order_status == 0)
				        				{
				        					$status = '<span class="label label-info">On Review</span>';
				        				}
				        				else if($supplier_order_status == 1)
				        				{
				        					$status = '<span class="label label-success">Awarded</span>';
				        				}
				        				else
				        				{
				        					$status = '<span class="label label-info">On Review</span>';
				        				}
				        				$counter++;
				        				$result_suppliers .='<tr >
				        										<td>'.$counter.'</td>
				        										<td>'.$creditor_name.'</td>
				        										<td>'.$creditor_contact_person.'</td>
				        										<td>'.$creditor_phone.'</td>
				        										<td>'.$status.'</td>
				        										<td>
																	<a  class="btn btn-sm btn-primary fa fa-folder" id="open_visit'.$supplier_order_id.'" onclick="get_visit_trail('.$supplier_order_id.');"> Open Details</a>
																	<a  class="btn btn-sm btn-info fa fa-folder" id="close_visit'.$supplier_order_id.'" style="display:none;" onclick="close_visit_trail('.$supplier_order_id.');"> Close Detail</a></td>
																</td>
				        										<td><a href=""  class="btn btn-danger btn-sm">Remove Supplier</a></td>

				        									</tr>';
				        				$v_data['order_id'] = $order_id;
				        				$v_data['supplier_order_id'] = $supplier_order_id;
				        				$v_data['supplier_id'] = $creditor_id;
				        				$v_data['order_number'] = $order_number;
				        				$result_suppliers .='
				        								<tr id="visit_trail'.$supplier_order_id.'" style="display:none;">
				        									<td colspan="7">'.$this->load->view("views/order_supplier", $v_data, TRUE).'</td>
				        								</tr>';
				        				
				        			}

				        			$result_suppliers .= '
				        								</tbody>
				        							</table>
				        						</div>
				        					</div>';

        			echo $result_suppliers;
        		}


        		?>
		    </div>
		 </section>
	<?php
		
 }

?>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-left">Order Items for <?php echo $store_name;?> Order <?php echo $order_number;?></h2>
         <div class="widget-icons pull-right">
            	<a href="<?php echo base_url();?>procurement/general-orders" class="btn btn-primary btn-sm">Back to Orders</a>
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
    	<div class="row">
			<div class="col-md-12">
				<div class="center-align">
					<?php
					$order_approval_status = $this->orders_model->get_order_approval_status($order_id);
					$rank = 2;
					$next_order_status = $order_approval_status+1;
						
					// check if assgned the next level 
					$check_level_approval = $this->orders_model->check_assigned_next_approval($order_approval_status);

					if($order_approval_status == 0)
					{
						?>
							<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for approval</a>
						<?php
					}

					else if($order_approval_status == 1 AND $check_level_approval == TRUE )
					{
						?>
							<a class="btn btn-warning btn-sm" href="<?php echo base_url();?>inventory/send-for-correction/<?php echo $order_id;?>" onclick="return confirm('Do you want to send order for review / correction?');">Send order for correction</a>
		            		<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for next approval</a>
						<?php
					}
					else if($order_approval_status == 2 AND $check_level_approval == TRUE )
					{
						?>
							<a class="btn btn-warning btn-sm" href="<?php echo base_url();?>inventory/send-for-correction/<?php echo $order_id;?>" onclick="return confirm('Do you want to send order for review / correction?');">Send order for correction</a>
		            		<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for approval</a>
						<?php
					}
					
					else if(($order_approval_status == 3 AND $check_level_approval == TRUE ))
					{
						?>
							<a class="btn btn-warning btn-sm" href="<?php echo base_url();?>inventory/send-for-correction/<?php echo $order_id;?>" onclick="return confirm('Do you want to send order for review / correction?');">Send order for correction</a>
		            		<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for approval</a>
						<?php
					}
					else if($order_approval_status == 4 AND $check_level_approval == TRUE )
					{
						?>
							<!-- <a class="btn btn-warning btn-sm fa fa-print" href="<?php echo base_url();?>inventory/print-supplier-quotation/<?php echo $order_id;?>" onclick="return confirm('Do you want to print supplier qoutation?');"> Print Supplier Qoutation</a> -->
							
							<a class="btn btn-default btn-sm fa fa-print" data-toggle='modal' data-target='#add_provider_items' > Add Supplier LPO ITEMS </a>
		            		<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for approval</a>
						<?php
					}
					else if($order_approval_status == 5 AND $check_level_approval == TRUE )
					{
						?>
		            		<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to approve the LPO?');">Approve LPO</a>
						<?php
					}
					else if($order_approval_status == 6 AND $check_level_approval == TRUE )
					{
						?>
							<!-- <a class="btn btn-primary btn-sm fa fa-print" href="<?php echo base_url();?>inventory/generate-lpo/<?php echo $order_id;?>" target="_blank" > Generate LPO </a> -->
		            		<!-- <a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/send-for-approval/<?php echo $order_id;?>/<?php echo $next_order_status;?>" onclick="return confirm('Do you want to send order for next approval?');">Send Order for approval</a> -->
						<?php
					}

					else
					{
						// echo '<div class="alert alert-info">Your Order is waiting for the next approval</div>';
					}
				
					?>
	            	
	            </div>
			</div>
		</div>
		<br>
    	<?php
    		$result ='';
			if($order_item_query->num_rows() > 0)
			{
				$col = '';
				$message = '';
				
				if($order_approval_status == 0)
				{
					$col = '<th colspan="3">Actions</th>';

				}
				else if($order_approval_status == 4)
				{
					$col .= '
							<th>Unit Price (KES)</th>
							<th>Total Price (KES) </th>
							<th colspan="1">Actions</th>';

				}
				else if($order_approval_status == 5 OR $order_approval_status == 6)
				{
					$col .= '
							<th>Unit Price (KES)</th>
							<th>Total Price (KES) </th>';

				}

				else
				{
					$col = '';
				}
				if($order_approval_status < 4)
				{
				$result .= 
				'
				<div class="row">
					<div class="col-md-12">
						<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
						  <thead>
							<tr>
							  <th class="table-sortable:default table-sortable" title="Click to sort">#</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Item Name</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">In Stock</th>
							  <th class="table-sortable:default table-sortable" title="Click to sort">Quantity</th>
							  '.$col.'
							</tr>
						  </thead>
						  <tbody>
						';
				}
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
								if(($order_approval_status == 0 OR $order_approval_status == 1 OR $order_approval_status == 2) AND $check_level_approval == TRUE )
								{
				                    $result .= ' '.form_open('inventory/update-order-item/'.$order_id.'/'.$order_number.'/'.$order_item_id).'
												<tr>
													<td>'.$count.'</td>
													<td>'.$product_name.'</td>
													<td><input type="text" class="form-control" name="in_stock" value="'.$in_stock.'"></td>
													<td><input type="text" class="form-control" name="quantity" value="'.$order_item_quantity.'"></td>
													<td><button class="btn btn-success btn-sm" type="submit"><i class="fa fa-pencil"></i> Edit Order</button></td>
													<td><a href="'.site_url("inventory/delete-order-item/".$order_item_id).'" onclick="return confirm("Do you want to delete '.$product_name.'?")" title="Delete '.$product_name.' class="btn btn-danger btn-sm">Delete</a></td>
												</tr>
												'.form_close().'
												';
								}
							
								else if($order_approval_status == 3 AND $check_level_approval == TRUE)
								{
									 $result .= '
												<tr>
													<td>'.$count.'</td>
													<td>'.$product_name.'</td>
													<td><input type="text" class="form-control" name="in_stock" value="'.$in_stock.'" readonly></td>
													<td><input type="text" class="form-control" name="quantity" value="'.$order_item_quantity.'" readonly></td>
												</tr>
												';
								}
						}
								if($order_approval_status == 4 || $order_approval_status == 5)
								{
									 $total_price_items = 0;

									
									 $result .= ' 
									 			 <tr>
									 			 	<div class="col-md-12">';
								 			 			$supplier_order_details = $this->orders_model->get_order_suppliers($order_id);


														if($supplier_order_details->num_rows() > 0)
														{
															foreach ($supplier_order_details->result() as $key_supplier){
																$creditor_name = $key_supplier->creditor_name;
																$creditor_contact_person_name = $key_supplier->creditor_contact_person_name;
																$creditor_phone = $key_supplier->creditor_phone;
																$creditor_id = $key_supplier->creditor_id;
																$creditor_email = $key_supplier->creditor_email;
																$items_rs = $this->orders_model->get_order_items_supplier($order_id,$creditor_id);

																// var_dump($items_rs); die();
									 			 		 $result .= '<div class="col-md-6">
									 			 		 				<h4 style="margin-bottom:10px;">'.$creditor_name.' <a class="btn btn-primary btn-sm fa fa-print pull-right" taget="_blank" href="'.base_url().'inventory/generate-lpo/'.$order_id.'/'.$creditor_id.'" onclick="return confirm(\'Do you want to view the LPO?\');"> View LPO </a></h4> 
									 			 		 				<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
																		  <thead>
																			<tr>
																			  <th >#</th>
																			  <th >Item Name</th>
																			  <th >Quantity</th>
																			  <th >Unit Price Ksh</th>
																			  <th >Total Ksh</th>
																			  <th >Action</th>
																			
																			</tr>
																		  </thead>
																		  <tbody>';
																		  		if($items_rs->num_rows() > 0)
																				{	$counter = 0;
																				foreach ($items_rs->result() as $key_items){
																					    $order_supplier_id = $key_items->order_supplier_id;
																						$product_idd = $key_items->product_id;
																						$product_name1 = $key_items->product_name;
																						$quantity1 = $key_items->supplying;
																						$unit_price1 = $key_items->single_price;
																						 $total_price_items = $total_price_items + ($quantity1 * $unit_price1);
																						$counter++;
																						$result .='<tr>
																						 				<td>'.$counter.'</td>
																						 				<td>'.$product_name1.'</td>
																						 				<td>'.$quantity1.'</td>
																						 				<td>'.$unit_price1.'</td>
																						 				<td>'.$unit_price1 * $quantity1.'</td>
																						 				<td><a href="'.site_url().'remove-item/'.$order_id.'/'.$order_number.'/'.$order_supplier_id.'" class="btn btn-sm btn-danger"><i class="fa fa-trash" onclick="return confirm(\'Do you want to remove this item ?\')"></i></a></td>

																						 			</tr>';
																					}
																				}
																		  $result .=' 	
																		  <tbody>
																		  </table>

													 			 	 </div>';
									 			 					}
									 			 			}	
									 			 		 $result .= '
									 			 	</div>

									 			 <tr>

												';
								}
								
								if($order_approval_status == 6)
								{
									 $result .= ' 
									 			 <tr>
									 			 	<div class="col-md-12">';
									 			 	$creditor_items = $this->orders_model->get_supplied_list($order_id);
									 			 	$result .= '
							 			 		 				<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
																  <thead>
																	<tr>
																	  <th >#</th>
																	  <th >Creditor Name</th>
																	  <th >Item Name</th>
																	  <th >QTY</th>
																	  <th >BU.P</th>
																	  <th >Total</th>
																	  <th >Markup %</th>
																	  <th >SU.P</th>
																	  <th >Invoice</th>
																	  <th >Packs</th>
																	  <th >Pack Size</th>
																	  <th >Total</th>
																	  <th >Expiry Date</th>
																	  <th >Action</th>
																	
																	</tr>
																  </thead>
																  <tbody>';
																  		if($creditor_items->num_rows() > 0)
																		{	$counters = 0;
																			$total_price_items =0;
																		foreach ($creditor_items->result() as $creditoritems){

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

																				$pack_size = $creditoritems->pack_size;

																				$amount = $quantity_received * $pack_size;
																				$expiry_date = $creditoritems->expiry_date;
																				 $total_price_items = $total_price_items + ($quantity1 * $unit_price1);
																				$counters++;

																				if($quantity1 == $amount)
																				{
																					$color = 'success';
																				}
																				else
																				{
																					$color = 'default';
																				}

																				$result .='<tr class="'.$color.'">'.
																							form_open("update-charges", array("class" => "form-horizontal")).'
																				 				<td>'.$counters.'</td>
																				 				<td>'.$creditor_name.'</td>
																				 				<td>'.$product_name1.'</td>
																				 				<td>'.$quantity1.'</td>
																				 				<td>'.$unit_price1.'</td>
																				 				<td>'.number_format($unit_price1 * $quantity1,2).'</td>
																				 				<td>	    
																				 					<input type="text" class="form-control" name="mark_up" placeholder="mark up" value="'.$mark_up.'">
																				                   
																				 				</td>
																				 				<td>'.number_format($selling_unit_price,2).'</td>

																				 				<td>	    
																				                     <input type="text" class="form-control" name="invoice_number" placeholder="Invoice Number" value="'.$invoice_number.'">
																				 				</td>
																				 				<td>	    
																				                    <input type="number" class="form-control" name="quantity_received" placeholder="Parks"  value="'.$quantity_received.'">
																				                    <input type="hidden" name="order_supplier_id" value="'.$order_supplier_id.'" >
																				                     <input type="hidden" name="buying_unit_price" value="'.$unit_price1.'" >
																				                    <input type="hidden" name="product_name" value="'.$product_name1.'" >
																				                    <input type="hidden" name="creditor_id" value="'.$creditor_id.'" >
																				                    <input type="hidden" name="total_amount" value="'.$unit_price1 * $quantity1.'" >
																				                    <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="'.$this->uri->uri_string().'">
																				 				</td>
																				 				<td>	    
																				                    <input type="number" class="form-control" name="pack_size" placeholder="Park Size"  value="'.$pack_size.'">
																				                </td>
																				                <td>	    
																				                   '.$quantity_received*$pack_size.'
																				                </td>
																				 				<td>
																		                            <div class="input-group">
																		                                <span class="input-group-addon">
																		                                    <i class="fa fa-calendar"></i>
																		                                </span>
																		                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="expiry_date" placeholder="Date from" value="'.$expiry_date.'">
																		                            </div>
																		                        </td>
																				 				<td><button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></td>
																				 				'.form_close().'
																				 			</tr>';
																			}
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
						}

						if($order_approval_status == 7)
								{
									 $result .= ' 
									 			 <tr>
									 			 	<div class="col-md-12">';
									 			 	$creditor_items = $this->orders_model->get_supplied_list($order_id);
									 			 	$result .= '
							 			 		 				<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
																  <thead>
																	<tr>
																	  <th >#</th>
																	  <th >Creditor Name</th>
																	  <th >Item Name</th>
																	  <th >QTY</th>
																	  <th >BU.P</th>
																	  <th >Total</th>
																	  <th >Markup %</th>
																	  <th >SU.P</th>
																	  <th >Invoice</th>
																	  <th >Packs</th>
																	  <th >Pack Size</th>
																	  <th >Total</th>
																	  <th >Expiry Date</th>
																	
																	</tr>
																  </thead>
																  <tbody>';
																  		if($creditor_items->num_rows() > 0)
																		{	$counters = 0;
																			$total_price_items =0;
																		foreach ($creditor_items->result() as $creditoritems){

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

																				$pack_size = $creditoritems->pack_size;

																				$amount = $quantity_received * $pack_size;
																				$expiry_date = $creditoritems->expiry_date;
																				 $total_price_items = $total_price_items + ($quantity1 * $unit_price1);
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
																				 				<td>'.$creditor_name.'</td>
																				 				<td>'.$product_name1.'</td>
																				 				<td>'.$quantity1.'</td>
																				 				<td>'.$unit_price1.'</td>
																				 				<td>'.number_format($unit_price1 * $quantity1,2).'</td>
																				 				<td>	    
																				 					'.$mark_up.'
																				                   
																				 				</td>
																				 				<td>'.number_format($selling_unit_price,2).'</td>

																				 				<td>	    
																				                    '.$invoice_number.'
																				 				</td>
																				 				<td>	    
																				                    '.$quantity_received.'
																				                   
																				 				</td>
																				 				<td>	    
																				                    '.$pack_size.'
																				                </td>
																				                <td>	    
																				                   '.$quantity_received*$pack_size.'
																				                </td>
																				 				<td>
																		                            '.$expiry_date.'
																		                        </td>
																				 				
																				 			</tr>';
																			}
																		}
																  $result .=' 	
																  <tbody>
																  </table>';
																  $result .= '
									 			 	</div>

									 			 <tr>

												';
									
						
							
		                   
						}
						
						$result .= '
							</tbody>
						</table>
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
							if($order_approval_status > 0 AND $order_approval_status <6)
							{
								echo '
									<div class="alert alert-info">Your Order is being processed</div>
								';
							}
							else if ($order_approval_status == 6 )
							{
								?>
								<a class="btn btn-success btn-sm" href="<?php echo base_url();?>inventory/finish-order/<?php echo $order_id;?>" onclick="return confirm('Do you want to close procurement ? ')" >CLOSE PROCUREMENT</a>
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
                    <h4 class="modal-title" id="myModalLabel">Add New Provider</h4>
                </div>
                 <?php echo form_open("inventory/orders/add_supplier_items", array("class" => "form-horizontal"));?>
                <div class="modal-body">
                	<div class="row">
                    	<div class='col-md-12'>
                    		<div class='col-md-6'>
	                          	<div class="form-group">
									<label class="col-lg-2 control-label">Supplier: </label>
									<div class="col-lg-10">
										<select class="form-control custom-select" name="creditor_id" id="creditor_id">
											<option>SELECT A SUPPLIER</option>
											<?php 
												$supplier_order_details = $this->orders_model->get_order_suppliers($order_id);

												if($supplier_order_details->num_rows() > 0)
												{
													foreach ($supplier_order_details->result() as $key_supplier) {
														# code...

														// $order_number = $key_supplier->order_number;
														$creditor_name = $key_supplier->creditor_name;
														$creditor_contact_person_name = $key_supplier->creditor_contact_person_name;
														$creditor_phone = $key_supplier->creditor_phone;
														$creditor_id = $key_supplier->creditor_id;
														$creditor_email = $key_supplier->creditor_email;
														echo "<option value='".$creditor_id."'>".$creditor_name."</option>";
													}
												}
											?>
											
										</select>
										
									</div>
								</div>

								<input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
								<input type="hidden" class="form-control" name="order_id" placeholder="" autocomplete="off" value="<?php echo $order_id?>">

	                          	<div class="form-group">
									<label class="col-lg-2 control-label">Product: </label>
								  
									<div class="col-lg-10">
										<select class="form-control custom-select" name="order_product_id" id="order_product_id">
				                    		<option>SELECT A PRODUCT</option>
				                    		<?php
				                    		$order_products = $this->orders_model->get_order_items($order_id);
				                    		if($order_products->num_rows() > 0)
				                    		{
				                    			foreach ($order_products->result() as $key_products ) {
				                    				# code...
				                    				$order_item_quantity = $key_products->order_item_quantity;
				                    				$order_item_id = $key_products->order_item_id;
													$in_stock = $key_products->in_stock;
													$order_item_id = $key_products->order_item_id;
													$supplier_unit_price = $key_products->supplier_unit_price;
													$product_id = $key_products->product_id;
													$product_name = $key_products->product_name;

				                    				echo '<option value="'.$order_item_id.'">'.$product_name.' QTY Requested  '.$order_item_quantity.'</option>';
				                    			}
				                    		}
				                    		?>

				                    	</select>
									</div>
								</div>
							</div>
							<div class='col-md-6'>
								<div class="form-group">
									<label class="col-lg-2 control-label">QTY: </label>
								  
									<div class="col-lg-10">
										<input type="number" class="form-control" name="quantity_to_deliver" placeholder="" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-2 control-label">U.Price: </label>
								  
									<div class="col-lg-10">
										<input type="text" class="form-control" name="unit_price_supplier" placeholder="" autocomplete="off">
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Supplier Items</button>
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
</script>