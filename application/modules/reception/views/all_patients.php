<!-- search -->
<?php echo $this->load->view('patients/search_patient', '', TRUE);?>
<!-- end search -->

<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="pull-right">
        	<?php

        	$authorize_invoice_changes = $this->session->userdata('authorize_invoice_changes');

        	if($authorize_invoice_changes)
        		echo '<a href="'.site_url().'reception/export-patients" class="btn btn-success  btn-sm " target="_blank" style="margin-left:10px; margin-top:-40px;">Export Patients</a>
			<a href="'.site_url().'reception/import-patients" class="btn btn-primary  btn-sm " style="margin-left:10px; margin-top:-40px;">Import Patients</a>';


        	echo '
			<a href="'.site_url().'add-patient" class="btn btn-success btn-sm" style="margin-top:-40px;">Add Patient</a>
			';
        	?>
        	
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
		
		
		
		if($delete != 1)
		{
			$result = '
				';
		}
		
		else
		{
			$result = '';
		}
		
		$pt_balances_rs = $this->reception_model->get_patient_balances();
		$pd_balances_array = array();

		if($pt_balances_rs->num_rows() > 0)
		{
			foreach ($pt_balances_rs->result() as $key) {
				// code...
				$pd_balances_array[$key->patient_id] = $key;
			}
		}
			// echo "<pre>";
			// 	print_r(print_r($pd_balances_array));
			// 	echo "</pre>";
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
						  <th>Patient name</th>
						  <th>Contact details</th>
						  <th>Last Visit</th>
						  <th>Doctor</th>
						  <th>Balance</th>
						  <th colspan="3">Actions</th>
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
				$patient_year = $row->patient_year;
				$last_visit = $row->last_visit;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				$current_patient_number = $row->current_patient_number;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$gender_id = $row->gender_id;

			
				$last_visit = $row->last_visit;
				$last_visit_date = $row->last_visit;
				//$card_no = $row->card_no;
				$patient_phone1 = $row->patient_phone1;
				$patient_number = $row->patient_number;
				if($last_visit != NULL)
				{
					$last_visit = date('jS M Y',strtotime($last_visit));
				}
				
				else
				{
					$last_visit = '';
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
				$insurance_company = $this->reception_model->get_patient_insurance_company($patient_id);

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

				// $cash_balance = $this->accounts_model->get_cash_balance($patient_id);
				// $insurance_balance = $this->accounts_model->get_insurance_balance($patient_id);

				$balance = 0;


				if(array_key_exists($patient_id, $pd_balances_array)){


					$dr_amount = $pd_balances_array[$patient_id]->dr_amount;
					$cr_amount = $pd_balances_array[$patient_id]->cr_amount;

					$balance = $dr_amount - $cr_amount;
				}
				$count++;
				
				

					$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$patient_number.'</td>
							<td>'.$patient_surname.' '.$patient_othernames.'</td>
							<td>'.$patient_phone1.'</td>							
							<td>'.$last_visit.'</td>
							<td>'.$doctor.'</td>
							<td>'.number_format($balance,2).'</td>
							<td><a href="'.site_url().'reception/set_visit/'.$patient_id.'" class="btn btn-sm btn-info">Queue </a></td>
							<td><a href="'.site_url().'reception/edit_patient/'.$patient_id.'" class="btn btn-sm btn-warning">Edit </a></td>
							
							<td><a href="'.site_url().'administration/individual_statement/'.$patient_id.'/1" class="btn btn-primary btn-sm " style="margin-top:0px"><i class="fa fa-print"></i> Statement </a></td>
							
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
		unset($pd_balances_array);
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

<script type="text/javascript">
	function get_visit_type(patient_id)
	{
		var visit_type_id = document.getElementById("visit_type_id2"+patient_id).value;

		
		if(visit_type_id != 1)
		{
			$('#insured_company2'+patient_id).css('display','block');
		}
		else
		{
			$('#insured_company2'+patient_id).css('display', 'none');
		}
		
		
	}

</script>



<script type="text/javascript">
	

	function check_date(patient_id){
	     var datess=document.getElementById("scheduledate"+patient_id).value;
	     var doctor_id=document.getElementById("doctor_id"+patient_id).value;

	    
	     if(datess && doctor_id){
	     	load_schedule(patient_id);
	     	load_patient_appointments_two(patient_id);
		  $('#show_doctor').fadeToggle(1000); return false;
		 }
		 else{
		  alert('Select Date and a Doctor First')
		 }
	}

	function load_schedule(patient_id){
		var config_url = $('#config_url').val();
		var datess=document.getElementById("scheduledate"+patient_id).value;
		var doctor= document.getElementById("doctor_id"+patient_id).value;

		var url= config_url+"reception/doc_schedule/"+doctor+"/"+datess;

		// alert(url);
		
		  $('#doctors_schedule'+patient_id).load(url);
		  $('#doctors_schedule'+patient_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments(patient_id){
		var patient_id = $('#patient_id'+patient_id).val();
		var current_date = $('#current_date'+patient_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule'+patient_id).load(url);
		$('#patient_schedule'+patient_id).fadeIn(1000); return false;	

		$('#patient_schedule2'+patient_id).load(url);
		$('#patient_schedule2'+patient_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments_two(patient_id){
		var patient_id = $('#patient_id'+patient_id).val();
		var current_date = $('#current_date'+patient_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule2'+patient_id).load(url);
		$('#patient_schedule2'+patient_id).fadeIn(1000); return false;	
	}
	function schedule_appointment(appointment_id)
	{
		if(appointment_id == '1')
		{
			$('#appointment_details').css('display', 'block');
		}
		else
		{
			$('#appointment_details').css('display', 'none');
		}
	}

	function submit_reception_appointment(patient_id)
	{
		var config_url = document.getElementById("config_url").value;

        var data_url = config_url+"reception/save_appointment_accounts/"+patient_id;

		var visit_date = $('#scheduledate'+patient_id).val();   
       	var doctor_id = $('#doctor_id'+patient_id).val(); 
       	var timepicker_start = $('#timepicker_start'+patient_id).val(); 
       	var timepicker_end = $('#timepicker_end'+patient_id).val();   
       	var procedure_done = $('#procedure_done'+patient_id).val(); 
       	var room_id = $('#room_id'+patient_id).val(); 
       	var url_redirect = $('#redirect_url'+patient_id).val(); 

		$.ajax({
	    type:'POST',
	    url: data_url,
	    data:{visit_date: visit_date,doctor_id: doctor_id, timepicker_start: timepicker_start, timepicker_end: timepicker_end, procedure_done: procedure_done, room_id: room_id},
	    dataType: 'text',
	    success:function(data){

	    	window.location = config_url+''+url_redirect;
	    },
	    error: function(xhr, status, error) {

	   		 alert(error);
	    }

	    });
	}

</script>