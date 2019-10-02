<!-- search -->
<?php echo $this->load->view('search/search_patient', '', TRUE);?>
<!-- end search -->

<section class="panel">
	<header class="panel-heading">
		<h2 class="panel-title">Patient Treatment Statements</h2>
	</header>
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
				
		$search = $this->session->userdata('patient_treatment_statement_search');
		
		if(!empty($search))
		{
			echo '<a href="'.site_url().'nurse/close_patient_treatment_search/'.$module.'" class="btn btn-warning">Close Search</a>';
		}
		
		
		$result = '';
		
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			if($delete == 0)
			{
				$result .= 
				'
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Patient Type</th>
						  <th>Surname</th>
						  <th>Other Names</th>
						  <th>Patient Number</th>
						  <th>Date Created</th>
						  <th>Last Visit</th>
						  <th colspan="5">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			}
			
			//deleted patients
			else
			{
				$result .= 
				'
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Patient Number</th>
						  <th>Patient Name</th>
						  <th>Patient Number</th>
						  <th>Last Visit</th>
						  <th>Seen By</th>
						  <th>Action</th>
						</tr>
					  </thead>
					  <tbody>
				';
			}
			
			$personnel_query = $this->personnel_model->retrieve_personnel();
			
			foreach ($query->result() as $row)
			{
				$patient_id = $row->patient_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$created_by = $row->created_by;
				$patient_phone_number = $row->patient_phone1;
				$modified_by = $row->modified_by;
				$deleted_by = $row->deleted_by;
				$visit_type_id = $row->visit_type_id;
				$created = $row->patient_date;
				$last_modified = $row->last_modified;
				$last_visit = $row->last_visit;
				$patient_number = $row->patient_number;

				$last_visit_date = $row->last_visit;
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
				$personnel_id = $this->reception_model->get_last_personnel_id($patient_id,$last_visit_date);
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
				$count++;
				
				if($delete == 1)
				{
					$deleted = $row->date_deleted;
					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_number.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$patient_phone_number.'</td>
							<td>'.date('jS M Y',strtotime($last_visit_date)).'</td>
							<td>'.$doctor.'</td>
							<td><a href="'.site_url().'nurse/treatment_statement/'.$patient_id.'/'.$module.'" class="btn btn-sm btn-success" target="_blank">Treatment Statement</a></td>
						</tr> 
					';
				}
				
				else
				{
					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_type.'</td>
							<td>'.$patient_surname.'</td>
							<td>'.$patient_othernames.'</td>
							<td>'.$patient_othernames.'</td>
							<td>'.$patient_phone_number.'</td>
							<td>'.$doctor.'</td>
							<td><a href="'.site_url().'/nurse/treatment_statement/'.$patient_id.'/'.$module.'" class="btn btn-sm btn-success">Treatment Statement</a></td>
						</tr> 
					';
				}
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
</section>