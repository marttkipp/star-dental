<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/administration/controllers/administration.php";

class Reports extends administration
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('reception/reception_model');
		$this->load->model('reports_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('pharmacy/pharmacy_model');
		$this->load->model('admin/dashboard_model');
		$this->load->model('dental/dental_model');
	}
	
	public function all_reports($module = '__')
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		
		$this->session->set_userdata('debtors', 'false2');
		$this->session->set_userdata('page_title', 'All Transactions for '.date('Y-m-d'));
		
		$this->all_transactions($module);
	}
	
	public function time_reports()
	{
		$this->session->unset_userdata('time_reports_search');
		$this->session->unset_userdata('time_reports_tables');
		
		$this->session->set_userdata('page_title', 'Time Reports');
		
		$this->all_time_reports();
	}
	
	public function debtors_report($module = '__')
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		$this->session->set_userdata('page_title', 'Debtors Report');
		
		$this->session->set_userdata('debtors', 'true');
		
		$this->all_transactions($module);
	}
	
	public function all_transactions($module = '__')
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 ';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('all_transactions_search');
		$table_search = $this->session->userdata('all_transactions_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			$where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';

		}
		if($module == '__')
		{
			$segment = 4;
		}
		else
		{
			$segment = 5;	
		}
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'administration/reports/all_transactions/'.$module;
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
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
		
		//total outpatients debt
		$where2 = $where.' AND patients.inpatient = 0';
		$total_outpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//outpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 0';
		$outpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//outpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 0';
		$outpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_outpatient_debt'] = ($total_outpatient_debt + $outpatient_debit_notes) - $outpatient_credit_notes;
		
		//total inpatient debt
		$where2 = $where.' AND patients.inpatient = 1';
		$total_inpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//inpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 1';
		$inpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//inpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 1';
		$inpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_inpatient_debt'] = ($total_inpatient_debt + $inpatient_debit_notes) - $inpatient_credit_notes;
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table);
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table);
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//debit notes
		$where2 = $where.' AND payments.payment_type = 2';
		$v_data['debit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//credit notes
		$where2 = $where.' AND payments.payment_type = 3';
		$v_data['credit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All transactions for '.date('Y-m-d');
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/all_transactions', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_transactions($module = '__')
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		/*if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\' ';
			
			$search_title .= 'Patient number. '.$patient_number;
		}*/
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		//date filter for cash report
		$prev_search = '';
		$prev_table = '';
		
		$debtors = $this->session->userdata('debtors');
		
		if($debtors == 'false')
		{
			$prev_search = ' AND payments.visit_id = visit.visit_id AND payments.payment_type = 1';
			$prev_table = ', payments';
			
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_from.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_to.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
		}
		
		else
		{
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id.$prev_search;
		$visit_search = $this->session->userdata('all_transactions_search');
		
		if(!empty($visit_search))
		{
			$search .= $visit_search;
		}
		$this->session->set_userdata('all_transactions_search', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		$this->all_transactions($module);
	}
	
	public function export_transactions()
	{
		$this->reports_model->export_transactions();
	}
	public function export_time_report()
	{
		$this->reports_model->export_time_report();
	}
	
	public function close_search()
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		$this->session->unset_userdata('search_title');
		
		$debtors = $this->session->userdata('debtors');
		
		if($debtors == 'true')
		{
			$this->debtors_report();
		}
		
		else if($debtors == 'false')
		{
			$this->cash_report();
		}
		
		else
		{
			$this->all_reports();
		}
	}
	
	public function department_reports()
	{
		//get all service types
		$v_data['services_result'] = $this->reports_model->get_all_service_types();
		$v_data['type'] = $this->reception_model->get_types();
		
		$data['title'] = 'Department Reports';
		$v_data['title'] = 'Department Reports';
		
		$data['content'] = $this->load->view('reports/department_reports', $v_data, true);
		
		
		$data['sidebar'] = 'admin_sidebar';
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_departments()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_date;
		
		$this->session->set_userdata('all_departments_search', $search);
		
		$this->department_reports();
	}
	
	public function all_time_reports()
	{
		$where = 'visit.patient_id = patients.patient_id AND visit.close_card = 1 AND visit.visit_type = visit_type.visit_type_id';
		$table = 'visit, patients,visit_type';
		$visit_search = $this->session->userdata('time_reports_search');
		$table_search = $this->session->userdata('time_reports_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
		}
		$segment = 4;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/visit-time-report';
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
		$query = $this->reports_model->get_all_visits_time($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		//$v_data['visit_departments'] = $this->reports_model->get_visit_departments($where, $table);
		
		//count student visits
		$where2 = $where.' AND visit.visit_type = 1';
		$v_data['students'] = $this->reception_model->count_items($table, $where2);
		
		//count staff visits
		$where2 = $where.' AND visit.visit_type = 2';
		$v_data['staff'] = $this->reception_model->count_items($table, $where2);
		
		//count other visits
		$where2 = $where.' AND visit.visit_type = 3';
		$v_data['other'] = $this->reception_model->count_items($table, $where2);
		
		//count insurance visits
		$where2 = $where.' AND visit.visit_type = 4';
		$v_data['insurance'] = $this->reception_model->count_items($table, $where2);
		
		$data['title'] = $this->session->userdata('page_title');
		$v_data['title'] = $this->session->userdata('page_title');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('reports/time_reports', $v_data, true);
		
		
		$data['sidebar'] = 'admin_sidebar';
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_time()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$strath_no = $this->input->post('strath_no');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
		}
		
		if(!empty($strath_no))
		{
			$strath_no = ' AND patients.strath_no LIKE \'%'.$strath_no.'%\' ';
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$strath_no.$visit_date.$personnel_id;
		$visit_search = $this->session->userdata('time_reports_search');
		
		if(!empty($visit_search))
		{
			//$search .= $visit_search;
		}
		$this->session->set_userdata('time_reports_search', $search);
		
		$this->all_time_reports();
	}
	
	public function close_time_reports_search()
	{
		$this->session->unset_userdata('time_reports_search');
		$this->session->unset_userdata('time_reports_tables');
		
		$this->all_time_reports();
	}
	public function doctor_reports($date_from = NULL, $date_to = NULL)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		//get all service types
		$v_data['doctor_results'] = $this->reports_model->get_all_doctors();
		
		if(!empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report from '.date('jS M Y',strtotime($date_from)).' to '.date('jS M Y',strtotime($date_to));
			$this->session->set_userdata('doctors_search',TRUE);
		}
		
		else if(empty($date_from) && !empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_to));
			$this->session->set_userdata('doctors_search',TRUE);
		}
		
		else if(!empty($date_from) && empty($date_to))
		{
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
			$this->session->set_userdata('doctors_search',TRUE);
		}
		
		else
		{
			$this->session->set_userdata('doctors_search',FALSE);
			$date_from = date('Y-m-d');
			$title = 'Doctors report for '.date('jS M Y',strtotime($date_from));
		}
		
		$v_data['date_from'] = $date_from;
		$v_data['date_to'] = $date_to;
		
		$v_data['title'] = $title;
		$data['title'] = 'Doctor Reports';
		
		$data['content'] = $this->load->view('reports/doctor_reports', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function search_doctors()
	{
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		
		redirect('administration/reports/doctor_reports/'.$visit_date_from.'/'.$visit_date_to);
	}
	
	public function doctor_reports_export($date_from = NULL, $date_to = NULL)
	{
		$this->reports_model->doctor_reports_export($date_from, $date_to);
	}
	public function doctor_patients_export($personnel_id, $date_from = NULL, $date_to = NULL)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		$this->reports_model->doctor_patients_export($personnel_id, $date_from, $date_to);
	}



	public function doctor_patients_view($personnel_id,$date_from=null,$date_to = null)
	{
		$patients_search  = $this->session->userdata('patients_search');

		// if(!empty($type))
		// {
		// 	$add = ' AND visit.visit_type = 1';
		// }
		// else
		// {
		// 	$add = ' AND visit.visit_type <> 1';
		// }
		// var_dump($week);die();
		// if(!empty($week))
		// {
		// 	$add_week = ' AND WEEK(visit.visit_date) = '.$week;
		// }
		// else
		// {
		// 	$add_week = '';
		// }

		// $where = 'visit.patient_id = patients.patient_id AND visit.personnel_id = '.$personnel_id;
		// $table = 'visit, patients';
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.close_card <> 2 AND visit.personnel_id = '.$personnel_id;
		$table = 'visit, patients, visit_type';

		if(!empty($patients_search))
		{
			$where .= $patients_search;		
		}

		if(!empty($date_from) AND !empty($date_to))
		{
			$where .=' AND visit.visit_date BETWEEN "'.$date_from.'" AND "'.$date_to.'"  ';
			$this->session->set_userdata('doctors_search',TRUE);
			$segment = 5;
			$config['base_url'] = site_url().'view-doctor-patients/'.$personnel_id.'/'.$date_from.'/'.$date_to;
		}
		else
		{
			$segment = 2;
			$this->session->set_userdata('doctors_search',FALSE);
			$config['base_url'] = site_url().'view-doctor-patients';
		}
		
		
		//pagination
		$this->load->library('pagination');
		
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
		$query = $this->reports_model->get_doctors_patients($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['type'] = 1;
		$v_data['personnel_id'] = $personnel_id;
		$v_data['date_from'] = $date_from;
		$v_data['date_to'] = $date_to;
		$v_data['title'] = 'Patient Report';
		$v_data['page'] = $page;
		$v_data['search'] = $patients_search;
		

		$data['content'] = $this->load->view('reports/doctor_patients', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	

	
	public function debtors_report_invoices($visit_type_id, $order = 'debtor_invoice_created', $order_method = 'DESC')
	{
		//get bill to
		$v_data['visit_type_query'] = $this->reports_model->get_visit_type();
		
		//select first debtor from query
		if($visit_type_id == 0)
		{
			if($v_data['visit_type_query']->num_rows() > 0)
			{
				$res = $v_data['visit_type_query']->result();
				$visit_type_id = $res[0]->visit_type_id;
				$visit_type_name = $res[0]->visit_type_name;
			}
		}
		
		else
		{
			if($v_data['visit_type_query']->num_rows() > 0)
			{
				$res = $v_data['visit_type_query']->result();
				
				foreach($res as $r)
				{
					$visit_type_id2 = $r->visit_type_id;
					
					if($visit_type_id == $visit_type_id2)
					{
						$visit_type_name = $r->visit_type_name;
						break;
					}
				}
			}
		}
		
		if($visit_type_id > 0)
		{
			$where = 'debtor_invoice.visit_type_id = '.$visit_type_id;
			$table = 'debtor_invoice';
			
			$visit_search = $this->session->userdata('debtors_invoice_search');
			$table_search = $this->session->userdata('debtors_invoice_tables');
			
			if(!empty($visit_search))
			{
				$where .= $visit_search;
			
				if(!empty($table_search))
				{
					$table .= $table_search;
				}
			}
			
			$segment = 7;
			
			//pagination
			$this->load->library('pagination');
			$config['base_url'] = site_url().'administration/reports/debtors_report_data/'.$visit_type_id.'/'.$order.'/'.$order_method;
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
			$query = $this->reports_model->get_all_debtors_invoices($table, $where, $config["per_page"], $page, $order, $order_method);
			
			$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
			$table .= ', visit, debtor_invoice_item';
			$v_data['where'] = $where;
			$v_data['table'] = $table;
			
			if($order_method == 'DESC')
			{
				$order_method = 'ASC';
			}
			else
			{
				$order_method = 'DESC';
			}
			$v_data['total_patients'] = $this->reports_model->get_total_visits($where, $table);
			$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
			$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
			
			$v_data['order'] = $order;
			$v_data['order_method'] = $order_method;
			$v_data['visit_type_name'] = $visit_type_name;
			$v_data['visit_type_id'] = $visit_type_id;
			$v_data['query'] = $query;
			$v_data['page'] = $page;
			$v_data['search'] = $visit_search;
			
			$data['title'] = $this->session->userdata('page_title');
			$v_data['title'] = $this->session->userdata('page_title');
			$v_data['debtors'] = $this->session->userdata('debtors');
			
			$v_data['services_query'] = $this->reports_model->get_all_active_services();
			$v_data['type'] = $this->reception_model->get_types();
			$v_data['doctors'] = $this->reception_model->get_doctor();
			//$v_data['module'] = $module;
			
			$data['content'] = $this->load->view('reports/debtors_report_invoices', $v_data, true);
		}
		
		else
		{
			$data['title'] = $this->session->userdata('page_title');
			$data['content'] = 'Please add debtors first';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function create_new_batch($visit_type_id)
	{
		$this->form_validation->set_rules('invoice_date_from', 'Invoice date from', 'required|xss_clean');
		$this->form_validation->set_rules('invoice_date_to', 'Invoice date to', 'required|xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			if($this->reports_model->add_debtor_invoice($visit_type_id))
			{
				
			}
			
			else
			{
				
			}
		}
		
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
		}
		//echo 'done '.$visit_type_id;
		redirect('accounts/insurance-invoices/'.$visit_type_id);
	}
	
	public function export_debt_transactions($debtor_invoice_id)
	{
		$this->reports_model->export_debt_transactions($debtor_invoice_id);
	}
	
	public function view_invoices($debtor_invoice_id)
	{
		$where = 'debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id.' AND debtor_invoice.visit_type_id = visit_type.visit_type_id';
		$table = 'debtor_invoice, visit_type';
		
		$v_data = array(
			'debtor_invoice_id'=>$debtor_invoice_id,
			'query' => $this->reports_model->get_debtor_invoice($where, $table),
			'debtor_invoice_items' => $this->reports_model->get_debtor_invoice_items($debtor_invoice_id),
			'personnel_query' => $this->personnel_model->get_all_personnel()
		);
			
		$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
		$table .= ', visit, debtor_invoice_item';
		
		$v_data['where'] = $where;
		$v_data['table'] = $table;
			
		$data['title'] = $v_data['title'] = 'Debtors Invoice';
		
		$data['content'] = $this->load->view('reports/view_invoices', $v_data, TRUE);
		$this->load->view('admin/templates/general_page', $data);
	}

	public function activate_debtor_invoice_item($debtor_invoice_item_id, $debtor_invoice_id)
	{
		$visit_data = array('debtor_invoice_item_status'=>0);
		$this->db->where('debtor_invoice_item_id',$debtor_invoice_item_id);
		if($this->db->update('debtor_invoice_item', $visit_data))
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
		else
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
	}
	
	public function deactivate_debtor_invoice_item($debtor_invoice_item_id, $debtor_invoice_id)
	{
		$visit_data = array('debtor_invoice_item_status'=>1);
		$this->db->where('debtor_invoice_item_id',$debtor_invoice_item_id);
		if($this->db->update('debtor_invoice_item', $visit_data))
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
		else
		{
			redirect('administration/reports/view_invoices/'.$debtor_invoice_id);
		}
	}
	
	public function invoice($debtor_invoice_id)
	{
		$where = 'debtor_invoice.debtor_invoice_id = '.$debtor_invoice_id.' AND debtor_invoice.visit_type_id = visit_type.visit_type_id';
		$table = 'debtor_invoice, visit_type';
		
		$data = array(
			'debtor_invoice_id'=>$debtor_invoice_id,
			'query' => $this->reports_model->get_debtor_invoice($where, $table),
			'debtor_invoice_items' => $this->reports_model->get_debtor_invoice_items($debtor_invoice_id),
			'personnel_query' => $this->personnel_model->get_all_personnel()
		);
			
		$where .= ' AND debtor_invoice.debtor_invoice_id = debtor_invoice_item.debtor_invoice_id AND visit.visit_id = debtor_invoice_item.visit_id ';
		$table .= ', visit, debtor_invoice_item';
		
		$data['where'] = $where;
		$data['table'] = $table;
		$data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('reports/invoice', $data);
	}
	
	public function search_debtors($visit_type_id)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		$this->session->unset_userdata('search_title');
		
		$date_from = $this->input->post('batch_date_from');
		$date_to = $this->input->post('batch_date_to');
		$batch_no = $this->input->post('batch_no');
		
		if(!empty($batch_no) && !empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created >= \''.$date_from.'\' AND debtor_invoice.debtor_invoice_created <= \''.$date_to.'\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created between '.date('jS M Y',strtotime($date_from)).' and '.date('jS M Y',strtotime($date_to));
		}
		
		else if(!empty($batch_no) && !empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_from.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created on '.date('jS M Y',strtotime($date_from));
		}
		
		else if(!empty($batch_no) && empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_to.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no.' created on '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($batch_no) && !empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created >= \''.$date_from.'\' AND debtor_invoice.debtor_invoice_created <= \''.$date_to.'\'';
			$search_title = 'Showing invoices created between '.date('jS M Y',strtotime($date_from)).' and '.date('jS M Y',strtotime($date_to));
		}
		
		else if(empty($batch_no) && !empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_from.'%\'';
			$search_title = 'Showing invoices created created on '.date('jS M Y',strtotime($date_from));
		}
		
		else if(empty($batch_no) && empty($date_from) && !empty($date_to))
		{
			$search = ' AND debtor_invoice.debtor_invoice_created LIKE \''.$date_to.'%\'';
			$search_title = 'Showing invoices created created on '.date('jS M Y',strtotime($date_to));
		}
		else if(!empty($batch_no) && empty($date_from) && empty($date_to))
		{
			$search = ' AND debtor_invoice.batch_no LIKE \'%'.$batch_no.'%\'';
			$search_title = 'Showing invoices for batch no. '.$batch_no;
		}
		
		else
		{
			$search = '';
			$search_title = '';
		}
		
		
		$_SESSION['all_transactions_search'] = $search;
		
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('administration/reports/debtors_report_data/'.$visit_type_id);
	}
	
	public function close_debtors_search($visit_type_id)
	{
		$_SESSION['all_transactions_search'] = NULL;
		$_SESSION['all_transactions_tables'] = NULL;
		
		$this->session->unset_userdata('search_title');
		redirect('administration/reports/debtors_report_data/'.$visit_type_id);
	}
	
	public function cash_report()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id AND payments.payment_type = 1 AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0';
		
		$table = 'payments, visit, patients, visit_type, payment_method';
		$visit_search = $this->session->userdata('cash_report_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		else
		{
			$where .=' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/cash-report';
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
		$query = $this->reports_model->get_all_payments($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table, 'cash');
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table, 'cash');
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table, 'cash');
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table, 'cash');

		$where4 = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id  AND visit.visit_delete = 0  AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 0 AND payments.payment_type = 3 AND visit.visit_date = "'.date('Y-m-d').'"';
		$v_data['total_waiver'] = $this->reports_model->get_total_cash_collection($where4, $table, 'cash');
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('cash_search_title');
		
		if(empty($page_title))
		{
			$page_title = 'Cash report for '.date('Y-m-d');
		}

		$table1 = 'petty_cash,account';
		$where1 = 'petty_cash.account_id = account.account_id AND (account.account_name = "Cash Box" OR account.account_name = "Cash Collection") AND petty_cash.petty_cash_delete = 0';

		$petty_cash_date_search = $this->session->userdata('petty_cash_date_search');
		
		if(!empty($petty_cash_date_search))
		{
			$where1 .= $petty_cash_date_search;
		}
		else
		{
			$where1 .=' AND petty_cash.petty_cash_date = "'.date('Y-m-d').'"';
		}
		$v_data['total_transfers'] = $this->reports_model->get_total_transfers($where1,$table1);

		$table3 = 'payments,visit';
		$where3 = 'payments.cancel = 0  AND payments.payment_method_id = 2 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0';

		$today_cash_date_search = $this->session->userdata('today_cash_date_search');
		
		if(!empty($today_cash_date_search))
		{
			$where3 .= $today_cash_date_search;
		}
		else
		{
			$where3 .=' AND visit.visit_date = "'.date('Y-m-d').'"';
		}
		$v_data['total_cash'] = $this->reports_model->get_total_cash_today($where3,$table3);


		
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('reports/cash_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_cash_reports()
	  {
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}

		$petty_cash_date ='';
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date = \''.$visit_date_from.'\'';
			$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date = \''.$visit_date_to.'\'';
			$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id;
		$this->session->unset_userdata('cash_report_search');
		
		$this->session->set_userdata('cash_report_search', $search);
		$this->session->set_userdata('today_cash_date_search', $visit_date);
		$this->session->set_userdata('petty_cash_date_search', $petty_cash_date);
		$this->session->set_userdata('cash_search_title', $search_title);
		
		redirect('hospital-reports/cash-report');
	}



	public function search_cancelled_reports()
	  {
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('cancelled_date_from');
		$visit_date_to = $this->input->post('cancelled_date_to');
		$branch_code = $this->input->post('branch_code');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}

		$petty_cash_date ='';
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';

			$search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND payments.payment_created = \''.$visit_date_from.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date = \''.$visit_date_from.'\'';
			$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND payments.payment_created = \''.$visit_date_to.'\'';
			$petty_cash_date = ' AND petty_cash.petty_cash_date = \''.$visit_date_to.'\'';
			$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id;
		$this->session->unset_userdata('cancelled_report_search');
		
		// var_dump($search); die();
		$this->session->set_userdata('cancelled_report_search', $search);
		
		redirect('hospital-reports/cancelled-reports');
	}
	
	public function close_cash_search()
	{
		$this->session->unset_userdata('cash_report_search');
		$this->session->unset_userdata('petty_cash_date_search');
		$this->session->unset_userdata('today_cash_date_search');
		$this->session->unset_userdata('cash_search_title');
		
		redirect('hospital-reports/cash-report');
	}
	public function close_cancelled_search()
	{
		$this->session->unset_userdata('cancelled_report_search');
		
		redirect('hospital-reports/cancelled-reports');
	}
	
	public function export_cash_report()
	{
		$this->reports_model->export_cash_report();
	}
	
	
	public function select_debtor()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		
		redirect('accounts/insurance-invoices/'.$visit_type_id);
	}
	public function symptoms($order  ='visit.visit_date',$order_method = 'DESC')
	{
		$table = 'visit_symptoms, visit, symptoms';
		$where = 'visit_symptoms.visit_id = visit.visit_id AND visit_symptoms.symptoms_id = symptoms.symptoms_id AND visit.visit_delete = 0';
		
		$search = $this->session->userdata('all_symptoms_search');
		$search_title = $this->session->userdata('all_symptoms_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_symptoms_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		
		//change order methods
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		$segment = 5;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/symptoms/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_symptoms($table, $where, $config["per_page"], $order, $order_method, $page);
		$v_data['query'] = $query;
		$v_data['order_method'] = $order_method;
		$v_data['page'] = $page;
		
		//symptoms
		$this->db->where('symptoms_name <> \'\'');
		$this->db->order_by('symptoms_name');
		$drug_query = $this->db->get('symptoms');

		$rs15 = $drug_query->result();
		$symptoms = '';
		foreach ($rs15 as $drug_rs) :
			$symptoms_id = $drug_rs->symptoms_id;
			$symptoms_name = $drug_rs->symptoms_name;
			$symptoms .="<option value='".$symptoms_id."'>".$symptoms_name."</option>";
		endforeach;
		$v_data['symptoms'] = $symptoms;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		if(!empty($search_title))
		{
			$data['title'] = $v_data['title'] = $search_title;
		}
		else
		{
			$data['title'] = $v_data['title'] = 'Symptoms Report Summary';
		}
		
		$data['content'] = $this->load->view('reports/all_symptoms', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function objective_findings($order  ='objective_findings_class_name',$order_method = 'ASC')
	{
		$table = 'visit_objective_findings, visit, objective_findings, objective_findings_class';
		$where = 'visit_objective_findings.visit_id = visit.visit_id AND visit_objective_findings.objective_findings_id = objective_findings.objective_findings_id AND visit.visit_delete = 0 AND objective_findings_class.objective_findings_clasS_id = objective_findings.objective_findings_id';
		
		$search = $this->session->userdata('all_objectives_search');
		$search_title = $this->session->userdata('all_objectives_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_objectives_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		
		//change order methods
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		$segment = 5;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/objective-findings/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_objectives($table, $where, $config["per_page"], $order, $order_method, $page);
		$v_data['query'] = $query;
		$v_data['order_method'] = $order_method;
		$v_data['page'] = $page;
		
		//objective_findings
		$this->db->where('objective_findings_name <> \'\'');
		$this->db->order_by('objective_findings_name');
		$drug_query = $this->db->get('objective_findings');

		$rs15 = $drug_query->result();
		$objective_findings = '';
		foreach ($rs15 as $drug_rs) :
			$objective_findings_id = $drug_rs->objective_findings_id;
			$objective_findings_name = $drug_rs->objective_findings_name;
			$objective_findings .="<option value='".$objective_findings_id."'>".$objective_findings_name."</option>";
		endforeach;
		$v_data['objectives'] = $objective_findings;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		if(!empty($search_title))
		{
			$data['title'] = $v_data['title'] = $search_title;
		}
		else
		{
			$data['title'] = $v_data['title'] = 'Objective Findings Report Summary';
		}
		
		$data['content'] = $this->load->view('reports/all_objectives', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function export_objective_findings()
	{
		$query = $this->reports_model->get_all_objectives();
		$v_data['query'] = $query;
		$data['title'] = $v_data['title'] = 'Download Objective Findings Report Summary';
		$this->load->view('reports/download_objectives', $v_data);
	}
	public function export_symptoms()
	{
		$query = $this->reports_model->get_all_symptoms();
		$v_data['query'] = $query;
		$data['title'] = $v_data['title'] = 'Download Symptoms Report Summary';
		$this->load->view('reports/download_symptoms', $v_data);
	}
	public function lab_tests($order  ='visit.visit_date',$order_method = 'DESC')
	{
		$table = 'visit, visit_lab_test, service_charge';
		$where = 'visit_lab_test.service_charge_id = service_charge.service_charge_id AND visit_lab_test.visit_id = visit.visit_id AND visit_lab_test.visit_lab_test_status = 1 AND visit.visit_delete = 0';
		
		$search = $this->session->userdata('all_tests_search');
		$search_title = $this->session->userdata('all_tests_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_tests_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		//change order methods
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		$segment = 5;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/lab-tests/'.$order.'/'.$order_method;
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
		//Lab Tests
		//Drugs
		$this->db->where('lab_test_name <> \'\'');
		$this->db->order_by('lab_test_name');
		$query = $this->db->get('lab_test');
		$rs9 = $query->result();
		$lab = '';
		
		foreach ($rs9 as $rs10) :
			$lab_test_name = $rs10->lab_test_name;
			$lab_test_id = $rs10->lab_test_id;
			$service_charge_amount = $rs10->lab_test_price;

			$lab .="<option value='".$lab_test_id."'>".$lab_test_name."</option>";
		endforeach;
		$v_data['lab'] = $lab;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reports_model->get_tests($table, $where, $config["per_page"], $order, $order_method, $page);
		$v_data['query'] = $query;
		$v_data['order_method'] = $order_method;
		$v_data['page'] = $page;
		if(!empty($search_title))
		{
			$data['title'] = $v_data['title'] = $search_title;
		}
		else
		{
			$data['title'] = $v_data['title'] = 'Lab Test Report Summary';
		}
		
		$data['content'] = $this->load->view('reports/all_lab_tests', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function export_lab_tests()
	{
		$query = $this->reports_model->get_all_lab_tests();
		$v_data['query'] = $query;
		$data['title'] = $v_data['title'] = 'Download Lab Tests Report Summary';
		$this->load->view('reports/download_lab_tests', $v_data);
	}
	
	public function drugs($order  ='visit.visit_date',$order_method = 'DESC')
	{
		$table = 'visit, pres, service_charge,visit_charge';
		$where = 'pres.service_charge_id = service_charge.service_charge_id AND pres.visit_id = visit.visit_id AND visit.visit_delete = 0 AND pres.visit_charge_id = visit_charge.visit_charge_id AND visit_charge.charged = 1';
		$search = $this->session->userdata('all_drugs_search');
		$search_title = $this->session->userdata('all_drugs_search_title');
		
		if(!empty($search))
		{
			$where .= $search;
		}
		
		$date_search = $this->session->userdata('all_drugs_date_search');
		if(empty($date_search))
		{
			$where .= ' AND visit.visit_date = \''.date('Y-m-d').'\'';
		}
		//change order methods
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		$segment = 5;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/drugs/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_drugs($table, $where, $config["per_page"], $order, $order_method, $page);
		$v_data['query'] = $query;
		$v_data['order_method'] = $order_method;
		$v_data['page'] = $page;
		
		//Drugs
		$this->db->where('product_name <> \'\'');
		$this->db->order_by('product_name');
		$drug_query = $this->db->get('product');

		$rs15 = $drug_query->result();
		$drugs = '';
		foreach ($rs15 as $drug_rs) :
			$drug_id = $drug_rs->product_id;
			$drug_name = $drug_rs->product_name;
			$drugs .="<option value='".$drug_id."'>".$drug_name."</option>";
		endforeach;
		$v_data['drugs'] = $drugs;
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		if(!empty($search_title))
		{
			$data['title'] = $v_data['title'] = $search_title;
		}
		else
		{
			$data['title'] = $v_data['title'] = 'Drugs Report Summary';
		}
		$data['content'] = $this->load->view('reports/all_drugs', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function export_drugs()
	{
		$query = $this->reports_model->get_all_drugs_given();
		$v_data['query'] = $query;
		$data['title'] = $v_data['title'] = 'Download Drugs Dispensed Report Summary';
		$this->load->view('reports/download_drugs', $v_data);
	}
	
	public function search_drugs()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$product_id = $this->input->post('product_id');
		
		$this->session->unset_userdata('all_drugs_date_search');
		$search_title = 'Showing reports for: ';
		
		if(!empty($branch_code))
		{
			if($branch_code =='OSE')
			{
				$search_title .= 'Main HC ';
			}
			else
			{
				$search_title .= 'Oserengoni ';
			}
			$branch_code = ' AND visit.branch_code = \''.$branch_code.'\'';
		}
		
		if(!empty($product_id))
		{
			$this->db->where('product_id', $product_id);
			$query = $this->db->get('product');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->product_name.' ';
			}
			$product_id = ' AND service_charge.product_id = '.$product_id.' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$this->session->set_userdata('all_drugs_date_search', 'yes');
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$this->session->set_userdata('all_drugs_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$this->session->set_userdata('all_drugs_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id.$product_id.$branch_code;
		
		$this->session->set_userdata('all_drugs_search', $search);
		$this->session->set_userdata('all_drugs_search_title', $search_title);
		
		redirect('medical-reports/drugs');
	}
	
	public function clear_drugs_search()
	{
		$this->session->unset_userdata('all_drugs_date_search');
		$this->session->unset_userdata('all_drugs_search');
		$this->session->unset_userdata('all_drugs_search_title');
		
		redirect('medical-reports/drugs');
	}
	
	public function search_tests()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$lab_test_id = $this->input->post('lab_test_id');
		
		$this->session->unset_userdata('all_tests_date_search');
		$search_title = 'Showing reports for: ';
		
		if(!empty($branch_code))
		{
			if($branch_code =='OSE')
			{
				$search_title .= 'Main HC ';
			}
			else
			{
				$search_title .= 'Oserengoni ';
			}
			$branch_code = ' AND visit.branch_code = \''.$branch_code.'\'';
		}
		
		if(!empty($lab_test_id))
		{
			$this->db->where('lab_test_id', $lab_test_id);
			$query = $this->db->get('lab_test');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->lab_test_name.' ';
			}
			$lab_test_id = ' AND service_charge.lab_test_id = '.$lab_test_id.' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$this->session->set_userdata('all_tests_date_search', 'yes');
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$this->session->set_userdata('all_tests_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$this->session->set_userdata('all_tests_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id.$lab_test_id.$branch_code;
		
		$this->session->set_userdata('all_tests_search', $search);
		$this->session->set_userdata('all_tests_search_title', $search_title);
		
		redirect('medical-reports/lab-tests');
	}
	
	public function clear_tests_search()
	{
		$this->session->unset_userdata('all_tests_date_search');
		$this->session->unset_userdata('all_tests_search');
		$this->session->unset_userdata('all_tests_search_title');
		
		redirect('medical-reports/lab-tests');
	}
	
	public function search_objectives()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$product_id = $this->input->post('product_id');
		
		$this->session->unset_userdata('all_objectives_date_search');
		$search_title = 'Showing reports for: ';
		
		if(!empty($branch_code))
		{
			if($branch_code =='OSE')
			{
				$search_title .= 'Main HC ';
			}
			else
			{
				$search_title .= 'Oserengoni ';
			}
			$branch_code = ' AND visit.branch_code = \''.$branch_code.'\'';
		}
		
		if(!empty($product_id))
		{
			$this->db->where('product_id', $product_id);
			$query = $this->db->get('product');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->product_name.' ';
			}
			$product_id = ' AND service_charge.product_id = '.$product_id.' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$this->session->set_userdata('all_objectives_date_search', 'yes');
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$this->session->set_userdata('all_objectives_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$this->session->set_userdata('all_objectives_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id.$product_id.$branch_code;
		
		$this->session->set_userdata('all_objectives_search', $search);
		$this->session->set_userdata('all_objectives_search_title', $search_title);
		
		redirect('medical-reports/objective-findings');
	}
	
	public function clear_objectives_search()
	{
		$this->session->unset_userdata('all_objectives_date_search');
		$this->session->unset_userdata('all_objectives_search');
		$this->session->unset_userdata('all_objectives_search_title');
		
		redirect('medical-reports/objective-findings');
	}
	
	public function search_symptoms()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$product_id = $this->input->post('product_id');
		
		$this->session->unset_userdata('all_symptoms_date_search');
		$search_title = 'Showing reports for: ';
		
		if(!empty($branch_code))
		{
			if($branch_code =='OSE')
			{
				$search_title .= 'Main HC ';
			}
			else
			{
				$search_title .= 'Oserengoni ';
			}
			$branch_code = ' AND visit.branch_code = \''.$branch_code.'\'';
		}
		
		if(!empty($product_id))
		{
			$this->db->where('product_id', $product_id);
			$query = $this->db->get('product');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->product_name.' ';
			}
			$product_id = ' AND service_charge.product_id = '.$product_id.' ';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$this->session->set_userdata('all_symptoms_date_search', 'yes');
			$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$this->session->set_userdata('all_symptoms_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$this->session->set_userdata('all_symptoms_date_search', 'yes');
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
		}
		
		$search = $visit_type_id.$visit_date.$personnel_id.$product_id.$branch_code;
		
		$this->session->set_userdata('all_symptoms_search', $search);
		$this->session->set_userdata('all_symptoms_search_title', $search_title);
		
		redirect('medical-reports/symptoms');
	}
	
	public function clear_symptoms_search()
	{
		$this->session->unset_userdata('all_symptoms_date_search');
		$this->session->unset_userdata('all_symptoms_search');
		$this->session->unset_userdata('all_symptoms_search_title');
		
		redirect('medical-reports/symptoms');
	}
	public function malaria($order = 'visit.visit_date', $order_method = 'DESC')
	{
		$malaria_serive_charge_id = "Blood Slide For MPS";
		$where = 'lab_test.lab_test_name = "'.$malaria_serive_charge_id.'" AND service_charge.lab_test_id = lab_test.lab_test_id AND visit_lab_test.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit_lab_test.service_charge_id = service_charge.service_charge_id';
		$table = 'lab_test, visit, patients,service_charge , visit_lab_test';
		
		$malaria_search = $this->session->userdata('malaria');
		if(!empty($malaria_search))
		{
			$where .= $malaria_search;
		}
		
		$segment = 5;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/malaria/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_all_malaria_tests($table, $where, $config["per_page"], $page, $order, $order_method);
		
		$page_title = 'Malaria Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('reports/malaria_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
		
	}
	public function malaria_download()
	{
		$malaria_serive_charge_id = "Blood Slide For MPS";
		$where = 'lab_test.lab_test_name = "'.$malaria_serive_charge_id.'" AND service_charge.lab_test_id = lab_test.lab_test_id AND visit_lab_test.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit_lab_test.service_charge_id = service_charge.service_charge_id';
		$table = 'lab_test, visit, patients,service_charge , visit_lab_test';
		
		$malaria_search = $this->session->userdata('malaria');
		if(!empty($malaria_search))
		{
			$where .= $malaria_search;
		}
		
		$query = $this->reports_model->get_all_malaria_tests_download($table, $where);
		
		$page_title = 'Malaria Report Download'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$this->load->view('reports/malaria_report_download', $v_data);
	}
	public function search_malaria_reports()
	{
		$department_mane = $this->input->post('department_mane');
		$payroll_number = $this->input->post('payroll_number');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$visit_type_id = $this->input->post('visit_type_id');
		$gender_id = $this->input->post('gender_id');
		$age = $this->input->post('age');
		$results = $this->input->post('results');
		
		if(!empty($results))
		{
			if($results == 1)
			{
				$malaria_results = ' AND visit_lab_test.visit_lab_test_results > 0';
			}
			elseif($results == 2)
			{
				$malaria_results = ' AND visit_lab_test.visit_lab_test_results <= 0';
			}
			else
			{
				$malaria_results = ' AND visit_lab_test.visit_lab_test_results > 0 OR visit_lab_test.visit_lab_test_results <= 0';
			}
			$results = $malaria_results;
		}
		
		$year = date('Y');
		if(!empty($age))
		{
			//0 =below 5, 1 = 5-14 years, 2 = 15 and above
			if($age == 0)
			{
				$age = $year1 - 5;
				$add = ' AND YEAR(patients.patient_date_of_birth) > '.$age;
			}
			elseif($age == 1)
			{
				$lower_limit_age = $year - 14;
				$upper_limit_age = $year - 5;
				$add = ' AND YEAR(patients.patient_date_of_birth) >= '.$upper_limit_age.' AND (patients.patient_date_of_birth) < '.$lower_limit_age;
				
			}
			else
			{
				$lower_limit_age = $year - 15;
				$add = ' AND YEAR(patients.patient_date_of_birth) <= '.$lower_limit_age;
			}
			$age = $add;
		}
		
		else
		{
			$age = '';
		}
		
		if(!empty($department_mane))
		{
			$dpt_name = explode(" ",$department_mane);
			$total = count($dpt_name);
			
			$count = 1;
			$department_mane = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$department_mane .= ' visit.department_name LIKE \'%'.mysql_real_escape_string($dpt_name[$r]).'%\'';
				}
				
				else
				{
					$department_mane .= ' visit.department_name LIKE \'%'.mysql_real_escape_string($dpt_name[$r]).'%\' AND ';
				}
				$count++;
			}
			$department_mane .= ') ';
		}
		
		if(!empty($payroll_number))
		{
			$payroll_number = ' AND patients.strath_no = \''.$payroll_number.'\'';
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date >= \''.$visit_date_from.'\' AND visit.visit_date <= \''.$visit_date_to.'\'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date >= \''.$visit_date_from.'\'';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date <= \''.$visit_date_to.'\'';
		}
		
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = \''.$visit_type_id.'\' ';
		}
		if(!empty($gender_id))
		{
			$gender = ' AND patients.gender_id = '.$gender_id;
			if($gender_id == 0)
			{
				$gender = '';
			}
		}
		$search = $visit_date.$payroll_number.$department_mane.$visit_type_id.$gender.$age.$results;

		$this->session->set_userdata('malaria', $search);
		redirect('medical-reports/malaria');
	}
	public function close_malaria_search()
	{
		$this->session->unset_userdata('malaria');
		redirect('medical-reports/malaria');
	}
	public function cholinestrase_report($order = 'visit.visit_date', $order_method = 'DESC')
	{
		$cholinestrase_serive_charge_id = "Cholinesterase";
		$where = 'lab_test.lab_test_name = "'.$cholinestrase_serive_charge_id.'" AND service_charge.lab_test_id = lab_test.lab_test_id AND visit_lab_test.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit_lab_test.service_charge_id = service_charge.service_charge_id AND visit.visit_type = visit_type.visit_type_id';
		$table = 'lab_test, visit, patients,service_charge , visit_lab_test, visit_type';
		
		$cholinestrase_search = $this->session->userdata('cholinestrase');
		if(!empty($cholinestrase_search))
		{
			$where .= $cholinestrase_search;
		}
		
		//get test formats
		$this->db->where("lab_test.lab_test_id = lab_test_format.lab_test_Id AND lab_test.lab_test_name = 'Cholinesterase'");
		$v_data['lab_test_formats'] = $this->db->get('lab_test, lab_test_format');
		
		$segment = 5;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'medical-reports/cholinestrase-report/'.$order.'/'.$order_method;
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
		$query = $this->reports_model->get_all_cholinestrase_tests($table, $where, $config["per_page"], $page, $order, $order_method);
		
		$page_title = 'Cholinestrase Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		
		$data['content'] = $this->load->view('reports/cholinestrase_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function search_cholinestrase_reports()
	{
		$department_mane = $this->input->post('department_mane');
		$payroll_number = $this->input->post('payroll_number');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$visit_type_id = $this->input->post('visit_type_id');
		$gender_id = $this->input->post('gender_id');
		$id_no = $this->input->post('id_no');
		
		if(!empty($department_mane))
		{
			$dpt_name = explode(" ",$department_mane);
			$total = count($dpt_name);
			
			$count = 1;
			$department_mane = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$department_mane .= ' visit.department_name LIKE \'%'.mysql_real_escape_string($dpt_name[$r]).'%\'';
				}
				
				else
				{
					$department_mane .= ' visit.department_name LIKE \'%'.mysql_real_escape_string($dpt_name[$r]).'%\' AND ';
				}
				$count++;
			}
			$department_mane .= ') ';
		}
		if(!empty($payroll_number))
		{
			$payroll_number = ' AND patients.strath_no = \''.$payroll_number.'\'';
		}
		if(!empty($id_no))
		{
			$id_no = ' AND patients.patient_national_id = \''.$id_no.'\'';
		}
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date >= \''.$visit_date_from.'\' AND visit.visit_date <= \''.$visit_date_to.'\'';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit.visit_date >= \''.$visit_date_from.'\'';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit.visit_date <= \''.$visit_date_to.'\'';
		}
		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = \''.$visit_type_id.'\' ';
		}
		if(!empty($gender_id))
		{
			$gender = ' AND patients.gender_id = '.$gender_id;
			if($gender_id == 0)
			{
				$gender = '';
			}
		}
		$search = $visit_date.$payroll_number.$department_mane.$visit_type_id.$gender.$id_no;

		$this->session->set_userdata('cholinestrase', $search);
		redirect('medical-reports/cholinestrase-report');
	}
	public function close_cholinestrase_search()
	{
		$this->session->unset_userdata('cholinestrase');
		redirect('medical-reports/cholinestrase-report');
	}
	public function cholinestrase_report_download()
	{
		$cholinestrase_serive_charge_id = "Cholinesterase";
		$where = 'lab_test.lab_test_name = "'.$cholinestrase_serive_charge_id.'" AND service_charge.lab_test_id = lab_test.lab_test_id AND visit_lab_test.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit_lab_test.service_charge_id = service_charge.service_charge_id AND visit.visit_type = visit_type.visit_type_id';
		$table = 'lab_test, visit, patients,service_charge , visit_lab_test, visit_type';
		
		$cholinestrase_search = $this->session->userdata('cholinestrase');
		if(!empty($cholinestrase_search))
		{
			$where .= $cholinestrase_search;
		}
		
		//get test formats
		$this->db->where("lab_test.lab_test_id = lab_test_format.lab_test_Id AND lab_test.lab_test_name = 'Cholinesterase'");
		$v_data['lab_test_formats'] = $this->db->get('lab_test, lab_test_format');
		
		$query = $this->reports_model->get_all_cholinestrase_tests_download($table, $where);
		$page_title = 'Cholinestrase Report'; 
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['query'] = $query;
		$this->load->view('reports/cholinestrase_report_download', $v_data);
	}
	public function mpesa_reports()
	{
		$where = 'payment_method_id = 5 AND payment_type = 1 AND payments.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id';
		$table = 'payments, visit, patients';
		
		$mpesa_search = $this->session->userdata('mpesa_search');
		if(!empty($mpesa_search))
		{
			$where .= $mpesa_search;
		}
		
		$order = 'payments.payment_created';
		$order_method = "DESC";
		
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/mpesa-reports/'.$order.'/'.$order_method;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 5;
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
		
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->reports_model->get_all_mpesa_payments($table, $where,$order,$order_method,$config['per_page'],$page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		
		$data['title'] = 'MPESA Payment Reports';
		$v_data['title'] = 'MPESA Payments';
		
		$data['content'] = $this->load->view('reports/mpesa_report', $v_data, true);
		
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function search_mpesa()
	{
		$mpesa_search_start_date = $this->input->post('payments_from');
		$mpesa_search_end_date = $this->input->post('payments_to');
		
		if(!empty($mpesa_search_start_date))
		{
			$mpesa_search_start_date = ' AND payments.payment_created  >= "'.$mpesa_search_start_date.'"';
		}
		if(!empty($mpesa_search_end_date))
		{
			$mpesa_search_end_date = ' AND payments.payment_created  <= "'.$mpesa_search_end_date.'"';
		}
		$search = $mpesa_search_start_date.$mpesa_search_end_date;
		$this->session->set_userdata('mpesa_search', $search);
		
		$this->mpesa_reports();
	}
	public function close_mpesa_search()
	{
		$this->session->unset_userdata('mpesa_search');
		
		$this->mpesa_reports();
	}
	public function mpesa_reports_export()
	{
		$this->reports_model->mpesa_reports_export();
	}

	public function providers_report()
	{


		$where = 'personnel.personnel_type_id = personnel_type.personnel_type_id AND personnel_type.personnel_type_name = "Service Provider"';
		$table = 'personnel,personnel_type';
		
		$providers_search = $this->session->userdata('providers_search');
		if(!empty($providers_search))
		{
			$where .= $providers_search;
		}
		
		$order = 'personnel.personnel_id';
		$order_method = "DESC";
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/providers-report';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = $segment;
		
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
		$query = $this->reports_model->get_all_personnel_providers($table, $where,$order,$order_method,$config['per_page'],$page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['doctor'] = $this->reception_model->get_providers();

		$data['title'] = 'Providers Reports';
		$v_data['title'] = 'Providers Report';
		
		$data['content'] = $this->load->view('reports/providers_report', $v_data, true);
		
		
		$this->load->view('admin/templates/general_page', $data);

	}
	public function provider_report_export($provider_id,$report_type)
	{
		$this->reports_model->export_provider_report($provider_id,$report_type);

	}


	public function search_provider()
	{
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');

		$search_title = 'Showing reports for: ';
				
		
		if(!empty($personnel_id))
		{
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}

			$personnel_id = ' AND personnel.personnel_id = '.$personnel_id.' ';
		}
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_date = ' AND visit_charge.date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$providers_date_from = $visit_date_from;
			$providers_date_to = $visit_date_to;
			$search_title .= 'Charges '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_date = ' AND visit_charge.date = \''.$visit_date_from.'\'';
			$providers_date_from = $visit_date_from;
			$search_title .= 'Charges of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_date = ' AND visit_charge.date = \''.$visit_date_to.'\'';
			$providers_date_to = $visit_date_to;
			$search_title .= ' Charges '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_date = '';
			$providers_date_from = '';
			$providers_date_to = '';
		}
		
		$providers_search = $personnel_id;
		$charges_search = $visit_date;

		$this->session->unset_userdata('charges_search');
		$this->session->unset_userdata('providers_search');

		$this->session->unset_userdata('providers_date_from');
		$this->session->unset_userdata('providers_date_to');
		
		$this->session->set_userdata('providers_search', $providers_search);
		$this->session->set_userdata('providers_date_from', $providers_date_from);
		$this->session->set_userdata('providers_date_to', $providers_date_to);
		$this->session->set_userdata('charges_search', $charges_search);
		$this->session->set_userdata('charges_title', $search_title);
		
		redirect('hospital-reports/providers-report');
	}
	public function close_providers_search()
	{
		$this->session->unset_userdata('charges_search');
		$this->session->unset_userdata('providers_search');
		$this->session->unset_userdata('providers_date_from');
		$this->session->unset_userdata('providers_date_to');
		
		
		redirect('hospital-reports/providers-report');
	}

	public function all_invoices()
	{
		$module = '__';
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.close_card <> 2 ';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('all_invoices_search');
		$table_search = $this->session->userdata('all_invoices_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			$where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';

		}
		
		$segment = 3;	
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/invoices';
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
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
		
		//total outpatients debt
		$where2 = $where.' AND patients.inpatient = 0';
		$total_outpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//outpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 0';
		$outpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//outpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 0';
		$outpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_outpatient_debt'] = ($total_outpatient_debt + $outpatient_debit_notes) - $outpatient_credit_notes;
		
		//total inpatient debt
		$where2 = $where.' AND patients.inpatient = 1';
		$total_inpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//inpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 1';
		$inpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//inpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 1';
		$inpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_inpatient_debt'] = ($total_inpatient_debt + $inpatient_debit_notes) - $inpatient_credit_notes;
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table);
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table);
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//debit notes
		$where2 = $where.' AND payments.payment_type = 2';
		$v_data['debit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//credit notes
		$where2 = $where.' AND payments.payment_type = 3';
		$v_data['credit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All invoices for '.date('Y-m-d');
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/all_invoices', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}


	public function doctor_invoices()
	{
		$module = '__';
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;

		$personnel_id = $this->session->userdata('personnel_id');
		$is_dentist = $this->reception_model->check_if_admin($personnel_id,1);
		$is_assitant = $this->reception_model->check_if_admin($personnel_id,6);

		if($is_dentist)
		{
			$add = ' AND visit.personnel_id = '.$personnel_id;
		}
		else
		{
			$add ='';
		}
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.close_card <> 2 '.$add;
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('all_doctor_invoices_search');
		$table_search = $this->session->userdata('all_docto_invoices_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			if($is_dentist)
			{

				$where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';
			}
			else
			{
				$date = date('Y-m-01');
				$a_date = date('Y-m-d');
				$end_date = date("Y-m-d", strtotime($a_date));
				
				$where .= ' AND visit.visit_date BETWEEN "'.$date.'" AND "'.$end_date.'"';
			}

		}
		
		$segment = 3;	
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'patient-invoices';
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
		$query = $this->reports_model->get_all_visits_doctors($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All invoices for '.date('Y-m-d');
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/doctor_invoices', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function all_lab_works()
	{
		$module = '__';
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0 AND visit.visit_id = visit_lab_work.visit_id ';
		$table = 'visit, patients, visit_type,visit_lab_work';
		$visit_search = $this->session->userdata('all_invoices_search');
		$table_search = $this->session->userdata('all_invoices_tables');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			if(!empty($table_search))
			{
				$table .= $table_search;
			}
			
		}
		else
		{
			$where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';

		}
		
		$segment = 3;	
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/invoices';
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
		$query = $this->reports_model->get_all_visits_lab_work($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_services_revenue'] = $this->reports_model->get_total_services_revenue($where, $table);
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table);
		
		//total outpatients debt
		$where2 = $where.' AND patients.inpatient = 0';
		$total_outpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//outpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 0';
		$outpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//outpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 0';
		$outpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_outpatient_debt'] = ($total_outpatient_debt + $outpatient_debit_notes) - $outpatient_credit_notes;
		
		//total inpatient debt
		$where2 = $where.' AND patients.inpatient = 1';
		$total_inpatient_debt = $this->reports_model->get_total_services_revenue($where2, $table);
		//inpatient debit notes
		$where2 = $where.' AND payments.payment_type = 2 AND patients.inpatient = 1';
		$inpatient_debit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		//inpatient credit notes
		$where2 = $where.' AND payments.payment_type = 3 AND patients.inpatient = 1';
		$inpatient_credit_notes = $this->reports_model->get_total_cash_collection($where2, $table);
		$v_data['total_inpatient_debt'] = ($total_inpatient_debt + $inpatient_debit_notes) - $inpatient_credit_notes;
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table);
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table);
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//debit notes
		$where2 = $where.' AND payments.payment_type = 2';
		$v_data['debit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//credit notes
		$where2 = $where.' AND payments.payment_type = 3';
		$v_data['credit_notes'] = $this->reports_model->get_total_cash_collection($where2, $table);
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All invoices for '.date('Y-m-d');
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/all_lab_works', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function search_invoices()
	{

		// var_dump($_POST); die();
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$patient_number = $this->input->post('patient_number');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$invoice_number = $this->input->post('invoice_number');
		$surname = $this->input->post('surname');
		$othernames = $this->input->post('othernames');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		

		if(!empty($invoice_number))
		{
			$search_title .= ' Invoice number <strong>'.$invoice_number.'</strong>';
			$invoice_number = ' AND visit.visit_id ='.$invoice_number.'';
		}

		if(!empty($patient_number))
		{
			$search_title .= ' Invoice number <strong>'.$patient_number.'</strong>';
			$patient_number = ' AND patients.patient_number ='.$patient_number.'';
		}



		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		//date filter for cash report
		$prev_search = '';
		$prev_table = '';
		
		$debtors = $this->session->userdata('debtors');
		
		if($debtors == 'false')
		{
			$prev_search = ' AND payments.visit_id = visit.visit_id AND payments.payment_type = 1';
			$prev_table = ', payments';
			
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_from.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_to.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
		}
		
		else
		{
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
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
					$surname .= ' (patients.patient_surname LIKE \'%'.$surnames[$r].'%\' OR patients.patient_othernames LIKE \'%'.$surnames[$r].'%\')';
				}
				
				else
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.$surnames[$r].'%\' OR patients.patient_othernames LIKE \'%'.$surnames[$r].'%\') AND ';
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
		
		$search = $invoice_number.$visit_type_id.$visit_date.$personnel_id.$patient_number.$surname;
		$this->session->unset_userdata('all_invoices_search');
		
		
		$this->session->set_userdata('all_invoices_search', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('accounts/invoices');
	}

	public function search_doctor_invoices()
	{

		// var_dump($_POST); die();
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$invoice_number = $this->input->post('invoice_number');
		$surname = $this->input->post('surname');
		$othernames = $this->input->post('othernames');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		

		if(!empty($invoice_number))
		{
			$search_title .= ' Invoice number <strong>'.$invoice_number.'</strong>';
			$invoice_number = ' AND visit.visit_id ='.$invoice_number.'';
		}



		if(!empty($visit_type_id))
		{
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		
		if(!empty($personnel_id))
		{
			$personnel_id = ' AND visit.personnel_id = '.$personnel_id.' ';
			
			$this->db->where('personnel_id', $personnel_id);
			$query = $this->db->get('personnel');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->personnel_fname.' '.$row->personnel_onames.' ';
			}
		}
		
		//date filter for cash report
		$prev_search = '';
		$prev_table = '';
		
		$debtors = $this->session->userdata('debtors');
		
		if($debtors == 'false')
		{
			$prev_search = ' AND payments.visit_id = visit.visit_id AND payments.payment_type = 1';
			$prev_table = ', payments';
			
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_from.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND payments.payment_created = \''.$visit_date_to.'\'';
				$search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
		}
		
		else
		{
			if(!empty($visit_date_from) && !empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
				$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else if(!empty($visit_date_from))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_from.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
			}
			
			else if(!empty($visit_date_to))
			{
				$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
				$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
			}
			
			else
			{
				$visit_date = '';
			}
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
					$surname .= ' patients.patient_surname LIKE \'%'.$surnames[$r].'%\'';
				}
				
				else
				{
					$surname .= ' patients.patient_surname LIKE \'%'.$surnames[$r].'%\' AND ';
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
			$search_title .= ' other names <strong>'.$_POST['othernames'].'</strong>';
			$other_names = explode(" ",$_POST['othernames']);
			$total = count($other_names);
			
			$count = 1;
			$other_name = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.$other_names[$r].'%\'';
				}
				
				else
				{
					$other_name .= ' patients.patient_othernames LIKE \'%'.$other_names[$r].'%\' AND ';
				}
				$count++;
			}
			$other_name .= ') ';
		}
		
		else
		{
			$other_name = '';
		}
		
		$search = $invoice_number.$visit_type_id.$visit_date.$personnel_id.$other_name.$surname;
		$this->session->unset_userdata('all_doctor_invoices_search');
		
		
		$this->session->set_userdata('all_doctor_invoices_search', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('patient-invoices');
	}
	public function close_invoice_search()
	{
		$this->session->unset_userdata('all_invoices_search');
		
		redirect('accounts/invoices');
	}
	public function close_doctor_invoice_search()
	{
		$this->session->unset_userdata('all_doctor_invoices_search');
		
		redirect('patient-invoices');
	}

	public function end_visit_current($visit_id, $page = NULL)
	{
		//check if card is held
		
		$data = array(
			"close_card" => 1,
			"visit_time_out" => date('Y-m-d H:i:s')
		);
		$table = "visit";
		$key = $visit_id;
		$this->database->update_entry($table, $data, $key);
		
		redirect('accounts/invoices');
		
	}

	public function open_visit_current($visit_id, $page = NULL)
	{
		//check if card is held
		
		$data = array(
			"close_card" => 0
		);
		$table = "visit";
		$key = $visit_id;
		$this->database->update_entry($table, $data, $key);
		
		redirect('accounts/invoices');
		
	}

	public function receipt_payment($visit_id)
	{
		$this->form_validation->set_rules('amount'.$visit_id, 'Paid Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('payment_method'.$visit_id, 'Payment Method', 'trim|required|xss_clean');
		
		$type_payment = 1;//$this->input->post('type_payment'.$visit_id);
		

		if($type_payment == 1)
		{
			$payment_method  = $this->input->post('payment_method'.$visit_id);
			if(!empty($payment_method))
			{
				if($payment_method == 1)
				{
					// check for cheque number if inserted
					$this->form_validation->set_rules('cheque_number'.$visit_id, 'Cheque Number', 'trim|required|xss_clean');
				}
				else if($payment_method == 6)
				{
					// check for insuarance number if inserted
					$this->form_validation->set_rules('insuarance_number'.$visit_id, 'Credit Card Detail', 'trim|required|xss_clean');
				}
				else if($payment_method == 5)
				{
					//  check for mpesa code if inserted
					$this->form_validation->set_rules('mpesa_code'.$visit_id, 'Amount', 'trim|xss_clean');
				}
				else if($payment_method == 7)
				{
					//  check for mpesa code if inserted
					$this->form_validation->set_rules('deposit_detail'.$visit_id, 'Reference', 'trim|xss_clean');
				}
				else if($payment_method == 8)
				{
					//  check for mpesa code if inserted
					$this->form_validation->set_rules('debit_card_detail'.$visit_id, 'Debit Card', 'trim|required|xss_clean');
				}
			}
		}
		else if($type_payment == 2)
		{
			$this->form_validation->set_rules('waiver_amount', 'Amount', 'trim|required|xss_clean');
			$this->form_validation->set_rules('reason', 'Reason', 'trim|required|xss_clean');
			// var_dump($_POST); die();
			// debit note
			// $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			// $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			// $this->form_validation->set_rules('payment_service_id', 'Service', 'required|xss_clean');
		}
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			// var_dump($_POST); die();
			if($this->reports_model->receipt_payment($visit_id))
			{
				$response['result'] ='success';
				$response['message'] ='You have successfully receipted the payment';
				$this->session->set_userdata('success_message','You have successfully updated the payment');
			}
			else
			{
				$response['result'] ='fail';
				$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
				$this->session->set_userdata('error_message','Sorry something went wrong, please try again');
			}
		}
		else
		{
			$response['result'] ='fail';
			$response['message'] =validation_errors();
			$this->session->set_userdata('error_message',validation_errors());
		}
		redirect('accounts/invoices');
	}


	public function invoice_hospital($visit_id,$type)
	{
		$this->form_validation->set_rules('amount'.$visit_id, 'Amount', 'trim|required|xss_clean');

		if($type == 2)
		{
			$this->form_validation->set_rules('cash_amount'.$visit_id, 'Cash Amount', 'trim|required|xss_clean');
		}
		$payment_method = 1;
		$type_payment = 1;//$this->input->post('type_payment'.$visit_id);
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			// var_dump($_POST); die();
			if($this->reports_model->invoice_hospital($visit_id,$type))
			{
				$response['result'] ='success';
				$response['message'] ='You have successfully receipted the payment';

				$this->session->set_userdata('success_message','You have successfully submitted the charge');
			}
			else
			{
				$response['result'] ='fail';
				$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
				$this->session->set_userdata('error_message','Something went wrong. Please try again.');
			}
		}
		else
		{
			$response['result'] ='fail';
			$response['message'] =validation_errors();
			$this->session->set_userdata('error_message',validation_errors());
		}
		redirect('patient-invoices');
	}

	public function receipt_lab_charge($visit_lab_work_id)
	{
		$this->form_validation->set_rules('amount'.$visit_lab_work_id, 'Charged Amount', 'trim|required|xss_clean');
		$payment_method = 1;
		$type_payment = 1;//$this->input->post('type_payment'.$visit_id);
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			// var_dump($_POST); die();
			$array['amount_to_charge'] = $this->input->post('amount'.$visit_lab_work_id);
			$array['approved_by'] = $this->session->userdata('personnel_id');

			$this->db->where('visit_lab_work_id',$visit_lab_work_id);

			if($this->db->update('visit_lab_work',$array))
			{
				$response['result'] ='success';
				$response['message'] ='You have successfully receipted the payment';
				$this->session->userdata('success_message','You have successfully updated the lab work done charge');
			}
			else
			{
				$response['result'] ='fail';
				$response['message'] ='Seems like you dont have the priviledges to effect this event. Please contact your administrator.';
				$this->session->userdata('error_message','Sorry, please try again');
			}
		}
		else
		{
			$response['result'] ='fail';
			$response['message'] =validation_errors();
			$this->session->userdata('error_message',validation_errors());
		}
		redirect('accounts/lab-works');
	}

	public function cancelled_payment()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'payments.payment_method_id = payment_method.payment_method_id AND payments.visit_id = visit.visit_id AND payments.payment_type = 1 AND visit.visit_delete = 0 AND visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND payments.cancel = 1';
		
		$table = 'payments, visit, patients, visit_type, payment_method';
		$visit_search = $this->session->userdata('cancelled_report_search');
		
		if(!empty($visit_search))
		{
			// var_dump($visit_search); die();
			$where .= $visit_search;
		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/cancelled-reports';
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
		$query = $this->reports_model->get_all_payments($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
		$v_data['total_payments'] = $this->reports_model->get_total_cash_collection($where, $table, 'cash');
		
		//all normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where2, $table, 'cash');
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where2, $table, 'cash');
		
		//normal payments
		$where2 = $where.' AND payments.payment_type = 1';
		$v_data['total_cash_collection'] = $this->reports_model->get_total_cash_collection($where2, $table, 'cash');
		
		//count outpatient visits
		$where2 = $where.' AND patients.inpatient = 0';
		$v_data['outpatients'] = $this->reception_model->count_items($table, $where2);
		
		//count inpatient visits
		$where2 = $where.' AND patients.inpatient = 1';
		$v_data['inpatients'] = $this->reception_model->count_items($table, $where2);
		
		$page_title = $this->session->userdata('cancelled_report_search');
		
		if(empty($page_title))
		{
			$page_title = 'Cancelled report';
		}
		
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['services_query'] = $this->reports_model->get_all_active_services();
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		
		$data['content'] = $this->load->view('reports/cancelled_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function cancelled_invoices()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('branch_code', $branch_code);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$branch_name = $row->branch_name;
		}
		
		else
		{
			$branch_name = '';
		}
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit.visit_type = visit_type.visit_type_id AND visit.rejected_status = 1';
		
		$table = 'visit,patients,visit_type';
		$visit_search = $this->session->userdata('cash_report_search');
		
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/cancelled-receipts';
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
		$query = $this->reports_model->get_all_data_content($table, $where, $config["per_page"], $page,'visit.visit_date' ,'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];
			
		$page_title = $this->session->userdata('cash_search_title');
		
		if(empty($page_title))
		{
			$page_title = 'Cancelled invoices';
		}
		
		$data['title'] = $v_data['title'] = $page_title;
	
		
		$data['content'] = $this->load->view('reports/cancelled_invoices', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}	
	public function approve_payment($visit_id)
	{
		$data['doctor_invoice_status'] = 1;
		$data['approved_by'] = $this->session->userdata('personnel_id');
		$this->db->where('visit_id',$visit_id);
		if($this->db->update('doctor_invoice',$data))
		{
			$this->session->set_userdata('success_message','You have successfully approved this payment detail');
		}
		else
		{
			$this->session->set_userdata('error_message','Sorry Something went wrong');
		}

		redirect('patient-invoices');
	}

	public function close_doctors_search()
	{
		$this->session->unset_userdata('all_transactions_search');
		$this->session->unset_userdata('all_transactions_tables');
		$this->session->unset_userdata('search_title');
		
		$debtors = $this->session->userdata('debtors');
		
		redirect('hospital-reports/doctors-report');
	}
}
?>