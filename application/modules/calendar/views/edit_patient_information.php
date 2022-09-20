<?php
//patient data
$row = $appointment_query->row();


$patient_surname = $row->patient_surname;
// var_dump($patient_surname);die();
$patient_othernames = $row->patient_othernames;
$title_id = $row->title_id;
$patient_id = $row->patient_id;
$patient_date_of_birth = $row->patient_date_of_birth;
$gender_id = $row->gender_id;
$religion_id = $row->religion_id;
$civil_status_id = $row->civil_status_id;
$patient_email = $row->patient_email;
$patient_address = $row->patient_address;
$patient_postalcode = $row->patient_postalcode;
$patient_town = $row->patient_town;
$patient_phone1 = $row->patient_phone1;
$patient_phone2 = $row->patient_phone2;
$patient_kin_sname = $row->patient_kin_sname;
$patient_kin_othernames = $row->patient_kin_othernames;
$relationship_id = $row->relationship_id;
$patient_national_id = $row->patient_national_id;
$patient_number = $row->patient_number;
$insurance_company_id = $row->insurance_company_id;
$next_of_kin_contact = $row->patient_kin_phonenumber1;
$current_patient_number = $row->current_patient_number;
$patient_first_name = $row->patient_first_name;
$occupation = $row->occupation;
$category_id = $row->category_id;
// $patient_tag_id = $row->tag_id;
$place_of_work = $row->place_of_work;
$scheme_name = $row->scheme_name;
$insurance_number = $row->insurance_number;
$age_group = $row->age_group;

if($category_id == 1)
{
    $walkin_checked = '';
    $new_checked = 'checked';
    $uncategorised_checked = '';
}
else if($category_id == 2)
{
    $walkin_checked = 'checked';
    $new_checked = '';
    $uncategorised_checked = '';
}

else
{
    $walkin_checked = '';
    $new_checked = '';
    $uncategorised_checked = 'checked';
}

$adult_checked = '';
$dependant_checked = '';
if($age_group == 'A')
{
    $adult_checked = 'checked';
    $dependant_checked = '';
}
else if($age_group == 'D')
{
    $dependant_checked = 'checked';
    $adult_checked = '';
}

//echo $gender_id;
//repopulate data if validation errors occur
$validation_error = validation_errors();
                
if(!empty($validation_error))
{
    $patient_surname = set_value('patient_surname');
    $patient_othernames = set_value('patient_othernames');
    $title_id = set_value('title_id');
    $patient_date_of_birth = set_value('patient_dob');
    $gender_id = set_value('gender_id');
    $religion_id = set_value('religion_id');
    $civil_status_id = set_value('civil_status_id');
    $patient_email = set_value('patient_email');
    $patient_address = set_value('patient_address');
    $patient_postalcode = set_value('patient_postalcode');
    $patient_town = set_value('patient_town');
    $patient_phone1 = set_value('patient_phone1');
    $patient_phone2 = set_value('patient_phone2');
    $patient_kin_sname = set_value('patient_kin_sname');
    $patient_kin_othernames = set_value('patient_kin_othernames');
    $relationship_id = set_value('relationship_id');
    $insurance_company_id1 = set_value('insurance_company_id');
    $patient_national_id = set_value('patient_national_id');
    $next_of_kin_contact = set_value('next_of_kin_contact');
    $patient_number = set_value('patient_number');

    $current_patient_number = set_value('current_patient_number');
    $patient_first_name = set_value('patient_first_name');


}


if(empty($patient_tag_id))
{
    $patient_tag_id = 1;
}

$genders = $this->reception_model->get_gender();
$insurance_companys = $this->reception_model->get_insurance();
// $tags = $this->reception_model->get_tags();
// var_dump($insurance_companys->result());die();
?>

       
<div class="row">

