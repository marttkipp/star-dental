        <section class="panel panel-warning">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right">Active branch: <?php echo $branch_name;?></h2>
            	<h2 class="panel-title">Search Preauths</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("administration/reports/search_preauths", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Visit Type: </label>
                        
                        <div class="col-lg-8">
                            <select class="form-control" name="visit_type_id">
                            	<option value="">---Select Visit Type---</option>
                                <?php
                                    if(count($type) > 0){
                                        foreach($type as $row):
                                            $type_name = $row->visit_type_name;
                                            $type_id= $row->visit_type_id;
                                                ?><option value="<?php echo $type_id; ?>" ><?php echo $type_name ?></option>
                                        <?php	
                                        endforeach;
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Invoice Number: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="invoice_number" placeholder="Invoice number">
                        </div>
                    </div>

                </div>

                
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Preauth Date From: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Preauth Date From" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Preauth Date To: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Preauth Date To" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-4">
                 
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Name: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="surname" placeholder="Name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Patient Number: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient Number" autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-lg-8 col-lg-offset-4">
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