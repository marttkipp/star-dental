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
  public function transfer_funds()
  {
    // $document_number = $this->create_purchases_payment();
    $account = array(
          'account_from_id'=>$this->input->post('account_from_id'),
          'finance_transfer_amount'=>$this->input->post('amount'),
          'transaction_date'=>$this->input->post('transfer_date'),
          'reference_number'=>$this->input->post('reference_number'),
          'created_by'=>$this->session->userdata('personnel_id'),
          'remarks'=>$this->input->post('description'),
          'created'=>date('Y-m-d H:i:s'),
          'last_modified'=>date('Y-m-d H:i:s'),
          // 'finance_transfer_status' => 0
          );
    if($this->db->insert('finance_transfer',$account))
    {
      $finance_transfer_id = $this->db->insert_id();
      $account = array(
            'account_to_id'=>$this->input->post('account_to_id'),
            'finance_transfered_amount'=>$this->input->post('amount'),
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
  public function get_account_name($from_account_id)
  {
    $account_name = '';
    $this->db->select('account_name');
    $this->db->where('account_id = '.$from_account_id);
    $query = $this->db->get('account');

    $account_details = $query->row();
    $account_name = '';
    if($query->num_rows() > 0)
    {
       $account_name = $account_details->account_name;
    }
   

    return $account_name;
  }
  function create_journal_number()
  {
    //select product code
    $preffix = "HA-RT-";
    $this->db->from('journal_entry');
    $this->db->where("journal_entry_id > 0");
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


  public function add_journal_entry()
  {
      
      $account = array(
            'account_to_id'=>$this->input->post('account_to_id'),
            'account_from_id'=>$this->input->post('account_from_id'),
            'amount_paid'=>$this->input->post('amount'),
            'journal_entry_description'=>$this->input->post('description'),
            'document_number'=>$this->input->post('reference_number'),
            'payment_date'=>$this->input->post('payment_date'),
            'created_by'=>$this->session->userdata('personnel_id'),
            'created'=>date('Y-m-d')
            );
      // var_dump($account); die();
      if($this->db->insert('journal_entry',$account))
      {
        return TRUE;
      }
      else
      {
        return FALSE;
      }
  }

  public function get_account_payments_transactions($table, $where, $config, $page, $order, $order_method)
  {
    //retrieve all accounts
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    $query = $this->db->get('', $config, $page);
    
    return $query;
  }
  public function get_creditor_name($creditor_id)
    {
      $account_name = '';
      $this->db->select('creditor_name');
      $this->db->where('creditor_id = '.$creditor_id);
      $query = $this->db->get('creditor');
      
      $account_name = '';
      if($query->num_rows () > 0)
      {
        $account_details = $query->row();
        $account_name = $account_details->creditor_name;
      }
      
      
      return $account_name;
    }
     public function get_type_variables($table,$where,$select)    
    {
      //retrieve all users
      $this->db->from($table);
      $this->db->select($select);
      $this->db->where($where);
      $query = $this->db->get();
      
      return $query;      
   
      }

    public function add_account_payment()
    {
      $property_beneficiary_data = $this->input->post('property_beneficiary_id');

      $explode = explode('#', $property_beneficiary_data);

      $property_beneficiary_id = $explode[0];
      $property_owner_id = $explode[1];
      $account = array(
            'account_to_id'=>$this->input->post('account_to_id'),
            'account_from_id'=>$this->input->post('account_from_id'),
            'amount_paid'=>$this->input->post('amount'),
            'account_payment_description'=>$this->input->post('description'),
              'account_to_type'=>$this->input->post('account_to_type'),
              'receipt_number'=>$this->input->post('cheque_number'),
              'payment_date'=>$this->input->post('payment_date'),
              'created_by'=>$this->session->userdata('personnel_id'),
              'created'=>date('Y-m-d'),
              'payment_to'=>$property_owner_id,
              'property_beneficiary_id'=>$property_beneficiary_id
            );
      // var_dump($account); die();
      if($this->db->insert('account_payments',$account))
      {

        $account_payment_id = $this->db->insert_id();
        $account_from_id = $this->input->post('account_from_id');
        $transfer_charge = $this->get_transfer_charge($account_from_id);

        if($transfer_charge > 0)
        {
            $account = array(
              'account_to_id'=>32,
              'account_from_id'=>$this->input->post('account_from_id'),
              'amount_paid'=>$transfer_charge,
              'account_payment_description'=>$this->input->post('description'),
                'account_to_type'=>4,
                'receipt_number'=>$this->input->post('cheque_number'),
                'payment_date'=>$this->input->post('payment_date'),
                'created_by'=>$this->session->userdata('personnel_id'),
                'created'=>date('Y-m-d'),
                'parent_payment_id'=>$account_payment_id,
                'property_beneficiary_id'=>0,
                'payment_to'=>10
              );
            // var_dump($account); die();
            if($this->db->insert('account_payments',$account))
            {
              return TRUE;
            }
            else
            {
              return TRUE;
            }
        }
        else
        {
          return TRUE;
        }
        
      }
      else
      {
        return FALSE;
      }
    }
    public function get_transfer_charge($account_id)
    {

       $transfer_charge = 0;
      $this->db->select('transfer_charge');
      $this->db->where('account_id = '.$account_id);
      $query = $this->db->get('account');
      
      $account_details = $query->row();
      $transfer_charge = $account_details->transfer_charge;
      
      return $transfer_charge;

    }
    public function get_owner_name($property_owner_id)
    {
      $account_name = '';
      $this->db->select('property_owner_name');
      $this->db->where('property_owner_id = '.$property_owner_id);
      $query = $this->db->get('property_owners');
      
      $account_details = $query->row();
      if($query->num_rows() > 0)
      {
        $account_name = $account_details->property_owner_name;
      }
      
      
      return $account_name;
    }

    public function get_beneficiary_name($property_beneficiary_id)
    {
      $account_name = '';
      $this->db->select('property_beneficiary_name');
      $this->db->where('property_beneficiary_id = '.$property_beneficiary_id);
      $query = $this->db->get('property_beneficiaries');
      
      $account_details = $query->row();
      $account_name = $account_details->property_beneficiary_name;
      
      return $account_name;
    }

    public function export_direct_payments()
    {
       $this->load->library('excel');
    
      //get all transactions
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
      
      $title = 'Direct Payments';
      
      if($defaulters_query->num_rows() > 0)
      {
        $count = 0;
        /*
          -----------------------------------------------------------------------------------------
          Document Header
          -----------------------------------------------------------------------------------------
        */

                

        $row_count = 0;
        $report[$row_count][0] = '#';
        $report[$row_count][1] = 'Payment Date';
        $report[$row_count][2] = 'Payment From';
        $report[$row_count][3] = 'Transaction Number.';
        $report[$row_count][4] = 'Description';
        $report[$row_count][5] = 'Amount';



        //get & display all services
        
        //display all patient data in the leftmost columns
        foreach($defaulters_query->result() as $leases_row)
        {
          
           $account_from_id = $leases_row->account_from_id;
            $account_to_type = $leases_row->account_to_type;
            $account_to_id = $leases_row->account_to_id;
            $receipt_number = $leases_row->receipt_number;
            $account_payment_id = $leases_row->account_payment_id;
             $payment_date = $leases_row->payment_date;
             $created = $leases_row->created;
            $amount_paid = $leases_row->amount_paid;
            $payment_to = $leases_row->payment_to;
            $property_beneficiary_id = $leases_row->property_beneficiary_id;

            $account_from_name = $this->transfer_model->get_account_name($account_from_id);
            if($account_to_type == 1 AND $account_to_id > 0)
            {
                $payment_type = 'Transfer';
                $account_to_name = $this->transfer_model->get_account_name($account_to_id);
            }
            else if($account_to_type == 3 AND $payment_to > 0)
            {
                // doctor payments
                $payment_type = "Landlord Payment";
                $account_to_name = $this->transfer_model->get_owner_name($payment_to);
            }
            else if($account_to_type == 2 AND $account_to_id > 0)
            {
                // creditor
                $payment_type = "Creditor Payment";
                $account_to_name = $this->transfer_model->get_creditor_name($account_to_id);
            }
            else if($account_to_type == 4 AND $account_to_id > 0)
            {
                // expense account
                $payment_type = "Direct Expense Payment";
                $account_to_name = $this->transfer_model->get_account_name($account_to_id);
            }
            else if($account_to_type == 3 AND $property_beneficiary_id > 0)
            {
                // doctor payments
                $payment_type = "Landlord Payment";
                $account_to_name = $this->transfer_model->get_beneficiary_name($property_beneficiary_id);
            }
            else
            {
              $account_to_name ='';
            }

       
                
              $row_count++;
              $count++;
              //display the patient data
              $report[$row_count][0] = $row_count;
              $report[$row_count][1] = $payment_date;
              $report[$row_count][3] = $account_from_name;
              $report[$row_count][2] = strtoupper($receipt_number);
              $report[$row_count][4] = $payment_type.' '.$account_to_name;
              $report[$row_count][5] = number_format($amount_paid,2);
        
       
        }
      }
      
      //create the excel document
      $this->excel->addArray ( $report );
      $this->excel->generateXML ($title);
    }

    // public function get_account_name($from_account_id)
    // {
    //   $account_name = '';
    //   $this->db->select('account_name');
    //   $this->db->where('account_id = '.$from_account_id);
    //   $query = $this->db->get('account');
      
    //   $account_details = $query->row();
    //   $account_name = $account_details->account_name;
      
    //   return $account_name;
    // }


    public function get_property_name($from_account_id)
    {
      $property_name = '';
      $this->db->select('property_name');
      $this->db->where('property_id = '.$from_account_id);
      $query = $this->db->get('property');
      
      $account_details = $query->row();
      $property_name = $account_details->property_name;
      
      return $property_name;
    }

    public function get_property_owner_name($from_account_id)
    {
      $property_name = '';
      $this->db->select('property_owner_name');
      $this->db->where('property_owner_id = '.$from_account_id);
      $query = $this->db->get('property_owners');
      
      $account_details = $query->row();
      $property_name = $account_details->property_owner_name;
      
      return $property_name;
    }


    function create_transfer_number()
    {
      //select product code
      $preffix = "HA-RT-";
      $this->db->from('landlord_transfer');
      $this->db->where("landlord_transfer_id > 0");
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
