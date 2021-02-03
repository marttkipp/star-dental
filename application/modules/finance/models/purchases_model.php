<?php

class Purchases_model extends CI_Model
{
  /*
  *	Count all items from a table
  *	@param string $table
  * 	@param string $where
  *
  */
  public function count_items($table, $where, $limit = NULL)
  {
    if($limit != NULL)
    {
      $this->db->limit($limit);
    }
    $this->db->from($table);
    $this->db->where($where);
    return $this->db->count_all_results();
  }



   public function get_account_payments_transactions($table, $where, $config, $page, $order, $order_method)
  {
    //retrieve all accounts
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    // $this->db->join();
     $this->db->join('creditor', 'creditor.creditor_id = v_general_ledger.recepientId','left');
    $query = $this->db->get('', $config, $page);

    return $query;
  }

  public function get_petty_cash_transactions($table, $where, $config, $page, $order, $order_method)
  {
    //retrieve all accounts
    $this->db->from($table);
    $this->db->select('v_general_ledger.*,creditor.creditor_name');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    $this->db->join('creditor', 'creditor.creditor_id = v_general_ledger.recepientId','left');
    // $this->db->join('account', 'account.account_id = finance_purchase.account_to_id','left');
    // $this->db->join('property', 'property.property_id = finance_purchase.property_id','left');
    $query = $this->db->get('', $config, $page);

    return $query;
  }




  public function get_creditor()
  {
    //retrieve all users
    $this->db->from('creditor');
    $this->db->select('*');
    $this->db->where('creditor_id >0 ');
    $query = $this->db->get();

    return $query;
  }




