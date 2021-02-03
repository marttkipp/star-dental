<?php

class Lenders_model extends CI_Model
{
  /*
  * Retrieve all lender
  * @param string $table
  *   @param string $where
  *
  */
  public function get_all_lenders($table, $where, $per_page, $page, $order = 'lender_name', $order_method = 'ASC')
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
	*	Add a new lender
	*
	*/
  public function get_lenders_list($table, $where, $order)
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
	*	get a single lender's details
	*	@param int $lender_id
	*
	*/
	public function get_lender($lender_id)
	{
		//retrieve all users
		$this->db->from('lender');
		$this->db->select('*');
		$this->db->where('lender_id = '.$lender_id);
		$query = $this->db->get();

		return $query;
	}


  public function add_invoice_item($lender_id,$lender_invoice_id)
	{
		$amount = $this->input->post('unit_price');
		$account_to_id=$this->input->post('account_to_id');
    $item_description = $this->input->post('item_description');
		$quantity=$this->input->post('quantity');
    $vat_amount = $this->input->post('vat_amount');
    $total_amount = $this->input->post('total_amount');
		$tax_type_id=$this->input->post('tax_type_id');


		$service = array(
							'lender_invoice_id'=>0,
							'unit_price'=> $amount,
							'account_to_id' => $account_to_id,
							'lender_id' => $lender_id,
              'item_description'=>$item_description,
							'created_by' => $this->session->userdata('personnel_id'),
							'created' => date('Y-m-d'),
              'total_amount'=>$total_amount,
              'vat_amount'=>$vat_amount,
              'quantity'=>$quantity,
              'vat_type_id'=>$tax_type_id
						);
    if(!empty($lender_invoice_id))
    {
      $service['lender_invoice_id'] = $lender_invoice_id;
      $service['lender_invoice_item_status'] = 1;
    }
    else
    {
      $service['lender_invoice_item_status'] = 0;
    }


		$this->db->insert('lender_invoice_item',$service);
		return TRUE;

	}

  public function confirm_lender_invoice($lender_id,$lender_invoice_id = NULL)
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
		$insertarray['lender_id'] = $lender_id;
		$insertarray['document_number'] = $document_number;
    $insertarray['invoice_number'] = strtoupper($invoice_number);
		$insertarray['total_amount'] = $amount_charged;
		$insertarray['vat_charged'] = $vat_charged;
		$insertarray['created_by'] = $this->session->userdata('personnel_id');
		$insertarray['created'] = date('Y-m-d');
		$insertarray['amount'] = $amount;

