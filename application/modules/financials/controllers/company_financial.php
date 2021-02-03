<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";
error_reporting(E_ALL);
class Company_financial extends admin
{
    var $documents_path;


	function __construct()
	{
		parent:: __construct();
		$this->load->model('auth/auth_model');
		$this->load->model('financials_model');
	    $this->load->model('admin/admin_model');
	    $this->load->model('admin/users_model');
	    $this->load->model('company_financial_model');
	    $this->load->model('reception/database');



		$this->load->model('admin/file_model');

		//path to image directory
		$this->documents_path = realpath(APPPATH . '../assets/documents/vehicles');


		$this->load->library('image_lib');

		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
	}


  /*
	*
	*	Default action is to show all the customer
	*
	*/
	public function index()
	{

		$data['title'] = 'Company Financials';
		$v_data['title'] = $data['title'];
    // var_dump($v_data);die();
		$data['content'] = $this->load->view('financials/financials/landing', $v_data, TRUE);

		// $this->load->view('admin/templates/general_page', $data);
    // $data['content'] = $this->load->view('finance/transfer/write_cheques', $v_data, true);
    // $this->load->view('admin/templates/general_page', $data);
    $data['title'] = 'Dashboard';
    $data['sidebar'] = 'reception_sidebar';
    $this->load->view('admin/templates/general_page', $data);
	}

	public function profit_and_loss()
	{
		$data['title'] = 'Profit and Loss';
		$v_data['title'] = $data['title'];

    	$search = $this->session->userdata('income_statement_search');
    	$date_from = date('Y-m').'-01';
		$date_to = date("Y-m-t", strtotime($date_from));
		// var_dump($date_to);die();
		if($search != 1)
		{

			$search_title =  'Reporting period: '.date('F j, Y', strtotime($date_from)).' to ' .date('F j, Y', strtotime($date_to));


			$this->session->set_userdata('date_from_income_statement',$date_from);
			$this->session->set_userdata('date_to_income_statement',$date_to);
			$this->session->set_userdata('income_statement_title_search',$search_title);
			$this->session->set_userdata('income_statement_search',1);

		}


		$this->session->unset_userdata('date_from_stock1');
		$this->session->unset_userdata('date_to_stock1');
		$this->session->unset_userdata('stock_title_search1');
		$this->session->unset_userdata('stock_report_id#1');

		$this->session->unset_userdata('date_from_stock2');
		$this->session->unset_userdata('date_to_stock2');
		$this->session->unset_userdata('stock_title_search2');
		$this->session->unset_userdata('stock_report_id#2');

		$this->session->unset_userdata('date_from_stock3');
		$this->session->unset_userdata('date_to_stock3');
		$this->session->unset_userdata('stock_title_search3');
		$this->session->unset_userdata('stock_report_id#3');

		$this->session->unset_userdata('date_from_stock4');
		$this->session->unset_userdata('date_to_stock4');
		$this->session->unset_userdata('stock_title_search4');
		$this->session->unset_userdata('stock_report_id#4');


		$this->session->unset_userdata('date_from_stock5');
		$this->session->unset_userdata('date_to_stock5');
		$this->session->unset_userdata('stock_title_search5');
		$this->session->unset_userdata('stock_report_id#5');


		$this->session->unset_userdata('date_from_stock6');
		$this->session->unset_userdata('date_to_stock6');
		$this->session->unset_userdata('stock_title_search6');
		$this->session->unset_userdata('stock_report_id#6');


		$data['content'] = $this->load->view('financials/financials/profit_and_loss', $v_data, true);
	   $this->load->view('admin/templates/general_page', $data);
	}

