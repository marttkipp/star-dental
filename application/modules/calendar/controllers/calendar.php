<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";
error_reporting(0);
class Calendar extends admin 
{
	function __construct()
	{
		parent:: __construct();
		
		
		$this->load->model('calendar/calendar_model');
		$this->load->model('auth/auth_model');
		$this->load->model('reception/reception_model');
		$this->load->model('messaging/messaging_model');
		
		// if(!$this->auth_model->check_login())
		// {
		// 	redirect('login');
		// }
	}



	public function index() 
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		
		$data['content'] = $this->load->view('calendar/calendar', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function uhdc_calendar() 
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		
		$data['content'] = $this->load->view('uhdc_calendar', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function calendar_annex() 
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		
		$data['content'] = $this->load->view('calendar_annex', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function print_schedule($todays_date)
	{


		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		$v_data['todays_date'] = $todays_date;
		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$html = $this->load->view('print_agenda', $v_data);


	}
	public function print_agenda($todays_date)
	{

		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		$v_data['todays_date'] = $todays_date;
		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$html = $this->load->view('print_agenda', $v_data);
	}
	public function print_annex_schedule($todays_date)
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		$v_data['todays_date'] = $todays_date;
		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$html = $this->load->view('print_annex_agenda', $v_data);
	}
	public function print_uhdc_schedule($todays_date)
	{
		$branch_id = $this->session->userdata('branch_id');
		$branch_name = $this->session->userdata('branch_name');
		$data['title'] = 'Online Diary';
		$v_data['title'] = $data['title'];
		$v_data['todays_date'] = $todays_date;
		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$html = $this->load->view('print_uhdc_agenda', $v_data);
	}
	public function search_dashboard()
	{
		$year = $this->input->post('year');
		$month = $this->input->post('month');

		$this->session->set_userdata('year',$year);
		$this->session->set_userdata('month',$month);

		redirect('appointments-reports/monthly-statistics');

	}




	// calendar sidebar



	public function calendar_sidebar($appointment_id,$patient_id = NULL)
	{
		$data = array('appointment_id'=> $appointment_id);

		$data['doctors'] = $this->reception_model->get_all_doctors();

		$visit_type_order = 'visit_type_id';		    
		$visit_type_where = 'visit_type_id > 0';
		$visit_type_table = 'visit_type';

		$visit_type_query = $this->reception_model->get_all_visit_type_details($visit_type_table, $visit_type_where,$visit_type_order);

		$rs14 = $visit_type_query->result();
		$visit_type = '';
		foreach ($rs14 as $visit_type_rs) :


		  $visit_type_id = $visit_type_rs->visit_type_id;
		  $visit_type_name = $visit_type_rs->visit_type_name;

		  $visit_type .="<option value='".$visit_type_id."'>".$visit_type_name."</option>";

		endforeach;

		$patient_name = '';
		$patient_phone1 = '';
		if($patient_id > 0)
		{
			$patient_order = 'patient_id';		    
			$patient_where = 'patients.patient_delete = 0 AND patient_id ='.$patient_id;
			$patient_table = 'patients';

			$patient_query = $this->reception_model->get_all_visit_type_details($patient_table, $patient_where,$patient_order);

			$rs14 = $patient_query->result();
		
			foreach ($rs14 as $patient_rs) :


			  $patient_id = $patient_rs->patient_id;
			  $patient_phone1 = $patient_rs->patient_phone1;
			  $patient_name = $patient_rs->patient_surname.' '.$patient_rs->patient_othernames.' - '.$patient_rs->patient_number.' - '.$patient_rs->patient_phone1;
			endforeach;
		}

		$appointments_order = 'appointment_id';		    
		$appointments_where = 'appointment_id ='.$appointment_id;
		$appointments_table = 'appointments';

		$appointments_query = $this->reception_model->get_all_visit_type_details($appointments_table, $appointments_where,$appointments_order);

		$rs14 = $appointments_query->result();
	
		foreach ($rs14 as $appointments_rs) :


		  $appointment_id = $appointments_rs->appointment_id;
		  $appointment_date_time_start = $appointments_rs->appointment_date_time_start;
		  $appointment_start_time = $appointments_rs->appointment_start_time;
		  $resource_id = $appointments_rs->resource_id;
		endforeach;

		$services_where = 'service.service_id = service_charge.service_id AND service.service_name ="Dental Procedures" AND service_charge.service_charge_status = 1 ';
		$services_table = 'service,service_charge';
		$services_order = 'service_charge.service_charge_name';
		$services_query = $this->reception_model->get_all_visit_type_details($services_table, $services_where,$services_order);

		$rs15 = $services_query->result();
		$service_charge = '';
		foreach ($rs15 as $service_charge_rs) :


		  $service_charge_id = $service_charge_rs->service_charge_id;
		  $service_charge_name = $service_charge_rs->service_charge_name;

		  $service_charge .="<option value='".$service_charge_id."'>".$service_charge_name."</option>";

		endforeach;


		$data['service_charge'] = $service_charge;

		$appointment_explode = explode('T', $appointment_date_time_start);
		$data['patient_id'] = $patient_id;
		$data['patient_name'] = $patient_name;
		$data['visit_type'] = $visit_type;
		$data['patient_phone1'] = $patient_phone1;
		$data['resource_id'] = $resource_id;
		// var_dump($patient_name);die();
		$data['appointment_date_time_start'] = $appointment_explode[0];
		$data['appointment_start_time'] = $appointment_start_time;
		$page = $this->load->view('sidebar/appointment_sidebar',$data);

		echo $page;

		
	}

	public function patient_details($appointment_id,$patient_id)
	{
		$patient_order = 'patient_id';		    
		$patient_where = 'patients.patient_delete = 0 AND patient_id ='.$patient_id;
		$patient_table = 'patients';

		$patient_query = $this->reception_model->get_all_visit_type_details($patient_table, $patient_where,$patient_order);

		$rs14 = $patient_query->result();
		$patient_phone1 = '';
		foreach ($rs14 as $patient_rs) :


		  $patient_id = $patient_rs->patient_id;
		  $patient_phone1 = $patient_rs->patient_phone1;
		  $patient_name = $patient_rs->patient_surname.' '.$patient_rs->patient_othernames.' - '.$patient_rs->patient_number.' - '.$patient_rs->patient_phone1;
		endforeach;

		echo $patient_phone1;
	}

	public function search_laboratory_tests($appointment_id)
	{		
		$symptoms_search = $this->input->post('query');
		$query = null;
		if(!empty($symptoms_search))
		{
			$lab_test_where = 'patients.patient_delete = 0';
			$lab_test_table = 'patients';
			$lab_test_where .= ' AND patient_surname LIKE \'%'.$symptoms_search.'%\' OR patient_number LIKE \'%'.$symptoms_search.'%\' OR patient_phone1 LIKE \'%'.$symptoms_search.'%\'';

			$this->db->where($lab_test_where);
			$this->db->limit(10);
			$query = $this->db->get($lab_test_table);

		}
		$data['query'] = $query;
		$data['appointment_id'] = $appointment_id;
		$page = $this->load->view('sidebar/search_patients',$data);

		echo $page;

	}


	public function search_patients_list($appointment_id)
	{		
		$symptoms_search = $this->input->post('query');
		$query = null;
		if(!empty($symptoms_search))
		{
			$lab_test_where = 'patients.patient_delete = 0';

			$surnames = explode(" ",$symptoms_search);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{

					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' )';
				}
				
				else
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\') AND ';
				}
				$count++;
			}
			$surname .= ') ';

			// $lab_test_where .= ' AND patient_surname LIKE \'%'.$symptoms_search.'%\'';
			$lab_test_where .=$surname;

			

		}
		$phone_query = $this->input->post('phone_query');

		if(!empty($phone_query))
		{
			$lab_test_where = 'patients.patient_delete = 0';
			
			$lab_test_where .= ' AND patient_phone1 LIKE \'%'.$phone_query.'%\'';

		}

		if(!empty($symptoms_search) OR !empty($phone_query))
		{

			$lab_test_table = 'patients';
			$this->db->where($lab_test_where);
			$this->db->limit(10);
			$query = $this->db->get($lab_test_table);

		}
		


		$data['query'] = $query;
		$data['appointment_id'] = $appointment_id;
		$page = $this->load->view('sidebar/search_patients',$data);

		echo $page;

	}




