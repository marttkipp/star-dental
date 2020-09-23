       
        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title pull-right"><?php echo $title;?></h2>
            	<h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
			<?php
            echo form_open("administration/reports/search_malaria_reports", array("class" => "form-horizontal"));
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
                        <label class="col-lg-4 control-label">Visit Date From: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Visit Date From">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Visit Date To: </label>
                        
                        <div class="col-lg-8">
                        	<div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Visit Date To">
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <br>
            <div class="row">
            	<div class="col-md-4">
                	<label class="col-lg-4 control-label">Department </label>
                    <div class="col-lg-8">
                        <div class="form-group">
                        	<input type="text" class="form-control" name="department_mane" placeholder="Department">
                        </div>
                    </div>
                </div>
                <?php
				$type = $this->reception_model->get_types();
				?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Company: </label>
                        
                        <div class="col-lg-8">
                            <select class="form-control" name="visit_type_id">
                            	<option value="">---Select Company---</option>
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
                </div>
                <div class="col-md-4">
                	<div class="form-group">
                        <label class="col-md-3 control-label">Gender: </label>
                        <div class="col-md-3">
                            <input type="radio" name="gender_id" value="0" checked="checked"> All
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="gender_id" value="1"> Female
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="gender_id" value="2"> Male
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="row">
            	<div class="col-md-4">

            		<div class="form-group">
                        <label class="col-md-4 control-label">Range: </label>
                        
                        <div class="col-md-8">
                            <select class="form-control" name="age">
                            	<option value="">---Select Age Range--</option>
                                <option value="0"> < 5 Years</option>
                        		<option value="1"> 5 - 14 Years </option>
                                <option value="2"> > 15 Years </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                	<div class="form-group">
                        <label class="col-md-3 control-label">Results: </label>
                        <div class="col-md-3">
                            <input type="radio" name="results" value="0" checked="checked"> All
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="results" value="1"> Positive(+)
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="results" value="2"> Negative(-)
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
            	<div class="col-md-12">
            		<div class="form-group">
                        <div class="center-align">
                            <button type="submit" class="btn btn-info">Search Malaria Report</button>
                        </div>
                </div>
            		
            	</div>
            	
            </div>
            
            
            <?php
            echo form_close();
            ?>
          </div>
		</section>