	public function balance_sheet()
	{
		$data['title'] = 'Balance Sheet';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/financials/balance_sheet', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);
	}
	public function aged_receivables()
	{
		$data['title'] = 'Aged Receivables';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/receivables/aged_receivables', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);
	}
	public function sales_taxes()
	{
		$data['title'] = 'Sales Taxes';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/taxes/sales_taxes', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}

	public function customers_income()
	{
		$data['title'] = 'Income By Customer';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/income/customer_income', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}

	public function vendor_expenses()
	{
		$data['title'] = 'Vendor Expenses';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/vendors/vendor_expenses', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}

	public function aged_payables()
	{
		$data['title'] = 'Vendor Expenses';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/vendors/aged_payables', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}
	public function general_ledger()
	{
		$data['title'] = 'General Ledger';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/other/general_ledger', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}

	public function account_transactions()
	{
		$data['title'] = 'General Ledger';
		$v_data['title'] = $data['title'];
		$data['content'] = $this->load->view('financials/other/account_transactions', $v_data, true);
	    $this->load->view('admin/templates/general_page', $data);

	}

  public function mpesa()
  {
      $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      $credentials = base64_encode('wE2XiEqWDIwOH94xqtABTJGRgVGQuP04:xzNBaRpi61SdFUcL');
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
      curl_setopt($curl, CURLOPT_HEADER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      $curl_response = curl_exec($curl);

      echo json_decode($curl_response);

  }



  public function search_income_statement()
	{
		$date_from = $year_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{

			// $date_to = $year_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
			// $date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
			$date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else
		{
			$date_from = date('Y-m').'-01';
			$date_to = date('Y-m-d');
			$search_title = 'Income Statementr of '.date('Y').' ';
		}


		$this->session->set_userdata('date_from_income_statement',$date_from);
		$this->session->set_userdata('date_to_income_statement',$date_to);
		$this->session->set_userdata('income_statement_title_search',$search_title);
		$this->session->set_userdata('income_statement_search',1);

		redirect($redirect_url);

	}

	public function close_income_statement_search()
	{
		$this->session->unset_userdata('date_from_income_statement');
		$this->session->unset_userdata('date_to_income_statement');
		$this->session->unset_userdata('income_statement_title_search');
		$this->session->unset_userdata('income_statement_search');
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);

	}



  public function search_balance_sheet()
	{
		$date_from = $year_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{

			// $date_to = $year_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
			// $date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
			$date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else
		{
			$date_from = date('Y-m').'-01';
			$date_to = date('Y-m-d');
			$search_title = 'Balance sheet statement from  '.date('Y').' ';
		}

		// var_dump($date_to); die();
		$this->session->set_userdata('date_from_balance_sheet',$date_from);
		$this->session->set_userdata('date_to_balance_sheet',$date_to);
		$this->session->set_userdata('balance_sheet_title_search',$search_title);
		$this->session->set_userdata('balance_sheet_search',1);

		redirect($redirect_url);

	}


  public function search_tax()
	{
		$date_from = $year_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{

			// $date_to = $year_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
			// $date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
			$date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else
		{
			$date_from = date('Y-m').'-01';
			$date_to = date('Y-m-d');
			$search_title = 'Tax statement from  '.date('Y').' ';
		}

		// var_dump($date_to); die();
		$this->session->set_userdata('date_from_tax',$date_from);
		$this->session->set_userdata('date_to_tax',$date_to);
		$this->session->set_userdata('tax_title_search',$search_title);
		$this->session->set_userdata('tax_search',1);

		redirect($redirect_url);

	}


  public function search_customer_income()
	{
		$date_from = $year_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{

			// $date_to = $year_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
			// $date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
			$date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		else
		{
			$date_from = date('Y-m-d');
			$date_to = date('Y-m-d');
			$search_title = 'Tax statement from  '.date('Y').' ';
		}

		// var_dump($date_to); die();
		$this->session->set_userdata('date_from_customer_income',$date_from);
		$this->session->set_userdata('date_to_customer_income',$date_to);
		$this->session->set_userdata('customer_income_title_search',$search_title);
		$this->session->set_userdata('customer_income_search',1);

		redirect($redirect_url);

	}

  	public function print_income_statement()
	{
		// var_dump($account); die();
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['search_title'] = 'Income Statement';
		$v_data['title'] = 'Income Statement';
		$this->load->view('financials/financials/print_income_statement', $v_data);
	}
	public function print_balance_sheet()
	{
		// var_dump($account); die();
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['search_title'] = 'Balance Sheet';
		$v_data['title'] = 'Balance Sheet';
		$this->load->view('financials/financials/print_balance_sheet', $v_data);
	}



	public function services_bills($department_id)
	{
		$where = 'visit_charge.visit_id = visit.visit_id AND visit.patient_id = patients.patient_id AND visit.visit_type = visit_type.visit_type_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND service_charge.service_id = service.service_id AND service.department_id ='.$department_id;

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$where .=  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$where .= ' AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$where .= ' AND visit_charge.date = \''.$date_to.'\'';
			}
		}
		else
		{
			$where .= '';

		}
		$table = 'visit_charge,visit,patients,service_charge,visit_type,service';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'company-financials/services-bills/'.$department_id;
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
        $v_data['department_id'] = $department_id;
        $v_data["links"] = $this->pagination->create_links();
		$v_data['query'] = $this->company_financial_model->get_all_service_bills($table, $where, $config["per_page"], $page,'visit_charge.date','ASC');
		$data['title'] = $v_data['title'] = 'Department Bills';
		$data['content'] = $this->load->view('financials/financials/services_bills', $v_data, TRUE);

		$this->load->view('admin/templates/general_page', $data);
	}
	public function export_services_bills($department_id)
	{
		// var_dump($department_id); die();
		$this->company_financial_model->export_services_bills($department_id);
	}

	public function expense_ledger_old($account_id)
	{
		// var_dump("dakdasda"); die();



		// redirect('financials/financials/get_expense_ledger');
	}

	public function expense_ledger($account_id)
	{
		$account_name = $this->company_financial_model->get_account_name($account_id);
		$this->session->set_userdata('expense_account_id',$account_id);
		$this->session->set_userdata('expense_account_name',$account_name);
		$this->session->set_userdata('expense_search_title',$account_name.' Expenses');
		$this->session->set_userdata('expense_ledger_search',1);
		if($account_id == 1)
		{
			$search_title = '';
		}
		else
		{
			$search_title = '';
		}
		$expense_search_title = $this->session->userdata('expense_search_title');
		$v_data['title']  = $expense_search_title;
		$data['title'] = $expense_search_title;
    $v_data['account_id'] = $account_id;
		// $v_data['account']
		$data['content'] = $this->load->view('financials/financials/expense_ledger', $v_data, TRUE);

		$this->load->view('admin/templates/general_page', $data);
	}
	public function print_expense_ledger()
	{

		$expense_search_title = $this->session->userdata('expense_search_title');
		$v_data['title'] = $expense_search_title;
		$data['title'] = $expense_search_title;
		$v_data['contacts'] = $this->site_model->get_contacts();
		$v_data['title'] = $expense_search_title;
		$this->load->view('reports/print_expense_ledger', $v_data);
	}


	public function search_expense_ledger()
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


		if(!empty($date_from) && !empty($date_to))
		{
			$date_from = $date_from;
			$date_to = $date_to;
			$search_title = $account_name.' EXPENSES REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';

			$date_from = $date_from;
			$search_title = $account_name.' EXPENSES FOR '.date('jS M Y', strtotime($date_from)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			$search_title = $account_name.' EXPENSES '.date('jS M Y', strtotime($date_to)).' ';
		}

		else
		{
			$date_from = '';
			$date_to = '';
			$search_title =  $account_name.' EXPENSES ';
		}




		$this->session->set_userdata('account_id', $account_id);
		$this->session->set_userdata('expense_account_id', $account_id);
		$this->session->set_userdata('expense_search_title', $search_title);
		$this->session->set_userdata('expense_account_name', $account_name);
		$this->session->set_userdata('expense_ledger_search',1);
		$this->session->set_userdata('balance_sheet_search',1);
		$this->session->set_userdata('date_from_balance_sheet',$date_from);
		$this->session->set_userdata('date_to_balance_sheet',$date_to);

		redirect('accounting/petty_cash/get_expense_ledger');



	}

	public function close_expense_ledger()
	{
		$this->session->unset_userdata('account_id');
		$this->session->unset_userdata('expense_account_id');
		$this->session->unset_userdata('expense_search_title');
		$this->session->unset_userdata('expense_account_name');
		$this->session->unset_userdata('expense_ledger_search');
		$this->session->unset_userdata('balance_sheet_search');
		$this->session->unset_userdata('date_from_balance_sheet');
		$this->session->unset_userdata('date_to_balance_sheet');

		redirect('accounting/petty_cash/get_expense_ledger');
	}
  public function account_ledger($account_id)
  {
    $account_name = $this->company_financial_model->get_account_name($account_id);
		$this->session->set_userdata('bank_account_id',$account_id);
		$this->session->set_userdata('bank_account_name',$account_name);
		$this->session->set_userdata('bank_search_title',$account_name.' Statement');
		$this->session->set_userdata('bank_ledger_search',1);
		if($account_id == 1)
		{
			$search_title = '';
		}
		else
		{
			$search_title = '';
		}
		$expense_search_title = $this->session->userdata('bank_search_title');
		$v_data['title']  = $expense_search_title;
		$data['title'] = $expense_search_title;
    	$v_data['account_id'] = $account_id;
		// $v_data['account']
		$data['content'] = $this->load->view('financials/financials/accounts_ledger', $v_data, TRUE);

		$this->load->view('admin/templates/general_page', $data);
  }
  public function search_customer_income_list()
  {
    $search_status = $this->session->userdata('balance_sheet_search');

    if($search_status == 1)
    {
      $date_from = $this->session->userdata('date_from_balance_sheet');
      $date_to = $this->session->userdata('date_to_balance_sheet');


      if(!empty($date_from) && !empty($date_to))
      {
        // $date_from = $date_from;
        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }


      else if(!empty($date_from))
      {
        //
        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }

      else if(!empty($date_to))
      {
        $date_to = $date_to;
        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }

      else
      {
        $date_from = date('Y-m').'-01';
        $date_to = date('Y-m-d');
        $search_title = 'REPORT FOR  '.date('Y').' ';
      }

  // var_dump('sdasda');die();
      // set the date_to_income_statement
      $this->session->set_userdata('customer_income_search',1);
      $this->session->set_userdata('date_from_customer_income',$date_from);
      $this->session->set_userdata('date_to_customer_income',$date_to);
      $this->session->set_userdata('customer_income_title_search',$search_title);
    }
    else {

        $search_title = 'REPORT FOR  '.date('jS M Y', strtotime(date('Y-m-d'))).' ';
        $date_from = date('Y-m').'-01';
        $date_to = date('Y-m-d');
        $this->session->set_userdata('customer_income_search',1);
        $this->session->set_userdata('date_from_customer_income',$date_from);
        $this->session->set_userdata('date_to_customer_income',$date_to);
        $this->session->set_userdata('customer_income_title_search',$search_title);
    }

    redirect('company-financials/customer-income');


  }

  public function search_customer_invoices($visit_type_id)
  {
    $search_status = $this->session->userdata('customer_income_search');

    if($search_status == 1)
    {
      $date_from = $this->session->userdata('date_from_customer_income');
      $date_to = $this->session->userdata('date_to_customer_income');

      if(!empty($date_from) && !empty($date_to))
      {
        $visit_date = ' AND visit.visit_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'';
        $search_title .= 'Visit date from '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }

      else if(!empty($date_from))
      {
        $visit_date = ' AND visit.visit_date = \''.$date_from.'\'';
        $search_title .= 'Visit date of '.date('jS M Y', strtotime($date_from)).' ';
      }

      else if(!empty($date_to))
      {
        $visit_date = ' AND visit.visit_date = \''.$date_to.'\'';
        $search_title .= 'Visit date of '.date('jS M Y', strtotime($date_to)).' ';
      }

      else
      {
        $visit_date = ' AND visit.visit_date = \''.date('Y-m-d').'\'';
      }
      if(!empty($visit_type_id))
      {
        $visit_type_id = ' AND visit.visit_type = '.$visit_type_id.' ';
      }
      // $visit_type = ' AND visit.visit_date = \''.date('Y-m-d').'\'';
      // set the date_to_income_statement
      $this->session->set_userdata('customer_income_search',1);
      $this->session->set_userdata('date_from_customer_income',$date_from);
      $this->session->set_userdata('date_to_customer_income',$date_to);
      $this->session->set_userdata('customer_income_title_search',$search_title);

      $search = $visit_type_id.$visit_date;
  		$this->session->unset_userdata('all_invoices_search');
  		$this->session->set_userdata('all_invoices_search', $search);
  		$this->session->set_userdata('search_title', $search_title);
    }
    else {
        // var_dump($search_title);die();
        $search_title = 'REPORT FOR  '.date('jS M Y', strtotime(date('Y-m-d'))).' ';
        $visit_date = ' AND visit.visit_date = \''.date('Y-m-d').'\'';
        $search = $visit_date;
        $this->session->unset_userdata('all_invoices_search');
        $this->session->set_userdata('all_invoices_search', $search);
        $this->session->set_userdata('search_title', $search_title);
    }

    redirect('cash-office/invoices');
  }

  

  	public function search_vendor_expenses()
	{
		$date_from = $year_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{

			// $date_to = $year_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}


		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
			// $date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' ';
		}

		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
			$date_to = $year_from;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' ';
		}

		else
		{
			$date_from = date('Y-m').'-01';
			$date_to = date('Y-m-d');
			$search_title = 'REPORT FOR '.date('Y').' ';
		}

		// var_dump($date_to); die();
		$this->session->set_userdata('date_from_vendor_expense',$date_from);
		$this->session->set_userdata('date_to_vendor_expense',$date_to);
		$this->session->set_userdata('vendor_expense_title_search',$search_title);
		$this->session->set_userdata('vendor_expense_search',1);

		redirect($redirect_url);

	}
	public function close_creditor_expense_ledger($creditor_id)
	{

		$this->session->unset_userdata('date_from_vendor_expense');
		$this->session->unset_userdata('date_to_vendor_expense');
		$this->session->unset_userdata('vendor_expense_title_search');
		$this->session->unset_userdata('vendor_expense_search');

		redirect('creditor-statement/'.$creditor_id);

	}
  public function search_creditor_expense_list()
  {
    $search_status = $this->session->userdata('balance_sheet_search');

    if($search_status == 1)
    {
      $date_from = $this->session->userdata('date_from_balance_sheet');
      $date_to = $this->session->userdata('date_to_balance_sheet');


      if(!empty($date_from) && !empty($date_to))
      {

        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }


      else if(!empty($date_from))
      {

        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }

      else if(!empty($date_to))
      {
        $date_to = $date_to;
        $date_to = $year_from;
        $search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
      }

      else
      {
        $date_from = date('Y-m').'-01';
        $date_to = date('Y-m-d');
        $search_title = 'REPORT FOR  '.date('Y').' ';
      }


      // set the date_to_income_statement
      $this->session->set_userdata('vendor_expense_search',1);
      $this->session->set_userdata('date_from_vendor_expense',$date_from);
      $this->session->set_userdata('date_to_vendor_expense',$date_to);
      $this->session->set_userdata('vendor_expense_title_search',$search_title);
    }
    else {
        // var_dump($search_title);die();
        $search_title = 'REPORT FOR  '.date('jS M Y', strtotime(date('Y-m-d'))).' ';
        $this->session->set_userdata('vendor_expense_search',0);
        $this->session->set_userdata('vendor_expense_title_search',$search_title);
    }

    redirect('company-financials/vendor-expenses');
  }

  public function creditor_statement($creditor_id)
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
	$v_data['balance_brought_forward'] = 0;// $this->creditors_model->calculate_balance_brought_forward($date_from,$creditor_id);
	$creditor = $this->company_financial_model->get_creditor($creditor_id);
	$row = $creditor->row();
	$creditor_name = $row->creditor_name;
	$opening_balance = $row->opening_balance;
	$debit_id = $row->debit_id;
	$start_date = $row->start_date;
	// var_dump($opening_balance); die();
	$v_data['module'] = 1;
	$v_data['creditor_name'] = $creditor_name;
	$v_data['creditor_id'] = $creditor_id;
	$v_data['opening_balance'] = $opening_balance;
	$v_data['debit_id'] = $debit_id;


  	// $v_data['query'] = $this->company_financial_model->get_creditor_account($where, $table);
	$v_data['title'] = $creditor_name.' '.$search_title;
	$data['title'] = $creditor_name.' Statement';
	$data['content'] = $this->load->view('vendors/creditor_statement', $v_data, TRUE);
	$this->load->view('admin/templates/general_page', $data);
  }
  public function print_creditor_statement($creditor_id)
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
  	$v_data['balance_brought_forward'] = 0;// $this->creditors_model->calculate_balance_brought_forward($date_from,$creditor_id);
    // var_dump($where);
    $creditor = $this->company_financial_model->get_creditor($creditor_id);
    $row = $creditor->row();
    $creditor_name = $row->creditor_name;
    $start_date = $row->start_date;
    $opening_balance = $row->opening_balance;
    $debit_id = $row->debit_id;

    $v_data['contacts'] = $this->site_model->get_contacts();
    $v_data['creditor_id'] = $creditor_id;
    $v_data['start_date'] = $start_date;
    $v_data['opening_balance'] = $opening_balance;
    $v_data['debit_id'] = $debit_id;


    	// $v_data['query'] = $this->company_financial_model->get_creditor_account($where, $table);
  	$v_data['title'] = $creditor_name.' '.$search_title;
  	$data['title'] = $creditor_name.' Statement';
    $this->load->view('vendors/print_creditor_account', $v_data);
  	// $this->load->view('admin/templates/general_page', $data);
  }


  public function view_closing_stock()
  {
  	$report_id = 1;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }
    $table = 'v_product_stock';
    $where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales")  '.$search_invoice_add;



    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-closing-stock';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Opening Stock';
     $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/closing_stock', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);
  }

  public function view_purchases()
  {
  	$report_id = 2;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }

    $table = 'v_product_stock';
    $where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;



    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-purchases';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Purchases';
    $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/purchases_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }


  public function view_return_outwards()
  {
  	$report_id = 4;
   	$search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }

    $table = 'v_product_stock';
    $where = '(v_product_stock.transactionClassification = "Supplier Credit Note" ) AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;


    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-return-outwards';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Return Outwards View';
     $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/return_outwards_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }


  public function view_other_deductions()
  {
  	$report_id = 5;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Transfer") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;


    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-other-deductions';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Other Deductions View';
     $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/other_deductions_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }

  public function view_other_additions()
  {
  	$report_id = 3;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }

		$table = 'v_product_stock';
		$where = '(v_product_stock.transactionClassification = "Product Addition") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;


    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-other-additions';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Other Additions';
     $v_data['report_id'] = 3;
    $data['content'] = $this->load->view('financials/other/other_purchases_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }


  public function view_current_stock()
  {

  	$report_id = 6;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (transactionDate >= \''.$date_from.'\' AND transactionDate <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND transactionDate = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }
    // var_dump($search_invoice_add);die();
    $table = 'v_product_stock';
    $where = '(v_product_stock.transactionClassification = "Product Opening Stock" OR  v_product_stock.transactionClassification = "Supplier Purchases" OR  v_product_stock.transactionClassification = "Product Addition" OR v_product_stock.transactionClassification = "Supplier Credit Note" OR v_product_stock.transactionClassification = "Product Deductions" OR v_product_stock.transactionClassification = "Drug Sales" OR v_product_stock.transactionClassification = "Drug Transfer") AND ( category_id = 2 OR category_id = 3)  '.$search_invoice_add;



    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-current-stock';
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
    $v_data['query'] = $this->company_financial_model->get_all_income_statement_views($table, $where, $config["per_page"], $page,'v_product_stock.transactionDate','ASC');
    $data['title'] = $v_data['title'] = 'Current Stock';
     $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/current_stock_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }

  public function search_stock_report($report_id)
  {

  	$date_from = $year_from = $this->input->post('date_from'.$report_id);
	$date_to = $this->input->post('date_to'.$report_id);
	$redirect_url = $this->input->post('redirect_url');


	if(!empty($date_from) && !empty($date_to))
	{

	// $date_to = $year_to;
	$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
	}


	else if(!empty($date_from))
	{
	// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';


	// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
	// $date_to = $year_from;
	$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
	}

	else if(!empty($date_to))
	{
	$date_to = $date_to;
	// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
	// $search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
	$date_to = $year_from;
	$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
	}

	else
	{
	$date_from = date('Y-m').'-01';
	$date_to = date('Y-m-d');
	$search_title = 'Income Statement of '.date('Y').' ';
	}


	$this->session->set_userdata('date_from_stock'.$report_id,$date_from);
	$this->session->set_userdata('date_to_stock'.$report_id,$date_to);
	$this->session->set_userdata('stock_title_search'.$report_id,$search_title);
	$this->session->set_userdata('stock_report_id#'.$report_id,$report_id);

	redirect($redirect_url);


  }
  public function close_stock_search($report_id,$category_id = null)
  {

  	$this->session->unset_userdata('date_from_stock'.$report_id);
	$this->session->unset_userdata('date_to_stock'.$report_id);
	$this->session->unset_userdata('stock_title_search'.$report_id);
	$this->session->unset_userdata('stock_report_id#'.$report_id);

	if($report_id == 1)
	{
		$redirect_url = 'view-closing-stock';
	}
	else if($report_id == 2)
	{
		$redirect_url = 'view-purchases';
	}
	else if($report_id == 3)
	{
		$redirect_url = 'view-other-additions';

	}
	else if($report_id == 4)
	{
		$redirect_url = 'view-return-outwards';
	}
	else if($report_id == 5)
	{
		$redirect_url = 'view-other-deductions';
	}
	else if($report_id == 6)
	{
		$redirect_url = 'view-current-stock';
	}
	else if($report_id == 8)
	{
		$redirect_url = 'view-expenses-stock';
	}
	else if($report_id == 7)
	{
		if($category_id)
		{
			$redirect_url = 'view-non-pharm-purchases/'.$category_id;
		}
		else
		{
			$redirect_url = 'view-current-stock';
		}
		
	}
	else 
	{
		$redirect_url = 'view-current-stock';
	}

	redirect($redirect_url);

  }





  // non pharm purchases


  public function view_non_pharm_purchases($category_id)
  {
  	$report_id = 7;
       $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }

   	$table = 'product_deductions_stock,product,store,category';
    $where = 'product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0 AND store.store_id = product_deductions_stock.store_id AND product.category_id = category.category_id AND (product.category_id = '.$category_id.') AND store.store_parent > 0  '.$search_invoice_add;


    $this->db->where('category_id',$category_id);
    $query_cat = $this->db->get('category');

    $row = $query_cat->row();
    $category_name = $row->category_name;
    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 3;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-non-pharm-purchases/'.$category_id;
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
    $v_data['query'] = $this->company_financial_model->get_all_stock_expense_statement_views($table, $where, $config["per_page"], $page,'product_deductions_stock_date','ASC');
    $data['title'] = $v_data['title'] = $category_name.' Expenses';
    $v_data['report_id'] = $report_id;
    $v_data['category_id'] = $category_id;
    // var_dump($v_data);die();
    $data['content'] = $this->load->view('financials/other/non_pharm_purchases_view', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }
  public function export_report($report_id,$category_id=null)
  {
  	// var_dump($report_id);die();
  	if($report_id == 2 OR $report_id == 3 OR $report_id == 4 OR $report_id == 5 OR $report_id == 7)
  	{
  		// var_dump($report_id);die();
  		$this->company_financial_model->export_stock_report($report_id,$category_id);
  	}
  	else if($report_id == 6)
  	{

  		$this->company_financial_model->export_current_stock_report($report_id,$category_id);
  	}

  	
  }



  public function view_expenses_stock()
  {

  	$report_id = 8;
    $search_status = $this->session->userdata('income_statement_search');
    $search_payments_add = '';
    $search_invoice_add = '';
    if($search_status == 1)
    {
        $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
		if(!empty($stock_search) AND $stock_search == $report_id)
		{
	    	// $exploded = explode('#', $stock_search);
	    	 
	    	$export_report_id = $report_id;
			
	      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
			$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
			// var_dump($date_from);die();
			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
			}
	      
	  }
      else
      {
      	$date_from = $this->session->userdata('date_from_income_statement');
		$date_to = $this->session->userdata('date_to_income_statement');

		if(!empty($date_from) AND !empty($date_to))
		{
			$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
		}
		else if(!empty($date_from))
		{
			$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
		}
		else if(!empty($date_to))
		{
			$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
		}
      }
      
    }
    else
    {
    	// $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
    	 $stock_search  = $this->session->userdata('stock_report_id#'.$report_id);
        // var_dump($stock_search);die();
       
			if(!empty($stock_search) AND $stock_search == $report_id)
			{
		    	// $exploded = explode('#', $stock_search);
		    	 
		    	$export_report_id = $report_id;
				
		      	$date_from = $this->session->userdata('date_from_stock'.$export_report_id);
				$date_to = $this->session->userdata('date_to_stock'.$export_report_id);
				// var_dump($date_from);die();
				if(!empty($date_from) AND !empty($date_to))
				{
					$search_invoice_add =  ' AND (product_deductions_stock_date >= \''.$date_from.'\' AND product_deductions_stock_date <= \''.$date_to.'\') ';
				}
				else if(!empty($date_from))
				{
					$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_from.'\'';
				}
				else if(!empty($date_to))
				{
					$search_invoice_add = ' AND product_deductions_stock_date = \''.$date_to.'\'';
				}
		      
		  }
		else
		{
			$search_invoice_add = '';
		}

      

    }
    // var_dump($search_invoice_add);die();
    $table = 'product_deductions_stock,product,store,category';
    $where = 'product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0 AND store.store_id = product_deductions_stock.store_id AND product.category_id = category.category_id AND (product.category_id < 2 OR product.category_id > 2) AND store.store_parent > 0  '.$search_invoice_add;



    // $table = 'visit_charge,visit,patients,service_charge,visit_type';
    $segment = 2;
    //pagination
    $this->load->library('pagination');
    $config['base_url'] = site_url().'view-current-stock';
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
    $v_data['query'] = $this->company_financial_model->get_all_stock_expense_statement_views($table, $where, $config["per_page"], $page,'product_deductions_stock_date','ASC');
    $data['title'] = $v_data['title'] = 'Stock Expenses';
     $v_data['report_id'] = $report_id;
    $data['content'] = $this->load->view('financials/other/stock_expense', $v_data, TRUE);

    $this->load->view('admin/templates/general_page', $data);

  }




  	public function account_balances()
	{
		$order = 'account.account_type_id';
		$order_method ='ASC';
		$where = 'account_deleted  = 0 AND account_type.account_type_id = account.account_type_id';
		$table = 'account,account_type';

		$search = $this->session->userdata('search_petty_cash1');
		$where .= $search;
		
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/charts-of-accounts';
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
		$query = $this->company_financial_model->get_all_cash_accounts($table, $where, $config["per_page"], $page, $order, $order_method);
		
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
		$data['content'] = $this->load->view('accounts/all_accounts', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function deactivate_account($account_id)
	{
		if($this->company_financial_model->deactivate_account($account_id))
		{
			$this->session->set_userdata('success_message', 'Account deactivated successfully');
		}
		else
		{
			$this->session->set_userdata('error_message', 'Account deactivation failed');
		}
		
		redirect('accounting/charts-of-accounts');
	}
	public function activate_account($account_id)
	{
		if($this->company_financial_model->activate_account($account_id))
		{
			$this->session->set_userdata('success_message', 'Account activated successfully');
		}
		else
		{
			$this->session->set_userdata('error_message', 'Account activation failed');
		}
		
		redirect('accounting/charts-of-accounts');
	}
	public function edit_account($account_id)
	{
		//form validation
		$this->form_validation->set_rules('account_name', 'Name','required|xss_clean');
		$this->form_validation->set_rules('account_balance', 'Opening Balance','required|xss_clean');
		$this->form_validation->set_rules('account_type_id', 'Account type','required|xss_clean');
		$this->form_validation->set_rules('start_date', 'Start Date','required|xss_clean');

		
		if ($this->form_validation->run())
		{
			//update order
			if($this->company_financial_model->update_account($account_id))
			{
				$this->session->set_userdata('success_message', 'Account updated successfully');
				redirect('accounting/charts-of-accounts');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update account. Please try again');
			}
		}
		
		//open the add new order
		$data['title'] = $v_data['title']= 'Edit Account';
		$v_data['types'] = $this->company_financial_model->get_type();
		$v_data['parent_accounts'] = $this->company_financial_model->get_parent_accounts();
		
		//select the order from the database
		$query = $this->company_financial_model->get_account($account_id);
		$v_data['query'] = $query;
		$data['content'] = $this->load->view('accounts/edit_account', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function add_account()
	{
		//form validation
		$this->form_validation->set_rules('account_name', 'Name','required|xss_clean');
		$this->form_validation->set_rules('account_balance', 'Opening Balance','required|xss_clean');
		$this->form_validation->set_rules('account_type_id', 'Account_type','required|xss_clean');
		$this->form_validation->set_rules('start_date', 'Start Date','required|xss_clean');
		

		if ($this->form_validation->run())
		{
			//update order
			if($this->company_financial_model->add_account())
			{
				$this->session->set_userdata('success_message', 'Account updated successfully');
				redirect('accounting/charts-of-accounts');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update account. Please try again');
			}
		}
		
		//open the add new order
		$v_data['types'] = $this->company_financial_model->get_type();
		$v_data['parent_accounts'] = $this->company_financial_model->get_parent_accounts();
		$data['title'] = $v_data['title']= 'Add Account';
		$data['content'] = $this->load->view('accounts/add_account', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	public function search_accounts()
	{
		$account_name = $this->input->post('account_name');
		
		if(!empty($account_name))
		{
			$this->session->set_userdata('search_petty_cash1', ' AND account.account_name LIKE \'%'.$account_name.'%\'');
		}
		
		redirect('accounting/charts-of-accounts');
	}
	
	public function close_search_petty_cash()
	{
		$this->session->unset_userdata('search_petty_cash1');
		
		redirect('accounting/charts-of-accounts');
	}

	public function delete_account($account_id)
	{
		$update['account_deleted'] = 1;
		$this->db->where('account_id',$account_id);
		if($this->db->update('account',$update))
		{

		}
		redirect('accounting/charts-of-accounts');
	}

	public function salary()
	{

		$where = 'payroll_id > 0';

		$search_status = $this->session->userdata('income_statement_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_income_statement');
			$date_to = $this->session->userdata('date_to_income_statement');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_invoice_add =  ' AND (payroll_created_for >= \''.$date_from.'\' AND payroll_created_for <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_invoice_add = ' AND payroll_created_for = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';

		}

		$where .= $search_invoice_add;
		//retrieve all users

		$table = 'v_payroll';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'company-financials/salary';
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
        // $v_data['department_id'] = $department_id;
        $v_data["links"] = $this->pagination->create_links();
		$v_data['query'] = $this->company_financial_model->get_payroll_list($table, $where, $config["per_page"], $page,'v_payroll.payroll_created_for','ASC');
		$data['title'] = $v_data['title'] = 'Payroll List';
		$data['content'] = $this->load->view('financials/financials/payroll_list', $v_data, TRUE);

		$this->load->view('admin/templates/general_page', $data);
	}

	public function export_salary()
	{
		$this->company_financial_model->export_salary();
	}


	

}
?>
