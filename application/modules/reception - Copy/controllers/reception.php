<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require_once "./application/modules/auth/controllers/auth.php";
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
		// if(!$this->auth_model->check_login())
		// {
		// 	redirect('login');
		// }

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
		$this->form_validation->set_rules('patient_surname', 'Surname', 'required|xss_clean');
		$this->form_validation->set_rules('patient_othernames', 'Other Names', 'required|xss_clean');
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
				// var_dump($_POST);die();
				$visit_id = $this->reception_model->create_visit($visit_date, $patient_id, $doctor_id, $insurance_limit, $insurance_number, $visit_type_id, $timepicker_start, $timepicker_end, $appointment_id, $close_card, $insurance_description);
				// $this->sync_model->sync_patient_bookings($visit_id);
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

				$dental_visit = $this->input->post('dental_visit');

				if($dental_visit == 0)
				{
					redirect('queue');
				}
				else
				{
					redirect('preauths');
				}

				
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
		$this->db->where('visit_id > 0 AND invoice_number IS NULL');
		$query = $this->db->get('visit');

		if($query->num_rows() > 0 )
		{
			$number = 0;
			foreach ($query->result() as $key) {
				# code...
				$visit_id = $key->visit_id;
				// $patient_phone1 = $key->patient_phone1;
				// $current_patient_number = $key->current_patient_number;
				// $patient_id = $key->patient_id;
				$year = date('Y');

				$number++;

				// $explode = explode('/', $current_patient_number);

				// $number = $explode[0];
				// $year = $explode[1];

				// $patient_phone1 = str_replace(' ', '', $patient_phone1);

				$update_array = array('invoice_number'=>$visit_id);

				$this->db->where('visit_id',$visit_id);
				$this->db->update('visit',$update_array);

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

	function get_appointments()
	{
		$this->load->model('reports_model');
		//get all appointments
		$appointments_result = $this->reports_model->get_all_appointments();

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
				$time_start = $visit_date.' '.$res->time_start.':00 GMT+0300';
				$time_end = $visit_date.' '.$res->time_end.':00 GMT+0300';
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
				$patient_data = $patient_surname.' '.$patient_othernames.' to see Dr. '.$personnel_onames.'  '.$procedure_done;
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
				$data['end'][$r] = $time_start;
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
			$this->session->set_userdata('visit_search', $search);
		}

		redirect('reception/appointment_list');
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
		$this->form_validation->set_rules('patient_surname', 'Surname', 'required|xss_clean');
		$this->form_validation->set_rules('patient_othernames', 'Other Names', 'required|xss_clean');
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
				//redirect('patients');
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
		// $this->form_validation->set_rules('timepicker_start', 'Time start', 'required');
		// $this->form_validation->set_rules('timepicker_end', 'Time start', 'required');

		$redirect_url = $this->input->post("redirect_url");

		if ($this->form_validation->run() == TRUE)
		{

			$visit_date = $this->input->post("visit_date");
			$timepicker_start = date('H:i:s');//$this->input->post("timepicker_start");
			$timepicker_end = date('H:i:s');//$this->input->post("timepicker_end");
			$room_id = $this->input->post("room_id");
			$procedure_done = $this->input->post("procedure_done");

			$doctor_id = $this->input->post("doctor_id");
			$check_visits = $this->reception_model->check_patient_appointment_exist($patient_id,$visit_date);
			$check_time = $this->reception_model->check_another_appointment_exist($patient_id,$timepicker_start,$timepicker_end,$visit_date,$doctor_id);

			$check_count = 0;//count($check_visits);
			$time_count = 0;//count($check_time);

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
		$where = 'visit.inpatient = 0 AND visit.visit_delete = 0 AND visit_department.visit_id = visit.visit_id AND visit_department.visit_department_status = 1 AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_date ="'.date('Y-m-d').'" AND preauth = 0 ';


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

        // $date_tomorrow = date("Y-m-d");
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
				$message = 'Hello '.$patient_othernames.', please remember that you have an appointment scheduled for '.$date_to_send.' '.$visit_date.' at '.$time_start.'. For more information contact 0704579064, 0735210570.';
				// $patient_phone = 723396322;
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
				$message = 'Hello '.$patient_othernames.' '.$patient_surname.', This is a kind reminder you are now due for your 6 Months dental checkup. Call us to book an appointment 0704579064, 0735210570.';

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

}
?>
