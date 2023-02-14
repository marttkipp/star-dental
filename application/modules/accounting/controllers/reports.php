<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);
require_once "./application/modules/accounting/controllers/company_financial.php";

class Reports extends company_financial
{	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('accounting_model');
		$this->load->model('administration/reports_model');
		$this->load->model('reception/reception_model');
	}
	

	public function debtors()
	{
		$module = NULL;
		
		$v_data['branch_name'] = $branch_name;
		
		$where = 'visit.patient_id = patients.patient_id AND visit_type.visit_type_id = visit.visit_type AND visit.visit_delete = 0  AND visit.close_card <> 2 AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL)';
		$table = 'visit, patients, visit_type';
		$visit_search = $this->session->userdata('debtors_search_query');
		// var_dump($visit_search);die();
		if(!empty($visit_search))
		{
			$where .= $visit_search;
		
			
			
		}
		else
		{
			$where .= ' AND visit.visit_date = "'.date('Y-m-d').'" ';
		
			$visit_payments = ' AND payments.payment_created = \''.date('Y-m-d').'\'';
			$visit_invoices = ' AND visit.visit_date = \''.date('Y-m-d').'\'';
			$search_title = 'Visit date of '.date('jS M Y', strtotime(date('Y-m-d'))).' ';

			$this->session->set_userdata('visit_invoices', $visit_invoices);
			$this->session->set_userdata('visit_payments', $visit_payments);
		


			$where .= '';

		}
		$segment = 3;
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/all-transactions';
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
		$query = $this->accounting_model->get_all_visits_reports($table, $where, $config["per_page"], $page, 'ASC');
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['search'] = $visit_search;
		$v_data['total_patients'] = $config['total_rows'];		
		
		$page_title = $this->session->userdata('page_title');
		if(empty($page_title))
		{
			$page_title = 'All transactions for '.date('Y-m-d');
		}
		// var_dump($page_title);die();
		$data['title'] = $v_data['title'] = $page_title;
		$v_data['debtors'] = $this->session->userdata('debtors');
		$v_data['type'] = $this->reception_model->get_types();
		$v_data['doctors'] = $this->reception_model->get_doctor();
		$v_data['total_visits'] = $config['total_rows'];
		
		$v_data['module'] = $module;
		
		$data['content'] = $this->load->view('reports/debtors', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_debtors_report()
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
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' OR patients.patient_othernames LIKE \'%'.addslashes($surnames[$r]).'%\')';
				}
				
				else
				{
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' OR OR patients.patient_othernames LIKE \'%'.addslashes($surnames[$r]).'%\') AND ';
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
		
		$this->session->set_userdata('debtors_search_query', $search);
		$this->session->set_userdata('visit_invoices', $visit_invoices);
		$this->session->set_userdata('visit_payments', $visit_payments);
		$this->session->set_userdata('visit_type_id', $visit_type_id);
		$this->session->set_userdata('visit_type', $visit_type);
		$this->session->set_userdata('patient_number', $patient_number);
		$this->session->set_userdata('search_title', $search_title);
		
		redirect('hospital-reports/all-transactions');
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

		redirect('hospital-reports/all-transactions');
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


		$where = 'v_patient_balances.balance > 0 AND patients.patient_id = v_patient_balances.patient_id';
		$table = 'patients,v_patient_balances';

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
		$patient_number = $this->input->post('patient_number');
		$patient_phone = $this->input->post('patient_phone');
		$patient_name = $this->input->post('patient_name');
		$this->session->set_userdata('search_branch_code', $branch_code);
		
		$search_title = 'Showing reports for: ';
		
		if(!empty($patient_phone))
		{

			$patient_phone = ' AND patients.patient_phone1 = '.$patient_phone.' ';


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
					$surname .= ' (patients.patient_surname LIKE \'%'.addslashes($surnames[$r]).'%\' OR OR patients.patient_othernames LIKE \'%'.addslashes($surnames[$r]).'%\') AND ';
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
		
		redirect('hospital-reports/debtors');
	}

	public function close_all_reports_search()
	{
		$this->session->unset_userdata('all_debtors_search_query');

		redirect('hospital-reports/debtors');
	}

	public function export_debtors_report()
	{
		$this->accounting_model->export_debtors_report();
	}

	public function sendmessage_sidebar($patient_id,$balance)
	{
		$data = array('patient_id'=>$patient_id,'balance'=>$balance);
		
		$data['patient'] = $this->reception_model->patient_names2($patient_id);
		
		$page = $this->load->view('sidebar/sendmessage_sidebar',$data);

		echo $page;
	}

}
?>