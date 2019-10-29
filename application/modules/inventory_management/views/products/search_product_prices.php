 <section class="panel panel-success">
    <header class="panel-heading">
        <h2 class="panel-title"><i class="icon-reorder"></i>Search orders</h2>
    </header>             
    
    <!-- Widget content -->
         <div class="panel-body">
    	<div class="padd">
			<?php
			
			
			echo form_open("inventory/search-product-prices", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Product: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="product_name" placeholder="Product">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ">                    
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="center-align">
                                <button type="submit" class="btn btn-info">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <?php
            echo form_close();
            ?>
    	</div>
</section>