<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
         <h2 class="panel-title pull-left"><?php echo $title;?></h2>
         <div class="widget-icons pull-right">
            	<a href="<?php echo base_url();?>procurement/general-orders" class="btn btn-info btn-sm"><i class="fa fa-arrow-left"></i> Back to orders</a>
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! Change a few things up and try submitting again. </div>';
            }
            
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
     		<div class="row">
     			<div class="col-md-12">
     				 <!-- brand Name -->
                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Stores</label>
                            <div class="col-lg-8">
                                <select name="store_id" id="store_id" class="form-control">
                                        <?php
                                        $personnel_id = $this->session->userdata('personnel_id');
                                        $all_stores = $this->stores_model->all_stores_assigned($personnel_id);
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
                    </div>
                    <div class="col-md-6">
			            <div class="form-group">
			                <label class="col-lg-3 control-label">Order Instructions</label>
			                <div class="col-lg-9">
			                	<textarea class="form-control" name="order_instructions"><?php echo set_value('order_instructions');?></textarea>
			                </div>
			            </div>
                    </div>
     			</div>
     		</div>
     		<br>
     		<div class="row">
	            <div class="form-actions center-align">
	                <button class="submit btn btn-primary btn-sm" type="submit">
	                    Create New Order
	                </button>
	            </div>
	         </div>
            <br />
            <?php echo form_close();?>
    </div>
</section>