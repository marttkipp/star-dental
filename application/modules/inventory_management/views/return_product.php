 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
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
				
				if(!empty($validation_errors))
				{
					echo '<div class="alert alert-danger">'.$validation_errors.'</div>';
				}
				
				if(!empty($success))
				{
					echo '<div class="alert alert-success">'.$success.'</div>';
					$this->session->unset_userdata('success_message');
				}
			?>
          </div>
			<?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal"));?>
        	<div class="row">
                
                <div class="col-md-offset-3 col-md-6">  
                   
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Return to which store ?</label>
                        <div class="col-lg-8">
                            <select name="store_id" id="store_id" class="form-control">
                                    <?php
                                    $personnel_id = $this->session->userdata('personnel_id');
                                    $all_stores = $this->stores_model->get_parent_stores($personnel_id);
                                    echo '<option value="0">No Store</option>';
                                    if($all_stores->num_rows() > 0)
                                    {
                                        $result = $all_stores->result();
                                        
                                        foreach($result as $res)
                                        {
                                            if($res->store_id == set_value('store_id'))
                                            {
                                                echo '<option value="'.$res->store_id.'" selected>'.$res->store_name.'</option>';
                                            }
                                            else
                                            {
                                                echo '<option value="'.$res->store_id.'">'.$res->store_name.'</option>';
                                            }
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Returned Quantity: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deduction_quantity" placeholder="Deduction Quantity" value="<?php echo set_value('product_deduction_quantity');?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Pack Size: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deduction_pack_size" placeholder="Pack Size" value="1" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Returned Reason: </label>
                        
                        <div class="col-lg-8">
                          <textarea class="form-control" name="deduction_description"><?php echo set_value('deduction_description');?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="center-align">
            	<a href="<?php echo site_url().'inventory/products';?>" class="btn btn-lg btn-default">Back</a>
                <button class="btn btn-info btn-lg" type="submit">Deduct Product</button>
            </div>
            <?php echo form_close();?>
            
          </div>
        </div>
        <!-- Widget ends -->

      </div>
    </section>