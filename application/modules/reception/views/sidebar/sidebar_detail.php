<?php 
	$patient = $this->reception_model->patient_names2(NULL, $visit_id);
	// $visit_type = $patient['visit_type'];
	// $patient_type = $patient['patient_type'];
	// $patient_othernames = $patient['patient_othernames'];
	// $patient_surname = $patient['patient_surname'];
	// $patient_date_of_birth = $patient['patient_date_of_birth'];
	// $age = $this->reception_model->calculate_age($patient_date_of_birth);
	// $gender = $patient['gender'];
	$account_balance = $patient['account_balance'];
	// $phone_number = $patient['patient_phone_number'];
	// $patient_id = $patient['patient_id'];
	// $visit_type_name = $patient['visit_type_name'];
		// $v_data['patient_details'] = $this->reception_model->get_patient_data($patient_id);

		
// var_dump($query);die();
if($query->num_rows() > 0)
{
	foreach ($query->result() as $key => $value) {
		# code...
		// $patient_id = $value->patient_id;
		$patient_surname = $value->patient_surname;
		// $patient_first_name = $value->patient_first_name;
		$patient_othernames = $value->patient_othernames;
		$patient_phone1 = $value->patient_phone1;
		$patient_email = $value->patient_email;
		$patient_number = $value->patient_number;
		$patient_date_of_birth = $value->patient_date_of_birth;
		// $patient_age = $value->patient_age;
		$gender_id = $value->gender_id;
		$patient_age = $value->patient_age;
		$total_balance = 0;//$value->total_balance;

		// $account_balance = $patient['account_balance'];

		
		
			$patient_name = $patient_othernames.' '.$patient_surname;
		
	}
}

if($gender_id == 1)
{
	$gender = 'Male';
}
else if($gender_id == 2)
{
	$gender = 'Female';
}
else
{
	$gender = '';
}

if($patient_date_of_birth == "0000-00-00" OR empty($patient_date_of_birth))
{
   $patient_date_of_birth = '';

   if(!empty($patient_age))
   {
   	$age = $patient_age;
   }
   else
   {
   	 $age = '';
   }
   $dob = '';
}
else
{
	$dob= date('jS M Y',strtotime($patient_date_of_birth));
	$age = $this->reception_model->calculate_age($patient_date_of_birth);
}

$items = '';
$payment_info = '';
$next_appointment = '-';

$personnel_id = $this->session->userdata('personnel_id');
			
