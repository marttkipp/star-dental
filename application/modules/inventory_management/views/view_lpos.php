 <section class="panel">
        <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-relpo"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
            <a href="<?php echo site_url().'inventory/manage-orders';?>" class="btn btn-sm btn-default">Back to Orders</a>
               
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
              	<div class="col-md-2 col-md-offset-10">
                	<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#lpos_modal">
                        Create LPO
                    </button>
                    
                    <div class="modal fade" id="lpos_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Create New LPO</h4>
                                </div>
                                <div class="modal-body">
                                    
                                    <?php echo form_open('inventory_management/create_new_lpo/'.$order_id, array('class' => 'form-horizontal'));?>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="col-md-5">LPO Date</label>
                                            <div class="col-md-7">
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="lpo_date" placeholder="LPO Date">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group" id="supplier_id">
                                            <label for="exampleInputEmail1" class="col-lg-5 control-label">Select Supplier: </label>
                                            
                                            <div class="col-lg-7">
                                                <select name="nav_supplier_id" class="form-control custom-select">
                                                    <?php echo $suppliers;?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="col-sm-offset-6 col-sm-6">
                                                <button type="submit" class="btn btn-primary">Create LPO</button>
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
                </div>
              </div>
              
                <div class="row">
                    <div class="col-md-12">
                    	<div class="table-responsive">
                              <table blpo="0" class="table table-hover table-condensed">
                                <thead> 
                                    <th>#</th>
                                    <th>LPO Date</th>
                                    <th>LPO Number</th>
                                    <th>Supplier</th>
                                    <th>Created By</th>
                                    <th>LPO Status</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                <?php 
                                if($query->num_rows() > 0)
								{
									$lpo_count = 0;
									foreach($query->result() as $lpos_query)
									{
										$personnel_first_name = $lpos_query->personnel_fname;
										$personnel_surname = $lpos_query->personnel_onames;
										$personnel_names = $personnel_first_name.' '.$personnel_surname;
										$lpo_id = $lpos_query->lpo_id;
										$lpo_number = $lpos_query->lpo_number;
										$nav_supplier_id = $lpos_query->nav_supplier_id;
										$nav_supplier = $lpos_query->Search_Name;
										$lpo_date = $lpos_query->lpo_date;
										$lpo_status = $lpos_query->lpo_status_id;
										$status = $this->inventory_management_model->get_lpo_status($lpo_status);
										$lpo_count++;
										?>
                                        <tr>
                                            <td><?php echo $lpo_count;?></td>
                                            <td><?php echo date('jS M Y H:i:s',strtotime($lpo_date));?></td>
                                            <td><?php echo $lpo_number;?></td>
                                            <td><?php echo $nav_supplier;?></td>
                                            <td><?php echo $personnel_names;?></td>
                                            <td><?php echo $status;?></td>
                                            <?php
											if($lpo_status == 1)
											{
												?>
                                             	<td><a href="<?php echo site_url().'add-lpo-items/'.$lpo_id.'/'.$order_id;?>" target="_blank" class="btn btn-sm btn-warning ">Add Items</a></td>
                                                <td><a href="<?php echo site_url().'approve-lpo/'.$lpo_id.'/'.$order_id;?>" class="btn btn-sm btn-success" onClick="return confirm('Are you sure you want to approve this LPO?');">Approve LPO</a></td>
                                             		
												<?php
											}
											else
											{
												?>
                                                <td><a href="<?php echo site_url().'print-lpo/'.$lpo_id.'/'.$order_id;?>" target="_blank" class="btn btn-sm btn-warning ">Print</a></td>
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
       var url = "<?php echo base_url();?>inventory/award-store-lpo/"+product_deductions_id+'/'+quantity;
  
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