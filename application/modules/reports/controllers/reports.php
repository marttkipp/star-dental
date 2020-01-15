<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require_once "./application/modules/administration/controllers/administration.php";

class Reports extends MX_Controller
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('reception/reception_model');
		$this->load->model('reports/reports_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('reception/database');
		$this->load->model('administration/personnel_model');
	}
	
	public function visit_report()
	{
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 0 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$visit_report_search = $this->session->userdata('visit_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'records/outpatient-report';
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
		$query = $this->reports_model->get_all_visits($table, $where, $config["per_page"], $page, 'ASC');
		
		$page_title = 'Visit Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('visit_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function inpatient_report()
	{
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 1 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$inpatient_report_search = $this->session->userdata('inpatient_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';

		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'records/inpatient-report';
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
		$query = $this->reports_model->get_all_visits($table, $where, $config["per_page"], $page, 'ASC');
		
		$page_title = 'Visit Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('inpatient_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}


	public function sick_off_report()
	{
		$where = 'patient_leave.visit_id = visit.visit_id AND patients.patient_id = visit.patient_id AND patient_leave.leave_type_id = leave_type.leave_type_id ';
		$table = 'patients,patient_leave,visit, leave_type';
		$sick_off_report_search = $this->session->userdata('sick_off_report_search');
		
		if(!empty($sick_off_report_search))
		{
			$where .= $sick_off_report_search;
		}
		else
		{
			$where .= ' AND patient_leave.start_date = "'.date('Y-m-d').'"';
		}
		//echo $where; die();
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'records/sick-off-report';
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
		$query = $this->reports_model->get_all_visits_sick_offs($table, $where, $config["per_page"], $page, 'ASC');
		
		$page_title = 'Sick Off Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$department = $this->reports_model->get_all_departments();
		$departments = '';
		if($department->num_rows() > 0)
		{
			foreach ($department->result() as $department_test_rs):
				//var_dump($department_test_rs); die();
			  $department_name = $department_test_rs->department_name;
	
			  $departments .="<option value='".$department_name."'>".$department_name."</option>";
	
			endforeach;
		}
		
		$this->db->order_by('leave_type_name');
		$leave_types = $this->db->get('leave_type');
		$l_types = '';
		if($leave_types->num_rows() > 0)
		{
			foreach ($leave_types->result() as $rs):
				//var_dump($department_test_rs); die();
			  $leave_type_name = $rs->leave_type_name;
			  $leave_type_id = $rs->leave_type_id;
	
			  $l_types .="<option value='".$leave_type_id."'>".$leave_type_name."</option>";
	
			endforeach;
		}

		$v_data['l_types'] = $l_types;
		$v_data['departments'] = $departments;
		$data['content'] = $this->load->view('sick_off_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_visit_reports()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$visit_search_title ='';
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$visit_search_title = 'Visit From '.$visit_date_from.' To '.$visit_date_to.'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$visit_search_title = 'Visit From '.$visit_date_from.' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$visit_search_title = 'Visit To '.$visit_date_to.'';
		}
		
		else
		{
			$visit_date = '';

		}
		
		$search = $visit_date;
		
		$this->session->set_userdata('visit_report_search', $search);
		$this->session->set_userdata('visit_title_search', $visit_search_title);
		
		redirect('records/outpatient-report');
	}
	public function search_inpatient_reports()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$visit_search_title ='';
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$visit_search_title = 'Visit From '.$visit_date_from.' To '.$visit_date_to.'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$visit_search_title = 'Visit From '.$visit_date_from.' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$visit_search_title = 'Visit To '.$visit_date_to.'';
		}
		
		else
		{
			$visit_date = '';

		}
		
		$search = $visit_date;
		
		$this->session->set_userdata('inpatient_report_search', $search);
		$this->session->set_userdata('inpatient_title_search', $visit_search_title);
		
		redirect('records/inpatient-report');
	}

	public function search_sick_off_reports()
	{
		$payroll_number = $this->input->post('payroll_number');
		$leave_type_id = $this->input->post('leave_type_id');
		$department_name = $this->input->post('department_name');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$visit_search_title = '';
		
		if(!empty($payroll_number))
		{
			$visit_search_title .= ' Payroll number '.$payroll_number;
			$payroll_number = ' AND patients.strath_no = \''.$payroll_number.'\'';
		}
		
		if(!empty($leave_type_id))
		{
			$this->db->where('leave_type_id', $leave_type_id);
			$query = $this->db->get('leave_type');
			$leave_type_name = '';
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$leave_type_name = $row->leave_type_name;
			}
			$visit_search_title .= ' Leave type '.$leave_type_name;
			$leave_type_id = ' AND patient_leave.leave_type_id = \''.$leave_type_id.'\'';
		}
		
		if(!empty($department_name))
		{
			$visit_search_title .= ' Department '.$department_name;
			$department_name = ' AND visit.department_name = \''.$department_name.'\'';
		}
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND patient_leave.start_date >= \''.$visit_date_from.'\' AND patient_leave.start_date <= \''.$visit_date_to.'\'';

			$visit_search_title = 'Start Date From '.$visit_date_from.' To '.$visit_date_to.'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND patient_leave.start_date = \''.$visit_date_from.'\'';
			$visit_search_title = 'Start From '.$visit_date_from.' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND patient_leave.start_date = \''.$visit_date_to.'\'';
			$visit_search_title = 'Start To '.$visit_date_to.'';
		}

		$search = $visit_date.$payroll_number.$department_name.$leave_type_id;

		$this->session->set_userdata('sick_off_report_search', $search);
		$this->session->set_userdata('sick_off_title_search', $visit_search_title);
		
		redirect('records/sick-off-report');
	}

	public function close_visit_search()
	{
		# code...
		$this->session->unset_userdata('visit_report_search');
		$this->session->unset_userdata('visit_title_search');

		redirect('records/outpatient-report');
	}
	public function close_inpatient_search()
	{
		# code...
		$this->session->unset_userdata('inpatient_report_search');
		$this->session->unset_userdata('inpatient_title_search');

		redirect('records/inpatient-report');
	}

	public function close_sick_off_search()
	{
		# code...
		$this->session->unset_userdata('sick_off_report_search');
		$this->session->unset_userdata('sick_off_title_search');

		redirect('records/sick-off-report');
	}

	public function print_visit_report()
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 0 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$visit_report_search = $this->session->userdata('visit_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}

		$query = $this->reports_model->get_all_visits_content($table, $where,'visit.visit_time' ,'ASC');


		$page_title = 'Visit Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;

		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('visit_report_print', $v_data);


	}
	public function print_inpatient_report()
	{

		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.inpatient = 1 AND patients.rip_status =0 AND (visit.close_card = 0 OR visit.close_card = 2)';
		$table = 'visit, patients, visit_type';
		$inpatient_report_search = $this->session->userdata('inpatient_report_search');
		
		if(!empty($inpatient_report_search))
		{
			$where .= $inpatient_report_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$query = $this->reports_model->get_all_visits_content($table, $where,'visit.visit_time' ,'ASC');


		$page_title = 'Inpatient Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;

		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('inpatient_report_print', $v_data);


	}

	public function print_sick_off_report()
	{
		$where = 'patient_leave.visit_id = visit.visit_id AND patients.patient_id = visit.patient_id   ';
		$table = 'patients, patient_leave,visit';
		$sick_off_report_search = $this->session->userdata('sick_off_report_search');
		
		if(!empty($sick_off_report_search))
		{
			$where .= $sick_off_report_search;
		}
		else
		{
			$where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}



		$query = $this->reports_model->get_all_sick_off_content($table, $where,'patient_leave.from_date' ,'ASC');


		$page_title = 'Sick Off Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;

		$v_data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('sick_off_report_print', $v_data);
	}
	
	public function leave_reports($order = 'patient_leave.start_date',$order_method = 'DESC')
	{
		$where = 'visit.patient_id = patients.patient_id AND visit.visit_id = patient_leave.visit_id AND patient_leave.leave_type_id = leave_type.leave_type_id';
		$table = 'visit, patients, patient_leave, leave_type';
		
		$leave_search = $this->session->userdata('leave_report_search');
		if(!empty($leave_search))
		{
			$where .= $leave_search;
		}
		else
		{
			$where .='  AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 5;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'records/leave-reports/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_all_patient_leave($table, $where, $config["per_page"], $page, $order, $order_method);
		
		$page_title = 'Patient Leave Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('patient_leave_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}
	public function search_leave_reports()
	{
		$payroll_number = $this->input->post('payroll_number');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		
		if(!empty($payroll_number))
		{
			$payroll_number = ' AND patients.strath_no = \''.$payroll_number.'\'';
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND patient_leave.start_date >= \''.$visit_date_from.'\' AND patient_leave.end_date <= \''.$visit_date_to.'\'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND patient_leave.start_date >= \''.$visit_date_from.'\'';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND patient_leave.end_date <= \''.$visit_date_to.'\'';
		}

		$search = $visit_date.$payroll_number;

		$this->session->set_userdata('leave_report_search', $search);
		redirect('records/leave-reports');
	}
	public function close_leave_search()
	{
		$this->session->unset_userdata('leave_report_search');
		redirect('records/leave-reports');
	}
	public function patient_statistics()
	{
	}


	public function rip_patients()
	{
		$where = 'rip_status = 1';
		$table = 'patients';
		$visit_report_search = $this->session->userdata('rip_patient_report');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'records/rip-report';
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
		$query = $this->reports_model->get_all_patient_rip($table, $where, $config["per_page"], $page, 'ASC');
		
		$page_title = "Patient's RIP Report"; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('patients_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function export_outpatient_report()
	{
		$this->reports_model->export_outpatient_report();
	}
	public function export_inpatient_report()
	{
		$this->reports_model->export_inpatient_report();
	}

	public function procedures_report()
	{

		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit.visit_id = visit_charge.visit_id';
		$table = 'visit_charge,service_charge,visit';
		$visit_report_search = $this->session->userdata('procedure_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'management-reports/procedures-report';
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
		$query = $this->reports_model->get_all_procedures_visit($table, $where, $config["per_page"], $page, 'ASC');
		
		$page_title = 'Procedures Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('procedures_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function search_procedures_report()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$procedure_name = $this->input->post('procedure_name');
		$visit_search_title ='';
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$visit_search_title = 'Visit From '.$visit_date_from.' To '.$visit_date_to.'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$visit_search_title = 'Visit From '.$visit_date_from.' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$visit_search_title = 'Visit To '.$visit_date_to.'';
		}
		
		else
		{
			$visit_date = '';

		}
		

		if(!empty($procedure_name))
		{
			$procedure_name = ' AND service_charge.service_charge_name LIKE \'%'.$procedure_name.'%\'';
			$visit_search_title .= ' Procedure '.$procedure_name.'';
		}
		
		else
		{
			$procedure_name = '';

		}

		
		$search = $visit_date.$procedure_name;
		
		$this->session->set_userdata('procedure_report_search', $search);
		$this->session->set_userdata('procedure_title_search', $visit_search_title);
		
		redirect('management-reports/procedures-report');
	}

	public function close_procedures_search()
	{
		# code...
		$this->session->unset_userdata('procedure_report_search');
		$this->session->unset_userdata('procedure_title_search');

		redirect('management-reports/procedures-report');
	}

	public function export_procedures_report($service_charge_id = NULL)
	{
		$this->reports_model->export_procedures_report($service_charge_id);
	}
	public function export_visit_procedures_report($service_charge_id = NULL)
	{
		$this->reports_model->export_visit_procedures_report($service_charge_id);
	}

	public function appointments_report()
	{


		$where = 'visit.patient_id = patients.patient_id AND visit.appointment_id = 1 AND visit.visit_delete = 0';
		$table = 'visit,patients';
		$visit_report_search = $this->session->userdata('appointment_report_search');
		
		if(!empty($visit_report_search))
		{
			$where .= $visit_report_search;
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'management-reports/appointments-report';
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
		$query = $this->reports_model->get_all_patients_appointments($table, $where, $config["per_page"], $page, 'ASC');
		// var_dump($query);die();
		$page_title = 'Appointments Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('appointments_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function search_appointment_report_search()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$patient_name = $this->input->post('patient_name');
		$close_card = $this->input->post('close_card');
		$visit_search_title ='';
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$visit_search_title = 'Visit From '.$visit_date_from.' To '.$visit_date_to.'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$visit_search_title = 'Visit From '.$visit_date_from.' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$visit_search_title = 'Visit To '.$visit_date_to.'';
		}
		
		else
		{
			$visit_date = '';

		}
		

		if(!empty($patient_name))
		{
			$patient_name = ' AND patients.patient_surname LIKE \'%'.$patient_name.'%\'';
			$visit_search_title .= ' Patient Name '.$patient_name.'';
		}
		
		else
		{
			$patient_name = '';

		}

		if(!empty($close_card))
		{
			if($close_card == 1)
			{
				$close_card = ' AND visit.close_card <> 2';
				$closed_name = 'Showed';
			}
			else
			{
				$close_card = ' AND visit.close_card = 2';
				$closed_name = 'No Show';
			}
			
			$visit_search_title .= $closed_name;
		}
		
		else
		{
			$close_card = '';

			$visit_search_title .= ' ALL';
		}

		
		$search = $visit_date.$patient_name.$close_card;
		
		$this->session->set_userdata('appointment_report_search', $search);
		$this->session->set_userdata('appointment_report_title', $visit_search_title);
		
		redirect('management-reports/appointments-report');
	}

	public function close_appointments_report_search()
	{
		# code...
		$this->session->unset_userdata('appointment_report_search');
		$this->session->unset_userdata('appointment_report_title');

		redirect('management-reports/appointments-report');
	}

	public function export_appointment_report()
	{
		$this->reports_model->export_appointment_report();
	}
}
?>