<?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal","id"=> 'edit-patient-detail'));?>
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Name: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_surname" placeholder="Names" value="<?php echo $patient_surname;?>">
                </div>
            </div>
            <input type="hidden" class="form-control" name="patient_id" id="patient_id"  placeholder="Names" value="<?php echo $patient_id;?>">
            <input type="hidden" class="form-control" name="appointment_id" id="appointment_id"  placeholder="Names" value="<?php echo $appointment_id;?>">
            <div class="form-group" style="display: none">
                <label class="col-md-4 control-label">Other Names: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_othernames" placeholder="Other Names" value="<?php echo $patient_othernames;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">New Patient ?</label>
                <div class="col-lg-8">
                    <div class="radio">
                       
                        <label>
                            <input id="optionsRadios1" type="radio" <?php echo $walkin_checked;?> value="2" name="category">
                            Active Patient
                        </label>
                        <label>
                            <input id="optionsRadios1" type="radio" <?php echo $new_checked;?> value="1" name="category">
                            New Patient 
                        </label>
                       
                       
                    </div>
                    
                </div>
                
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">File Number: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_number" placeholder="Patient Number" value="<?php echo $patient_number;?>" readonly>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Date of Birth: </label>
                
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="patient_dob" placeholder="Date of Birth" value="<?php echo $patient_date_of_birth;?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Gender: </label>
                
                <div class="col-md-8">
                    <select class="form-control" name="gender_id">
                         <option value="">Select a gender</option>
                        <?php
                            if($genders->num_rows() > 0)
                            {
                                $gender = $genders->result();
                                
                                foreach($gender as $res)
                                {
                                    $db_gender_id = $res->gender_id;
                                    $gender_name = $res->gender_name;
                                    
                                    if($db_gender_id == $gender_id)
                                    {
                                        echo '<option value="'.$db_gender_id.'" selected>'.$gender_name.'</option>';
                                    }
                                    
                                    else
                                    {
                                        echo '<option value="'.$db_gender_id.'">'.$gender_name.'</option>';
                                    }
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            
            
           
            <div class="form-group">
                <label class="col-md-4 control-label">Email Address: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_email" placeholder="Email Address" value="<?php echo $patient_email;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Primary Phone: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_phone1" placeholder="Primary Phone" value="<?php echo $patient_phone1;?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Other Phone: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_phone2" placeholder="Other Phone" value="<?php echo $patient_phone2;?>">
                </div>
            </div>

            
          
           
            
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Residence: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_town" placeholder="Residence" value="<?php echo $patient_town;?>">
                </div>
            </div>                          
                           
                            
            <div class="form-group">
                <label class="col-md-4 control-label">Next of Kin Surname: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_kin_sname" placeholder="Kin Surname" value="<?php echo $patient_kin_sname;?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-4 control-label">Next of Kin Other Names: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="patient_kin_othernames" placeholder="Kin Other Names" value="<?php echo $patient_kin_othernames;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Next of Kin Contact: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="next_of_kin_contact" placeholder="" value="<?php echo $next_of_kin_contact;?>">
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
                                    $db_relationship_id = $res->relationship_id;
                                    $relationship_name = $res->relationship_name;
                                    
                                    if($db_relationship_id == $relationship_id)
                                    {
                                        echo '<option value="'.$db_relationship_id.'" selected>'.$relationship_name.'</option>';
                                    }
                                    
                                    else
                                    {
                                        echo '<option value="'.$db_relationship_id.'">'.$relationship_name.'</option>';
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
                                    
                                    if($visit_type_id1 == $insurance_company_id)
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
            <div class="form-group">
                <label class="col-lg-4 control-label">Branch Code: </label>
                
                <div class="col-lg-8">
                    <select class="form-control" name="branch_id" required="required">
                        <!-- <option value="">---Select branch---</option> -->
                        <?php
                            $session_branch_id = $this->session->userdata('branch_id');
                            if($branches->num_rows() > 0){
                                foreach($branches->result() as $row):
                                    $branch_name = $row->branch_name;
                                    $branch_code = $row->branch_code;
                                    $branch_id = $row->branch_id;

                                    if($branch_id == $session_branch_id)
                                    {
                                        echo "<option value='".$branch_id."#".$branch_code."' selected>".$branch_name."</option>";
                                    }
                                    else
                                    {
                                        if($session_branch_id == 0)
                                        {
                                            echo "<option value='".$branch_id."#".$branch_code."'>".$branch_name."</option>";
                                        }
                                        
                                    }
                                endforeach;
                            }
                        ?>
                    </select>
                </div>
            </div>       		
             
        </div>

        <div class="col-md-12">
	    	<div class="center-align">
		        <button class="btn btn-info btn-sm" type="submit" onclick="return confirm('Are you sure you want to edit patient details')">Edit Patient</button>
		    </div>
	    </div>
    </div>
    
   
<?php echo form_close();?>
</div>
            

<script type="text/javascript">    

    $(document).ready(function(){

        var insurance_company_id =document.getElementById("insurance_company_id").value;
        
        if(insurance_company_id == 0)
        {
            // this is not set yest
            $('#insured_company').css('display', 'none');
        }
        else if(insurance_company_id == 1) 
        {
            $('#insured_company').css('display', 'none');
        }
        else if(insurance_company_id > 1)
        {
            // alert(insurance_company_id);
            $('#insured_company').css('display', 'block');
        }
  
    });

     $(document).on("change","select#insurance_company",function(e)
    {
        var visit_type_id = $(this).val();
        
        if(visit_type_id != '1')
        {
            $('#insured_company').css('display', 'block');
            // $('#consultation').css('display', 'block');
        }
        else
        {
            $('#insured_company').css('display', 'none');
            // $('#consultation').css('display', 'block');
        }  

    });
</script>