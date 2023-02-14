<?php
//patient data
// $row = $patient->row();
$patient_surname = $patient['patient_surname'];
$patient_othernames = $patient['patient_othernames'];
$title_id = $patient['title_id'];
$patient_id = $patient['patient_id'];
$patient_date_of_birth = $patient['patient_date_of_birth'];
$gender_id = $patient['gender_id'];
$religion_id = $patient['religion_id'];
$civil_status_id = $patient['civil_status_id'];
$patient_email = $patient['patient_email'];
$patient_address = $patient['patient_address'];
$patient_postalcode = $patient['patient_postalcode'];
$patient_town = $patient['patient_town'];
$patient_phone1 = $patient['patient_phone_number'];
$patient_phone2 = $patient['patient_phone2'];
$patient_kin_sname = $patient['patient_kin_sname'];
$patient_kin_othernames = $patient['patient_kin_othernames'];
$relationship_id = $patient['relationship_id'];
$patient_national_id = $patient['patient_national_id'];
$insurance_company_id = $patient['insurance_company_id'];
$patient_number = $patient['patient_number'];
$next_of_kin_contact = $patient['patient_kin_phonenumber1'];
$current_patient_number = $patient['current_patient_number'];
// $patient['patient_phone_number'] = $patient_phone_number;
?>

<section class="panel">

<div class="panel-body">
    <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-5 control-label" style="margin-left:-15px;">Phone Number: </label>

                                <div class="col-lg-7" style="margin-left:110px; margin-top:-30px;">
                                <input type="text" class="form-control"  value="<?php echo $patient_phone1;?>">
                                </div>
                        </div>
                       
                        <div class="form-group">
                                <h4>MESSAGE</h4>
                                <br>
                                <textarea class="form-control">Hello <?php echo $patient_surname?>,Your balance up to date is <?php echo $balance;?>. Kindly pay at your earliest.</textarea>
                        </div>
                        <div class="" style="margin-left:480px;">
                         <a class="btn btn-xs btn-info" onclick="sendmessage_sidebar('.$patient_id.','.$balance.')" > Send Message </a></td>
                        </div>
                        </div>
                    </div>
                </div>
    </div>

    <div class="row" style="margin-top: 5px;">
        <ul>
            <li style="margin-bottom: 5px;">
                <div class="row">
                    <div class="col-md-12 center-align">
                        <a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
                    </div>
                </div>
                
            </li>
        </ul>
    </div>
</section>