<?php

class Transfer_model extends CI_Model
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

  public function get_account_transfer_transactions($table, $where, $config, $page, $order, $order_method)
  {
    //retrieve all accounts
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    $this->db->join('finance_transfered', 'finance_transfered.finance_transfer_id = finance_transfer.finance_transfer_id','left');
    $query = $this->db->get('', $config, $page);

    return $query;
  }



  public function get_petty_cash_transactions($table, $where, $config, $page, $order, $order_method)
  {
    //retrieve all accounts
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    // $this->db->join();
    $query = $this->db->get('', $config, $page);

    return $query;
  }
  public function transfer_funds($finance_transfer_id = NULL)
  {
    // $document_number = $this->create_purchases_payment();
    $amount = $this->input->post('amount');
    $amount = str_replace(',', '', $amount);
    $account = array(
          'account_from_id'=>$this->input->post('account_from_id'),
          'finance_transfer_amount'=>$amount,
          'transaction_date'=>$this->input->post('transfer_date'),
          'reference_number'=>$this->input->post('reference_number'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'remarks'=>$this->input->post('description'),          
          'last_modified'=>date('Y-m-d H:i:s'),
          // 'finance_transfer_status' => 0
          );


    if(!empty($finance_transfer_id))
    {
      $this->db->where('finance_transfer_id',$finance_transfer_id);
      if($this->db->update('finance_transfer',$account))
      {
        $account = array(
              'account_to_id'=>$this->input->post('account_to_id'),
              'finance_transfered_amount'=>$amount,
              'transaction_date'=>$this->input->post('transfer_date'),
              'created_by'=>$this->session->userdata('personnel_id'),
              'finance_transfer_id'=>$finance_transfer_id,
              'remarks'=>$this->input->post('description'),
              'last_modified'=>date('Y-m-d H:i:s'),
              );
        $this->db->where('finance_transfer_id',$finance_transfer_id);
        if($this->db->update('finance_transfered',$account))
        {
          return TRUE;
        }
        else {
            return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }
    else
    {
      $account['created'] = date('Y-m-d H:i:s');
      if($this->db->insert('finance_transfer',$account))
      {
        $finance_transfer_id = $this->db->insert_id();
        $account = array(
              'account_to_id'=>$this->input->post('account_to_id'),
              'finance_transfered_amount'=>$amount,
              'transaction_date'=>$this->input->post('transfer_date'),
              'created_by'=>$this->session->userdata('personnel_id'),
              'finance_transfer_id'=>$finance_transfer_id,
              'remarks'=>$this->input->post('description'),
              'created'=>date('Y-m-d H:i:s'),
              'last_modified'=>date('Y-m-d H:i:s'),
              );
        if($this->db->insert('finance_transfered',$account))
        {
          return TRUE;
        }
        else {
            return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }

  }
  public function get_account_name($from_account_id)
  {
    $account_name = '';
    $this->db->select('account_name');
    $this->db->where('account_id = '.$from_account_id);
    $query = $this->db->get('account');

    $account_details = $query->row();
    $account_name = $account_details->account_name;

    return $account_name;
  }


  public function get_transfer_details($finance_transfer_id)
  {
    $account_name = '';
    $this->db->select('finance_transfer.*');
    $this->db->where('finance_transfer_id = '.$finance_transfer_id);
    $query = $this->db->get('finance_transfer');

    // $account_details = $query->row();
    // $account_name = $account_details->account_name;

    return $query;
  }
  public function get_creditor_name($creditor_id)
    {
      $account_name = '';
      $this->db->select('creditor_name');
      $this->db->where('creditor_id = '.$creditor_id);
      $query = $this->db->get('creditor');
      
      $account_details = $query->row();
      $account_name = $account_details->creditor_name;
      
      return $account_name;
    }
}
?>
