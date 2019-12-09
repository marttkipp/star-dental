			<?php
            echo form_open("administration/reports/search_tests", array("class" => "form-horizontal"));
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
                        <label class="col-lg-4 control-label">Doctor: </label>
                        
                        <div class="col-lg-8">
                            <select class="form-control" name="personnel_id">
                            	<option value="">---Select Doctor---</option>
                                <?php
									if(count($doctors) > 0){
										foreach($doctors as $row):
											$fname = $row->personnel_fname;
											$onames = $row->personnel_onames;
											$personnel_id = $row->personnel_id;
											echo "<option value=".$personnel_id.">".$onames." ".$fname."</option>";
										endforeach;
									}
								?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Test: </label>
                    	<div class="col-lg-8">
                            <select id='lab_test_id' name='lab_test_id' class="form-control custom-select">
                                <option value=''>None - Please Select a test</option>
                                <?php echo $lab;?>
                            </select>
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
                
                <div class="col-md-4">
                    
                    <!--<div class="form-group">
                        <label class="col-lg-4 control-label">Patient number: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient number">
                        </div>
                    </div>-->
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Branch: </label>
                        
                        <div class="col-lg-8">
                            <select class="form-control" name="branch_code">
                            	<option value="">---Select branch---</option>
                                <?php
									if($branches->num_rows() > 0){
										foreach($branches->result() as $row):
											$branch_name = $row->branch_name;
											$branch_code = $row->branch_code;
											echo "<option value=".$branch_code.">".$branch_name."</option>";
										endforeach;
									}
								?>
                            </select>
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

<script type="text/javascript">
$(document).ready(function(){

    $("#lab_test_id").customselect();
});
</script>