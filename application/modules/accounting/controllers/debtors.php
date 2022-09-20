<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounts/controllers/accounts.php";

class Debtors extends accounts 
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('creditors_model');
		$this->load->model('debtors_model');
		$this->load->model('petty_cash_model');
	}
	
	public function index()
	{
		$branch_code = $this->session->userdata('search_branch_code');
		
		if(empty($branch_code))
		{
			$branch_code = $this->session->userdata('branch_code');
		}
		
		$this->db->where('visit_type_id > 0');
		$query = $this->db->get('visit_type');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$visit_type_name = $row->visit_type_name;
		}
		
		else
		{
			$visit_type_name = '';
		}
		$where = 'visit_type.visit_type_id >0 ';
		$search = $this->session->userdata('search_hospital_debtors');		
		$where .= $search;		
		$table = 'visit_type';
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/debtors-statements';
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
		$v_data['query'] = $this->debtors_model->get_all_debtors($table, $where, $config["per_page"], $page);
		$data['title'] = $v_data['title'] = 'Debtors';
		$data['content'] = $this->load->view('debtors/all_debtors', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);
	}
	public function debtor_statement($debtor_id)
	{

		$where = 'visit_type_id = '.$debtor_id;
		$table = 'visit_type';
		
		
		// echo $where;die();
		// $v_data['balance_brought_forward'] = $this->creditors_model->calculate_balance_brought_forward($date_from,$creditor_id);
		// $creditor = $this->creditors_model->get_creditor($creditor_id);
		// $row = $creditor->row();
		// $creditor_name = $row->creditor_name;
		// $opening_balance = $row->opening_balance;
		// $debit_id = $row->debit_id;
		// // var_dump($opening_balance); die();
		// $v_data['module'] = 1;
		// $v_data['creditor_name'] = $creditor_name;
		$v_data['accounts'] = $this->petty_cash_model->get_accounts();
		$v_data['accounts'] = $this->petty_cash_model->get_expense_accounts();
		// $v_data['creditor_id'] = $creditor_id;
		// $v_data['date_from'] = $date_from;
		// $v_data['date_to'] = $date_to;
		// $v_data['opening_balance'] = $opening_balance;
		// $v_data['debit_id'] = $debit_id;
		// $v_data['query'] = $this->creditors_model->get_creditor_account($where, $table);
		$v_data['title'] = 'Debtor ';
		$v_data['debtor_id'] = $debtor_id;
		$data['title'] = 'Statement';
		$data['content'] = $this->load->view('debtors/statement', $v_data, TRUE);
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function export_debtor_statement($visit_type_id,$start_date,$end_date)
	{
		$this->debtors_model->export_debtor_statement($visit_type_id,$start_date,$end_date);
	}

	public function update_opening_balance($visit_type_id)
	{

		// var_dump($_POST); die();
		$this->form_validation->set_rules('start_date', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('opening_balance', 'Amount', 'trim|required|xss_clean');
		
		if ($this->form_validation->run())
		{
			if($this->debtors_model->update_debtor_account($visit_type_id))
			{
				$this->session->set_userdata('success_message', 'Updated provider account successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Unable to update. Please try again');
			}
		}
		
		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);
	}

	public function search_hospital_visit_type()
	{
		$visit_type_name = $this->input->post('visit_type_name');
		
		if(!empty($visit_type_name))
		{
			$this->session->set_userdata('search_hospital_debtors', ' AND visit_type.visit_type_name LIKE \'%'.$visit_type_name.'%\'');
		}
		
		redirect('accounting/debtors-statement');
	}
	
	public function close_search_hospital_debtors()
	{
		$this->session->unset_userdata('search_hospital_debtors');
		
		redirect('accounting/debtors-statement');
	}


	public function create_billing_views()
	{

		for ($i=1; $i <= 6; $i++) { 
			# code...
			$invoice = '';
			$start_date = '2018-03-01';
			$first_date = date('Y-m').'-01';
			if($i == 1)
			{
				// 30 days
				$last_date = date('Y-m-t', strtotime($first_date));
				$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$first_date.'" AND visit.visit_date <= "'.$last_date.'" ';

				$invoice_view_name ='v_visit_type_charges_current';
				$bill_view_name = 'v_visit_type_bills_current';
				$payment_view_name = 'v_visit_type_payment_current';
				$waiver_view_name = 'v_visit_type_waiver_current';
				$rejects_view_name = 'v_visit_type_rejects_current';
			
			}
			else if($i == 2)
			{
				// 30 days
				$sixty_months = date('Y-m-01');
				$sixty_months = date('Y-m-d',strtotime ( '-1 month' , strtotime ( $sixty_months ) ) );
				// var_dump($sixty_months); die();
				$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
				$last_date = date('Y-m-t', strtotime($newdate));

				// $last_date = date('Y-m-d', strtotime('-2 months'));
				// var_dump($last_date); die();
				$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
				$invoice_view_name ='v_visit_type_charges_thirty';
				$bill_view_name = 'v_visit_type_bills_thirty';
				$payment_view_name = 'v_visit_type_payment_thirty';
				$waiver_view_name = 'v_visit_type_waiver_thirty';
				$rejects_view_name = 'v_visit_type_rejects_thirty';

			}
			else if($i == 3)
			{
				$sixty_months = date('Y-m-01');
				$sixty_months = date('Y-m-d',strtotime ( '-2 month' , strtotime ( $sixty_months ) ) );
				$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
				$last_date = date('Y-m-t', strtotime($newdate));


				$invoice = ' AND visit.visit_date >= "'.$start_date.'"AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
				$invoice_view_name ='v_visit_type_charges_sixty';
				$bill_view_name = 'v_visit_type_bills_sixty';
				$payment_view_name = 'v_visit_type_payment_sixty';
				$waiver_view_name = 'v_visit_type_waiver_sixty';
				$rejects_view_name = 'v_visit_type_rejects_sixty';
			}

			else if($i == 4)
			{
				// over 90 days

				$sixty_months = date('Y-m-01');
				$sixty_months = date('Y-m-d',strtotime ( '-3 month' , strtotime ( $sixty_months ) ) );
				$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
				$last_date = date('Y-m-t', strtotime($newdate));

				$invoice = ' AND visit.visit_date >= "'.$start_date.'"AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';
				$invoice_view_name ='v_visit_type_charges_ninety';
				$bill_view_name = 'v_visit_type_bills_ninety';
				$payment_view_name = 'v_visit_type_payment_ninety';
				$waiver_view_name = 'v_visit_type_waiver_ninety';
				$rejects_view_name = 'v_visit_type_rejects_ninety';
			}
			else if($i == 5)
			{
				// over 120 days

				$sixty_months = date('Y-m-01');
				$sixty_months = date('Y-m-d',strtotime ( '-4 month' , strtotime ( $sixty_months ) ) );
				// var_dump($sixty_months); die();
				$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
				$last_date = date('Y-m-t', strtotime($newdate));

				$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date >= "'.$sixty_months.'" AND visit.visit_date <= "'.$last_date.'" ';

				$invoice_view_name ='v_visit_type_charges_one_twenty';
				$bill_view_name = 'v_visit_type_bills_one_twenty';
				$payment_view_name = 'v_visit_type_payment_one_twenty';
				$waiver_view_name = 'v_visit_type_waiver_one_twenty';
				$rejects_view_name = 'v_visit_type_rejects_one_twenty';
			}
			else if($i == 6)
			{
				// over 120 days

				$sixty_months = date('Y-m-01');
				$sixty_months = date('Y-m-d',strtotime ( '-5 month' , strtotime ( $sixty_months ) ) );
				$newdate = date('Y-m-t',strtotime ( '+0 month' , strtotime ( $sixty_months ) ) );
				$last_date = date('Y-m-t', strtotime($newdate));


				$invoice = ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date <= "'.$last_date.'" ';
				$invoice_view_name ='v_visit_type_charges_over';
				$bill_view_name = 'v_visit_type_bills_over';
				$payment_view_name = 'v_visit_type_payment_over';
				$waiver_view_name = 'v_visit_type_waiver_over';
				$rejects_view_name = 'v_visit_type_rejects_over';
			}

			$invoice_query = 'CREATE VIEW '.$invoice_view_name.' AS SELECT SUM(visit_charge_amount*visit_charge_units) AS total_invoice,visit.visit_type 
							FROM (`visit_charge`, `visit`) 
							WHERE `visit_charge`.`visit_charge_delete` = 0 
							AND (visit.parent_visit IS NULL OR visit.parent_visit = 0) 
							AND visit.visit_id = visit_charge.visit_id 
							AND visit.visit_delete = 0 
							AND visit_charge.charged = 1 
							'.$invoice.'
							GROUP BY visit.visit_type
							';

			$this->db->query($invoice_query);




			$bills_query = 'CREATE VIEW '.$bill_view_name.' AS SELECT SUM(visit_bill_amount) AS total_invoice,visit.visit_type FROM (`visit_bill`, `visit`) 
							WHERE `visit`.`visit_id` = visit_bill.visit_parent 
							AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) 
							AND visit.visit_delete = 0  
							'.$invoice.'
							GROUP BY visit.visit_type
							';

			$this->db->query($bills_query);


			$payment_query = 'CREATE VIEW '.$payment_view_name.' AS SELECT SUM(amount_paid) AS total_payments,visit.visit_type 
							FROM (`payments`, `visit`) 
							WHERE `cancel` = 0 
							AND visit.visit_id = payments.visit_id 
							AND visit.visit_delete = 0 
							AND payments.payment_type = 1   
							'.$invoice.'
							GROUP BY visit.visit_type
							';

			$this->db->query($payment_query);

			$waiver_query = 'CREATE VIEW '.$waiver_view_name.' AS SELECT SUM(amount_paid) AS total_payments,visit.visit_type 
							FROM (`payments`, `visit`) 
							WHERE `cancel` = 0 
							AND visit.visit_id = payments.visit_id 
							AND visit.visit_delete = 0 
							AND payments.payment_type = 2   
							'.$invoice.'
							GROUP BY visit.visit_type
							';

			$this->db->query($waiver_query);


			$rejects_query = 'CREATE VIEW '.$rejects_view_name.' AS SELECT SUM(rejected_amount) AS total_invoice,visit.visit_type 
							FROM (`visit`) 
							WHERE (visit.parent_visit = 0 OR visit.parent_visit IS NULL) 
							AND visit.visit_delete = 0     
							'.$invoice.'
							GROUP BY visit.visit_type';
			$this->db->query($rejects_query);


			
		}
	}
}
?>