<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Purchases extends admin
{
	function __construct()
	{
		parent:: __construct();

    $this->load->model('finance/purchases_model');
		$this->load->model('accounts/accounts_model');
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
		// var_dump($table); die();

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
  public function record_purchased_items()
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

	public function petty_cash()
	{
		$this->form_validation->set_rules('account_to_id', 'Account To','required|xss_clean');
		$this->form_validation->set_rules('transacted_amount', 'Amount','required|xss_clean');
		$this->form_validation->set_rules('reference_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');

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
		$account_from_id = $this->purchases_model->get_account_id('Petty Cash');
		$where =  '((v_account_ledger_by_date.transactionClassification = "Purchase Payment" AND v_account_ledger_by_date.accountName = "Petty Cash")
									OR (v_account_ledger_by_date.transactionCategory = "Transfer" AND  v_account_ledger_by_date.accountName = "Petty Cash")
									OR (v_account_ledger_by_date.transactionCategory = "Expense Payment" AND  v_account_ledger_by_date.accountName = "Petty Cash")
								) ';

		$search_purchases = $this->session->userdata('search_petty_cash');
		if($search_purchases)
		{
			$where .= $search_purchases;
			$search_title = $this->session->userdata('search_petty_cash_title');
		}
		else {

			$add7days = date('Y-m-d', strtotime('-7 days'));
			// $where .= ' AND v_account_ledger_by_date.transactionDate BETWEEN \''.$add7days.'\' AND \''.date('Y-m-d').'\'';
			$transaction_date = date('jS M Y',strtotime($add7days));

			$todays_date = date('jS M Y',strtotime(date('Y-m-d')));
			$search_title = 'Transactions for period '.$transaction_date.' to  '.$todays_date.' ';
		}
		$table = 'v_account_ledger_by_date';

		// var_dump($search_title);die();
		$v_data['search_title'] = $search_title;
		$v_data['query_purchases'] = $this->purchases_model->get_petty_cash($where, $table);
		// var_dump($table); die();
			$v_data['departments'] = $this->purchases_model->get_all_departments();
		$v_data['account_from_id'] = $this->purchases_model->get_account_id('Petty Cash');
		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];

		// $v_data['query_purchases'] = $query;
			$v_data['search_title'] = $search_title;
		// $v_data['page'] = $page;

		$data['title'] = $v_data['title']= 'Petty Cash';

		$data['content'] = $this->load->view('purchases/petty_cash', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}


  public function record_petty_cash()
  {
    //form validation
		$this->form_validation->set_rules('department_id', 'Department','required|xss_clean');
		$this->form_validation->set_rules('account_to_id', 'Expense Account','required|xss_clean');
		$this->form_validation->set_rules('account_from_id', 'Paying Account','required|xss_clean');
		$this->form_validation->set_rules('transacted_amount', 'Amount','required|xss_clean');
        $this->form_validation->set_rules('transaction_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
		// $this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
		// $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');
    // var_dump($_POST);die();
		if ($this->form_validation->run())
		{
			//update order
			// check balance

			// $account_balance = $this->purchases_model->get_account_balance('Petty Cash');
			$transacted_amount = $this->input->post('transacted_amount');

			if($transacted_amount > 5000)
			{
				// var_dump($account_balance);die();
				$this->session->set_userdata('error_message', 'Sorry you are not permitted to transact over Ksh. 5,000/=');
			}
			else {
				if($this->purchases_model->record_petty_cash_transaction())
				{
					$this->session->set_userdata('success_message', 'Expense has been successfully added');
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

    redirect('accounting/petty-cash');
  }

  public function search_petty_cash()
  {
    $visit_date_from = $this->input->post('date_from');
		// $transaction_number = $this->input->post('transaction_number');
		$visit_date_to = $this->input->post('date_to');

		$search_title = '';



     if(!empty($visit_date_from) && !empty($visit_date_to))
     {
       $visit_date = ' AND v_account_ledger_by_date.transactionDate BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
       $search_title .= 'Transaction from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
     }

     else if(!empty($visit_date_from))
     {
			 // $visit_date_from = $visit_date_from;
       $visit_date = ' AND v_account_ledger_by_date.transactionDate = \''.$visit_date_from.'\'';
       $search_title .= 'Transactions of '.date('jS M Y', strtotime($visit_date_from)).' ';
     }

     else if(!empty($visit_date_to))
     {
       $visit_date = ' AND v_account_ledger_by_date.transactionDate = \''.$visit_date_to.'\'';
       $search_title .= 'Transactions of '.date('jS M Y', strtotime($visit_date_to)).' ';
     }

     else
     {
       $visit_date = '';
     }


		$search = $visit_date;

		$this->session->set_userdata('search_petty_cash', $search);
		$this->session->set_userdata('petty_cash_visit_date_from', $visit_date_from);

    redirect('accounting/petty-cash');
  }

	public function close_petty_cash_search()
	{
		$this->session->unset_userdata('search_petty_cash');
		$this->session->unset_userdata('petty_cash_visit_date_from');



		redirect('accounting/petty-cash');
	}

	public function print_petty_cash()
	{
		// var_dump($account); die();
		$v_data['contacts'] = $this->site_model->get_contacts();


		$where =  '((v_account_ledger_by_date.transactionClassification = "Purchase Payment" AND v_account_ledger_by_date.accountName = "Petty Cash")
									OR (v_account_ledger_by_date.transactionCategory = "Transfer" AND  v_account_ledger_by_date.accountName = "Petty Cash")
								OR (v_account_ledger_by_date.transactionCategory = "Expense Payment" AND  v_account_ledger_by_date.accountName = "Petty Cash")) ';

		$search_purchases = $this->session->userdata('search_petty_cash');
		if($search_purchases)
		{
			$where .= $search_purchases;
			$search_title = $this->session->userdata('search_petty_cash_title');
		}
		else {

			$add7days = date('Y-m-d', strtotime('-7 days'));
			$where .= ' AND v_account_ledger_by_date.transactionDate BETWEEN \''.$add7days.'\' AND \''.date('Y-m-d').'\'';
			$transaction_date = date('jS M Y',strtotime($add7days));

			$todays_date = date('jS M Y',strtotime(date('Y-m-d')));
			$search_title = 'Transactions for period '.$transaction_date.' to  '.$todays_date.' ';
		}
		$table = 'v_account_ledger_by_date';

		// var_dump($search_title);die();
		$v_data['search_title'] = $search_title;
		$v_data['query_purchases'] = $this->purchases_model->get_petty_cash($where, $table);
		$v_data['title'] = $search_title;
		$this->load->view('purchases/print_petty_cash', $v_data);
	}

		public function transfer_account_purchases()
    {
      $this->db->from('account_payments');
      $this->db->select('*');
      $this->db->where('account_to_type = 4 AND `account_from_id` = 6 AND account_payment_deleted = 0 AND sync_status = 0 ');

      $query = $this->db->get();
      // var_dump($query);die();
      if($query->num_rows() > 0)
      {
        foreach ($query->result() as $key => $value) {
          # code...
          $amount_paid = $value->amount_paid;
          $account_to_id = $value->account_to_id;
          $account_payment_status = $value->account_payment_status;
          $account_payment_description = $value->account_payment_description;
          $account_from_id = $value->account_from_id;
					$account_payment_id = $value->account_payment_id;
          $created = $value->created;
          $created_by = $value->created_by;
          $receipt_number = $value->receipt_number;
          $payment_to = $value->payment_to;
          $payment_date = $value->payment_date;

          $exploded = explode('-', $payment_date);

          $year = $exploded[0];
          $month = $exploded[1];

          $document_number = '';//$this->transfer_model->create_credit_payment_number();


          $document_number_two = $this->purchases_model->create_purchases_number();
		    $account = array(
		          'account_to_id'=>$account_to_id,
		          'property_id'=>0,
		          'finance_purchase_amount'=>$amount_paid,
		          'finance_purchase_description'=>$account_payment_description,
		          'creditor_id'=>$payment_to,
		          'transaction_number'=>$receipt_number,
		          'transaction_date'=>$payment_date,
		          'created_by'=>$created_by,
		          'document_number'=>$document_number_two,
		          'created'=>$created,
		          'last_modified'=>$created
		          );
		    // var_dump($account); die();
		    if($this->db->insert('finance_purchase',$account))
		    {
		      $finance_purchase_id = $this->db->insert_id();

		      // $document_number = $this->create_purchases_payment();
		      $account = array(
		            'account_from_id'=>$account_from_id,
		            'finance_purchase_id'=>$finance_purchase_id,
		            'amount_paid'=>$amount_paid,
		            'transaction_date'=>$payment_date,
		            'transaction_number'=>$receipt_number,
		            'created_by'=>$created_by,
		            'created'=>$created,
		            'document_number'=>$document_number_two
		            );
		     $this->db->insert('finance_purchase_payment',$account);
				 $sync_array['sync_status'] = 1;
				 $this->db->where('account_payment_id',$account_payment_id);
				 $this->db->update('account_payments',$sync_array);
		    }
        }
      }
    }

		public function transfer_account_purchases_invoices()
    {


      $this->db->from('account_invoices');
      $this->db->select('*');
      $this->db->where('account_to_type = 1 AND `account_from_id` = 6 AND account_invoice_deleted = 0 AND sync_status = 0 AND account_invoice_id > 5174 AND invoice_date < "2019-03-25"');

      $query = $this->db->get();
      // var_dump($query);die();
      if($query->num_rows() > 0)
      {
        foreach ($query->result() as $key => $value) {
          # code...
					$invoice_amount = $value->invoice_amount;
	        $account_to_id = $value->account_to_id;
	        $account_invoice_status = $value->account_invoice_status;
	        $account_invoice_description = $value->account_invoice_description;
	        $account_from_id = $value->account_from_id;
	        $created = $value->created;
	        $created_by = $value->created_by;
	        $invoice_number = $value->voucher_number;
	        $department_id = $value->department_id;
	        $invoice_date = $value->invoice_date;
					$account_invoice_id = $value->account_invoice_id;
					$billed_account_id = $value->billed_account_id;

	        $exploded = explode('-', $invoice_date);

	        $year = $exploded[0];
	        $month = $exploded[1];

          $document_number = '';//$this->transfer_model->create_credit_payment_number();


          $document_number_two = $this->purchases_model->create_purchases_number();
		    $account = array(
		          'account_to_id'=>$account_to_id,
		          'property_id'=>0,
		          'finance_purchase_amount'=>$invoice_amount,
		          'finance_purchase_description'=>$account_invoice_description,
		          'creditor_id'=>$billed_account_id,
		          'transaction_number'=>$invoice_number,
		          'transaction_date'=>$invoice_date,
		          'created_by'=>$created_by,
		          'document_number'=>$document_number_two,
		          'created'=>$created,
		          'last_modified'=>$created,
							'department_id'=>$department_id
		          );
		    // var_dump($account); die();
		    if($this->db->insert('finance_purchase',$account))
		    {
		      $finance_purchase_id = $this->db->insert_id();

		      // $document_number = $this->create_purchases_payment();
		      $account = array(
		            'account_from_id'=>$account_from_id,
		            'finance_purchase_id'=>$finance_purchase_id,
		            'amount_paid'=>$invoice_amount,
		            'transaction_date'=>$invoice_date,
		            'transaction_number'=>$document_number_two,
		            'created_by'=>$created_by,
		            'created'=>$created,
		            'document_number'=>$document_number_two
		            );
		     $this->db->insert('finance_purchase_payment',$account);
				 $sync_array['sync_status'] = 1;
				 $this->db->where('account_invoice_id',$account_invoice_id);
				 $this->db->update('account_invoices',$sync_array);
		    }
        }
      }
    }
}
?>
