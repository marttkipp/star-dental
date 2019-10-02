<?php

//patient details
$visit_type = $patient['visit_type'];
$patient_type = $patient['patient_type'];
$patient_othernames = $patient['patient_othernames'];
$patient_surname = $patient['patient_surname'];
$patient_surname = $patient['patient_surname'];
$patient_number = $patient['patient_number'];
$gender = $patient['gender'];
$patient_insurance_number = $patient['patient_insurance_number'];
$inpatient = $patient['inpatient'];
$visit_type_name = $patient['visit_type_name'];

$today = date('jS F Y H:i a',strtotime(date("Y:m:d h:i:s")));
$visit_date = date('jS F Y',strtotime($this->accounts_model->get_visit_date($visit_id)));

//doctor
$doctor = $this->accounts_model->get_att_doctor($visit_id);

//served by
$served_by = $this->accounts_model->get_personnel($this->session->userdata('personnel_id'));


                   
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Sick Leave</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <style type="text/css">
			.receipt_spacing{letter-spacing:0px; font-size: 12px;}
			.center-align{margin:0 auto; text-align:center;}
			
			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:15px;
			}
			.row .col-md-12 th, .row .col-md-12 td {
				border:solid #000 !important;
				border-width:0 1px 1px 0 !important;
			}
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 2px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	  <strong>
                    <?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-Mail:<?php echo $contacts['email'];?>.<br> Tel : <?php echo $contacts['phone'];?><br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>SICK LEAVE DETAILS</strong>
            </div>
        </div>
        
        <!-- Patient Details -->
    	<div class="row receipt_bottom_border" style="margin-bottom: 10px;">
        	<div class="col-md-4 pull-left">
            	<div class="row">
                	<div class="col-md-12">
                    	
                    	<div class="title-item">NAME:</div>
                        
                    	<?php echo $patient_surname.' '.$patient_othernames; ?>
                    </div>
                </div>
            	
            
            </div>
            
        	<div class="col-md-4">
            	
            </div>
            
        	<div class="col-md-4 pull-right">
            	<div class="row">
                	<div class="col-md-12">
                    	<div class="title-item">Date:</div>
                        
                    	<?php echo $visit_date; ?>
                    </div>
                </div>
              
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="padding:20px;">
            	<?php
				$rs_pa = $this->dental_model->get_payment_info($visit_id);
				$sick_leave_days = 0;
				$sick_leave_start_date = date('Y-m-d');
				$sick_leave_note ='';
				if(count($rs_pa) >0){
					foreach ($rs_pa as $r2):
						# code...
						$sick_leave_note = $r2->sick_leave_note;
						$sick_leave_start_date = $r2->sick_leave_start_date;
						$sick_leave_days = $r2->sick_leave_days;

						// get the visit charge

					endforeach;

				}
				?>
				<h4><?php echo $sick_leave_note;?></h4>
            </div>
        </div>
        <?php
		$this->session->unset_userdata('selected_drugs');
		
		
        if($doctor == '-')
        {

        }
        else
        {
        ?>
            <div class="row" style="font-style:bold; font-size:11px;">
                <div class="col-md-10 pull-left">
                    <div class="col-md-6 pull-left">
                        <p>Approved By: </p>
                        <p><strong>Dr. <?php echo $doctor;?></strong></p>
                        <br>

                        <p>Sign : ................................ </p>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-md-12 ">
                <div class="col-md-6 pull-left">
                    	Prepared by: <?php echo $served_by;?> 
                </div>
                <div class="col-md-6 pull-right">
            			<?php echo date('jS M Y H:i a'); ?> Thank you
            	</div>
          	</div>
        	
        </div>
    </body>
    
</html>