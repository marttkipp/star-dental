 <section class="panel">
        <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
            <a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-default">Back to inventory</a>
               
            </div>
          <div class="clearfix"></div>
        </header>        
        <!-- Widget content -->
        <div class="panel-body">
            <div class="padd">
              <div class="center-align">
                <?php
                    $error = $this->session->userdata('error_message');
                    $success = $this->session->userdata('success_message');
                    
                    if(!empty($error))
                    {
                        echo '<div class="alert alert-danger">'.$error.'</div>';
                        $this->session->unset_userdata('error_message');
                    }
                    
                    if(!empty($success))
                    {
                        echo '<div class="alert alert-success">'.$success.'</div>';
                        $this->session->unset_userdata('success_message');
                    }
                ?>
              </div>
              
                <div class="row">
                    <div class="col-md-12">
                    	<div class="table-responsive">
                        
                              <table border="0" class="table table-hover table-condensed">
                                <thead> 
                                    <th>#</th>
                                    <th>Store Name</th>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Ordered By</th>
                                    <th>Order Status</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                <?php 
                                if($query->num_rows() > 0)
								{
									$order_count = 0;
									foreach($query->result() as $orders_query)
									{
										$personnel_first_name = $orders_query->personnel_fname;
										$personnel_surname = $orders_query->personnel_onames;
										$personnel_names = $personnel_first_name.' '.$personnel_surname;
										$order_id = $orders_query->order_id;
										$store_id = $orders_query->store_id;
										$order_number = $orders_query->orders_number;
										$nav_requisition_id = $orders_query->nav_requisition_id;
										$nav_id = $orders_query->nav_id;
										$nav_supplier_id = $orders_query->nav_supplier_id;
										$store_name = $orders_query->store_name;
										$order_date = $orders_query->orders_date;
										$store_parent = $orders_query->store_parent;
										$order_approval_status = $orders_query->orders_approval_status;
										$status = $this->inventory_management_model->get_status($order_approval_status);
										$order_count++;
										?>
                                        <tr>
                                            <td><?php echo $order_count;?></td>
                                            <td><?php echo $store_name;?></td>
                                            <td><?php echo $order_number;?></td>
                                            <td><?php echo date('jS M Y H:i:s',strtotime($order_date));?></td>
                                            <td><?php echo $personnel_names;?></td>
                                            <td><?php echo $status;?></td>
                                            <td><a href="<?php echo site_url().'view-order/'.$order_id;?>" class="btn btn-sm btn-primary ">View Details</a></td>
                                            <?php
											if($order_approval_status==1)
											{
												?>
                                             		<td><a href="<?php echo site_url().'print-order/'.$order_id;?>" target="_blank" class="btn btn-sm btn-warning ">Print Order</a></td>
                                             		<td><a href="<?php echo site_url().'view-lpos/'.$order_id;?>" class="btn btn-sm btn-default ">LPOs</a></td>
												<?php
												
												if(empty($nav_id) || ($nav_id == NULL))
												{
												?>
													<td>
														<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#orders_modal<?php echo $order_id;?>">
															Create Nav Order
														</button>
														
														<div class="modal fade" id="orders_modal<?php echo $order_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																		<h4 class="modal-title" id="myModalLabel">Create New Order</h4>
																	</div>
																	<div class="modal-body">
																		
																		<?php echo form_open('inventory_management/add_navision_order/'.$order_id, array('class' => 'form-horizontal'));?>
																			<div class="form-group" id="supplier_id">
																				<label for="exampleInputEmail1" class="col-lg-5 control-label">Select Supplier: </label>
																				
																				<div class="col-lg-7">
																					<select name="nav_supplier_id" class="form-control custom-select">
																						<?php echo $suppliers;?>
																					</select>
																				</div>
																			</div>
																			<div class="form-group">
																				<div class="col-sm-offset-2 col-sm-10">
																					<button type="submit" class="btn btn-primary">Update Nav</button>
																				</div>
																			</div>
																		<?php echo form_close();?>
																   

																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																	</div>
																</div>
															</div>
														</div>
									
													</td>
													
                                                <?php
												}
												
												else
												{
												?>
                                             		<td><a href="<?php echo site_url().'inventory_management/update_navision_order/'.$order_id.'/'.$nav_supplier_id;?>" class="btn btn-sm btn-danger ">Update Nav Order</a></td>
												<?php
												}
												
												if(empty($nav_requisition_id) || ($nav_requisition_id == NULL))
												{
													?>
														
													<td>
														<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#requests_modal<?php echo $order_id;?>">
															Create Nav Requisition
														</button>
														
														<div class="modal fade" id="requests_modal<?php echo $order_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
															<div class="modal-dialog" role="document">
																<div class="modal-content">
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																		<h4 class="modal-title" id="myModalLabel">Create New Order</h4>
																	</div>
																	<div class="modal-body">
																		
																		<?php echo form_open('inventory_management/add_navision_request/'.$order_id, array('class' => 'form-horizontal'));?>
																			<div class="form-group" id="supplier_id">
																				<label for="exampleInputEmail1" class="col-lg-5 control-label">Select Supplier: </label>
																				
																				<div class="col-lg-7">
																					<select name="nav_supplier_id" class="form-control custom-select">
																						<?php echo $suppliers;?>
																					</select>
																				</div>
																			</div>
																			<div class="form-group">
																				<div class="col-sm-offset-2 col-sm-10">
																					<button type="submit" class="btn btn-primary">Update Nav</button>
																				</div>
																			</div>
																		<?php echo form_close();?>
																   

																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																	</div>
																</div>
															</div>
														</div>
									
													</td>
													<?php
												}
												
												else
												{
												?>
													<td><a href="<?php echo site_url().'inventory_management/update_navision_request/'.$order_id.'/'.$nav_supplier_id;?>" class="btn btn-sm btn-info ">Update Nav Requisition</a></td>
												<?php
												}
											}
											else
											{
												?>
                                                 <td><a href="<?php echo site_url().'approve-order/'.$order_id;?>" class="btn btn-sm btn-success ">Approve Order</a></td>
                                                <?php
											}
											?>
										</tr>
                                        <?php
									}
								}
								?>
                                </tbody>
                            </table>
                    	</div>
                    </div>
                </div>
            
            </div>
        </div>
            
        <div class="widget-foot">
        <?php
        if(isset($links)){echo $links;}
        ?>
        </div>
</section>

<script type="text/javascript">

$(function() {
    $(".custom-select").customselect();
});
    function update_quantity(product_deductions_id,store_id)
    {
      
       //var product_deductions_id = $(this).attr('href');
       var quantity = $('#quantity_given'+product_deductions_id).val();
       var url = "<?php echo base_url();?>inventory/award-store-order/"+product_deductions_id+'/'+quantity;
  
        $.ajax({
           type:'POST',
           url: url,
           data:{quantity: quantity},
           cache:false,
           contentType: false,
           processData: false,
           dataType: 'json',
           success:function(data){
            
            window.alert(data.result);
            window.location.href = "<?php echo base_url();?>inventory/product-deductions";
           },
           error: function(xhr, status, error) {
            alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
           
           }
        });
        return false;
     }
</script>