	// calendar


	public function create_appointment()
	{
		$start_date = $this->input->post('start');
		$resource_id = $this->input->post('resource');
		$start_date_new = str_replace('T', ' ', $start_date);
		// echo json_encode($_POST);
		$exploded =explode(" ", $start_date_new);
		$visit_date = $exploded[0];
		$exploded2 =explode("+", $exploded[1]);
		$time_start = $exploded2[0];


		$endTime = date('Y-m-d H:i',strtotime('+1 hour',strtotime($start_date_new)));
		$exploded2 =explode(" ", $endTime);
		$time_end = $exploded2[1];
		$time_end = $time_end.':00';
		$end_date = str_replace(' ', 'T', $endTime);
		

		$appointment_array  = array(
										'appointment_date' => $visit_date, 
										'appointment_status' => 0, 
										'sync_status' => 0, 
										'appointment_start_time' => $time_start,
										'appointment_end_time' => $time_end, 
										'appointment_date_time_start' => $start_date,
										'appointment_date_time_end' => $end_date,
										'created_by' => $this->session->userdata('personnel_id'),
										'created' => date('Y-m-d'),
										'resource_id' => $resource_id
									);
		$this->db->insert('appointments', $appointment_array);
		$appointment_id = $this->db->insert_id();
		$this->reception_model->send_appointments_to_cloud($appointment_id);
		$response['message'] ='success';
		$response['appointment_id'] = $appointment_id;
		$response['appointment_detail'] = $this->get_new_appointment_detail($appointment_id);

		echo json_encode($response);
	}

