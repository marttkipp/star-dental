<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Transfer extends admin
{
	function __construct()
	{
		parent:: __construct();

    $this->load->model('finance/purchases_model');
    $this->load->model('finance/transfer_model');
    $this->load->model('real_estate_administration/property_model');
	}



  public function write_cheque()
	{
		//form validation
		$this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
		$this->form_validation->set_rules('account_to_id', 'Account To','required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount','required|xss_clean');
		$this->form_validation->set_rules('description', 'Description','required|xss_clean');
		$this->form_validation->set_rules('reference_number', 'Reference Number','required|xss_clean');
		$this->form_validation->set_rules('transfer_date', 'Transfer Date','required|xss_clean');

		if ($this->form_validation->run())
		{
			//update order
			if($this->transfer_model->transfer_funds())
			{
				$this->session->set_userdata('success_message', 'Cheque successfully writted to account');


				redirect('accounting/accounts-transfer');
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

		$where = 'finance_transfer_status = 1 ';


    $search_transfers = $this->session->userdata('search_transfers');
    if(!empty($search_transfers))
    {
      $where .= $search_transfers;
    }
		$table = 'finance_transfer';


		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'accounting/accounts-transfer';
		$config['total_rows'] = $this->transfer_model->count_items($table, $where);
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
		$query = $this->transfer_model->get_account_transfer_transactions($table, $where, $config["per_page"], $page, $order='finance_transfer.transaction_date', $order_method='ASC');
		// var_dump($query); die();

		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title']= 'Transfer Cheque';

		$data['content'] = $this->load->view('finance/transfer/write_cheques', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

  public function get_list_type($type)
  {
        $query = $this->purchases_model->get_child_accounts("Bank",$type);
        echo '<option value="0">--Select an option --</option>';
        $options = $query;
        foreach($options-> result() AS $key_old) {
            if($key_old->account_id != $type)
            {
                echo '<option value="'.$key_old->account_id.'">'.$key_old->account_name.'</option>';
            }

        }
    }
    public function search_transfers()
    {
      $visit_date_from = $this->input->post('date_from');
      $reference_number = $this->input->post('reference_number');
      $visit_date_to = $this->input->post('date_to');

      $search_title = '';

      if(!empty($reference_number))
      {
        $search_title .= $tenant_name.' ';
        $reference_number = ' AND finance_transfer.reference_number LIKE \'%'.$transaction_number.'%\'';


      }
      else
      {
        $transaction_number = '';
        $search_title .= '';
      }

       if(!empty($visit_date_from) && !empty($visit_date_to))
       {
         $visit_date = ' AND finance_transfer.transaction_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
         $search_title .= 'Payments from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else if(!empty($visit_date_from))
       {
         $visit_date = ' AND finance_transfer.transaction_date = \''.$visit_date_from.'\'';
         $search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_from)).' ';
       }

       else if(!empty($visit_date_to))
       {
         $visit_date = ' AND finance_transfer.transaction_date = \''.$visit_date_to.'\'';
         $search_title .= 'Payments of '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else
       {
         $visit_date = '';
       }


      $search = $visit_date.$transaction_number;

      $this->session->set_userdata('search_transfers', $search);

      redirect('accounting/accounts-transfer');
    }

    public function close_search()
  	{
  		$this->session->unset_userdata('search_transfers');
  		redirect('accounting/accounts-transfer');
  	}
}
?>
