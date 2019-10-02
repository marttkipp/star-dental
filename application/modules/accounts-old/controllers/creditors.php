<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounts/controllers/accounts.php";

class Creditors extends accounts 
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('creditors_model');
		$this->load->model('petty_cash_model');
	}
	
	public function index()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('creditor_id > 0');
		$query = $this->db->get('creditor');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$creditor_name = $row->creditor_name;
		}
		
		else
		{
			$creditor_name = '';
		}
		$where = 'creditor.creditor_id > 0';
		$search = $this->session->userdata('search_hospital_creditors');
		///var_dump($search);die();
		
		$where .= $search;
		
		$table = 'creditor';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/creditors';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 40;
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
        $v_data["page"] = $page;
        $v_data["links"] = $this->pagination->create_links();
		$v_data['query'] = $this->creditors_model->get_all_creditors($table, $where, $config["per_page"], $page);
		$data['title'] = $v_data['title'] = 'Creditors';
		$data['content'] = $this->load->view('creditors/creditors', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}


	public function providers()
	{
		
		
		$this->db->where('personnel_type_id = 6');
		$query = $this->db->get('personnel');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$personnel_fname = $row->personnel_fname;
		}
		
		else
		{
			$personnel_fname = '';
		}
		$where = 'personnel.personnel_type_id > 0';
		$search = $this->session->userdata('search_hospital_providers');
		
		$where .= $search;
		
		$table = 'personnel';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounts/providers';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 40;
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
        $v_data["page"] = $page;
        $v_data["links"] = $this->pagination->create_links();
		$v_data['query'] = $this->creditors_model->get_all_providers($table, $where, $config["per_page"], $page);
		$data['title'] = $v_data['title'] = 'Providers';
		$data['content'] = $this->load->view('providers/providers', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function search_hospital_creditors()
	{
		$creditor_name = $this->input->post('creditor_name');
		
		if(!empty($creditor_name))
		{
			$this->session->set_userdata('search_hospital_creditors', ' AND creditor.creditor_name LIKE \'%'.$creditor_name.'%\'');
		}
		
		redirect('accounts/creditors');
	}
	
	public function close_search_hospital_creditors()
	{
		$this->session->unset_userdata('search_hospital_creditors');
		
		redirect('accounts/creditors');
	}
    
	/*
	*
	*	Add a new creditor
	*
	*/
	public function add_creditor() 
	{
		//form validation rules
		$this->form_validation->set_rules('creditor_name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('creditor_email', 'Email', 'xss_clean');
		$this->form_validation->set_rules('creditor_phone', 'Phone', 'xss_clean');
		$this->form_validation->set_rules('creditor_location', 'Location', 'xss_clean');
		$this->form_validation->set_rules('creditor_building', 'Building', 'xss_clean');
		$this->form_validation->set_rules('creditor_floor', 'Floor', 'xss_clean');
		$this->form_validation->set_rules('creditor_address', 'Address', 'xss_clean');
		$this->form_validation->set_rules('creditor_post_code', 'Post code', 'xss_clean');
		$this->form_validation->set_rules('creditor_city', 'City', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_name', 'Contact Name', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_onames', 'Contact Other Names', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
		$this->form_validation->set_rules('creditor_description', 'Description', 'xss_clean');
		$this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
		$this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');
		
		// var_dump($_POST); die();
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			$creditor_id = $this->creditors_model->add_creditor();
			if($creditor_id > 0)
			{
				$this->session->set_userdata("success_message", "Creditor added successfully");
				$redirect_url = $this->input->post('redirect_url');
				if(!empty($redirect_url))
				{
					redirect($redirect_url);
				}
				else
				{
					redirect('accounts/creditors');	
				}
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not add creditor. Please try again");
				
				$redirect_url = $this->input->post('redirect_url');
				if(!empty($redirect_url))
				{
					redirect($redirect_url);
				}
				else
				{
					redirect('accounts/creditors/add_creditor');
				}
			}
		}
		$data['title'] = 'Add creditor';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('creditors/add_creditor', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add a new creditor
	*
	*/
	public function edit_creditor($creditor_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('creditor_name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('creditor_email', 'Email', 'xss_clean');
		$this->form_validation->set_rules('creditor_phone', 'Phone', 'xss_clean');
		$this->form_validation->set_rules('creditor_location', 'Location', 'xss_clean');
		$this->form_validation->set_rules('creditor_building', 'Building', 'xss_clean');
		$this->form_validation->set_rules('creditor_floor', 'Floor', 'xss_clean');
		$this->form_validation->set_rules('creditor_address', 'Address', 'xss_clean');
		$this->form_validation->set_rules('creditor_post_code', 'Post code', 'xss_clean');
		$this->form_validation->set_rules('creditor_city', 'City', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_name', 'Contact Name', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_onames', 'Contact Other Names', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
		$this->form_validation->set_rules('creditor_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
		$this->form_validation->set_rules('creditor_description', 'Description', 'xss_clean');
		$this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
		$this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');
		
		//if form conatins invalid data
		if ($this->form_validation->run())
		{
			$creditor_id = $this->creditors_model->edit_creditor($creditor_id);
			if($creditor_id > 0)
			{
				$this->session->set_userdata("success_message", "Creditor updated successfully");
				redirect('accounts/creditors');
			}
			
			else
			{
				$this->session->set_userdata("error_message","Could not add creditor. Please try again");
				redirect('accounts/creditors/add_creditor');
			}
		}
		$data['title'] = 'Add creditor';
		$v_data['title'] = $data['title'];
		$v_data['creditor'] = $this->creditors_model->get_creditor($creditor_id);
		$data['content'] = $this->load->view('creditors/edit_creditor', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function add_amount()
	{
		//form validation rules
		$this->form_validation->set_rules('creditor_name', 'Account', 'required|xss_clean');
		$this->form_validation->set_rules('creditor_status', 'Status', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->creditors_model->add_creditor())
			{
				$this->session->set_userdata('success_message', 'Account added successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not add creditor. Please try again');
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		
		redirect('creditors/hospital-creditors');
	}
	
	public function statement($creditor_id, $date_from = NULL, $date_to = NULL)
	{
		$where = 'creditor_account.transaction_type_id = transaction_type.transaction_type_id AND creditor_account.creditor_account_delete = 0 AND creditor_account.creditor_id = '.$creditor_id;
		$table = 'creditor_account, transaction_type';
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (creditor_account.creditor_account_date >= \''.$date_from.'\' AND creditor_account.creditor_account_date <= \''.$date_to.'\')';
			$search_title = 'Statement from '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else if(!empty($date_from))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_from)).' ';
		}
		
		else if(!empty($date_to))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_to)).' ';
			$date_from = $date_to;
		}
		
		else
		{
			// $where .= ' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%m\') = \''.date('m').'\' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%Y\') = \''.date('Y').'\'';
			$where .= '';
			$search_title = 'Statement for the month of '.date('M Y').' ';
			$date_from = date('Y-m-d');
		}
		// echo $where;die();
		$v_data['balance_brought_forward'] = $this->creditors_model->calculate_balance_brought_forward($date_from,$creditor_id);
		$creditor = $this->creditors_model->get_creditor($creditor_id);
		$row = $creditor->row();
		$creditor_name = $row->creditor_name;
		$opening_balance = $row->opening_balance;
		$debit_id = $row->debit_id;
		// var_dump($opening_balance); die();
		$v_data['module'] = 1;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['accounts'] = $this->petty_cash_model->get_accounts();
		$v_data['creditor_id'] = $creditor_id;
		$v_data['date_from'] = $date_from;
		$v_data['date_to'] = $date_to;
		$v_data['opening_balance'] = $opening_balance;
		$v_data['debit_id'] = $debit_id;
		$v_data['query'] = $this->creditors_model->get_creditor_account($where, $table);
		$v_data['title'] = $creditor_name.' '.$search_title;
		$data['title'] = $creditor_name.' Statement';
		$data['content'] = $this->load->view('creditors/statement', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}


	public function provider_statement($provider_id)
	{
		$v_data['module'] = 1;
		$v_data['provider_id'] = $provider_id;
		$data['title'] = $v_data['title'] = ' Statement';
		$data['content'] = $this->load->view('providers/statement', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	
	public function record_creditor_account($creditor_id)
	{
		$this->form_validation->set_rules('transaction_type_id', 'Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_amount', 'Amount', 'trim|required|xss_clean');
		if($this->input->post('transaction_type_id')== 1)
		{
			$this->form_validation->set_rules('transaction_code', 'Transaction Code', 'trim|required|xss_clean');
		}
		if ($this->form_validation->run())
		{
			if($this->creditors_model->record_creditor_account($creditor_id))
			{
				$this->session->set_userdata('success_message', 'Record saved successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to save. Please try again');
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		
		redirect('accounts/creditors/statement/'.$creditor_id.'');
	}
	
	public function search_creditor_account($creditor_id)
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		
		if(!empty($date_from) && !empty($date_to))
		{
			redirect('accounts/creditors/statement/'.$creditor_id.'/'.$date_from.'/'.$date_to);
		}
		
		else if(!empty($date_from))
		{
			redirect('accounts/creditors/statement/'.$creditor_id.'/'.$date_from);
		}
		
		else if(!empty($date_to))
		{
			redirect('accounts/creditors/statement/'.$creditor_id.'/'.$date_to);
		}
		
		else
		{
			redirect('accounts/creditors/statement/'.$creditor_id);
		}
	}

	
	public function print_creditor_account($creditor_id, $date_from = NULL, $date_to = NULL)
	{
		$where = 'creditor_account.transaction_type_id = transaction_type.transaction_type_id AND creditor_account.creditor_account_delete = 0  AND creditor_account.creditor_id = '.$creditor_id;

		$table = 'creditor_account, transaction_type';
		
		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (creditor_account.creditor_account_date >= \''.$date_from.'\' AND creditor_account.creditor_account_date <= \''.$date_to.'\')';
			$search_title = 'Statement from '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else if(!empty($date_from))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_from)).' ';
		}
		
		else if(!empty($date_to))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else
		{
			// $where .= ' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%m\') = \''.date('m').'\' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%Y\') = \''.date('Y').'\'';
			$where .='';
			$search_title = 'Statement for the month of '.date('M Y').' ';
		}

		// var_dump($where);
		$creditor = $this->creditors_model->get_creditor($creditor_id);
		$row = $creditor->row();
		$creditor_name = $row->creditor_name;
		
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['date_from'] = $date_from;
		$v_data['date_to'] = $date_to;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['query'] = $this->creditors_model->get_creditor_account($where, $table);
		$v_data['title'] = $creditor_name.' '.$search_title;
		$this->load->view('creditors/print_creditor_account', $v_data);
	}
   public function delete_creditor($creditor_account_id)
    {
		//delete creditor
		
		$this->creditors_model->delete_creditor($creditor_account_id);
		$this->session->set_userdata('success_message', 'Debit or Credit has been deleted');
		redirect('accounts/creditors');
    }	

    public function creditor_summary()
    {
    	$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		
		$where = 'creditor.creditor_id > 0 AND creditor.creditor_id = creditor_account.creditor_id  AND creditor_account.creditor_account_delete = 0 AND creditor_account.transaction_type_id = 2';
		$search = $this->session->userdata('search_hospital_creditors_list');
		
		$where .= $search;
		
		$table = 'creditor,creditor_account';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital-reports/creditors-summary';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 40;
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
        $v_data["page"] = $page;
        $v_data["links"] = $this->pagination->create_links();
		$v_data['query'] = $this->creditors_model->get_all_creditors_account($table, $where, $config["per_page"], $page);
		$data['title'] = $v_data['title'] = 'All Creditors Acccount';
		$data['content'] = $this->load->view('creditors/creditors_view', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
    }

    public function search_creditor_values()
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');


		if(!empty($date_from) && !empty($date_to))
		{
			$where .= ' AND (creditor_account.creditor_account_date >= \''.$date_from.'\' AND creditor_account.creditor_account_date <= \''.$date_to.'\')';
			$search_title = 'Statement from '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else if(!empty($date_from))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_from)).' ';
		}
		
		else if(!empty($date_to))
		{
			$where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			$search_title = 'Statement of '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else
		{
			$where .= ' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%m\') = \''.date('m').'\' AND DATE_FORMAT(creditor_account.creditor_account_date, \'%Y\') = \''.date('Y').'\'';
			$search_title = 'Statement for the month of '.date('M Y').' ';
		}
		

		$visit_data = $where;
		$this->session->set_userdata('search_hospital_creditors_list',$visit_data);

		redirect('hospital-reports/creditors-summary');
		
	}
	public function close_search_values_creditors()
	{
		$this->session->unset_userdata('search_hospital_creditors_list');
		redirect('hospital-reports/creditors-summary');
	}

	public function print_credtors_report()
	{

		$where = 'creditor.creditor_id > 0 AND creditor.creditor_id = creditor_account.creditor_id  AND creditor_account.creditor_account_delete = 0 AND creditor_account.transaction_type_id = 2';
		$search = $this->session->userdata('search_hospital_creditors_list');
		
		$where .= $search;
		
		$table = 'creditor,creditor_account';

		$data['query'] = $this->creditors_model->get_creditors_detail_summary($where,$table);
		
		$data['contacts'] = $this->site_model->get_contacts($branch_id);
		
		$this->load->view('creditors/creditors_summary', $data);
	}
	public function supplier_statement($creditor_id)
	{

		$where = 'creditor_account.transaction_type_id = transaction_type.transaction_type_id AND creditor_account.creditor_id = '.$creditor_id;
		$table = 'creditor_account, transaction_type';
		
		// $date = date('Y-m-d');

		$query_date = date('Y-m-d');

		// First day of the month.
		$first_day =  date('Y-m-01', strtotime($query_date));

		// Last day of the month.
		$last_day =  date('Y-m-t', strtotime($query_date));
				
		// $where .= ' AND (creditor_account.creditor_account_date >= \''.$first_day.'\' AND creditor_account.creditor_account_date <= \''.$last_day.'\')';
		$where .='';
		$search_title = 'Statement from '.date('jS M Y', strtotime($first_day)).' to '.date('jS M Y', strtotime($last_day)).' ';


		// baance brought foward 
		$balance_brought_forward = $this->creditors_model->calculate_balance_brought_forward($first_day,$creditor_id);

		
		$creditor = $this->creditors_model->get_creditor($creditor_id);
		$row = $creditor->row();
		$creditor_name = $row->creditor_name;
		
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['date_from'] = $first_day;
		$v_data['date_to'] = $last_day;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['balance_brought_forward'] = $balance_brought_forward;
		$v_data['query'] = $this->creditors_model->get_creditor_transactions($where, $table);
		$v_data['title'] = $creditor_name.' '.$search_title;
		$this->load->view('creditors/print_creditor_statement', $v_data);
	}


	public function record_creditor_invoice($creditor_id)
	{
		$this->form_validation->set_rules('creditor_account_description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_amount', 'Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_date', 'Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('transaction_code', 'Invoice', 'trim|required|xss_clean');
		
		if ($this->form_validation->run())
		{
			if($this->creditors_model->record_creditor_invoices($creditor_id))
			{
				$this->session->set_userdata('success_message', 'Record saved successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to save. Please try again');
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		
		redirect('accounts/creditors/statement/'.$creditor_id.'');
	}


	public function record_provider_invoice($provider_id)
	{
		$this->form_validation->set_rules('creditor_account_description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_amount', 'Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('creditor_account_date', 'Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('transaction_code', 'Invoice', 'trim|required|xss_clean');
		
		if ($this->form_validation->run())
		{
			if($this->creditors_model->record_provider_invoices($provider_id))
			{
				$this->session->set_userdata('success_message', 'Record saved successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to save. Please try again');
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		
		redirect('accounts/provider-statement/'.$provider_id.'');
	}
}
?>