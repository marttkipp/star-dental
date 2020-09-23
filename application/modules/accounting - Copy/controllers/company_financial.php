<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounts/controllers/accounts.php";

class Company_financial extends accounts 
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('company_financial_model');
	}

	function profit_and_loss()
	{

		$v_data['module'] = 1;
		$data['title'] = $v_data['title'] = ' Statement';
		$data['content'] = $this->load->view('reports/profit_and_loss', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	function balance_sheet()
	{

		$v_data['module'] = 1;
		$data['title'] = $v_data['title'] = ' Balance Sheet';
		$data['content'] = $this->load->view('reports/balance_sheet', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function search_balance_sheet()
	{
		$date_from = $this->input->post('date_from');
		$date_to = $this->input->post('date_to');
		$redirect_url = $this->input->post('redirect_url');


		if(!empty($date_from) && !empty($date_to))
		{
			$date_from = $date_from;
			$date_to = $date_to;
			$search_title = 'REPORT FOR PERIOD '.date('jS M Y', strtotime($date_from)).' to '.date('jS M Y', strtotime($date_to)).' ';
		}

		
		else if(!empty($date_from))
		{
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_from.'\'';

			$date_from = $date_from;
			$search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_from)).' ';
		}
		
		else if(!empty($date_to))
		{
			$date_to = $date_to;
			// $where .= ' AND creditor_account.creditor_account_date = \''.$date_to.'\'';
			$search_title = 'REPORT FOR '.date('jS M Y', strtotime($date_to)).' ';
		}
		
		else
		{
			$date_from = '';
			$date_to = '';
			$search_title = 'Statement for the month of '.date('M Y').' ';
		}
		

		$this->session->set_userdata('date_from_balance_sheet',$date_from);
		$this->session->set_userdata('date_to_balance_sheet',$date_to);
		$this->session->set_userdata('balance_sheet_title_search',$search_title);
		$this->session->set_userdata('balance_sheet_search',1);

		redirect($redirect_url);
		
	}

	public function close_balance_sheet_search()
	{
		$this->session->unset_userdata('date_from_balance_sheet');
		$this->session->unset_userdata('date_to_balance_sheet');
		$this->session->unset_userdata('balance_sheet_title_search');
		$this->session->unset_userdata('balance_sheet_search');
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);

	}

	public function search_visit_transactions($visit_type_id)
	{

		$visit_date_from = '';
		$visit_date_to = '';
		$search_status = $this->session->userdata('balance_sheet_search');
		if($search_status == 1)
		{
			$visit_date_from = $this->session->userdata('date_from_balance_sheet');
			$visit_date_to = $this->session->userdata('date_to_balance_sheet');
			
		}
		// var_dump($visit_date_to); die();
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
	
		if(!empty($visit_date_from) && !empty($visit_date_to))
		{
			$visit_payments = ' AND payments.payment_created BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$visit_invoices = ' AND visit.visit_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$date_from = $visit_date_from;
			$date_to = $visit_date_to;
			$cash_out = ' AND account_payments.payment_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
			$search_title .= 'Visit date from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
		}
		
		else if(!empty($visit_date_from))
		{
			$visit_payments = ' AND payments.payment_created = \''.$visit_date_from.'\'';
			$visit_invoices = ' AND visit.visit_date = \''.$visit_date_from.'\'';
			$cash_out = ' AND account_payments.payment_date = \''.$visit_date_from.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_from)).' ';
			$date_from = $visit_date_from;
			$date_to = '';
		}
		
		else if(!empty($visit_date_to))
		{
			$visit_payments = ' AND payments.payment_created = \''.$visit_date_to.'\'';
			$visit_date = ' AND visit.visit_date = \''.$visit_date_to.'\'';
			$cash_out = ' AND account_payments.payment_date = \''.$visit_date_to.'\'';
			$search_title .= 'Visit date of '.date('jS M Y', strtotime($visit_date_to)).' ';
			$date_from = '';
			$date_to = $visit_date_to;
		}
		
		else
		{
			$visit_invoices = '';
			$visit_payments = '';
			$cash_out = '';
			$date_from = '';
			$date_to = '';
		}
		
		$search = $visit_type_id.$visit_invoices;
		// $visit_search = $this->session->userdata('all_transactions_search');		
		// var_dump($search); die();
		$this->session->set_userdata('cash_report_search', $search);
		$this->session->set_userdata('debtors_search_query', $search);
		$this->session->set_userdata('cash_out_search', $cash_out);
		$this->session->set_userdata('cash_report_date_from', $date_from);
		$this->session->set_userdata('cash_report_date_to', $date_to);
		$this->session->set_userdata('search_title', $search_title);
		$this->session->set_userdata('visit_invoices', $visit_invoices);
		$this->session->set_userdata('visit_payments', $visit_payments);
		
		// redirect('hospital-reports/cash-report');
		redirect('hospital-reports/all-transactions');
	}
}
?>