<?php


$search_title = $this->session->userdata('inpatient_title_search');
if(!empty($search_title))
{
	$title_ext = $search_title;
}
else
{
	$title_ext = 'Visit Report for '.date('Y-m-d');
}

$result = '';
//if users exist display them
if ($query->num_rows() > 0)
{
	$count = 0;
	
			
			$result .= 
				'
				<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					<thead>
						<tr>
							<th>#</th>
							<th>Patient No.</th>
							<th>Patient Name </th>
							<th>Gender</th>
							<th>Age</th>
							<th>Date of Admission</th>
							<th>Status</th>
							<th>D X</th>
							<th>RIP</th>
							<th>HC Time In</th>
							<th>HC Time Out</th>
						</tr>
					</thead>
					<tbody>		  
				';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}
				
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$patient_number = $row->patient_number;

				$strath_no = $row->strath_no;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$gender_id = $row->gender_id;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				// $first_visit_department = $this->reception_model->first_department($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$last_visit = $row->last_visit;
				// $department_name = $row->department_name;
				$branch_code = $row->branch_code;
				$department = $row->department;
				$inpatient = $row->inpatient;
				$rip_status = $row->rip_status;
				// $relative_code = $row->relative_code;
				$referral_reason = $row->referral_reason;
				$rip_status = $row->rip_status;
				$rip_date = $row->rip_date;
				$visit_date1 = $row->visit_date;
				// var_dump($difference);
				if($rip_status == 1  AND $visit_date1 >= $rip_date)
				{
					$rip_status = 'RIP';
				}
				else
				{
					$rip_status = '';
				}
				
				//branch Code
				// if($branch_code =='OSE')
				// {
					$branch_code = 'Main HC';
				// }
				// else
				// {
				// 	$branch_code = 'Oserengoni';
				// }
				
				$close_card = $row->close_card;
				if($close_card == 1)
				{
					$visit_time_out = date('jS M Y H:i a',strtotime($row->visit_time_out));
					$close_card_status = 'Discharged';
				}
				else if($close_card == 0)
				{
					$close_card_status = 'Patient Admitted';
					$visit_time_out = '-';
				}
				else 
				{
					$close_card_status = 'Discharged In';
					$visit_time_out = '-';
				}
				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);
				// var_dump($last_visit_rs); die();
				if($last_visit_rs->num_rows() > 1)
				{
					$last_visit_name = 'Re Visit';
				}
				
				else
				{
					$last_visit_name = 'First Visit';
				}

				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}


				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}
				

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				// end of the debit and credit notes


				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}
						
						else
						{
							$doctor = '-';
						}
					}
				}
				
				else
				{
					$doctor = '-';
				}


				if($inpatient == 0)
				{
					$patient_type = 'Outpatient';
				}
				else
				{
					$patient_type = 'Inpatient';
				}
				
				$count++;
				
				

				$age = $this->reception_model->calculate_age($patient_date_of_birth);


				$diagnosis_rs = $this->nurse_model->get_visit_diagnosis($visit_id);
				$diagnosis = '';
				if($diagnosis_rs->num_rows() > 0)
				{
					foreach ($diagnosis_rs->result() as $key_other) {
						# code...
						$diseases_name = $key_other->diseases_name;
						$diseases_code = $key_other->diseases_code;

						$diagnosis .= $diseases_name.' '.$diseases_code.' ';
					}
				}

				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$patient_number.'</td>
						<td>'.$patient_surname.' '.$patient_othernames.'</td>
						<td>'.$gender.'</td>
						<td>'.$age.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$close_card_status.'</td>
						<td>'.$diagnosis.'</td>
						<td>'.$rip_status.'</td>
						<td>'.$visit_time.'</td>
						<td>'.$visit_time_out.'</td>
						
					</tr>
				';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
}

else
{
	$result .= "There are no visits today";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Visit Report | <?php echo $title_ext;?></title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
		<script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>tableExport.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jquery.base64.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>html2canvas.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/sprintf.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jspdf.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/base64.js"></script>
        <style type="text/css">
			.receipt_spacing{letter-spacing:0px; font-size: 12px;}
			.center-align{margin:0 auto; text-align:center;}
			
			.receipt_bottom_border{border-bottom: #888888 medium solid;}
			.row .col-md-12 table {
				border:solid #000 !important;
				border-width:1px 0 0 1px !important;
				font-size:10px;
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
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
                </strong>
            </div>
        </div>
        
		<div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>VISIT REPORT (<?php echo $title_ext;?>)</strong>
            </div>
        </div>
        <div class="row receipt_bottom_border" style="margin-bottom: 10px;">
        	<div class="col-md-12">
        		<?php echo $result;?>
        	</div>
        </div>
		<a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});">XLS</a>
    </body>
    
</html>