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
                        <label class="col-lg-4 control-label">Deduction Quantity: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deduction_quantity" placeholder="Deduction Quantity" value="<?php echo set_value('product_deduction_quantity');?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Pack Size: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deduction_pack_size" placeholder="Pack Size" value="<?php echo set_value('product_deduction_pack_size');?>">
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-lg-4 control-label">Deduction Date: </label>
                        
                        <div class="col-lg-8">
                          <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="deduction_date" value="<?php echo date('Y-m-d')?>" placeholder="Date"  autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Deducted Reason: </label>
                        
                        <div class="col-lg-8">
                          <textarea class="form-control" name="deduction_description"><?php echo set_value('deduction_description');?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="center-align">
            	<a href="<?php echo site_url().'deductions/'.$product_id.'/'.$store_id;?>" class="btn btn-lg btn-default">Back</a>
                <button class="btn btn-info btn-lg" type="submit">Deduct Product</button>
            </div>
            <?php echo form_close();?>
            
          </div>
        </div>
        <!-- Widget ends -->

      </div>
    </section>