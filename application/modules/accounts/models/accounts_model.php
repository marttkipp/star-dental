<?php
class Accounts_model extends CI_Model 
{
	public function payments2($visit_id)
	{
		$table = "payments";
		$where = "payments.visit_id =". $visit_id;
		$items = "payments.amount_paid,payments.payment_type";
		$order = "amount_paid";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		$total = 0;
		
		if(count($result) > 0){
			foreach ($result as $row2):
				$payment_type = $row2->payment_type;
				if($payment_type == 1)
				{
					$amount_paid = $row2->amount_paid;
					$total = $total + $amount_paid;
				}
			endforeach;
		}
		
		else{
			$total = 0;
		}
		
		$value = $total;
		
		return $value;
	}
	public function total_invoice($visit_id)
	{
		 $item_invoiced_rs = $this->get_patient_visit_charge_items($visit_id);
         $credit_note_amount = $this->get_sum_credit_notes($visit_id);
         $debit_note_amount = $this->get_sum_debit_notes($visit_id);
         $total = 0;
          $total_amount =  0;
          if(count($item_invoiced_rs) > 0){
            $s=0;
            
            foreach ($item_invoiced_rs as $key_items):
              $s++;
			  $visit_total = 0;
			  $service_id = $key_items->service_id;
			  $service_charge_id = $key_items->service_charge_id;
              $service_charge_name = $key_items->service_charge_name;
              $visit_charge_amount = $key_items->visit_charge_amount;
              $service_name = $key_items->service_name;
              $units = $key_items->visit_charge_units;
			  
			  //If pharmacy
			  	
					$visit_total = $visit_charge_amount * $units;
		
             // $visit_total = $visit_charge_amount * $units;
              $total = $total + $visit_total;
            endforeach;
            $total_amount = $total;
          }
          else
          {
          	$total_amount = 0;
          }
          $total_amount = ($total + $debit_note_amount) - $credit_note_amount;
          return $total_amount;
	}
	public function total_payments($visit_id)
	{
	      $payments_rs = $this->accounts_model->payments($visit_id);
	      $total_payments = 0;
	      
	      if(count($payments_rs) > 0)
	      {
	        $x=0;
	        
	          foreach ($payments_rs as $key_items):
	            $x++;
	                $payment_type = $key_items->payment_type;
	                $payment_status = $key_items->payment_status;
	                if($payment_type == 1 && $payment_status ==1)
	                {
	                  $payment_method = $key_items->payment_method;
	                  $amount_paid = $key_items->amount_paid;
	                  
	                  $total_payments = $total_payments + $amount_paid;
	                }
	          endforeach;
	                    
	      }
	      else
	      {
	      	$total_payments = 0;
	      }
	      return $total_payments;
	}

