<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounts/controllers/accounts.php";

class Petty_cash extends accounts 
{
	function __construct()
	{
		parent:: __construct();
	}
	
	public function index()
	{
		$date_from = NULL;
		$where = 'petty_cash.transaction_type_id = transaction_type.transaction_type_id AND petty_cash.petty_cash_status = 1 AND petty_cash.petty_cash_delete = 0 AND petty_cash.account_id = account.account_id AND account.account_name = "Petty Cash" ';
		$table = 'petty_cash,transaction_type,account';

		$title = 'Account Details';
		$search = $this->session->userdata('accounts_search');
		$search_title = $this->session->userdata('accounts_search_title');
		$from = $this->session->userdata('date_from');
		$account = $this->session->userdata('account_id');//echo $account;die();
		if(!empty($search))
		{
			$where.= $search;
		}
		else
		{
			$where.=' AND account.account_name = "Petty Cash"';
		}
		if(!empty($search_title))
		{
			$title = $search_title;
		}
		if(!empty($from))
		{
			$date_from = $from;
		}
		//var_dump($where); die();
		$v_data['balance_brought_forward'] = $this->petty_cash_model->calculate_balance_brought_forward($date_from);
		$account = $this->petty_cash_model->get_account_id("Petty Cash");

		// var_dump($account); die();
		$v_data['date_from'] = $from;
		$v_data['date_to'] = $date_to;
		$v_data['account'] = $account;
		$v_data['accounts'] = $this->petty_cash_model->get_expense_accounts();
		$v_data['departments'] = $this->petty_cash_model->get_all_departments();
		// $v_data['query'] = $this->petty_cash_model->get_petty_cash($where, $table);
		$v_data['title'] = $title;
		$data['title'] = 'Accounting';
		$data['content'] = $this->load->view('petty_cash/statement', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}


	
	public function record_petty_cash()
	{
		$this->form_validation->set_rules('transaction_type_id', 'Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('account_id', 'Account', 'xss_clean');
		$this->form_validation->set_rules('account_from_id', 'From Account', 'xss_clean');
		$this->form_validation->set_rules('petty_cash_description', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('petty_cash_amount', 'Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('petty_cash_date', 'Transaction date', 'required|xss_clean');
		$this->form_validation->set_rules('department_id', 'Department ', 'xss_clean');
		
		// credit or debit
		$transaction_type_id = $this->input->post('transaction_type_id');



		if ($this->form_validation->run())
		{
			$to_account = $this->input->post('account_to_id');
			$from_account =$this->input->post('account_from_id');
			$amount_to_charge =$this->input->post('petty_cash_amount');
			$result = $this->petty_cash_model->get_petty_cash_statement($from_account);

			$balance = $result['total_arrears'];
			// var_dump($balance); die();

			if($balance >= $amount_to_charge)
			{
				if($this->petty_cash_model->add_account_invoice())
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
				$this->session->set_userdata('error_message', 'Sorry,, you have insufficient funds to use. Please top up your petty cash before making the expense');
			}
			
			
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		
		redirect('accounting/petty-cash');
	}
	
	public function search_petty_cash()
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$account_id = $this->input->post('account_id');
		$account_where = '';
		$date_where = '';
		$search_title = '';
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
			$account_where = ' AND petty_cash.account_id = '.$account_id;
			$search_title = 'Petty based on account '.$account_name;
		}
		
		if(!empty($date_from) && !empty($date_to))
		{
			$date_where = ' AND (account_invoices.invoice_date >= \''.$date_from.'\' AND account_invoices.invoice_date <= \''.$date_to.'\')';
			//$where .= ' AND account_invoices.invoice_date BETWEEN \''.$date_from.'\' AND \'account_invoices.invoice_date <= '.$date_to.'\')';
			$search_title = 'Petty cash from '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		if(!empty($date_from))
		{
			$date_where = ' AND account_invoices.invoice_date >= \''.$date_from.'\'';
			$search_title = 'Petty cash of '.date('jS M Y', strtotime($date_from)).' ';
		}
		
		else if(!empty($date_to))
		{
			$date_where = ' AND account_invoices.invoice_date <= \''.$date_to.'\'';
			$search_title = 'Petty cash of '.date('jS M Y', strtotime($date_to)).' ';
		} 
		$search = $date_where;
		$this->session->set_userdata('accounts_petty_search', 1);
		$this->session->set_userdata('accounts_search_title', $search_title);
		$this->session->set_userdata('date_from',$date_from);
		$this->session->set_userdata('date_to',$date_to);
		$this->session->set_userdata('account_id',$account_id);
		
		redirect('accounting/petty-cash');
		
		
	
	}


	public function search_ledger()
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$account_id = $this->input->post('account_id');
		$account_where = '';
		$date_where = '';
		$search_title = '';

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
		$this->session->set_userdata('date_from',$date_from);
		$this->session->set_userdata('date_to',$date_to);
		$this->session->set_userdata('ledger_search',1);
		
		redirect('accounting/ledger-entry');
		
		
	
	}

	public function get_transactions($account_id)
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		$search_payment_add ='';
		$date_from = '';
		$date_to = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			
		}

		if(!empty($account_id))
		{

			$this->db->where('account_id',$account_id);
			$query = $this->db->get('account');
			$account_name = '';
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
		$this->session->set_userdata('account_name', $account_name);
		$this->session->set_userdata('search_title', $search_title);
		$this->session->set_userdata('date_from',$date_from);
		$this->session->set_userdata('date_to',$date_to);
		$this->session->set_userdata('ledger_search',1);
		
		redirect('accounting/ledger-entry');
	}


	public function close_search_ledger()
	{
		$this->session->unset_userdata('account_id');
		$this->session->unset_userdata('search_title');
		$this->session->unset_userdata('date_from');
		$this->session->unset_userdata('date_to');
		$this->session->unset_userdata('ledger_search');
		
		redirect('accounting/ledger-entry');
	}
	
	public function print_petty_cash()
	{
		$search = $this->session->userdata('accounts_search');
		$search_title = $this->session->userdata('accounts_search_title');
		$from = $this->session->userdata('date_from');
		$account = $this->session->userdata('account_id');//echo $account;die();
		if(!empty($search))
		{
			$where.= $search;
		}
		else
		{
			$where.=' AND account.account_name = "Petty Cash"';
		}
		if(!empty($search_title))
		{
			$title = $search_title;
		}
		if(!empty($from))
		{
			$date_from = $from;
		}
		//var_dump($where); die();
		$v_data['balance_brought_forward'] = $this->petty_cash_model->calculate_balance_brought_forward($date_from);
		$account = $this->petty_cash_model->get_account_id("Petty Cash");

		// var_dump($account); die();
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['date_from'] = $from;
		$v_data['date_to'] = $date_to;
		$v_data['account'] = $account;
		$v_data['accounts'] = $this->petty_cash_model->get_expense_accounts();
		$v_data['departments'] = $this->petty_cash_model->get_all_departments();
		// $v_data['query'] = $this->petty_cash_model->get_petty_cash($where, $table);
		$v_data['title'] = $search_title;
		$this->load->view('petty_cash/print_petty_cash', $v_data);
	}
	public function account_balances()
	{
		$order = 'account.account_type_id';
		$order_method ='ASC';
		$where = 'account_id > 0 AND account_type.account_type_id = account.account_type_id';
		$table = 'account,account_type';

		$search = $this->session->userdata('search_petty_cash1');
		$where .= $search;
		
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/general-journal-entries';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
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
		$query = $this->petty_cash_model->get_all_cash_accounts($table, $where, $config["per_page"], $page, $order, $order_method);
		
		//change of order method 
		if($order_method == 'DESC')
		{
			$order_method = 'ASC';
		}
		
		else
		{
			$order_method = 'DESC';
		}
		
		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];
		
		$v_data['order'] = $order;
		$v_data['order_method'] = $order_method;
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$data['content'] = $this->load->view('petty_cash/all_accounts', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function deactivate_account($account_id)
	{
		if($this->petty_cash_model->deactivate_account($account_id))
		{
			$this->session->set_userdata('success_message', 'Account deactivated successfully');
		}
		else
		{
			$this->session->set_userdata('error_message', 'Account deactivation failed');
		}
		
		redirect('accounting/general-journal-entries');
	}
	public function activate_account($account_id)
	{
		if($this->petty_cash_model->activate_account($account_id))
		{
			$this->session->set_userdata('success_message', 'Account activated successfully');
		}
		else
		{
			$this->session->set_userdata('error_message', 'Account activation failed');
		}
		
		redirect('accounting/general-journal-entries');
	}
	public function edit_account($account_id)
	{
		//form validation
		$this->form_validation->set_rules('account_name', 'Name','required|xss_clean');
		$this->form_validation->set_rules('account_balance', 'Opening Balance','required|xss_clean');
		$this->form_validation->set_rules('account_type_id', 'Account type','required|xss_clean');

		
		if ($this->form_validation->run())
		{
			//update order
			if($this->petty_cash_model->update_account($account_id))
			{
				$this->session->set_userdata('success_message', 'Account updated successfully');
				redirect('accounting/general-journal-entries');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update account. Please try again');
			}
		}
		
		//open the add new order
		$data['title'] = $v_data['title']= 'Edit Account';
		$v_data['types'] = $this->petty_cash_model->get_type();
		$v_data['parent_accounts'] = $this->petty_cash_model->get_parent_accounts();
		
		//select the order from the database
		$query = $this->petty_cash_model->get_account($account_id);
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('petty_cash/edit_account', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function add_account()
	{
		//form validation
		$this->form_validation->set_rules('account_name', 'Name','required|xss_clean');
		$this->form_validation->set_rules('account_balance', 'Opening Balance','required|xss_clean');
		$this->form_validation->set_rules('account_type_id', 'Account_type','required|xss_clean');
		
		if ($this->form_validation->run())
		{
			//update order
			if($this->petty_cash_model->add_account())
			{
				$this->session->set_userdata('success_message', 'Account updated successfully');
				redirect('accounting/general-journal-entries');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update account. Please try again');
			}
		}
		
		//open the add new order
		$v_data['types'] = $this->petty_cash_model->get_type();
		$v_data['parent_accounts'] = $this->petty_cash_model->get_parent_accounts();
		$data['title'] = $v_data['title']= 'Add Account';
		$data['content'] = $this->load->view('petty_cash/add_account', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	public function write_cheque()
	{
		//form validation
		$this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
		$this->form_validation->set_rules('account_to_id', 'Account To','required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
		$this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
		$this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');
		
		if ($this->form_validation->run())
		{
			//update order
			if($this->petty_cash_model->add_account_payment())
			{
				$this->session->set_userdata('success_message', 'Cheque successfully writted to account');


				redirect('accounting/write-cheque');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not write cheque. Please try again');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', validation_errors());	
		}


		
		//open the add new order
		$v_data['accounts'] = $this->petty_cash_model->get_child_accounts("Bank");

		$where = 'account_payment_deleted = 0 ';
		$table = 'account_payments';

		
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/write-cheque';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
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
		$query = $this->petty_cash_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='account_payments.created', $order_method='DESC');
		// var_dump($query); die();
	
		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title']= 'Write Cheque';

		$data['content'] = $this->load->view('accounting/accounting/write_cheques', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function close_search()
	{
		$this->session->unset_userdata('accounts_petty_search');
		$this->session->unset_userdata('accounts_search_title');
		$this->session->unset_userdata('date_from');
		$this->session->unset_userdata('date_to');
		$this->session->unset_userdata('account_id');
		redirect('accounting/petty-cash');
	}

	public function delete_petty_cash($petty_cash_id)
    {
		//delete creditor
		
		$this->petty_cash_model->delete_petty_cash($petty_cash_id);
		$this->session->set_userdata('success_message', 'Debit or Credit has been deleted');
		redirect('accounting/petty-cash');
    }

    public function delete_invoice_entry($account_invoice_id)
    {
		//delete creditor
		
		$array['account_invoice_deleted'] = 1;
		$array['account_invoice_deleted_by'] = $this->session->userdata('personnel_id');
		$array['account_invoice_deleted_date'] = date('Y-m-d');

		$this->db->where('account_invoice_id',$account_invoice_id);
		$this->db->update('account_invoices',$array);
		$this->session->set_userdata('success_message', 'You have successfully removed the entry');
		redirect('accounting/petty-cash');
    }

    public function delete_invoice_ledger_entry($account_invoice_id)
    {
		//delete creditor
		
		$array['account_invoice_deleted'] = 1;
		$array['account_invoice_deleted_by'] = $this->session->userdata('personnel_id');
		$array['account_invoice_deleted_date'] = date('Y-m-d');

		$this->db->where('account_invoice_id',$account_invoice_id);
		$this->db->update('account_invoices',$array);
		$this->session->set_userdata('success_message', 'You have successfully removed the entry');
		redirect('accounting/ledger-entry');
    }
    public function delete_payment_entry($account_payment_id)
    {
		//delete creditor
		
		$array['account_payment_deleted'] = 1;
		$array['account_payment_deleted_by'] = $this->session->userdata('personnel_id');
		$array['account_payment_deleted_date'] = date('Y-m-d');

		$this->db->where('account_payment_id',$account_payment_id);
		$this->db->update('account_payments',$array);
		$this->session->set_userdata('success_message', 'You have successfully removed the entry');
		redirect('accounting/petty-cash');
    }


     public function delete_payment_ledger_entry($account_payment_id)
    {
		//delete creditor
		
		$array['account_payment_deleted'] = 1;
		$array['account_payment_deleted_by'] = $this->session->userdata('personnel_id');
		$array['account_payment_deleted_date'] = date('Y-m-d');

		$this->db->where('account_payment_id',$account_payment_id);
		$this->db->update('account_payments',$array);
		$this->session->set_userdata('success_message', 'You have successfully removed the entry');
		redirect('accounting/write-cheque');
    }	

    public function get_department_accounts($department_id)
	{

		
		$table = "department_accounts,account";
		$where = "department_accounts.account_id = account.account_id AND department_accounts.department_account_delete = 0 AND department_accounts.department_id = ".$department_id;
		$select = "account_name AS charge_to_name, department_accounts.account_id AS charge_to_id";
	
		echo '<option value="0">--Select an option --</option>';
		

		$options = $this->petty_cash_model->get_type_variables($table,$where,$select);
		foreach($options->result() AS $key) 
		{ 
			echo '<option value="'.$key->charge_to_id.'"> '.$key->charge_to_name.'</option>';			
		}
		
		
	}


    public function get_list_type($type)
	{

		if($type == 2)
		{
			$table = "creditor";
			$where = "creditor_id > 0";
			$select = "creditor_name AS charge_to_name, creditor_id AS charge_to_id";
		}
		else if($type == 3)
		{
			$table = "personnel,personnel_job,job_title";
			$where = "personnel.personnel_id = personnel_job.personnel_id AND personnel_job.job_title_id = job_title.job_title_id AND job_title.job_title_name = 'Dentist'";
			$select = "personnel_fname AS charge_to_name, personnel.personnel_id AS charge_to_id";

		}
		else if($type == 1)
		{
			$query = $this->petty_cash_model->get_child_accounts("Bank");
		}

		else if($type == 4)
		{
			$query = $this->petty_cash_model->get_child_accounts("Expense Accounts");
		}

		echo '<option value="0">--Select an option --</option>';
		if($type == 2)
		{

			$options = $this->petty_cash_model->get_type_variables($table,$where,$select);
			foreach($options->result() AS $key) 
			{ 
				echo '<option value="'.$key->charge_to_id.'">'.$key->charge_to_name.'</option>';			
			}
		}
		else if($type == 3)
		{

			$options = $this->petty_cash_model->get_type_variables($table,$where,$select);
			foreach($options->result() AS $key) 
			{ 
				echo '<option value="'.$key->charge_to_id.'">Dr. '.$key->charge_to_name.'</option>';			
			}
		}
		else
		{
			$options = $query;
			foreach($options->result() AS $key_old) 
			{ 
				echo '<option value="'.$key_old->account_id.'">'.$key_old->account_name.'</option>';			
			}
		}
		
	}

	public function ledger()
	{
		

		$ledger_search = $this->session->userdata('ledger_search');
		if($ledger_search == 1)
		{
			$search_title = '';
		}
		else
		{
			$search_title = '';
		}
		
		$v_data['title'] = '';
		$data['title'] = 'Accounting';
		$data['content'] = $this->load->view('reports/ledger', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_petty_cash1()
	{
		$account_name = $this->input->post('account_name');
		
		if(!empty($account_name))
		{
			$this->session->set_userdata('search_petty_cash1', ' AND account.account_name LIKE \'%'.$account_name.'%\'');
		}
		
		redirect('accounting/general-journal-entries');
	}
	
	public function close_search_petty_cash()
	{
		$this->session->unset_userdata('search_petty_cash1');
		
		redirect('accounting/general-journal-entries');
	}
    

}
?>