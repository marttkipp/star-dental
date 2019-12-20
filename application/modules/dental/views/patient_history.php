<!-- search -->
<?php 

		
		 $patient_id = $this->nurse_model->get_patient_id($visit_id);
		// this is it

		$where = 'visit.patient_id = patients.patient_id AND visit.visit_id <= '.$visit_id.' AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.`patient_id`='.$patient_id;
		
		
		$table = 'visit,patients';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'dental/patient_card/'.$visit_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 4;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $v_data["links"] = $links2 = $this->pagination->create_links();
		$query = $this->nurse_model->get_all_patient_history($table, $where, $config["per_page"], $page);
		
	
	 	$number_items = $query->num_rows();
		
?>
<!-- end search -->
 
<div class="row">
    <div class="col-md-12">

      <!-- Widget -->
      <div class="widget boxed">
        <!-- Widget head -->
        <div class="widget-head">
          <h4 class="pull-left"><i class="icon-reorder"></i>Patient History</h4>
          <div class="widget-icons pull-right">
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
            <a href="#" class="wclose"><i class="icon-remove"></i></a>
          </div>
          <div class="clearfix"></div>
        </div>             

        <!-- Widget content -->
        <div class="widget-content">
          <div class="padd">
          
			<?php
					$search = $this->session->userdata('visit_search');
					
					if(!empty($search))
					{
						echo '<a href="'.site_url().'/nurse/close_queue_search" class="btn btn-warning">Close Search</a>';
					}
					$result = '';
				
					//if users exist display them
					if ($query->num_rows() > 0)
					{
						$count = $page;
						
						$result .= 
							'
								<table class="table table-hover table-bordered ">
								  <thead>
									<tr>
									  <th>#</th>
									  <th>Visit Date</th>
									  <th>Doctors Notes</th>
									  <th>Doctor</th>
									  <th>Visit</th>
									</tr>
								  </thead>
								  <tbody>
							';
						
						$personnel_query = $this->personnel_model->retrieve_personnel();
						$count = 1;
						foreach ($query->result() as $row)
						{
							$past_visit_date = $row->visit_date;
							$visit_date = date('jS M Y',strtotime($row->visit_date));
							$visit_time = date('H:i a',strtotime($row->visit_time));
							$visit_id1 = $row->visit_id;
							$waiting_time = $this->nurse_model->waiting_time($visit_id1);
							$patient_id = $row->patient_id;
							$personnel_id = $row->personnel_id;
							$dependant_id = $row->dependant_id;
							$strath_no = $row->strath_no;
							$created_by = $row->created_by;
							$modified_by = $row->modified_by;
							$visit_type_id = $row->visit_type_id;
							$visit_type = $row->visit_type;
							$created = $row->patient_date;
							$last_modified = $row->last_modified;
							$last_visit = $row->last_visit;
							
							
								$patient_type = $this->reception_model->get_patient_type($visit_type_id);
								
								if($visit_type == 3)
								{
									$visit_type = 'Other';
								}
								else if($visit_type == 4)
								{
									$visit_type = 'Insurance';
								}
								else
								{
									$visit_type = 'General';
								}
								
								$patient_othernames = $row->patient_othernames;
								$patient_surname = $row->patient_surname;
								$title_id = $row->title_id;
								$patient_date_of_birth = $row->patient_date_of_birth;
								$civil_status_id = $row->civil_status_id;
								$patient_address = $row->patient_address;
								$patient_post_code = $row->patient_postalcode;
								$patient_town = $row->patient_town;
								$patient_phone1 = $row->patient_phone1;
								$patient_phone2 = $row->patient_phone2;
								$patient_email = $row->patient_email;
								$patient_national_id = $row->patient_national_id;
								$religion_id = $row->religion_id;
								$gender_id = $row->gender_id;
								$patient_kin_othernames = $row->patient_kin_othernames;
								$patient_kin_sname = $row->patient_kin_sname;
								$relationship_id = $row->relationship_id;
							
							
							//creators and editors
							if($personnel_query->num_rows() > 0)
							{
								$personnel_result = $personnel_query->result();
								
								foreach($personnel_result as $adm)
								{
									$personnel_id2 = $adm->personnel_id;
									
									if($personnel_id == $personnel_id2)
									{
										$doctor = $adm->personnel_fname;
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

							$item_to_display2 ='';
							$item_to_display = '';

							//  start of plan
							$plan_rs = $this->nurse_model->get_plan($visit_id1);
							$plan_num_rows = count($plan_rs);
							$plan = "";
							if($plan_num_rows > 0){
								foreach ($plan_rs as $key_plan):
									$visit_plan = $key_plan->visit_plan;
								endforeach;
								$plan .="".$visit_plan."";
								
							}
							else
							{
								$plan .="-";
							}
							// end of plan

							// start of diagnosis todays records 
							$notes_rs = $this->nurse_model->get_doctors_patient_notes($visit_id1);
							$notes_num_rows = count($notes_rs);
							// echo $notes_num_rows;
							// $todays_new_notes = '<form id="opening_form" method="POST" class="form-horizontal">';
							$todays_new_notes = form_open("dental/save-new-notes/".$visit_id, array("class" => "form-horizontal"));
							$notes = "";

							$todays_notes = "";
							$doctor_notes ="";
							if($notes_num_rows > 0){
								foreach ($notes_rs as $notes_key):
									$doctor_notes_id = $notes_key->doctor_notes_id;
									$doctor_notes = $notes_key->doctor_notes;
									$todays_notes .="".$doctor_notes."";
								endforeach;
							}
							else
							{
								$notes .= "-";
							}
							// end of diagnosis
							

							//  notes 
							$notes = '';
							$get_medical_rs = $this->nurse_model->get_hpco_notes($visit_id1);
							$hpco_num_rows = count($get_medical_rs);
							// echo $hpco_num_rows;

							if($hpco_num_rows > 0){
								foreach ($get_medical_rs as $key2) :
									$hpco = $key2->hpco_description;
									$todays_new_notes .= '
										                	<div class="col-md-12">
											                	<div class="form-group">
											                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
											                		<div class="col-lg-8">
																		<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$hpco.'</textarea>
																	</div>
																</div>
															</div>
														';
								endforeach;

								$notes .= '<div class="border-bottom"><span class="bold">Presenting Complaint : </span> '.$hpco.' </div><br>';
								
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '	
													
													<div class="row">
														<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
										                		<div class="col-lg-8">
																	<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div><br/>';
							}





							
							$get_histories_rs = $this->nurse_model->get_histories_notes($visit_id1);
							$history_num_rows = count($get_histories_rs);
							//echo $history_num_rows;

							if($history_num_rows > 0){
								foreach ($get_histories_rs as $key3) :
									$past_dental_history = $key3->past_dental_history;
									$past_medical_history = $key3->past_medical_history;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">PM Hx</span> '.$past_medical_history.'</div>';
								$notes .= '<div class="border-bottom"><span class="bold">PD Hx</span> '.$past_dental_history.'</div>';

								$todays_new_notes .= '
													<br/>
													<div class="row">
									                	<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">PD Hx : </label>
										                		<div class="col-lg-8">
										                			<p>-Any previous dental treatment </p>
										                			<p>-Any adverse reactions & experience to dental Rx </p>
										                			<p>- Frequency of brushing </p>
										                			<p>- Food packing, bleeding gums,sensitivity </p>
																	<textarea id="past_dental_hx'.$visit_id1.'" name="past_dental_hx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$past_dental_history.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													<div class="row">

														<div class="col-md-12">
															<div class="form-group">
										                		<label class="col-lg-2 control-label">PM Hx : </label>
																<div class="col-lg-8">
																	<p>-Allergy to any medicine </p>
										                			<p>- Currently on any medication</p>
										                			<p>- Any chronic disease/History of radiation </p>
										                			<p>- Bleeding gums,sensitivity </p>
																	<textarea id="past_medical_hx'.$visit_id1.'" name="past_medical_hx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$past_medical_history.'</textarea>
																</div>
															</div>
														</div>
													</div>
												';
							}
							else
							{
								$notes .='';
								$todays_new_notes .= '
													<br/>
													<div class="row">
									                	<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">PD Hx : </label>
										                		<div class="col-lg-8">
										                			<p>-Any previous dental treatment </p>
										                			<p>-Any adverse reactions & experience to dental Rx </p>
										                			<p>- Frequency of brushing </p>
										                			<p>- Food packing, bleeding gums,sensitivity </p>
																	<textarea id="past_dental_hx'.$visit_id1.'" name="past_dental_hx'.$visit_id1.'"  rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
										                		<label class="col-lg-2 control-label">PM Hx : </label>
																<div class="col-lg-8">
																	<p>- Allergy to any medicine </p>
										                			<p>- Currently on any medication</p>
										                			<p>- Any chronic disease/History of radiation </p>
										                			<p>- Bleeding gums,sensitivity </p>
																	<textarea id="past_medical_hx'.$visit_id1.'" name="past_medical_hx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div> <br/>';
							}


							// get the medicals this s
							$get_general_rs = $this->nurse_model->get_general_exam_notes($visit_id1);
							$general_num_rows = count($get_general_rs);
							// echo $general_num_rows;

							if($general_num_rows > 0){
								foreach ($get_general_rs as $key2) :
									$general_exam = $key2->general_exam_description;
									$todays_new_notes .= '
										                	<div class="col-md-12">
											                	<div class="form-group">
											                		<label class="col-lg-2 control-label"> General Exam: </label>
											                		<div class="col-lg-8">
											                			<p>(Pallour , Jaundice, Swelling , Fever , Lymphadenopathy)</p>
																		<textarea id="general_exam'.$visit_id.'" name="general_exam'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$general_exam.'</textarea>
																	</div>
																</div>
															</div>
														';
								endforeach;

								$notes .= '<div class="border-bottom"><span class="bold">General Exam</span> '.$general_exam.',/div>';
								
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '	
													
													<div class="row">
														<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">General Exam: </label>
										                		<div class="col-lg-8">
										                			<p>(Pallour , Jaundice, Swelling , Fever , Lymphadenopathy)</p>
																	<textarea id="general_exam'.$visit_id.'" name="general_exam'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div><br/>';
							}

							$get_oc_rs = $this->nurse_model->get_oc_notes($visit_id1);
							$oc_num_rows = count($get_oc_rs);
							//echo $oc_num_rows;

							if($oc_num_rows > 0){
								foreach ($get_oc_rs as $key2) :
									$soft_tissue = $key2->soft_tissue;
									$decayed = $key2->decayed;
									$filled = $key2->filled;
									$missing = $key2->missing;
									$general = $key2->general;
									$other = $key2->other;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Soft Tissue</span> '.$soft_tissue.'</div>';
								$notes .= '<div class="border-bottom"><span class="bold">Hard Tissue</span>';
								$notes .= '<div style="margin-left:20px">
												<p><span class="bold">General : </span> '.$general.'</p>';
								$notes .= '		<p><span class="bold">Decayed : </span> '.$decayed.'</p>';
								$notes .= '		<p><span class="bold">Filled :</span> '.$filled.'</p>';
								$notes .= '		<p><span class="bold">Missing :</span> '.$missing.'</p>';
								$notes .= '		<p><span class="bold">Other : </span> '.$other.'</p>
											</div>
											</div>';
								$todays_new_notes .= '
													<br/>
													<div class="row">
									                	<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">Soft Tissue : </label>
										                		<div class="col-lg-8">
																	<textarea id="soft_tissue'.$visit_id1.'" name="soft_tissue'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$soft_tissue.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
										                		<label class="col-lg-2 control-label">Hard Tissue : </label>
										                		<div class="col-md-10">
											                		<div class="row">
											                			<div class="col-md-12">
												                			<label class="col-lg-2 control-label">General : </label>
																			<div class="col-lg-8">
																				<textarea id="general'.$visit_id1.'" name="general'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$general.'</textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
											                			<div class="col-md-12">
												                			<label class="col-lg-2 control-label">Decayed : </label>
																			<div class="col-lg-8">
																				<textarea id="decayed'.$visit_id1.'" name="decayed'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$decayed.'</textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<label class="col-lg-2 control-label">Missing : </label>
																			<div class="col-lg-8">
																				<textarea id="missing'.$visit_id1.'" name="missing'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$missing.'</textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<label class="col-lg-2 control-label">Filled : </label>
																			<div class="col-lg-8">
																				<textarea id="filled'.$visit_id1.'" name="filled'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$filled.'</textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
												                			<label class="col-lg-2 control-label">Others : </label>
																			<div class="col-lg-8">
																				<p>(Impacted, Abrasion, Florosis, Traumatic bite) </p>
																				<textarea id="others'.$visit_id1.'" name="others'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$other.'</textarea>
																			</div>
																		</div>
																	</div>

																</div>
															</div>
														</div>
													</div>
													<br>				
												';
							}
							else
							{
								$notes .='';
								$todays_new_notes .='
													<div class="row">
									                	<div class="col-md-12">
										                	<div class="form-group">
										                		<label class="col-lg-2 control-label">Soft Tissue : </label>
										                		<div class="col-lg-8">
																	<textarea id="soft_tissue'.$visit_id1.'" name="soft_tissue'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
										                		<label class="col-lg-2 control-label">Hard Tissue : </label>
										                		<div class="col-md-10">
											                		<div class="row">
											                			<div class="col-md-12">
												                			<label class="col-lg-2 control-label">General : </label>
																			<div class="col-lg-8">
																				<textarea id="general'.$visit_id1.'" name="general'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
											                			<div class="col-md-12">
												                			<label class="col-lg-2 control-label">Decayed : </label>
																			<div class="col-lg-8">
																				<textarea id="decayed'.$visit_id1.'" name="decayed'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<label class="col-lg-2 control-label">Missing : </label>
																			<div class="col-lg-8">
																				<textarea id="missing'.$visit_id1.'" name="missing'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<label class="col-lg-2 control-label">Filled : </label>
																			<div class="col-lg-8">
																				<textarea id="filled'.$visit_id1.'" name="filled'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																			</div>
																		</div>
																	</div>
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
												                			<label class="col-lg-2 control-label">Others : </label>

																			<div class="col-lg-8">
																				<p>(Impacted, Abrasion, Florosis, Traumatic bite) </p>
																				<textarea id="others'.$visit_id1.'" name="others'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<br>
													';
							}
							$get_oral_examination_rs = $this->nurse_model->get_oe_notes($visit_id1);
							$oe_num_rows = count($get_oral_examination_rs);
							if($oe_num_rows > 0){
								foreach ($get_oral_examination_rs as $key3) :
									$oral_examination = $key3->oe_description;
									$item_to_display2 .= '
										                	<div class="col-md-12">
											                	<div class="form-group">
											                		<label class="col-lg-2 control-label">Oral Examination : </label>
											                		<div class="col-lg-8">
																		<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$oral_examination.'</textarea>
																	</div>
																</div>
															</div>
														';
								endforeach;

								$notes .= '<div class="border-bottom"><span class="bold">Oral Examination : </span> '.$oral_examination.'</div>';
								
							}
							else
							{
								$notes .= '';
							}
							$get_inves_rs = $this->nurse_model->get_investigations_notes($visit_id1);
							$invest_num_rows = count($get_inves_rs);
							//echo $invest_num_rows;

							if($invest_num_rows > 0){
								foreach ($get_inves_rs as $key4) :
									$investigation = $key4->investigation;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Investigations : </span> '.$investigation.'</div>';
								$todays_new_notes .= '
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Investigations : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$investigation.'</textarea>
																</div>
															</div>
														</div>
													</div>
												';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Investigations : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div> <br/>';
							}
							$get_occlusal_exam_rs = $this->nurse_model->get_occlusal_exam_notes($visit_id1);
							$occlusal_num_rows = count($get_occlusal_exam_rs);
							//echo $occlusal_num_rows;

							if($occlusal_num_rows > 0){
								foreach ($get_occlusal_exam_rs as $key4) :
									$occlusal_exam = $key4->occlusal_exam_description;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Occlusal Exam : </span> '.$occlusal_exam.'</div>';
								$todays_new_notes .= '
													<br/>
														<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Occlusal Exam : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="occlusal_exam'.$visit_id1.'" name="occlusal_exam'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$occlusal_exam.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Occlusal Exam : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="occlusal_exam'.$visit_id1.'" name="occlusal_exam'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
														</div>
														<br/>';
							}

							$get_findings_rs = $this->nurse_model->get_findings_notes($visit_id1);
							$find_num_rows = count($get_findings_rs);
							//echo $find_num_rows;

							if($find_num_rows > 0){
								foreach ($get_findings_rs as $key4) :
									$findings = $key4->finding_description;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Findings : </span> '.$findings.'</div>';
								$todays_new_notes .= '
													<br/>
														<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Findings : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="findings'.$visit_id1.'" name="findings'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$findings.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '
													<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Findings : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="findings'.$visit_id1.'" name="findings'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
														</div>
														<br/>';
							}

							$get_plan_rs = $this->nurse_model->get_plan_notes($visit_id1);
							$plannum_rows = count($get_plan_rs);
							//echo $plannum_rows;

							if($plannum_rows > 0){
								foreach ($get_plan_rs as $key2) :
									$plan_description = $key2->plan_description;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Plan : </span> '.$plan_description.'</div>';
								$todays_new_notes .='<br/>
														<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Plan Description : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="plan'.$visit_id1.'" name="plan'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$plan_description.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>			
												';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
															<label class="col-lg-2 control-label"> Plan  : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="plan'.$visit_id1.'" name="plan'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													';
							}

							$get_rx_rs = $this->nurse_model->get_rxdone_notes($visit_id1);
							$rs_num_rows = count($get_rx_rs);
							//echo $rs_num_rows;

							if($rs_num_rows > 0){
								foreach ($get_rx_rs as $key6) :
									$rx_description = $key6->rx_description;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">Rx Done : </span> '.$rx_description.'</div>';
								$todays_new_notes .= '<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">Rx Done : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$rx_description.'</textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>	
												';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">RX Done : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>
													';
							}
							$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id1);
							$tca_num_rows = count($get_tca_rs);
							//echo $tca_num_rows;



							if($tca_num_rows > 0){
								foreach ($get_tca_rs as $key7):
									$tca_description = $key7->tca_description;
								endforeach;
								$notes .= '<div class="border-bottom"><span class="bold">TCA : </span> '.$tca_description.'</div>';
								$todays_new_notes .= '<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">TCA : </label>
																<div class="col-lg-8">	
																	<p> (Please enumerate the items)</p>
																	<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$tca_description.'</textarea>
																</div>
															</div>
														</div>
													</div>	
												';
							}
							else
							{
								$notes .= '';
								$todays_new_notes .= '<br/>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="col-lg-2 control-label">TCA : </label>
																<div class="col-lg-8">
																	<p> (Please enumerate the items)</p>
																	<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																</div>
															</div>
														</div>
													</div>
													<br/>	
											';
							}
								$todays_new_notes .= '<br/><div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<div class="center-align col-lg-12 ">
																	  <!--<a hred="#" class="btn btn-large btn-info" onclick="save_all_soap('.$visit_id1.')">Save Doctors Notes</a>-->
																	  <input type="hidden" name="visit_id_form" value="'.$visit_id.'">
																	  <button type="submit" class="btn btn-large btn-info">Save doctor\'s notes</button>
																</div>
															</div>
														</div>
													</div>';
								$todays_new_notes .= form_close();

							
							if($visit_id == $visit_id1 && $number_items > 1 && $count == $number_items)
							{
								// means that there are previous visits already done 
								// this should only include the small additional notes text area
								$visit_personnel_id = $this->session->userdata('personnel_id');
								if($visit_personnel_id == $personnel_id OR $visit_personnel_id == 0)
								{
									
									$changer = '<td>	
													<a  class="btn btn-sm btn-success" id="open_visit'.$visit_id.'" onclick="get_visit_trail('.$visit_id.');">Add visit notes</a>
													<a  class="btn btn-sm btn-danger" id="close_visit'.$visit_id.'" style="display:none;" onclick="close_visit_trail('.$visit_id.');">Close tab</a>
												</td>';
								}
								else
								{
									$changer = 'No action to perform';
								}

									$item_to_display2 = '';
									$get_medical_rs = $this->nurse_model->get_hpco_notes($visit_id);
									$hpco_num_rows = count($get_medical_rs);
									// $todays_notes = $doctor_notes;
									if($hpco_num_rows > 0){
										foreach ($get_medical_rs as $key2) :
											$hpco = $key2->hpco_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
													                		<div class="col-lg-8">
																				<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$hpco.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Presenting Complaint</span> '.$hpco.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
												                		<div class="col-lg-8">
																			<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}
									$get_oral_examination_rs = $this->nurse_model->get_oe_notes($visit_id);
									$oe_num_rows = count($get_oral_examination_rs);
									if($oe_num_rows > 0){
										foreach ($get_oral_examination_rs as $key3) :
											$oral_examination = $key3->oe_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
													                		<div class="col-lg-8">
																				<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$oral_examination.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Oral Examination : </span> '.$oral_examination.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Oral Examination : </label>
												                		<div class="col-lg-8">
																			<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}

									$get_inves_rs = $this->nurse_model->get_investigations_notes($visit_id);
									$invest_num_rows = count($get_inves_rs);
									//echo $invest_num_rows;

									if($invest_num_rows > 0){
										foreach ($get_inves_rs as $key4) :
											$investigation = $key4->investigation;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Investigations : </span> '.$investigation.'</div>';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$investigation.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div> <br/>';
									}

									$get_rx_rs = $this->nurse_model->get_rxdone_notes($visit_id);
									$rs_num_rows = count($get_rx_rs);
									//echo $rs_num_rows;

									if($rs_num_rows > 0){
										foreach ($get_rx_rs as $key6) :
											$rx_description = $key6->rx_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Rx Done : </span> '.$rx_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Rx Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$rx_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">RX Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>
															';
									}
									$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id);
									$tca_num_rows = count($get_tca_rs);
									//echo $tca_num_rows;



									if($tca_num_rows > 0){
										foreach ($get_tca_rs as $key7):
											$tca_description = $key7->tca_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">TCA : </span> '.$tca_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">	
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$tca_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
													';
									}

									$notes = $todays_notes;
									// $item_to_display2 = '<div class="col-md-12">
									// 							<div class="form-group">
									// 								<div class="col-lg-2">
									// 									Todays Visit 
									// 								</div>
									// 								<div class="col-lg-8">
									// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control cleditor col-md-6" >'.$notes.'</textarea>
									// 								</div>
									// 							</div>
									// 						</div>';


							}
							else if($visit_id == $visit_id1 && $number_items == 1 && $count == $number_items)
							{
								// this is the first visit
								$visit_personnel_id = $this->session->userdata('personnel_id');
								if($visit_personnel_id == $personnel_id OR $visit_personnel_id == 0)
								{
									$changer = '<td>
														<a  class="btn btn-sm btn-success" id="open_todays_visit'.$visit_id.'" onclick="get_patient_first_trail('.$visit_id.');">Open patient first card</a>
														<a  class="btn btn-sm btn-danger" id="close_todays_visit'.$visit_id.'" style="display:none;" onclick="close_patient_first_trail('.$visit_id.');">Close patient first card</a>
													</td>';
								}
								else
								{
									$changer ='';
								}
								$notes = $notes;
								$item_to_display = $todays_new_notes;
							}
							else if($visit_id > $visit_id1 && $number_items > $count AND $number_items > 1)
							{

								// past visit checker 
									// if($count < ($number_items -1))
									// {
									// 	$changer = '<td></td>';

									// }
									// else
									// {
										// $visit_personnel_id = $this->session->userdata('personnel_id');
										// if($visit_personnel_id == $personnel_id)
										// {

											// $changer = '<td>	
											// 		<a  class="btn btn-sm btn-success" id="open_visit'.$visit_id.'" onclick="get_visit_trail('.$visit_id.');">Add visit notes</a>
											// 		<a  class="btn btn-sm btn-danger" id="close_visit'.$visit_id.'" style="display:none;" onclick="close_visit_trail('.$visit_id.');">Close tab</a>
											// 	</td>';
										$changer = '<td>No action to perform </td>';

										// }
										// else
										// {
										// 	$changer = '<td>You are not allowed to perform an action</td>';
										// }
										
									// }

									$item_to_display2 = '';
									$get_medical_rs = $this->nurse_model->get_hpco_notes($visit_id1);
									$hpco_num_rows = count($get_medical_rs);
									// $todays_notes = $doctor_notes;
									if($hpco_num_rows > 0){
										foreach ($get_medical_rs as $key2) :
											$hpco = $key2->hpco_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
													                		<div class="col-lg-8">
																				<textarea id="hpco'.$visit_id1.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$hpco.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Presenting Complaint</span> '.$hpco.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
												                		<div class="col-lg-8">
																			<textarea id="hpco'.$visit_id1.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}


									$get_oral_examination_rs = $this->nurse_model->get_oe_notes($visit_id1);
									$oe_num_rows = count($get_oral_examination_rs);
									if($oe_num_rows > 0){
										foreach ($get_oral_examination_rs as $key3) :
											$oral_examination = $key3->oe_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Oral Examination : </label>
													                		<div class="col-lg-8">
																				<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$oral_examination.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Oral Examination :</span> '.$oral_examination.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Oral Examination: </label>
												                		<div class="col-lg-8">
																			<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}

									$get_inves_rs = $this->nurse_model->get_investigations_notes($visit_id1);
									$invest_num_rows = count($get_inves_rs);
									//echo $invest_num_rows;


									if($invest_num_rows > 0){
										foreach ($get_inves_rs as $key4) :
											$investigation = $key4->investigation;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Investigations : </span> '.$investigation.'</div>';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$investigation.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div> <br/>';
									}


						

									$get_rx_rs = $this->nurse_model->get_rxdone_notes($visit_id1);
									$rs_num_rows = count($get_rx_rs);
									//echo $rs_num_rows;

									if($rs_num_rows > 0){
										foreach ($get_rx_rs as $key6) :
											$rx_description = $key6->rx_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Rx Done : </span> '.$rx_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Rx Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$rx_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">RX Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>
															';
									}
									$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id1);
									$tca_num_rows = count($get_tca_rs);
									//echo $tca_num_rows;



									if($tca_num_rows > 0){
										foreach ($get_tca_rs as $key7):
											$tca_description = $key7->tca_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">TCA : </span> '.$tca_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">	
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$tca_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
													';
									}
									
									if($rs_num_rows == 0 && $visit_id == $visit_id1)
									{
										$notes = $todays_notes;
									}
									else if($rs_num_rows == 0 && $visit_id != $visit_id1)
									{
										// echo "sdjkasda";
										$notes = $todays_notes;
									}
									else
									{
										$notes = $notes;
									}
									
									// $item_to_display2 = '<div class="col-md-12">
									// 							<div class="form-group">
									// 								<div class="col-lg-2">
									// 									Todays Visit
									// 								</div>
									// 								<div class="col-lg-8">
									// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control cleditor col-md-6" >'.$todays_notes.'</textarea>
									// 								</div>
									// 							</div>
									// 						</div>';

								
							}
							else if($visit_id > $visit_id1 && $number_items == $count AND $number_items > 1)
							{
								$notes = $notes;
							}
							else
							{
								$notes .= $todays_notes;
								$visit_personnel_id = $this->session->userdata('personnel_id');
								if($visit_personnel_id == $personnel_id OR $visit_personnel_id == 0)
								{
									$changer = '<td>
														<a  class="btn btn-sm btn-success" id="open_visit'.$visit_id.'" onclick="get_visit_trail('.$visit_id.');">Add visit notes </a>
														<a  class="btn btn-sm btn-danger" id="close_visit'.$visit_id.'" style="display:none;" onclick="close_visit_trail('.$visit_id.');">Close tab</a>
													</td>';
								}
								else
								{
									$changer = 'No action to perform';
								}
								// $item_to_display2 = '<div class="row">
								// 						<div class="col-md-12">
								// 							<div class="form-group">
								// 								<div class="col-lg-2">
								// 									Todays Visit
								// 								</div>
								// 								<div class="col-lg-8">
								// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control cleditor col-md-6" ></textarea>
								// 								</div>
								// 							</div>
								// 						</div>
								// 					</div><br>';
									$item_to_display2 = '';
									$get_medical_rs = $this->nurse_model->get_hpco_notes($visit_id);
									$hpco_num_rows = count($get_medical_rs);
									// $todays_notes = $doctor_notes;
									if($hpco_num_rows > 0){
										foreach ($get_medical_rs as $key2) :
											$hpco = $key2->hpco_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Presenting Complaint : </label>
													                		<div class="col-lg-8">
																				<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$hpco.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Presenting Complaint</span> '.$hpco.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">P/C : </label>
												                		<div class="col-lg-8">
																			<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}
									$get_oral_examination_rs = $this->nurse_model->get_oe_notes($visit_id);
									$oe_num_rows = count($get_oral_examination_rs);
									if($oe_num_rows > 0){
										foreach ($get_oral_examination_rs as $key3) :
											$oral_examination = $key3->oe_description;
											$item_to_display2 .= '
												                	<div class="col-md-12">
													                	<div class="form-group">
													                		<label class="col-lg-2 control-label">Oral Examination : </label>
													                		<div class="col-lg-8">
																				<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$oral_examination.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$todays_notes .= '<div class="border-bottom"><span class="bold">Oral Examination :</span> '.$oral_examination.'</div>';
										
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Oral Examination : </label>
												                		<div class="col-lg-8">
																			<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div><br/>';
									}

									$get_inves_rs = $this->nurse_model->get_investigations_notes($visit_id);
									$invest_num_rows = count($get_inves_rs);
									//echo $invest_num_rows;

									if($invest_num_rows > 0){
										foreach ($get_inves_rs as $key4) :
											$investigation = $key4->investigation;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Investigations : </span> '.$investigation.'</div>';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$investigation.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div> <br/>';
									}
							
									$get_rx_rs = $this->nurse_model->get_rxdone_notes($visit_id);
									$rs_num_rows = count($get_rx_rs);
									//echo $rs_num_rows;

									if($rs_num_rows > 0){
										foreach ($get_rx_rs as $key6) :
											$rx_description = $key6->rx_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">Rx Done : </span> '.$rx_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Rx Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$rx_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">RX Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>
															';
									}
									$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id);
									$tca_num_rows = count($get_tca_rs);
									//echo $tca_num_rows;



									if($tca_num_rows > 0){
										foreach ($get_tca_rs as $key7):
											$tca_description = $key7->tca_description;
										endforeach;
										$todays_notes .= '<div class="border-bottom"><span class="bold">TCA : </span> '.$tca_description.'</div>';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">	
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" >'.$tca_description.'</textarea>
																		</div>
																	</div>
																</div>
															</div>	
														';
									}
									else
									{
										$todays_notes .= '';
										$item_to_display2 .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control cleditor col-md-6" ></textarea>
																		</div>
																	</div>
																</div>
															</div>
															<br/>	
													';
									}

								
							}

							$result .= 
								'
									<tr>
										<td>'.$count.'</td>
										<td>'.$visit_date.'</td>
										<td>'.$notes.'</td>
										<td>'.$doctor.'</td>
										'.$changer.'

									</tr> 
								';
								$result .=
									'<tr id="visit_trail'.$visit_id.'" style="display:none;">
										<td colspan="5">';
											$result .= form_open("dental/save-current-notes/".$visit_id, array("class" => "form-horizontal"));
											$result .= '
														<div class="row">
															'.$item_to_display2.'
																	
														</div>
														<br/>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<div class="center-align col-lg-12 ">
																		<button class="btn btn-info btn-lg" type="submit"> Save Doctors Current Notes</button>
																	</div>
																</div>
															</div>
														</div>';
											 $result .= form_close();
									$result .= '		 
										</td>
									</tr>';
							
							 

							$result .=
									'<tr id="todays_trail'.$visit_id.'" style="display:none;">
										<td colspan="5">
											'.$item_to_display.'
											
											 
										</td>
									</tr>';
								$count++;


						}
					
						 
							
						
						$result .= 
						'
									  </tbody>
									</table>
						';
					}
					
					else
					{
						$result .= "There are no patients";
					}
					
						echo $result;
					

			?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links2)){echo $links2;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->
          <hr>

        <?php 

        	$v_data['patient_id'] = $patient_id;
        	$v_data['past_visit_date'] = $past_visit_date;

        	echo $this->load->view("diary", $v_data, TRUE);

        ?>
        <div class="row">
        	<div class="col-md-12">
        		<div id="patient_schedule"></div>
        	</div>
        </div>

      </div>

     
    </div>
  </div>

  <script>
	  function patient_history_popup(visit_id,mike) {
	    var config_url = $('#config_url').val();
	    window.open( config_url+"/nurse/patient_card/"+visit_id+"/"+mike, "myWindow", "status = 1, height = auto, width = 100%, resizable = 0" )
		}

  </script>


  <script type="text/javascript">
  
  	function get_patient_first_trail(visit_id)
  	{
  		var myTarget3 = document.getElementById("todays_trail"+visit_id);
		var button = document.getElementById("open_todays_visit"+visit_id);
		var button2 = document.getElementById("close_todays_visit"+visit_id);

		myTarget3.style.display = '';
		button.style.display = 'none';
		button2.style.display = '';
  	}

  	function close_patient_first_trail(visit_id)
  	{
  		var myTarget3 = document.getElementById("todays_trail"+visit_id);
		var button = document.getElementById("open_todays_visit"+visit_id);
		var button2 = document.getElementById("close_todays_visit"+visit_id);

		myTarget3.style.display = 'none';
		button.style.display = '';
		button2.style.display = 'none';
  	}
	function get_visit_trail(visit_id){

		var myTarget2 = document.getElementById("visit_trail"+visit_id);
		var button = document.getElementById("open_visit"+visit_id);
		var button2 = document.getElementById("close_visit"+visit_id);

		myTarget2.style.display = '';
		button.style.display = 'none';
		button2.style.display = '';
	}
	function close_visit_trail(visit_id){

		var myTarget2 = document.getElementById("visit_trail"+visit_id);
		var button = document.getElementById("open_visit"+visit_id);
		var button2 = document.getElementById("close_visit"+visit_id);

		myTarget2.style.display = 'none';
		button.style.display = '';
		button2.style.display = 'none';
	}

	function save_doctors_current_notes(visit_id)
	{
		var config_url = $('#config_url').val();
        var data_url = config_url+"/nurse/save_doctor_notes/"+visit_id;
        //window.alert(data_url);
         var doctor_notes_rx = $('#todays_notes'+visit_id).val();//document.getElementById("vital"+vital_id).value;
        $.ajax({
        type:'POST',
        url: data_url,
        data:{notes: doctor_notes_rx},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
           window.alert("Notes successfully saved");
           window.location.href = config_url+"/dental/patient_card/"+visit_id;
           
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving rx
	}

	$( document ).on( "submit", 'form#opening_form', function(e) 
	{
		e.preventDefault();
	  	var visit_id = $(this).find('input[name="visit_id_form"]').val();


      // start of saving rx
        var config_url = $('#config_url').val();
        var data_url = config_url+"/nurse/save_rx/"+visit_id;
        //window.alert(data_url);
         var doctor_notes_rx = $(this).find('textarea[name="rx'+visit_id+'"]').val();
      
        $.ajax({
        type:'POST',
        url: data_url,
        data:{notes: doctor_notes_rx},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("Rx Done notes saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving rx

      // start of saving plan
       
        var data_url_plan = config_url+"/nurse/save_plan/"+visit_id;
        //window.alert(data_url_plan);
        var doctor_notes_plan = $(this).find('textarea[name="plan'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_plan,
        data:{notes: doctor_notes_plan},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("Plan saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving plan

      // start of saving findings
       
        var data_url_findings = config_url+"/nurse/save_findings/"+visit_id;
        //window.alert(data_url_findings);
        //document.getElementById("vital"+vital_id).value;
         var doctor_notes_findings = $(this).find('textarea[name="findings'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_findings,
        data:{notes: doctor_notes_findings},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("Findings saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving findings

      // start of saving inestigations
        
        var data_url_investigations = config_url+"/nurse/save_investigation/"+visit_id;
        //window.alert(data_url_investigations);
          var doctor_notes_investigations = $(this).find('textarea[name="investigations'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_investigations,
        data:{notes: doctor_notes_investigations},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("Investigations notes saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving investgations

      // start of saving oc
        
        var data_url_oc = config_url+"/nurse/save_oc/"+visit_id;
        //window.alert(data_url_oc);
        var var_filled = $('#filled').val();//document.getElementById("vital"+vital_id).value;
        var var_missing = $('#missing').val();
        var var_decayed = $('#decayed').val();
        var var_soft_tissue = $('#soft_tissue'+visit_id).val();

         var var_filled = $(this).find('textarea[name="filled'+visit_id+'"]').val();
         var var_missing = $(this).find('textarea[name="missing'+visit_id+'"]').val();
         var var_decayed = $(this).find('textarea[name="decayed'+visit_id+'"]').val();
         var var_soft_tissue = $(this).find('textarea[name="soft_tissue'+visit_id+'"]').val();
         var var_general = $(this).find('textarea[name="general'+visit_id+'"]').val();
         var var_others = $(this).find('textarea[name="others'+visit_id+'"]').val();

        $.ajax({
        type:'POST',
        url: data_url_oc,
        data:{filled: var_filled,missing : var_missing, decayed : var_decayed, soft_tissue : var_soft_tissue, general : var_general, others : var_others},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("O/C notes saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });

      // end of saving oc

      // start of saving histories
      
        var data_url_hist = config_url+"/nurse/save_histories/"+visit_id;
        //window.alert(data_url_hist);
          var doctor_notes_medical = $(this).find('textarea[name="past_medical_hx'+visit_id+'"]').val();
           var dental_notes = $(this).find('textarea[name="past_dental_hx'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_hist,
        data:{notes: doctor_notes_medical,notes2: dental_notes},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("histories notes saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving histories

      // start of saving hpco
      
        var data_url_hpco = config_url+"/nurse/save_hpco/"+visit_id;
        //window.alert(data_url_hpco);
          var doctor_notes_hpco = $(this).find('textarea[name="hpco'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_hpco,
        data:{notes: doctor_notes_hpco},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
          // window.alert("HP C/O notes saved successfully");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving hpco

      // start of saving tca
      
        var data_url_tca = config_url+"/nurse/save_tca/"+visit_id;
        //window.alert(data_url_tca);
          var doctor_notes_tca = $(this).find('textarea[name="tca'+visit_id+'"]').val();
        $.ajax({
        type:'POST',
        url: data_url_tca,
        data:{notes: doctor_notes_tca},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
           
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving tca

       window.alert("Notes saved successfully");
       window.location.href = config_url+"/dental/patient_card/"+visit_id;

	});
  </script>

  <script type="text/javascript">




</script>