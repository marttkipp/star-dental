 <section class="panel">
        <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
            <a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-default">Back to inventory</a>
                <!-- <a href="#user" class="btn btn-primary  btn-sm" data-toggle="modal">View Order Details</a>
              
                <div id="user" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title"> <span class="bold">Order Number : </span>dsfgsjdfj</h4>
                            </div>
                            
                            <div class="modal-body">
                                '.$items.'
                            </div>
                            <div class="modal-footer">
                                '.$button.'
                                <button type="button" class="btn btn-default btn-sm " data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                        </div>
                    </div>
                </div> -->
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
                                //echo "current - ".$current_item."end - ".$end_item;
                                $count = $page;
								if($store_priviledges->num_rows() > 0)
								{
									$order_count = 0;
									$count = 0;
									$number_rows = $store_priviledges->num_rows();
									
									foreach ($store_priviledges->result() as $key) 
									{
										# code...
										$store_parent = $key->store_parent;
										
										if($store_parent == 0)
										{
											$store_id = $key->store_id;
											
											//get all orders for the stores asssigned to the logged in persoonel.
											$all_orders = $this->inventory_management_model->get_all_requests($store_id);
											
											if($all_orders->num_rows() > 0)
											{
												//foreach request get all the request items
												foreach($all_orders->result() as $orders)
												{
													$order_id = $orders->order_id;
													$personnel_first_name = $orders->personnel_fname;
													$personnel_surname = $orders->personnel_onames;
													$personnel_names = $personnel_first_name.' '.$personnel_surname;
													$store_id = $orders->store_id;
													$order_number = $orders->order_number;
													$store_name = $orders->store_name;
													$order_date = $orders->orders_date;
													$store_parent = $orders->store_parent;
													$order_approval_status = $orders->order_approval_status;
													$status = $this->inventory_management_model->get_status($order_approval_status);

													if($status == "Approved"){
														$status = "Approved";
													}
													else{
														$status = "Not Approved";
													}
													
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
                                                    </tr>
                                                    <?php
								
												}
											}
										}
										else
										{
											$store_id = $key->store_id;
											//get all orders for the stores asssigned to the logged in persoonel.
											$all_orders = $this->inventory_management_model->get_store_requests($store_id);
											
											if($all_orders->num_rows() > 0)
											{
												foreach($all_orders->result() as $keys)
												{
													$order_id = $keys->order_id;
													$personnel_first_name = $keys->personnel_fname;
													$personnel_surname = $keys->personnel_onames;
													$personnel_names = $personnel_first_name.' '.$personnel_surname;
													$store_id = $keys->store_id;
													$order_number = $keys->order_number;
													$store_name = $keys->store_name;
													$order_date = $keys->orders_date;
													$store_parent = $keys->store_parent;
													$order_approval_status = $keys->order_approval_status;
													$status = $this->inventory_management_model->get_status($order_approval_status);
													$order_count++;

													if($status == "Approved"){
														$status = "Approved";
													}
													else{
														$status = "Not Approved";
													}
													
													
													?>
													<tr>
														<td><?php echo $order_count;?></td>
														<td><?php echo $store_name;?></td>
														<td><?php echo $order_number;?></td>
														<td><?php echo date('jS M Y H:i:s',strtotime($order_date));?></td>
														<td><?php echo $personnel_names;?></td>
														<td><?php echo $status;?></td>
														
														<td><a href="<?php echo site_url().'view-order/'.$order_id;?>" class="btn btn-sm btn-primary ">View Details</a></td>
													</tr>
													<?php
												}
											}
											
										}
										
										
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