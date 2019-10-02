       
        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right"><?php echo $title;?></h2>
            	<h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("reports/search_leave_reports", array("class" => "form-horizontal"));
            ?>
            <div class="row">
            	<div class="col-md-4">
                	<label class="col-lg-4 control-label">Payroll No: </label>
                    <div class="col-lg-8">
                        <div class="form-group">
                        	<input type="text" class="form-control" name="payroll_number" placeholder="Payroll Number">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Leave Start Date: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Leave Start Date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Leave End Date: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Leave End Date">
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <br>
            <div class="row">
            	<div class="col-md-12">
            		<div class="form-group">
                        <div class="center-align">
                            <button type="submit" class="btn btn-info">Search Leave Report</button>
                        </div>
                </div>
            		
            	</div>
            	
            </div>
            
            
            <?php
            echo form_close();
            ?>
          </div>
		</section>