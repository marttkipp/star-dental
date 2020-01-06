<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Transfer extends admin
{
	function __construct()
	{
		parent:: __construct();

    $this->load->model('finance/purchases_model');
    $this->load->model('finance/transfer_model');
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
			// var_dump($_POST);die();
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
		$v_data['accounts'] = $this->purchases_model->get_transacting_accounts("Bank");

		$where = 'finance_transfer_status = 1 AND finance_transfer_deleted = 0';


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
		$query = $this->transfer_model->get_account_transfer_transactions($table, $where, $config["per_page"], $page, $order='finance_transfer.transaction_date', $order_method='DESC');
		// var_dump($query); die();

		$data['title'] = 'Accounts';
		$v_data['title'] = $data['title'];

		$v_data['query'] = $query;
		$v_data['page'] = $page;
    // var_dump($v_data['accounts']);die();

		$data['title'] = $v_data['title']= 'Transfer Cheque';

		$data['content'] = $this->load->view('finance/transfer/write_cheques', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

  public function get_list_type($type)
  {
        $query = $this->purchases_model->get_transacting_accounts("Bank",$type);
        echo '<option value="">--Select an option --</option>';
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

  	public function reverse_transfer($finance_transfer_id)
  	{
  		// get the transfer from account

  		$this->db->where('finance_transfer_id',$finance_transfer_id);
  		$query = $this->db->get('finance_transfer');
  		if($query->num_rows() == 1)
  		{
  			$items = $query->row();

  			$finance_transfer_id = $items->finance_transfer_id;
  			$finance_transfer_amount = $items->finance_transfer_amount;
  			$reference_number = $items->reference_number;
  			$account_from_id = $items->account_from_id;
  			$created = $items->created;
  			$transaction_date = $items->transaction_date;
  			$created_by = $items->created_by;
  			$finance_transfer_status = $items->finance_transfer_status;
  			$remarks = $items->remarks;

  			$changed_item['finance_transfer_amount'] = -$finance_transfer_amount;
  			$changed_item['reference_number'] = $reference_number;
  			$changed_item['account_from_id'] = $account_from_id;
  			$changed_item['created'] = $created;
  			$changed_item['transaction_date'] = date('Y-m-d');
  			$changed_item['created_by'] = $this->session->userdata('personnel_id');
  			$changed_item['finance_transfer_status'] = $finance_transfer_status;
  			$changed_item['remarks'] = 'Reversal of money';

  			$this->db->insert('finance_transfer',$changed_item);
  			$linked_id = $this->db->insert_id();


  			$this->db->where('finance_transfer_id',$finance_transfer_id);
  			$query2 = $this->db->get('finance_transfered');
  			if($query2->num_rows() == 1)
	  		{
	  			$items2 = $query2->row();
	  			$account_to_id = $items2->account_to_id;
	  			$transaction_date = $items2->transaction_date;
	  			$created = $items2->created;
	  			$last_modified = $items2->last_modified;
	  			$remarks = $items2->remarks;
	  			$created_by = $items2->created_by;
	  			$finance_transfered_amount = -$items2->finance_transfered_amount;


	  			$changed_item_thing['finance_transfer_amount'] = $finance_transfered_amount;
	  			$changed_item_thing['account_to_id'] = $account_to_id;
	  			$changed_item_thing['created'] = date('Y-m-d H:i:s');
	  			$changed_item_thing['transaction_date'] = date('Y-m-d');
	  			$changed_item_thing['created_by'] = $this->session->userdata('personnel_id');
	  			$changed_item_thing['finance_transfer_id'] = $linked_id;
	  			$changed_item_thing['remarks'] = 'Reversal of money';

	  			$this->db->insert('finance_transfer',$changed_item_thing);



	  		}
  		}
  	}
    public function transfer_delete_record($finance_transfer_id)
    {
        $array['finance_transfer_deleted'] = 1;
        $array['finance_transfer_status'] = 0;
        $array['deleted_by'] = $this->session->userdata('personnel_id');
        $this->db->where('finance_transfer_id',$finance_transfer_id);
        $this->db->update('finance_transfer',$array);

        $this->session->set_userdata('success_message','You have successfully removed the transfer');
        redirect('accounting/accounts-transfer');

    }


    public function account_transfers()
    {
      $this->db->from('account_payments');
      $this->db->select('*');
      $this->db->where('account_to_type = 1 AND account_payment_deleted = 0');

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
          $created = $value->created;
          $created_by = $value->created_by;
          $receipt_number = $value->receipt_number;
          $payment_to = $value->payment_to;
          $payment_date = $value->payment_date;

          $exploded = explode('-', $payment_date);

          $year = $exploded[0];
          $month = $exploded[1];

          $document_number = '';//$this->transfer_model->create_credit_payment_number();


          $invoice['finance_transfer_amount'] = $amount_paid;
          $invoice['finance_transfer_status'] = 1;
          $invoice['created'] = $created;
          $invoice['created_by'] = $created_by;
          $invoice['transaction_date'] = $payment_date;
          $invoice['reference_number'] = $receipt_number;
          // $invoice['document_number'] = $document_number;
          $invoice['account_from_id'] = $account_from_id;
          $invoice['remarks'] = $account_payment_description;



          $this->db->insert('finance_transfer',$invoice);
          $finance_transfer_id = $this->db->insert_id();


          $invoice_item['finance_transfer_id'] = $finance_transfer_id;
          $invoice_item['remarks'] = $account_payment_description;
          $invoice_item['finance_transfered_amount'] = $amount_paid;
          $invoice_item['transaction_date'] = $payment_date;
          $invoice_item['created_by'] = $created_by;
          $invoice_item['account_to_id'] = $account_to_id;
          // $invoice_item['document_number'] = $document_number;
          $this->db->insert('finance_transfered',$invoice_item);
        }
      }
    }


    public function edit_transfer_record($finance_transfer_id)
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
        // var_dump($_POST);die();
        //update order
        if($this->transfer_model->transfer_funds($finance_transfer_id))
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

       $data['title'] = 'Edit Finance Transfer';
      $v_data['finance_transfer_id'] = $finance_transfer_id;
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('transfer/edit_transfer', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);

    }
}
?>
