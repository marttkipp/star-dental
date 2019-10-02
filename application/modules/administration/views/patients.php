<!-- search -->
<?php echo $this->load->view('search/search_patient', '', TRUE);?>
<!-- end search -->

<div class="row">
    <div class="col-md-12">
		<!-- Widget -->
		<section class="panel">


			<!-- Widget head -->
			<header class="panel-heading">
				<h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
				<div class="clearfix"></div>
			</header>             

			<!-- Widget content -->
			<div class="panel-body">
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
				
		$search = $this->session->userdata('patient_statement_search');
		
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/close_patient_search" class="btn btn-warning">Close Search</a>';
		}
		
		if($delete != 1)
		{
			$result = '<a href="'.site_url().'reception/add-patient" class="btn btn-success pull-right">Add Patient</a>';
		}
		
		else
		{
			$result = '';
		}
		
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
						  <th>Patient Number</th>
						  <th>First name</th>
						  <th>Other Names</th>
						  <th>Age</th>
						  <th>Contact details</th>
					  	  <th>Balance</th>
						  <th colspan="1">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			
			//deleted patient
			
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
				$patient_age = $row->patient_age;
				$current_patient_number = $row->current_patient_number;
				$patient = $this->reception_model->patient_names2($patient_id);
				$patient_type = $patient['patient_type'];
				$patient_othernames = $patient['patient_othernames'];
				$patient_surname = $patient['patient_surname'];
				$patient_type_id = $patient['visit_type_id'];
				$account_balance = $patient['account_balance'];
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
					$patient_age1 = $this->reception_model->calculate_age($patient_date_of_birth);
				}
				else
				{
					$patient_age1 = '';

				}
				//$patient_age =$patient;
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
				
				$count++;
				
					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_number.'</td>
							<td>'.$patient_surname.'</td>
							<td>'.$patient_othernames.'</td>
							<td>'.$patient_phone1.'</td>
							<td>'.$patient_age.'</td>
							<td>  '.number_format($account_balance,0).'</td>

							<td><a href="'.site_url().'administration/individual_statement/'.$patient_id.'/2" class="btn btn-sm btn-success">Statement</a></td>
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
		</section>
	</div>
</div>