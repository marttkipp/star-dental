        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right">Active branch: <?php echo $branch_name;?></h2>
            	<h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("search-general-report", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Visit Date From: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Visit Date From" autocomplete="off">
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="col-md-3">
                    
                    
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Visit Date To: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Visit Date To" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-3">
                    
                    
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Patient Name: </label>
                        
                        <div class="col-lg-8">
                            
                            <input type="text" name="patient_name" class="form-control" id="patient_name" placeholder="Patient Name">
                                
            
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-3">
                   
                    
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