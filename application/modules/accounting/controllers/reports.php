<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounting/controllers/company_financial.php";
// error_reporting();
class Reports extends company_financial
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('accounting_model');
		$this->load->model('administration/reports_model');
	}
	

	public function debtors()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		// $where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL)';
		// $table = 'visit, patients, visit_type';

		$where = 'v_transactions_by_date.transactionCategory = "Revenue" AND visit_invoice.visit_invoice_id = v_transactions_by_date.transaction_id';
		
		$table = 'v_transactions_by_date,visit_invoice';


		$visit_search = $this->session->userdata('debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= ' AND v_transactions_by_date.transaction_date = "'.date('Y-m-d').'" ';
		
			$visit_payments = ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
			$search_title = 'Visit date of '.date('jS M Y', strtotime(date('Y-m-d'))).' ';

			$this->session->set_userdata('visit_invoices', $visit_invoices);
			// $this->session->set_userdata('debtors_search_query', $visit_invoices);
			$this->session->set_userdata('visit_payments', $visit_payments);
		


			$where .= '';

		}

		$branch_session = $this->session->userdata('branch_id');

		if($branch_session > 0)
		{
			$where .= ' AND v_transactions_by_date.branch_id = '.$branch_session;
			// $where .= $visit_search;
		
		}
	



		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/invoices-report';
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
		$query = $this->accounting_model->get_all_visits_old($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All transactions for '.date('Y-m-d');
		}
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where, $table, 'cash');
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where, $table, 'cash');
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['total_visits'] = $config['total_rows'];
		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/debtors', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_debtors_report()
	{
		$visit_type_id = $visit_type_idd = $this->input->post('visit_type_id');
		$branch_id = $branch_idd= $this->input->post('branch_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		// $branch_code = $this->input->post('branch_code');
		// $patient_number = $this->input->post('patient_number');
		// $patient_name = $this->input->post('patient_name');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($visit_type_id))
		{
			$visit_type = $visit_type_id;
			$visit_type_id = ' AND v_transactions_by_date.payment_type = '.$visit_type_id.' ';


			
			$this->db->where('visit_type_id', $visit_type_idd);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		// if(!empty($patient_number))
		// {
		// 	$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\' ';
			
		// 	$search_title .= 'Patient number. '.$patient_number;
		// }
		
		if(!empty($branch_id))
		{
			$branch_id = ' AND v_transactions_by_date.branch_id = '.$branch_id.' ';
			
			$this->db->where('branch_id', $branch_idd);
			$query = $this->db->get('branch');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->branch_name.' ';
			}
		}
		
		//date filter for cash report
		$prev_search = '';
		$prev_table = '';
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_from.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_to.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_payments = '';
			$visit_invoices = '';
		}

		$surname = '';

		
		
		$search = $visit_type_id.$visit_invoices.$branch_id;
		
		$this->session->set_userdata('debtors_search_query', $search);
		$this->session->set_userdata('visit_invoices', $visit_invoices);
		$this->session->set_userdata('visit_payments', $visit_payments);
		$this->session->set_userdata('visit_type_id', $visit_type_id);
		$this->session->set_userdata('visit_type', $visit_type);
		$this->session->set_userdata('patient_number', $patient_number);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('hospital-reports/invoices-report');
	}

	public function close_reports_search()
	{
		$this->session->unset_userdata('debtors_search_query');
		$this->session->unset_userdata('visit_invoices');
		$this->session->unset_userdata('visit_payments');
		$this->session->unset_userdata('visit_type_id');
		$this->session->unset_userdata('visit_type');
		$this->session->unset_userdata('patient_number');
		$this->session->unset_userdata('search_title');

		redirect('hospital-reports/invoices-report');
	}

	public function export_debtors()
	{
		$this->accounting_model->export_debtors();
	}

	public function all_debtors()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		// $where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL)';
		// $table = 'visit, patients, visit_type';


		$where = 'patients.patient_id = v_patient_account_balances.patient_id';
		$table = 'patients,v_patient_account_balances';

		$visit_search = $this->session->userdata('all_debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= '';

		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/patients-accounts';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 30;
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
		$query = $this->accounting_model->get_all_visits_view($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'Debtors';
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['total_visits'] = $config['total_rows'];
		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/all_debtors_view', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function all_debtors_old()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		// $where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL)';
		// $table = 'visit, patients, visit_type';


		$where = 'v_patient_visit_statement.balance > 0 AND patients.patient_id = v_patient_visit_statement.patient_id';
		$table = 'patients,v_patient_visit_statement';

		$visit_search = $this->session->userdata('all_debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= '';

		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/debtors';
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
		$query = $this->accounting_model->get_all_visits($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'Debtors';
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['total_visits'] = $config['total_rows'];
		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/all_debtors_view', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_all_debtors_report()
	{
		// alert("dasdhakjh");
		$patient_number = $this->input->post('patient_number');
		$patient_phone = $this->input->post('patient_phone');
		$patient_name = $this->input->post('patient_name');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($patient_phone))
		{

			$patient_phone = ' AND patients.patient_phone1 LIKE \'%'.$patient_phone.'%\' ';


		}
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\' ';
			
			$search_title .= 'Patient number. '.$patient_number;
		}
		
		
		
		$surname = '';

		//search surname
		if(!empty($_POST['patient_name']))
		{
			$search_title .= ' first name <strong>'.$_POST['patient_name'].'</strong>';
			$surnames = explode(" ",$_POST['patient_name']);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' OR patients.patient_othernames LIKE \'%'.addslashes($surnames[$r]).'%\')';
				}
				
				else
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' OR patients.patient_othernames LIKE \'%'.addslashes($surnames[$r]).'%\') AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		$search = $patient_number.$surname.$patient_phone;
		// var_dump($search); die();
		$this->session->set_userdata('all_debtors_search_query', $search);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('accounts/patients-accounts');
	}

	public function close_all_reports_search()
	{
		$this->session->unset_userdata('all_debtors_search_query');

		// redirect('hospital-reports/debtors');
		redirect('accounts/patients-accounts');
	}


	public function deleted_invoices()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_delete = 1';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('visit_deleted_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			// $where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';
		
			// $visit_payments = ' AND payments.payment_created = \''.date('Y-m-d').'\'';
			// $visit_invoices = ' AND visit.visit_date = \''.date('Y-m-d').'\'';
			// $search_title = 'Visit date of '.date('jS M Y', strtotime(date('Y-m-d'))).' ';

			// $this->session->set_userdata('visit_invoices', $visit_invoices);
			// $this->session->set_userdata('visit_payments', $visit_payments);
		


			$where .= '';

		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/deleted-invoices';
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
		$query = $this->accounting_model->get_all_visits_old($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('search_visit_deleted_title');
		if(empty($page_title))
		{
			$page_title = 'Deleted Invoices ';
		}
		
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['total_visits'] = $config['total_rows'];
		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/deleted_invoices', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}


	public function search_deleted_invoices_report()
	{
		$visit_type_id = $this->input->post('visit_type_id');
		$personnel_id = $this->input->post('personnel_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		$branch_code = $this->input->post('branch_code');
		$patient_number = $this->input->post('patient_number');
		$patient_name = $this->input->post('patient_name');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($visit_type_id))
		{
			$visit_type = $visit_type_id;
			$visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';


			
			$this->db->where('visit_type_id', $visit_type_id);
			$query = $this->db->get('visit_type');
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$search_title .= $row->visit_type_name.' ';
			}
		}
		
		if(!empty($patient_number))
		{
			$patient_number = ' AND patients.patient_number LIKE \'%'.$patient_number.'%\' ';
			
			$search_title .= 'Patient number. '.$patient_number;
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
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_payments = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$visit_invoices = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_payments = ' AND payments.payment_created = \''.$visit_date_from.'\'';
			$visit_invoices = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_payments = ' AND payments.payment_created = \''.$visit_date_to.'\'';
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$visit_invoices .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_payments = '';
			$visit_invoices = '';
		}

		$surname = '';

		//search surname
		if(!empty($_POST['patient_name']))
		{
			$search_title .= ' first name <strong>'.$_POST['patient_name'].'</strong>';
			$surnames = explode(" ",$_POST['patient_name']);
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
					$surname .= ' (patients.patient_surname LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\' OR OR patients.patient_othernames LIKE \'%'.mysql_real_escape_string($surnames[$r]).'%\') AND ';
				}
				$count++;
			}
			$surname .= ') ';
		}
		
		else
		{
			$surname = '';
		}
		
		$search = $visit_type_id.$visit_invoices.$patient_number.$surname.$personnel_id;
		
		$this->session->set_userdata('visit_deleted_search_query', $search);
		$this->session->set_userdata('visit_type_id', $visit_type_id);
		$this->session->set_userdata('visit_type', $visit_type);
		$this->session->set_userdata('patient_number', $patient_number);
		$this->session->set_userdata('search_visit_deleted_title', $search_title);
		
		redirect('hospital-reports/deleted-invoices');
	}

	public function close_deleted_reports_search()
	{
		$this->session->unset_userdata('visit_deleted_search_query');
		$this->session->unset_userdata('search_visit_deleted_title');

		

		redirect('hospital-reports/deleted-invoices');
	}

	public function export_deleted_invoices()
	{
		$this->accounting_model->export_deleted_invoices();
	}

	public function general_report()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		$branch_session = $this->session->userdata('branch_id');
		if($branch_session == 0)
		{
			$branch = '';
		}
		else
		{
			$branch = ' AND v_transactions_by_date.branch_id = '.$branch_session;
		}
		// $where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL)';
		// $table = 'visit, patients, visit_type';

		$where = '(v_transactions_by_date.transactionCategory = "Revenue" OR v_transactions_by_date.transactionCategory = "Revenue Payment") AND patients.patient_id = v_transactions_by_date.patient_id'.$branch;
		
		$table = 'v_transactions_by_date,patients';


		$visit_search = $this->session->userdata('general_report_search');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= ' AND v_transactions_by_date.transaction_date = "'.date('Y-m-d').'" ';
		
			// $visit_payments = ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
			// $visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.date('Y-m-d').'\'';
			// $search_title = 'Visit date of '.date('jS M Y', strtotime(date('Y-m-d'))).' ';

			// $this->session->set_userdata('general_report_search', $visit_invoices);
		


		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/general-report';
		$config['total_rows'] = $this->reception_model->other_count_items($table, $where);
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
		$query = $this->accounting_model->get_general_report_data($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All transactions for '.date('Y-m-d');
		}
		$v_data['normal_payments'] = $this->reports_model->get_normal_payments($where, $table, 'cash');
		$v_data['payment_methods'] = $this->reports_model->get_payment_methods($where, $table, 'cash');
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['branches'] = $this->reports_model->get_all_active_branches();
		$v_data['total_visits'] = $config['total_rows'];

		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/general_report', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_general_report()
	{

		// $visit_type_id = $visit_type_idd = $this->input->post('visit_type_id');
		// $branch_id = $branch_idd= $this->input->post('branch_id');
		$visit_date_from = $this->input->post('visit_date_from');
		$visit_date_to = $this->input->post('visit_date_to');
		// $branch_code = $this->input->post('branch_code');
		// $patient_number = $this->input->post('patient_number');
		$patient_name = $this->input->post('patient_name');
		// $this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		
		
		
		//date filter for cash report
		$prev_search = '';
		$prev_table = '';
		
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_from.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_payments = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_to.'\'';
			$visit_invoices = ' AND v_transactions_by_date.transaction_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else
		{
			$visit_payments = '';
			$visit_invoices = '';
		}

		if(!empty($patient_name))
		{
			$search_title .= ' Patient Name <strong>'.$patient_name.'</strong>';
			$surnames = explode(" ",$patient_name);
			$total = count($surnames);
			
			$count = 1;
			$surname = ' AND (';
			for($r = 0; $r < $total; $r++)
			{
				if($count == $total)
				{
					$surname .= 'patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\'';
				}
				
				else
				{
					$surname .= 'patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' AND ';
				}
				$count++;
			}
			$surname .= ') ';
			// var_dump($surname);die();
		}
		
		else
		{
			$surname = '';
		}
		

		
		// var_dump($visit_invoices);die();
		$search = $visit_invoices.$surname;
		
		$this->session->set_userdata('general_report_search', $search);
		$this->session->set_userdata('general_search_title', $search_title);
		
		redirect('hospital-reports/general-report');
	}
	public function close_general_reports_search()
	{
		$this->session->unset_userdata('general_report_search');
		$this->session->unset_userdata('general_search_title');

		redirect('hospital-reports/general-report');

	}
	public function export_general_report()
	{
		$this->accounting_model->export_general_report();

	}

}
?>