
          <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    </div>
            
                    <h2 class="panel-title"><?php echo $title;?></h2>
                </header>
                <div class="panel-body">
                	<div class="row" style="margin-bottom:20px;">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url();?>administration/insurance-scheme" class="btn btn-info pull-right">Back to insurance schemes</a>
                        </div>
                    </div>
                <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! '.$error.' </div>';
            }
			
			//the insurance_scheme details
			$insurance_scheme_name = $insurance_scheme[0]->insurance_scheme_name;
			$insurance_scheme_status = $insurance_scheme[0]->insurance_scheme_status;
			$visit_type_id2 = $insurance_scheme[0]->visit_type_id;
            $branch_id = $insurance_scheme[0]->branch_id;
            
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
				$insurance_scheme_name = set_value('insurance_scheme_name');
				$insurance_scheme_status = set_value('insurance_scheme_status');
				$visit_type_id2 = set_value('visit_type_id');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
			
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">insurance scheme:</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="insurance_scheme_name" placeholder="insurance scheme name" value="<?php echo $insurance_scheme_name;?>" required>
                        </div>
                    </div>
                </div>
                        
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-5 control-label">Insurance company: </label>
                        <div class="col-lg-7">
                            <select id="visit_type_id" name="visit_type_id" class="form-control">
                                <option value="">--- None ---</option>
                                <?php
								if($insurance_companies->num_rows() > 0)
								{	
									foreach($insurance_companies->result() as $row):
                                        // $company_name = $row->company_name;
                                        $visit_type_name = $row->visit_type_name;
                                        $visit_type_id = $row->visit_type_id;
                                        
                                        if($visit_type_id == $visit_type_id2)
                                        {
                                            echo "<option value=".$visit_type_id." selected='selected'> ".$visit_type_name."</option>";
                                        }
                                        
                                        else
                                        {
                                            echo "<option value=".$visit_type_id."> ".$visit_type_name."</option>";
                                        }
                                    endforeach;	
                                } 
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-6 control-label">Activate insurance scheme?</label>
                        <div class="col-lg-3">
                            <div class="radio">
                                <label>
                                    <?php
                                    if($insurance_scheme_status == 1){echo '<input id="optionsRadios1" type="radio" checked value="1" name="insurance_scheme_status">';}
                                    else{echo '<input id="optionsRadios1" type="radio" value="1" name="insurance_scheme_status">';}
                                    ?>
                                    Yes
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="radio">
                                <label>
                                    <?php
                                    if($insurance_scheme_status == 0){echo '<input id="optionsRadios1" type="radio" checked value="0" name="insurance_scheme_status">';}
                                    else{echo '<input id="optionsRadios1" type="radio" value="0" name="insurance_scheme_status">';}
                                    ?>
                                    No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="form-actions center-align" style="margin-top:10px;">
                <button class="submit btn btn-primary" type="submit">
                    Edit insurance scheme
                </button>
            </div>
            <br />
            <?php echo form_close();?>
                </div>
            </section>