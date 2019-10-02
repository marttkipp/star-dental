<?php
$patient_lifestyle = $this->nurse_model->get_patient_lifestyle2($visit_id);

$excersise_id = '';
$excersise_duration_id = '';
$sleep_id = '';
$meals_id = '';
$coffee_id = '';
$housing_id = '';
$education_id = '';
$lifestyle_diet = '';
$lifestyle_drugs = '';
$lifestyle_alcohol_percentage = '';
$lifestyle_alcohol_quantity = '';

if($patient_lifestyle->num_rows() > 0)
{
	$row = $patient_lifestyle->row();
	
	$excersise_id = $row->excersise_id;
	$excersise_duration_id = $row->excersise_duration_id;
	$sleep_id = $row->sleep_id;
	$meals_id = $row->meals_id;
	$coffee_id = $row->coffee_id;
	$housing_id = $row->housing_id;
	$education_id = $row->education_id;
	$lifestyle_diet = $row->lifestyle_diet;
	$lifestyle_drugs = $row->lifestyle_drugs;
	$lifestyle_alcohol_percentage = $row->lifestyle_alcohol_percentage;
	$lifestyle_alcohol_quantity = $row->lifestyle_alcohol_quantity;
}
 
// get all exercises
 $exercise_rs = $this->nurse_model->get_exercices_values();
 // end of all exercises

 // exercise duration
  $exerciseduration_rs = $this->nurse_model->get_exercices_duration_values();
 // end of exercixe duration

  // sleep rs duration
  $sleep_rs = $this->nurse_model->get_sleep_values();
 // end of rs duration

 // sleep rs duration
  $meal_rs = $this->nurse_model->get_values('meals','meals_id');
 // end of rs duration 


   // education rs duration
  $education_rs = $this->nurse_model->get_values('education','education_id');
 // end of rs duration 

   // sleep rs duration
  $housing_rs = $this->nurse_model->get_values('housing','housing_id');
 // end of rs duration 

   // sleep rs duration
  $coffee_rs = $this->nurse_model->get_values('coffee','coffee_id');
 // end of rs duration 


?>



<div class="row">
    <div class="col-md-12">
        <section class="panel panel-featured panel-featured-info">
			<header class="panel-heading">
				<h2 class="panel-title">Allergies</h2>
			</header>
			<div class="panel-body">
				<!-- vitals from java script -->
				<div id="medication"></div>
				<!-- end of vitals data -->
			</div>
		</section>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
			<header class="panel-heading">
				<h2 class="panel-title">Surgeries</h2>
			</header>
			<div class="panel-body">
				<!-- vitals from java script -->
				<div id="surgeries"></div>
				<!-- end of vitals data -->
			</div>
		</section>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
			<header class="panel-heading">
				<h2 class="panel-title">Family history</h2>
			</header>
			<div class="panel-body">
				<?php
					$v_data['patient_id'] = $this->reception_model->get_patient_id_from_visit($visit_id);
					$v_data['patient'] = $this->reception_model->patient_names2(NULL, $visit_id);
					$v_data['family_disease_query'] = $this->nurse_model->get_family_disease();
					$v_data['family_query'] = $this->nurse_model->get_family();
				?>
				<!-- vitals from java script -->
				<?php echo $this->load->view("patients/family_history", $v_data, TRUE); ?>
				<!-- end of vitals data -->
			</div>
		</section>
    </div>
</div>
