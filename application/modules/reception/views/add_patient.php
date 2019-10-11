
<?php 
    echo form_open("reception/register_other_patient", array("class" => "form-horizontal"));
    form_hidden('visit_type_id', 3);
    if(isset($dependant_parent))
    {
        form_hidden('dependant_id', $dependant_parent);
    }
    
    else
    {
        form_hidden('dependant_id', 0);
    }
?>
 <section class="panel">
    <header class="panel-heading">
            <h5 class="pull-left"><i class="icon-reorder"></i>Add Patient</h5>
          <div class="widget-icons pull-right">
               <a href="<?php echo site_url();?>patients" class="btn btn-success btn-sm pull-right">  Patients List</a>

          </div>
          <div class="clearfix"></div>
    </header>
      <div class="panel-body">
        <div class="padd">
          <div class="row">
            <div class="col-md-6">
                <div class="form-group">
               
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Name: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_surname" placeholder="First name" value="<?php echo set_value('patient_surname');?>">
                    </div>
                </div>
                
                <div class="form-group" style="display: none;">
                    <label class="col-md-4 control-label">Other Names: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_othernames" placeholder="Other Names">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Gender: </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="gender_id">
                            <!-- <option value="" >---- select an option ----</option> -->
                            <?php
                                if($genders->num_rows() > 0)
                                {
                                    $gender = $genders->result();
                                    
                                    foreach($gender as $res)
                                    {
                                        $gender_id = $res->gender_id;
                                        $gender_name = $res->gender_name;
                                        
                                        if($gender_id == set_value("gender_id"))
                                        {
                                            echo '<option value="'.$gender_id.'" selected>'.$gender_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$gender_id.'">'.$gender_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                 <div class="form-group" >
                    <label class="col-md-4 control-label">Patient Number: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="current_patient_number" placeholder="Patient Number"  readonly="readonly">
                        NOTE: For a new patient leave this field blank
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">An Appointment?</label>
                    <div class="col-lg-8">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios1" type="radio"  value="1" name="appointment_status">
                                Yes
                            </label>
                            <label>
                                <input id="optionsRadios2" type="radio" checked value="0" name="appointment_status">
                                No
                            </label>
                        </div>
                        
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Email Address: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_email" placeholder="Email Address">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Date of Birth : </label>
            
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="patient_dob" placeholder="Date of Birth">
                        </div>
                    </div>
                </div>
                
            </div>
            </div>
            <div class="col-md-6">
                
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Primary Phone: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_phone1" placeholder="Primary Phone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Other Phone: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_phone2" placeholder="Other Phone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Residence: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_town" placeholder="Residence">
                    </div>
                </div>
                
               
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Surname: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_kin_sname" placeholder="Kin Surname">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Other Names: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_kin_othernames" placeholder="Kin Other Names">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Next of Kin Contact: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="next_of_kin_contact" placeholder="">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label">Relationship To Kin: </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="relationship_id">
                            <?php
                                if($relationships->num_rows() > 0)
                                {
                                    $relationship = $relationships->result();
                                    
                                    foreach($relationship as $res)
                                    {
                                        $relationship_id = $res->relationship_id;
                                        $relationship_name = $res->relationship_name;
                                        
                                        if($relationship_id == set_value("relationship_id"))
                                        {
                                            echo '<option value="'.$relationship_id.'" selected>'.$relationship_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$relationship_id.'">'.$relationship_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Insurance : </label>
                    
                    <div class="col-md-8">
                        <select class="form-control" name="insurance_company_id">
                            <option value="0">Select an insurance Company</option>
                            <?php
                                if($insurance->num_rows() > 0)
                                {
                                    $insurance = $insurance->result();
                                    
                                    foreach($insurance as $res)
                                    {
                                        $visit_type_id1 = $res->visit_type_id;
                                        $visit_type_name = $res->visit_type_name;
                                        
                                        if($visit_type_id1 ==  set_value("insurance_company_id"))
                                        {
                                            echo '<option value="'.$visit_type_id1.'" selected>'.$visit_type_name.'</option>';
                                        }
                                        
                                        else
                                        {
                                            echo '<option value="'.$visit_type_id1.'">'.$visit_type_name.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
            </div>
        </div>
       
            <br/>
        <div class="center-align">
            <button class="btn btn-info btn-sm" type="submit">Add Patient</button>
        </div>
        </div>
    </div>
</section>
<?php echo form_close();?>