	public function visit_payments($visit_id)
	{
		$table = "payments, payment_method";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method.payment_method_id = payments.payment_method_id  AND payments.visit_id =". $visit_id;
		$items = "SUM(amount_paid) AS total_amount";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$amount_paid = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$amount_paid = $value->total_amount;
			}
		}
		return $amount_paid;
	}
	
	public function total($visit_id)
	{
	 	$total=""; 
	 	$temp="";
		
		//identify patient/visit type
		$visit_type_rs = $this->nurse_model->get_visit_type($visit_id);
		foreach ($visit_type_rs as $key):
			$visit_t = $key->visit_type;
		endforeach;
		//  get patient id 
		$patient_id = $this->nurse_model->get_patient_id($visit_id);
	
		//  get the visit type details
		$type_details_rs = $this->visit_type_details($visit_t);
		$num_type = count($type_details_rs);
		if($num_type > 0){
			foreach ($type_details_rs as $key_details):
				$visit_type_name = $key_details->visit_type_name;
			endforeach;
		}
		if ($visit_type_name=="Insurance")
		{
			//  get insuarance amounts 
			$insurance_rs = $this->get_service_charges_amounts($visit_id);
		    $num_rows = count($insurance_rs);
			foreach ($insurance_rs as $key_values):
				$service_id1  = $key_values->service_id;
				$visit_charge_amount  = $key_values->visit_charge_amount;
				$visit_charge_units  = $key_values->visit_charge_units;
				$discounted_value="";
				
				$dicount_rs = $this->get_dicountend_values($patient_id,$service_id1);
				foreach ($dicount_rs as $key_disounts):
					$percentage = $key_disounts->percentage;
					$amount = $key_disounts->amount;
				endforeach;
					$penn=((100-$percentage)/100);
					$discounted_value="";	
					if($percentage==0){
						$discounted_value=$amount;	
						$sum = $visit_charge_amount -$discounted_value;			
				
					}
					else if($amount==0){
						$discounted_value=$percentage;
						$sum = $visit_charge_amount *((100-$discounted_value)/100);
						$penn=((100-$discounted_value)/100);
					}
					else if(($amount==0)&&($percentage==0)){
						$sum=$visit_charge_amount;
					}
						
				$total=($sum*$visit_charge_units)+$temp;	$temp=$total;
						
			endforeach;
			return $total;
		}
		else
		{
			$amount_rs = $this->get_service_charges_amounts($visit_id);
		    $num_rows = count($amount_rs);
			foreach ($amount_rs as $key_values):
				$service_id1  = $key_values->service_id;
				$visit_charge_amount  = $key_values->visit_charge_amount;
				$visit_charge_units  = $key_values->visit_charge_units;
				$amount=$visit_charge_amount*$visit_charge_units;
				$total = $total + $amount;
						
			endforeach;
			return $total;
		}
	
	}
	function visit_type_details($visit_type_id){
		$table = "visit_type";
		$where = "visit_type.visit_type_id =". $visit_type_id;
		$items = "*";
		$order = "visit_type_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	
	function get_service_charges_amounts($visit_id)
	{
		$table = "visit_charge, service_charge";
		$where = "service_charge.service_charge_id = visit_charge.service_charge_id
		AND visit_charge.visit_id =". $visit_id;
		$items = "visit_charge.visit_charge_amount,visit_charge.visit_charge_units,visit_charge.service_charge_id,service_charge.service_id";
		$order = "visit_charge.service_charge_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
		
	}
	function get_dicountend_values($patient_id,$service_id)
	{
		$table = "insurance_discounts";
		$where = "insurance_id = (SELECT company_insurance_id FROM `patient_insurance` where patient_id = ". $patient_id .") and service_id = ". $service_id;
		$items = "*";
		$order = "insurance_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	function get_payment_methods()
	{
		$table = "payment_method";
		$where = "payment_method_id > 0";
		$items = "*";
		$order = "payment_method";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function balance($payments, $invoice_total)
	{
		
		$value = $payments - $invoice_total;
		if($value > 0){
			$value= '(-'.$value.')';
		}
		else{
			$value= -(1) * ($value);
		}
	
		return $value;
	}
	public function get_patient_visit_charge_items($visit_id)
	{
		$table = "visit_charge, service_charge, service,visit";
		$where = "visit_charge.visit_charge_units <> 0 AND visit.visit_id = visit_charge.visit_id AND service_charge.service_id = service.service_id AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_id =". $visit_id;
		$items = "service.service_id,service.service_name,service_charge.service_charge_name,visit_charge.service_charge_id,visit_charge.visit_charge_units, visit_charge.visit_charge_amount,visit_charge.teeth, visit_charge.visit_charge_timestamp,visit_charge.visit_charge_id,visit_charge.created_by, visit_charge.personnel_id";
		$order = "service.service_name";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}

	public function get_patient_visit_charge_items_receipt($visit_id)
	{
		$table = "visit_charge, service_charge, service";
		$where = "visit_charge.visit_charge_units <> 0 AND service_charge.service_id = service.service_id AND visit_charge.visit_charge_delete = 0  AND visit_charge.service_charge_id = service_charge.service_charge_id  AND visit_charge.visit_id =". $visit_id;
		$items = "service.service_id,service.service_name,service_charge.service_charge_name,visit_charge.service_charge_id,visit_charge.visit_charge_units, visit_charge.visit_charge_amount, visit_charge.visit_charge_timestamp,visit_charge.visit_charge_id,visit_charge.created_by, visit_charge.personnel_id";
		$order = "service.service_name";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_patient_visit_charge($visit_id)
	{
		$table = "visit_charge, service_charge, service";
		$where = "service_charge.service_id = service.service_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_id =". $visit_id;
		$items = "DISTINCT(service_charge.service_id) AS service_id, service.service_name,";
		$order = "service_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function total_debit_note_per_service($service_id,$visit_id){
		$table = "payments,payment_method";
		$where = "payment_method.payment_method_id = payments.payment_method_id AND payments.payment_type = 3  AND payments.visit_id =". $visit_id;
		$items = "SUM(amount_paid) AS total_debit";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_debit = 0;
		 if(count($result) > 0){
		 	foreach ($result as $key_items):
		 		$total_debit = $key_items->total_debit;
		    endforeach;
		 }
		 else
		 {
		 	$total_debit = 0;
		 }
		 return $total_debit;
	}
	public function total_credit_note_per_service($service_id,$visit_id){
		$table = "payments,payment_method";
		$where = "payment_method.payment_method_id = payments.payment_method_id AND payments.payment_type = 2  AND payments.visit_id =". $visit_id;
		$items = "SUM(amount_paid) AS total_credit";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_credit = 0;
		 if(count($result) > 0){
		 	foreach ($result as $key_items):
		 		$total_credit = $key_items->total_credit;
		    endforeach;
		 }
		 else
		 {
		 	$total_credit = 0;
		 }
		 return $total_credit;
	}
	public function payments($visit_id){
		$table = "payments, payment_method";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method.payment_method_id = payments.payment_method_id AND payments.visit_id =". $visit_id;
		$items = "*";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}


	public function get_all_visit_transactions($visit_id){
		$table = "payments, payment_method";
		$where = "payments.cancel = 0 AND payment_method.payment_method_id = payments.payment_method_id AND payments.visit_id =". $visit_id;
		$items = "*";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_sum_credit_notes($visit_id)
	{
		$table = "payments";
		$where = "payments.payment_type = 2 AND payments.cancel = 0 AND payments.visit_id =". $visit_id;
		$items = "SUM(payments.amount_paid) AS amount_paid";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$amount = $key->amount_paid;
				if(!is_numeric($amount))
				{
					return 0;
				}
				else
				{
					return $amount;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
	}
	public function get_sum_debit_notes($visit_id)
	{
		$table = "payments";
		$where = "payments.payment_type = 3 AND payments.cancel = 0 AND payments.visit_id =". $visit_id;
		$items = "SUM(payments.amount_paid) AS amount_paid";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$amount = $key->amount_paid;
				if(!is_numeric($amount))
				{
					return 0;
				}
				else
				{
					return $amount;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
	}
	public function  get_payment_peronnel($payment_id)
	{
		$table = "payments";
		$where = "payment_id =". $payment_id;
		$items = "payment_created_by";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$payment_created_by = $key->payment_created_by;
				if(!is_numeric($payment_created_by))
				{
					return 0;
				}
				else
				{
					return $payment_created_by;
				}
			endforeach;
		}
		else
		{
			return 0;
		}
	}
	public function receipt_payment($visit_id,$personnel_id = NULL){
		
		$payment_method=$this->input->post('payment_method');
		$type_payment=$this->input->post('type_payment');
		$payment_service_id=$this->input->post('payment_service_id');
		
		if($type_payment == 1)
		{
			$payment_service_id=$this->input->post('payment_service_id');
			$amount = $this->input->post('amount_paid');
		}
		
		else
		{
			$payment_service_id=$this->input->post('waiver_service_id');
			$amount = $this->input->post('waiver_amount');
		}
		
		if($payment_method == 1)
		{
			// check for cheque number if inserted
			
			$transaction_code = $this->input->post('cheque_number');
		}
		else if($payment_method == 6)
		{
			// check for insuarance number if inserted
			$transaction_code = $this->input->post('debit_card_detail');
		}
		else if($payment_method == 5)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('mpesa_code');
		}
		else if($payment_method == 7)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('deposit_detail');
		}
		else if($payment_method == 8)
		{
			//  check for mpesa code if inserted
			$transaction_code = $this->input->post('debit_card_detail');
		}
		else
		{
			$transaction_code = '';
		}

		$amount = str_replace(',', '', $amount);
		$change = $this->input->post('change_payment');

		$reason = $this->input->post('reason');

		$payments_value = $this->accounts_model->total_payments($visit_id);

		$invoice_total = $this->accounts_model->total_invoice($visit_id);

		$balance = $this->accounts_model->balance($payments_value,$invoice_total);
		if($change > 0 AND $payment_method == 2 AND $balance > 0)
		{
			$amount = $amount - $change;
		}
		else
		{
			$change = 0;
			$amount = $amount;
		}

		// $payment_date = $this->input->post('payment_date');

		// if(!empty($payment_date))
		// {
		// 	$payment_date = $payment_date;
		// }
		// else
		// {
			$payment_date = date('Y-m-d');
		// }
		$data = array(
			'visit_id' => $visit_id,
			'payment_method_id'=>$payment_method,
			'amount_paid'=>$amount,
			'personnel_id'=>$this->session->userdata("personnel_id"),
			'payment_type'=>$type_payment,
			'transaction_code'=>$transaction_code,
			'reason'=>$reason,
			'payment_service_id'=>$payment_service_id,
			'change'=>$change,
			'payment_created'=>%$payment_date,
			'payment_created_by'=>$this->session->userdata("personnel_id"),
			'approved_by'=>$personnel_id,'date_approved'=>date('Y-m-d')
		);
		if($type_payment == 1)
		{
			$data['confirm_number'] = $this->create_receipt_number();
		}
		// var_dump($data);die();
		if($this->db->insert('payments', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
	public function check_admin_person($username,$password)
	{
		$authorize_invoice_changes = $this->session->userdata('authorize_invoice_changes');
		
		if($authorize_invoice_changes != 1)
		{
			$password = md5($password);
			$table = "personnel,personnel_department";
			$where = "personnel.personnel_username = '$username' AND personnel.personnel_password = '$password'  AND personnel.personnel_id = personnel_department.personnel_id AND personnel_department.department_id = 3";
			$items = "personnel.personnel_id";
			$order = "personnel.personnel_id";
			
			$result = $this->database->select_entries_where($table, $where, $items, $order);
			
			if(count($result) > 0)
			{
				foreach ($result as $row2):
					$personnel_id = $row2->personnel_id;
				endforeach;
				return $personnel_id;	
			}
			else{
				return FALSE;
			}
		}
		
		else
		{
			$personnel_id = $this->session->userdata('personnel_id');
			
			return $personnel_id;
		}
	}
	public function add_billing($visit_id)
	{
		$billing_method_id = $this->input->post('billing_method_id');
		$data = array('bill_to_id' => $billing_method_id);
		
		$this->db->where('visit_id', $visit_id);
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function add_service_item()
	{
		$parent_service_id = $this->input->post('parent_service_id');
		$service_charge_item = $this->input->post('service_charge_item');
		$service_amount = $this->input->post('service_amount');
		

		$this->db->where('visit_type_status', 1);
		$visit_type_query = $this->db->get('visit_type');

		if($visit_type_query->num_rows() > 0)
		{
			// foreach ($visit_type_query->result() as $key) {
			
				// $visit_type_id = $key->visit_type_id;
				// service charge entry
				$service_charge_insert = array(
								"service_charge_name" => $service_charge_item,
								"service_id" => $parent_service_id,
								"visit_type_id" => 1,
								"service_charge_amount" => $service_amount,
								'service_charge_status' => 1,
							);
				
				if($this->service_charge_exists($service_charge_item, 1))
				{
					$this->db->where(array('service_charge_name' => $service_charge_item, 'visit_type_id' => $visit_type_id));
					if($this->db->update('service_charge', $service_charge_insert))
					{
						
					}
					
					else
					{
					}
				}
				
				else
				{
					$service_charge_insert['created'] = date('Y-m-d H:i:s');
					$service_charge_insert['created_by'] = $this->session->userdata('personnel_id');
					$service_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
					// var_dump($service_charge_insert); die();
					if($this->db->insert('service_charge', $service_charge_insert))
					{
						
					}
					
					else
					{
					}
				}
			// }
			return TRUE;
		}
		else
		{
			return FALSE;
		}

		
	}

	public function service_charge_exists($service_charge_name, $visit_type_id)
	{
		$this->db->where(array('service_charge_name' => $service_charge_name, 'visit_type_id' => $visit_type_id, 'service_charge_delete' => 0));
		$query = $this->db->get('service_charge');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	
	public function get_att_doctor($visit_id)
	{
		$this->db->select('personnel.personnel_fname, personnel.personnel_onames');
		$this->db->from('personnel, visit');
		$this->db->where('personnel.personnel_id = visit.personnel_id AND visit.visit_id = '.$visit_id);
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$doctor = $row->personnel_onames.' '.$row->personnel_fname;
		}
		
		else
		{
			$doctor = '-';
		}
		
		return $doctor;
	}
	
	public function get_personnel($personnel_id)
	{
		if(empty($personnel_id))
		{
			//redirect('login');
			$personnel = '-';
		}
		
		else
		{
			$this->db->select('personnel.personnel_fname, personnel.personnel_onames');
			$this->db->from('personnel');
			$this->db->where('personnel.personnel_id = '.$personnel_id);
			
			$query = $this->db->get();
			
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$personnel = $row->personnel_onames.' '.$row->personnel_fname;
			}
			
			else
			{
				$personnel = '-';
			}
			
			return $personnel;
		}
	}
	
	public function get_visit_date($visit_id)
	{
		$this->db->select('visit_date');
		$this->db->from('visit');
		$this->db->where('visit_id = '.$visit_id);
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$visit_date = $row->visit_date;
		}
		
		else
		{
			$visit_date = '-';
		}
		
		return $visit_date;
	}
	public function get_visit_details($visit_id)
	{
		$this->db->select('*');
		$this->db->from('visit');
		$this->db->where('visit_id = '.$visit_id);
		
		$query = $this->db->get();
		
		
		return $query;
	}
	
	public function end_visit($visit_id)
	{
		$data = array(
        	"close_card" => 1,
        	"visit_time_out" => date('Y-m-d H:i:s')
    	);
		
		$this->db->where('visit_id', $visit_id);
		
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}


	public function close_visit($visit_id)
	{
		// $data = array(
  //       	"close_card" => 0,
  //       	"visit_time_out" => date('Y-m-d H:i:s')
  //   	);
		
		$this->db->where('visit_id', $visit_id);
		
		if($this->db->delete('visit_bill', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}

	public function end_visit_with_status($visit_id,$status)
	{
		$data = array(
        	"close_card" => $status
    	);
		
		$this->db->where('visit_id', $visit_id);
		
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}

	public function discharge_visit_with_status($visit_id,$status,$visit_date)
	{
		$data = array(
        	"close_card" => $status,
        	"visit_time_out" => $visit_date.' '.date('H:i:s')
    	);
		
		$this->db->where('visit_id', $visit_id);
		
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function get_billing_methods()
	{
		$this->db->order_by('bill_to_name');
		$query = $this->db->get('bill_to');
		
		return $query;
	}
	
	public function get_bill_to($visit_id)
	{
		$this->db->where('visit_id', $visit_id);
		$query = $this->db->get('visit');
		$row = $query->row();
		return $row->bill_to_id;
	}

	public function get_billing_info($visit_id)
	{
		$this->db->where('visit_id', $visit_id);
		$query = $this->db->get('visit');
		$row = $query->row();
		return $row->payment_info;
	}
	public function get_all_service($patient_id=null)
	{


		$table = "service";
		$where = "service_delete = 0";
		$items = "*";
		$order = "service_id";
		
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function get_service_detail($service_id)
	{
		$table = "service";
		$where = "service_id = ".$service_id;
		$items = "*";
		$order = "service_id";
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				# code...
				$service_name = $key->service_name;
			endforeach;
		}
		else
		{
			$service_name = "";
		}
		return  $service_name;
	}
	public function get_all_notes($visit_id)
	{
		$table = "payments, service";
		$where = "payments.payment_service_id = service.service_id AND (payments.payment_type = 2 OR payments.payment_type = 3) AND payments.visit_id = ". $visit_id;
		
		$this->db->select('service.service_name, payments.payment_service_id, payments.amount_paid, payments.payment_type');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	
	public function in_pres($service_charge_id, $visit_id)
	{
		$table = "pres, visit_charge";
		//$where = "pres.service_charge_id = visit_charge.service_charge_id AND pres.service_charge_id = ". $service_charge_id." AND pres.visit_id = ". $visit_id." AND visit_charge.visit_id = ". $visit_id;
		$where = "pres.service_charge_id = visit_charge.service_charge_id AND pres.visit_id = visit_charge.visit_id AND pres.service_charge_id = ". $service_charge_id." AND pres.visit_id = ". $visit_id." AND visit_charge.visit_id = ". $visit_id;
		
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function get_going_to($visit_id)
	{
		$this->db->select('departments.department_name, visit_department.accounts, departments.department_id');
		$this->db->where('visit_department.department_id = departments.department_id AND visit_department.visit_department_status = 1 AND visit_department.visit_id = '.$visit_id);
		$query = $this->db->get('visit_department, departments');
		
		return $query;
	}
	
	public function get_last_department($visit_id)
	{
		$this->db->select('departments.department_name, a.accounts, departments.department_id');
		$this->db->where('a.created = (
						SELECT MAX(created)
						FROM visit_department AS b
						WHERE b.visit_department_status = 0 AND b.visit_id = '.$visit_id.')
						AND a.department_id = departments.department_id AND a.visit_id = '.$visit_id);
		$query = $this->db->get('visit_department AS a, departments');
		
		return $query;
	}
	
	public function get_cancel_actions()
	{
		$this->db->where('cancel_action_status', 1);
		$this->db->order_by('cancel_action_name');
		
		return $this->db->get('cancel_action');
	}
	
	public function cancel_payment($payment_id)
	{
		$data = array(
			"cancel_action_id" => $this->input->post('cancel_action_id'),
			"cancel_description" => $this->input->post('cancel_description'),
			"cancelled_by" => $this->input->post('cancel_action_id'),
			"cancelled_date" => date("Y-m-d H:i:s"),
			"cancel" => 1
		);
		
		$this->db->where('payment_id', $payment_id);
		if($this->db->update('payments', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	function get_visit_procedure_charges($v_id)
	{
		$table = "visit_charge, service_charge, service";
		$where = "visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = $v_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id ";
		$items = "*";
		$order = "visit_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		return $result;
	}

	public function get_patient_visit_charge_items_tree($visit_id)
	{
		$table = "visit_charge, service_charge, service";
		$where = "visit_charge.visit_charge_units <> 0 AND service_charge.service_id = service.service_id AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND service.service_name <> 'Others' AND visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_id =". $visit_id;
		$items = "service.service_id,service.service_name,service_charge.service_charge_name,visit_charge.service_charge_id,visit_charge.visit_charge_units, visit_charge.visit_charge_amount, visit_charge.visit_charge_timestamp,visit_charge.visit_charge_id,visit_charge.created_by, visit_charge.personnel_id";
		$order = "service.service_name";
		$this->db->where($where);
		$this->db->select($items);
		$this->db->group_by('service.service_name');
		$this->db->order_by('visit_charge.date');
		$result = $this->db->get($table);
		
		return $result;
	}



	function get_visit_procedure_charges_as_services($v_id)
	{
		$table = "visit_charge, service_charge, service";
		$where = "visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = $v_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id ";
		$items = "visit_charge.created_by AS charge_creator, visit_charge.*,service_charge.*,service.*";
		$order = "visit_charge.date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		return $result;
	}
	function get_visit_procedure_charges_per_service($v_id,$service_id,$visit_type_id = null)
	{
		$adding = '';
		if(!empty($visit_type_id))
		{
			$adding = ' AND visit_charge.charge_to = '.$visit_type_id;
		}
		// var_dump($visit_type_id)
		$table = "visit_charge, service_charge, service";
		$where = "visit_charge.visit_charge_delete = 0 AND visit_charge.visit_id = $v_id AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id AND service.service_id = $service_id AND visit_charge.visit_charge_amount > 0 ".$adding;
		$items = "visit_charge.created_by AS charge_creator, visit_charge.*,service_charge.*,service.*";
		$order = "visit_charge.date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		return $result;
	}

	public function get_service_charge_detail($service_charge_id)
	{
		$table = "service_charge";
		$where = "service_charge_id = ". $service_charge_id;
		$items = "*";
		$order = "service_charge_name";
		$this->db->where($where);
		$this->db->select($items);
		$result = $this->db->get($table);
		$service_charge_amount = 0;
		if($result->num_rows() > 0)
		{
			foreach ($result->result() as $value) {
				# code...
				$service_charge_amount = $value->service_charge_amount;
			}
		}
			
		return $service_charge_amount;
	}

	public function add_personnel()
	{
		$data = array(
			'personnel_onames'=>ucwords(strtolower($this->input->post('personnel_onames'))),
			'personnel_fname'=>ucwords(strtolower($this->input->post('personnel_fname'))),
			'branch_id'=>2,
			'personnel_phone'=>$this->input->post('personnel_phone'),
			'title_id'=>4,
			'personnel_type_id'=>6,
		);
		
		if($this->db->insert('personnel', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_all_visits_parent($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit.*');
		$this->db->where($where);
		$this->db->order_by('visit.visit_date','DESC');
		// $this->db->group_by('visit.patient_id','');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_visits_invoice_items($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit_charge.created_by AS charge_creator, visit_charge.*,service_charge.*,service.*');
		$this->db->where($where);
		$this->db->order_by('service.service_name,visit_charge.date','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_visits_invoice_items_walkin($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('visit_charge.created_by AS charge_creator, visit_charge.*,service_charge.*,service.*,pres.*');
		$this->db->where($where);
		$this->db->order_by('service.service_name,visit_charge.date','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_visits_payments_items($table, $where, $per_page, $page, $order = NULL)
	{
		//retrieve all users

		
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('payments.payment_id','DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	/*
	*	Retrieve all patients
	*	@param string $table
	* 	@param string $where
	*	@param int $per_page
	* 	@param int $page
	*
	*/
	public function get_all_patients_accounts($table, $where, $per_page, $page, $items = '*')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select($items);
		$this->db->where($where);
		$this->db->order_by('last_visit','desc');
		// $this->db->group('last_visit','desc');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_all_personnel()
	{
		$this->db->select('*');
		$query = $this->db->get('personnel');
		
		return $query;
	}

	public function check_if_visit_active($visit_id)
	{
		$this->db->where('close_card = 0 OR close_card = 2 AND visit_id ='.$visit_id);
		$query = $this->db->get('visit');

		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	}


	public function receipt_invoice_payment($visit_id,$personnel_id = NULL)
	{
		$amount = $this->input->post('amount_paid');
		
		$change = $this->input->post('change_payment');

		$payments_value = $this->accounts_model->total_payments($visit_id);

		$invoice_total = $this->accounts_model->total_invoice($visit_id);

		$balance = $this->accounts_model->balance($payments_value,$invoice_total);
		if($change > 0 AND $payment_method == 2 AND $balance > 0)
		{
			$amount = $amount - $change;
		}
		else
		{
			$change = 0;
			$amount = $amount;
		}
		$data = array(
			'visit_id' => $visit_id,
			'payment_method_id'=>$payment_method,
			'amount_paid'=>$amount,
			'personnel_id'=>$this->session->userdata("personnel_id"),
			'payment_type'=>$type_payment,
			'transaction_code'=>$transaction_code,
			'payment_service_id'=>$payment_service_id,
			'change'=>$change,
			'payment_created'=>date("Y-m-d"),
			'payment_created_by'=>$this->session->userdata("personnel_id"),
			'approved_by'=>$personnel_id,'date_approved'=>date('Y-m-d')
		);

		// var_dump($data);die();
		if($this->db->insert('payments', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}

	public function previous_payment($visit_id,$date_today){
		$table = "payments, payment_method";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method.payment_method_id = payments.payment_method_id AND payments.visit_id =".$visit_id." AND payment_created <  '".$date_today."' ";
		$items = "SUM(amount_paid) AS total_amount";
		$order = "payments.payment_id";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$total_amount = 0;
		foreach ($query->result() as $key => $value) {
			# code...
			$total_amount = $value->total_amount;
		}
		
		if(empty($total_amount))
		{
			$total_amount = 0;
		}
		return $total_amount;
	}


	public function get_cash_payments($visit_id){
		$table = "payments";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method_id < 9 AND payments.visit_id =".$visit_id."";
		$items = "SUM(amount_paid) AS total_amount";
		$order = "payments.payment_id";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$total_amount = 0;
		foreach ($query->result() as $key => $value) {
			# code...
			$total_amount = $value->total_amount;
		}
		
		if(empty($total_amount))
		{
			$total_amount = 0;
		}
		return $total_amount;
	}

	public function get_insurance_payments($visit_id){
		$table = "payments";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method_id = 9 AND payments.visit_id =".$visit_id."";
		$items = "SUM(amount_paid) AS total_amount";
		$order = "payments.payment_id";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$total_amount = 0;
		foreach ($query->result() as $key => $value) {
			# code...
			$total_amount = $value->total_amount;
		}
		
		if(empty($total_amount))
		{
			$total_amount = 0;
		}
		return $total_amount;
	}
	public function payment_detail($payment_id){
		$table = "payments, payment_method";
		$where = "payments.cancel = 0 AND payment_method.payment_method_id = payments.payment_method_id AND payments.payment_id =". $payment_id;
		$items = "*";
		$order = "payments.payment_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}
	public function update_rejected_reasons($visit_id)
    {
    	// recreate new visit 
		$rs_rejection = $this->dental_model->get_rejection_info($visit_id);
		$rejected_amount = '';
		$rejected_reason ='';
		$close_card = 0;
		if(count($rs_rejection) >0){
			foreach ($rs_rejection as $r2):
			    # code...
			    $rejected_amount = $r2->rejected_amount;
			    $rejected_date = $r2->rejected_date;
			    $rejected_reason = $r2->rejected_reason;
			    $visit_type = $r2->visit_type;
			endforeach;


		    $data = array(
					            "visit_bill_amount" => $this->input->post('rejected_amount'),
					            "visit_type_id" => $this->input->post('visit_type_id'),
					            "visit_id" => $visit_id,
					            "visit_parent" => $visit_id,
					            'visit_bill_reason'=> $this->input->post('rejected_reason'),
					            "visit_parent_visit_type_id" => $visit_type,
					        );
		       
		     $this->db->insert('visit_bill', $data);
		     return TRUE;

	

		}    	


       
    }

    public function get_visit_charges($visit_id)
	{
		$this->db->where('visit_id', $visit_id);
		return $this->db->get('visit_charge');

	}
	


    public function total_payments_today($todays_date,$visit_type_id)
	{
	    $table = "payments, payment_method,visit";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method.payment_method_id = payments.payment_method_id AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND visit.visit_date = '".$todays_date."' AND visit.visit_type =".$visit_type_id;
		$items = "SUM(amount_paid) AS total_payments";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);

		$total_payments = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		
		return $total_payments;
	}


    public function total_invoice_today($todays_date,$visit_type_id)
	{
	    $table = "visit_charge,visit";
		$where = "visit_charge.visit_charge_delete = 0 AND visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit.visit_date = '".$todays_date."'  AND visit.visit_type =".$visit_type_id;
		$items = "SUM(visit_charge_amount*visit_charge_units) AS total_invoice";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);

		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}


		// $table = "visit";
		// $where = "visit.visit_delete = 0 AND visit.visit_date = '".$todays_date."'";
		// $items = "SUM(rejected_amount) AS total_rejected_amount";
		// $order = "payments.payment_id";
		// $this->db->where($where);
		// $query_rejected = $this->db->get($table);

		// $total_rejected_amount = 0;
		// if($query_rejected->num_rows() > 0)
		// {
		// 	foreach ($query_rejected->result() as $key => $value2) {
		// 		# code...
		// 		$total_rejected_amount = $value2->total_rejected_amount;
		// 	}
		// }


		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 2 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND visit.visit_date = '".$todays_date."' AND visit.visit_type =".$visit_type_id;
		$items = "SUM(amount_paid) AS total_waivers";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_waivers = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_waivers = $wiver_value->total_waivers;
			}
		}
		
		$invoice_total = $total_invoice - $total_waivers;
		return $invoice_total;
	}

	public function get_visit_total_invoice($visit_id)
	{
	    $table = "visit_charge,visit";
		$where = "visit_charge.visit_charge_delete = 0 AND (visit.parent_visit IS NULL OR visit.parent_visit = 0)AND visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit.visit_id = '".$visit_id."'";
		$items = "SUM(visit_charge_amount*visit_charge_units) AS total_invoice";
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);

		$total_invoice = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoice = $value->total_invoice;
			}
		}

		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 2 AND (visit.parent_visit IS NULL OR visit.parent_visit = 0) AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND visit.visit_id = '".$visit_id."' ";
		$items = "SUM(amount_paid) AS total_waivers";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_waivers = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_waivers = $wiver_value->total_waivers;
			}
		}
		
		$invoice_total = $total_invoice - $total_waivers;
		return $invoice_total;
	}
	public function get_child_amount_payable($visit_id)
	{
		# code...

		$table = "visit,visit_bill,visit_type";
		$where = "visit_bill.visit_id = '$visit_id' AND visit.visit_delete = 0 AND visit.visit_id = visit_bill.visit_id AND visit_type.visit_type_id = visit.visit_type";
		$items = "SUM(visit_bill_amount) AS total_bill";
		$order = "visit.visit_id";

		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);
		$total_bill = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_bill = $wiver_value->total_bill;
			}
		}

		return $total_bill;

	}
	public function get_visit_waiver($visit_id)
	{
		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 2 AND visit.visit_id = payments.visit_id AND visit.visit_delete = 0 AND visit.visit_id = '".$visit_id."' ";
		$items = "SUM(amount_paid) AS total_waivers";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_waivers = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_waivers = $wiver_value->total_waivers;
			}
		}
		return $total_waivers;
	}

	public function get_cash_balance($patient_id)
	{
		$this->db->where('visit.patient_id = '.$patient_id.' AND visit.visit_type = 1 AND visit.visit_delete = 0  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0');
		$this->db->select('sum(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_amount');

		$query_invoice = $this->db->get('visit,visit_charge');

		$total_amount = 0;
		if($query_invoice->num_rows() > 0)
		{
			foreach ($query_invoice->result() as $key => $wiver_value) {
				# code...
				$total_amount = $wiver_value->total_amount;
			}
		}


		// insurance rejections

		$this->db->where('visit.patient_id = '.$patient_id.' AND visit.visit_type <> 1 AND visit.visit_delete = 0  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0');
		$this->db->select('sum(rejected_amount) AS total_amount');

		$query_rejection = $this->db->get('visit,visit_charge');

		$total_rejection = 0;
		if($query_rejection->num_rows() > 0)
		{
			foreach ($query_rejection->result() as $key => $wiver_value) {
				# code...
				$total_rejection = $wiver_value->total_amount;
			}
		}


		$table = "visit_bill,visit";
		$where = "visit_parent = visit.parent_visit  AND visit.visit_delete = 0 AND visit.patient_id =".$patient_id;
		$items = "SUM(visit_bill_amount) AS total_rejected";
		$order = "visit.visit_id";


		$this->db->where($where);
		$this->db->select($items);

		$query_rejection = $this->db->get($table);
		$total_rejected = 0;
		if($query_rejection->num_rows() > 0)
		{
			foreach ($query_rejection->result() as $key => $wiver_value) {
				# code...
				$total_rejected = $wiver_value->total_rejected;
			}
		}
		$total_rejection += $total_rejected;
		// var_dump($total_rejection); die();


		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method_id < 9 AND visit.visit_type = 1 AND payments.visit_id =visit.visit_id AND visit.patient_id =".$patient_id;
		$items = "SUM(amount_paid) AS cash_payments";
		$order = "payments.payment_id";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$cash_payments = 0;
		foreach ($query->result() as $key => $value) {
			# code...
			$cash_payments = $value->cash_payments;
		}
		
		if(empty($cash_payments))
		{
			$cash_payments = 0;
		}



		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 2 AND visit.visit_id = payments.visit_id AND visit.visit_type = 1 AND visit.visit_delete = 0 AND visit.patient_id = '".$patient_id."' ";
		$items = "SUM(amount_paid) AS total_waivers";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_waivers = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_waivers = $wiver_value->total_waivers;
			}
		}




		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 3 AND visit.visit_id = payments.visit_id AND visit.visit_type = 1 AND visit.visit_delete = 0 AND visit.patient_id = '".$patient_id."' ";
		$items = "SUM(amount_paid) AS total_debits";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_debits = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_debits = $wiver_value->total_debits;
			}
		}
		// var_dump($total_rejection); die();
		return ($total_rejection + $total_amount + $total_debits) - ($cash_payments + $total_waivers);
	}



	public function get_insurance_balance($patient_id)
	{
		$this->db->where('visit.patient_id = '.$patient_id.' AND visit.visit_type <> 1 AND visit.visit_delete = 0  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 ');
		$this->db->select('sum(visit_charge.visit_charge_amount * visit_charge.visit_charge_units) AS total_amount');

		$query_invoice = $this->db->get('visit,visit_charge');

		$total_amount = 0;
		if($query_invoice->num_rows() > 0)
		{
			foreach ($query_invoice->result() as $key => $wiver_value) {
				# code...
				$total_amount = $wiver_value->total_amount;
			}
		}


		$this->db->where('visit.patient_id = '.$patient_id.' AND visit.visit_type <> 1 AND visit.visit_delete = 0  AND (visit.parent_visit = 0 OR visit.parent_visit IS NULL) AND visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0');
		$this->db->select('sum(rejected_amount) AS total_amount');

		$query_rejection = $this->db->get('visit,visit_charge');

		$total_rejection = 0;
		if($query_rejection->num_rows() > 0)
		{
			foreach ($query_rejection->result() as $key => $wiver_value) {
				# code...
				$total_rejection = $wiver_value->total_amount;
			}
		}

		$table = "visit_bill,visit";
		$where = "visit_parent = visit.parent_visit  AND visit.visit_delete = 0 AND visit.patient_id =".$patient_id;
		$items = "SUM(visit_bill_amount) AS total_rejected";
		$order = "visit.visit_id";


		$this->db->where($where);
		$this->db->select($items);

		$query_rejection = $this->db->get($table);
		$total_rejected = 0;
		if($query_rejection->num_rows() > 0)
		{
			foreach ($query_rejection->result() as $key => $wiver_value) {
				# code...
				$total_rejected = $wiver_value->total_rejected;
			}
		}
		$total_rejection += $total_rejected;
		
		// var_dump($total_rejection); die();

		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 1 AND payment_method_id = 9 AND payments.visit_id =visit.visit_id AND visit.patient_id =".$patient_id;
		$items = "SUM(amount_paid) AS cash_payments";
		$order = "payments.payment_id";
		
		$this->db->where($where);
		$this->db->select($items);
		$query = $this->db->get($table);
		$cash_payments = 0;
		foreach ($query->result() as $key => $value) {
			# code...
			$cash_payments = $value->cash_payments;
		}
		
		


		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 2 AND visit.visit_id = payments.visit_id AND visit.visit_type <> 1  AND visit.visit_delete = 0 AND visit.patient_id = '".$patient_id."' ";
		$items = "SUM(amount_paid) AS total_waivers";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_waivers = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_waivers = $wiver_value->total_waivers;
			}
		}


		$table = "payments,visit";
		$where = "payments.cancel = 0 AND payment_type = 3 AND visit.visit_id = payments.visit_id AND visit.visit_type <> 1 AND visit.visit_delete = 0 AND visit.patient_id = '".$patient_id."' ";
		$items = "SUM(amount_paid) AS total_debits";
		$order = "payments.payment_id";
		$this->db->where($where);
		$this->db->select($items);
		$query_waiver = $this->db->get($table);

		$total_debits = 0;
		if($query_waiver->num_rows() > 0)
		{
			foreach ($query_waiver->result() as $key => $wiver_value) {
				# code...
				$total_debits = $wiver_value->total_debits;
			}
		}
		// var_dump($total_rejection); die();
		
		return ($total_amount + $total_debits) - ($cash_payments + $total_waivers + $total_rejection);
	}


	public function approve_invoice($visit_id)
	{
		$data = array(
        	"preauth" => 2
    	);
		
		$this->db->where('visit_id', $visit_id);
		
		if($this->db->update('visit', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	function get_service_charge($procedure_id){
		$table = "service_charge";
		$where = "service_charge_id = '$procedure_id'";
		$items = "*";
		$order = "service_charge_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		return $result;
	}




	public function get_patient_visit_quote_items_tree($visit_id)
	{
		$table = "visit_quotation, service_charge, service";
		$where = "visit_quotation.visit_charge_units <> 0 AND service_charge.service_id = service.service_id AND visit_quotation.visit_charge_delete = 0 AND visit_quotation.charged = 1 AND service.service_name <> 'Others' AND visit_quotation.service_charge_id = service_charge.service_charge_id AND visit_quotation.visit_id =". $visit_id;
		$items = "service.service_id,service.service_name,service_charge.service_charge_name,visit_quotation.service_charge_id,visit_quotation.visit_charge_units, visit_quotation.visit_charge_amount, visit_quotation.visit_charge_timestamp,visit_quotation.visit_charge_id,visit_quotation.created_by, visit_quotation.personnel_id";
		$order = "service.service_name";
		$this->db->where($where);
		$this->db->select($items);
		$this->db->group_by('service.service_name');
		$this->db->order_by('visit_quotation.date');
		$result = $this->db->get($table);
		
		return $result;
	}

	function get_visit_quote_charges_per_service($v_id,$service_id,$visit_type_id = null)
	{
		$adding = '';
		if(!empty($visit_type_id))
		{
			$adding = ' AND visit_quotation.charge_to = '.$visit_type_id;
		}
		// var_dump($visit_type_id)
		$table = "visit_quotation, service_charge, service";
		$where = "visit_quotation.visit_charge_delete = 0 AND visit_quotation.visit_id = $v_id AND visit_quotation.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id AND service.service_id = $service_id AND visit_quotation.visit_charge_amount > 0 ".$adding;
		$items = "visit_quotation.created_by AS charge_creator, visit_quotation.*,service_charge.*,service.*";
		$order = "visit_quotation.date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		return $result;
	}

	public function get_visit_quote_amount($visit_id)
	{

		$this->db->where('visit.visit_id = visit_quotation.visit_id AND visit_quotation.visit_charge_delete = 0 and charged = 1 AND visit_quotation.visit_id ='.$visit_id);
		$this->db->select('sum(visit_quotation.visit_charge_amount * visit_quotation.visit_charge_units) AS total_amount');

		$query_invoice = $this->db->get('visit,visit_quotation');

		$total_amount = 0;
		if($query_invoice->num_rows() > 0)
		{
			foreach ($query_invoice->result() as $key => $wiver_value) {
				# code...
				$total_amount = $wiver_value->total_amount;
			}
		}

		return $total_amount;
	}

	public function create_receipt_number()
	{
		//select product code
		$this->db->where('payment_id > 0 AND payment_type = 1 ');
		$this->db->from('payments');
		$this->db->select('MAX(confirm_number) AS number');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			if($number == 1){
				$number = 600;
			}
			
			if($number == 1)
			{
				$number = 600;
			}
			
		}
		else{//start generating receipt numbers
			$number = 600;
		}
		return $number;
	}
}
?>