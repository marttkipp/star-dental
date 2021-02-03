<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Landlord extends admin
{
	function __construct()
	{
		parent:: __construct();

    $this->load->model('finance/landlord_model');
		$this->load->model('real_estate_administration/tenants_model');
    $this->load->model('accounts/accounts_model');
    $this->load->model('finance/purchases_model');
    $this->load->model('real_estate_administration/property_model');
	}

	public function all_transactions()
	{
		//form validation
		// $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
		$this->form_validation->set_rules('account_to_id', 'Account To','required|xss_clean');
		$this->form_validation->set_rules('transacted_amount', 'Amount','required|xss_clean');
    $this->form_validation->set_rules('reference_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
		// $this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
		// $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');

		if ($this->form_validation->run())
		{
			//update order
			if($this->landlord_model->add_payment_amount())
			{
				$this->session->set_userdata('success_message', 'Cheque successfully writted to account');


				redirect('accounting/purchases');
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
		$v_data['accounts'] = $this->purchases_model->get_child_accounts("Bank");
    $v_data['creditors'] = $this->purchases_model->get_creditor();
    $v_data['expense_accounts']= $this->purchases_model->get_child_accounts("Income Accounts");

		$where = 'landlord_transaction_id > 0 ';

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
		$table = 'landlord_transactions';

		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'finance/landlord-transactions';
		$config['total_rows'] = $this->landlord_model->count_items($table, $where);
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
		$query = $this->landlord_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='landlord_transactions.transaction_date', $order_method='DESC');
		// var_dump($query); die();

		$data['title'] = 'Landlord Transactions';
		$v_data['title'] = $data['title'];

		$v_data['query_purchases'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title']= ' Landlord Transactions';

		$data['content'] = $this->load->view('landlord/landlord_transactions', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

  public function record_landlord_transaction()
  {
    //form validation
		$this->form_validation->set_rules('account_to_id', 'Expense Account','required|xss_clean');
		$this->form_validation->set_rules('transacted_amount', 'Amount','required|xss_clean');
    $this->form_validation->set_rules('transaction_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
    $this->form_validation->set_rules('property_id', 'Property','required|xss_clean');
    $this->form_validation->set_rules('transaction_type_id', 'Type','required|xss_clean');
    $this->form_validation->set_rules('bank_id', 'Type','xss_clean');
    // var_dump($_POST);die();
		$transaction_type_id = $this->input->post('transaction_type_id');
		if($transaction_type_id == 4)
		{
			$this->form_validation->set_rules('property_to_id', 'Property To','required|xss_clean');
		}
		if ($this->form_validation->run())
		{
			//update order
			if($this->landlord_model->add_payment_amount())
			{
				$this->session->set_userdata('success_message', 'Amount has been successfully added');
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

    redirect('accounting/landlord-transactions');
  }


}
?>
