 <section class="panel panel-danger">
    <header class="panel-heading">
        <h2 class="panel-title"><i class="icon-reorder"></i>Search Store Deductions</h2>
    </header>             
    
    <!-- Widget content -->
         <div class="panel-body">
    	<div class="padd">
			<?php
			
			
			echo form_open("inventory/search-store-deductions", array("class" => "form-horizontal"));
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
                
                <div class="col-md-6">
                    <div class="form-group">
                    <label class="col-lg-4 control-label">Status ?</label>
                    <div class="col-lg-8">
                        <div class="radio">
                            <label>
                                <input  type="radio" checked value="0" name="drug_status" id="drug_status">
                                None
                            </label>
                            <label>
                                <input  type="radio"  value="1" name="drug_status" id="drug_status" >
                                Not Awarded
                            </label>
                            <label>
                                <input  type="radio" value="2" name="drug_status" id="drug_status" >
                                Awarded
                            </label>
                             <label>
                                <input  type="radio" value="3" name="drug_status" id="drug_status" >
                                Rejected
                            </label>
                        </div>
                    </div>
                </div>

                </div>
                
            </div>
            <br>
            <div class="row">
            	<div class="col-md-12 center-align">                    
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="center-align">
                                <button type="submit" class="btn btn-info btn-sm">Search Drug</button>
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