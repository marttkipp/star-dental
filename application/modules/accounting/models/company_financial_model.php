<?php

class Company_financial_model extends CI_Model 
{

	public function get_all_service_types()
	{
		$this->db->select('*');
		$this->db->where('service_delete = 0 AND service.service_status = 1');
		$query = $this->db->get('service');
		
		return $query;
	}

	public function get_service_invoice_total($service_id, $date = NULL)
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_payments_add =  ' AND (payment_created >= \''.$date_from.'\' AND payment_created <= \''.$date_to.'\') ';
				$search_invoice_add =  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_payments_add = ' AND payments.payment_created = \''.$date_from.'\'';
				$search_invoice_add = ' AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_payments_add = ' AND payments.payment_created = \''.$date_to.'\'';
				$search_invoice_add = ' AND visit_charge.date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';
			$search_payments_add = '';
				
		}

		
		$table = 'visit_charge, service_charge,service';
		
		$where = 'visit_charge.visit_charge_delete = 0 AND visit_charge.service_charge_id = service_charge.service_charge_id AND service.service_id = service_charge.service_id AND service.service_status = 1 AND service_charge.service_id = '.$service_id;

		
		$where .= $search_invoice_add;

		$this->db->select('SUM(visit_charge_units*visit_charge_amount) AS service_total');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->service_total;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}

	

	public function get_service_payments_total($service_id, $date = NULL)
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_payments_add =  ' AND (payment_created >= \''.$date_from.'\' AND payment_created <= \''.$date_to.'\') ';
				$search_invoice_add =  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_payments_add = ' AND payments.payment_created = \''.$date_from.'\'';
				$search_invoice_add = ' AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_payments_add = ' AND payments.payment_created = \''.$date_to.'\'';
				$search_invoice_add = ' AND visit_charge.date = \''.$date_to.'\'';
			}
		}
		else
		{
			$search_invoice_add = '';
			$search_payments_add = '';
				
		}
		
		$table = 'payments';
		

		$where = 'cancel = 0 AND payment_service_id = '.$service_id;
	
		$where .= $search_payments_add;
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}


	public function get_total_payments_collected()
	{
		
		$table = 'payments';
		

		$where = 'payment_created = "'.$date.'" AND cancel = 0';
	
	
		$this->db->select('SUM(amount_paid) AS paid_amount');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total = $result->paid_amount;;
		
		if($total == NULL)
		{
			$total = 0;
		}
		
		return $total;
	}
	public function get_total_purchases()
	{
		if($date == NULL)
		{
			$date = date('Y-m-d');
		}
		
		$table = 'order_supplier';
		

		$where = 'invoice_number <> ""';
	
	
		$this->db->select('selling_unit_price,pack_size,quantity_received');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		$result = $query->row();
		$total_value = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$selling_unit_price = $value->selling_unit_price;
				$pack_size = $value->pack_size;
				$quantity_received = $value->quantity_received;

				$total_units = $pack_size * $quantity_received;

				$total_value += $total_units * $pack_size;
			}
		}

		return $total_value;
	}



	public function get_stock_value()
	{
		
		
		$table = 'product';
		

		$where = 'product_status = 1 AND product_deleted = 0';
	
	
		$this->db->select('SUM((product.quantity * product.product_unitprice)) AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);

		$inventory_start_date = $this->company_financial_model->get_inventory_start_date();
		
		$result = $query->row();
		$total_value = 0;
		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
 // var_dump($starting_value); die();
		$sales_value = $this->company_financial_model->get_drug_units_sold_value($inventory_start_date);
		$procurred_amount = $this->company_financial_model->get_total_purchases();

		return ($starting_value + $procurred_amount) - $sales_value;
	}
	public function get_drug_units_sold_value($inventory_start_date, $product_id=NULL, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (date >= \''.$date_from.'\' AND date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND date = \''.$date_to.'\'';
			}
		}

		$table = "visit_charge, service_charge";
		$where = 'visit_charge.service_charge_id = service_charge.service_charge_id AND visit_charge.charged = 1 AND service_charge.product_id > 0 '.$search_add;
		
		
		
		$items = "SUM((visit_charge.visit_charge_units * visit_charge.visit_charge_amount)) AS amount";
		$order = "date";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$amount = $key->amount;

				$total_sold =$amount;
			}
		}
		return $total_sold;
	}

	public function item_proccured($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND created = \''.$date_to.'\'';
			}
		}

  		$table = "order_item, order_supplier,product";
		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = ".$product_id." AND order_item.product_id = product.product_id ".$search_add;
		$items = "order_supplier.quantity_received,order_supplier.pack_size";
		$order = "order_supplier_id";
		
		
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		$total = 0;
		$units = 0;
		if(count($result) > 0){
			
			foreach ($result as $row2)
			{
				$quantity_received = $row2->quantity_received;
				$pack_size = $row2->pack_size;
				$units = $pack_size * $quantity_received;
				$total = $units;
			}
		}
		return $total;
	}

	public function item_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		if($store_id == NULL)
		{
			$table = "product_deductions, product";
			$where = "product_deductions.product_deductions_date >= '".$inventory_start_date."' AND product.product_id = ".$product_id." AND product_deductions.product_id = product.product_id";
			$items = "product_deductions.product_deductions_pack_size, product_deductions.product_deductions_quantity";
			$order = "product_deductions_pack_size";
		
			if(($start_date != NULL) && ($end_date != NULL))
			{
				 $where .= 'AND product_deductions.product_deductions_date >= "'.$start_date.'" AND product_deductions.product_deductions_date<= "'.$end_date.'"';
			}
			
			else if(($start_date == NULL) && ($end_date != NULL))
			{
				 $where .= ' AND product_deductions.product_deductions_date = "'.$end_date.'"';
			}
			
			else if(($start_date != NULL) && ($end_date == NULL))
			{
				 $where .= ' AND product_deductions.product_deductions_date = "'.$start_date.'"';
			}
			
			$result = $this->database->select_entries_where($table, $where, $items, $order);
			
			$total = 0;
			
			if(count($result) > 0){
				
				foreach ($result as $row2)
				{
					$product_deductions_pack_size = $row2->product_deductions_pack_size;
					$product_deductions_quantity = $row2->product_deductions_quantity;
					$total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
				}
			}
		}
		
		else
		{
			$table = "product_deductions, product";
			$where = "product.product_id = ".$product_id." AND product_deductions.product_id = product.product_id AND product_deductions.store_id = ".$store_id;
			$items = "product_deductions.product_deductions_pack_size, product_deductions.product_deductions_quantity";
			$order = "product_deductions_pack_size";
			
			$result = $this->database->select_entries_where($table, $where, $items, $order);
			
			$total = 0;
			
			if(count($result) > 0){
				
				foreach ($result as $row2)
				{
					$product_deductions_pack_size = $row2->product_deductions_pack_size;
					$product_deductions_quantity = $row2->product_deductions_quantity;
					$total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
				}
			}
		}
		return $total;
	}

	public function get_inventory_start_date()
	{
		$this->db->where('branch_code', $this->session->userdata('branch_code'));
		$query = $this->db->get('branch');
		
		$inventory_start_date = '';
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$inventory_start_date = $row->inventory_start_date;
		}
		
		return $inventory_start_date;
	}
	public function get_child_accounts($parent_account_name)
    {
    	$this->db->from('account');
		$this->db->select('*');
		$this->db->where('account_name = "'.$parent_account_name.'" AND account.account_status = 1');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)  
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$account_id = $value->account_id;
			}
			//retrieve all users
			$this->db->from('account');
			$this->db->select('*');
			$this->db->where('parent_account = '.$account_id.' AND account.account_status = 1');
			$query = $this->db->get();
			
			return $query;    	


		}
		else
		{
			return FALSE;
		}

    }

    public function get_total_expense_amount($account_id)
    {
    	$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		$search_payment_add ='';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND created = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND created = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
		}


    	$this->db->from('account_invoices');
		$this->db->select('SUM(invoice_amount) AS total_paid');
		$this->db->where('account_invoice_deleted = 0 AND account_to_id = '.$account_id.''.$search_add);
		$query = $this->db->get();
		$total_paid  =0 ;
		if($query->num_rows() > 0)  
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_paid = $value->total_paid;
			}
		}
		return $total_paid;
    }

     public function get_total_expenses()
    {
    	
    	$this->db->from('account_invoices');
		$this->db->select('SUM(invoice_amount) AS total_paid');
		$this->db->where('account_invoice_deleted = 0 AND account_to_type = 1 ');
		$query = $this->db->get();
		$total_paid  =0 ;
		if($query->num_rows() > 0)  
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_paid = $value->total_paid;
			}
		}
		return $total_paid;
    }

    public function get_all_fixed_categories()
	{
		$this->db->select('*');
		$this->db->where('asset_category_id > 0');
		$query = $this->db->get('asset_category');
		
		return $query;
	}

    public function get_all_fixed_assets()
	{
		$this->db->select('*');
		$this->db->where('asset_id > 0');
		$query = $this->db->get('assets_details');
		
		return $query;
	}

	public function get_category_value($asset_category_id)
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = 'AND created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = 'AND created = \''.$date_to.'\'';
			}
		}

		$this->db->select('SUM(asset_value) AS amount');
		$this->db->where('asset_category_id ='.$asset_category_id.$search_add);
		$query = $this->db->get('assets_details');

		$query_result = $query->row();



		return $query_result->amount;
		

	}

	public function get_visit_details()
	{
		$this->db->from('visit_type');
		$this->db->select('*');
		$this->db->where('visit_type_status = 1');
		$query = $this->db->get();
		
		return $query;
	}

	public function get_visit_type_invoice($visit_type_id)
	{
		//retrieve all users

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_payments_add = '';
		$search_invoice_add = '';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_payments_add =  ' AND (payment_created >= \''.$date_from.'\' AND payment_created <= \''.$date_to.'\') ';
				$search_invoice_add =  ' AND (visit_charge.date >= \''.$date_from.'\' AND visit_charge.date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_payments_add = 'AND payments.payment_created = \''.$date_from.'\'';
				$search_invoice_add = 'AND visit_charge.date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_payments_add = 'AND payments.payment_created = \''.$date_to.'\'';
				$search_invoice_add = 'AND visit_charge.date = \''.$date_to.'\'';
			}
				// var_dump($search_payments_add); die();
		}


		$this->db->from('visit,payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where('visit.visit_delete = 0 AND payments.cancel = 0 AND visit.visit_id = payments.visit_id AND visit.visit_type = '.$visit_type_id.$search_payments_add);
		$query = $this->db->get('');
		$invoice_amount = 0;
		$payment_amount = 0;
		$balance_amount = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$payment_amount =$value->total_payments;

			}
		}

		$this->db->from('visit,visit_charge');
		$this->db->select('SUM(visit_charge_amount) AS total_invoice');
		$this->db->where('visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.visit_id = visit_charge.visit_id AND visit.visit_type = '.$visit_type_id.$search_invoice_add);
		$visit_charge_query = $this->db->get('');
		$invoice_amount = 0;
		if($visit_charge_query->num_rows() > 0)
		{
			foreach ($visit_charge_query->result() as $key => $value_charge) {
				# code...
				$invoice_amount =$value_charge->total_invoice;

			}
		}

		$balance_amount = $invoice_amount - $payment_amount;

		$response['invoice_total'] = $invoice_amount;
		$response['payments_value']= $payment_amount;
		$response['balance'] = $balance_amount;
		return $response;
	}

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


	public function get_account_balances($account_id)
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		$search_payment_add ='';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND created = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND created = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
		}

		$this->db->from('account');
		$this->db->select('account_opening_balance');
		$this->db->where('account_id = '.$account_id);
		$query_opening = $this->db->get('');
		$account_opening_balance = 0;
		if($query_opening->num_rows() > 0)
		{
			foreach ($query_opening->result() as $key => $value) {
				# code...
				$account_opening_balance = $value->account_opening_balance;
			}
		}



		//retrieve all users
		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_received');
		$this->db->where('account_payment_deleted = 0 AND account_to_id = '.$account_id.$search_add);
		$query = $this->db->get('');
		$total_received = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_received = $value->total_received;
			}
		}

	
		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_disbursed');
		$this->db->where('account_payment_deleted = 0 AND account_from_id = '.$account_id.$search_add);
		$query_disbursed = $this->db->get('');
		$total_disbursed = 0;
		if($query_disbursed->num_rows() > 0)
		{
			foreach ($query_disbursed->result() as $key => $value) {
				# code...
				$total_disbursed = $value->total_disbursed;
			}
		}

		$this->db->from('account_invoices');
		$this->db->select('SUM(invoice_amount) AS total_disbursed');
		$this->db->where('account_invoice_deleted = 0 AND account_from_id = '.$account_id.$search_add);
		$query_expenses = $this->db->get('');
		$total_expenses = 0;
		if($query_expenses->num_rows() > 0)
		{
			foreach ($query_expenses->result() as $key => $value) {
				# code...
				$total_expenses = $value->total_disbursed;
			}
		}




		$balance = ($total_received + $account_opening_balance) - ($total_disbursed + $total_expenses);
			// var_dump($total_disbursed); die();
		$response['total_received'] = $total_received;
		$response['total_disbursed']= $total_disbursed;
		$response['total_balance'] = $balance;


		return $response;
	}

	public function get_cash_collected($account_id,$payment_method = NULL)
	{
		if($payment_method == NULL)
		{
			$add = ' AND payment_method_id = 2';
		}
		{
			$add = ' AND payment_method_id = '.$payment_method;
		}
		$search_status = $this->session->userdata('balance_sheet_search');
		$where = 'payments.payment_type = 1 '.$add.' AND (cancel IS NULL or cancel = 0)';
		$search_add = '';
		$search_payment_add ='';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_created >= \''.$date_from.'\' AND payment_created <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND created = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_created = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND created = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_created = \''.$date_to.'\'';
			}
		}


		$where = $where.$search_payment_add;

		$this->db->from('payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where($where);
		$query_opening = $this->db->get('');
		$total_paid = 0;
		if($query_opening->num_rows() > 0)
		{
			foreach ($query_opening->result() as $key => $value) {
				# code...
				$total_paid = $value->total_payments;
			}
		}

		//retrieve all users
		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_received');
		$this->db->where('account_payment_deleted = 0 AND account_to_id = '.$account_id.$search_add);
		$query = $this->db->get('');
		$total_received = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_received = $value->total_received;
			}
		}





		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_disbursed');
		$this->db->where('account_payment_deleted = 0 AND account_from_id = '.$account_id.$search_add);
		$query_disbursed = $this->db->get('');
		$total_disbursed = 0;
		if($query_disbursed->num_rows() > 0)
		{
			foreach ($query_disbursed->result() as $key => $value) {
				# code...
				$total_disbursed = $value->total_disbursed;
			}
		}
		// var_dump($total_paid); die();

		// expense


		$balance = ($total_paid + $total_received) - $total_disbursed;
		// var_dump($total_disbursed); die();
		$response['total_received'] = $total_received;
		$response['total_disbursed']= $total_disbursed;
		$response['total_balance'] = $balance;
		$response['total_income'] = $total_paid;


		return $response;
	}


	public function get_suppliers_balances()
	{
		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		$search_payment_add ='';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (invoice_date >= \''.$date_from.'\' AND invoice_date <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND invoice_date = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND invoice_date = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
		}



		$this->db->from('creditor');
		$this->db->select('opening_balance,debit_id');
		$this->db->where('creditor_id > 0');
		$query_opening = $this->db->get('');
		$opening_balance = 0;
		$total_opening_balance =0;
		if($query_opening->num_rows() > 0)
		{
			foreach ($query_opening->result() as $key => $value) {
				# code...
				$opening_balance = $value->opening_balance;
				$debit_id = $value->debit_id;
				if($debit_id == 1)
				{
					$total_opening_balance -=$opening_balance;
				}
				else
				{
					$total_opening_balance +=$opening_balance;
				}

				
			}
		}



		//retrieve all users
		$this->db->from('account_invoices');
		$this->db->select('SUM(invoice_amount) AS total_invoices');
		$this->db->where('account_to_type = 2 AND account_invoice_deleted = 0 '.$search_add);
		$query = $this->db->get('');
		$total_invoices = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoices = $value->total_invoices;
			}
		}


		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where('account_to_type = 2 AND account_payment_deleted = 0 '.$search_payment_add);
		$query_disbursed = $this->db->get('');
		$total_payments = 0;
		if($query_disbursed->num_rows() > 0)
		{
			foreach ($query_disbursed->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}
		$balance = ($total_invoices + $total_opening_balance) - $total_payments;

		$response['total_invoices'] = $total_invoices;
		$response['total_payments']= $total_payments;
		$response['total_balance'] = $balance;
		$response['total_opening_balance'] = $total_opening_balance;


		return $response;
	}


	public function get_providers_balances()
	{

		$search_status = $this->session->userdata('balance_sheet_search');
		$search_add = '';
		$search_payment_add ='';
		if($search_status == 1)
		{
			$date_from = $this->session->userdata('date_from_balance_sheet');
			$date_to = $this->session->userdata('date_to_balance_sheet');

			if(!empty($date_from) AND !empty($date_to))
			{
				$search_add =  ' AND (created >= \''.$date_from.'\' AND created <= \''.$date_to.'\') ';
				$search_payment_add =  ' AND (payment_date >= \''.$date_from.'\' AND payment_date <= \''.$date_to.'\') ';
			}
			else if(!empty($date_from))
			{
				$search_add = ' AND created = \''.$date_from.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_from.'\'';
			}
			else if(!empty($date_to))
			{
				$search_add = ' AND created = \''.$date_to.'\'';
				$search_payment_add = ' AND payment_date = \''.$date_to.'\'';
			}
		}

		$this->db->from('provider_account');
		$this->db->select('opening_balance,debit_id');
		$this->db->where('provider_id > 0');
		$query_opening = $this->db->get('');
		$opening_balance = 0;
		$total_opening_balance =0;
		if($query_opening->num_rows() > 0)
		{
			foreach ($query_opening->result() as $key => $value) {
				# code...
				$opening_balance = $value->opening_balance;
				$debit_id = $value->debit_id;
				if($debit_id == 1)
				{
					$total_opening_balance -=$opening_balance;
				}
				else
				{
					$total_opening_balance +=$opening_balance;
				}

				
			}
		}
		//retrieve all users
		$this->db->from('account_invoices');
		$this->db->select('SUM(invoice_amount) AS total_invoices');
		$this->db->where('account_to_type = 3'.$search_add);
		$query = $this->db->get('');
		$total_invoices = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$total_invoices = $value->total_invoices;
			}
		}




		$this->db->from('account_payments');
		$this->db->select('SUM(amount_paid) AS total_payments');
		$this->db->where('account_to_type = 3'.$search_add);
		$query_disbursed = $this->db->get('');
		$total_payments = 0;
		if($query_disbursed->num_rows() > 0)
		{
			foreach ($query_disbursed->result() as $key => $value) {
				# code...
				$total_payments = $value->total_payments;
			}
		}

		$balance = ($total_invoices + $total_opening_balance) - $total_payments;

		$response['total_invoices'] = $total_invoices;
		$response['total_payments']= $total_payments;
		$response['total_balance'] = $balance;


		return $response;
	}

	public function get_profit_and_loss()
	{
		$services_result = $this->company_financial_model->get_all_service_types();
		$service_result = '';
		$total_service_invoice = 0;
		$total_service_payment = 0;
		$total_service_balance = 0;
		$total_payments = $this->get_total_payments_collected();

			
		$total_purchases = $this->company_financial_model->get_total_purchases();
		$total_stock_value = $this->company_financial_model->get_stock_value();
		$total_expenses = $this->company_financial_model->get_total_expenses();
		$total_profit = $total_stock_value+$total_payments + $total_purchases - $total_expenses;

		return $total_profit;
	}
}
?>