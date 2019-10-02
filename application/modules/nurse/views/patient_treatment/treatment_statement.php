<div class="tabbable" style="margin-top: 20px;">
	<ul class="nav nav-tabs nav-justified">
		<li class="active" ><a href="#patient-history" data-toggle="tab">Patient card history</a></li>
		<li><a href="#prescription" data-toggle="tab">Prescription</a></li>
		<li><a href="#uploads" data-toggle="tab">Uploads</a></li>
	</ul>
	<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
	</div>
</div>
<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>">
	<div class="tab-pane active" id="patient-history">

		<section class="panel">
			<header class="panel-heading">
				<h2 class="panel-title">Patient Treatment Statements</h2>
			</header>
			<div class="panel-body">
		    
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
											</tr>
										  </thead>
										  <tbody>
									';
								
								$personnel_query = $this->personnel_model->retrieve_personnel();
								$count = 1;
								foreach ($query->result() as $row)
								{
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
										$visit_id = $visit_id1;
									
									
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
													                		<label class="col-lg-2 control-label">HP C/O : </label>
													                		<div class="col-lg-8">
																				<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$hpco.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$notes .= '<span class="bold">Hp C/O</span> '.$hpco.'';
										
									}
									else
									{
										$notes .= '';
										$todays_new_notes .= '	
															
															<div class="row">
																<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">HP C/O : </label>
												                		<div class="col-lg-8">
																			<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">PM Hx</span> '.$past_medical_history.'</p>';
										$notes .= '<p><span class="bold">PD Hx</span> '.$past_dental_history.'</p>';

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
																			<textarea id="past_dental_hx'.$visit_id1.'" name="past_dental_hx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$past_dental_history.'</textarea>
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
																			<textarea id="past_medical_hx'.$visit_id1.'" name="past_medical_hx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$past_medical_history.'</textarea>
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
																			<textarea id="past_dental_hx'.$visit_id1.'" name="past_dental_hx'.$visit_id1.'"  rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																			<textarea id="past_medical_hx'.$visit_id1.'" name="past_medical_hx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																				<textarea id="general_exam'.$visit_id.'" name="general_exam'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$general_exam.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$notes .= '<span class="bold">General Examp</span> '.$general_exam.'';
										
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
																			<textarea id="general_exam'.$visit_id.'" name="general_exam'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">Soft Tissue</span> '.$soft_tissue.'</p>';
										$notes .= '<p><span class="bold">Hard Tissue</span></p>';
										$notes .= '<div style="margin-left:20px">
														<p><span class="bold">General : </span> '.$general.'</p>';
										$notes .= '		<p><span class="bold">Decayed : </span> '.$decayed.'</p>';
										$notes .= '		<p><span class="bold">Filled :</span> '.$filled.'</p>';
										$notes .= '		<p><span class="bold">Missing :</span> '.$missing.'</p>';
										$notes .= '		<p><span class="bold">Other : </span> '.$other.'</p>
													</div>';
										$todays_new_notes .= '
															<br/>
															<div class="row">
											                	<div class="col-md-12">
												                	<div class="form-group">
												                		<label class="col-lg-2 control-label">Soft Tissue : </label>
												                		<div class="col-lg-8">
																			<textarea id="soft_tissue'.$visit_id1.'" name="soft_tissue'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$soft_tissue.'</textarea>
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
																						<textarea id="general'.$visit_id1.'" name="general'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$general.'</textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
													                			<div class="col-md-12">
														                			<label class="col-lg-2 control-label">Decayed : </label>
																					<div class="col-lg-8">
																						<textarea id="decayed'.$visit_id1.'" name="decayed'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$decayed.'</textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
																					<label class="col-lg-2 control-label">Missing : </label>
																					<div class="col-lg-8">
																						<textarea id="missing'.$visit_id1.'" name="missing'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$missing.'</textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
																					<label class="col-lg-2 control-label">Filled : </label>
																					<div class="col-lg-8">
																						<textarea id="filled'.$visit_id1.'" name="filled'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$filled.'</textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
														                			<label class="col-lg-2 control-label">Others : </label>
																					<div class="col-lg-8">
																						<p>(Impacted, Abrasion, Florosis, Traumatic bite) </p>
																						<textarea id="others'.$visit_id1.'" name="others'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$other.'</textarea>
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
																			<textarea id="soft_tissue'.$visit_id1.'" name="soft_tissue'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																						<textarea id="general'.$visit_id1.'" name="general'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
													                			<div class="col-md-12">
														                			<label class="col-lg-2 control-label">Decayed : </label>
																					<div class="col-lg-8">
																						<textarea id="decayed'.$visit_id1.'" name="decayed'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
																					<label class="col-lg-2 control-label">Missing : </label>
																					<div class="col-lg-8">
																						<textarea id="missing'.$visit_id1.'" name="missing'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
																					<label class="col-lg-2 control-label">Filled : </label>
																					<div class="col-lg-8">
																						<textarea id="filled'.$visit_id1.'" name="filled'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
																					</div>
																				</div>
																			</div>
																			<br/>
																			<div class="row">
																				<div class="col-md-12">
														                			<label class="col-lg-2 control-label">Others : </label>

																					<div class="col-lg-8">
																						<p>(Impacted, Abrasion, Florosis, Traumatic bite) </p>
																						<textarea id="others'.$visit_id1.'" name="others'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
													                		<label class="col-lg-2 control-label">O/E : </label>
													                		<div class="col-lg-8">
																				<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$oral_examination.'</textarea>
																			</div>
																		</div>
																	</div>
																';
										endforeach;

										$notes .= '<span class="bold">Oral Examination</span> '.$oral_examination.'';
										
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
										$notes .= '<p><span class="bold">Investigations : </span> '.$investigation.'</p>';
										$todays_new_notes .= '
															<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Investigations : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$investigation.'</textarea>
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
																			<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">Occlusal Exam : </span> '.$occlusal_exam.'</p>';
										$todays_new_notes .= '
															<br/>
																<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Occlusal Exam : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="occlusal_exam'.$visit_id1.'" name="occlusal_exam'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6" >'.$occlusal_exam.'</textarea>
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
																			<textarea id="occlusal_exam'.$visit_id1.'" name="occlusal_exam'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6" ></textarea>
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
										$notes .= '<p><span class="bold">Findings : </span> '.$findings.'</p>';
										$todays_new_notes .= '
															<br/>
																<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Findings : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="findings'.$visit_id1.'" name="findings'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$findings.'</textarea>
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
																			<textarea id="findings'.$visit_id1.'" name="findings'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">Plan : </span> '.$plan_description.'</p>';
										$todays_new_notes .='<br/>
																<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Plan Description : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="plan'.$visit_id1.'" name="plan'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$plan_description.'</textarea>
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
																			<textarea id="plan'.$visit_id1.'" name="plan'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">Rx Done : </span> '.$rx_description.'</p>';
										$todays_new_notes .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">Rx Done : </label>
																		<div class="col-lg-8">
																			<p> (Please enumerate the items)</p>
																			<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$rx_description.'</textarea>
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
																			<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
										$notes .= '<p><span class="bold">TCA : </span> '.$tca_description.'</p>';
										$todays_new_notes .= '<br/>
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label class="col-lg-2 control-label">TCA : </label>
																		<div class="col-lg-8">	
																			<p> (Please enumerate the items)</p>
																			<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$tca_description.'</textarea>
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
																			<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																						<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$hpco.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Presenting Complaint</span> '.$hpco.'';
												
											}
											else
											{
												$todays_notes .= '';
												$item_to_display2 .= '	
																	
																	<div class="row">
																		<div class="col-md-12">
														                	<div class="form-group">
														                		<label class="col-lg-2 control-label">HP C/O : </label>
														                		<div class="col-lg-8">
																					<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																						<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$oral_examination.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Oral Examination</span> '.$oral_examination.'';
												
											}
											else
											{
												$todays_notes .= '';
												$item_to_display2 .= '	
																	
																	<div class="row">
																		<div class="col-md-12">
														                	<div class="form-group">
														                		<label class="col-lg-2 control-label">HP C/O : </label>
														                		<div class="col-lg-8">
																					<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Investigations : </span> '.$investigation.'</p>';
												$item_to_display2 .= '
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Investigations : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$investigation.'</textarea>
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
																					<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Rx Done : </span> '.$rx_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Rx Done : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$rx_description.'</textarea>
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
																					<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">TCA : </span> '.$tca_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">TCA : </label>
																				<div class="col-lg-8">	
																					<p> (Please enumerate the items)</p>
																					<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$tca_description.'</textarea>
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
																					<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
											// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control col-md-6 cleditor" >'.$notes.'</textarea>
											// 								</div>
											// 							</div>
											// 						</div>';


									}
									else if($visit_id == $visit_id1 && $number_items == 1 && $count == $number_items)
									{
										// this is the first visit
										$changer = '<td>
															<a  class="btn btn-sm btn-success" id="open_todays_visit'.$visit_id.'" onclick="get_patient_first_trail('.$visit_id.');">Open patient first card</a>
															<a  class="btn btn-sm btn-danger" id="close_todays_visit'.$visit_id.'" style="display:none;" onclick="close_patient_first_trail('.$visit_id.');">Close patient first card</a>
														</td>';
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
																						<textarea id="hpco'.$visit_id1.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$hpco.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Presenting Complaint</span> '.$hpco.'';
												
											}
											else
											{
												$todays_notes .= '';
												$item_to_display2 .= '	
																	
																	<div class="row">
																		<div class="col-md-12">
														                	<div class="form-group">
														                		<label class="col-lg-2 control-label">HP C/O : </label>
														                		<div class="col-lg-8">
																					<textarea id="hpco'.$visit_id1.'" name="hpco'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
															                		<label class="col-lg-2 control-label">O/E : </label>
															                		<div class="col-lg-8">
																						<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$oral_examination.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Oral Examination</span> '.$oral_examination.'';
												
											}
											else
											{
												$todays_notes .= '';
												$item_to_display2 .= '	
																	
																	<div class="row">
																		<div class="col-md-12">
														                	<div class="form-group">
														                		<label class="col-lg-2 control-label">O/E: </label>
														                		<div class="col-lg-8">
																					<textarea id="oral_examination'.$visit_id1.'" name="oral_examination'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Investigations : </span> '.$investigation.'</p>';
												$item_to_display2 .= '
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Investigations : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$investigation.'</textarea>
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
																					<textarea id="investigations'.$visit_id1.'" name="investigations'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Rx Done : </span> '.$rx_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Rx Done : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$rx_description.'</textarea>
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
																					<textarea id="rx'.$visit_id1.'" name="rx'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">TCA : </span> '.$tca_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">TCA : </label>
																				<div class="col-lg-8">	
																					<p> (Please enumerate the items)</p>
																					<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$tca_description.'</textarea>
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
																					<textarea id="tca'.$visit_id1.'" name="tca'.$visit_id1.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
											// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control col-md-6 cleditor" >'.$todays_notes.'</textarea>
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
										// 									<textarea id="todays_notes'.$visit_id.'" name="todays_notes'.$visit_id.'" rows="5" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
																						<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$hpco.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Presenting Complaint</span> '.$hpco.'';
												
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
																					<textarea id="hpco'.$visit_id.'" name="hpco'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
															                		<label class="col-lg-2 control-label">O/E : </label>
															                		<div class="col-lg-8">
																						<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$oral_examination.'</textarea>
																					</div>
																				</div>
																			</div>
																		';
												endforeach;

												$todays_notes .= '<span class="bold">Oral Examination</span> '.$oral_examination.'';
												
											}
											else
											{
												$todays_notes .= '';
												$item_to_display2 .= '	
																	
																	<div class="row">
																		<div class="col-md-12">
														                	<div class="form-group">
														                		<label class="col-lg-2 control-label">O/E : </label>
														                		<div class="col-lg-8">
																					<textarea id="oral_examination'.$visit_id.'" name="oral_examination'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Investigations : </span> '.$investigation.'</p>';
												$item_to_display2 .= '
																	<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Investigations : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$investigation.'</textarea>
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
																					<textarea id="investigations'.$visit_id.'" name="investigations'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">Rx Done : </span> '.$rx_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">Rx Done : </label>
																				<div class="col-lg-8">
																					<p> (Please enumerate the items)</p>
																					<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$rx_description.'</textarea>
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
																					<textarea id="rx'.$visit_id.'" name="rx'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
												$todays_notes .= '<p><span class="bold">TCA : </span> '.$tca_description.'</p>';
												$item_to_display2 .= '<br/>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="form-group">
																				<label class="col-lg-2 control-label">TCA : </label>
																				<div class="col-lg-8">	
																					<p> (Please enumerate the items)</p>
																					<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" >'.$tca_description.'</textarea>
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
																					<textarea id="tca'.$visit_id.'" name="tca'.$visit_id.'" rows="4" cols="40" class="form-control col-md-6 cleditor" ></textarea>
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
											</tr> 
										';
										$count++;


								}
							
								 
									$result .=
											'<tr id="visit_trail'.$visit_id.'" style="display:none;">
												<td colspan="5">';
													$result .= form_open("dental/save-current-notes/".$visit_id, array("class" => "form-horizontal"));
													$result .= '
																<div class="row">
																	<div class="col-md-12">
																		<div class="form-group">
																			<div class="col-lg-2">
																				Todays Visit
																			</div>
																			<div class="col-lg-8">
																				'.$item_to_display2.'
																			</div>
																			
																		</div>
																	</div>
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
		                                
						<?php if(isset($links)){echo $links;}?>
		            
		                <div class="clearfix"></div> 
		            
		            </div>

		    </div>
		</section>
	</div>
	<div class="tab-pane " id="prescription">

		<div id="visit-prescription"></div>
	</div>
	<div class="tab-pane " id="uploads">		
			<section class="panel">
			    <header class="panel-heading">
			        <h2 class="panel-title">Documents</h2>
			    </header>
			    <div class="panel-body">
			    
					<div class="row" style="margin-top:10px;">
				   		<div class="col-md-12">
					       <?php
					       if($patient_other_documents->num_rows() > 0)
					        {
					            $count = 0;
					                
					            $identification_result = 
					            '
					            <table class="table table-bordered table-striped table-condensed">
					                <thead>
					                    <tr>
					                        <th>#</th>
					                        <th>Document Type</th>
					                        <th>Document Name</th>
					                        <th>Download Link</th>
					                        <th colspan="3">Actions</th>
					                    </tr>
					                </thead>
					                  <tbody>
					                  
					            ';
					            
					            foreach ($patient_other_documents->result() as $row)
					            {
					                $document_type_name = $row->document_type_name;
					                $document_upload_id = $row->document_upload_id;
					                $document_name = $row->document_name;
					                $document_upload_name = $row->document_upload_name;
					                $document_status = $row->document_status;
					                
					                //create deactivated status display
					                if($document_status == 0)
					                {

					                    $status = '<span class="label label-default">Deactivated</span>';
					                    $button = '<a class="btn btn-info" href="'.site_url().'microfinance/activate-personnel-identification/'.$document_upload_id.'/'.$visit_id.'" onclick="return confirm(\'Do you want to activate?\');" title="Activate "><i class="fa fa-thumbs-up"></i></a>';
					                }
					                //create activated status display
					                else if($document_status == 1)
					                {
					                    $status = '<span class="label label-success">Active</span>';
					                    $button = '<a class="btn btn-default" href="'.site_url().'microfinance/deactivate-personnel-identification/'.$document_upload_id.'/'.$visit_id.'" onclick="return confirm(\'Do you want to deactivate ?\');" title="Deactivate "><i class="fa fa-thumbs-down"></i></a>';
					                }
					                
					                $count++;
					                $identification_result .= 
					                '
					                    <tr>
					                        <td>'.$count.'</td>
					                        <td>'.$document_type_name.'</td>
					                        <td>'.$document_name.'</td>
					                        <td><a href="'.$this->document_upload_location.''.$document_upload_name.'" target="_blank" >Download Here</a></td>
					                        <td>'.$status.'</td>
					                        <!--<td>'.$button.'</td>-->
					                        <td><a href="'.site_url().'dental/delete_document_scan/'.$document_upload_id.'/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete ?\');" title="Delete"><i class="fa fa-trash"></i></a></td>
					                    </tr> 
					                ';
					            }
					            
					            $identification_result .= 
					            '
					                          </tbody>
					                        </table>
					            ';
					        }
					        
					        else
					        {
					            $identification_result = "<p>No plans have been added</p>";
					        }
					        echo $identification_result;
					       ?>
				       </div>
			       </div>
			    </div>
			</section>
	</div>
</div>
<script type="text/javascript">
 function prescription_view()
  {

    // var myTarget = document.getElementById("add_item");
    // myTarget.style.display = 'block';

    // var visit_id = document.getElementById("visit_id").value;
    var patient_id = document.getElementById("patient_id").value;
    // alert(patient_id);
     var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"dental/display_patient_prescription/"+null+"/"+patient_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("visit-prescription").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
  }

</script>