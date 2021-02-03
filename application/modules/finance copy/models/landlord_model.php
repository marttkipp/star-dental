<?php

class Landlord_model extends CI_Model
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
    $this->db->select('landlord_transactions.*,property.property_name,property_owners.property_owner_name,account.account_name');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    $this->db->join('account', 'account.account_id = landlord_transactions.account_to_id','left');
    $this->db->join('property', 'property.property_id = landlord_transactions.property_id','left');
    $this->db->join('property_owners', 'property_owners.property_owner_id = property.property_owner_id','left');
    $query = $this->db->get('', $config, $page);

    return $query;
  }


  public function add_payment_amount()
  {

    $document_number_two = $this->create_transaction_number();
    $transaction_explode = explode('-',$this->input->post('transaction_date'));
    $year = $transaction_explode[0];
    $month = $transaction_explode[1];

    $this->db->where('property_id',$this->input->post('property_id'));
    $query = $this->db->get('property');
    $row = $query->row();



    $landlord_id = $row->property_owner_id;
    $account = array(
          'account_to_id'=>$this->input->post('account_to_id'),
          'property_id'=>$this->input->post('property_id'),
          'landlord_transaction_description'=>$this->input->post('description'),
          'bank_id'=>$this->input->post('bank_id'),
          'transaction_type_id'=>$this->input->post('transaction_type_id'),
          'transaction_number'=>$this->input->post('transaction_number'),
          'transaction_date'=>$this->input->post('transaction_date'),
          'payment_method_id'=>$this->input->post('payment_method'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'document_number'=>$document_number_two,
          'landlordid'=>$landlord_id,
          'month'=>$month,
          'year'=>$year,
          'chequeno'=>$this->input->post('transaction_number'),
          'paymenttermid'=>$this->input->post('invoice_type_id'),
          'created'=>date('Y-m-d H:i:s'),
          'last_modified'=>date('Y-m-d H:i:s')
          );
      $transaction_type_id = $this->input->post('transaction_type_id');
      if($transaction_type_id == 4)
      {

        $account['landlord_transaction_amount']  = -$this->input->post('transacted_amount');
      }
      else {
        $account['landlord_transaction_amount']  = $this->input->post('transacted_amount');
      }
    if($this->db->insert('landlord_transactions',$account))
    {
      $landlord_transaction_id = $this->db->insert_id();

      $transaction_type_id = $this->input->post('transaction_type_id');
      if($transaction_type_id == 4)
      {
        $account = array(
              'account_to_id'=>$this->input->post('account_to_id'),
              'property_id'=>$this->input->post('property_to_id'),
              'landlord_transaction_description'=>$this->input->post('description'),
              'bank_id'=>$this->input->post('bank_id'),
              'transaction_type_id'=>$this->input->post('transaction_type_id'),
              'transaction_number'=>$this->input->post('transaction_number'),
              'transaction_date'=>$this->input->post('transaction_date'),
              'payment_method_id'=>$this->input->post('payment_method'),
              'created_by'=>$this->session->userdata('personnel_id'),
              'document_number'=>$document_number_two,
              'landlordid'=>$landlord_id,
              'month'=>$month,
              'year'=>$year,
              'chequeno'=>$this->input->post('transaction_number'),
              'paymenttermid'=>$this->input->post('invoice_type_id'),
              'parent_transaction'=>$landlord_transaction_id,
              'created'=>date('Y-m-d H:i:s'),
              'last_modified'=>date('Y-m-d H:i:s')
              );
          $transaction_type_id = $this->input->post('transaction_type_id');
          $account['landlord_transaction_amount']  = $this->input->post('transacted_amount');
          $this->db->insert('landlord_transactions',$account);
      }
      return TRUE;
    }
    else
    {
      return FALSE;
    }
  }

  function create_transaction_number()
	{
		//select product code
		$preffix = "HA-RT-";
		$this->db->from('landlord_transactions');
		$this->db->where("landlord_transaction_id > 0");
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

}
?>
