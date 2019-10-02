<!-- search -->
<?php

 // echo $this->load->view('patients/search_patient', '', TRUE);
 ?>
<!-- end search -->

<section class="panel ">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="pull-right">
	          <!-- <a href="<?php echo site_url();?>queues/outpatient-queue" class="btn btn-primary btn-sm pull-right " style="margin-top:-25px"><i class="fa fa-arrow-up"></i> Outpatient Queue</a>
	           <a href="<?php echo site_url();?>queues/inpatient-queue" class="btn btn-success btn-sm pull-right " style="margin-top:-25px;margin-right:5px;"><i class="fa fa-arrow-up"></i> Inpatient Queue</a> -->
	    </div>
    </header>

        <!-- Widget content -->
        <div class="panel-body">
          <div class="padd">
		<?php
		$error = $this->session->userdata('error_message');
		$success = $this->session->userdata('success_message');
		
		if(!empty($error))
		{
			echo '<div class="alert alert-danger">'.$error.'</div>';
			$this->session->unset_userdata('error_message');
		}
		
		if(!empty($success))
		{
			echo '<div class="alert alert-success">'.$success.'</div>';
			$this->session->unset_userdata('success_message');
		}
				
		$search = $this->session->userdata('patient_search');
		
		if(!empty($search))
		{
			echo '
			<a href="'.site_url().'reception/close_patient_search" class="btn btn-warning btn-sm ">Close Search</a>
			';
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
						  <th>Patient Type</th>
						  <th>Surname</th>
						  <th>Other Names</th>
						   <th>Age</th>
						  <th>Date Created</th>						  
						  <th>Visits</th>
						  <th>RIP Date</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{

				$patient_id = $row->patient_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$created_by = $row->created_by;
				$modified_by = $row->modified_by;
				$deleted_by = $row->deleted_by;
				$visit_type_id = $row->visit_type_id;
				$created = $row->patient_date;
				$last_modified = $row->last_modified;
				$last_visit = $row->last_visit;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				$current_patient_number = $row->current_patient_number;
				$patient_date = $row->patient_date;
				$patient = $this->reception_model->patient_names2($patient_id);
				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_type_id = $patient['visit_type_id'];
				$account_balance = $patient['account_balance'];
				$rip_date = $row->rip_date;
				if($last_visit != NULL)
				{
					$last_visit = date('jS M Y',strtotime($last_visit));
				}
				
				else
				{
					$last_visit = '';
				}
				$patient = $this->reception_model->patient_names2($patient_id);

				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_date_of_birth = $patient['patient_date_of_birth'];
				$gender = $patient['gender'];
				

				if(!empty($patient_date_of_birth))
				{
					$patient_age = $this->reception_model->calculate_age($patient_date_of_birth,$rip_date);
				}
				else
				{
					$patient_age = '';
				}
				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id = $adm->personnel_id;
						
						if($personnel_id == $created_by)
						{
							$created_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $modified_by)
						{
							$modified_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $modified_by)
						{
							$modified_by = $adm->personnel_fname;
						}
						
						if($personnel_id == $deleted_by)
						{
							$deleted_by = $adm->personnel_fname;
						}
					}
				}
				
				else
				{
					$created_by = '-';
					$modified_by = '-';
					$deleted_by = '-';
				}
				$last_visit_rs = $this->reception_model->get_if_patients_first_visit($patient_id);
				$visits = $last_visit_rs->num_rows();
				$count++;
				
			
				
				
					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_number.' </td>
							<td>'.$patient_surname.' </td>
							<td>'.$patient_othernames.'</td>
							<td>'.$patient_age.'</td>
							<td>'.date('jS M Y',strtotime($patient_date)).'</td>
							<td>'.$visits.'</td>
							<td>'.date('jS M Y',strtotime($rip_date)).'</td>

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
        <!-- Widget ends -->

      </div>
    </section>