    if(!empty($lender_invoice_id))
    {
        $this->db->where('lender_invoice_id',$lender_invoice_id);
        if($this->db->update('lender_invoice', $insertarray))
        {

          $total_visits = sizeof($_POST['lender_invoice_items']);
          //check if any checkboxes have been ticked
          if($total_visits > 0)
          {
            for($r = 0; $r < $total_visits; $r++)
            {
              $visit = $_POST['lender_invoice_items'];
              $lender_invoice_item_id = $visit[$r];
              //check if card is held
              $service = array(
                        'lender_invoice_id'=>$lender_invoice_id,
                        'created' =>$invoice_date,
                        'lender_invoice_item_status'=>1,
                        'year'=>$year,
                        'month'=>$month,
                      );
              $this->db->where('lender_invoice_item_id',$lender_invoice_item_id);
              $this->db->update('lender_invoice_item',$service);
            }
          }

          return TRUE;
        }
    }
    else
    {
      if($this->db->insert('lender_invoice', $insertarray))
      {

        $lender_invoice_id = $this->db->insert_id();
        $total_visits = sizeof($_POST['lender_invoice_items']);
        //check if any checkboxes have been ticked
        if($total_visits > 0)
        {
          for($r = 0; $r < $total_visits; $r++)
          {
            $visit = $_POST['lender_invoice_items'];
            $lender_invoice_item_id = $visit[$r];
            //check if card is held
            $service = array(
                      'lender_invoice_id'=>$lender_invoice_id,
                      'created' =>$invoice_date,
                      'lender_invoice_item_status'=>1,
                      'year'=>$year,
                      'month'=>$month,
                    );
            $this->db->where('lender_invoice_item_id',$lender_invoice_item_id);
            $this->db->update('lender_invoice_item',$service);
          }
        }

        return TRUE;
      }
    }
		

	}

  public function create_invoice_number()
	{
		//select product code
		$this->db->where('lender_invoice_id > 0');
		$this->db->from('lender_invoice');
		$this->db->select('MAX(document_number) AS number');
		$this->db->order_by('lender_invoice_id','DESC');
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

  public function get_lender_invoice($lender_id,$limit=null)
	{
		$this->db->where('lender_invoice.lender_invoice_id = lender_invoice_item.lender_invoice_id AND lender_invoice.lender_invoice_status = 1 AND lender_invoice.lender_id = '.$lender_id);
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->group_by('lender_invoice.lender_invoice_id');
		$this->db->order_by('lender_invoice.transaction_date','DESC');
		return $this->db->get('lender_invoice_item,lender_invoice');
	}



  public function get_lender_invoice_number($lender_id,$limit=null)
  {
    // $this->db->where('v_lenders_invoice_balances.lender_id = '.$lender_id);
    // $this->db->select('*');
    // return $this->db->get('v_lenders_invoice_balances');

    $select_statement = "
                        SELECT
                          data.invoice_id AS lender_invoice_id,
                          data.invoice_number AS invoice_number,
                          data.invoice_date AS invoice_date,
                          data.lender_invoice_type AS lender_invoice_type,
                          COALESCE (SUM(data.dr_amount),0) AS dr_amount,
                          COALESCE (SUM(data.cr_amount),0) AS cr_amount,
                          COALESCE (SUM(data.dr_amount),0) - COALESCE (SUM(data.cr_amount),0) AS balance
                        FROM 
                        (
                          SELECT
                            `orders`.`supplier_id` AS lender_id,
                            `orders`.`order_id` as invoice_id,
                            `orders`.`order_number` as invoice_number,
                            `orders`.`supplier_invoice_date` as invoice_date,
                            'Lender Invoice' AS lender_invoice_type,
                            COALESCE (SUM(`product_deductions`.`total_amount`),0) AS dr_amount,
                            0 AS cr_amount
                            FROM (`orders`,product_deductions)
                            WHERE `product_deductions`.`order_id` = `orders`.`order_id`
                            AND orders.is_store = 2
                            AND orders.order_approval_status = 7
                            GROUP BY orders.order_id

                            UNION ALL 


                            SELECT
                            `lender_invoice`.`lender_id` AS lender_id,
                            `lender_invoice`.`lender_invoice_id` AS invoice_id,
                            `lender_invoice`.`invoice_number` AS invoice_number,
                            `lender_invoice`.`transaction_date` AS invoice_date,
                            'Lender Bills' AS lender_invoice_type,
                            COALESCE (SUM(`lender_invoice_item`.`total_amount`),0) AS dr_amount,
                            0 AS cr_amount
                            FROM (`lender_invoice`,lender_invoice_item)
                            WHERE `lender_invoice_item`.`lender_invoice_id` = `lender_invoice`.`lender_invoice_id` AND lender_invoice.lender_invoice_status = 1
                            GROUP BY `lender_invoice`.`lender_invoice_id`

                            UNION ALL 

                            SELECT
                            `lender_invoice`.`lender_id` AS lender_id,
                            `lender_invoice`.`lender_invoice_id` AS invoice_id,
                            `lender_invoice`.`invoice_number` AS invoice_number,
                            `lender_invoice`.`transaction_date` AS invoice_date,
                            'Lender Bills Credit Note' AS lender_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`lender_credit_note_item`.`credit_note_amount`),0) AS cr_amount
                            FROM (`lender_invoice`,lender_credit_note,lender_credit_note_item)
                            WHERE `lender_credit_note_item`.`lender_credit_note_id` = `lender_credit_note`.`lender_credit_note_id`
                            AND `lender_invoice`.`lender_invoice_id` = `lender_credit_note_item`.`lender_invoice_id` AND lender_credit_note.lender_credit_note_status = 1
                            GROUP BY `lender_credit_note_item`.`lender_invoice_id`

                            UNION ALL


                            SELECT
                            `lender_invoice`.`lender_id` AS lender_id,
                            `lender_invoice`.`lender_invoice_id` AS invoice_id,
                            `lender_invoice`.`invoice_number` AS invoice_number,
                            `lender_invoice`.`transaction_date` AS invoice_date,
                            'Bill Payments' AS lender_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`lender_payment_item`.`amount_paid`),0) AS cr_amount
                            FROM (lender_payment_item,lender_payment,lender_invoice)
                            WHERE `lender_payment_item`.`lender_invoice_id` = `lender_invoice`.`lender_invoice_id` 
                            AND `lender_payment_item`.`lender_payment_id` = `lender_payment`.`lender_payment_id` AND lender_payment_item.invoice_type = 0 AND lender_payment.lender_payment_status = 1
                            GROUP BY lender_invoice.lender_invoice_id

                            UNION ALL 

                             SELECT
                            `lender`.`lender_id` AS lender_id,
                            `lender`.`lender_id` AS invoice_id,
                            `lender`.`lender_id` AS invoice_number,
                            `lender`.`start_date` AS invoice_date,
                            'Opening Balance' AS lender_invoice_type,
                             COALESCE (SUM(opening_balance),0) AS dr_amount,
                            '0' AS cr_amount
                            FROM (lender)
                            WHERE lender.lender_id > 0
                            GROUP BY lender.lender_id

                            UNION ALL 

                            SELECT
                            `lender`.`lender_id` AS lender_id,
                            `lender`.`lender_id` as invoice_id,
                            `lender`.`lender_id` as invoice_number,
                            `lender`.`start_date` as invoice_date,
                            'Opening Balance Payment' AS lender_invoice_type,
                            0 AS dr_amount,
                            COALESCE (SUM(`lender_payment_item`.`amount_paid`),0) AS cr_amount
                            FROM (lender_payment_item,lender_payment,lender)
                            WHERE `lender_payment_item`.`lender_id` = `lender`.`lender_id` 
                            AND `lender_payment_item`.`lender_payment_id` = `lender_payment`.`lender_payment_id` 
                            AND lender_payment_item.invoice_type = 2 AND lender_payment.lender_payment_status = 1
                            GROUP BY lender.lender_id

                          ) AS data WHERE data.lender_id = ".$lender_id."  GROUP BY data.invoice_number ORDER BY data.invoice_date ASC ";
                          $query = $this->db->query($select_statement);
                  return $query;


  }

  public function add_credit_note_item($lender_id,$lender_credit_note_id)
  {

    $amount = $this->input->post('amount');
		$account_to_id=$this->input->post('account_to_id');
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
							'lender_id' => $lender_id,
              'account_to_id' => $account_to_id,
              'description'=>$description,
							'created_by' => $this->session->userdata('personnel_id'),
							'created' => date('Y-m-d'),
              'credit_note_amount'=>$amount,
              'credit_note_charged_vat'=>$vat,
              'vat_type_id'=>$tax_type_id
						);

    if(!empty($lender_credit_note_id))
    {
      $service['lender_credit_note_id'] = $lender_credit_note_id;
      $service['lender_credit_note_item_status'] = 1;
    }
    else
    {
       $service['lender_credit_note_item_status'] = 0;
    }

		$this->db->insert('lender_credit_note_item',$service);
		return TRUE;

  }


  public function confirm_lender_credit_note($lender_id,$lender_credit_note_id)
  {
    $amount = $this->input->post('amount');
    $amount_charged = $this->input->post('amount_charged');
    $invoice_date = $this->input->post('credit_note_date');
    $lender_invoice_id = $this->input->post('invoice_id');
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
    $insertarray['lender_id'] = $lender_id;
    $insertarray['lender_invoice_id'] = $lender_invoice_id;
    $insertarray['document_number'] = $document_number;
    $insertarray['invoice_number'] = strtoupper($invoice_number);
    $insertarray['total_amount'] = $amount_charged;
    $insertarray['vat_charged'] = $vat_charged;
 
    $insertarray['amount'] = $amount;
    $insertarray['account_from_id'] = 83;


     $total_visits = sizeof($_POST['lender_notes_items']);

     // var_dump($total_visits);die();

     if(!empty($lender_credit_note_id))
     {
        $this->db->where('lender_credit_note_id',$lender_credit_note_id);
        if($this->db->update('lender_credit_note', $insertarray))
        {


          $total_visits = sizeof($_POST['lender_notes_items']);
          //check if any checkboxes have been ticked
          if($total_visits > 0)
          {
            for($r = 0; $r < $total_visits; $r++)
            {
              $visit = $_POST['lender_notes_items'];
              $lender_credit_note_item_id = $visit[$r];
              //check if card is held
              $service = array(
                        'lender_credit_note_id'=>$lender_credit_note_id,
                        'created' =>$invoice_date,
                        'lender_credit_note_item_status'=>1,
                        'lender_invoice_id'=>$lender_invoice_id,
                        'year'=>$year,
                        'month'=>$month,
                      );
              $this->db->where('lender_credit_note_item_id',$lender_credit_note_item_id);
              $this->db->update('lender_credit_note_item',$service);
            }
          }
          return TRUE;
        }
     }
     else
     {

        $insertarray['created_by'] = $this->session->userdata('personnel_id');
        $insertarray['created'] = date('Y-m-d');
        if($this->db->insert('lender_credit_note', $insertarray))
        {
          $lender_credit_note_id = $this->db->insert_id();


          $total_visits = sizeof($_POST['lender_notes_items']);
          //check if any checkboxes have been ticked
          if($total_visits > 0)
          {
            for($r = 0; $r < $total_visits; $r++)
            {
              $visit = $_POST['lender_notes_items'];
              $lender_credit_note_item_id = $visit[$r];
              //check if card is held
              $service = array(
                        'lender_credit_note_id'=>$lender_credit_note_id,
                        'created' =>$invoice_date,
                        'lender_credit_note_item_status'=>1,
                        'lender_invoice_id'=>$lender_invoice_id,
                        'year'=>$year,
                        'month'=>$month,
                      );
              $this->db->where('lender_credit_note_item_id',$lender_credit_note_item_id);
              $this->db->update('lender_credit_note_item',$service);
            }
          }
          return TRUE;
        }
     }

  }

  public function create_credit_note_number()
	{
		//select product code
		$this->db->where('lender_invoice_id > 0');
		$this->db->from('lender_invoice');
		$this->db->select('MAX(document_number) AS number');
		$this->db->order_by('lender_invoice_id','DESC');
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


  public function get_lender_credit_notes($lender_id,$limit=null)
	{
		$this->db->where('lender_credit_note.lender_credit_note_id = lender_credit_note_item.lender_credit_note_id AND lender_credit_note.lender_credit_note_status = 1 AND lender_credit_note.lender_id = '.$lender_id);
		if($limit)
		{
			$this->db->limit($limit);
		}
		$this->db->group_by('lender_credit_note.lender_credit_note_id');
		$this->db->order_by('lender_credit_note.transaction_date','DESC');
		return $this->db->get('lender_credit_note_item,lender_credit_note');
	}


  public function get_lender_payments($lender_id,$limit=null)
  {
    $this->db->where('lender_payment.lender_payment_id = lender_payment_item.lender_payment_id AND lender_payment.lender_payment_status = 1 AND lender_payment.lender_id = '.$lender_id);
    if($limit)
    {
      $this->db->limit($limit);
    }
    $this->db->group_by('lender_payment.lender_payment_id');
    $this->db->join('account','account.account_id = lender_payment.account_from_id','left');
    $this->db->order_by('lender_payment.transaction_date','DESC');
    return $this->db->get('lender_payment_item,lender_payment');
  }

  public function add_payment_item($lender_id,$lender_payment_id)
  {

    $amount = $this->input->post('amount_paid');
    $lender_invoice_id = $this->input->post('invoice_id');



    // if(empty($lender_invoice_id))
    // {
    //   $invoice_type = 2;
    // }
    // else
    // {
      $exploded = explode('.', $lender_invoice_id);
      $invoice_id = $exploded[0];
      $invoice_number = $exploded[1];
      $invoice_type = $exploded[2];

    // }

    $service = array(
              'lender_invoice_id'=>$invoice_id,
              'invoice_number'=>$invoice_number,
              'invoice_type'=>$invoice_type,
              'lender_payment_item_status' => 0,
              'lender_payment_id' => 0,
              'lender_id' => $lender_id,
              'created_by' => $this->session->userdata('personnel_id'),
              'created' => date('Y-m-d'),
              'amount_paid'=>$amount,
            );
            // var_dump($service);die();

    if(!empty($lender_payment_id))
    {
      $service['lender_payment_id'] = $lender_payment_id;
      $service['lender_payment_item_status'] = 1;
    }

    $this->db->insert('lender_payment_item',$service);
    return TRUE;

  }

  public function confirm_lender_payment($lender_id,$lender_payment_id)
  {
    $amount_paid = $this->input->post('amount_paid');
    $payment_date = $this->input->post('payment_date');
    $reference_number = $this->input->post('reference_number');
    $account_from_id = $this->input->post('account_from_id');

    $date_check = explode('-', $payment_date);
    $month = $date_check[1];
    $year = $date_check[0];


    // var_dump($year);die();

    if(!empty($lender_payment_id))
    {
     

      // $document_number = $this->create_credit_payment_number();

      $insertarray['transaction_date'] = $payment_date;
      $insertarray['payment_year'] = $year;
      $insertarray['payment_month'] = $month;
      $insertarray['lender_id'] = $lender_id;
      $insertarray['reference_number'] = strtoupper($reference_number);
      $insertarray['total_amount'] = $amount_paid;
      $insertarray['account_from_id'] = $account_from_id;
      $insertarray['created_by'] = $this->session->userdata('personnel_id');
       $this->db->where('lender_payment_id',$lender_payment_id);

      if($this->db->update('lender_payment', $insertarray))
      {


        $total_visits = sizeof($_POST['lender_payments_items']);

        //check if any checkboxes have been ticked
        if($total_visits > 0)
        {
          for($r = 0; $r < $total_visits; $r++)
          {
            $visit = $_POST['lender_payments_items'];
            $lender_payment_item_id = $visit[$r];
            //check if card is held
            $service = array(
                      'lender_payment_id'=>$lender_payment_id,
                      'created' =>$payment_date,
                      'lender_payment_item_status'=>1,
                      'year'=>$year,
                      'month'=>$month,
                    );
            $this->db->where('lender_payment_item_id',$lender_payment_item_id);
            $this->db->update('lender_payment_item',$service);
          }
        }



          return TRUE;
      }

    }

    else
    {
      $document_number = $this->create_credit_payment_number();

      $insertarray['transaction_date'] = $payment_date;
      $insertarray['payment_year'] = $year;
      $insertarray['payment_month'] = $month;
      $insertarray['lender_id'] = $lender_id;
      $insertarray['document_number'] = $document_number;
      $insertarray['reference_number'] = strtoupper($reference_number);
      $insertarray['total_amount'] = $amount_paid;
      $insertarray['account_from_id'] = $account_from_id;
      $insertarray['created_by'] = $this->session->userdata('personnel_id');
      $insertarray['created'] = date('Y-m-d');

      if($this->db->insert('lender_payment', $insertarray))
      {
        $lender_payment_id = $this->db->insert_id();


        $total_visits = sizeof($_POST['lender_payments_items']);

        //check if any checkboxes have been ticked
        if($total_visits > 0)
        {
          for($r = 0; $r < $total_visits; $r++)
          {
            $visit = $_POST['lender_payments_items'];
            $lender_payment_item_id = $visit[$r];
            //check if card is held
            $service = array(
                      'lender_payment_id'=>$lender_payment_id,
                      'created' =>$payment_date,
                      'lender_payment_item_status'=>1,
                      'year'=>$year,
                      'month'=>$month,
                    );
            $this->db->where('lender_payment_item_id',$lender_payment_item_id);
            $this->db->update('lender_payment_item',$service);
          }
        }



        return TRUE;
      }
    }

    
  }

  public function create_credit_payment_number()
  {
    //select product code
    $this->db->where('lender_payment_id > 0');
    $this->db->from('lender_payment');
    $this->db->select('MAX(document_number) AS number');
    $this->db->order_by('lender_payment_id','DESC');
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
  * Add a new lender
  *
  */
  public function add_lender()
  {
    $lender_type_id = $this->input->post('lender_type_id');

    if(isset($lender_type_id))
    {
      $lender_type_id = 1;
    }
    else
    {
      $lender_type_id = 0;
    }
    $data = array(
      'lender_name'=>$this->input->post('lender_name'),
      'lender_email'=>$this->input->post('lender_email'),
      'lender_phone'=>$this->input->post('lender_phone'),
      'lender_location'=>$this->input->post('lender_location'),
      'lender_building'=>$this->input->post('lender_building'),
      'lender_floor'=>$this->input->post('lender_floor'),
      'lender_address'=>$this->input->post('lender_address'),
      'lender_post_code'=>$this->input->post('lender_post_code'),
      'lender_city'=>$this->input->post('lender_city'),
      'opening_balance'=>$this->input->post('opening_balance'),
      'start_date'=>$this->input->post('lender_account_date'),
      'lender_contact_person_name'=>$this->input->post('lender_contact_person_name'),
      'lender_contact_person_onames'=>$this->input->post('lender_contact_person_onames'),
      'lender_contact_person_phone1'=>$this->input->post('lender_contact_person_phone1'),
      'lender_contact_person_phone2'=>$this->input->post('lender_contact_person_phone2'),
      'lender_contact_person_email'=>$this->input->post('lender_contact_person_email'),
      'lender_description'=>$this->input->post('lender_description'),
      'branch_code'=>$this->session->userdata('branch_code'),
      'created_by'=>$this->session->userdata('lender_id'),
      'debit_id'=>$this->input->post('debit_id'),
      'modified_by'=>$this->session->userdata('lender_id'),
      'lender_type_id'=>$lender_type_id,
      'created'=>date('Y-m-d H:i:s')
    );

    // var_dump($lder)
    if($this->db->insert('lender', $data))
    {
      return $this->db->insert_id();
    }
    else{
      return FALSE;
    }
  }

  /*
  * Update an existing lender
  * @param string $image_name
  * @param int $lender_id
  *
  */
  public function edit_lender($lender_id)
  {
    $data = array(
      'lender_name'=>$this->input->post('lender_name'),
      'lender_email'=>$this->input->post('lender_email'),
      'lender_phone'=>$this->input->post('lender_phone'),
      'lender_location'=>$this->input->post('lender_location'),
      'lender_building'=>$this->input->post('lender_building'),
      'lender_floor'=>$this->input->post('lender_floor'),
      'lender_address'=>$this->input->post('lender_address'),
      'lender_post_code'=>$this->input->post('lender_post_code'),
      'lender_city'=>$this->input->post('lender_city'),
      'opening_balance'=>$this->input->post('opening_balance'),
      'start_date'=>$this->input->post('lender_account_date'),
      'lender_contact_person_name'=>$this->input->post('lender_contact_person_name'),
      'lender_contact_person_onames'=>$this->input->post('lender_contact_person_onames'),
      'lender_contact_person_phone1'=>$this->input->post('lender_contact_person_phone1'),
      'lender_contact_person_phone2'=>$this->input->post('lender_contact_person_phone2'),
      'lender_contact_person_email'=>$this->input->post('lender_contact_person_email'),
      'lender_description'=>$this->input->post('lender_description'),
      'debit_id'=>$this->input->post('debit_id'),
      'modified_by'=>$this->session->userdata('lender_id'),
    );

    $this->db->where('lender_id', $lender_id);
    if($this->db->update('lender', $data))
    {
      return TRUE;
    }
    else{
      return FALSE;
    }
  }


  /*
  * get a single lender's details
  * @param int $lender_id
  *
  */
  public function get_lender_account($lender_id)
  {
    //retrieve all users
    $this->db->from('v_general_ledger');
    $this->db->select('SUM(dr_amount) AS total_invoice_amount');
    $this->db->where('transactionClassification = "lenders Invoices" AND recepientId = '.$lender_id);
    $query = $this->db->get();
    $invoices = $query->row();

    $total_invoice_amount = $invoices->total_invoice_amount;


    $this->db->from('v_general_ledger');
    $this->db->select('SUM(cr_amount) AS total_paid_amount');
    $this->db->where('transactionClassification = "lenders Invoices Payments" AND recepientId = '.$lender_id);
    $query = $this->db->get();
    $payments = $query->row();

    $total_paid_amount = $payments->total_paid_amount;


    $response['total_invoice'] = $total_invoice_amount;
    $response['total_paid_amount'] = $total_paid_amount;
    $response['total_credit_note'] = 0;

    return $response;
  }



   /*
  * Retrieve all lender
  * @param string $table
  *   @param string $where
  *
  */
  public function get_all_lenders_details($table, $where, $per_page, $page, $order = 'lender_name', $order_method = 'ASC')
  {
    //retrieve all users
    $this->db->from($table);
    $this->db->select('*');
    $this->db->where($where);
    $this->db->order_by($order, $order_method);
    // $this->db->group_by('lender_invoice.lender_invoice_id');
    $query = $this->db->get('', $per_page, $page);

    return $query;
  }


  public function get_lender_invoice_details($lender_invoice_id)
  {

      $this->db->from('lender_invoice');
      $this->db->select('*');
      $this->db->where('lender_invoice_id = '.$lender_invoice_id);
      $query = $this->db->get();
      return $query;
  }

  public function get_lender_payment_details($lender_payment_id)
  {

      $this->db->from('lender_payment');
      $this->db->select('*');
      $this->db->where('lender_payment_id = '.$lender_payment_id);
      $query = $this->db->get();
      return $query;
  }

  public function check_on_account($lender_payment_id)
  {

     $this->db->from('lender_payment_item');
      $this->db->select('*');
      $this->db->where('invoice_type = 3 AND lender_payment_id = '.$lender_payment_id);
      $query = $this->db->get();
      if($query->num_rows() > 0)
      {
          return TRUE;
      }
      else
      {
        return FALSE;
      }


  }

  public function get_lender_credit_note_details($lender_credit_note_id)
  {

      $this->db->from('lender_credit_note');
      $this->db->select('*');
      $this->db->where('lender_credit_note_id = '.$lender_credit_note_id);
      $query = $this->db->get();
      return $query;
  }
  
  public function get_content($table, $where,$select,$group_by=NULL,$limit=NULL)
  {
    $this->db->from($table);
    $this->db->select($select);
    $this->db->where($where);
    if($group_by != NULL)
    {
      $this->db->group_by($group_by);
    }
    $query = $this->db->get('');
    
    return $query;
  }

}
?>
