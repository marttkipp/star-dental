<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/admin/controllers/admin.php";

class Transfer extends admin
{
	function __construct()
	{
		parent:: __construct();

	    $this->load->model('finance/purchases_model');
	    $this->load->model('finance/transfer_model');
      $this->load->model('accounts/accounts_model');
	    
    	if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
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

		$data['title'] = $v_data['title']= 'Transfer Cheque';

		$data['content'] = $this->load->view('finance/transfer/write_cheques', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

  public function get_account_list_type($type)
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
      $reference_number = $this->input->post('transaction_number');
      $visit_date_to = $this->input->post('date_to');

      $search_title = '';

      if(!empty($reference_number))
      {
        $search_title .= $tenant_name.' ';
        $transaction_number = ' AND finance_transfer.reference_number LIKE \'%'.$reference_number.'%\'';


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
  	public function delete_transfer_payment($finance_transfer_id)
  	{
  // 		$personnel_id = $this->session->userdata('personnel_id');
		// $this->db->where('finance_transfer_id = '.$finance_transfer_id);
		// $query = $this->db->get('finance_transfer');
		// $item = $query->row();

		// $deleted_by = $item->deleted_by;
		// $finance_transfer_deleted = $item->finance_transfer_deleted;
		// // var_dump($item);die();
		// if($deleted_by == $personnel_id AND $finance_transfer_deleted > 0)
		// {
		// 	if($finance_transfer_deleted == 0)
		// 	{
		// 		$deleted_status = 0;
		// 	}
		// 	else
		// 	{
		// 		$deleted_status = $finance_transfer_deleted-1;
		// 	}			

		// 	if($deleted_status == 1)
		// 	{
		// 		$status = ' reverted deletion';
		// 	}
		// 	else
		// 	{
		// 		$status = ' confirmed revertion';
		// 	}


			$update_array['deleted_by'] = $this->session->userdata('personnel_id');
			$update_array['finance_transfer_deleted'] = 1;
			$this->db->where('finance_transfer_id = '.$finance_transfer_id);
			if($this->db->update('finance_transfer',$update_array))
			{
				$this->session->set_userdata('success_message', 'You have reversed the transaction status successfully ');
			}
			else
			{
				$this->session->set_userdata('error_message', 'Sorry could not perform the action. Please try again');
			}
		// }
		// else
		// {

		// 	$deleted_status = $finance_transfer_deleted +1;

		// 	if($deleted_status == 1)
		// 	{
		// 		$status = ' request to delete';
		// 	}
		// 	else
		// 	{
		// 		$status = ' confirmed delete';
		// 	}


		// 	$update_array['deleted_by'] = $personnel_id;
		// 	$update_array['date_deleted'] = date('Y-m-d');
		// 	$update_array['finance_transfer_deleted'] = $deleted_status;
		// 	$update_array['deleted_remarks'] = $deleted_status;
		// 	$this->db->where('finance_transfer_id = '.$finance_transfer_id);
		// 	if($this->db->update('finance_transfer',$update_array))
		// 	{
		// 		$this->session->set_userdata('success_message', 'You have successfully '.$status);
		// 	}
		// 	else
		// 	{
		// 		$this->session->set_userdata('error_message', 'Sorry could not perform the action. Please try again');
		// 	}

		// }
		redirect('accounting/accounts-transfer');
  	}


  	public function journal_entry()
    {
      //form validation
      $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
      $this->form_validation->set_rules('account_to_id', 'Charge To','required|xss_clean');
      $this->form_validation->set_rules('amount', 'Amount','required|xss_clean');
      $this->form_validation->set_rules('description', 'Description','required|xss_clean');
      $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');

      
      if ($this->form_validation->run())
      {
        //update order
        if($this->transfer_model->add_journal_entry())
        {
          $this->session->set_userdata('success_message', 'Cheque successfully writted to account');


          redirect('accounting/journal-entry');
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
      $v_data['accounts'] = $accounts = $this->purchases_model->get_all_accounts();

      // var_dump($accounts->result());die();
      $v_data['expense_accounts']= $this->purchases_model->get_child_accounts("Expense Accounts");

      $where = 'journal_entry_deleted = 0 ';
      $table = 'journal_entry';

      $journal = $this->session->userdata('search_journal');

      if(!empty($journal))
      {
        $where .= $journal;
      }
      
      $segment = 3;
      $this->load->library('pagination');
      $config['base_url'] = site_url().'accounting/journal-entry';
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
      $query = $this->transfer_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='journal_entry.created', $order_method='DESC');
      // var_dump($query); die();
    
      $data['title'] = 'Accounts';
      $v_data['title'] = $data['title'];
      
      $v_data['query'] = $query;
      $v_data['page'] = $page;

      $data['title'] = $v_data['title']= 'Journal Entry';

      $data['content'] = $this->load->view('transfer/journal_entry', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);
    }

  public function get_other_accounts($account_id)
  {
        $query = $this->purchases_model->get_all_accounts($account_id);
        $changed = '<option value="">--Select an option --</option>';
        // $options = $query;
        // foreach($options-> result() AS $key_old) {
        //     if($key_old->account_id != $type)
        //     {
        //         echo '<option value="'.$key_old->account_id.'">'.$key_old->account_name.'</option>';
        //     }

        // }

       if($query->num_rows() > 0)
       {
           foreach($query->result() as $row):
               // $company_name = $row->company_name;
               $account_name = $row->account_name;
               $account_id = $row->account_id;
               $parent_account = $row->parent_account;

               if($parent_account != $current_parent)
               {
                  $account_from_name = $this->transfer_model->get_account_name($parent_account);
                $changed .= '<optgroup label="'.$account_from_name.'">';
               }

               $changed .= "<option value=".$account_id."> ".$account_name."</option>";
               $current_parent = $parent_account;
               if($parent_account != $current_parent)
               {
                $changed .= '</optgroup>';
               }

             
            
           endforeach;
       }

       echo $changed;
    }

    public function delete_journal_entry($journal_entry_id)
    {
      //delete creditor
      
        $array['journal_entry_deleted'] = 1;
        $array['journal_entry_deleted_by'] = $this->session->userdata('personnel_id');
        $array['journal_entry_deleted_date'] = date('Y-m-d');

        $this->db->where('journal_entry_id',$journal_entry_id);
        $this->db->update('journal_entry',$array);
        $this->session->set_userdata('success_message', 'You have successfully removed the entry');
        redirect('accounting/journal-entry');
    } 


    public function search_journal_entry()
    {
      $visit_date_from = $this->input->post('date_from');
      $account_id = $this->input->post('account');
      $reference_number = $this->input->post('reference_number');
      $visit_date_to = $this->input->post('date_to');

      $search_title = '';

      if(!empty($account_id))
      {
        $search_title .= $tenant_name.' ';
        $account_id = ' AND (journal_entry.account_from_id = '.$account_id.' OR journal_entry.account_to_id ='.$account_id.')';


      }
      else
      {
        $account_id = '';
        $search_title .= '';
      }

      if(!empty($reference_number))
      {
        $search_title .= $tenant_name.' ';
        $reference_number = ' AND journal_entry.document_number LIKE \'%'.$transaction_number.'%\'';


      }
      else
      {
        $transaction_number = '';
        $search_title .= '';
      }

       if(!empty($visit_date_from) && !empty($visit_date_to))
       {
         $visit_date = ' AND journal_entry.payment_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
         $search_title .= 'Journal from '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else if(!empty($visit_date_from))
       {
         $visit_date = ' AND journal_entry.payment_date = \''.$visit_date_from.'\'';
         $search_title .= 'Journal of '.date('jS M Y', strtotime($visit_date_from)).' ';
       }

       else if(!empty($visit_date_to))
       {
         $visit_date = ' AND journal_entry.payment_date = \''.$visit_date_to.'\'';
         $search_title .= 'Journal of '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else
       {
         $visit_date = '';
       }


      $search = $visit_date.$transaction_number.$account_id;

      $this->session->set_userdata('search_journal', $search);

      redirect('accounting/journal-entry');
    }

    public function close_journal_search()
    {
      $this->session->unset_userdata('search_journal');
      redirect('accounting/journal-entry');
    }



  public function direct_payments()
  {
    //form validation
    $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
    // $this->form_validation->set_rules('account_to_id', 'Account To','required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount','required|xss_clean');
    $this->form_validation->set_rules('description', 'Description','required|xss_clean');
    $this->form_validation->set_rules('account_to_type', 'Account To Type','required|xss_clean');
    $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');
    
    if ($this->form_validation->run())
    {
      //update order
      if($this->transfer_model->add_account_payment())
      {
        $this->session->set_userdata('success_message', 'Cheque successfully writted to account');


        redirect('accounting/journal-entry');
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

    $where = 'account_payment_deleted = 0 ';
    $table = 'account_payments';


    $search = $this->session->userdata('search_direct_payments');

    if(!empty($search))
    {
      $where .=$search;
    }
    
    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/journal-entry';
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
    $query = $this->transfer_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='account_payments.payment_date', $order_method='DESC');
    // var_dump($query); die();
  $v_data['expense_accounts'] = $this->purchases_model->get_child_accounts("Expense Accounts");
    $data['title'] = 'Accounts';
    $v_data['title'] = $data['title'];
    
    $v_data['query'] = $query;
    $v_data['page'] = $page;

    $data['title'] = $v_data['title']= 'Direct Payments';

    $data['content'] = $this->load->view('finance/transfer/direct_payments', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
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
      $table = "property_owners";
      $where = "property_owners.property_owner_id > 0";
      $select = "property_owner_name AS charge_to_name, property_owners.property_owner_id AS charge_to_id";

    }
    else if($type == 1)
    {
      $query = $this->transfer_model->get_child_accounts("Bank");
    }

    else if($type == 4)
    {
      $query = $this->purchases_model->get_child_accounts("Expense Accounts");
    }

    echo '<option value="0">--Select an option --</option>';
    if($type == 2 OR $type == 3)
    {

      $options = $this->transfer_model->get_type_variables($table,$where,$select);
      foreach($options->result() AS $key) 
      { 
        echo '<option value="'.$key->charge_to_id.'">'.$key->charge_to_name.'</option>';      
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
  public function delete_direct_payment($account_payment_id)
  {
    $array['account_payment_deleted'] = 1;
    $array['account_payment_deleted_by'] = $this->session->userdata('personnel_id');
    $array['account_payment_deleted_date'] = date('Y-m-d');
    $this->db->where('account_payment_id',$account_payment_id);
    $this->db->update('account_payments',$array_update);

    redirect('accounting/journal-entry');
  }

  public function search_direct_payments()
  {
     $visit_date_from = $this->input->post('date_from');
      $account_id = $this->input->post('account');
      $property_owner_id = $this->input->post('property_owner_id');
      $visit_date_to = $this->input->post('date_to');

      $search_title = '';

     

      if(!empty($property_owner_id))
      {
        $property_owner_name = $this->transfer_model->get_property_owner_name($property_owner_id);
        $search_title .= $property_owner_name;
        $property_owner_id = ' AND account_payments.payment_to = '.$property_owner_id;


      }
      else
      {
        $property_owner_id = '';
        $search_title .= '';
      }

      

       if(!empty($visit_date_from) && !empty($visit_date_to))
       {
         $visit_date = ' AND account_payments.payment_date BETWEEN \''.$visit_date_from.'\' AND \''.$visit_date_to.'\'';
         $search_title .= ' FROM '.date('jS M Y', strtotime($visit_date_from)).' to '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else if(!empty($visit_date_from))
       {
         $visit_date = ' AND account_payments.payment_date = \''.$visit_date_from.'\'';
         $search_title .= ' FOR '.date('jS M Y', strtotime($visit_date_from)).' ';
       }

       else if(!empty($visit_date_to))
       {
         $visit_date = ' AND account_payments.payment_date = \''.$visit_date_to.'\'';
         $search_title .= ' FOR '.date('jS M Y', strtotime($visit_date_to)).' ';
       }

       else
       {
         $visit_date = '';
       }

      if(!empty($account_id))
      {
        $account_name = $this->transfer_model->get_account_name($account_id);
        $search_title .= 'FROM '.$account_name;
        $account_id = ' AND (account_payments.account_from_id = '.$account_id.')';


      }
      else
      {
        $account_id = '';
        $search_title .= '';
      }


      $search = $visit_date.$property_owner_id.$account_id;

      // var_dump($search);die();

      $this->session->set_userdata('search_direct_payments', $search);
       $this->session->set_userdata('title_direct_payments', $search_title);

      redirect('accounting/journal-entry');
  }

    public function close_direct_payments_search()
    {
      $this->session->unset_userdata('search_direct_payments');
       $this->session->unset_userdata('title_direct_payments');
      redirect('accounting/journal-entry');
    }

    public function print_direct_payments()
    {
        // var_dump($account); die();

       $where = 'account_payment_deleted = 0 ';
      $table = 'account_payments';


      $search = $this->session->userdata('search_direct_payments');

      if(!empty($search))
      {
        $where .=$search;
      }
      $this->db->where($where);
      $this->db->order_by('account_payments.payment_date', 'ASC');
      $this->db->select('*');
      $defaulters_query = $this->db->get($table);

      $v_data['query'] = $defaulters_query;

      $v_data['contacts'] = $this->site_model->get_contacts();
      $v_data['search_title'] = 'Direct Payments';
      $v_data['title'] = 'Direct Payments';
      $this->load->view('finance/transfer/print_direct_payments', $v_data);
    }

    public function export_direct_payments()
    {

      $this->transfer_model->export_direct_payments();
      
    }








    // landloard transfers


    public function landlord_transfer()
    {
      //form validation
      $this->form_validation->set_rules('account_from_id', 'From','required|xss_clean');
      $this->form_validation->set_rules('account_to_id', 'Charge To','required|xss_clean');
      $this->form_validation->set_rules('amount', 'Amount','required|xss_clean');
      $this->form_validation->set_rules('description', 'Description','required|xss_clean');
      $this->form_validation->set_rules('payment_date', 'Payment Date','required|xss_clean');

      
      if ($this->form_validation->run())
      {
        //update order
        if($this->transfer_model->add_journal_entry())
        {
          $this->session->set_userdata('success_message', 'Cheque successfully writted to account');


          redirect('accounting/journal-entry');
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
      $v_data['accounts'] = $accounts = $this->purchases_model->get_all_accounts();

      // var_dump($accounts->result());die();
      $v_data['expense_accounts']= $this->purchases_model->get_child_accounts("Expense Accounts");

      $where = 'journal_entry_deleted = 0 ';
      $table = 'journal_entry';

      $journal = $this->session->userdata('search_journal');

      if(!empty($journal))
      {
        $where .= $journal;
      }
      
      $segment = 3;
      $this->load->library('pagination');
      $config['base_url'] = site_url().'accounting/journal-entry';
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
      $query = $this->transfer_model->get_account_payments_transactions($table, $where, $config["per_page"], $page, $order='journal_entry.created', $order_method='DESC');
      // var_dump($query); die();
    
      $data['title'] = 'Accounts';
      $v_data['title'] = $data['title'];
      
      $v_data['query'] = $query;
      $v_data['page'] = $page;

      $data['title'] = $v_data['title']= 'Landlord Transfers';

      $data['content'] = $this->load->view('transfer/landlord_transfer', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);
    }

}
?>
