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

$message = "Hello ".$patient_surname.",Your balance up to date is Kes. ".number_format($balance,2).".\nKindly pay at your earliest. M-PESA (Buy Goods till No. Account No. 630642).\nIncase of queries kindly contact 0717123440.";
?>

<section class="panel">

    <div class="panel-body">
            <?php 
                echo form_open("reception/register_other_patient", array("class" => "form-horizontal","id"=> 'send-message'));
            ?>

            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>" required>
                <div class="col-md-12">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Phone Number: </label>

                                <div class="col-lg-8">
                                <input type="text" class="form-control" name="phone_number" id="phone_number" value="<?php echo $patient_phone1;?>" required>
                                </div>
                        </div>
                       
                        <div class="form-group">
                            <label class="col-md-4 control-label">MESSAGE</label>
                     
                            <div class="col-md-8">
                                 <textarea class="form-control" name="message" id="message" rows="5" required><?php echo $message?></textarea>
                            </div>
                               
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <button class="btn btn-md btn-info"  > Send Message </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

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