 <?php
 $creditor_result = $this->products_model->get_drug_trail_report($drug_id);

 ?>
 <section class="panel" style="margin-top:25px;">
        <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
              <a href="<?php echo site_url().'print-drug-trail/'.$drug_id;?>" class="btn btn-sm btn-warning">Print Drug Trail</a>
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
                        	<table class="table table-hover table-bordered ">
            							 	<thead>
            									<tr>
            									  <th>Date</th>						  
            									  <th>Store</th>
            									  <th>Type</th>
                                <th>Description</th>
            									  <th>Total In</th>
            									  <th>Total Out</th>
                                <th>Stock</th>						
            									</tr>
            								 </thead>
            							  	<tbody>
            							  		<?php echo $creditor_result['result']?>
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