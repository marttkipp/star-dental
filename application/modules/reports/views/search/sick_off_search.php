       
        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right">Sick Search:</h2>
            	<h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("reports/search_sick_off_reports", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-6">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Date From: </label>
                        
                        <div class="col-md-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Date From">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label"> Date To: </label>
                        
                        <div class="col-md-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Date To">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label"> Type: </label>
                        
                        <div class="col-md-8">
                        	<select id='leave_type_id' name='leave_type_id' class='form-control custom-select '>
                                <option value=''>None - Please Select a type</option>
                                <?php echo $l_types;?>
                            </select>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label"> Payroll Number: </label>
                        
                        <div class="col-md-8">
                        	<input type="text" class="form-control" name="payroll_number" placeholder="Payroll Number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label"> Department: </label>
                        
                        <div class="col-md-8">
                        	<select id='department_name' name='department_name' class='form-control custom-select '>
                                <option value=''>None - Please Select an department</option>
                                <?php echo $departments;?>
                            </select>
                        </div>
                    </div>
                    
                </div>
            </div>
            <br>
            <div class="row">
            	<div class="col-md-12">
            		<div class="form-group">
                        <div class="center-align">
                            <button type="submit" class="btn btn-info">Search Report</button>
                        </div>
                </div>
            		
            	</div>
            	
            </div>
            
            
            <?php
            echo form_close();
            ?>
          </div>
		</section>
<script type="text/javascript">
	$(function() {
	    $("#department_name").customselect();
	    $("#leave_type_id").customselect();
	  });
</script>