	public function get_new_appointment_detail($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,visit.procedure_done');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$query = $this->db->get('appointments');
		$data['status'] = 0;
		$data['result'] = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $res) {
				# code...
				$v_data['appointment_query'] = $query->result();

				$visit_date = date('D M d Y',strtotime($res->appointment_date)); 
				$appointment_start_time = $res->appointment_start_time; 
				$appointment_end_time = $res->appointment_end_time; 
				$time_start = $res->appointment_date_time_start; 
				$time_end = $res->appointment_date_time_end;
				$patient_id = $res->patient_id;
				$patient_othernames = $res->patient_othernames;
				$patient_surname = $res->patient_surname;				
				$visit_id = $res->visit_id;
				$appointment_id = $res->appointment;
				$resource_id = $res->resource_id;
				$event_name = $res->event_name;
				$event_description = $res->event_description;
				$appointment_status = $res->appointment_status;
				$procedure_done = $res->procedure_done;
				$resource_id = $res->resource_id;
				$patient_data = $patient_surname.' '.$patient_othernames;
				$patient_phone1 = $res->patient_phone1;
				$patient_email = $res->patient_email;
				if($appointment_status == 0)
				{
					$color = 'blue';
					$status_name = 'unassigned';
				}
				else if($appointment_status == 1)
				{
					$color = '';
					$status_name = 'unassigned';
				}
				else if($appointment_status == 2)
				{
					$color = 'green';
					$status_name = 'Confirmed';
				}
				else if($appointment_status == 3)
				{
					$color = 'red';
					$status_name = 'Cancelled';
				}
				else if($appointment_status == 4)
				{
					$color = 'purple';
					$status_name = 'Showed';
				}
				else if($appointment_status == 5)
				{
					$color = 'black';
					$status_name = 'No Showed';
				}
				else if($appointment_status == 6)
				{
					$color = 'DarkGoldenRod';
					$status_name = 'Notified';
				}
				else if($appointment_status == 7)
				{
					$color = 'blue';
					$status_name = 'Not Notified';
				}
				else if($appointment_status == 8)
				{
					$color = 'blueviolet';
					$status_name = 'Not Notified';
				}
				else
				{
					$color = 'orange';
					$status_name = '';
				}
				if(empty($patient_data))
				{
					$patient_data = '';
				}
				if(empty($procedure_done))
				{
					$procedure_done = '';
				}

				$data['status'] = $appointment_status;
				$v_data['doctors'] = $this->reception_model->get_all_doctors();


				$patients_order = 'patient_id';		    
				$patients_where = 'patient_id > 0 and patient_delete = 0';
				$patients_table = 'patients';

				$patients_query = $this->reception_model->get_all_patients_details($patients_table, $patients_where,$patients_order);

				$rs14 = $patients_query->result();
				$patients = '';
				foreach ($rs14 as $patients_rs) :


				  $patients_id = $patients_rs->patient_id;
				  // $patients_id = $patients_rs->patient_id;
				  $patients_name = $patients_rs->patient_surname.' '.$patients_rs->patient_othernames.' - '.$patients_rs->patient_number.' Phone '.$patients_rs->patient_phone1;

				  $patients .="<option value='".$patients_id."'>".$patients_name."</option>";

				endforeach;
				$v_data['patients'] = $patients;

				$visit_type_order = 'visit_type_id';		    
				$visit_type_where = 'visit_type_id > 0';
				$visit_type_table = 'visit_type';

				$visit_type_query = $this->reception_model->get_all_visit_type_details($visit_type_table, $visit_type_where,$visit_type_order);

				$rs14 = $visit_type_query->result();
				$visit_type = '';
				foreach ($rs14 as $visit_type_rs) :


				  $visit_type_id = $visit_type_rs->visit_type_id;
				  $visit_type_name = $visit_type_rs->visit_type_name;

				  $visit_type .="<option value='".$visit_type_id."'>".$visit_type_name."</option>";

				endforeach;

				$services_where = 'service.service_id = service_charge.service_id AND service.service_name ="Dental Procedures" AND service_charge.service_charge_status = 1 ';
				$services_table = 'service,service_charge';
				$services_order = 'service_charge.service_charge_name';
				$services_query = $this->reception_model->get_all_visit_type_details($services_table, $services_where,$services_order);

				$rs15 = $services_query->result();
				$service_charge = '';
				foreach ($rs15 as $service_charge_rs) :


				  $service_charge_id = $service_charge_rs->service_charge_id;
				  $service_charge_name = $service_charge_rs->service_charge_name;

				  $service_charge .="<option value='".$service_charge_id."'>".$service_charge_name."</option>";

				endforeach;


				$v_data['service_charge'] = $service_charge;
				$v_data['visit_type'] = $visit_type;
				$v_data['appointment_id'] = $appointment_id;
				$v_data['resource_id'] = $resource_id;
				$v_data['visit_id'] = $visit_id;
				$data['results'] = $this->load->view('new_appointment_view', $v_data, TRUE);
				

			}
		}

		return $data;
	}

	public function add_appointment($category = null)
	{
		$this->form_validation->set_rules('appointment_id', 'Event', 'required');
		$this->form_validation->set_rules('appointment_type', 'Appointment', 'required');
		$appointment_id = $this->input->post('appointment_id');
		$appointment_type = $this->input->post('appointment_type');

		
		$category_id = $this->input->post('category');
		$visit_type_id = $this->input->post('visit_type_id'.$appointment_id);

		if($appointment_type == 1)
		{
			if($category_id == 0)
			{
				$this->form_validation->set_rules('patient_id'.$appointment_id, 'Patient', 'required');
			}	
			// $this->form_validation->set_rules('service_charge_id'.$appointment_id, 'Procedure', 'required');
			$this->form_validation->set_rules('visit_type_id'.$appointment_id, 'visit type', 'required');
			$this->form_validation->set_rules('visit_time_id'.$appointment_id, 'visit time', 'required');	
		}
		else
		{

			$this->form_validation->set_rules('appointment_title', 'Title', 'required');
			// $this->form_validation->set_rules('event_duration', 'Duration');
		}

		if($category_id == 1)
		{
			$this->form_validation->set_rules('surname'.$appointment_id, 'Surname', 'required');	
			// $this->form_validation->set_rules('other_names'.$appointment_id, 'Other Names', 'required');
			// $this->form_validation->set_rules('first_name'.$appointment_id, 'First Name', '');
			$this->form_validation->set_rules('phone_number'.$appointment_id, 'Phone', 'required');
		}
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			// $data['message'] = validation_errors();
			$data['message'] = strip_tags(validation_errors());
		}
		else
		{
			
			$appointment_type = $this->input->post('appointment_type');
			if($appointment_type == 1 )
			{

				if($category_id == 1)
				{

					$patient_surname = $this->input->post('surname'.$appointment_id);
					// $patient_first_name = $this->input->post('first_name'.$appointment_id);
					// $patient_othernames = $this->input->post('other_names'.$appointment_id);
					$patient_phone1 = $this->input->post('phone_number'.$appointment_id);
					// $new_patient_number = $this->reception_model->create_new_patient_number();
					$new_patient_array['patient_surname'] = $patient_surname;
					// $new_patient_array['patient_othernames'] = $patient_othernames;
					// $new_patient_array['patient_first_name'] = $patient_first_name;
					$new_patient_array['patient_phone1'] = $patient_phone = $patient_phone1;
					$new_patient_array['patient_number'] = '';
					$new_patient_array['category_id'] = 1;
					$new_patient_array['sync_status'] = 0;
					$new_patient_array['about_us'] = $this->input->post('about_us'.$appointment_id);
					$new_patient_array['about_us_view'] = $this->input->post('about_us_view'.$appointment_id);
					$new_patient_array['patient_year'] = date('Y');
					$new_patient_array['patient_date'] = date('Y-m-d H:i:s');
					

					$this->db->insert('patients',$new_patient_array);

					// push to cloud
					$patients_id = $this->db->insert_id();
					$this->reception_model->send_patients_to_cloud($patients_id);


					$event_name = $patient_surname.' '.$patient_othernames.' - '.$new_patient_number.' '.$patient_phone1;
					
				}
				else
				{

					$patient_id = $this->input->post('patient_id'.$appointment_id);
					$this->db->where('patients.patient_id',$patient_id);
					$this->db->select('patients.patient_surname,patients.patient_othernames,patient_phone1,patient_number,patient_id');
					$query = $this->db->get('patients');
					$event_name = '';
					if($query->num_rows() > 0)
					{
						foreach ($query->result() as $key => $patients_rs) {
							# code...
							$patients_id = $patients_rs->patient_id;
						   $event_name = $patients_rs->patient_surname.' '.$patients_rs->patient_othernames.' - '.$patients_rs->patient_number.' '.$patients_rs->patient_phone1;

						}
					}
					

				}
				

			}
			else
			{
				$event_name = $this->input->post('appointment_title');
				
			}
			$service_charge_id = $this->input->post('service_charge_id'.$appointment_id);

			$procedure_done = '';
			if(!empty($service_charge_id))
			{
				$procedure_done = $this->reception_model->get_service_charge_detail($service_charge_id);
			}

			$procedure_done .= $this->input->post('procedure_done'.$appointment_id);


			$this->db->where('appointment_id',$appointment_id);
			$this->db->select('appointments.*');
			$query_appointment = $this->db->get('appointments');
			if($query_appointment->num_rows() > 0)
			{
				foreach ($query_appointment->result() as $key => $appointment) {
					# code...
					$appointment_id = $appointment->appointment_id;
				   $appointment_date = $appointment->appointment_date;
				   $appointment_start_time = $appointment->appointment_start_time;
				   $appointment_end_time = $appointment->appointment_end_time;


				}
			}
			
			if($appointment_type == 1)
			{
				$minutes_to_add = $this->input->post('visit_time_id'.$appointment_id);
				$counting =  0;
				$account_types= '';
				$total_count = count($_POST['visit_type']);
			
				$this->db->where('visit_type_id',$visit_type_id);
				$this->db->select('visit_type.*');
				$query_visit_type = $this->db->get('visit_type');
				$account_types = '';
				if($query_visit_type->num_rows() > 0)
				{
					foreach ($query_visit_type->result() as $key => $value) {
						# code...
						$account_types .= $value->visit_type_name;
					}
				}

				$time = strtotime($appointment_start_time);
				$branch_code = $this->session->userdata('branch_code');
				$branch_id = $this->session->userdata('branch_id');

				if($branch_id == 0)
				{
					$branch_id =2;
					$branch_code ='RS';
				}
				// $startTime = date("H:i", strtotime('-30 minutes', $time));
				$endTime = date("H:i", strtotime('+'.$minutes_to_add.' minutes', $time));
				$visit_data  = array(
								'visit_date' => $appointment_date, 
								'appointment_id' => 1, 
								'time_start' => $appointment_start_time,
								'time_end' => $endTime, 
								'procedure_done' => $procedure_done,
								'personnel_id' => $this->input->post('dentist_id'.$appointment_id), 
								'patient_id' => $patients_id,
								'visit_type' => $this->input->post('visit_type_id'.$appointment_id), 
								'close_card' => 2,
								'sync_status' => 0,
								'visit_type' => $visit_type_id,
								'branch_code' => $branch_code,
								'branch_id' => $branch_id,
								'visit_account' => $account_types
							);
			
				$this->db->insert('visit', $visit_data);
				$visit_id = $this->db->insert_id();
				$this->reception_model->send_visit_to_cloud($visit_id);
				$appointment_array['visit_id'] = $visit_id;

				$appointment_array['appointment_date_time_end'] = $appointment_date.'T'.$endTime;
			}
			else
			{
				$minutes_to_add = $this->input->post('event_duration');
				$time = strtotime($appointment_start_time);
				$endTime = date("H:i", strtotime('+'.$minutes_to_add.' minutes', $time));
				$appointment_array['appointment_date_time_end'] = $appointment_date.'T'.$endTime;
				$procedure_done =  $this->input->post('procedure_done');
			}

			$appointment_status = $this->input->post('appointment_status');

			if(empty($appointment_status))
			{
				$appointment_status = 1;
			}
			$appointment_array['event_description'] = $procedure_done;
			$appointment_array['event_name'] = $event_name.' '.$account_types;			
			$appointment_array['appointment_status'] = $appointment_status;
			$appointment_array['sync_status'] = 0;
			$appointment_array['appointment_type'] = $appointment_type;
			$appointment_array['duration'] = $minutes_to_add;
			$this->db->where('appointment_id',$appointment_id);
			$this->db->update('appointments',$appointment_array);
			$this->reception_model->send_appointments_to_cloud($appointment_id);
			$data['message'] = 'success';
		}

		echo json_encode($data);

	}

		function get_todays_appointments($todays_date= null)
	{	

		if(empty($todays_date))
		{
			parse_str(substr(strrchr($_SERVER['REQUEST_URI'], "?"), 1), $_GET);

			$epoch2 = $_GET['end'];

			$dt = new DateTime("@$epoch2");  // convert UNIX timestamp to PHP DateTime
			$todays_date =  $dt->format('Y-m-d');
		}
		else
		{
			$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
			$todays_date =  $dt->format('Y-m-d');
		}
		
		// output = 2017-01-01 00:00:00
		// $start_date_new = str_replace('T', ' ', $todays_date);
		// // echo json_encode($_POST);
		// $exploded =explode(" ", $start_date_new);
		// $todays_date = $exploded[0];
		// $time_start = $exploded[1];
		//get all appointments
		// var_dump($todays_date); die();
		// $visit_date = date('y-m-d',strtotime($todays_date)); 
		// var_dump($visit_date); die();
		// $todays_date = null;
		$appointments_result = $this->reception_model->get_todays_appointments($todays_date);
		
		//initialize required variables
		$totals = '';
		$highest_bar = 0;
		$r = 0;
		// var_dump($appointments_result); die();
		$data = array(
					  "success" => false,
					  "results" => array()
					);
		if($appointments_result->num_rows() > 0)
		{
			$result = $appointments_result->result();
			
			foreach($result as $res)
			{
				
				$visit_date = date('D M d Y',strtotime($res->visit_date)); 
				$time_start = $res->appointment_date_time_start; 
				$time_end = $res->appointment_date_time_end;
				$patient_id = $res->patient_id;
				$patient_othernames = $res->patient_othernames;
				$patient_surname = $res->patient_surname;				
				$visit_id = $res->visit_id;
				$appointment_id = $res->appointment;
				$resource_id = $res->resource_id;
				$event_name = $res->event_name;
				$event_description = $res->event_description;
				$appointment_status = $res->appointment_status;
				$procedure_done = $res->procedure_done;
				$event_name = $res->event_name;
				$event_description = $res->event_description;
				$resource_id = $res->resource_id;
				$patient_phone1 = $res->patient_phone1;
				$branch_code = $res->branch_code;
				$appointment_type = $res->appointment_type;

				if($appointment_status == 0)
				{
					$color = 'blue';
					$status_name = 'unassigned';
				}
				else if($appointment_status == 1)
				{
					$color = '';
					$status_name = 'unassigned';
				}
				else if($appointment_status == 2)
				{
					$color = 'green';
					$status_name = 'Confirmed';
				}
				else if($appointment_status == 3)
				{
					$color = 'red';
					$status_name = 'Cancelled';
				}
				else if($appointment_status == 4)
				{
					$color = 'purple';
					$status_name = 'Showed';
				}
				else if($appointment_status == 5)
				{
					$color = 'black';
					$status_name = 'No Showed';
				}
				else if($appointment_status == 6)
				{
					$color = 'DarkGoldenRod';
					$status_name = 'Notified';
				}
				else if($appointment_status == 7)
				{
					$color = '';
					$status_name = 'Not Notified';
				}
				else if($appointment_status == 8)
				{
					$color = 'blueviolet';
					$status_name = 'Not Notified';
				}
				else
				{
					$color = 'orange';
					$status_name = '';
				}

				if(empty($patient_data))
				{
					$patient_data = '';
				}
				if(empty($procedure_done))
				{
					$procedure_done = '';
				}
				$visit_type_name = $patient_phone1.'';
				if($appointment_type == 1 AND !empty($visit_id))
				{
					$visit_order = 'visit_id';		    
					$visit_where = 'visit_type.visit_type_id = visit.visit_type AND visit_id = '.$visit_id;
					$visit_table = 'visit,visit_type';

					$visit_query = $this->reception_model->get_all_visit_type_details($visit_table, $visit_where,$visit_order);

					if($visit_query->num_rows() > 0)
					{
						foreach ($visit_query->result() as $key => $value) {
							# code...
							$visit_type_name .= ' '.$value->visit_type_name;
						}
					}
					// $visit_type_name .= ' '.$visit_type_name;
				}


				
				$data['results'][] = array(
											'id'=>$appointment_id,
										    'title' => $event_name,
										    'start' => $time_start,
										    'end' => $time_end,
										    'description' => $event_description.' '.$branch_code,
										    'resourceId' => $resource_id,
										    'backgroundColor'=>$color,
										    'borderColor'=>$color,
										    'className'=>'fc-nonbusiness'
										    
										  );
				
				$r++;
			}
		}
		
		
		// var_dump($data['results']); die();
		$data['success'] = true; // if you want to update `status` as well
		echo json_encode($data['results']);
	}


	public function get_appointments_details($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.created as date_created,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,personnel.personnel_fname,personnel.personnel_onames,patients.*');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$this->db->join('personnel','personnel.personnel_id = appointments.created_by','left');
		$query = $this->db->get('appointments');
		$data['status'] = 1;
		$data['query'] = $query;
		$results = $this->load->view('calendar/appointment_details', $data,true);

		// echo json_encode($data);
		echo $results;
	}



	public function delete_note_details($calendar_note_id,$status)
	{
		$app_update['note_delete'] = 1;
		$app_update['sync_status'] = 0;
		$this->db->where('calendar_note_id',$calendar_note_id);
		$this->db->update('calendar_note',$app_update);
		$data['message'] = 'success';
		echo json_encode($data);

	}

	public function get_todays_top_notes($todays_date,$branch_id)
	{
		$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
		$todays_date =  $dt->format('Y-m-d');
		$v_data['todays_date'] =$todays_date;
		$v_data['branch_id'] =$branch_id;
		
		$data['content'] = $this->load->view('todays_top_notes', $v_data, TRUE);
		$data['message'] = 'success';
		echo json_encode($data);
		
	}


	public function get_todays_bottom_notes($todays_date,$branch_id)
	{

		$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
		$todays_date =  $dt->format('Y-m-d');


		$v_data['todays_date'] =$todays_date;
		$v_data['branch_id'] =$branch_id;
		
		$data['content'] = $this->load->view('todays_bottom_notes', $v_data, TRUE);
		$data['message'] = 'success';
		echo json_encode($data);
		
	}


	public function get_featured_notes($todays_date,$branch_id)
	{
		$dt = new DateTime("@$todays_date");  // convert UNIX timestamp to PHP DateTime
		$todays_date =  $dt->format('Y-m-d');
		// var_dump($todays_date); die();
		$v_data['todays_date'] =$todays_date;
		$v_data['branch_id'] =$branch_id;
		// var_dump($todays_date);die();
		$data['content'] = $this->load->view('todays_featured_notes', $v_data, TRUE);
		$data['message'] = 'success';
		echo json_encode($data);
		
	}
	public function add_note($date_created,$branch_id)
	{
		$this->form_validation->set_rules('schedule', 'Schedule', 'required');
		$this->form_validation->set_rules('schedule_note', 'Note', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$featured = $this->input->post('featured');
		$end_date = $this->input->post('end_date');

					
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			// $data['message'] = validation_errors();
			$data['message'] = 'fail';
		}
		else
		{
			$schedule = $this->input->post('schedule');
			$schedule_note = $this->input->post('schedule_note');
			$type = $this->input->post('type');
			$dt = new DateTime("@$date_created");  // convert UNIX timestamp to PHP DateTime
			$todays_date =  $dt->format('Y-m-d');
			$date_count = 0;
			if(!empty($end_date))
			{
				$date_count = $this->reception_model->dateDiffInDays($todays_date,$end_date);
			}
			$date_today = $todays_date;
			if($date_count > 0 AND $type == 1)
			{

				for ($i=0; $i <= $date_count; $i++) { 
					# code...
					
					
					$appointment_array['resource_id'] = $schedule;
					$appointment_array['note'] = $schedule_note;			
					$appointment_array['section_id'] = $type;
					$appointment_array['featured_note'] = $featured;
					$appointment_array['end_date'] = $end_date;
					$appointment_array['created'] = $date_today;
					$appointment_array['branch_id'] = $branch_id;
					$appointment_array['sync_status'] = 0;
					$appointment_array['created_by'] = $this->session->userdata('personnel_id');

					$this->db->insert('calendar_note',$appointment_array);
					$checked_date = date('Y-m-d',strtotime('+1 day', strtotime($date_today)));
					$date_today = $checked_date;
				}
			}
			else
			{
				$appointment_array['resource_id'] = $schedule;
				$appointment_array['note'] = $schedule_note;			
				$appointment_array['section_id'] = $type;
				$appointment_array['featured_note'] = $featured;
				$appointment_array['end_date'] = $end_date;
				$appointment_array['created'] = $todays_date;
				$appointment_array['branch_id'] = $branch_id;
				$appointment_array['sync_status'] = 0;
				$appointment_array['created_by'] = $this->session->userdata('personnel_id');

				$this->db->insert('calendar_note',$appointment_array);
			}
		
			
			$data['message'] = 'success';
		}

		echo json_encode($data);

	}


		// events sidebar
	public function get_event_details($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.created as date_created,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,personnel.personnel_fname,personnel.personnel_onames,patients.*');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$this->db->join('personnel','personnel.personnel_id = appointments.created_by','left');
		$query = $this->db->get('appointments');
		$data['status'] = 1;
		$data['query'] = $query;
		$results = $this->load->view('appointments_view_sidebar', $data,true);

		// echo json_encode($data);
		echo $results;
	}
	public function get_edit_patient_view($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.created as date_created,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,personnel.personnel_fname,personnel.personnel_onames,patients.*');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$this->db->join('personnel','personnel.personnel_id = appointments.created_by','left');
		$query = $this->db->get('appointments');
		$data['status'] = 1;
		$data['appointment_query'] = $query;
		$data['appointment_id'] = $appointment_id;
		$data['relationships'] = $this->reception_model->get_relationship();
		$data['genders'] = $this->reception_model->get_gender();
		$data['insurance'] = $this->reception_model->get_insurance();
		$data['branches'] = $this->reception_model->get_branches();
		$results = $this->load->view('edit_patient_information', $data,true);

		// echo json_encode($data);
		echo $results;
	}

	public function get_reschedule_div($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.created as date_created,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,personnel.personnel_fname,personnel.personnel_onames,patients.*');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$this->db->join('personnel','personnel.personnel_id = appointments.created_by','left');
		$query = $this->db->get('appointments');
		$data['status'] = 1;
		$data['query'] = $query;
		$data['appointment_id'] = $appointment_id;
		$results = $this->load->view('reschedule_patient_appointment_details', $data,true);

		// echo json_encode($data);
		echo $results;
	}

	public function update_appointment_details($appointment_id,$status)
	{
		// if($status == 4)
		$this->db->select('appointments.*,visit.patient_id,visit.visit_date');
		$this->db->where('appointments.appointment_id ='.$appointment_id.' AND appointments.visit_id = visit.visit_id');
		$appointment_rs = $this->db->get('appointments,visit');

		$visit_id = NULL;


		if($appointment_rs->num_rows() > 0 AND $status == 4)
		{
			foreach ($appointment_rs->result() as $key => $value_doctor) {
				# code...
				$visit_id = $value_doctor->visit_id;
				$patient_id = $value_doctor->patient_id;

				$visit_date = $value_doctor->visit_date;

			}


			if($visit_date == date('Y-m-d'))
			{


				$branch_id = $this->session->userdata('branch_id');
				$branch_code = $this->session->userdata('branch_code');


				$app_update_visit['visit_time_in'] = date('Y-m-d H:i:s');	
				$app_update_visit['visit_time'] = date('Y-m-d H:i:s');	
				$app_update_visit['close_card'] = 0;
				$app_update_visit['visit_type'] = 1;
				$app_update_visit['branch_id'] = $branch_id;
				$app_update_visit['branch_code'] = $branch_code;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit',$app_update_visit);


				// var_dump($visit_id);die();
				$this->reception_model->set_visit_department($visit_id, 7);


				// chek if there is another visit before this 
				$this->db->where('patient_id = '.$patient_id.' AND visit_id < '.$visit_id);
				$query_less = $this->db->get('visit');

				$less_items = $query_less->num_rows();

				// check if there is another visit of this patient after this day
				$this->db->where('patient_id = '.$patient_id.' AND visit_id > '.$visit_id);
				$query_more = $this->db->get('visit');

				$more_items = $query_more->num_rows();


				if($less_items > 0 AND $more_items > 0)
				{
					// update the visit is like a revisit
					$visit_update['revisit'] = 2;

				}
				else if($less_items == 0 AND $more_items > 0)
				{

					// update the visit new visit
					$visit_update['revisit'] = 1;

				}
				else if($less_items == 0 AND $more_items == 0)
				{

					// update the visit new visit
					$visit_update['revisit'] = 1;

				}

				else if($less_items > 0 AND $more_items == 0)
				{

					// update the visit revisit visit
					$visit_update['revisit'] = 2;

				}

				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit',$visit_update);



				$this->reception_model->set_last_visit_date($patient_id, $visit_date);
			}

		}
		
		$app_update['appointment_status'] = $status;
		$app_update['sync_status'] = 0;
		$this->db->where('appointment_id',$appointment_id);
		$this->db->update('appointments',$app_update);
		// $this->reception_model->send_appointments_to_cloud($appointment_id);
		$data['message'] = 'success';
		echo json_encode($data);

	}

	public function edit_appointment_detail()
	{
		$this->form_validation->set_rules('appointment_id', 'Appointment', 'required');
		$this->form_validation->set_rules('appointment_type', 'Appointment', 'required');
		$this->form_validation->set_rules('visit_date', 'Date', 'required');
		$this->form_validation->set_rules('time_start', 'Start Time', 'required');
		$appointment_id = $this->input->post('appointment_id');
		$appointment_type = $this->input->post('appointment_type');

		// $this->form_validation->set_rules('appointment_title', 'Title', 'required');
		$this->form_validation->set_rules('event_duration', 'Duration');
		

		
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			$data['message'] = validation_errors();
			// $data['message'] = 'fail';
		}
		else
		{
			$procedure_done = $this->input->post('procedure_done');
			$appointment_type = $this->input->post('appointment_type');
			$visit_type_id = $this->input->post('visit_type_id');			
			// $event_name = $this->input->post('appointment_title');
			$time_start = $this->input->post('time_start');
			$visit_date = $this->input->post('visit_date');
				
			$time_in_24_hour_format  = date("H:i", strtotime($time_start));


			$this->db->where('appointment_id',$appointment_id);
			$this->db->select('appointments.*');
			$query_appointment = $this->db->get('appointments');
			if($query_appointment->num_rows() > 0)
			{
				foreach ($query_appointment->result() as $key => $appointment) {
					# code...
					$appointment_id = $appointment->appointment_id;
					$visit_id = $appointment->visit_id;
				    $appointment_date = $appointment->appointment_date;
				    $appointment_start_time = $appointment->appointment_start_time;
				    $appointment_end_time = $appointment->appointment_end_time;


				}

			}


			
			$visit_array['visit_type'] = $visit_type_id;
			$visit_array['visit_date'] = $visit_date;
			$visit_array['time_start'] = $time_in_24_hour_format;
			$visit_array['personnel_id'] = $this->input->post('personnel_id');
			$visit_array['sync_status'] = 0;
			$this->db->where('visit_id',$visit_id);
			$this->db->update('visit',$visit_array);
			$this->reception_model->send_visit_to_cloud($visit_id);
			
			$minutes_to_add = $this->input->post('visit_time');
			$time = strtotime($time_in_24_hour_format);
			$endTime = date("H:i:s", strtotime('+'.$minutes_to_add.' minutes', $time));
			$appointment_array['appointment_date_time_start'] = $visit_date.'T'.$time_in_24_hour_format.':00+03:00';
			$appointment_array['appointment_date_time_end'] = $visit_date.'T'.$endTime;
			$appointment_array['appointment_start_time'] = $time_in_24_hour_format;
			$appointment_array['appointment_end_time'] = $endTime;
			$appointment_array['resource_id'] = $this->input->post('personnel_id');
			$procedure_done = $this->input->post('procedure_done');

			
			$appointment_array['event_description'] = $procedure_done;
			// $appointment_array['event_name'] = $event_name;			
			$appointment_array['appointment_status'] = 1;
			$appointment_array['sync_status'] = 0;
			$appointment_array['appointment_date'] = $visit_date;
			$appointment_array['appointment_type'] = $appointment_type;
			$appointment_array['duration'] = $minutes_to_add;
			$this->db->where('appointment_id',$appointment_id);
			$this->db->update('appointments',$appointment_array);	

			$this->reception_model->send_appointments_to_cloud($appointment_id);

			// $visit_array['time_end'] = $endTime;
			$visit_array['time_start'] = $time_in_24_hour_format;
			$visit_array['sync_status'] = 0;
			$this->db->where('visit_id',$visit_id);
			$this->db->update('visit',$visit_array);

			$this->reception_model->send_visit_to_cloud($visit_id);
			$data['message'] = 'success';
			$data['appointment_date'] = $visit_date;
		}

		echo json_encode($data);

	}


	public function update_patient_details_appointments($patient_id)
	{
		//form validation rules
		$this->form_validation->set_rules('title_id', 'Title', 'is_numeric|xss_clean');
		$this->form_validation->set_rules('patient_surname', 'Name', 'required|xss_clean');
		// $this->form_validation->set_rules('patient_othernames', 'Other Names', 'xss_clean');
		$this->form_validation->set_rules('patient_dob', 'Date of Birth', 'trim|xss_clean');
		$this->form_validation->set_rules('gender_id', 'Gender', 'xss_clean');
		$this->form_validation->set_rules('religion_id', 'Religion', 'xss_clean');
		$this->form_validation->set_rules('civil_status_id', 'Civil Status', 'xss_clean');
		$this->form_validation->set_rules('patient_email', 'Email Address', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_address', 'Postal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_postalcode', 'Postal Code', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_town', 'Town', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_phone1', 'Primary Phone', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_phone2', 'Other Phone', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_number', 'Patient Number', 'xss_clean');
		$this->form_validation->set_rules('patient_kin_sname', 'Next of Kin Surname', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_kin_othernames', 'Next of Kin Other Names', 'trim|xss_clean');
		$this->form_validation->set_rules('category','Cateogory', 'trim|xss_clean');
		$this->form_validation->set_rules('relationship_id', 'Relationship With Kin', 'xss_clean');
		$this->form_validation->set_rules('patient_national_id', 'National ID', 'trim|xss_clean');
		$this->form_validation->set_rules('next_of_kin_contact', 'Next of Kin Contact', 'trim|xss_clean');
		$this->form_validation->set_rules('insurance_company_id', 'Insurance', 'trim|xss_clean');
		
		
		// var_dump($_POST);die();
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->reception_model->edit_other_patient($patient_id))
			{

				$appointment_id = $this->input->post('appointment_id');
				if(!empty($appointment_id))
				{
					$this->db->where('visit.visit_id = appointments.visit_id AND visit.patient_id = patients.patient_id AND appointments.appointment_id ='.$appointment_id);
					$this->db->select('patients.patient_surname,patients.patient_phone1,patients.patient_number,visit_type.visit_type_name');
					$this->db->join('visit_type','visit_type.visit_type_id = visit.visit_type','LEFT');
					$query = $this->db->get('appointments,visit,patients');

					
					$event_name = '';
					if($query->num_rows() > 0)
					{
						foreach ($query->result() as $key => $value) {
							# code...
							$patient_number = $value->patient_number;
							$patient_surname = $value->patient_surname;
							// $patient_first_name = $value->patient_first_name;
							$patient_othernames = $value->patient_othernames;
							$visit_type_name = $value->visit_type_name;
							$patient_phone1 = $value->patient_phone1;

							$event_name = $patient_surname.' - '.$patient_number.' '.$patient_phone1.' '.$visit_type_name;
						}
					}


					

					
					$appointment_array['event_name'] = $event_name;			
					$appointment_array['appointment_status'] = 1;
					$appointment_array['sync_status'] = 0;

					$this->db->where('appointment_id',$appointment_id);
					$this->db->update('appointments',$appointment_array);
				}
				$data['result'] = 'You have successfully edited patient details';
				$data['message'] = 'success';
			}
			else
			{
				
				$data['result'] = 'Sorry could not update patient details';
				$data['message'] = 'fail';
			}

		}else
		{
			$data['result'] = strip_tags(validation_errors());
			$data['message'] = 'fail';
		}



		
		echo json_encode($data);
	}

	public function reschedule_appointment_details($appointment_id)
	{
		$this->form_validation->set_rules('appointment_id', 'Appointment', 'xss_clean');
		$this->form_validation->set_rules('visit_time', 'Period', 'trim|xss_clean');
		$this->form_validation->set_rules('personnel_id', 'Doctor', 'trim|xss_clean');
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'trim|date|xss_clean');
		$this->form_validation->set_rules('procedure_done', 'Procedure Done', 'trim|xss_clean');
		$this->form_validation->set_rules('visit_type_id', 'Type of Visit', 'trim|xss_clean');
		
		
		
		// var_dump($_POST);die();
		//if form conatins invalid data
		if ($this->form_validation->run())
		{

					// var_dump($_POST);die();
				$appointment_date = $this->input->post('visit_date');
				$visit_id = $this->input->post('visit_id');
				$appointment_id = $this->input->post('appointment_id');
				$patient_phone1 = $this->input->post('patient_phone1');
				$patient_surname = $this->input->post('patient_surname');
				$personnel_id = $this->input->post('personnel_id');
				$appointment_start_time = $this->input->post('appointment_start_time');

				$name_explode = explode(' ', $patient_surname);

				$name = $name_explode[0];


				if(!empty($visit_id))
				{
					$time_start = $this->input->post('time_start');


					$appointment_start_time = date("H:i:00", strtotime($time_start));
					$minutes_to_add = $this->input->post('visit_time');
					$time = strtotime($appointment_start_time);
					$endTime = date("H:i", strtotime('+'.$minutes_to_add.' minutes', $time));
					$appointment_array['appointment_date_time_end'] = $appointment_date.'T'.$endTime;
					$appointment_array['appointment_date_time_start'] = $appointment_date.'T'.$appointment_start_time;
					// $procedure_done = '';// $this->input->post('procedure_done');
			
					$appointment_array['appointment_status'] = 1;
					$appointment_array['appointment_date'] = $appointment_date;
					$appointment_array['sync_status'] = 0;

					$this->db->where('appointment_id',$appointment_id);
					$this->db->update('appointments',$appointment_array);


					$time_start = date("H:i A", strtotime($appointment_start_time));
					


					$visit_array['visit_date'] = $appointment_date;
					$visit_array['personnel_id'] = $personnel_id;
					$visit_array['time_start'] = $time_start;
					
					// var_dump($visit_array);die();
					$this->db->where('visit_id',$visit_id);
					$this->db->update('visit',$visit_array);
					// $patient_phone1 = '0734808007';

					if(!empty($patient_phone1))
					{

						$appointment_start_time = date('H:i A',strtotime($appointment_start_time));

						$dt= $appointment_date;
				        $dt1 = strtotime($dt);
				        $dt2 = date("l", $dt1);
				        $dt3 = strtolower($dt2);

						$appointment_date = date('jS M Y',strtotime($appointment_date));


						$message = 'Hello '.$name.', kindly note that your appointment has been rescheduled to '.$dt2.' '.$appointment_date.' at '.$appointment_start_time.'. For more information contact us 0717123440. Star Dental';
						// $patient_phone1 = 704808007;
						$message_data = array(
								"phone_number" => $patient_phone1,
								"entryid" => 1,
								"message" => $message,
								"message_batch_id"=>0,
								'message_status' => 0
							);
						$this->db->insert('messages', $message_data);
						$message_id = $this->db->insert_id();
						
						$patient_phone1 = str_replace(' ', '', $patient_phone1);
						// $this->messaging_model->sms($patient_phone1,$message);
					}


				}


				

				$data['result'] = 'You have successfully reschedule appointment';
				$data['message'] = 'success';
			

		}else
		{
			$data['result'] = strip_tags(validation_errors());
			$data['message'] = 'fail';
		}

		echo json_encode($data);
	}
	public function delete_event_details($appointment_id,$status)
	{
		$app_update['appointment_delete'] = 1;
		$app_update['sync_status'] = 0;
		$this->db->where('appointment_id',$appointment_id);
		$this->db->update('appointments',$app_update);
		$this->reception_model->send_appointments_to_cloud($appointment_id);
		$data['message'] = 'success';
		echo json_encode($data);

	}

	public function edit_event()
	{
		$this->form_validation->set_rules('appointment_id', 'Event', 'required');
		$this->form_validation->set_rules('appointment_type', 'Appointment', 'required');
		$appointment_id = $this->input->post('appointment_id');
		$appointment_type = $this->input->post('appointment_type');

		$this->form_validation->set_rules('appointment_title', 'Title', 'required');
		$this->form_validation->set_rules('event_duration', 'Duration');
		

		
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			$data['message'] = validation_errors();
			// $data['message'] = 'fail';
		}
		else
		{
			$procedure_done = $this->input->post('procedure_done');
			$appointment_type = $this->input->post('appointment_type');
			
			$event_name = $this->input->post('appointment_title');
				
			


			$this->db->where('appointment_id',$appointment_id);
			$this->db->select('appointments.*');
			$query_appointment = $this->db->get('appointments');
			if($query_appointment->num_rows() > 0)
			{
				foreach ($query_appointment->result() as $key => $appointment) {
					# code...
					$appointment_id = $appointment->appointment_id;
				    $appointment_date = $appointment->appointment_date;
				    $appointment_start_time = $appointment->appointment_start_time;
				    $appointment_end_time = $appointment->appointment_end_time;


				}
			}
			
			
			$minutes_to_add = $this->input->post('event_duration');
			$time = strtotime($appointment_start_time);
			$endTime = date("H:i", strtotime('+'.$minutes_to_add.' minutes', $time));
			$appointment_array['appointment_date_time_end'] = $appointment_date.'T'.$endTime;
			$procedure_done = $this->input->post('procedure_done');
			
			$appointment_array['event_description'] = $procedure_done;
			$appointment_array['event_name'] = $event_name.' '.$account_types;			
			$appointment_array['appointment_status'] = 1;
			$appointment_array['sync_status'] = 0;
			$appointment_array['appointment_type'] = $appointment_type;
			$appointment_array['duration'] = $minutes_to_add;
			$this->db->where('appointment_id',$appointment_id);
			$this->db->update('appointments',$appointment_array);
			$this->reception_model->send_appointments_to_cloud($appointment_id);
			
			$data['message'] = 'success';
		}

		echo json_encode($data);

	}

	




}
?>