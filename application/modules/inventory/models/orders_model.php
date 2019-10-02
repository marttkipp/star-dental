<?php

class Orders_model extends CI_Model 
{
	/*
	*	Retrieve all orders
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_orders($table, $where, $per_page, $page)
	{
		//retrieve all orders
		$this->db->from($table);
		$this->db->select('orders.*,order_status.order_status_name,store.store_id, store.store_name');
		$this->db->where($where);
		$this->db->order_by('orders.order_id,order_approval_status','ASC');
		$this->db->join('store', 'store.store_id = orders.store_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function get_all_supplier_order_items($table, $where, $per_page, $page)
	{
		//retrieve all orders
		$this->db->from($table);
		$this->db->select('product.product_name, order_item.*');
		$this->db->where($where);
		$this->db->order_by('order_item.order_item_id','ASC');
		// $this->db->join('store', 'store.store_id = orders.store_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	


	/*
	*	Retrieve all suppliers
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_supplied_items($table, $where, $per_page, $page)
	{

		//retrieve all users
		$this->db->from($table);
		$this->db->select('order_item.*, order_supplier.*,orders.supplier_invoice_date,orders.supplier_invoice_number,orders.order_status_id,product_category.product_category_name, product.product_id,product.product_name,product.product_status,product.product_deleted, product.reorder_level,product.product_unitprice,product.store_id,product.quantity AS opening_quantity,creditor.creditor_name');
		$this->db->where($where);
		$this->db->order_by('product.product_name');
		$this->db->join('product_category', 'product_category.product_category_id = product.category_id','left');
		$this->db->join('creditor', 'creditor.creditor_id = orders.supplier_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	

	public function get_all_orders_suppliers($table, $where, $per_page, $page)
	{
		//retrieve all orders
		$this->db->from($table);
		$this->db->select('orders.*,order_status.order_status_name,store.store_id, store.store_name,creditor.creditor_id,creditor.creditor_name');
		$this->db->where($where);
		$this->db->order_by('order_approval_status','ASC');
		$this->db->join('store', 'store.store_id = orders.store_id','left');
		$this->db->join('creditor', 'creditor.creditor_id = orders.supplier_id','left');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	/*
	*	Retrieve all orders of a user
	*
	*/
	public function get_user_orders($user_id)
	{
		$this->db->where('user_id = '.$user_id);
		$this->db->order_by('created', 'DESC');
		$query = $this->db->get('orders');
		
		return $query;
	}
	public function get_order_details($order_id)
	{
		$this->db->where('store.store_id = orders.store_id AND orders.order_id = '.$order_id);
		$this->db->order_by('orders.created', 'DESC');
		$query = $this->db->get('orders,store');
		
		return $query;
	}

	public function get_order_supplier_details($order_id)
	{
		$this->db->where('store.store_id = orders.store_id AND creditor.creditor_id = orders.supplier_id AND orders.order_id = '.$order_id);
		$this->db->order_by('orders.created', 'DESC');
		$query = $this->db->get('orders,store,creditor');
		
		return $query;
	}
	public function get_order_suppliers($order_id)
	{
		$this->db->where('creditor.creditor_id = supplier_order.supplier_id AND supplier_order.order_id = '.$order_id);
		$query = $this->db->get('creditor,supplier_order');
		
		return $query;
	}

	public function get_order_items_supplier($order_id,$creditor_id)
	{
		$this->db->select('order_supplier.quantity AS supplying,order_supplier.unit_price AS single_price, product.*,order_supplier.*,order_item.*');
		$this->db->where('order_supplier.supplier_id = '.$creditor_id.' AND order_supplier.order_id = '.$order_id.'  AND order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id');
		$this->db->order_by('order_supplier_id');
		$query = $this->db->get('order_supplier,order_item,product');
		
		return $query;

	}
	public function get_supplied_list($order_id)
	{
		$this->db->select('order_supplier.quantity AS supplying,order_supplier.unit_price AS single_price,order_supplier.pack_size, product.*,order_supplier.*,order_item.*,creditor.*,order_item.order_item_id AS item_id');
		$this->db->where('order_supplier.supplier_id = creditor.creditor_id AND order_supplier.order_id = '.$order_id.'  AND order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id');
		$this->db->order_by('order_supplier_id');
		$query = $this->db->get('order_supplier,order_item,product,creditor');
		
		return $query;

	}
	
