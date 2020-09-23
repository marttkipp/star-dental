<!-- search -->
<?php echo $this->load->view('search/appointments_report_search', '', TRUE);
$appointment_report_title = $this->session->userdata('appointment_report_title');
?>
<!-- end search -->
 
 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title.' '.$appointment_report_title;?></h4>
          <div class="widget-icons pull-right">
            <!-- <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a>  -->
             <a href="<?php echo site_url().'export-appointments-report'?>" class="btn btn-sm btn-success"> Export List</a>
            <a href="<?php echo site_url().'online-diary'?>" class="btn btn-sm btn-primary"> Online Diary</a>
          </div>
          <div class="clearfix"></div>
        </header>
      <div class="panel-body">
          <div class="padd">
          
<?php
		$search = $this->session->userdata('appointment_report_search');
		
		
		if(!empty($search))
		{
			echo '<a href="'.site_url().'reports/close_appointments_report_search" class="btn btn-warning">Close Search</a>';
		}
		$result = '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-stripped table-condensed table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						   <th>Patient Number</th>
						  <th>Visit Date</th>
						  <th>Patient</th>
						  <th>Phone</th>
						  <th>Procedure</th>
						  <th>Visit Type</th>
						  <th>Time Start</th>						  
						  <th>Doctor</th>
						  <th>Status</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
				// $appointment_start = date('Y-m-d');
				// $visit_dates_query = $this->reception_model->get_all_appointments_dates($appointment_start);
				

				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_date_old = $row->visit_date;
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
				$personnel_id3 = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$patient_number = $row->patient_number;
				$room_id2 = $row->room_id;
				$patient_year = $row->patient_year;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$patient_national_id = $row->patient_national_id;
				$patient_phone = $row->patient_phone1;
				$time_start = $row->time_start;
				$time_end = $row->time_end;
				$procedure_done = $row->procedure_done;
				$close_card = $row->close_card;
				
				$last_visit = $row->last_visit;
				$last_visit_date = $row->last_visit;

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
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id3 == $personnel_id2)
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
				
				if($close_card <> 2)
				{
					$visit_status = 'Showed';
					$color = 'success';
				}
				else
				{
					$visit_status = 'No Show';
					$color = 'warning';
				}
				$count++;
				
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$patient_number.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$patient_surname.' '.$patient_othernames.'</td>
						<td>'.$patient_phone.'</td>
						<td>'.$procedure_done.'</td>
						<td>'.$visit_type_name.'</td>
						<td>'.$time_start.'</td>
						<td>'.$doctor.'</td>

						<td class="'.$color.'">'.$visit_status.'</td>
						
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
			$result .= "There are no appointment patients";
		}
		
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

  <script type="text/javascript">
	

	function check_date(visit_id){
	     var datess=document.getElementById("scheduledate"+visit_id).value;
	     var doctor_id=document.getElementById("doctor_id"+visit_id).value;

	    
	     if(datess && doctor_id){
	     	load_schedule(visit_id);
	     	load_patient_appointments_two(visit_id);
		  $('#show_doctor').fadeToggle(1000); return false;
		 }
		 else{
		  alert('Select Date and a Doctor First')
		 }
	}

	function load_schedule(visit_id){
		var config_url = $('#config_url').val();
		var datess=document.getElementById("scheduledate"+visit_id).value;
		var doctor= document.getElementById("doctor_id"+visit_id).value;

		var url= config_url+"reception/doc_schedule/"+doctor+"/"+datess;
		
		  $('#doctors_schedule'+visit_id).load(url);
		  $('#doctors_schedule'+visit_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments(visit_id){
		var patient_id = $('#patient_id'+visit_id).val();
		var current_date = $('#current_date'+visit_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule'+visit_id).load(url);
		$('#patient_schedule'+visit_id).fadeIn(1000); return false;	

		$('#patient_schedule2'+visit_id).load(url);
		$('#patient_schedule2'+visit_id).fadeIn(1000); return false;	
	}
	function load_patient_appointments_two(visit_id){
		var patient_id = $('#patient_id'+visit_id).val();
		var current_date = $('#current_date'+visit_id).val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule2'+visit_id).load(url);
		$('#patient_schedule2'+visit_id).fadeIn(1000); return false;	
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

	function update_appointment(visit_id,patient_id)
	{
		var config_url = document.getElementById("config_url").value;

        var data_url = config_url+"reception/update_appointment_accounts/"+patient_id+"/"+visit_id;

		var visit_date = $('#scheduledate'+visit_id).val();   
       	var doctor_id = $('#doctor_id'+visit_id).val(); 
       	var timepicker_start = $('#timepicker_start'+visit_id).val(); 
       	var timepicker_end = $('#timepicker_end'+visit_id).val();   
       	var procedure_done = $('#procedure_done'+visit_id).val(); 
       	var room_id = $('#room_id'+visit_id).val(); 
		$.ajax({
	    type:'POST',
	    url: data_url,
	    data:{visit_date: visit_date,doctor_id: doctor_id, timepicker_start: timepicker_start, timepicker_end: timepicker_end, procedure_done: procedure_done, room_id: room_id},
	    dataType: 'text',
	    success:function(data){

	    	window.location = config_url+'appointments';
	    },
	    error: function(xhr, status, error) {

	   		 alert(error);
	    }

	    });
	}
	function start_appointment_visit(visit_id)
	{
		var config_url = document.getElementById("config_url").value;

        var data_url = config_url+"reception/initiate_visit_appointment/"+visit_id;


		var insurance_description = $('#insurance_description'+visit_id).val();   
       	var insurance_limit = $('#insurance_limit'+visit_id).val(); 
       	var insurance_number = $('#insurance_number'+visit_id).val(); 
       	var mcc = $('#mcc'+visit_id).val();   
       	var visit_type_id = $('#visit_type_id2'+visit_id).val(); 

       	// alert(visit_type_id);
		$.ajax({
	    type:'POST',
	    url: data_url,
	    data:{insurance_limit: insurance_limit,insurance_description: insurance_description, insurance_number: insurance_number, mcc: mcc, visit_type_id: visit_type_id, visit_id: visit_id},
	    dataType: 'text',
	    success:function(data){

	    	var data = jQuery.parseJSON(data);
            
            var status = data.status;
            if(status == 1)
            {
	    		window.location = config_url+'queue';
            }
            else
            {

	    		window.location = config_url+'appointments';
            }
	    },
	    error: function(xhr, status, error) {

	   		 alert(error);
	    }

	    });
	}
	function get_visit_type(visit_id)
	{
		var visit_type_id = document.getElementById("visit_type_id2"+visit_id).value;
	
		
		if(visit_type_id != 1)
		{
			$('#insured_company2'+visit_id).css('display','block');
		}
		else
		{
			$('#insured_company2'+visit_id).css('display', 'none');
		}
		
		
	}
</script>