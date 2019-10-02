<?php


//var_dump($query);die();
?>
<div class="row">
	<div class="col-md-12">
    	<section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                <h2 class="panel-title">Search Products</h2>
            </header>
            <div class="panel-body">
            	<?php echo form_open('inventory/search-request-product/'.$orders_id.'/'.$store_parent, array('class' => 'form-horizontal', 'id' => 'search-product'));?>
                	<div class="row">
                    	<div class="col-md-6">
                        	<div class="form-group">
                                <label for="exampleInputEmail1" class="col-lg-5 control-label">Select Item: </label>
                                
                                <div class="col-lg-7">
                                    <select name="product_id" class="form-control custom-select">
                                        <?php echo $all_products;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                    	<div class="col-md-6">
                        	<div class="row">
                            	<div class="col-md-6">
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </div>
                                </div>
                            	<div class="col-md-6">
                                    <?php
                                    $search = $this->session->userdata('product_request_search');
									
									if(!empty($search))
									{
										?>
                                        <a href="<?php echo site_url().'inventory/close-request-search/'.$orders_id.'/'.$store_parent;?>" class="btn btn-warning close-request-search">Close Search</a>
                                        <?php
									}
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo form_close();?>
            </div>
        </section>
    </div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-hover table-bordered ">
	         <tbody>
	         	<?php
	         	$personnel_query = $this->personnel_model->get_all_personnel();//var_dump($personnel_query);die();
				//var_dump($query);die();
	         	if($query->num_rows() > 0)
	         	{
							
					if($store_parent == 0)
					{
						?>
						<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
							<thead> 
								<th>#</th>
								 <th class="table-sortable:default table-sortable" title="Click to sort">Store Name</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Item Name</th>
								<th>Status</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">QTY Ordered</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Purchase QTY</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Pack Size</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Total</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Expiry Date</th>
								<th>Action</th>
							</thead>
							<tbody>
						<?php
					}
					
					else
					{
						?>
						<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
							<thead> 
								<th>#</th>
								 <th class="table-sortable:default table-sortable" title="Click to sort">Store Name</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">Item Name</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">QTY Ordered</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">QTY Given</th>
								<th>Status</th>
								<th class="table-sortable:default table-sortable" title="Click to sort">QTY Received</th>
								<th>Action</th>
							</thead>
							<tbody>
						<?php
					}
						$counter = $page;
						foreach ($query->result() as $key)
						{
							$product_name = $key->product_name;
							$product_id = $key->product_id;
							$store_name = $key->store_name;
							$store_id = $key->store_id;
							$search_date = $key->search_date;
							$quantity_requested = $key->quantity_requested;
							$quantity_received = $key->quantity_received;
							$quantity_given = $key->quantity_given;
							$product_deductions_id = $key->product_deductions_id;
							$product_deductions_status = $key->product_deductions_status;
							if($product_deductions_status == 0)
							{
								$status = '<span class="label label-warning">Not Awarded</span>';
							}
							//create activated status db2_field_display_size(stmt, column)
							else if($product_deductions_status == 1)
							{
								$status = '<span class="label label-info">Awarded</span>';
							}
							else if($product_deductions_status == 2)
							{
								$status = '<span class="label label-success">Received</span>';
							}
							$counter++;
						
							if($store_parent == 0)
							{
								$purchase_quantity = $key->purchase_quantity;
								$purchase_pack_size = $key->purchase_pack_size;
								$expiry_date = $key->expiry_date;
								$purchase_id = $key->purchase_id;
								$quantity_given = $purchase_quantity * $purchase_pack_size;
								
								?>
									<tr>
										<td><?php echo $counter;?></td>
										<td><?php echo $store_name?></td>
										<td><?php echo $product_name?></td>
										<td><?php echo $status;?></td>
										<td><?php echo number_format($quantity_requested);?></td>
										<td><input type="text" class="form-control" id="purchase_quantity<?php echo $product_deductions_id;?>" name="purchase_quantity<?php echo $product_deductions_id;?>" size="1" value="<?php echo $purchase_quantity;?>"></td>
										<td><input type="text" class="form-control" id="purchase_pack_size<?php echo $product_deductions_id;?>" name="purchase_pack_size<?php echo $product_deductions_id;?>" size="1" value="<?php echo $purchase_pack_size;?>"></td>
										<td id="quantity_given<?php echo $product_deductions_id;?>"><?php echo number_format($quantity_given);?></td>
										<td>
											<div class="input-group">
												<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</span>
												<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" id="expiry_date<?php echo $product_deductions_id;?>" name="expiry_date<?php echo $product_deductions_id;?>" placeholder="Expiry Date" value="<?php echo $expiry_date;?>">
											</div>
										</td>
										<td><a id="update_action_point_form"  onclick="receive_purchase(<?php echo $product_deductions_id;?>,<?php echo $product_id;?>,<?php echo $store_parent;?>)" class="btn btn-sm btn-warning fa fa-pencil"> Receive</a></td>
										<td></td>
									</tr>
								<?php
							}
							
							else
							{
								?>
									<tr>
										<td><?php echo $counter;?></td>
										<td><?php echo $store_name?></td>
										<td><?php echo $product_name?></td>
										<td><?php echo number_format($quantity_requested);?></td>
										<td id="quantity_given<?php echo $product_deductions_id;?>"><?php echo number_format($quantity_given);?></td>
										<td><?php echo $status;?></td>
										<td><input type="text" class="form-control" id="quantity_received<?php echo $product_deductions_id;?>" name="quantity_received<?php echo $product_deductions_id;?>" size="1" value="<?php echo $quantity_received;?>"></td>
										<td><a id="update_action_point_form"  onclick="receive_quantity(<?php echo $product_deductions_id;?>,<?php echo $store_id;?>,<?php echo $product_id;?>)" class="btn btn-sm btn-warning fa fa-pencil"> Receive</a></td>
										<td></td>
									</tr>
								<?php
							}
						}
					}
				?>
			</tbody>
		</table>
					
	</div>
</div>
<div class="widget-foot">
                                
	<?php if(isset($links)){echo $links;}?>

    <div class="clearfix"></div> 

</div>
        