	public function get_supplier_order_details($supplier_order_id)
	{
		$this->db->where('creditor.creditor_id = supplier_order.supplier_id AND orders.order_id = supplier_order.order_id AND supplier_order.supplier_order_id = '.$supplier_order_id);
		$query = $this->db->get('creditor,supplier_order,orders');
		
		return $query;
	}
	public function get_order_approval_status($order_id)
	{
		$this->db->select('order_approval_status');
		$this->db->where('order_id = '.$order_id);
		$query = $this->db->get('orders');
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$order_approval_status = $key->order_approval_status;
			}
		}
		else
		{
			$order_approval_status = 0;
		}
		return $order_approval_status;
	}
	
	/*
	*	Retrieve an order
	*
	*/
	public function get_order($order_id)
	{
		$this->db->select('*');
		$this->db->where('orders.order_status = order_status.order_status_id AND users.user_id = orders.user_id AND orders.order_id = '.$order_id);
		$query = $this->db->get('orders, order_status, users');
		
		return $query;
	}
	/*
	*	Retrieve all orders
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_order_status()
	{
		//retrieve all orders
		$this->db->from('order_status');
		$this->db->select('*');
		$this->db->order_by('order_status_name');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Retrieve all order items of an order
	*
	*/
	public function get_order_items($order_id)
	{
		$this->db->select('product.product_name, order_item.*');
		$this->db->where('product.product_id = order_item.product_id AND order_item.order_id = '.$order_id);
		$query = $this->db->get('order_item, product');
		
		return $query;
	}
	
	/*
	*	Create order number
	*
	*/
	public function create_order_number()
	{
		//select product code
		$this->db->from('orders');
		$this->db->where("order_number LIKE '".$this->session->userdata('branch_code')."".date('y')."-%'");
		$this->db->select('MAX(order_number) AS number');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			
			if($number == 1){
				$number = "".$this->session->userdata('branch_code')."".date('y')."-001";
			}
		}
		else{//start generating receipt numbers
			$number = "".$this->session->userdata('branch_code')."".date('y')."-001";
		}
		
		return $number;
	}
	
	/*
	*	Create the total cost of an order
	*	@param int order_id
	*
	*/
	public function calculate_order_cost($order_id)
	{
		//select product code
		$this->db->from('order_item');
		$this->db->where('order_id = '.$order_id);
		$this->db->select('SUM(price * quantity) AS total_cost');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$total_cost =  $result[0]->total_cost;
		}
		
		else
		{
			$total_cost = 0;
		}
		
		return $total_cost;
	}
	
	/*
	*	Add a new order
	*
	*/
	public function add_order()
	{
		$order_number = $this->create_order_number();
		
		$data = array(
				'order_number'=>$order_number,
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id')
			);
			
		if($this->db->insert('orders', $data))
		{
			$order_id = $this->db->insert_id();
			$insert_data = array(
					'order_id'=>$order_id,
					'order_level_status_status'=>0,
					'created'=>date("Y-m-d H:i:s"),
					'created_by' => $this->session->userdata('personnel_id'),
					'modified_by' =>$this->session->userdata('personnel_id')
				);

			$this->db->insert('order_level_status', $insert_data);
			return $order_id;
		}
		else{
			return FALSE;
		}
	}

	public function add_supplier_order()
	{
		$order_number = $this->create_order_number();
		
		$data = array(
				'order_number'=>$order_number,
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id'),
				'supplier_id'=>$this->input->post('supplier_id')
			);
			
		if($this->db->insert('orders', $data))
		{
			$order_id = $this->db->insert_id();
			$insert_data = array(
					'order_id'=>$order_id,
					'order_level_status_status'=>0,
					'created'=>date("Y-m-d H:i:s"),
					'created_by' => $this->session->userdata('personnel_id'),
					'modified_by' =>$this->session->userdata('personnel_id')
				);

			$this->db->insert('order_level_status', $insert_data);
			return $order_id;
		}
		else{
			return FALSE;
		}
	}

	public function add_supplier_items()
	{
		$creditor_id = $this->input->post('creditor_id');
		$order_item_id = $this->input->post('order_product_id');
		$quantity = $this->input->post('quantity_to_deliver');
		$unit_price = $this->input->post('unit_price_supplier');
		$order_id = $this->input->post('order_id');
		$created = date('Y-m-d');
		
		$data = array(
				'supplier_id'=>$creditor_id,
				'order_item_id'=>$order_item_id,
				'order_id'=>$order_id,
				'created'=>$created				
			);

		$this->db->where($data);
		$query = $this->db->get('order_supplier');

		if($query->num_rows() > 0)
		{
			// $this->db->where($data);
			if($this->db->delete('order_supplier',$data))
			{
				$data['quantity']=$quantity;
				$data['unit_price']=$unit_price;

				if($this->db->insert('order_supplier',$data))
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
		}
		else
		{

			$data['quantity']=$quantity;
			$data['unit_price']=$unit_price;

			if($this->db->insert('order_supplier',$data))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}

		}
	}

	public function add_supplier_to_order($order_id)
	{
		$supplier_id = $this->input->post('supplier_id');

		$this->db->from('supplier_order');
		$this->db->where('order_id = '.$order_id.' AND supplier_id = '.$supplier_id);
		$this->db->select('*');
		$query = $this->db->get();

		if($query->num_rows() == 0)
		{

			$data = array(
					'order_id'=>$order_id,
					'supplier_id'=>$supplier_id,
					'created_by'=>$this->session->userdata('personnel_id'),
					'created'=>date('Y-m-d H:i:s'),
					'modified_by'=>$this->session->userdata('personnel_id')
				);
				
			if($this->db->insert('supplier_order', $data))
			{
				return $this->db->insert_id();
			}
			else{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	/*
	*	Update an order
	*	@param int $order_id
	*
	*/
	public function _update_order($order_id)
	{
		
		$data = array(
				'created_by'=>$this->input->post('personnel_id'),
				'order_status'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id')
			);
		
		$this->db->where('order_id', $order_id);
		if($this->db->update('orders', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	/*
	*	Retrieve all orders
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_payment_methods()
	{
		//retrieve all orders
		$this->db->from('payment_method');
		$this->db->select('*');
		$this->db->order_by('payment_method_name');
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	Add a order product
	*
	*/
	public function add_order_item($order_id)
	{
		$product_id = $this->input->post('product_id');
		$quantity = $this->input->post('quantity');

		$in_stock = $this->input->post('in_stock');
		// var_dump($in_stock); die();
		//Check if item exists
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
		$query = $this->db->get('order_item');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			$qty = $result->quantity;
			
			$quantity += $qty;
			
			$data = array(
					'order_item_quantity'=>$quantity,
					'in_stock'=>$in_stock
				);
			$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
			if($this->db->update('order_item', $data))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
		
		else
		{
			$data = array(
					'order_id'=>$order_id,
					'product_id'=>$product_id,
					'order_item_quantity'=>$quantity,
					'in_stock'=>$in_stock
				);
				
			if($this->db->insert('order_item', $data))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
	}



	public function update_order_item($order_id,$order_item_id)
	{
		$data = array(
					'order_item_quantity'=>$this->input->post('quantity'),
					'in_stock'=>$this->input->post('in_stock')
				);
				
		$this->db->where('order_item_id = '.$order_item_id);
		if($this->db->update('order_item', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function update_order_item_price($order_id,$order_item_id)
	{
		$data = array(
					'supplier_unit_price'=>$this->input->post('unit_price')
				);
				
		$this->db->where('order_item_id = '.$order_item_id);
		if($this->db->update('order_item', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Update an order item
	*
	*/
	public function update_cart($order_item_id, $quantity)
	{
		$data = array(
					'quantity'=>$quantity
				);
				
		$this->db->where('order_item_id = '.$order_item_id);
		if($this->db->update('order_item', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Delete an existing order item
	*	@param int $product_id
	*
	*/
	public function delete_order_item($order_item_id)
	{
		if($this->db->delete('order_item', array('order_item_id' => $order_item_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function get_next_approval_status_name($status)
	{
		$this->db->select('inventory_level_status_name');
		$this->db->where('inventory_level_status_id = '.$status);
		$query = $this->db->get('inventory_level_status');
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$inventory_level_status_name = $key->inventory_level_status_name;
			}
		}
		else
		{
			$inventory_level_status_name = 0;
		}
		return $inventory_level_status_name;	
	}
	public function check_assigned_next_approval($next_level_status)
	{
		$this->db->select('*');
		$this->db->where('approval_status_id = '.($next_level_status+1).' AND personnel_id = '.$this->session->userdata('personnel_id').'');
		$query = $this->db->get('personnel_approval');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}	
	}
	public function check_if_can_access($order_approval_status,$order_id)
	{
		if($order_approval_status == 0)
		{
			$addition =' AND personnel_approval.approval_status_id = 1';
		}
		else
		{
			$addition = 'AND order_level_status.order_level_status_status = 1 AND personnel_approval.approval_status_id <= '.($order_approval_status+1);
		}
		$this->db->select('*');
		$this->db->where('order_level_status.order_id = '.$order_id.' '.$addition.'  AND personnel_approval.personnel_id = '.$this->session->userdata('personnel_id').'');
		$this->db->order_by('order_level_status.order_level_status_id','DESC');
		$this->db->limit(1);
		$query = $this->db->get('personnel_approval,order_level_status');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	}
	public function get_rfq_authorising_personnel($order_id)
	{
		$this->db->select('*');
		$this->db->where('order_level_status.created_by = personnel.personnel_id AND job_title.job_title_id = personnel_job.job_title_id AND personnel.personnel_id = personnel_job.personnel_id AND order_level_status.order_level_status_status = 1 AND title.title_id = personnel.title_id AND order_level_status.personnel_order_approval_status = 2');
		$query = $this->db->get('personnel,order_level_status,title,personnel_job,job_title');
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$other_names = $key->personnel_onames;
				$first_name = $key->personnel_fname;
				$title_name = $key->title_name;
				$job_title_name = $key->job_title_name;

				$item = '<br>'.$title_name.' '.$first_name.' '.$other_names.' <br> '.$job_title_name.' <br> ';
			}

		}
		else
		{
			$item = '';
		}
		return $item;
	}
	public function update_order_status($order_id,$order_status)
	{
		$data = array(
					'order_approval_status'=>$order_status
				);
				
		$this->db->where('order_id = '.$order_id);
		if($this->db->update('orders', $data))
		{
			$this->save_order_approval_status($order_id,$order_status);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function save_order_approval_status($order_id,$order_status)
	{
		$insert_data = array(
					'order_id'=>$order_id,
					'personnel_order_approval_status'=>$order_status,
					'order_level_status_status'=>1,
					'created'=>date("Y-m-d H:i:s"),
					'created_by' => $this->session->userdata('personnel_id'),
					'modified_by' =>$this->session->userdata('personnel_id')
				);
		if($this->db->insert('order_level_status', $insert_data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}


	}
	public function get_lpo_authorising_personnel($order_id)
	{
		$this->db->select('*');
		$this->db->where('order_level_status.created_by = personnel.personnel_id AND job_title.job_title_id = personnel_job.job_title_id AND personnel.personnel_id = personnel_job.personnel_id AND order_level_status.order_level_status_status = 1 AND title.title_id = personnel.title_id AND order_level_status.personnel_order_approval_status = 6');
		$query = $this->db->get('personnel,order_level_status,title,personnel_job,job_title');
		
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$other_names = $key->personnel_onames;
				$first_name = $key->personnel_fname;
				$title_name = $key->title_name;
				$job_title_name = $key->job_title_name;

				$item = '<br>'.$title_name.' '.$first_name.' '.$other_names.' <br> '.$job_title_name.' <br> ';
			}

		}
		else
		{
			$item = '';
		}
		return $item;
	}
	 public function get_order_supply($order_id)
	
	  {
		//retrieve all users
		$this->db->from('order_item');
		$this->db->select('*');
		$this->db->where('order_id = '.$order_id);
		$query = $this->db->get();
		
		return $query;    	
 
     }	

   public function delete_order_supply($order_id)
	{
		if($this->db->delete('order_item', array('order_id' => $order_id)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

  }

	public function update_invoice_charges()
	{
		$invoice_number = $this->input->post('invoice_number');
		$mark_up = $this->input->post('mark_up');
		$quantity_received = $this->input->post('quantity_received');
		$order_supplier_id = $this->input->post('order_supplier_id');
		$product_name = $this->input->post('product_name');
		$total_amount = $this->input->post('total_amount');
		$expiry_date = $this->input->post('expiry_date');
		$creditor_id = $this->input->post('creditor_id');
		$pack_size = $this->input->post('pack_size');
		
		$total_purchases = $quantity_received * $pack_size;

		$buying_price = $total_amount / $total_purchases;

		$selling_price = ((($mark_up/100) * $buying_price) + $buying_price);

		// $form_id = $this->input->post('form_id');


		$data = array(
						'invoice_number'=>$invoice_number,
						'mark_up'=>$mark_up,
						'quantity_received'=>$quantity_received,
						'expiry_date'=>$expiry_date,
						'selling_unit_price'=>$selling_price,
						'pack_size'=>$pack_size,
						'unit_price'=>$total_amount,
						'created'=>date('Y-m-d'),
						'modified_by'=>$this->session->userdata('personnel_id')
					);
		// $form_id = $this->input->post('form_id');

		// if(!empty($form_id))
		// {
		// 	$data['unit_price']=$buying_unit_price;
		// }
		$this->db->where('order_supplier_id', $order_supplier_id);
		if($this->db->update('order_supplier', $data))
		{

			$this->db->where('order_supplier_id = '.$order_supplier_id.' AND creditor_id = '.$creditor_id.' AND transaction_code = "'.$invoice_number.'"');
			$query = $this->db->get('creditor_account');

			if($query->num_rows() > 0)
			{
				// uppdate the credor
				$data_array = array('transaction_code'=>$invoice_number,
									'creditor_id'=> $creditor_id,
									'order_supplier_id'=>$order_supplier_id,
									'creditor_account_description'=> 'Delivery of '.$product_name.'',
									'creditor_account_amount'=> $total_amount,
									'creditor_account_date'=>date('Y-m-d'),
									'creditor_account_status'=>1,
									'transaction_type_id'=>2,
									// 'expense_account'=>4,
									'created_by'=>$this->session->userdata('personnel_id')
									);
				$this->db->where('order_supplier_id = '.$order_supplier_id.' AND creditor_id = '.$creditor_id.' AND transaction_code = "'.$invoice_number.'"');
				if($this->db->update('creditor_account',$data_array))
				{

					$data_array = array('transaction_code'=>$invoice_number,
										// 'creditor_id'=> $creditor_id,
										'order_supplier_id'=>$order_supplier_id,
										'creditor_account_description'=> 'Delivery of '.$product_name.'',
										'creditor_account_amount'=> $total_amount,
										'creditor_account_date'=>date('Y-m-d'),
										'creditor_account_status'=>1,
										'transaction_type_id'=>1,
										'expense_account'=>4,
										'created_by'=>$this->session->userdata('personnel_id')
									);
					if($this->db->insert('creditor_account',$data_array))
					{
						
						return TRUE;	
					}
					else
					{
						return FALSE;
					}

				}
			}
			else
			{
				// insert  
				$data_array = array('transaction_code'=>$invoice_number,
									'creditor_id'=> $creditor_id,
									'order_supplier_id'=>$order_supplier_id,
									'creditor_account_description'=> 'Delivery of '.$product_name.'',
									'creditor_account_amount'=> $total_amount,
									'creditor_account_date'=>date('Y-m-d'),
									'creditor_account_status'=>1,
									'transaction_type_id'=>2,
									// 'expense_account'=>4,
									'created_by'=>$this->session->userdata('personnel_id')
									);
				if($this->db->insert('creditor_account',$data_array))
				{
					$data_array = array('transaction_code'=>$invoice_number,
										// 'creditor_id'=> $creditor_id,
										'order_supplier_id'=>$order_supplier_id,
										'creditor_account_description'=> 'Delivery of '.$product_name.'',
										'creditor_account_amount'=> $total_amount,
										'creditor_account_date'=>date('Y-m-d'),
										'creditor_account_status'=>1,
										'transaction_type_id'=>1,
										'expense_account'=>4,
										'created_by'=>$this->session->userdata('personnel_id')
									);
					if($this->db->insert('creditor_account',$data_array))
					{
						
						return TRUE;	
					}
					else
					{
						return FALSE;
					}
				}
			}

		}
		else{
			return FALSE;
		}
	}

	public function add_order_item_supplier($order_id)
	{
		$product_id = $this->input->post('product_id');
		$quantity = 0;//$this->input->post('quantity');
		$in_stock = $this->input->post('in_stock');
		// var_dump($in_stock); die();
		//Check if item exists
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
		$query = $this->db->get('order_item');
		
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			$qty = $result->quantity;
			
			$quantity += $qty;
			
			$data = array(
					'order_item_quantity'=>$quantity,
					'in_stock'=>$in_stock
				);
			$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
			if($this->db->update('order_item', $data))
			{	
				foreach ($query->result() as $key => $value) {
					# code...
					$order_item_id = $value->order_item_id;
				}

				$creditor_id = $this->input->post('creditor_id');
				$quantity = $quantity;
				$unit_price = 0;
				$order_id = $order_id;
				$created = date('Y-m-d');
				
				$data = array(
						'supplier_id'=>$creditor_id,
						'order_item_id'=>$order_item_id,
						'order_id'=>$order_id,
						'created'=>$created				
					);

				$this->db->where($data);
				$query = $this->db->get('order_supplier');

				if($query->num_rows() > 0)
				{
					// $this->db->where($data);
					if($this->db->delete('order_supplier',$data))
					{
						$data['quantity']=$quantity;
						$data['unit_price']=0;

						if($this->db->insert('order_supplier',$data))
						{
							return TRUE;
						}
						else
						{
							return FALSE;
						}
					}
				}
				else
				{

					$data['quantity']=$quantity;
					$data['unit_price']=0;

					if($this->db->insert('order_supplier',$data))
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}

				}
				// return TRUE;
			}
			else{
				return FALSE;
			}
		}
		
		else
		{
			$data = array(
					'order_id'=>$order_id,
					'product_id'=>$product_id,
					'order_item_quantity'=>$quantity,
					'in_stock'=>$in_stock
				);
				
			if($this->db->insert('order_item', $data))
			{

				$order_item_id = $this->db->insert_id();

				$creditor_id = $this->input->post('creditor_id');
				$quantity = $quantity;
				$unit_price = 0;
				$order_id = $order_id;
				$created = date('Y-m-d');
				
				$data = array(
						'supplier_id'=>$creditor_id,
						'order_item_id'=>$order_item_id,
						'order_id'=>$order_id,
						'created'=>$created				
					);

				$this->db->where($data);
				$query = $this->db->get('order_supplier');

				if($query->num_rows() > 0)
				{
					// $this->db->where($data);
					if($this->db->delete('order_supplier',$data))
					{
						$data['quantity']=$quantity;
						$data['unit_price']=0;

						if($this->db->insert('order_supplier',$data))
						{
							return TRUE;
						}
						else
						{
							return FALSE;
						}
					}
				}
				else
				{

					$data['quantity']=$quantity;
					$data['unit_price']=0;

					if($this->db->insert('order_supplier',$data))
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}

				}
			}
			else{
				return FALSE;
			}
		}
	}
}