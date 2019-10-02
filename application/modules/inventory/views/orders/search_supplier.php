<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
        <h2 class="panel-title">Search Supplier</h2>
    </header>
    <div class="panel-body">
    <?php $personnel_id = $this->session->userdata('personnel_id'); //echo $personnel_id;?>
			<div class="row">
			
				<?php
				echo form_open("inventory/search-supplier", array("class" => "form-horizontal"));
	            ?>
	            <div class="row">
	           		<div class="col-md-11">
		                <div class="col-md-6">
		                    <div class="form-group">
		                        <label class="col-md-5 control-label">Creditor Name: </label>
		                        
		                        <div class="col-md-7">
		                            <input type="text" class="form-control" name="creditor_name" placeholder="Creditor Name">
		                        </div>
		                    </div>
                          
		                </div>
		                <br/>
		                <div class="col-md-4">
							
						
		                    <div class="form-group">
		                        <label class="col-md-5 control-label">Supplier Number: </label>
		                        
		                        <div class="col-md-7">
		                            <input type="text" class="form-control" name="$supplier_invoice_number" placeholder="Supplier Number">
		                        </div>
		                    </div>
						</div>
						<br/>
		                <div class="col-md-6">
		                    
		                   
                            <div class="form-group">
		                        <label class="col-md-5 control-label">Store Name: </label>
		                        
		                        <div class="col-md-7">
		                             <select name="store_id" id="store_id" class="form-control">
		                                <?php
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
		              </div>
	            </div>
	            <br/>
	            <div class="center-align">
					<?php
					$supplier_search = $this->session->userdata('supplier_search');
				
					
					if((!empty($supplier_search)))
					{
						?>
						<a href="<?php echo site_url().'inventory_management/close_inventory_search'?>" class="btn btn-sm btn-warning">Close search</a>
						<?php
					}
					?>
	            	<button type="submit" class="btn btn-info btn-sm">Search</button>
	            </div>
	            <?php
	            echo form_close();
	            ?>
				    	
			</div>
		</div>
	</section>