$is_dentist = $this->reception_model->check_personnel_department_id($personnel_id,4);
$account_notes_list = '';
if($visit_id > 0 AND !$is_dentist)
{

	// $all_departments_rs = $this->reception_model->get_patient_departments($visit_id);



	// if($all_departments_rs->num_rows() > 0)
	// {
	// 	foreach ($all_departments_rs->result() as $key => $value) {
	// 		# code...

	// 		$department_name = $value->department_name;
	// 		$created = $value->created;
	// 		$picked = $value->picked;
	// 		$personnel_onames = $value->personnel_onames;


	// 		if($picked == 0)
	// 		{
	// 			$color = 'red';
	// 		}
	// 		else if($picked == 1)
	// 		{
	// 			$color = 'green';
	// 		}

	// 		else if($picked == 2)
	// 		{
	// 			$color = 'orange';
	// 		}

	// 		$items .= ' <li class="'.$color.'">
	// 						<span class="title">'.$department_name.'</span>
	// 						<span class="description truncate">'.date('jS M Y H:i A',strtotime($created)).'. ~ '.$personnel_onames.'</span>
	// 					</li>';
	// 	}
	// }

		// var_dump($visit_id);die();
	$rs_rejection = $this->accounts_model->get_rejection_info($visit_id);

	if(count($rs_rejection) > 0) {
	  foreach ($rs_rejection as $r2):
	    # code...
	    $rejected_amount = $r2->rejected_amount;
	    $rejected_date = $r2->rejected_date;
	    $rejected_reason = $r2->rejected_reason;
	    $visit_type_id = $visit_type = $r2->visit_type;
	    $close_card = $r2->close_card;
	    $invoice_number = $r2->invoice_number;
	    $parent_visit = $r2->parent_visit;
	    $payment_info = $r2->payment_info;
	    $visit_date = $r2->visit_date;

	 //    $account_notes = $r2->account_notes;
		// $temparature = $r2->temparature;
		// $spo2 = $r2->spo2;
		// $pi = $r2->pi;
		// $pulse = $r2->pulse;
		// $account_notes_list = '<div>
		// 													<p><strong>Account Notes : </strong> '.$account_notes.'</p>
		// 													<p><strong>Temparature : </strong> '.$temparature.'</p>
		// 													<p><strong>spo2 : </strong> '.$spo2.'</p>
		// 													<p><strong>Pulse : </strong> '.$pulse.'</p>
		// 													<p><strong>P I % : </strong> '.$pi.'</p>

		// 												</div>';



	  endforeach;
	}

	$rs_pa = $this->nurse_model->get_prescription_notes_visit($visit_id);
	$visit_prescription = count($rs_pa);

	if($visit_prescription)
	{
		$items .= ' <li class="danger">
						<h4> Prescription </h4>
						<p class="text-light"><a href="'.site_url().'print-prescription/'.$visit_id.'" target="_blank" style="color:#fff;"> >> Print Prescription </a></p>
					</li>';
	}
	$branch_id = $this->session->userdata('branch_id');
	$where = 'visit.visit_id = appointments.visit_id AND visit.visit_delete = 0 AND appointments.appointment_delete = 0 AND visit.appointment_id = 1 AND patients.patient_id = visit.patient_id AND visit.patient_id = '.$patient_id.' AND appointments.appointment_date > "'.$visit_date.'" AND visit.branch_id = '.$branch_id.' AND visit.visit_id > '.$visit_id;
	$table = 'visit,appointments,patients';
	$next_appointment = $this->accounts_model->get_next_appointment($table, $where);

	if(!empty($next_appointment))
	{
		$next_appointment = date('jS M Y',strtotime($next_appointment));
	}
	else
	{
		$next_appointment = '-';
	}
	
}
// $payments = $this->accounts_model->get_patient_payments($patient_id);
// $invoices = $this->accounts_model->get_patient_invoice($patient_id);
// $balance = $invoices - $payments;




?>
<section class="card" style="margin-top: 10px;">
	<div class="card-body">
		<div class="thumb-info mb-3 " >
			<img src="https://via.placeholder.com/100" class="rounded img-fluid" alt="<?php echo $patient_name?>">
			<div class="thumb-info-title" >
				<span class="thumb-info-inner"><?php echo $patient_name?></span>
				<span class="thumb-info-type"><?php echo $patient_number;?></span>
			</div>
		</div>

		<div class="widget-toggle-expand mb-3">
			
			<div class="widget-content-expanded">
				<ul class="mt-3" style="list-style: none;padding: 0px !important;">
					<li><strong>Gender: </strong><br><?php echo $gender;?></li>
					<li><strong>Birth Date: </strong><br> <?php echo $dob;?></li>
					<li><strong>Age: </strong><br> <?php echo $age;?></li>

				</ul>
			</div>
		</div>


	</div>
</section>

<hr class="dotted short">
<h4 class="mb-3 mt-0">Patient Info</h4>
<ul class="simple-card-list mb-3">
	<li class="danger">
		<h4>Account Balance</h4>
		<h4 class="text-light"><?php echo number_format($account_balance,2)?></h4>
		<p class="text-light" style="color:#fff"><a style="color:#fff" href="<?php echo site_url();?>administration/individual_statement/<?php echo $patient_id?>/1" target="_blank">(view statement)</a></p>
	</li>
	
	<?php echo $items;?>
	
</ul>

<?php

if($is_dentist)
{

}
else
{
	?>
	<h4 class="mb-3 mt-4 pt-2"> Other Details </h4>
	<ul class="simple-bullet-list mb-3" style="height: 20vh;overflow-y: scroll;">
		<li class="red">
			<span class="title"><?php echo $payment_info;?></span>
			<span class="description truncate">Notes. ~ Doctor</span>
		</li>
		
		
	</ul>
	<?php

}
?>


