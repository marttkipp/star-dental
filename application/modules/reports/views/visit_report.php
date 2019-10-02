
<!-- Today status ends -->

<!-- <div class="row">
	<div class="col-md-12"> -->
	<?php // $this->load->view('administration/dashboard/line_graph', '', TRUE);?>
	<!-- </div>
</div> -->
<?php echo $this->load->view('search/visit_search', '', TRUE);?>

<?php echo $this->load->view('administration/dashboard/summary', '', TRUE);?>
<?php
$search_title = $this->session->userdata('visit_title_search');
if(!empty($search_title))
{
	$title_ext = $search_title;
}
else
{
	$title_ext = 'Visit Report for '.date('Y-m-d');
}

?>
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>

            	 <a href="<?php echo site_url();?>reports/print-visit" target="_blank" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;"> <i class="fa fa-print"></i> Print List</a>
            	  <a href="<?php echo site_url();?>reports/export-visits" target="_blank" class="btn btn-sm btn-success pull-right" style="margin-top:-25px;margin-right: 5px;"> <i class="fa fa-print"></i> Export List</a>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
<?php
		$result = '';
		$search = $this->session->userdata('visit_report_search');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'reports/close_visit_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
				<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					<thead>
						<tr>
							<th>#</th>
							<th>Visit Date.</th>
							<th>Patient No.</th>
							<th>Patient Name </th>
							<th>Gender</th>
							<th>Age</th>
							<th>Chemo / Review</th>
							<th>Visit</th>
							<th>D X</th>
							<th>RIP</th>
							<th>Patient Type</th>
							<th>HC Time In</th>
						</tr>
					</thead>
					<tbody>		  
				';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date =  date('jS M Y',strtotime($row->visit_date));
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
				}
				else
				{
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
				$personnel_id = $this->session->userdata('personnel_id');
				$is_records = $this->reception_model->check_if_admin($personnel_id,35);


				if($is_records)
				{
					$buttons = '<td>
									<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#create_inpatient'.$patient_id.'">RIP</button>
								
									<div class="modal fade" id="create_inpatient'.$patient_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h4 class="modal-title" id="myModalLabel">Change Patient Status to RIP</h4>
												</div>
												<div class="modal-body">
													'.form_open('reception/change_patient_status/'.$patient_id, array("class" => "form-horizontal")).'
													<div class="form-group">
														<label class="col-lg-4 control-label">RIP date: </label>
														<input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="'.$this->uri->uri_string().'">
														<div class="col-lg-8">
															<div class="input-group">
																<span class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</span>
																<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="rip_date" placeholder="RIP Date" value="'.date('Y-m-d').'">
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="col-md-12">
															<div class="center-align">
																<button type="submit" class="btn btn-primary">Update Status</button>
															</div>
														</div>
													</div>
													'.form_close().'
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>
								</td>';
				}
				else
				{
					$buttons = '';
				}

				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$patient_number.'</td>
						<td>'.$patient_surname.' '.$patient_othernames.'</td>
						<td>'.$gender.'</td>
						<td>'.$age.'</td>
						<td>-</td>
						<td>'.$last_visit_name.'</td>
						<td>'.$diagnosis.'</td>
						<td>'.$rip_status.'</td>
						<td>'.$patient_type.'</td>
						<td>'.$visit_time.'</td>
						'.$buttons.'
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
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        
		</section>
    </div>
  </div>