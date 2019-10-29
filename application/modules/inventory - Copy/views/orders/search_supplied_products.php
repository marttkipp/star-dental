 <section class="panel panel-success">
    <header class="panel-heading">
        <h2 class="panel-title"><i class="icon-reorder"></i>Search Purchases</h2>
    </header>             
    
    <!-- Widget content -->
         <div class="panel-body">
    	<div class="padd">
			<?php
			
			
			echo form_open("search-products-purchased", array("class" => "form-horizontal"));
			
            
            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Creditor Name: </label>
                        
                        <div class="col-md-8">
                            <select class="form-control" name="supplier_id">
                            	<option value="">---Select Visit Type---</option>
                                <?php
                                	$all_suppliers = $this->orders_model->get_suppliers();
                                    if($all_suppliers->num_rows() > 0){
                                        foreach($all_suppliers->result() as $row):
                                            $creditor_id = $row->creditor_id;
                                            $creditor_name= $row->creditor_name;
                                            ?><option value="<?php echo $creditor_id; ?>" ><?php echo $creditor_name ?></option>
                                        <?php	
                                        endforeach;
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
               	<div class="col-md-4">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Product Name: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="product_name" placeholder="Product Name">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Invoice Number.: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="invoice_number" placeholder="Invoice Number">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
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