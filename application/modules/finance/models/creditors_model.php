<?php

class Creditors_model extends CI_Model
{
  /*
  * Retrieve all creditor
  * @param string $table
  *   @param string $where
  *
  */
  public function get_all_creditors($table, $where, $per_page, $page, $order = 'creditor_name', $order_method = 'ASC')
  {
    //retrieve all users
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    $query = $this->db->get('', $per_page, $page);

    return $query;
  }

	/*
	*	Add a new creditor
	*
	*/
  public function get_creditors_list($table, $where, $order)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order,'asc');
		$query = $this->db->get('');

		return $query;
	}

  /*
	*	get a single creditor's details
	*	@param int $creditor_id
	*
	*/
	public function get_creditor($creditor_id)
	{
		//retrieve all users
		$this->db->from('creditor');
		$this->db->select('*');
		$this->db->where('creditor_id = '.$creditor_id);
		$query = $this->db->get();

		return $query;
	}


  public function add_invoice_item($creditor_id)
	{
		$amount = $this->input->post('unit_price');
		$account_to_id=$this->input->post('account_to_id');
    $item_description = $this->input->post('item_description');
		$quantity=$this->input->post('quantity');
    $vat_amount = $this->input->post('vat_amount');
    $total_amount = $this->input->post('total_amount');
		$tax_type_id=$this->input->post('tax_type_id');


		$service = array(
							'creditor_invoice_id'=>0,
							'unit_price'=> $amount,
							'account_to_id' => $account_to_id,
							'creditor_invoice_item_status' => 0,
							'creditor_id' => $creditor_id,
              'item_description'=>$item_description,
							'created_by' => $this->session->userdata('personnel_id'),
							'created' => date('Y-m-d'),
              'total_amount'=>$total_amount,
              'vat_amount'=>$vat_amount,
              'quantity'=>$quantity,
              'vat_type_id'=>$tax_type_id
						);


		$this->db->insert('creditor_invoice_item',$service);
		return TRUE;

	}

  public function confirm_creditor_invoice($creditor_id,$personnel_id = NULL)
	{
		$amount = $this->input->post('amount');
		$amount_charged = $this->input->post('amount_charged');
		$invoice_date = $this->input->post('invoice_date');
    $vat_charged = $this->input->post('vat_charged');
    $invoice_number = $this->input->post('invoice_number');

		$date_check = explode('-', $invoice_date);
		$month = $date_check[1];
		$year = $date_check[0];


		$document_number = $this->create_invoice_number();

		// var_dump($checked); die();

		$insertarray['transaction_date'] = $invoice_date;
		$insertarray['invoice_year'] = $year;
		$insertarray['invoice_month'] = $month;
		$insertarray['creditor_id'] = $creditor_id;
		$insertarray['document_number'] = $document_number;
    $insertarray['invoice_number'] = strtoupper($invoice_number);
		$insertarray['total_amount'] = $amount_charged;
		$insertarray['vat_charged'] = $vat_charged;
		$insertarray['created_by'] = $this->session->userdata('personnel_id');
		$insertarray['created'] = date('Y-m-d');
		$insertarray['amount'] = $amount;

		if($this->db->insert('creditor_invoice', $insertarray))
		{

			$creditor_invoice_id = $this->db->insert_id();
      $total_visits = sizeof($_POST['creditor_invoice_items']);
      //check if any checkboxes have been ticked
      if($total_visits > 0)
      {
        for($r = 0; $r < $total_visits; $r++)
        {
          $visit = $_POST['creditor_invoice_items'];
          $creditor_invoice_item_id = $visit[$r];
          //check if card is held
          $service = array(
                    'creditor_invoice_id'=>$creditor_invoice_id,
                    'created' =>$invoice_date,
                    'creditor_invoice_item_status'=>1,
                    'year'=>$year,
                    'month'=>$month,
                  );
          $this->db->where('creditor_invoice_item_id',$creditor_invoice_item_id);
          $this->db->update('creditor_invoice_item',$service);
        }
      }



			return TRUE;
		}

	}

  public function create_invoice_number()
	{
		//select product code
		$this->db->where('creditor_invoice_id > 0');
		$this->db->from('creditor_invoice');
		$this->db->select('MAX(document_number) AS number');
		$this->db->order_by('creditor_invoice_id','DESC');
		// $this->db->limit(1);
		$query = $this->db->get();
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			// var_dump($number);die();
			$number++;

		}
		else{
			$number = 1;
		}
		// var_dump($number);die();
		return $number;
	}

  public function get_creditor_invoice($creditor_id,$limit=null)
	{
		$this->db->where('creditor_invoice.creditor_invoice_id = creditor_invoice_item.creditor_invoice_id AND creditor_invoice.creditor_invoice_status = 1 AND creditor_invoice.creditor_id = '.$creditor_id);
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->group_by('creditor_invoice.creditor_invoice_id');
		$this->db->order_by('creditor_invoice.transaction_date','DESC');
		return $this->db->get('creditor_invoice_item,creditor_invoice');
	}



  public function get_creditor_invoice_number($creditor_id,$limit=null)
  {
    // $this->db->where('v_creditors_invoice_balances.creditor_id = '.$creditor_id);
    // $this->db->select('*');
    // return $this->db->get('v_creditors_invoice_balances');

    $select_statement = "
                        SELECT
                          data.invoice_id AS creditor_invoice_id,
                          data.invoice_number AS invoice_number,
                          data.invoice_date AS invoice_date,
                          data.creditor_invoice_type AS creditor_invoice_type,
                          COALESCE (SUM(data.dr_amount),0) AS dr_amount,
                          COALESCE (SUM(data.cr_amount),0) AS cr_amount,
                          COALESCE (SUM(data.dr_amount),0) - COALESCE (SUM(data.cr_amount),0) AS balance
                        FROM 
                        (
                          SELECT
                            `orders`.`supplier_id` AS creditor_id,
                            `orders`.`order_id` as invoice_id,
                            `orders`.`supplier_invoice_number` as invoice_number,
                            `orders`.`supplier_invoice_date` as invoice_date,
                            'Supplies Invoice' AS creditor_invoice_type,
                            COALESCE (SUM(`order_supplier`.`less_vat`),0) AS dr_amount,
                            0 AS cr_amount
                            FROM (`orders`,order_supplier,order_item)
                            WHERE `order_supplier`.`order_id` = `orders`.`order_id`
                            AND `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
                            AND orders.is_store = 0
                            AND orders.order_approval_status = 7
                            GROUP BY orders.order_id

                            UNION ALL 

                            SELECT
                            `orders`.`supplier_id` AS creditor_id,
                            `orders`.`reference_id` as invoice_id,
                            `orders`.`reference_number` as invoice_number,
                            `orders`.`supplier_invoice_date` as invoice_date,
                            'Supplies Invoice' AS creditor_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`order_supplier`.`less_vat`),0) AS cr_amount
                            FROM (`orders`,order_supplier,order_item)
                            WHERE `order_supplier`.`order_id` = `orders`.`order_id`
                            AND `order_item`.`order_item_id` = `order_supplier`.`order_item_id`
                            AND orders.is_store = 3
                            AND orders.order_approval_status = 7
                            GROUP BY orders.reference_id

                            UNION ALL 


                            SELECT
                            `orders`.`supplier_id` AS creditor_id,
                            `orders`.`order_id` as invoice_id,
                            `orders`.`supplier_invoice_number` as invoice_number,
                            `creditor_payment`.`transaction_date` as invoice_date,
                            'Supplies Payments' AS creditor_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`creditor_payment_item`.`amount_paid`),0) AS cr_amount
                            FROM (creditor_payment_item,creditor_payment,orders)
                            WHERE `creditor_payment_item`.`creditor_invoice_id` = `orders`.`order_id` 
                            AND `creditor_payment_item`.`creditor_payment_id` = `creditor_payment`.`creditor_payment_id` 
                            AND creditor_payment_item.invoice_type = 1
                            GROUP BY orders.order_id


                            UNION ALL

                            SELECT
                            `creditor_invoice`.`creditor_id` AS creditor_id,
                            `creditor_invoice`.`creditor_invoice_id` AS invoice_id,
                            `creditor_invoice`.`invoice_number` AS invoice_number,
                            `creditor_invoice`.`transaction_date` AS invoice_date,
                            'Creditor Bills' AS creditor_invoice_type,
                            COALESCE (SUM(`creditor_invoice_item`.`total_amount`),0) AS dr_amount,
                            0 AS cr_amount
                            FROM (`creditor_invoice`,creditor_invoice_item)
                            WHERE `creditor_invoice_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id`
                            GROUP BY `creditor_invoice`.`creditor_invoice_id`

                            UNION ALL 

                            SELECT
                            `creditor_invoice`.`creditor_id` AS creditor_id,
                            `creditor_invoice`.`creditor_invoice_id` AS invoice_id,
                            `creditor_invoice`.`invoice_number` AS invoice_number,
                            `creditor_invoice`.`transaction_date` AS invoice_date,
                            'Creditor Bills Credit Note' AS creditor_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`creditor_credit_note_item`.`credit_note_amount`),0) AS cr_amount
                            FROM (`creditor_invoice`,creditor_credit_note,creditor_credit_note_item)
                            WHERE `creditor_credit_note_item`.`creditor_credit_note_id` = `creditor_credit_note`.`creditor_credit_note_id`
                            AND `creditor_invoice`.`creditor_invoice_id` = `creditor_credit_note_item`.`creditor_invoice_id`
                            GROUP BY `creditor_credit_note_item`.`creditor_invoice_id`

                            UNION ALL


                            SELECT
                            `creditor_invoice`.`creditor_id` AS creditor_id,
                            `creditor_invoice`.`creditor_invoice_id` AS invoice_id,
                            `creditor_invoice`.`invoice_number` AS invoice_number,
                            `creditor_invoice`.`transaction_date` AS invoice_date,
                            'Bill Payments' AS creditor_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`creditor_payment_item`.`amount_paid`),0) AS cr_amount
                            FROM (creditor_payment_item,creditor_payment,creditor_invoice)
                            WHERE `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` 
                            AND `creditor_payment_item`.`creditor_payment_id` = `creditor_payment`.`creditor_payment_id` AND creditor_payment_item.invoice_type = 0
                            GROUP BY creditor_invoice.creditor_invoice_id

                            UNION ALL 

                             SELECT
                            `creditor`.`creditor_id` AS creditor_id,
                            `creditor`.`creditor_id` AS invoice_id,
                            `creditor`.`creditor_id` AS invoice_number,
                            `creditor`.`start_date` AS invoice_date,
                            'Opening Balance' AS creditor_invoice_type,
                             COALESCE (SUM(opening_balance),0) AS dr_amount,
                            '0' AS cr_amount
                            FROM (creditor)
                            WHERE creditor.creditor_id > 0
                            GROUP BY creditor.creditor_id

                            UNION ALL 

                            SELECT
                            `creditor`.`creditor_id` AS creditor_id,
                            `creditor`.`creditor_id` as invoice_id,
                            `creditor`.`creditor_id` as invoice_number,
                            `creditor`.`start_date` as invoice_date,
                            'Opening Balance Payment' AS creditor_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`creditor_payment_item`.`amount_paid`),0) AS cr_amount
                            FROM (creditor_payment_item,creditor_payment,creditor)
                            WHERE `creditor_payment_item`.`creditor_id` = `creditor`.`creditor_id` 
                            AND `creditor_payment_item`.`creditor_payment_id` = `creditor_payment`.`creditor_payment_id` 
                            AND creditor_payment_item.invoice_type = 2
                            GROUP BY creditor.creditor_id

                          ) AS data WHERE data.creditor_id = ".$creditor_id." GROUP BY data.invoice_number ";
                          $query = $this->db->query($select_statement);
                  return $query;


  }

  public function add_credit_note_item($creditor_id)
  {

    $amount = $this->input->post('amount');
		$creditor_invoice_id=$this->input->post('invoice_id');
    $description = $this->input->post('description');
		$tax_type_id=$this->input->post('tax_type_id');

    if($tax_type_id == 0)
    {
      $amount = $amount;
      $vat = 0;
    }
    else if($tax_type_id == 1)
    {

      $vat = $amount *0.16;
      $amount = $amount*1.16;
    }
    else if($tax_type_id == 2){

      $vat = $amount*0.05;
      $amount = $amount *1.05;
    }

    // var_dump($amount);die();


		$service = array(
							'creditor_invoice_id'=>$creditor_invoice_id,
							'creditor_credit_note_item_status' => 0,
              'creditor_credit_note_id' => 0,
							'creditor_id' => $creditor_id,
              'description'=>$description,
							'created_by' => $this->session->userdata('personnel_id'),
							'created' => date('Y-m-d'),
              'credit_note_amount'=>$amount,
              'credit_note_charged_vat'=>$vat,
              'vat_type_id'=>$tax_type_id
						);


		$this->db->insert('creditor_credit_note_item',$service);
		return TRUE;

  }


  public function confirm_creditor_credit_note($creditor_id,$personnel_id = NULL)
  {
    $amount = $this->input->post('amount');
    $amount_charged = $this->input->post('amount_charged');
    $invoice_date = $this->input->post('credit_note_date');
    $vat_charged = $this->input->post('vat_charged');
    $invoice_number = $this->input->post('credit_note_number');

    $date_check = explode('-', $invoice_date);
    $month = $date_check[1];
    $year = $date_check[0];


    $document_number = $this->create_credit_note_number();

    // var_dump($checked); die();

    $insertarray['transaction_date'] = $invoice_date;
    $insertarray['invoice_year'] = $year;
    $insertarray['invoice_month'] = $month;
    $insertarray['creditor_id'] = $creditor_id;
    $insertarray['document_number'] = $document_number;
    $insertarray['invoice_number'] = strtoupper($invoice_number);
    $insertarray['total_amount'] = $amount_charged;
    $insertarray['vat_charged'] = $vat_charged;
    $insertarray['created_by'] = $this->session->userdata('personnel_id');
    $insertarray['created'] = date('Y-m-d');
    $insertarray['amount'] = $amount;
    $insertarray['account_from_id'] = 83;


    if($this->db->insert('creditor_credit_note', $insertarray))
    {
      $creditor_invoice_id = $this->db->insert_id();
      $service = array(
                'creditor_credit_note_id'=>$creditor_invoice_id,
                'created' =>$invoice_date,
                'creditor_credit_note_item_status'=>1,
                'year'=>$year,
                'month'=>$month,
              );
              // var_dump($service);die();
      $this->db->where('creditor_credit_note_item_status = 0 AND creditor_id = '.$creditor_id.' AND creditor_credit_note_id = 0  ');
      $this->db->update('creditor_credit_note_item',$service);


      return TRUE;
    }

  }

  public function create_credit_note_number()
	{
		//select product code
		$this->db->where('creditor_invoice_id > 0');
		$this->db->from('creditor_invoice');
		$this->db->select('MAX(document_number) AS number');
		$this->db->order_by('creditor_invoice_id','DESC');
		// $this->db->limit(1);
		$query = $this->db->get();
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			// var_dump($number);die();
			$number++;

		}
		else{
			$number = 1;
		}
		// var_dump($number);die();
		return $number;
	}


  public function get_creditor_credit_notes($creditor_id,$limit=null)
	{
		$this->db->where('creditor_credit_note.creditor_credit_note_id = creditor_credit_note_item.creditor_credit_note_id AND creditor_credit_note.creditor_credit_note_status = 1 AND creditor_credit_note.creditor_id = '.$creditor_id);
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->group_by('creditor_credit_note.creditor_credit_note_id');
		$this->db->order_by('creditor_credit_note.transaction_date','DESC');
		return $this->db->get('creditor_credit_note_item,creditor_credit_note');
	}


  public function get_creditor_payments($creditor_id,$limit=null)
  {
    $this->db->where('creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id AND creditor_payment.creditor_payment_status = 1 AND creditor_payment.creditor_id = '.$creditor_id);
    if($limit)
    {
      $this->db->limit($limit);
    }
    $this->db->group_by('creditor_payment.creditor_payment_id');
    $this->db->join('account','account.account_id = creditor_payment.account_from_id','left');
    $this->db->order_by('creditor_payment.transaction_date','DESC');
    return $this->db->get('creditor_payment_item,creditor_payment');
  }

  public function add_payment_item($creditor_id,$creditor_payment_id)
  {

    $amount = $this->input->post('amount_paid');
    $creditor_invoice_id = $this->input->post('invoice_id');



    // if(empty($creditor_invoice_id))
    // {
    //   $invoice_type = 2;
    // }
    // else
    // {
      $exploded = explode('.', $creditor_invoice_id);
      $invoice_id = $exploded[0];
      $invoice_number = $exploded[1];
      $invoice_type = $exploded[2];

    // }

    $service = array(
              'creditor_invoice_id'=>$invoice_id,
              'invoice_number'=>$invoice_number,
              'invoice_type'=>$invoice_type,
              'creditor_payment_item_status' => 0,
              'creditor_payment_id' => 0,
              'creditor_id' => $creditor_id,
              'created_by' => $this->session->userdata('personnel_id'),
              'created' => date('Y-m-d'),
              'amount_paid'=>$amount,
            );
            // var_dump($service);die();

    if(!empty($creditor_payment_id))
    {
      $service['creditor_payment_id'] = $creditor_payment_id;
      $service['creditor_payment_item_status'] = 1;
    }

    $this->db->insert('creditor_payment_item',$service);
    return TRUE;

  }

  public function confirm_creditor_payment($creditor_id,$creditor_payment_id)
  {
    $amount_paid = $this->input->post('amount_paid');
    $payment_date = $this->input->post('payment_date');
    $reference_number = $this->input->post('reference_number');
    $account_from_id = $this->input->post('account_from_id');

    $date_check = explode('-', $payment_date);
    $month = $date_check[1];
    $year = $date_check[0];


    // var_dump($year);die();

    if(!empty($creditor_payment_id))
    {
      $updatearray['transaction_date'] = $payment_date;
      $updatearray['payment_year'] = $year;
      $updatearray['payment_month'] = $month;
      $updatearray['creditor_id'] = $creditor_id;
      $updatearray['reference_number'] = strtoupper($reference_number);
      $updatearray['total_amount'] = $amount_paid;
      $updatearray['account_from_id'] = $account_from_id;
      
      $this->db->where('creditor_payment_id',$creditor_payment_id);
      if($this->db->update('creditor_payment', $updatearray))
      {
        return TRUE;
      }
      else
      {
        return FALSE;
      }

    }

    else
    {
      $document_number = $this->create_credit_payment_number();

      $insertarray['transaction_date'] = $payment_date;
      $insertarray['payment_year'] = $year;
      $insertarray['payment_month'] = $month;
      $insertarray['creditor_id'] = $creditor_id;
      $insertarray['document_number'] = $document_number;
      $insertarray['reference_number'] = strtoupper($reference_number);
      $insertarray['total_amount'] = $amount_paid;
      $insertarray['account_from_id'] = $account_from_id;
      $insertarray['created_by'] = $this->session->userdata('personnel_id');
      $insertarray['created'] = date('Y-m-d');

      if($this->db->insert('creditor_payment', $insertarray))
      {
        $creditor_payment_id = $this->db->insert_id();


        $total_visits = sizeof($_POST['creditor_payments_items']);

        //check if any checkboxes have been ticked
        if($total_visits > 0)
        {
          for($r = 0; $r < $total_visits; $r++)
          {
            $visit = $_POST['creditor_payments_items'];
            $creditor_payment_item_id = $visit[$r];
            //check if card is held
            $service = array(
                      'creditor_payment_id'=>$creditor_payment_id,
                      'created' =>$payment_date,
                      'creditor_payment_item_status'=>1,
                      'year'=>$year,
                      'month'=>$month,
                    );
            $this->db->where('creditor_payment_item_id',$creditor_payment_item_id);
            $this->db->update('creditor_payment_item',$service);
          }
        }



        return TRUE;
      }
    }

    
  }

  public function create_credit_payment_number()
  {
    //select product code
    $this->db->where('creditor_payment_id > 0');
    $this->db->from('creditor_payment');
    $this->db->select('MAX(document_number) AS number');
    $this->db->order_by('creditor_payment_id','DESC');
    // $this->db->limit(1);
    $query = $this->db->get();
    // var_dump($query); die();
    if($query->num_rows() > 0)
    {
      $result = $query->result();
      $number =  $result[0]->number;
      // var_dump($number);die();
      $number++;

    }
    else{
      $number = 1;
    }
    // var_dump($number);die();
    return $number;
  }




  /*
  * Add a new creditor
  *
  */
  public function add_creditor()
  {
    $creditor_type_id = $this->input->post('creditor_type_id');

    if(isset($creditor_type_id))
    {
      $creditor_type_id = 1;
    }
    else
    {
      $creditor_type_id = 0;
    }
    $data = array(
      'creditor_name'=>$this->input->post('creditor_name'),
      'creditor_email'=>$this->input->post('creditor_email'),
      'creditor_phone'=>$this->input->post('creditor_phone'),
      'creditor_location'=>$this->input->post('creditor_location'),
      'creditor_building'=>$this->input->post('creditor_building'),
      'creditor_floor'=>$this->input->post('creditor_floor'),
      'creditor_address'=>$this->input->post('creditor_address'),
      'creditor_post_code'=>$this->input->post('creditor_post_code'),
      'creditor_city'=>$this->input->post('creditor_city'),
      'opening_balance'=>$this->input->post('opening_balance'),
      'start_date'=>$this->input->post('creditor_account_date'),
      'creditor_contact_person_name'=>$this->input->post('creditor_contact_person_name'),
      'creditor_contact_person_onames'=>$this->input->post('creditor_contact_person_onames'),
      'creditor_contact_person_phone1'=>$this->input->post('creditor_contact_person_phone1'),
      'creditor_contact_person_phone2'=>$this->input->post('creditor_contact_person_phone2'),
      'creditor_contact_person_email'=>$this->input->post('creditor_contact_person_email'),
      'creditor_description'=>$this->input->post('creditor_description'),
      'branch_code'=>$this->session->userdata('branch_code'),
      'created_by'=>$this->session->userdata('creditor_id'),
      'debit_id'=>$this->input->post('debit_id'),
      'modified_by'=>$this->session->userdata('creditor_id'),
      'creditor_type_id'=>$creditor_type_id,
      'created'=>date('Y-m-d H:i:s')
    );

    if($this->db->insert('creditor', $data))
    {
      return $this->db->insert_id();
    }
    else{
      return FALSE;
    }
  }

  /*
  * Update an existing creditor
  * @param string $image_name
  * @param int $creditor_id
  *
  */
  public function edit_creditor($creditor_id)
  {
    $data = array(
      'creditor_name'=>$this->input->post('creditor_name'),
      'creditor_email'=>$this->input->post('creditor_email'),
      'creditor_phone'=>$this->input->post('creditor_phone'),
      'creditor_location'=>$this->input->post('creditor_location'),
      'creditor_building'=>$this->input->post('creditor_building'),
      'creditor_floor'=>$this->input->post('creditor_floor'),
      'creditor_address'=>$this->input->post('creditor_address'),
      'creditor_post_code'=>$this->input->post('creditor_post_code'),
      'creditor_city'=>$this->input->post('creditor_city'),
      'opening_balance'=>$this->input->post('opening_balance'),
      'start_date'=>$this->input->post('creditor_account_date'),
      'creditor_contact_person_name'=>$this->input->post('creditor_contact_person_name'),
      'creditor_contact_person_onames'=>$this->input->post('creditor_contact_person_onames'),
      'creditor_contact_person_phone1'=>$this->input->post('creditor_contact_person_phone1'),
      'creditor_contact_person_phone2'=>$this->input->post('creditor_contact_person_phone2'),
      'creditor_contact_person_email'=>$this->input->post('creditor_contact_person_email'),
      'creditor_description'=>$this->input->post('creditor_description'),
      'debit_id'=>$this->input->post('debit_id'),
      'modified_by'=>$this->session->userdata('creditor_id'),
    );

    $this->db->where('creditor_id', $creditor_id);
    if($this->db->update('creditor', $data))
    {
      return TRUE;
    }
    else{
      return FALSE;
    }
  }


  /*
  * get a single creditor's details
  * @param int $creditor_id
  *
  */
  public function get_creditor_account($creditor_id)
  {
    //retrieve all users
    $this->db->from('v_general_ledger');
    $this->db->select('SUM(dr_amount) AS total_invoice_amount');
    $this->db->where('transactionClassification = "Creditors Invoices" AND recepientId = '.$creditor_id);
    $query = $this->db->get();
    $invoices = $query->row();

    $total_invoice_amount = $invoices->total_invoice_amount;


    $this->db->from('v_general_ledger');
    $this->db->select('SUM(cr_amount) AS total_paid_amount');
    $this->db->where('transactionClassification = "Creditors Invoices Payments" AND recepientId = '.$creditor_id);
    $query = $this->db->get();
    $payments = $query->row();

    $total_paid_amount = $payments->total_paid_amount;


    $response['total_invoice'] = $total_invoice_amount;
    $response['total_paid_amount'] = $total_paid_amount;
    $response['total_credit_note'] = 0;

    return $response;
  }


}
?>
