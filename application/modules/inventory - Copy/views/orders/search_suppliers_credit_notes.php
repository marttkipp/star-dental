 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title pull-right"></h2>

        <h2 class="panel-title">Search Credit Notes</h2>

    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php

            echo form_open("inventory/orders/search_supplier_credit_notes", array("class" => "form-horizontal"));
            ?>
            <div class="row">
            	<div class="col-md-12">
            		<div class="col-md-4">
	                  	<div class="form-group" style="margin:0 auto;">
	                        <label class="col-lg-4 control-label">CREDIT NOTE: </label>
	                        
	                        <div class="col-lg-8">
	                            <input type="text" class="form-control" name="credit_note_number" placeholder="Credit Note Number">
	                        </div>
	                    </div>
	                </div>
            		<div class="col-md-4">
	                  	<div class="form-group" style="margin:0 auto;">
	                        <label class="col-lg-4 control-label">INVOICE: </label>
	                        
	                        <div class="col-lg-8">
	                            <input type="text" class="form-control" name="invoice_number" placeholder="Invoice Number">
	                        </div>
	                    </div>
	                </div>
	                <div class="col-md-4">
	                	<div class="form-group" style="margin:0 auto;">
	                        <label class="col-lg-4 control-label">SUPPLIER </label>
                            <div class="col-lg-8">
                                <select id="supplier_id" name="supplier_id" class="form-control">
                                   <option value="0">SELECT A SUPPLIER</option>
		                    		<?php
		                    		if($suppliers_query->num_rows() > 0)
		                    		{
		                    			foreach ($suppliers_query->result() as $key_supplier_items ) {
		                    				# code...
		                    				$creditor_id = $key_supplier_items->creditor_id;
		                    				$creditor_name = $key_supplier_items->creditor_name;

		                    				echo '<option value="'.$creditor_id.'">'.$creditor_name.'</option>';
		                    			}
		                    		}
		                    		?>
                                </select>
                            </div>
	                    </div>
	                </div>
                </div>
            </div>

            <br/>
            <div class="center-align">
                <button type="submit" class="btn btn-info btn-sm">Search</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</section>