  public function add_payment_amount()
  {

    $document_number_two = $this->create_purchases_number();
    $account = array(
          'account_to_id'=>$this->input->post('account_to_id'),
          'property_id'=>$this->input->post('property_id'),
          'finance_purchase_amount'=>$this->input->post('transacted_amount'),
          'finance_purchase_description'=>$this->input->post('description'),
          'creditor_id'=>$this->input->post('creditor_id'),
          'transaction_number'=>$this->input->post('transaction_number'),
          'transaction_date'=>$this->input->post('transaction_date'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'document_number'=>$document_number_two,
          'created'=>date('Y-m-d H:i:s'),
          'last_modified'=>date('Y-m-d H:i:s')
          );
    // var_dump($account); die();
    if($this->db->insert('finance_purchase',$account))
    {
      $finance_purchase_id = $this->db->insert_id();

      // $document_number = $this->create_purchases_payment();
      // $account = array(
      //       'account_from_id'=>$this->input->post('account_from_id'),
      //       'finance_purchase_id'=>$finance_purchase_id,
      //       'amount_paid'=>$this->input->post('transacted_amount'),
      //       'transaction_date'=>$this->input->post('transaction_date'),
      //       'transaction_number'=>$this->input->post('reference_number'),
      //       'transaction_date'=>$this->input->post('transaction_date'),
      //       'created_by'=>$this->session->userdata('personnel_id'),
      //       'created'=>date('Y-m-d'),
      //       'document_number'=>$document_number
      //       );
      // if($this->db->insert('finance_purchase_payment',$account))
      // {
          return TRUE;
      // }

    }
    else
    {
      return FALSE;
    }
  }
  public function payaninvoice($finance_purchase_id)
  {
    $document_number = $this->create_purchases_payment();
    $account = array(
          'account_from_id'=>$this->input->post('account_from_id'),
          'finance_purchase_id'=>$finance_purchase_id,
          'amount_paid'=>$this->input->post('amount_paid'),
          'transaction_date'=>$this->input->post('payment_date'),
          'transaction_number'=>$this->input->post('reference_number'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'created'=>date('Y-m-d H:i:s'),
          'last_modified'=>date('Y-m-d H:i:s'),
          'document_number'=>$document_number
          );
    if($this->db->insert('finance_purchase_payment',$account))
    {
        return TRUE;
    }
    else {
      return FALSE;
    }
  }


  public function record_petty_cash_transaction()
  {

    $document_number_two = $this->create_purchases_number();
    $account = array(
          'account_to_id'=>$this->input->post('account_to_id'),
          'property_id'=>$this->input->post('property_id'),
          'finance_purchase_amount'=>$this->input->post('transacted_amount'),
          'finance_purchase_description'=>$this->input->post('description'),
          'creditor_id'=>$this->input->post('creditor_id'),
          'transaction_number'=>$this->input->post('transaction_number'),
          'transaction_date'=>$this->input->post('transaction_date'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'document_number'=>$document_number_two,
          'created'=>date('Y-m-d H:i:s'),
          'last_modified'=>date('Y-m-d H:i:s')
          );
    // var_dump($account); die();
    if($this->db->insert('finance_purchase',$account))
    {
      $finance_purchase_id = $this->db->insert_id();

      $document_number = $this->create_purchases_payment();
      $account = array(
            'account_from_id'=>$this->input->post('account_from_id'),
            'finance_purchase_id'=>$finance_purchase_id,
            'amount_paid'=>$this->input->post('transacted_amount'),
            'transaction_date'=>$this->input->post('transaction_date'),
            'transaction_number'=>$this->input->post('reference_number'),
            'transaction_date'=>$this->input->post('transaction_date'),
            'created_by'=>$this->session->userdata('personnel_id'),
            'created'=>date('Y-m-d'),
            'document_number'=>$document_number
            );
      if($this->db->insert('finance_purchase_payment',$account))
      {
          return TRUE;
      }
      else {
          return FALSE;
      }

    }
    else
    {
      return FALSE;
    }
  }
  public function get_all_purchase_invoices()
  {
    //retrieve all users
    $this->db->from('finance_purchase,account');
    $this->db->select('*');
    $this->db->where('finance_purchase_id > 0 AND finance_purchase.account_to_id = account.account_id');
    $query = $this->db->get();

    return $query;
  }

  public function get_amount_paid($finance_purchase_id)
  {
    $this->db->from('finance_purchase_payment');
    $this->db->select('SUM(amount_paid) AS total_amount');
    $this->db->where('finance_purchase_id = '.$finance_purchase_id.' AND finance_purchase_payment_status = 1');
    $query = $this->db->get();
    $total_amount = 0;

    if($query->num_rows() > 0)
    {
      foreach ($query->result() as $key => $value) {
        // code...
        $total_amount = $value->total_amount;
      }
    }
    return $total_amount;
  }
  function create_purchases_number()
	{
		//select product code
		$preffix = "HA-RT-";
		$this->db->from('finance_purchase');
		$this->db->where("finance_purchase_id > 0");
		$this->db->select('MAX(document_number) AS number');
		$query = $this->db->get();//echo $query->num_rows();

		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;

			$number++;//go to the next number
		}
		else{//start generating receipt numbers
			$number = 1;
		}

		return $number;
	}


  function create_purchases_payment()
  {
    //select product code
    $preffix = "HA-RT-";
    $this->db->from('finance_purchase_payment');
    $this->db->where("finance_purchase_payment_id > 0");
    $this->db->select('MAX(document_number) AS number');
    $query = $this->db->get();//echo $query->num_rows();

    if($query->num_rows() > 0)
    {
      $result = $query->result();
      $number =  $result[0]->number;

      $number++;//go to the next number
    }
    else{//start generating receipt numbers
      $number = 1;
    }

    return $number;
  }

  public function get_transacting_accounts($parent_account_name,$type=null)
  {
      $this->db->from('account');
      $this->db->select('*');
      $this->db->where('(parent_account = 2 OR parent_account =19) AND paying_account = 0 AND account_status = 1');
      $query = $this->db->get();     

      return $query;     

  }

  public function get_child_accounts($parent_account_name,$type=null)
  {
      $this->db->from('account');
      $this->db->select('*');
      $this->db->where('account_name = "'.$parent_account_name.'"');
      $query = $this->db->get();

      if($query->num_rows() > 0)
      {
        foreach ($query->result() as $key => $value) {
          # code...
          $account_id = $value->account_id;
        }
        $values ='';
        if(!empty($type))
        {
          $values = ' AND account_id <> '.$type;
        }
        //retrieve all users
        $this->db->from('account');
        $this->db->select('*');
        $this->db->where('paying_account = 0 AND account_status = 1 AND parent_account = '.$account_id.$values);
        $query = $this->db->get();

        return $query;


      }
      else
      {
        return FALSE;
      }

  }

  public function get_all_departments()
  {

    $this->db->where('department_status = 1');
    $this->db->order_by('department_id');
    $query = $this->db->get('departments');


    return $query;
  }
  public function get_purchases_details($finance_purchase_id)
  {

    $this->db->where('finance_purchase_id = '.$finance_purchase_id);
    $query = $this->db->get('finance_purchase');


    return $query;
  }
  public function get_transfer_details($finance_transfered_id)
  {

    $this->db->where('finance_transfer.finance_transfer_id = finance_transfered.finance_transfer_id AND finance_transfered.finance_transfered_id = '.$finance_transfered_id);
    $query = $this->db->get('finance_transfered,finance_transfer');
    return $query;
  }
  public function get_account_id($account_name)
  {
    $account_id = 0;

    $this->db->select('account_id');
    $this->db->where('account_name = "'.$account_name.'"');
    $query = $this->db->get('account');

    $bal = $query->row();
    $account_id = $bal->account_id;
    // var_dump($account_id); die();
    return $account_id;

  }

  public function get_account_balance($account_name)
  {
    $account_id = 0;

    $this->db->select('SUM(dr_amount) - SUM(cr_amount) AS balance');
    $this->db->where('accountName = "'.$account_name.'"');
    $query = $this->db->get('v_general_ledger');

    $bal = $query->row();
    $account_id = $bal->balance;
    // var_dump($account_id); die();
    return $account_id;
  }
  public function get_account_opening_balance($account_name)
  {
    $date_from = $this->session->userdata('petty_cash_visit_date_from');
    if(!empty($date_from))
    {

      $search  = ' AND v_account_ledger_by_date.transactionDate < "'.$date_from.'"';
    }
    else 
    {
      $add7days = date('Y-m-d', strtotime('-7 days'));
      $search = ' AND v_account_ledger_by_date.transactionDate < "'.$add7days.'"';
    }


    $this->db->select('SUM(dr_amount) AS dr_amount , SUM(cr_amount) AS cr_amount');
    $this->db->where('((v_account_ledger_by_date.transactionClassification = "Purchase Payment" AND v_account_ledger_by_date.accountName = "Petty Cash")
                  OR (v_account_ledger_by_date.transactionCategory = "Transfer" AND  v_account_ledger_by_date.accountName = "Petty Cash")
                  OR (v_account_ledger_by_date.transactionCategory = "Expense Payment" AND  v_account_ledger_by_date.accountName = "Petty Cash")
                ) '.$search);
    $query = $this->db->get('v_account_ledger_by_date');

    // $bal = $query->row();
    // $account_id = $bal->balance;
    // // var_dump($account_id); die();
    return $query;
  }

  public function get_petty_cash($where, $table)
  {
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by('v_account_ledger_by_date.transactionDate', 'ASC');
    $this->db->join('creditor', 'creditor.creditor_id = v_account_ledger_by_date.recepientId','left');
    $query = $this->db->get($table);

    return $query;
  }

  
  public function get_all_accounts($account_id = NULL)
  {
      if(!empty($account_id))
      {
        $add = ' AND account_id <> '.$account_id;
      }
      else
      {
        $add ='';
      }
      $this->db->from('account');
      $this->db->select('*');
      $this->db->where('parent_account <> 0'.$add);
      $this->db->order_by('parent_account','ASC');
      $query = $this->db->get();

       return $query;

  }
}
?>
