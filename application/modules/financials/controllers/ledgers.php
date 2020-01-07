<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";
class Ledgers extends admin
{
    var $documents_path;


	function __construct()
	{
		parent:: __construct();
		$this->load->model('auth/auth_model');
		$this->load->model('financials/financials_model');
	    $this->load->model('admin/admin_model');
	    $this->load->model('admin/users_model');
	    $this->load->model('financials/company_financial_model');
	    $this->load->model('reception/database');
	    $this->load->model('financials/ledgers_model');



		$this->load->model('admin/file_model');

		//path to image directory
		$this->documents_path = realpath(APPPATH . '../assets/documents/vehicles');


		$this->load->library('image_lib');

		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
	}


	public function accounts_ledgers()
	{
		$account_ledger_search = $this->session->userdata('account_ledger_search');
		if($account_ledger_search == 1)
		{
			$search_title = '';
		}
		else
		{
			$search_title = '';
		}



		$data['title'] = 'Accounts Ledgers';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/ledgers/accounts_ledgers', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);
	}


	public function search_ledger()
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$account_id = $this->input->post('account_id');
		$account_where = '';
		$date_where = '';
		$search_title = '';


		$this->form_validation->set_rules('account_id', 'Account', 'required');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_userdata('error_message', validation_errors());
			
		}
		else
		{

			if(!empty($account_id))
			{

				$this->db->where('account_id',$account_id);
				$query = $this->db->get('account');
				$account_name = 'Petty Cash';
				if($query->result() > 0 )
				{
					foreach ($query->result() as $key => $value) {
						# code...
						$account_name = $value->account_name;
					}
				}
				$account_where = ' AND account_id = '.$account_id;
				$search_title = $account_name.' Transactions';
			}
		

			$this->session->set_userdata('account_id', $account_id);
			$this->session->set_userdata('search_title', $search_title);
			$this->session->set_userdata('account_name', $account_name);
			$this->session->set_userdata('account_date_from',$date_from);
			$this->session->set_userdata('account_date_to',$date_to);
			$this->session->set_userdata('account_ledger_search',1);

		}
		
		redirect('company-financials/accounts-ledgers');
		
	
	}
	public function close_search_ledger()
	{
		$this->session->unset_userdata('account_id');
		$this->session->unset_userdata('search_title');
		$this->session->unset_userdata('account_date_from');
		$this->session->unset_userdata('account_date_to');
		$this->session->unset_userdata('account_ledger_search');
		
		redirect('company-financials/accounts-ledgers');
	}


	public function print_account_ledger()
	{

			// var_dump($account); die();
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['search_title'] = 'Income Statement';
		$v_data['title'] = 'Account Ledger';
		$this->load->view('financials/ledgers/print_account_ledger', $v_data);
	}

	public function export_account_ledger()
	{
		$this->ledgers_model->export_account_ledger();
	}

}
?>