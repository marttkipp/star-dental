<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require_once "./application/modules/auth/controllers/auth.php";
date_default_timezone_set('Africa/Nairobi');
error_reporting(0);
class Reception  extends MX_Controller
{	
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('reception_model');
		$this->load->model('strathmore_population');
		$this->load->model('admin/email_model');
		$this->load->model('database');
		$this->load->model('administration/reports_model');
		$this->load->model('administration/administration_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('administration/personnel_model');
		$this->load->model('administration/sync_model');
		$this->load->model('online_diary/rooms_model');
		$this->load->model('messaging/messaging_model');
		
		$this->csv_path = realpath(APPPATH . '../assets/csv');
		
		$this->load->model('auth/auth_model');
		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}

	}
	
	public function index()
	{
		$this->session->unset_userdata('visit_search');
		$this->session->unset_userdata('patient_search');
		
		$where = 'visit.visit_delete = 0 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$this->session->userdata('branch_code').'\' AND visit.visit_date = \''.date('Y-m-d').'\'';
		
		$table = 'visit, patients, visit_type';
		$query = $this->reception_model->get_all_ongoing_visits2($table, $where, 10, 0);
		$v_data['query'] = $query;
		$v_data['page'] = 0;
		
		$v_data['visit'] = 0;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('reception_dashboard', $v_data, TRUE);
		
		$data['title'] = 'Dashboard';
		$data['sidebar'] = 'reception_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	/*
	* Code for displaying deleted patients removed
	*/
	public function patients()
	{
		$delete = 0;
		$segment = 2;
		
		$patient_search = $this->session->userdata('patient_search');
		//$where = '(visit_type_id <> 2 OR visit_type_id <> 1) AND patient_delete = '.$delete;
		$where = 'patient_delete = '.$delete;
		if(!empty($patient_search))
		{
			$where .= $patient_search;
		}
		
		else
		{
			// $where .= ' AND patients.branch_code = \''.$this->session->userdata('branch_code').'\'';
			$where .='';
		}
		
		$table = 'patients';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'patients';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_patients($table, $where, $config["per_page"], $page);
		
		if($delete == 1)
		{
			$data['title'] = 'Deleted Patients';
			$v_data['title'] = 'Deleted Patients';
		}
		
		else
		{
			$search_title = $this->session->userdata('patient_search_title');
			
			if(!empty($search_title))
			{
				$data['title'] = $v_data['title'] = 'Patients filtered by :'.$search_title;
			}
			
			else
			{
				$data['title'] = 'Patients';
				$v_data['title'] = 'Patients';
			}
		}
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['delete'] = $delete;

		// $v_data['visit_types'] = $this->reception_model->get_visit_types();
		$v_data['doctors'] = $this->reception_model->get_all_doctors();
		$v_data['rooms'] = $this->rooms_model->all_rooms();

		$v_data['branches'] = $this->reception_model->get_branches();
		$data['content'] = $this->load->view('all_patients', $v_data, true);
		
		$data['sidebar'] = 'reception_sidebar';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	/*
	*
	*	$visits = 0 :ongoing visits of the current day
	*	$visits = 1 :terminated visits
	*	$visits = 2 :deleted visits
	*	$visits = 3 :all other ongoing visits
	*
	*/
	public function visit_list($visits, $page_name = 'none')
	{
		//Deleted visits
		if($visits == 2)
		{
			$delete = 1;
		}
		//undeleted visits
		else
		{
			$delete = 0;
		}
		
		if(empty($page_name))
		{
			$segment = 4;
		}
		
		else
		{
			$segment = 5;
		}
		
		// this is it
		if($visits != 2)
		{
			$where = 'visit.visit_delete = '.$delete.' AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$this->session->userdata('branch_code').'\'';
			
			if($page_name == 'doctor')
			{
				//$where .= ' AND visit.personnel_id = '.$this->session->userdata('personnel_id');
			}
			
			//terminated visits
			if($visits == 1)
			{
				/*if($page_name == 'nurse' || $page_name == 'doctor')
				{
					$where .= ' ';
				}
				else
				{
					$where .= ' AND visit.close_card = '.$visits;	
				}*/
				$where .= ' AND visit.close_card = '.$visits;
				
			}
			
			//ongoing visits
			else
			{
				if($page_name == 'nurse' || $page_name == 'doctor')
				{
					$where .= ' ';
				}
				else
				{
					$where .= ' AND (visit.close_card = 0 OR visit.close_card = 7)';	
				}
				
				//visits of the current day
				if($visits == 0)
				{
					$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
				}
				
				else
				{
					$where .= ' AND visit.visit_date < \''.date('Y-m-d').'\'';
				}
			}
		}
		
		else
		{
			$where = 'visit.visit_delete = '.$delete.' AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$this->session->userdata('branch_code').'\'';
		}
		
		$table = 'visit, patients, visit_type';
		
		$visit_search = $this->session->userdata('visit_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'reception/visit_list/'.$visits.'/'.$page_name;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_visits2($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		if($visits == 0)
		{
			$data['title'] = 'General Queue';
			$v_data['title'] = 'General Queue';
		}
		
		elseif($visits == 2)
		{
			$data['title'] = 'Deleted Visits';
			$v_data['title'] = 'Deleted Visits';
		}
		
		elseif($visits == 3)
		{
			$data['title'] = 'Unclosed Visits';
			$v_data['title'] = 'Unclosed Visits';
		}
		
		else
		{
			$data['title'] = 'Visit History';
			$v_data['title'] = 'Visit History';
		}
		$v_data['visit'] = $visits;
		$v_data['page_name'] = $page_name;
		$v_data['delete'] = $delete;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('ongoing_visit', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	
	/*
	*
	*	$visits = 0 :ongoing visits of the current day
	*	$visits = 1 :terminated visits
	*	$visits = 2 :deleted visits
	*	$visits = 3 :all other ongoing visits
	*
	*/
	public function general_queue($page_name)
	{
		$segment = 4;
		// AND visit.visit_date = \''.date('Y-m-d').'\'
		$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type';
		// var_dump($page_name); die();
		if($page_name == 'reception')
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		
		if($page_name == 'doctor')
		{
			$where .= ' AND visit.personnel_id = '.$this->session->userdata('personnel_id');
		}
		
		if(($page_name != 'accounts') && ($page_name != 'doctor'))
		{
			// $where .= ' AND visit.branch_code = \''.$this->session->userdata('branch_code').'\'';
		}
		
		if(($page_name == 'laboratory') || ($page_name == 'radiology') || ($page_name == 'pharmacy'))
		{
			$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type';
		}
		
		$table = 'visit_department, visit, patients, visit_type';
		
		$visit_search = $this->session->userdata('general_queue_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		// var_dump($where);die();
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'reception/general_queue/'.$page_name;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_visits($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		if($page_name == 'administration')
		{
			$data['title'] = 'All visits';
			$v_data['title'] = 'All visits';
		}
		else
		{
			$data['title'] = 'General Queue';
			$v_data['title'] = 'General Queue';
		}
		
		$v_data['wards'] = $this->reception_model->get_wards();
		$v_data['doctor'] = $this->reception_model->get_doctor();
		$v_data['page_name'] = $page_name;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('general_queue', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	
	/*
	*	Add a new patient
	*
	*/
	public function add_patient($dependant_staff = NULL)
	{
		$v_data['relationships'] = $this->reception_model->get_relationship();
		$v_data['religions'] = $this->reception_model->get_religion();
		$v_data['civil_statuses'] = $this->reception_model->get_civil_status();
		$v_data['titles'] = $this->reception_model->get_title();
		$v_data['genders'] = $this->reception_model->get_gender();
		$v_data['insurance'] = $this->reception_model->get_insurance();
		$v_data['dependant_staff'] = $dependant_staff;
		$data['content'] = $this->load->view('add_patient', $v_data, true);
		
		$data['title'] = 'Add Patients';
		$data['sidebar'] = 'reception_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	/*
	*	Add a new patient
	*
	*/
	public function add_other_dependant($dependant_parent)
	{
		$v_data['relationships'] = $this->reception_model->get_relationship();
		$v_data['religions'] = $this->reception_model->get_religion();
		$v_data['civil_statuses'] = $this->reception_model->get_civil_status();
		$v_data['titles'] = $this->reception_model->get_title();
		$v_data['genders'] = $this->reception_model->get_gender();
		$v_data['dependant_parent'] = $dependant_parent;
		$patient = $this->reception_model->patient_names2($dependant_parent);
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$data['content'] = $this->load->view('add_other_dependant', $v_data, true);
		
		$data['title'] = 'Add Patients';
		$data['sidebar'] = 'reception_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	/*
	*	Register other patient
	*
	*/
	public function register_other_patient()
	{
		//form validation rules
		$this->form_validation->set_rules('title_id', 'Title', 'is_numeric|xss_clean');
		$this->form_validation->set_rules('patient_surname', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('patient_othernames', 'Other Names', 'xss_clean');
		$this->form_validation->set_rules('patient_dob', 'Date of Birth', 'trim|xss_clean');
		$this->form_validation->set_rules('gender_id', 'Gender', 'trim|xss_clean');
		$this->form_validation->set_rules('religion_id', 'Religion', 'trim|xss_clean');
		$this->form_validation->set_rules('civil_status_id', 'Civil Status', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_email', 'Email Address', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_address', 'Postal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_postalcode', 'Postal Code', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_town', 'Town', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_phone1', 'Primary Phone', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_phone2', 'Other Phone', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_kin_sname', 'Next of Kin Surname', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_kin_othernames', 'Next of Kin Other Names', 'trim|xss_clean');
		$this->form_validation->set_rules('relationship_id', 'Relationship With Kin', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_national_id', 'National ID', 'trim|xss_clean');
		$this->form_validation->set_rules('next_of_kin_contact', 'Next of Kin Contact', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_number', 'Other Phone', 'xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == FALSE)
		{
			$this->add_patient();
		}
		
		else
		{
			// var_dump($_POST); die();
			$patient_id = $this->reception_model->save_other_patient();
			// echo $patient_id; die();
			if($patient_id != FALSE)
			{
				$this->get_found_patients($patient_id,3);
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not add patient. Please try again");
				$this->add_patient();	
			}
		}
	}
	
	public function get_found_patients($patient_id,$place_id)
	{
		//  1 for students 2 for staff 3 for others 4 for dependants
		$this->session->set_userdata('patient_search', ' AND patients.patient_id = '.$patient_id);
	
		redirect('patients');
		
		
	}
	
	public function get_department_services($department_id, $selected_service_id = NULL)
	{
		echo '<option value="0">--Select Service--</option>';
		
		$service_charge = $this->reception_model->get_services_per_department($department_id);
		foreach($service_charge AS $key) 
		{
			if($selected_service_id == $key->service_id)
			{
				echo '<option value="'.$key->service_id.'" selected="selected">'.$key->service_name.'</option>';
			}
			
			else
			{
				echo '<option value="'.$key->service_id.'">'.$key->service_name.'</option>';
			}
		}
	}
	
	public function get_services_charges($patient_type_id, $service_id, $selected_service_charge_id=null)
	{
		echo '<option value="0">--Select Consultation Charge--</option>';
		
		$service_charge = $this->reception_model->get_service_charges_per_type($patient_type_id, $service_id);
		foreach($service_charge AS $key) 
		{ 
			if($selected_service_charge_id == $key->service_charge_id)
			{
				echo '<option value="'.$key->service_charge_id.'" selected="selected">'.$key->service_charge_name.' Kes. '.$key->service_charge_amount.'</option>';
			}
			
			else
			{
				echo '<option value="'.$key->service_charge_id.'">'.$key->service_charge_name.' Kes. '.$key->service_charge_amount.'</option>';
			}
		}
	}

	public function get_insurance_schemes($patient_type_id, $service_id, $selected_service_charge_id=null)
	{
		echo '<option value="0">--Select Insurance Scheme--</option>';
		
		$service_charge = $this->reception_model->get_insurance_scheme($patient_type_id, $service_id);
		foreach($service_charge AS $key) 
		{ 
			if($selected_service_charge_id == $key->insurance_scheme_name)
			{
				echo '<option value="'.$key->insurance_scheme_name.'" selected="selected">'.$key->insurance_scheme_name.'</option>';
			}
			
			else
			{
				echo '<option value="'.$key->insurance_scheme_name.'">'.$key->insurance_scheme_name.'</option>';
			}
		}
	}
	
	/*
	*	Add a visit
	*
	*/
	public function set_visit($primary_key)
	{
		$v_data["patient_id"] = $primary_key;
		$v_data['visit_departments'] = $this->reception_model->get_visit_departments();
		$v_data['visit_types'] = $this->reception_model->get_visit_types();
		$v_data['charge'] = $this->reception_model->get_service_charges($primary_key);
		$v_data['wards'] = $this->reception_model->get_wards();
		$v_data['doctor'] = $this->reception_model->get_doctor();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['patient_insurance'] = $this->reception_model->get_patient_insurance($primary_key);
		$patient = $this->reception_model->patient_names2($primary_key);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['all_rooms'] = $this->rooms_model->all_rooms();
		
		$data['content'] = $this->load->view('visit/initiate_visit', $v_data, true);
		
		
		$data['title'] = 'Create Visit';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	/*
	*	Add a visit
	*
	*/
	public function edit_visit($visit_id)
	{
		$v_data["visit_id"] = $visit_id;
		$v_data['visit_details'] = $this->reception_model->get_visit($visit_id);
		$v_data['visit_depts'] = $this->reception_model->get_visit_depts($visit_id);
		$v_data['visit_charges'] = $this->reception_model->get_visit_charges($visit_id);
		$patient = $this->reception_model->patient_names2(NULL, $visit_id);
		$v_data['patient_type'] = $patient['patient_type'];
		$v_data['patient_othernames'] = $patient['patient_othernames'];
		$v_data['patient_surname'] = $patient['patient_surname'];
		$v_data['patient_type_id'] = $patient['visit_type_id'];
		$v_data['account_balance'] = $patient['account_balance'];
		$v_data['visit_type_name'] = $patient['visit_type_name'];
		$v_data['patient_id'] = $patient['patient_id'];
		$patient_date_of_birth = $patient['patient_date_of_birth'];
		$age = $this->reception_model->calculate_age($patient_date_of_birth);
		$visit_date = $this->reception_model->get_visit_date($visit_id);
		$gender = $patient['gender'];
		$visit_date = date('jS M Y',strtotime($visit_date));
		$v_data['age'] = $age;
		$v_data['visit_date'] = $visit_date;
		$v_data['gender'] = $gender;
		
		$v_data['visit_departments'] = $this->reception_model->get_visit_departments();
		$v_data['visit_types'] = $this->reception_model->get_visit_types();
		$v_data['charge'] = $this->reception_model->get_service_charges2($visit_id);
		$v_data['wards'] = $this->reception_model->get_wards();
		$v_data['doctor'] = $this->reception_model->get_doctor();
		$v_data['type'] = $this->reception_model->get_types();
		
		$data['content'] = $this->load->view('visit/edit_visit', $v_data, true);
		
		$data['title'] = 'Edit Visit';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function save_visit($patient_id)
	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		$this->form_validation->set_rules('department_id', 'Department', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$visit_type_id = $this->input->post("visit_type_id"); 
		
		if(isset($_POST['department_id'])){
			if(($_POST['department_id'] == 2) || ($_POST['department_id'] == 7) || ($_POST['department_id'] == 4) || ($_POST['department_id'] == 10))
			{
				//if nurse visit (7) or theatre (14) service must be selected
				$this->form_validation->set_rules('personnel_id', 'Doctor', 'is_natural_no_zero');
				$this->form_validation->set_rules('service_charge_name', 'Consultation Type', 'xss_clean');
				$service_charge_id = $this->input->post("service_charge_name");
				$doctor_id = $this->input->post('personnel_id');
			}
			else if($_POST['department_id'] == 12)
			{
				//if nurse visit doctor must be selected
				$this->form_validation->set_rules('personnel_id2', 'Doctor', 'required|is_natural_no_zero');
				$this->form_validation->set_rules('service_charge_name2', 'Consultation Type', 'required|is_natural_no_zero');
				$service_charge_id = $this->input->post("service_charge_name2");
				$doctor_id = $this->input->post('personnel_id2');
			}
			else 
			{
				$service_charge_id = 0;
				$doctor_id = 0;
			}
		}
		
		if($visit_type_id != 1)
		{
			$this->form_validation->set_rules('insurance_limit', 'Insurance limit', 'xss_clean|numeric');
			$this->form_validation->set_rules('insurance_number', 'Insurance number', 'required');
			$this->form_validation->set_rules('insurance_description', 'Insurance description', 'xss_clean');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->set_visit($patient_id);
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit");
			$insurance_number = $this->input->post("insurance_number");
			$insurance_description = $this->input->post("insurance_description");
			
			//$visit_type = $this->get_visit_type($type_name);
			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			
			$appointment_id = $this->input->post("appointment_id");
			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}
			//  check if the student exisit for that day and the close card 0;
			$check_visits = $this->reception_model->check_patient_exist($patient_id, $visit_date);
			$check_count = count($check_visits);
			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another visit that has been initiated');
				redirect('reception/general-queue');
			}
			
			else
			{
				//create visit
				$visit_id = $this->reception_model->create_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card, $insurance_description);
				$this->sync_model->sync_patient_bookings($visit_id);
				//save consultation charge for nurse visit, counseling or theatre
				if($_POST['department_id'] == 2 || $_POST['department_id'] == 7 || $_POST['department_id'] == 12 || $_POST['department_id'] == 4 || $_POST['department_id'] == 10)
				{
					if(!empty($service_charge_id))
					{
						$this->reception_model->save_visit_consultation_charge($visit_id, $service_charge_id);	
					}
				}
				
				//set visit department if not appointment
				if($appointment_id == 0)
				{
					//update patient last visit
					$this->reception_model->set_last_visit_date($patient_id, $visit_date);
					//$this->reception_model->create_invoice_number($visit_id);
					$department_id = $this->input->post('department_id');
					if($this->reception_model->set_visit_department($visit_id, $department_id, $visit_type_id))
					{
						if($appointment_id == 0)
						{
							$this->session->set_userdata('success_message', 'Visit has been initiated');
						}
						else
						{
							$this->session->set_userdata('success_message', 'Appointment has been created');
						}
					}
					else
					{
						$this->session->set_userdata('error_message', 'Internal error. Could not add the visit');
					}
				}
				
				else
				{
					$this->session->set_userdata('success_message', 'Visit has been initiated');
				}
				
				redirect('reception/general-queue');
			}
			
		}
	}
public function save_visit2($patient_id)
	

	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		$this->form_validation->set_rules('department_id', 'Department', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('procedure_done', 'Procedure Done', 'xss_clean');
		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$visit_type_id = $this->input->post("visit_type_id"); 
		
		if(isset($_POST['department_id'])){
			if(($_POST['department_id'] == 2) || ($_POST['department_id'] == 7) || ($_POST['department_id'] == 14) || ($_POST['department_id'] == 10))
			{
				//if nurse visit (7) or theatre (14) service must be selected
				$this->form_validation->set_rules('personnel_id', 'Doctor', 'is_natural_no_zero');
				$this->form_validation->set_rules('service_charge_name', 'Consultation Type', 'xss_clean');
				$service_charge_id = $this->input->post("service_charge_name");
				$doctor_id = $this->input->post('personnel_id');
			}
			else if($_POST['department_id'] == 12)
			{
				//if nurse visit doctor must be selected
				$this->form_validation->set_rules('personnel_id2', 'Doctor', 'required|is_natural_no_zero');
				$this->form_validation->set_rules('service_charge_name2', 'Consultation Type', 'required|is_natural_no_zero');
				$service_charge_id = $this->input->post("service_charge_name2");
				$doctor_id = $this->input->post('personnel_id2');
			}
			else 
			{
				$service_charge_id = 0;
				$doctor_id = 0;
			}
		}
		
		if($visit_type_id != 1)
		{
			$this->form_validation->set_rules('insurance_limit', 'Insurance limit', 'xss_clean|numeric');
			$this->form_validation->set_rules('insurance_number', 'Insurance number', 'required');
			$this->form_validation->set_rules('insurance_description', 'Insurance description', 'xss_clean');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->set_visit($patient_id);
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit");
			$insurance_number = $this->input->post("insurance_number");
			$insurance_description = $this->input->post("insurance_description");
			
			//$visit_type = $this->get_visit_type($type_name);
			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			
			$appointment_id = $this->input->post("appointment_id");
			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}
			//  check if the student exisit for that day and the close card 0;
			$check_visits = $this->reception_model->check_patient_exist($patient_id, $visit_date);
			$check_count = count($check_visits);
			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another visit that has been initiated');
				redirect('reception/appointments-list');
			}
			
			else
			{
				//create visit
				$visit_id = $this->reception_model->create_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card, $insurance_description);
				$this->sync_model->sync_patient_bookings($visit_id);
				//save consultation charge for nurse visit, counseling or theatre
				if($_POST['department_id'] == 2 || $_POST['department_id'] == 7 || $_POST['department_id'] == 12 || $_POST['department_id'] == 14 || $_POST['department_id'] == 10)
				{
					if(!empty($service_charge_id))
					{
						$this->reception_model->save_visit_consultation_charge($visit_id, $service_charge_id);	
					}
				}
				
				//set visit department if not appointment
				if($appointment_id == 0)
				{
					//update patient last visit
					$this->reception_model->set_last_visit_date($patient_id, $visit_date);
					//$this->reception_model->create_invoice_number($visit_id);
					$department_id = $this->input->post('department_id');
					if($this->reception_model->set_visit_department($visit_id, $department_id, $visit_type_id))
					{
						if($appointment_id == 0)
						{
							$this->session->set_userdata('success_message', 'Visit has been initiated');
						}
						else
						{
							$this->session->set_userdata('success_message', 'Appointment has been created');
						}
					}
					else
					{
						$this->session->set_userdata('error_message', 'Internal error. Could not add the visit');
					}
				}
				
				else
				{
					$this->session->set_userdata('success_message', 'Visit has been initiated');
				}
				
				redirect('reception/appointments-list');
			}
			
		}
	}
	public function save_inpatient_visit($patient_id)
	{
		$this->form_validation->set_rules('visit_date', 'Admission Date', 'required');
		$this->form_validation->set_rules('personnel_id', 'Doctor', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('service_charge_name', 'Charge Charge', 'required|is_natural_no_zero');
		$doctor_id = $this->input->post('personnel_id');
		$room_id = 1;//$this->input->post("room_id"); 
		$visit_type_id = $this->input->post("visit_type_id"); 
		
		if($visit_type_id != "1")
		{
			// $this->form_validation->set_rules('insurance_limit'.$patient_id, 'Insurance limit', 'required');
			$this->form_validation->set_rules('insurance_number', 'Insurance number', 'required');
			$this->form_validation->set_rules('insurance_description', 'Insurance Scheme', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			// $this->set_visit($patient_id);

			// var_dump($_POST);die();
			$this->session->set_userdata('error_message', 'Please ensure that you have all the values entered correctly');
			redirect('patients');
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit");
			$insurance_number = $this->input->post("insurance_number");
			$insurance_description = $this->input->post("insurance_description");

			$patients_array['insurance_company_id'] = $visit_type_id;
			$patients_array['scheme_name'] = $insurance_description;
			$patients_array['insurance_number'] = $insurance_number;

			$this->db->where('patient_id',$patient_id);
			$this->db->update('patients',$patients_array);
			
			$visit_date = $this->input->post("visit_date");
			$mcc = $this->input->post("mcc");
			
			$close_card = 0;
			//  check if the student exisit for that day and the close card 0;
			$check_visits = $this->reception_model->check_patient_exist($patient_id, $visit_date);
			$check_count = count($check_visits);
			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another visit that has been initiated');
				redirect('patients');
			}
			
			else
			{
				//create visit
				$this->session->set_userdata('success_message', 'Visit successfully created');
				
				$visit_id = $this->reception_model->create_inpatient_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $close_card, $room_id,$insurance_description);
				$this->reception_model->update_patient_detail($visit_id);
				$service_charge_id = $this->input->post("service_charge_name");
				$this->reception_model->save_visit_consultation_charge($visit_id, $service_charge_id);


				
				redirect('queue');
			}
		}
	}
	
	public function save_inpatient_visit_older($patient_id)
	{
		$this->form_validation->set_rules('visit_date'.$patient_id, 'Admission Date', 'required');
		$this->form_validation->set_rules('room_id'.$patient_id, 'Room', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('visit_type_id'.$patient_id, 'Visit type', 'required|is_natural_no_zero');
		
		$room_id = $this->input->post("room_id".$patient_id); 
		$visit_type_id = $this->input->post("visit_type_id".$patient_id); 
		
		if($visit_type_id != 1)
		{
			// $this->form_validation->set_rules('insurance_limit'.$patient_id, 'Insurance limit', 'required');
			// $this->form_validation->set_rules('insurance_number'.$patient_id, 'Insurance number', 'required');
			// $this->form_validation->set_rules('insurance_description'.$patient_id, 'Company', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			// $this->set_visit($patient_id);
			$this->session->set_userdata('error_message', 'Please ensure that you have all the values entered correctly');
			redirect('patients');
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit".$patient_id);
			$insurance_number = $this->input->post("insurance_number".$patient_id);
			$insurance_description = $this->input->post("insurance_description".$patient_id);
			
			$visit_date = $this->input->post("visit_date".$patient_id);
			$mcc = $this->input->post("mcc".$patient_id);
			
			$close_card = 0;
			//  check if the student exisit for that day and the close card 0;
			$check_visits = $this->reception_model->check_patient_exist($patient_id, $visit_date);
			$check_count = count($check_visits);
			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another visit that has been initiated');
				redirect('patients');
			}
			
			else
			{
				//create visit
				$this->session->set_userdata('success_message', 'Visit successfully created');
				
				$visit_id = $this->reception_model->create_inpatient_visit($visit_date, $patient_id, $doctor_id =0, $insurance_limit, $insurance_number, $visit_type_id, $close_card, $room_id,$insurance_description);
				
				
				redirect('queue');
			}
		}
	}

	public function save_inpatient_visit_old($patient_id)
	{
		$this->form_validation->set_rules('visit_date', 'Admission Date', 'required');
		$this->form_validation->set_rules('ward_id', 'Ward', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('personnel_id', 'Doctor', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('service_charge_name', 'Consultation charge', 'required|is_natural_no_zero');
		
		$ward_id = $this->input->post("ward_id"); 
		$visit_type_id = $this->input->post("visit_type_id"); 
		$doctor_id = $this->input->post("personnel_id");
		$service_charge_id = $this->input->post("service_charge_name");
		
		if($visit_type_id != 1)
		{
			$this->form_validation->set_rules('insurance_limit', 'Insurance limit', 'required');
			$this->form_validation->set_rules('insurance_number', 'Insurance number', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->set_visit($patient_id);
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit");
			$insurance_number = $this->input->post("insurance_number");
			
			$visit_date = $this->input->post("visit_date");
			
			$close_card = 0;
			//  check if the student exisit for that day and the close card 0;
			$check_visits = $this->reception_model->check_patient_exist($patient_id, $visit_date);
			$check_count = count($check_visits);
			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another visit that has been initiated');
				redirect('reception/set_visit/'.$patient_id);
			}
			
			else
			{
				//create visit
				$visit_id = $this->reception_model->create_inpatient_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $close_card, $ward_id);
				
				//save admission fee
				if($this->reception_model->save_admission_fee($visit_type_id, $visit_id))
				{
					//save consultation fee
					$this->reception_model->save_visit_consultation_charge($visit_id, $service_charge_id);
					
					$this->session->set_userdata('success_message', 'Inpatient visit initiated successfully');
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'Unable to save admission fee');
				}
				
				redirect('reception/inpatients');
			}
		}
	}
	
	public function update_patient_number()
	{
		// $invoice_number = $this->reception_model->create_invoice_number();
		// var_dump($invoice_number); die();
		$this->db->select('*');
		$this->db->where('patient_id > 0');
		$query = $this->db->get('patients');

		if($query->num_rows() > 0 )
		{
			$number = 0;
			foreach ($query->result() as $key) {
				# code...
				$patient_id = $key->patient_id;
				$patient_phone1 = '0'.$key->patient_phone1;
				// $patient_phone1 = $key->patient_phone1;
				// $current_patient_number = $key->current_patient_number;
				// $patient_id = $key->patient_id;
				$year = date('Y');

				$number++;

				// $explode = explode('/', $current_patient_number);

				// $number = $explode[0];
				// $year = $explode[1];

				// $patient_phone1 = str_replace(' ', '', $patient_phone1);

				$update_array = array('patient_phone1'=>$patient_phone1);

				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients',$update_array);

				// if(empty($patient_number))
				// {
				// 	$explode = explode('/', $current_patient_number);

				// 	$number = $explode[0];
				// 	$year = $explode[1];

				// 	// $patient_number = str_replace('17', '', $patient_number);

				// 	$update_array = array('patient_number'=>$number,'patient_year'=>$year);

				// 	$this->db->where('patient_id',$patient_id);
				// 	$this->db->update('patients',$update_array);

				// }
				// else
				// {

				// 	// $explode = explode('/', $patient_number);

				// 	// $number = $explode[0];
				// 	// $year = $explode[1];

				// 	// // $patient_number = str_replace('17', '', $patient_number);

				// 	// $update_array = array('patient_number'=>$number,'patient_year'=>$year);

				// 	// $this->db->where('patient_id',$patient_id);
				// 	// $this->db->update('patients',$update_array);

				// }
			}
		}
	}
	
	public function search_patients()
	{
		$patient_national_id = $this->input->post('patient_national_id');
		$patient_number = $this->input->post('patient_number');
		$patient_phone = $this->input->post('patient_phone');
		$search_title = '';
		
		if(!empty($patient_number))
		{
			$search_title .= ' patient number <strong>'.$patient_number.'</strong>';
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($patient_national_id))
		{
			$search_title .= ' I.D. number <strong>'.$patient_national_id.'</strong>';
			$patient_national_id = ' AND patients.patient_national_id = \''.$patient_national_id.'\' ';
		}
		
		if(!empty($patient_phone))
		{
			$search_title .= ' Phone <strong>'.$patient_phone.'</strong>';
			$patient_phone = ' AND patients.patient_phone1 = \''.$patient_phone.'\' ';
		}
		
		//search surname
		if(!empty($_POST['surname']))
		{
			$search_title .= ' first name <strong>'.$_POST['surname'].'</strong>';
			$surnames = explode(" ",$_POST['surname']);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' OR patients.patient_othernames LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\')';
				}
				
				else
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' OR patients.patient_othernames LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\') AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		
		
		$search = $patient_national_id.$patient_number.$surname.$patient_phone;
		$this->session->set_userdata('patient_search', $search);
		$this->session->set_userdata('patient_search_title', $search_title);
		
		redirect('patients');
	}
	public function search_patient_appointments($visits, $page_name = NULL)
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date = $this->input->post('visit_date');
		$patient_number = $this->input->post('patient_number');
		$patient_national_id = $this->input->post('patient_national_id');
		
		if(!empty($patient_national_id))
		{
			$patient_national_id = ' AND patients.patient_national_id = \''.$patient_national_id.'\' ';
		}
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
		}
		
		if(!empty($visit_date))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date.'\' ';
		}
		//search surname
		$surnames = explode(" ",$_POST['surname']);
		$total = count($surnames);
		

		if(!empty($surnames))
		{
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
			//search other_names
		$other_names = explode(" ",$_POST['othernames']);
		$total = count($other_names);
		
		if(!empty($other_names))
		{

			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\'';
				}
				
				else
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';

		}

		$search = $visit_type_id.$patient_number.$surname.$other_name.$visit_date.$patient_national_id.$personnel_id;
		$this->session->set_userdata('appointment_search', $search);
		
		redirect('appointments');
	}


	
	public function search_visits($visits, $page_name = NULL)
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date = $this->input->post('visit_date');
		$patient_number = $this->input->post('patient_number');
		$patient_national_id = $this->input->post('patient_national_id');
		
		if(!empty($patient_national_id))
		{
			$patient_national_id = ' AND patients.patient_national_id = \''.$patient_national_id.'\' ';
		}
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
		}
		
		if(!empty($visit_date))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date.'\' ';
		}
		//search surname
		$surnames = explode(" ",$_POST['surname']);
		$total = count($surnames);
		

		if(!empty($surnames))
		{
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
			//search other_names
		$other_names = explode(" ",$_POST['othernames']);
		$total = count($other_names);
		
		if(!empty($other_names))
		{

			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\'';
				}
				
				else
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';

		}

		$search = $visit_type_id.$patient_number.$surname.$other_name.$visit_date.$patient_national_id.$personnel_id;
		$this->session->set_userdata('visit_search', $search);
		
		if($visits == 13)
		{
			$this->appointment_list();
		}
		
		else
		{
			$this->visit_list($visits, $page_name);
		}
	}
	
	function doc_schedule($personnel_id,$date)
	{

		$data = array('personnel_id'=>$personnel_id,'date'=>$date);
		$this->load->view('show_schedule',$data);	
	}
	function patient_schedule($patient_id,$date)
	{
		$data = array('patient_id'=>$patient_id,'visit_date'=>$date);
		$this->load->view('show_patient_appointment',$data);	
	}
	function load_charges($patient_type){
		
		$v_data['service_charge'] = $this->reception_model->get_service_charges_per_type($patient_type);
		
		$this->load->view('service_charges_pertype',$v_data);	
		
	}
	public function save_initiate_lab($primary_key)
	{
		$this->form_validation->set_rules('patient_type', 'Patient Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_id', 'Insurance Company', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_insurance_id', 'Patient Insurance Number', 'trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == FALSE)
		{
			$this->initiate_lab($primary_key);
		}
		
		else
		{
			$visit_type_id = $this->input->post("patient_type");
			$patient_insurance_number = $this->input->post("insurance_id");
			$patient_insurance_id = $this->input->post("patient_insurance_id");
			$insert = array(
				"close_card" => 0,
				"patient_id" => $primary_key,
				"visit_type" => $visit_type_id,
				"patient_insurance_id" => $patient_insurance_id,
				"patient_insurance_number" => $patient_insurance_number,
				"visit_date" => date("y-m-d"),
				"nurse_visit"=>1,
				"lab_visit" => 12
			);
			$this->database->insert_entry('visit', $insert);
	
			$this->visit_list(0);
		}
	}
	
	public function save_initiate_pharmacy($patient_id)
	{
		$this->form_validation->set_rules('patient_type', 'Patient Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('insurance_id', 'Insurance Company', 'trim|xss_clean');
		$this->form_validation->set_rules('patient_insurance_id', 'Patient Insurance Number', 'trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == FALSE)
		{
			$this->initiate_pharmacy($primary_key);
		}
		
		else
		{
			$visit_type_id = $this->input->post("patient_type");
			$patient_insurance_number = $this->input->post("insurance_id");
			$patient_insurance_id = $this->input->post("patient_insurance_id");
				$insert = array(
					"close_card" => 0,
					"patient_id" => $patient_id,
					"visit_type" => $visit_type_id,
					"patient_insurance_id" => $patient_insurance_id,
					"patient_insurance_number" => $patient_insurance_number,
					"visit_date" => date("y-m-d"),
					"visit_time" => date("Y-m-d H:i:s"),
					"nurse_visit" => 1,
					"pharmarcy" => 6
				);
			$table = "visit";
			$this->database->insert_entry($table, $insert);
	
			$this->visit_list(0);
		}
	}
	
	public function close_visit_search($visit, $page_name = NULL)
	{
		$this->session->unset_userdata('visit_search');
		redirect('appointments');
		// $this->visit_list($visit, $page_name);
	}
	public function close_appointments_search($visit, $page_name = NULL)
	{
		$this->session->unset_userdata('appointment_search');
		
		redirect('appointments');
	}
	public function close_patient_search($page = NULL)
	{
		if($page == NULL)
		{
			$this->session->unset_userdata('patient_search');
			$this->session->unset_userdata('patient_staff_search');
			$this->session->unset_userdata('patient_dependants_search');
			$this->session->unset_userdata('patient_student_search');
			redirect('patients');
		} else if($page == 2)
		{
			$this->session->unset_userdata('patient_staff_search');
			redirect('reception/staff');
		}
		else if($page == 3)
		{
			$this->session->unset_userdata('patient_student_search');
			redirect('reception/students');
		}
		else if($page == 4)
		{
			$this->session->unset_userdata('patient_dependants_search');
			redirect('reception/staff_dependants');
		}
		else
		{
			$this->session->unset_userdata('patient_dependants_search');
			redirect('reception/staff_dependants');
		}
	}
	
	
	public function dependants($patient_id)
	{
		$v_data['dependants_query'] = $this->reception_model->get_all_patient_dependant($patient_id);
		$v_data['patient_query'] = $this->reception_model->get_patient_data($patient_id);
		$v_data['patient_id'] = $patient_id;
		$v_data['relationships'] = $this->reception_model->get_relationship();
		$v_data['religions'] = $this->reception_model->get_religion();
		$v_data['civil_statuses'] = $this->reception_model->get_civil_status();
		$v_data['titles'] = $this->reception_model->get_title();
		$v_data['genders'] = $this->reception_model->get_gender();
		$data['content'] = $this->load->view('dependants', $v_data, true);
		
		$data['title'] = 'Dependants';
		$data['sidebar'] = 'reception_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function register_dependant($patient_id, $visit_type_id, $staff_no)
	{
		//form validation rules
		$this->form_validation->set_rules('title_id', 'Title', 'is_numeric|xss_clean');
		$this->form_validation->set_rules('patient_surname', 'Surname', 'required|xss_clean');
		$this->form_validation->set_rules('patient_othernames', 'Other Names', 'required|xss_clean');
		$this->form_validation->set_rules('patient_dob', 'Date of Birth', 'trim|xss_clean');
		$this->form_validation->set_rules('gender_id', 'Gender', 'trim|xss_clean');
		$this->form_validation->set_rules('religion_id', 'Religion', 'trim|xss_clean');
		$this->form_validation->set_rules('civil_status_id', 'Civil Status', 'trim|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run() == FALSE)
		{
			$this->dependants($patient_id);
		}
		
		else
		{
			//add staff dependant
			if($visit_type_id == 2)
			{
				$patient_id = $this->reception_model->save_dependant_patient($staff_no);
			}
			
			else
			{
				$patient_id = $this->reception_model->save_other_dependant_patient($patient_id);
			}
			
			if($patient_id != FALSE)
			{
				//initiate visit for the patient
				$this->session->set_userdata('success_message', 'Patient added successfully');
				$this->get_found_patients($patient_id,3);
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not create patient. Please try again');
				$this->dependants($patient_id);
			}
		}
	}
	
	public function end_visit($visit_id, $page = NULL)
	{
		//check if card is held
		
			$data = array(
				"close_card" => 1,
				"visit_time_out" => date('Y-m-d H:i:s')
			);
			$table = "visit";
			$key = $visit_id;
			$this->database->update_entry($table, $data, $key);
			
			// //sync data
			// $response = $this->sync_model->syn_up_on_closing_visit($visit_id);
			
			
			// if($response)
			// {
			redirect('queue');
			// }
	}
	public function appointment_list()
	{
		$where = 'visit.visit_delete = 0 AND visit.patient_id = patients.patient_id AND close_card = 2  AND visit.appointment_id = 1 AND visit.visit_date >= "'.date('Y-m-d').'" ';
		
		$table = 'visit, patients';
		$appointment_search = $this->session->userdata('appointment_search');
		// var_dump($appointment_search); die();
		if(!empty($appointment_search))
		{
			$where .= $appointment_search;
		}
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'appointments';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 2;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$data['title'] = 'Appointment List';
		$v_data['title'] = 'Appointment List';
		$v_data['visit'] = 13;
		
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
		
		$page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_appointments($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['page_name'] = 'none';
		
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['rooms'] = $this->rooms_model->all_rooms();
		$v_data['doctors'] = $this->reception_model->get_all_doctors();
		
		$data['content'] = $this->load->view('appointment_list', $v_data, true);
		$data['sidebar'] = 'reception_sidebar';
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	
	public function initiate_visit_appointment($visit_id,$patient_id)
	{


		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$visit_type_id = $this->input->post("visit_type_id"); 

		// var_dump($visit_type_id); die();
		
		
		
		if($visit_type_id != 1)
		{
			$this->form_validation->set_rules('insurance_description', 'Insurance limit', 'required');
			$this->form_validation->set_rules('insurance_number', 'Insurance number', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$data['status'] = 0;
		}
		else
		{
			$insurance_limit = $this->input->post("insurance_limit");
			$insurance_number = $this->input->post("insurance_number");
			$insurance_description = $this->input->post("insurance_description");
			$mcc = $this->input->post("mcc");
			
			$query = $this->reception_model->get_visit_items($visit_id);
			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key => $value) {
					# code...
					$visit_date = $value->visit_date;
				}
			}
			if($visit_date == date('Y-m-d'))
			{
				//update visit
				$visit_id_new = $this->reception_model->initiate_appointment_visit($insurance_description, $insurance_number, $insurance_description,$visit_id,$visit_type_id,$mcc,$patient_id);
				$data['status'] = 1;
				$this->session->set_userdata('success_message', 'You have successfully initiated a visit');

			}
			else{
				$data['status'] = 0;
				$this->session->set_userdata('error_message', 'Please update the visit date before you queue the patient');
			}		
			
		}
		
		
		// redirect('queue');
		echo json_encode($data);
	}
	
	function get_appointments($doctor_id)
	{	
		$this->load->model('reports_model');
		//get all appointments
		$appointments_result = $this->reports_model->get_all_appointments(null,$doctor_id);
		
		//initialize required variables
		$totals = '';
		$highest_bar = 0;
		$r = 0;
		$data = array();
		
		if($appointments_result->num_rows() > 0)
		{
			$result = $appointments_result->result();
			
			foreach($result as $res)
			{
				$visit_date = date('D M d Y',strtotime($res->visit_date)); 
				$time_start = $res->time_start;
				$time_start = date("H:i", strtotime($time_start));

				$time_end = $res->time_end;
				$time_end = date("H:i", strtotime($time_end));

				$time_start = $res->visit_date.'T'.$time_start.':00+03:00'; 
				$time_end = $res->visit_date.'T'.$time_end.':00+03:00';
				$visit_type_name = $res->visit_type_name.' Appointment';
				$patient_id = $res->patient_id;
				$dependant_id = $res->dependant_id;
				$visit_type = $res->visit_type;
				$patient_othernames = $res->patient_othernames;
				$patient_surname = $res->patient_surname;
				$personnel_fname = $res->personnel_fname;
				$personnel_onames = $res->personnel_onames;
				$visit_id = $res->visit_id;
				$strath_no = $res->strath_no;
				$room_id = $res->room_id;
				$room_name = $res->room_name;
				$procedure_done = $res->procedure_done;
				$time_checked = $res->time_start;
				$patient_data = $patient_surname.' '.$patient_othernames;
				$color = $this->reception_model->random_color();


				if($room_id == 1)
				{
					$color = '#DB5C20';
				}
				else if($room_id == 2)
				{
					$color = '#DB5C20';
				}
				else if($room_id == 3)
				{
					$color = '#DB5C20';
				}
				else if($room_id == 4)
				{
					$color = '#DB5C20';
				}
				else
				{
					$color = '#0088CC';
				}

				
				
				$data['title'][$r] = $patient_data;
				$data['start'][$r] = $time_start;
				$data['end'][$r] = $time_end;
				$data['room_name'][$r] = $room_name;
				$data['procedure_done'][$r] = $procedure_done;
				$data['backgroundColor'][$r] = $color;
				$data['borderColor'][$r] = $color;
				$data['allDay'][$r] = FALSE;
				$data['url'][$r] = site_url().'reception/search_appointment/'.$visit_id;
				$r++;
			}
		}
		
		$data['total_events'] = $r;
		echo json_encode($data);
	}
	
	function search_appointment($visit_id)
	{
		if($visit_id > 0)
		{
			$search = ' AND visit.visit_id = '.$visit_id;
			$this->session->set_userdata('appointment_search', $search);
		}
		
		redirect('appointments');
	}
	
	public function delete_patient($patient_id, $page)
	{
		if($this->reception_model->delete_patient($patient_id))
		{
			$this->session->set_userdata('success_message', 'The patient has been deleted successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Could not delete patient. Please <a href="'.site_url().'reception/delete_patient/'.$patient_id.'">try again</a>');
		}
		
		if($page == 1)
		{
			redirect('patients');
		}
		
		if($page == 2)
		{
			redirect('reception/staff');
		}
		
		if($page == 3)
		{
			redirect('reception/staff_dependants');
		}
		
		if($page == 4)
		{
			redirect('reception/students');
		}
	}
	
	public function delete_visit($visit_id)
	{
		if($this->reception_model->delete_visit($visit_id))
		{
			$this->session->set_userdata('success_message', 'The visit has been deleted successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Could not delete visit. Please <a href="'.site_url().'reception/delete_patient/'.$patient_id.'">try again</a>');
		}
		
		redirect('queue');
	}

	public function delete_appontment($visit_id)
	{
		if($this->reception_model->delete_visit($visit_id))
		{
			$this->session->set_userdata('success_message', 'The visit has been deleted successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Could not delete visit. Please try again');
		}
		
		redirect('appointments');
	}
	
	public function change_patient_type($patient_id)
	{
		//form validation rules
		$this->form_validation->set_rules('visit_type_id', 'Visit Type', 'required|is_numeric|xss_clean');
		$this->form_validation->set_rules('strath_no', 'Staff/Student ID No.', 'trim|required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->reception_model->change_patient_type($patient_id))
			{
				$this->session->set_userdata('success_message', 'Patient type updated successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to update patient type. Please try again');
			}
			
			if($this->input->post('visit_type_id') == 1)
			{
				// this is a student
				redirect('reception/students');
			}
			else
			{
				redirect('reception/staff');
			}
			
		}
		
		$v_data['patient'] = $this->reception_model->patient_names2($patient_id);
		$data['content'] = $this->load->view('change_patient_type', $v_data, true);
		$data['sidebar'] = 'reception_sidebar';
		$data['title'] = 'Change Patient Type';
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function change_patient_to_others($patient_id,$visit_type_idd)
	{
		
		if($this->reception_model->change_patient_type_to_others($patient_id,$visit_type_idd))
		{
			$this->session->set_userdata('success_message', 'Patient type updated successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Unable to update patient type. Please try again');
		}
		
		redirect('patients');
	}
	
	/*
	*	Edit other patient
	*
	*/
	public function edit_patient($patient_id)
	{
		//form validation rules
		$this->form_validation->set_rules('title_id', 'Title', 'is_numeric|xss_clean');
		$this->form_validation->set_rules('patient_surname', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('patient_othernames', 'Other Names', 'xss_clean');
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
		$this->form_validation->set_rules('relationship_id', 'Relationship With Kin', 'xss_clean');
		$this->form_validation->set_rules('patient_national_id', 'National ID', 'trim|xss_clean');
		$this->form_validation->set_rules('next_of_kin_contact', 'Next of Kin Contact', 'trim|xss_clean');
		$this->form_validation->set_rules('insurance_company_id', 'Insurance', 'trim|xss_clean');
		
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->reception_model->edit_other_patient($patient_id))
			{
				$this->session->set_userdata("success_message","Patient edited successfully");
				redirect('patients');
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not add patient. Please try again");
			}
		}
		
		$v_data['relationships'] = $this->reception_model->get_relationship();
		$v_data['religions'] = $this->reception_model->get_religion();
		$v_data['civil_statuses'] = $this->reception_model->get_civil_status();
		$v_data['titles'] = $this->reception_model->get_title();
		$v_data['genders'] = $this->reception_model->get_gender();
		$v_data['patient'] = $this->reception_model->get_patient_data($patient_id);
		$v_data['insurance'] = $this->reception_model->get_insurance();
		$data['content'] = $this->load->view('patients/edit_other_patient', $v_data, true);
		
		$data['title'] = 'Edit Patients';
		$data['sidebar'] = 'reception_sidebar';
		$this->load->view('admin/templates/general_page', $data);	
	}
	
	public function search_general_queue($page_name)
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$patient_national_id = $this->input->post('patient_national_id');
		$patient_number = $this->input->post('patient_number');
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($patient_national_id))
		{
			$patient_national_id = ' AND patients.patient_national_id = \''.$patient_national_id.'\' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}
		
		if(!empty($strath_no))
		{
			$strath_no = ' AND patients.strath_no LIKE '.$strath_no.' ';
		}
		
		//search surname
		if(!empty($_POST['surname']))
		{
			$surnames = explode(" ",$_POST['surname']);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		//search other_names
		if(!empty($_POST['othernames']))
		{
			$other_names = explode(" ",$_POST['othernames']);
			$total = count($other_names);
			
			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\'';
				}
				
				else
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';
		}
		
		else
		{
			$other_name = '';
		}
		
		$search = $visit_type_id.$patient_number.$surname.$other_name.$patient_national_id;
		$this->session->set_userdata('general_queue_search', $search);
		
		$this->general_queue($page_name);
	}
	
	public function close_general_queue_search($page_name)
	{
		$this->session->unset_userdata('general_queue_search');
		redirect('queue');
	}
	
	function import_template()
	{
		//export products template in excel 
		 $this->reception_model->import_template();
	}
	
	function import_patients()
	{
		//open the add new product
		$v_data['title'] = 'Import Patients';
		$data['title'] = 'Import Patients';
		$data['content'] = $this->load->view('patients/import_patients', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	
	function do_patients_import()
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->reception_model->import_csv_products($this->csv_path);
				
				if($response == FALSE)
				{
				}
				
				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}
					
					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}
			
			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}
		
		else
		{
			$v_data['import_response_error'] = 'Please select a file to import.';
		}
		
		//open the add new product
		$v_data['title'] = 'Import Patients';
		$data['title'] = 'Import Patients';
		$data['content'] = $this->load->view('patients/import_patients', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function inpatients($page_name)
	{
		$segment = 4;
		
		$where = 'visit.ward_id = ward.ward_id AND visit.inpatient = 1 AND visit.visit_delete = 0 AND visit.patient_id = patients.patient_id AND (visit.close_card = 0 OR visit.close_card = 7) AND visit_type.visit_type_id = visit.visit_type AND visit.branch_code = \''.$this->session->userdata('branch_code').'\'';
		
		$table = 'visit, patients, visit_type, ward';
		
		$visit_search = $this->session->userdata('inpatients_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'reception/inpatients/'.$page_name;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_inpatient_visits($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$data['title'] = $v_data['title'] = 'Inpatients';
		
		$v_data['page_name'] = $page_name;
		$v_data['wards'] = $this->reception_model->get_wards();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('inpatients', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it
	}
	
	public function search_inpatients($page_name)
	{
		$ward_id = $this->input->post('ward_id');
		$visit_type_id = $this->input->post('visit_type_id');
		$patient_national_id = $this->input->post('patient_national_id');
		$patient_number = $this->input->post('patient_number');
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\'';
		}
		
		if(!empty($patient_national_id))
		{
			$patient_national_id = ' AND patients.patient_national_id = \''.$patient_national_id.'\' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}
		
		if(!empty($ward_id))
		{
			$ward_id = ' AND visit.ward_id = '.$ward_id.' ';
		}
		
		//search surname
		if(!empty($_POST['surname']))
		{
			$surnames = explode(" ",$_POST['surname']);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= ' patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		//search other_names
		if(!empty($_POST['othernames']))
		{
			$other_names = explode(" ",$_POST['othernames']);
			$total = count($other_names);
			
			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\'';
				}
				
				else
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.mysql_real_escape_string($other_names[$r]).'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';
		}
		
		else
		{
			$other_name = '';
		}
		
		$search = $visit_type_id.$patient_number.$surname.$other_name.$patient_national_id.$ward_id;
		$this->session->set_userdata('inpatients_search', $search);
		
		$this->inpatients($page_name);
	}
	public function change_items()
	{
		$this->reception_model->changing_to_osh();
	}
	
	public function change_patient_visit($visit_id, $visit_type_id)
	{
		$this->form_validation->set_rules('visit_date', 'Admission Date', 'required');
		$this->form_validation->set_rules('ward_id', 'Ward', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('personnel_id', 'Doctor', 'required|is_natural_no_zero');
		
		$ward_id = $this->input->post("ward_id"); 
		$doctor_id = $this->input->post("personnel_id");
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			redirect('reception/general-queue');
		}
		else
		{
			$visit_date = $this->input->post("visit_date");
			//create visit
			if($this->reception_model->change_patient_visit($visit_date, $doctor_id, $visit_id, $ward_id))
			{
				//save admission fee
				if($this->reception_model->save_admission_fee($visit_type_id, $visit_id))
				{
					
					$this->session->set_userdata('success_message', 'Inpatient visit initiated successfully');
					redirect('reception/inpatients');
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'Unable to save admission fee');
					redirect('reception/general-queue');
				}
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to change to inpatient. Please try again');
				redirect('reception/general-queue');
			}
		}
	}

	public function close_todays_visit()
	{
		$response	= $this->reception_model->close_todays_visits();

		echo $response."<br>";
	}
	
	public function update_visit($visit_id)
	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		$this->form_validation->set_rules('visit_type_id', 'Visit type', 'required|is_natural_no_zero');
		$visit_type_id = $this->input->post("visit_type_id"); 

		// var_dump($_POST); die();
		
		
		
		if($visit_type_id != 1)
		{
			// $this->form_validation->set_rules('insurance_limit'.$visit_id, 'Insurance limit', 'required');
			// $this->form_validation->set_rules('insurance_number'.$visit_id, 'Insurance number', 'required');
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->edit_visit($visit_id);
		}
		else
		{
			$insurance_description = $this->input->post("insurance_description");
			$insurance_number = $this->input->post("insurance_number");
			
			//$visit_type = $this->get_visit_type($type_name);
			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			$doctor_id = $this->input->post("personnel_id");			
			$appointment_id = $this->input->post("appointment_id");


			// var_dump($_POST);die();
			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}
			
			//update visit
			$visit_id = $this->reception_model->update_visit($visit_date, $visit_id, $doctor_id, $insurance_description, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card);
			
			
			
			//set visit department if not appointment
			if($appointment_id == 0)
			{
				//update patient last visit
				$this->reception_model->set_last_visit_date($patient_id, $visit_date);
				$this->session->set_userdata('success_message', 'Visit has been updated');
				
				// $department_id = $this->input->post('department_id');
				// if($this->reception_model->set_visit_department($visit_id, $department_id, $visit_type_id))
				// {
				// 	if($appointment_id == 0)
				// 	{
				// 		$this->session->set_userdata('success_message', 'Visit has been updated');
				// 	}
				// 	else
				// 	{
				// 		$this->session->set_userdata('success_message', 'Appointment has been created');
				// 	}
				// }
				// else
				// {
				// 	$this->session->set_userdata('error_message', 'Internal error. Could not add the visit');
				// }
			}
			
			else
			{
				$this->session->set_userdata('success_message', 'Visit has been updated');
			}
			
			redirect('queue');
		}
	}
	
	public function create_invoice_number($visit_id)
	{
		if($this->reception_model->create_invoice_number($visit_id))
		{
			$this->session->set_userdata('success_message', 'Invoice number created successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'Unable to create invoice number');
		}
		
		redirect('accounts/payments/'.$visit_id);
	}
	public function save_appointment($patient_id,$visit_id)
	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		// $this->form_validation->set_rules('procedure_done', 'Procedure done', 'required');
		$this->form_validation->set_rules('timepicker_start', 'Time Start', 'required');
		// $this->form_validation->set_rules('timepicker_end', 'Time end', 'required');

		$redirect_url = $this->input->post("redirect_url");

		if ($this->form_validation->run() == TRUE)
		{

			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			$room_id = $this->input->post("room_id");

			$check_visits = $this->reception_model->check_patient_appointment_exist($patient_id,$visit_date);
			$check_count = count($check_visits);


			$this->db->where('visit_id',$visit_id);
			$query = $this->db->get('visit');

			$patient_insurance_id = 0;
			$patient_insurance_number = '';
			$patient_type = 0;

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key) {
					# code...
					$patient_insurance_id = $key->patient_insurance_id;
					$patient_insurance_number = $key->patient_insurance_number;
					$patient_type = $key->visit_type;
				}
			}


			$get_tca_rs = $this->nurse_model->get_tca_notes($visit_id);
			$tca_num_rows = count($get_tca_rs);
			$tca_description ='';
			if($tca_num_rows > 0){
				foreach ($get_tca_rs as $key7):
					$tca_description = $key7->tca_description;
				endforeach;
			}
			$doctor_id = $this->session->userdata('personnel_id');

			$appointment_id = 1;

			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}

			if($check_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like there is another appointment scheduled for this patient');

			}
			else
			{
				//create visit
				$visit_id_new = $this->reception_model->create_visit($visit_date, $patient_id, $doctor_id, $patient_insurance_id, $patient_insurance_number, $patient_type, $timepicker_start, $timepicker_end, $appointment_id, $close_card,'',$tca_description,$room_id);
				
				if($visit_id_new > 0)
				{
					$this->session->set_userdata('success_message', 'Appointment Has been scheduled');
					$response['status'] = 'success';
					$response['message'] = 'Appointment Has been scheduled';
				}
				else
				{
					$this->session->set_userdata('error_message', 'Appointment Has not been scheduled. Pleae try again');
					$response['status'] = 'fail';
					$response['message'] = 'Appointment Has not been scheduled. Pleae try again';
				}
				
				
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Appointment Has not been scheduled. Please fill in all the details');
			$response['status'] = 'fail';
			$response['message'] = 'Appointment Has not been scheduled. Please fill in all the details';

		}
		
		// echo json_encode($response);
		redirect($redirect_url);
	}

	public function save_appointment_accounts($patient_id,$visit_id = NULL)
	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		$this->form_validation->set_rules('doctor_id', 'Doctor', 'required');
		$this->form_validation->set_rules('timepicker_start', 'Time start', 'required');
		$this->form_validation->set_rules('timepicker_end', 'Time start', 'required');

		$redirect_url = $this->input->post("redirect_url");

		if ($this->form_validation->run() == TRUE)
		{

			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			$room_id = $this->input->post("room_id");
			$procedure_done = $this->input->post("procedure_done");

			$doctor_id = $this->input->post("doctor_id");
			$check_visits = $this->reception_model->check_patient_appointment_exist($patient_id,$visit_date);
			$check_time = $this->reception_model->check_another_appointment_exist($patient_id,$timepicker_start,$timepicker_end,$visit_date,$doctor_id);

			$check_count = count($check_visits);
			$time_count = count($check_time);

			if($visit_id == NULL)
			{

				$this->db->where('patient_id',$patient_id);
			}
			else
			{

				$this->db->where('visit_id',$visit_id);
			}
			$query = $this->db->get('visit');

			$patient_insurance_id = 0;
			$patient_insurance_number = '';
			$patient_type = 0;

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key) {
					# code...
					$patient_insurance_id = $key->patient_insurance_id;
					$patient_insurance_number = $key->patient_insurance_number;
					$patient_type = $key->visit_type;
				}
			}



			$appointment_id = 1;

			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}

			if($check_count > 0 )
			{
				$this->session->set_userdata('error_message', 'Seems like there is another appointment scheduled');

			}
			else if($time_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like the time picked is already taken');
			}
			else
			{
				//create visit
				$visit_id_new = $this->reception_model->create_visit($visit_date, $patient_id, $doctor_id, $patient_insurance_id, $patient_insurance_number, $patient_type, $timepicker_start, $timepicker_end, $appointment_id, $close_card,'',$procedure_done,$room_id);
				
				if($visit_id_new > 0)
				{
					$this->session->set_userdata('success_message', 'Appointment Has been scheduled');
					$response['status'] = 'success';
					$response['message'] = 'Appointment Has been scheduled';
				}
				else
				{
					$this->session->set_userdata('error_message', 'Appointment Has not been scheduled. Pleae try again');
					$response['status'] = 'fail';
					$response['message'] = 'Appointment Has not been scheduled. Pleae try again';
				}
				
				
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Appointment Has not been scheduled. Please fill in all the details');
			$response['status'] = 'fail';
			$response['message'] = 'Appointment Has not been scheduled. Please fill in all the details';

		}
		
		// echo json_encode($response);
		redirect($redirect_url);
	}

	public function update_appointment_accounts($patient_id,$visit_id)
	{
		$this->form_validation->set_rules('visit_date', 'Visit Date', 'required');
		$this->form_validation->set_rules('doctor_id', 'Doctor', 'required');
		$this->form_validation->set_rules('timepicker_start', 'Time start', 'required');

		$redirect_url = $this->input->post("redirect_url");

		if ($this->form_validation->run() == TRUE)
		{

			$visit_date = $this->input->post("visit_date");
			$timepicker_start = $this->input->post("timepicker_start");
			$timepicker_end = $this->input->post("timepicker_end");
			$room_id = $this->input->post("room_id");
			$doctor_id = $this->input->post("doctor_id");
			$procedure_done = $this->input->post("procedure_done");

			// $check_visits = $this->reception_model->check_patient_appointment_exist($patient_id,$visit_date);
			$check_time = $this->reception_model->check_reschedule_appointment_exist($patient_id,$timepicker_start,$timepicker_end,$visit_date,$doctor_id);

			$check_count = count($check_visits);

			if($visit_id == NULL)
			{

				$this->db->where('patient_id',$patient_id);
			}
			else
			{

				$this->db->where('visit_id',$visit_id);
			}
			$query = $this->db->get('visit');

			$patient_insurance_id = 0;
			$patient_insurance_number = '';
			$patient_type = 0;

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key) {
					# code...
					$patient_insurance_id = $key->patient_insurance_id;
					$patient_insurance_number = $key->patient_insurance_number;
					$patient_type = $key->visit_type;
				}
			}


			$doctor_id = $this->input->post("doctor_id");

			$appointment_id = 1;

			if($appointment_id == 1)
			{
				$close_card = 2;
			}
			else
			{		
				$close_card = 0;
			}

			if($time_count > 0)
			{
				$this->session->set_userdata('error_message', 'Seems like the time picked is already taken');
			}
			else
			{
			
				//create visit
				$visit_id_new = $this->reception_model->update_appointment_accounts($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card,$insurance_description,$visit_id,$procedure_done);
				
				if($visit_id_new > 0)
				{
					$this->session->set_userdata('success_message', 'Appointment Has been updated successfully');
					$response['status'] = 'success';
					$response['message'] = 'Appointment Has been scheduled';
				}
				else
				{
					$this->session->set_userdata('error_message', 'Appointment Has not been updated. Pleae try again');
					$response['status'] = 'fail';
					$response['message'] = 'Appointment Has not been updated. Pleae try again';
				}
			}
				
			
		}
		else
		{
			$this->session->set_userdata('error_message', 'Appointment Has not been scheduled. Please fill in all the details');
			$response['status'] = 'fail';
			$response['message'] = 'Appointment Has not been scheduled. Please fill in all the details';

		}
		
		// echo json_encode($response);
		redirect($redirect_url);
	}
	public function update_patient_current_number()
	{
		$this->db->where('current_patient_number = "" OR current_patient_number = 0 AND patient_number <> 100' );
		$query = $this->db->get('patients');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...

				$patient_number = $key->patient_number;
				$patient_id = $key->patient_id;

				// update patient record

				$array = array('current_patient_number' => $patient_number);
				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients',$array);
			}
		}
	}
	public function patients_queue()
	{

		// $invoice_number = $this->reception_model->create_invoice_number();
		// var_dump($invoice_number); die();


		$segment = 2;

		$personnel_id = $this->session->userdata('personnel_id');
		$department_id = $this->reception_model->get_personnel_department($personnel_id);
		// var_dump($department_id); die();
		if($department_id == 0 OR $department_id == 9 OR $department_id == 4)
		{
			$department_where = '';
		}
		else
		{

			$department_where = '';
		}


		// $where = 'visit.inpatient = 0 AND visit.visit_delete = 0 '.$department_where.' AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_date ="'.date('Y-m-d').'" ';
		$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_date ="'.date('Y-m-d').'" ';
		
		
		$table = 'visit_department, visit, patients, visit_type';
		
		$visit_search = $this->session->userdata('general_queue_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		// var_dump($where);die();
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'queue';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_ongoing_visits($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		

		$v_data['title'] = $data['title'] = 'Patients Queue';
		$page_name = 'reception';
		$v_data['wards'] = $this->reception_model->get_wards();
		$v_data['page_name'] = $page_name;
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['rooms'] = $this->rooms_model->all_rooms();

		
		$v_data['doctors'] = $this->reception_model->get_all_doctors();
		
		$data['content'] = $this->load->view('patient_queue', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		// end of it

	}
	public function send_appointments()
	{
		$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));

		// $amount = $this->reception_model->get_total_unsent_appointments();
		$dt= $date_tomorrow;
        $dt1 = strtotime($dt);
        $dt2 = date("l", $dt1);
        $dt3 = strtolower($dt2);
    	if(($dt3 == "sunday"))
		{
            // echo $dt3.' is weekend'."\n";

            $date_tomorrow = strtotime('+1 day', strtotime($dt));
            $date_tomorrow = date("Y-m-d",$date_tomorrow);
            $date_to_send = 'Monday';
        } 
    	else
		{
            // echo $dt3.' is not weekend'."\n";
             $date_tomorrow = $dt;
             $date_to_send = 'tomorrow';
        }


        // var_dump($amount); die();
		$this->db->select('*');
		$this->db->where('visit.visit_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND schedule_id = 0');
		$query = $this->db->get('visit,patients');
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_phone = $value->patient_phone1;
				$patient_id = $value->patient_id;
				$visit_id = $value->visit_id;
				$patient_othernames = $value->patient_othernames;
				$patient_surname = $value->patient_surname;
				$time_start = $value->time_start;
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				// $time_start = date('H:i A',strtotime($time_start));
				$message = 'Hello '.$patient_surname.', please remember that you have a dental appointment scheduled for '.$date_to_send.' '.$visit_date.' at '.$time_start.'. For more information contact 0707348180.';
				// $patient_phone = 734808007;
				$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
				$this->db->insert('messages', $message_data);
				$message_id = $this->db->insert_id();
				
				$patient_phone = str_replace(' ', '', $patient_phone);
				$response = $this->messaging_model->sms($patient_phone,$message);
				// var_dump($response); die();

				$email_message .= $patient_surname.' '.$patient_othernames.' AT '.$time_start.'<br>';
				$visit_update = array('schedule_id' => 1);
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit', $visit_update);

				if($response == "Success" OR $response == "success")
				{
					


					$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);

				}
				else
				{
					$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);


				}

				
			}
			if($email_message == '')
			{

			}
			else
			{
				// $this->send_email_for_appointment($email_message);
				$date_tomorrow = date('Y-m-d');
				$date_tomorrow = date("Y-m-d", strtotime("+1 day", strtotime($date_tomorrow)));
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$branch = $this->config->item('branch_name');
				$message_result['subject'] = $date_tomorrow.' Appointment report';
				$v_data['persons'] = $email_message;
				$text =  $this->load->view('emails_items',$v_data,true);

				// echo $text; die();
				$message_result['text'] = $text;
				$contacts = $this->site_model->get_contacts();
				$sender_email =$this->config->item('sender_email');//$contacts['email'];
				$shopping = "";
				$from = $sender_email; 
				
				$button = '';
				$sender['email']= $sender_email; 
				$sender['name'] = $contacts['company_name'];
				$receiver['name'] = $message_result['subject'];
				// $payslip = $title;

				$sender_email = $sender_email;
				$tenant_email = $this->config->item('recepients_email').'/'.$sender_email;
				// var_dump($sender_email); die();
				$email_array = explode('/', $tenant_email);
				$total_rows_email = count($email_array);

				for($x = 0; $x < $total_rows_email; $x++)
				{
					$receiver['email'] = $email_tenant = $email_array[$x];

					$this->email_model->send_sendgrid_mail($receiver, $sender, $message_result, NULL);		
					

				}
			}
		}
		// redirect('appointments');
		echo '<script language="JavaScript">';
		echo 'window.self.close();';
		echo '</script>';
	}
	public function send_reminders()
	{
		$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));


		$this->db->select('*');
		$this->db->where('visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND next_appointment_date = "'.$date_tomorrow.'"');
		$this->db->order_by('visit_id','desc');
		$this->db->group_by('visit.patient_id');
		// $this->db->limit(1);
		$query = $this->db->get('visit,patients');
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_phone = $value->patient_phone1;
				$patient_id = $value->patient_id;
				$patient_othernames = $value->patient_othernames;
				$patient_surname = $value->patient_surname;
				$time_start = $value->time_start;
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$time_start = date('H:i A',strtotime($time_start));
				$message = 'Hello '.$patient_othernames.' '.$patient_surname.', This is a kind reminder you are now due for your 6 Months dental checkup. Call us to book an appointment 0722858879 / 0707348180.';

				$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
				$this->db->insert('messages', $message_data);
				$message_id = $this->db->insert_id();
				// $patient_phone = 721481703;
				$email_message .= $patient_surname.' '.$patient_othernames.' PHONE: '.$patient_phone.'<br>';
				$patient_phone = str_replace(' ', '', $patient_phone);
				$response = $this->messaging_model->sms($patient_phone,$message);
				// var_dump($response); die();
				$response = $response->status;
				if($response == "Success" OR $response == "success")
				{
					$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);

				}
				else
				{
					$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);


				}
			}
			if($email_message == '')
			{

			}
			else
			{
				// $this->send_email_for_appointment($email_message);
				$date_tomorrow = date('Y-m-d');
				$date_tomorrow = date("Y-m-d", strtotime("+1 day", strtotime($date_tomorrow)));
				$visit_date = date('jS M Y',strtotime($date_tomorrow));
				$branch = $this->config->item('branch_name');
				$message_result['subject'] = 'Six Month Dental Checkup Reminder';
				$v_data['persons'] = $email_message;
				$text =  $this->load->view('emails_items',$v_data,true);

				// echo $text; die();
				$message_result['text'] = $text;
				$contacts = $this->site_model->get_contacts();
				$sender_email =$this->config->item('sender_email');//$contacts['email'];
				$shopping = "";
				$from = $sender_email; 
				
				$button = '';
				$sender['email']= $sender_email; 
				$sender['name'] = $contacts['company_name'];
				$receiver['name'] = $message_result['subject'];
				// $payslip = $title;

				$sender_email = $sender_email;
				$tenant_email = $this->config->item('recepients_email').'/'.$sender_email;
				// var_dump($sender_email); die();
				$email_array = explode('/', $tenant_email);
				$total_rows_email = count($email_array);

				for($x = 0; $x < $total_rows_email; $x++)
				{
					$receiver['email'] = $email_tenant = $email_array[$x];

					$this->email_model->send_sendgrid_mail($receiver, $sender, $message_result, NULL);		
					

				}
			}
		}
		// redirect('appointments');
		echo '<script language="JavaScript">';
		echo 'window.self.close();';
		echo '</script>';
		// redirect('appointments');
	}

	public function get_next_number()
	{
		$number = $this->reception_model->create_patient_number();

		var_dump($number); die();
	}
	public function update_all_invoices()
	{
		$this->db->where('visit_date > "2018-07-20" AND close_card <> 2 AND visit_date <= "'.date('Y-m-d').'"');

		$query = $this->db->get('visit');
		// var_dump($query->num_rows());die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;

				$update['invoice_number'] = $visit_id;
				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit',$update);
			}
		}
	}
	// public function send_appointments()
	// {
	// 	$date_tomorrow = date("Y-m-d",strtotime("tomorrow"));;
	// 	$this->db->select('*');
	// 	$this->db->where('visit.visit_date = "'.$date_tomorrow.'" AND visit.patient_id = patients.patient_id');
	// 	$query = $this->db->get('visit,patients');
	// 	// var_dump($query); die();
	// 	if($query->num_rows() > 0)
	// 	{
	// 		foreach ($query->result() as $key => $value) {
	// 			# code...
	// 			$patient_phone = $value->patient_phone1;
	// 			$patient_id = $value->patient_id;
	// 			$patient_othernames = $value->patient_othernames;
	// 			$patient_surname = $value->patient_surname;
	// 			$visit_date = date('jS M Y',strtotime($date_tomorrow));
	// 			$message = 'Hello '.$patient_othernames.', please remember that you have an appointment scheduled for tomorrow the '.$visit_date.'. Thank you Alexandria Hospital ';

	// 			$message_data = array(
	// 					"phone_number" => $patient_phone,
	// 					"entryid" => $patient_id,
	// 					"message" => $message,
	// 					"message_batch_id"=>0,
	// 					'message_status' => 0
	// 				);
	// 			$this->db->insert('messages', $message_data);
	// 			$message_id = $this->db->insert_id();
	// 			$patient_phone = 704808007;
	// 			$response = $this->messaging_model->sms($patient_phone,$message);
	// 			var_dump($patient_phone); die();
	// 			if($response == "Success" OR $response == "success")
	// 			{

	// 				$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
	// 				$this->db->where('message_id',$message_id);
	// 				$this->db->update('messages', $service_charge_update);

	// 			}
	// 			else
	// 			{
	// 				$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
	// 				$this->db->where('message_id',$message_id);
	// 				$this->db->update('messages', $service_charge_update);


	// 			}
	// 		}
	// 	}
	// }

	public function update_visit_items()
	{
		$this->db->where('visit_id > 0 AND revisit = 0 AND parent_visit IS NULL');

		$query= $this->db->get('visit');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;
				$patient_id = $value->patient_id;
				$visit_date = $value->visit_date;

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


				$appointment_date = date("Y-m-d", strtotime("+6 months", strtotime($visit_date)));
				$patient_update['next_appointment_date'] = $appointment_date;
				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients',$patient_update);

				

			}
		}
	}

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
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email');
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
				$v_data['visit_id'] = $visit_id;
				$data['results'] = $this->load->view('new_appointment_view', $v_data, TRUE);
				

			}
		}

		return $data;
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
										    'description' => $event_description,
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

	public function get_event_details($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,appointments.created as date_created,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email,personnel.personnel_fname,personnel.personnel_onames');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$this->db->join('personnel','personnel.personnel_id = appointments.created_by','left');
		$query = $this->db->get('appointments');
		$data['status'] = 0;
		$data['result'] = '';
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $res) {
				# code...
				$v_data['appointment_query'] = $query->result();

				$visit_date = date('D M d Y',strtotime($res->appointment_date)); 
				$date_created = date('D M d Y',strtotime($res->date_created)); 
				$appointment_start_time = $res->appointment_start_time; 
				$personnel_fname = $res->personnel_fname; 
				$personnel_onames = $res->personnel_onames; 
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
				$appointment_type = $res->appointment_type;
				$procedure_done = $res->procedure_done;
				$resource_id = $res->resource_id;
				$patient_data = $patient_surname.' '.$patient_othernames;
				$patient_phone1 = $res->patient_phone1;
				$patient_email = $res->patient_email;
				$patient_number = $res->patient_number;
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
				$data['appointment_id'] = $appointment_id;
				$data['appointment_type'] = $appointment_type;
				$v_data['doctors'] = $this->reception_model->get_doctor();


				if($appointment_status == 0)		
				{

					$patients_order = 'patient_id';		    
					$patients_where = 'patient_id > 0';
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
					$v_data['doctors'] = $this->reception_model->get_all_doctors();
					$v_data['visit_type'] = $visit_type;
					$v_data['appointment_id'] = $appointment_id;
					$v_data['visit_id'] = $visit_id;
					$data['results'] = $this->load->view('new_appointment_view', $v_data, TRUE);

				}
				else
				{
					if($appointment_type == 2 )
					{

						$v_data['event_items'] = '
					            		<p><h4><strong>Event Details</strong> </h4></p>
					            		<strong>Title</strong> '.$event_name.' '.$event_description.'<br/>
					            		<strong>Start date</strong> '.$visit_date.' '.$appointment_start_time.'<br/>
					            		<strong>End Date</strong> '.$visit_date.' '.$appointment_end_time.'<br/>
					            		<strong>Status</strong> '.$status_name.'<br/>
					            		<strong>Created By</strong> '.$personnel_fname.' '.$personnel_onames.'<br/>
					            		<strong>Created On</strong> '.$date_created.'<br/>
					            		<div >
							            	
							            	<button type="button" class="btn btn-warning btn-sm" onclick="edit_patient_appointment('.$appointment_id.',2)">Edit Appointment Details </button>
							            	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" onclick="delete_event_details('.$appointment_id.',1)">Delete </button>
							            </div>
					            	';
						$data['results'] = $this->load->view('event_appointment_view', $v_data, TRUE);
						$data['buttons'] = '<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',2)">Confirmed</button>
						            	<button type="button" class="btn btn-warning pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',3)">Cancelled </button>
						            	<button type="button" class="btn btn-primary pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',4)">Showed </button>
						            	<button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',5)">No Showed </button>
						            	<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',6)">Notified </button>
						            	<button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',7)">Not Notified </button>';
					

					}
					else
					{

						$list_order = 'list_name';		    
						$list_where = 'list_id > 0';
						$list_table = 'schedule_list';

						$list_query = $this->reception_model->get_all_visit_type_details($list_table, $list_where,$list_order);

						$rs14 = $list_query->result();
						$list = '';
						foreach ($rs14 as $list_rs) :


						  $list_id = $list_rs->list_id;
						  $list_name = $list_rs->list_name;

						  $list .="<option value='".$list_id."'>".$list_name."</option>";

						endforeach;
						$v_data['list'] = $list;
						$v_data['appointment_id'] = $appointment_id;
						$v_data['visit_id'] = $visit_id;
						$v_data['patient_id'] = $patient_id;

						$data['visit_id'] = $visit_id;
						$data['patient_id'] = $patient_id;
						$v_data['patient_items'] = '<p><h4><strong>'.$visit_date.'</strong> </h4></p>
					            		<strong>Name : </strong> '.$patient_data.'<br/>
					            		<strong>Phone : </strong> '.$patient_phone1.'<br/>
					            		<strong>Email : </strong> '.$patient_email.'<br/>
					            		<strong>Client ID : </strong> '.$patient_number.'<br/><br/>

					            		<p><h4><strong>Appointment Details</strong> </h4></p>
					            		<p><strong>Title</strong> '.$event_name.'  '.$event_description.'</p>
					            		<strong>Start date</strong> '.$visit_date.' '.$appointment_start_time.'<br/>
					            		<strong>End Date</strong> '.$visit_date.' '.$appointment_end_time.'<br/>
					            		<strong>Status</strong> '.$status_name.'<br/>
					            		<strong>Created By</strong> '.$personnel_fname.' '.$personnel_onames.'<br/>
					            		<strong>Created On</strong> '.$date_created.'<br/>
					            		<div >
					            			<button type="button" class="btn btn-warning btn-sm" data-dismiss="modal" onclick="edit_patient_appointment('.$appointment_id.',1)">Edit Appointment Details </button>
					            			<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="resheduled_appointments('.$appointment_id.',1)"> Mark as rescheduled appointment </button>
							            	<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" onclick="delete_event_details('.$appointment_id.',1)">Delete </button>
							            	
							            </div>
					            	';
					$data['buttons'] = '<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',2)">Confirmed</button>
						            	<button type="button" class="btn btn-warning pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',3)">Cancelled </button>
						            	<button type="button" class="btn btn-primary pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',4)">Showed </button>
						            	<button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',5)">No Showed </button>
						            	<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',6)">Notified </button>
						            	<button type="button" class="btn btn-default pull-left" data-dismiss="modal" onclick="update_event_status('.$appointment_id.',7)">Not Notified </button>
						            	<button type="button" class="btn btn-info pull-left" data-dismiss="modal" onclick="send_message_note('.$appointment_id.',7)">Thank You Note </button>';
						$v_data['doctor'] = $this->reception_model->get_doctor();
						$data['results'] = $this->load->view('patient_appointment_view', $v_data, TRUE);

					}

				}
				

			}
		}

		echo json_encode($data);
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
			$this->form_validation->set_rules('other_names'.$appointment_id, 'Other Names', 'required');
			// $this->form_validation->set_rules('first_name'.$appointment_id, 'First Name', '');
			$this->form_validation->set_rules('phone_number'.$appointment_id, 'Phone', 'required');
		}
				
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			// $data['message'] = validation_errors();
			$data['message'] = 'fail';
		}
		else
		{
			
			$appointment_type = $this->input->post('appointment_type');
			if($appointment_type == 1 )
			{

				if($category_id == 1)
				{

					$patient_surname = $this->input->post('surname'.$appointment_id);
					$patient_first_name = $this->input->post('first_name'.$appointment_id);
					$patient_othernames = $this->input->post('other_names'.$appointment_id);
					$patient_phone1 = $this->input->post('phone_number'.$appointment_id);
					$new_patient_number = $this->reception_model->create_new_patient_number();
					$new_patient_array['patient_surname'] = $patient_surname;
					$new_patient_array['patient_othernames'] = $patient_othernames;
					$new_patient_array['patient_first_name'] = $patient_first_name;
					$new_patient_array['patient_phone1'] = $patient_phone = $patient_phone1;
					$new_patient_array['new_patient_number'] = $new_patient_number;
					$new_patient_array['category_id'] = 1;
					$new_patient_array['sync_status'] = 0;
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
								'branch_code' => $this->session->userdata('branch_code'),
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
				$procedure_done = '';// $this->input->post('procedure_done');
			}
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
	public function update_appointment_details($appointment_id,$status)
	{
		$app_update['appointment_status'] = $status;
		$app_update['sync_status'] = 0;
		$this->db->where('appointment_id',$appointment_id);
		$this->db->update('appointments',$app_update);
		$this->reception_model->send_appointments_to_cloud($appointment_id);
		$data['message'] = 'success';
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

	public function reschedule_event_details($appointment_id,$status)
	{
		$app_update['appointment_rescheduled'] = 1;
		$app_update['sync_status'] = 0;
		$this->db->where('appointment_id',$appointment_id);
		$this->db->update('appointments',$app_update);
		$this->reception_model->send_appointments_to_cloud($appointment_id);
		$data['message'] = 'success';
		echo json_encode($data);

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


	public function add_note_old($date_created,$branch_id)
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
			$appointment_array['resource_id'] = $schedule;
			$appointment_array['note'] = $schedule_note;			
			$appointment_array['section_id'] = $type;
			$appointment_array['featured_note'] = $featured;
			$appointment_array['end_date'] = $end_date;
			$appointment_array['created'] = $todays_date;
			$appointment_array['branch_id'] = $branch_id;
			$appointment_array['created_by'] = $this->session->userdata('personnel_id');
			$appointment_array['sync_status'] = 0;
			$this->db->insert('calendar_note',$appointment_array);
			
			$data['message'] = 'success';
		}

		echo json_encode($data);

	}


	public function edit_note($date_created,$branch_id)
	{
		$this->form_validation->set_rules('schedule', 'Schedule', 'required');
		$this->form_validation->set_rules('schedule_note', 'Note', 'required');
		$this->form_validation->set_rules('calendar_note_id', 'Type', 'required');
		$featured = $this->input->post('featured');
		$end_date = $this->input->post('end_date');
		$calendar_note_id = $this->input->post('calendar_note_id');

					
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
			$appointment_array['resource_id'] = $schedule;
			$appointment_array['note'] = $schedule_note;			
			$appointment_array['section_id'] = $type;
			$appointment_array['end_date'] = $end_date;
			$appointment_array['sync_status'] = 0;
			$this->db->where('calendar_note_id',$calendar_note_id);
			$this->db->update('calendar_note',$appointment_array);
			
			$data['message'] = 'success';
		}

		echo json_encode($data);

	}
	public function get_note_detail($note_id)
	{

		$v_data['note_id'] =$note_id;
		$v_data['notes_detail'] = $this->reception_model->get_notes_detail($note_id);
		$v_data['schedule_views'] = $this->reception_model->get_schedule_views();
		$data['content'] = $this->load->view('calendar_note_detail', $v_data, TRUE);
		$data['message'] = 'success';
		echo json_encode($data);

	}
	public function get_edit_appointment_details($appointment_id)
	{

		$this->db->where('appointments.appointment_id',$appointment_id);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email');
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
				$appointment_type = $res->appointment_type;
				$procedure_done = $res->procedure_done;
				$resource_id = $res->resource_id;
				$patient_data = $patient_surname.' '.$patient_othernames;
				$patient_phone1 = $res->patient_phone1;
				$patient_email = $res->patient_email;
				$patient_number = $res->patient_number;
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
				$v_data['doctors'] = $this->reception_model->get_doctor();

					// var_dump($appointment_status); die();

				if($appointment_type == 2 )
				{

					$v_data['event_items'] = '
				            		<p><h4><strong>Event Details</strong> </h4></p>
				            		<strong>Title</strong> '.$event_name.' '.$event_description.'<br/>
				            		<strong>Start date</strong> '.$visit_date.' '.$appointment_start_time.'<br/>
				            		<strong>End Date</strong> '.$visit_date.' '.$appointment_end_time.'<br/>
				            		<strong>Status</strong> '.$status_name.'<br/>
				            		
				            	';
				    $v_data['appointment_id'] = $appointment_id;
					$data['results'] = $this->load->view('edit_event_view', $v_data, TRUE);
					$data['buttons'] = '';

				}
				else
				{

					$v_data['patient_items'] = '<p><h4><strong>'.$visit_date.'</strong> </h4></p>
				            		<strong>Name : </strong> '.$patient_data.'<br/>
				            		<strong>Phone : </strong> '.$patient_phone1.'<br/>
				            		<strong>Email : </strong> '.$patient_email.'<br/>
				            		<strong>Client ID : </strong> '.$patient_number.'<br/><br/>

				            		<p><h4><strong>Appointment Details</strong> </h4></p>
				            		<strong>Title</strong> '.$event_description.'<br/>
				            		<strong>Start date</strong> '.$visit_date.' '.$appointment_start_time.'<br/>
				            		<strong>End Date</strong> '.$visit_date.' '.$appointment_end_time.'<br/>
				            	';
					$v_data['event_description'] = $event_description;
					$v_data['event_name'] = $event_name;
					$v_data['appointment_id'] = $appointment_id;
					$data['results'] = $this->load->view('edit_patient_appointment_details', $v_data, TRUE);

				}

				

			}
		}
		$data['message'] = 'success';
		echo json_encode($data);
	}

	public function send_thank_you_note($appointment_id = NULL)
	{

		$date_tomorrow = date("Y-m-d",strtotime("yesterday"));
		// $date_tomorrow = date("Y-m-d");
    	// $date_tomorrow = strtotime('-1 day', strtotime($dt));

    	if(!empty($appointment_id))
    	{
    		$add = 'appointments.appointment_id = '.$appointment_id;
    	}
    	else
    	{
    		$add = 'appointments.appointment_date = "'.$date_tomorrow.'" AND appointment_status = 4';
    	}


		$this->db->where($add);
		$this->db->select('visit_date, time_start, time_end, appointments.*,appointments.appointment_id AS appointment,patients.patient_surname,patients.patient_othernames,patients.patient_id,patients.patient_number,patients.patient_phone1,patients.patient_email');
		$this->db->join('visit','visit.visit_id = appointments.visit_id','left');
		$this->db->join('patients','patients.patient_id = visit.patient_id','left');
		$query = $this->db->get('appointments');

		// var_dump($query); die();
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
				$appointment_type = $res->appointment_type;
				$procedure_done = $res->procedure_done;
				$resource_id = $res->resource_id;
				$patient_data = $patient_surname.' '.$patient_othernames;
				$patient_phone = $res->patient_phone1;
				$patient_email = $res->patient_email;
				$patient_number = $res->patient_number;
			}
		}

		$message  = "Thank you for choosing Upper Hill Dental Centre for your dental care and treatment. We are happy to receive your feedback with regards to our services at management@upperhilldentalcentre.com.\nHave a pleasant day! We look forward to attending to you once again.";

		$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
		// var_dump($message_data); die();

		$this->db->insert('messages', $message_data);
		$message_id = $this->db->insert_id();
		$patient_phone = '+254734808007';
		$patient_phone = str_replace(' ', '', $patient_phone);
		// var_dump($patient); die();
		if(!empty($patient_phone))
		{
			$response = $this->messaging_model->sms($patient_phone,$message);
			var_dump($response); die();
			if($response == "Success" OR $response == "success")
			{

				$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);

			}
			else
			{
				$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
				$this->db->where('message_id',$message_id);
				$this->db->update('messages', $service_charge_update);


			}
			$response['message'] = "success";
		}
		else
		{
			$response['message'] = "fail";
		}
		
		 echo json_encode($response);
	}
	public function send_recall_list($appointment_id)
	{
		$this->form_validation->set_rules('summary_notes', 'Summary Notes', 'required');
		$this->form_validation->set_rules('period_id', 'Period', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('list_id', 'List', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('doctor_id', 'Doctor', 'required|is_natural_no_zero');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response['message'] = "fail";	
			$response['result'] =  validation_errors();	
		}
		else
		{	
			$recal_list_array['list_id'] = $this->input->post('list_id');
			$recal_list_array['visit_id'] =$visit_id = $this->input->post('visit_id');
			$recal_list_array['patient_id'] = $patient_id = $this->input->post('patient_id');
			$recal_list_array['summary_notes']  = $this->input->post('summary_notes');
			$recal_list_array['duration'] = $duration = $this->input->post('period_id');
			$recal_list_array['created_by'] = $this->session->userdata('personnel_id');
			$recal_list_array['doctor_id'] = $this->input->post('doctor_id');

			$date = date('Y-m-d');
			$recal_list_array['period_date'] = date('Y-m-d', strtotime($date. ' +'.$duration.' days'));
			$recal_list_array['created'] = date('y-m-d');

			$this->db->insert('recall_list',$recal_list_array);
			
			$response['message'] = "success";	
		}

		 echo json_encode($response);
	}

	public function get_recall_list($visit_id,$patient_id)
	{
		$v_data['visit_id'] = $visit_id;
		$v_data['patient_id'] = $patient_id;
		$response['results'] = $this->load->view('patient_recall_list', $v_data, TRUE);
		$response['message'] = 'success';
		 echo json_encode($response);
	}

	public function send_patient_message($appointment_id)
	{

		$this->form_validation->set_rules('message', 'Message', 'required');
		$this->form_validation->set_rules('visit_id', 'Visit', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('patient_id', 'Patient', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('email', 'Email', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$response['message'] = "fail";	
			$response['result'] =  validation_errors();	
		}
		else
		{	

			$recal_list_array['message'] = $message = $this->input->post('message');
			$recal_list_array['visit_id'] =$visit_id = $this->input->post('visit_id');
			$recal_list_array['patient_id'] = $patient_id = $this->input->post('patient_id');
			$recal_list_array['option'] = $option = $this->input->post('option');
			$recal_list_array['created_by'] = $this->session->userdata('personnel_id');
			$recal_list_array['created'] = date('y-m-d');



			// $this->db->insert('patient_messages',$recal_list_array);
			// $message_id = $this->db->insert_id();

			// get patient details
			$patient_data = $this->reception_model->get_patient_data($patient_id);

			if($patient_data->num_rows() > 0)
			{
				foreach ($patient_data->result() as $key => $value) {
					# code...
					$patient_surname = $value->patient_surname;
					$patient_phone = $value->patient_phone1;
					$patient_email = $value->patient_email;
				}
			}
			// send message
			// $patient_phone = +254704808007;
			// $patient_email = 'marttkip@gmail.com';
			if(!empty($patient_phone))
			{

				$message_data = array(
						"phone_number" => $patient_phone,
						"entryid" => $patient_id,
						"message" => $message,
						"message_batch_id"=>0,
						'message_status' => 0
					);
				$message = strip_tags($message);
				$this->db->insert('messages', $message_data);
				$message_id = $this->db->insert_id();
				
				$patient_phone = str_replace(' ', '', $patient_phone);
				$response = $this->messaging_model->sms($patient_phone,$message);
				if($response == "Success" OR $response == "success")
				{
					$service_charge_update = array('message_status' => 1,'delivery_message'=>'Success','sms_cost'=>3);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);
				}
				else
				{
					$service_charge_update = array('message_status' => 0,'delivery_message'=>'Success','sms_cost'=>0);
					$this->db->where('message_id',$message_id);
					$this->db->update('messages', $service_charge_update);
				}

			}
			// send_email

			if(!empty($patient_email))
			{

				$email_message = $message;

				$message_result['subject'] = $this->input->post('subject');
				$v_data['persons'] = $email_message;
				$text =  $this->load->view('emails_items',$v_data,true);

				// echo $text; die();
				$message_result['text'] = $text;
				$contacts = $this->site_model->get_contacts();
				$sender_email = $this->input->post('email');//$this->config->item('sender_email');//$contacts['email'];
				$shopping = "";
				$from = $sender_email; 
				
				$button = '';
				$sender['email']= $sender_email; 
				$sender['name'] = $contacts['company_name'];
				$receiver['name'] = $message_result['subject'];
				// $payslip = $title;

				$sender_email = $sender_email;
				$tenant_email = $this->config->item('recepients_email').'/'.$patient_email;
				// var_dump($sender_email); die();
				$email_array = explode('/', $tenant_email);
				$total_rows_email = count($email_array);

				for($x = 0; $x < $total_rows_email; $x++)
				{
					$receiver['email'] = $email_tenant = $email_array[$x];

					$this->email_model->send_sendgrid_mail($receiver, $sender, $message_result, NULL);	
				}
				
			}
			
			$response['message'] = "success";	
		}

		

		 echo json_encode($response);

	}


	public function recall_list()
	{
		$delete = 0;
		$segment = 2;
		
		$patient_search = $this->session->userdata('recall_patient_search');
		//$where = '(visit_type_id <> 2 OR visit_type_id <> 1) AND patient_delete = '.$delete;
		$where = 'recall_list.patient_id = patients.patient_id AND schedule_list.list_id = recall_list.list_id AND patients.patient_delete = '.$delete;
		if(!empty($patient_search))
		{
			$where .= $patient_search;
		}
		
		else
		{
			$where .='';
		}
		
		$table = 'patients,recall_list,schedule_list';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'uhdc-lists';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
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
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_patients_recall_list($table, $where, $config["per_page"], $page);
		
		
		$search_title = $this->session->userdata('recall_patient_search_title');
		
		if(!empty($search_title))
		{
			$data['title'] = $v_data['title'] = 'Patients filtered by :'.$search_title;
		}		
		else
		{
			$data['title'] = 'Recall Patients';
			$v_data['title'] = 'Recall Patients';
		}

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['delete'] = $delete;

		// $v_data['visit_types'] = $this->reception_model->get_visit_types();
		$v_data['schedules'] = $this->reception_model->get_schedule_list();
		$v_data['branches'] = $this->reception_model->get_branches();
		$data['content'] = $this->load->view('recall_list', $v_data, true);
		
		$data['sidebar'] = 'reception_sidebar';
		
		$this->load->view('admin/templates/general_page', $data);
	}

	function separate_patient_number()
	{	
		$this->db->select('patient_number,patient_id');
		$query = $this->db->get('patients');

		if($query->num_rows() >0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$patient_number = $value->patient_number;
				$patient_id = $value->patient_id;

				$update_array['prefix'] = $prefix = substr($patient_number, 0, 2);
				$update_array['suffix'] = str_replace($prefix, '', $patient_number);
				// var_dump($update_array); die();
				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients',$update_array);


			}
		}
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

	public function view_patient_appointments($patient_id)
	{

		$where = 'patients.patient_id = '.$patient_id.' AND visit.patient_id = patients.patient_id AND visit.visit_delete = 0 AND appointments.appointment_delete = 0 AND appointments.visit_id = visit.visit_id';		
		$table = 'visit,patients,appointments';

		// $appointment_search = $this->session->userdata('appointment_search');
		// // var_dump($appointment_search); die();
		// if(!empty($appointment_search))
		// {
		// 	$where .= $appointment_search;
		// }
		


		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'view-appointments/'.$patient_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 3;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$data['title'] = 'Appointment List';
		$v_data['title'] = 'Appointment List';
		$v_data['visit'] = 13;
		
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
		
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reception_model->get_all_patients_appointments($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['page_name'] = 'none';

		

		$v_data['patient_id'] = $patient_id;
		
		$data['content'] = $this->load->view('patient_appointments', $v_data, true);
		$data['sidebar'] = 'reception_sidebar';
		
		$this->load->view('admin/templates/general_page', $data);

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

			$visit_array['time_end'] = $time;
			$visit_array['time_start'] = $endTime;
			$visit_array['sync_status'] = 0;
			$this->db->where('visit_id',$visit_id);
			$this->db->update('visit',$visit_array);

			$this->reception_model->send_visit_to_cloud($visit_id);
			$data['message'] = 'success';
			$data['appointment_date'] = $visit_date;
		}

		echo json_encode($data);

	}

	public function export_patients($category_id = null)
	{
		$this->reception_model->export_patients($category_id);
	}



	function import_template_list()
	{
		//export products template in excel 
		 $this->reception_model->import_template_list();
	}
	
	function import_patients_list()
	{
		//open the add new product
		$v_data['title'] = 'Import Recall Lists';
		$data['title'] = 'Import Recall Lists';
		$v_data['schedules'] = $this->reception_model->get_schedule_list();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$data['content'] = $this->load->view('patients/import_patients_list', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	
	function do_patients_import_list()
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->reception_model->import_csv_patient_recall_list($this->csv_path);
				
				if($response == FALSE)
				{
				}
				
				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}
					
					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}
			
			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}
		
		else
		{
			$v_data['import_response_error'] = 'Please select a file to import.';
		}
		
		//open the add new product
		$v_data['title'] = 'Import Patients';
		$data['title'] = 'Import Patients';
		$data['content'] = $this->load->view('patients/import_patients', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	public function export_appointments($list_id = null)
	{
		$this->reception_model->export_appointments($list_id);
	}

}
?>