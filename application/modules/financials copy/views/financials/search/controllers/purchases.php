<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Purchases extends admin
{
	function __construct()
	{
		parent:: __construct();

    $this->load->model('finance/purchases_model');
    $this->load->model('real_estate_administration/property_model');
	}

	public function all_purchases()
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
			if($this->purchases_model->add_payment_amount())
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

    $v_data['expense_accounts']= $this->purchases_model->get_child_accounts("Expense Accounts");

		$where = 'finance_purchase_id > 0 ';

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
		$table = 'finance_purchase';


		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/purchases';
		$config['total_rows'] = $this->purchases_model->count_items($table, $where);
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
		$query = $this->purchases_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='finance_purchase.created', $order_method='DESC');
		// var_dump($query); die();

		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];

		$v_data['query_purchases'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title']= 'Purchases';

		$data['content'] = $this->load->view('purchases/purchases', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
  public function purchase_payments()
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
			if($this->purchases_model->add_payment_amount())
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
    $v_data['purchase_items'] = $this->purchases_model->get_all_purchase_invoices();

		$where = 'account_payment_deleted = 0 ';
		$table = 'account_payments';


		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/write-cheque';
		$config['total_rows'] = $this->purchases_model->count_items($table, $where);
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
		$query = $this->purchases_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='account_payments.payment_date', $order_method='ASC');
		// var_dump($query); die();

		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title']= 'Purchase Payment';

		$data['content'] = $this->load->view('purchases/purchase_payments', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
  }
  public function record_petty_cash()
  {
    //form validation
		// $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
		$this->form_validation->set_rules('account_to_id', 'Expense Account','required|xss_clean');
		$this->form_validation->set_rules('transacted_amount', 'Amount','required|xss_clean');
    $this->form_validation->set_rules('transaction_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
		// $this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
		// $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');
    // var_dump($_POST);die();
		if ($this->form_validation->run())
		{
			//update order
			if($this->purchases_model->add_payment_amount())
			{
				$this->session->set_userdata('success_message', 'Expense has been successfully added');



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

    redirect('accounting/purchases');
  }


  public function make_payment($finance_purchase_id)
  {
    //form validation
    // $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
    $this->form_validation->set_rules('account_from_id', 'Expense Account','required|xss_clean');
    $this->form_validation->set_rules('amount_paid', 'Amount','required|xss_clean');
    $this->form_validation->set_rules('reference_number', 'Reference Number','required|xss_clean');
    $this->form_validation->set_rules('payment_date', 'Description','required|xss_clean');
    // $this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
    // $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');
    // var_dump($_POST);die();
    if ($this->form_validation->run())
    {
      //update order
      $balance = $this->input->post('balance');
      $amount_paid = $this->input->post('amount_paid');
      if($amount_paid > $balance)
      {
        $this->session->set_userdata('error_message', 'Sorry you are not allowed to make an overpayment to this account ');
      }
      else {
        if($this->purchases_model->payaninvoice($finance_purchase_id))
        {
          $this->session->set_userdata('success_message', 'You have successfully added the payment');
        }

        else
        {
          $this->session->set_userdata('error_message', 'Could not write cheque. Please try again');
        }
      }

    }
    else
    {
      $this->session->set_userdata('error_message', validation_errors());
    }

    redirect('accounting/purchases');
  }

  public function search_purchases()
  {
    $visit_date_from = $this->input->post('date_from');
		$transaction_number = $this->input->post('transaction_number');
		$visit_date_to = $this->input->post('date_to');

		$search_title = '';

		if(!empty($transaction_number))
		{
			$search_title .= $tenant_name.' ';
			$transaction_number = ' AND finance_purchase.transaction_number LIKE \'%'.$transaction_number.'%\'';


		}
		else
		{
			$transaction_number = '';
			$search_title .= '';
		}

     if(!empty($visit_date_from) && !empty($visit_date_to))
     {
       $visit_date = ' AND finance_purchase.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
       $search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
     }

     else if(!empty($visit_date_from))
     {
       $visit_date = ' AND finance_purchase.transaction_date = \''.$visit_date_from.'\'';
       $search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
     }

     else if(!empty($visit_date_to))
     {
       $visit_date = ' AND finance_purchase.transaction_date = \''.$visit_date_to.'\'';
       $search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
     }

     else
     {
       $visit_date = '';
     }


		$search = $visit_date.$transaction_number;

		$this->session->set_userdata('search_purchases', $search);

    redirect('accounting/purchases');
  }
	public function close_search()
	{
		$this->session->unset_userdata('search_purchases');
		redirect('accounting/purchases');
	}
}
?>
