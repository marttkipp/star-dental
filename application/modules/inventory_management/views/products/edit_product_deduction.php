<?php
	$product_deductions_quantity = $deduction_details->product_deductions_quantity;
	$product_deductions_pack_size = $deduction_details->product_deductions_pack_size;
	
	if(!empty($validation_errors))
	{
		$product_deductions_pack_size = set_value('product_deductions_pack_size');
		$product_deductions_quantity = set_value('product_deductions_quantity');
		$expiry_date = set_value('expiry_date');
	}
?>
 <section class="panel">
    <header class="panel-heading">
      <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
      <div class="widget-icons pull-right">
        <a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-default">Back to Inventory</a>
        <a href="<?php echo site_url().'inventory/deduct-product/'.$product_id;?>" class="btn btn-sm btn-success">Deduct product</a>
                        	
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
                        <label class="col-lg-4 control-label">Purchase Quantity: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deductions_quantity" placeholder="Deduction Quantity" value="<?php echo $product_deductions_quantity;?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Pack Size: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="product_deductions_pack_size" placeholder="Pack Size" value="<?php echo $product_deductions_pack_size;?>">
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="center-align">
            	<button class="btn btn-info btn-sm" type="submit">Edit Deduction</button>
            </div>
            <?php echo form_close();?>
            
       </div>
    </div>
</section>