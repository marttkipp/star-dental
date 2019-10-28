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
		$this->db->order_by('orders.order_id','DESC');
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

	public function get_all_order_order_items($table, $where, $per_page, $page)
	{
		//retrieve all orders
		$this->db->from($table);
		$this->db->select('product.product_name, product_deductions.*,product.*');
		$this->db->where($where);
		$this->db->order_by('product_deductions.product_deductions_id','ASC');
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
		$this->db->order_by('orders.orders_date','DESC');
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


	public function get_suppliers()
	{
		$this->db->where('creditor.creditor_id > 0 ');
		$query = $this->db->get('creditor');

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

	public function get_supplier_order_details($supplier_order_id,$creditor_id)
	{
		$this->db->where('creditor.creditor_id = supplier_order.supplier_id AND orders.order_id = supplier_order.order_id AND supplier_order.order_id = '.$supplier_order_id.' AND creditor.creditor_id = '.$creditor_id);
		$query = $this->db->get('creditor,supplier_order,orders');

		return $query;
	}


	public function get_supplier_order_details_inventory($supplier_order_id)
	{
		$this->db->where('creditor.creditor_id = supplier_order.supplier_id AND orders.order_id = supplier_order.order_id AND supplier_order.order_id = '.$supplier_order_id);
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
	public function get_creditors_detail_summary($where, $table)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		// $this->db->order_by('creditor_name', 'ASC');
		$query = $this->db->get('');

		return $query;
	}
	/*
	*	Create order number
	*
	*/
	public function create_order_number()
	{

		$this->db->where('order_id > 0 ');
		$this->db->from('orders');
		$this->db->select('MAX(order_number) AS number');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;//go to the next number
			if($number == 1){
				$number = "SORD00000001";
			}
			if($number == 1)
			{
				$number = "SORD00000001";
			}

		}
		else{//start generating receipt numbers
			// $number = 1;
			$number = "SORD00000001";
		}

		// var_dump($number); die();
		return $number;



		//select product code
		// $this->db->from('orders');
		// $this->db->where("order_number LIKE 'OSH".date('y')."-%'");
		// $this->db->select('MAX(order_number) AS number');
		// $query = $this->db->get();

		// if($query->num_rows() > 0)
		// {
		// 	$result = $query->result();
		// 	$number =  $result[0]->number;
		// 	$number++;//go to the next number

		// 	if($number == 1){
		// 		$number = "OSH".date('y')."-001";
		// 	}
		// }
		// else{//start generating receipt numbers
		// 	$number = "OSH".date('y')."-001";
		// }

		// return $number;
	}


	public function create_form_order_number($prefix)
	{
		//select product code
		$this->db->where('prefix LIKE  \'%'.$prefix.'%\'');
		$this->db->from('orders');
		$this->db->select('MAX(suffix) AS number');
		$this->db->order_by('order_id','DESC');
		// $this->db->limit(1);
		$query = $this->db->get();
		// var_dump($query); die();
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			$number =  $result[0]->number;
			$number++;

		}
		else{
			$number = 1;
		}
		// var_dump($number);die();
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
	public function add_order($prefix)
	{
		// prefix

		$suffix = $this->create_form_order_number($prefix);

		// $prefix = $prefix;
		if($suffix < 100)
		{
			if($suffix < 10)
			{
				$order_number = $prefix.'/00'.$suffix;
			}
			else
			{
				$order_number = $prefix.'/0'.$suffix;
			}
		}
		else
		{
			$order_number = $prefix.'/'.$suffix;
		}




		$data = array(
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id')
			);

		$data['order_number'] = $order_number;
		$data['suffix'] = $suffix;
		$data['prefix'] = $prefix;

		// var_dump($data); die();

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

	public function add_supplier_order($prefix)
	{
		$suffix = $this->create_form_order_number($prefix);

		// $prefix = $prefix;
		if($suffix < 100)
		{
			if($suffix < 10)
			{
				$order_number = $prefix.'/00'.$suffix;
			}
			else
			{
				$order_number = $prefix.'/0'.$suffix;
			}
		}
		else
		{
			$order_number = $prefix.'/'.$suffix;
		}

		$data = array(
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id'),
				'account_id'=>$this->config->item('credit_note_supplies'),
				'supplier_id'=>$this->input->post('supplier_id'),
				'supplier_invoice_date'=>$this->input->post('supplier_invoice_date'),
				'supplier_invoice_number'=>$this->input->post('supplier_invoice_number')
			);

		$data['order_number'] = $order_number;
		$data['suffix'] = $suffix;
		$data['prefix'] = $prefix;
		// var_dump($data); die();
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

	public function add_transfer_order($prefix)
	{
		$suffix = $this->create_form_order_number($prefix);

		// $prefix = $prefix;
		if($suffix < 100)
		{
			if($suffix < 10)
			{
				$order_number = $prefix.'/00'.$suffix;
			}
			else
			{
				$order_number = $prefix.'/0'.$suffix;
			}
		}
		else
		{
			$order_number = $prefix.'/'.$suffix;
		}

		$data = array(
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id'),
				'supplier_id'=>$this->input->post('supplier_id'),
				'is_store'=>2
			);

		$data['order_number'] = $order_number;
		$data['suffix'] = $suffix;
		$data['prefix'] = $prefix;

		// var_dump($data);die();
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


	public function add_credit_note_order($prefix,$deduction_type_id=0)
	{
		$order_number = $this->create_order_number();

		// reference number
		$reference = $this->input->post('reference_id');
		$exploded = explode('.', $reference);

		$reference_id = $exploded[0];
		$reference_number = $exploded[1];
		$data = array(
				'order_number'=>$order_number,
				'created_by'=>$this->input->post('personnel_id'),
				'order_status_id'=>1,
				'order_instructions'=>$this->input->post('order_instructions'),
				'created'=>date('Y-m-d H:i:s'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'store_id'=>$this->input->post('store_id'),
				'account_id'=>$this->config->item('credit_note_id'),
				'supplier_id'=>$this->input->post('supplier_id'),
				'supplier_invoice_date'=>$this->input->post('supplier_invoice_date'),
				'supplier_invoice_number'=>$this->input->post('credit_note_number'),
				'is_store'=>3,
				'reference_id'=>$reference_id,
				'reference_number'=>$reference_number
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

		// $in_stock = $this->input->post('in_stock');
		// var_dump($in_stock); die();
		//Check if item exists
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
		$query = $this->db->get('order_item');
		$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		$parent_stock = $this->inventory_management_model->parent_stock_store($inventory_start_date, $product_id,5);
		$child_stock = $this->inventory_management_model->get_other_stores_stock($inventory_start_date, $product_id,5);
		// var_dump($child_stock);die();
		$in_stock = $parent_stock + $child_stock;
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			$qty = $result->purchase_quantity;

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
					'in_stock'=>$in_stock,
					'parent_stock'=>$parent_stock,
					'child_stock'=>$child_stock
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
		$product_id = $this->input->post('product_id');
		$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		$parent_stock = $this->inventory_management_model->parent_stock_store($inventory_start_date, $product_id,5);
		$child_stock = $this->inventory_management_model->get_other_stores_stock($inventory_start_date, $product_id,5);
		// var_dump($child_stock);die();
		$in_stock = $parent_stock + $child_stock;
		$data = array(
					'order_item_quantity'=>$this->input->post('quantity'),
					'in_stock'=>$in_stock,
					'parent_stock'=>$parent_stock,
					'child_stock'=>$child_stock
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

		$personnel_id = $this->session->userdata('personnel_id');
		if($personnel_id == 0)
		{
			return TRUE;
		}
		else
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
		$mark_up = 33;//$this->input->post('mark_up');
		$quantity_received = $this->input->post('quantity_received');
		$discount = $this->input->post('discount');
		$vat = $this->input->post('vat');
		$order_supplier_id = $this->input->post('order_supplier_id');
		$product_name = $this->input->post('product_name');
		$total_amount = $this->input->post('total_amount');
		$expiry_date = $this->input->post('expiry_date');
		$creditor_id = $this->input->post('creditor_id');
		$pack_size = $this->input->post('pack_size');
		$discount = $this->input->post('discount');
		$vat = $this->input->post('vat');
		$product_id = $this->input->post('product_id');
		$product_unitprice = $this->input->post('product_unitprice');
		$buying_price_vat = $this->input->post('buying_price_vat');

		$total_purchases = $quantity_received * $pack_size;
		// single unit price
		if($vat > 0 AND $buying_price_vat == 0)
		{
			$total_amount = 1.16 * $total_amount;
		}
		// var_dump($total_amount); die();
		$buying_price = $total_amount / $pack_size;


		$total_purchase_amount = $buying_price;





		if($vat > 0)
		{
			$total_price_vat = $total_purchase_amount +  (($vat/100)*$total_purchase_amount);
			$array_product['vatable'] = 1;
			$array_charge['vatable'] = 1;
		}
		else
		{
			$array_product['vatable'] = 0;
			$array_charge['vatable'] = 0;
			$total_price_vat = 0;
		}

		if($discount > 0)
		{
			$total_purchase_amount = $total_purchase_amount - (($discount/100)*$total_purchase_amount);
		}
		// if($total_price_vat > 0)
		// {
		// 	$buying_price = $total_price_vat;
		// }

		// $gross_amount = $total_purchase_amount * ($quantity_received*$pack_size);
		$selling_price = ((($mark_up/100) * $buying_price) + $buying_price);



		$gross_amount = $buying_price * ($quantity_received*$pack_size);



		$less_vat = $gross_amount * ((100 - $discount)/100);
		$gross_amount = $less_vat * ((100+$vat)/100);
		// var_dump($less_vat); die();

		$data = array(
						'invoice_number'=>$invoice_number,
						'mark_up'=>$mark_up,
						'quantity_received'=>$quantity_received,
						'expiry_date'=>$expiry_date,
						'selling_unit_price'=>$selling_price,
						'pack_size'=>$pack_size,
						'unit_price'=>$total_amount,
						'discount'=>$discount,
						'buying_price_vat'=>$buying_price_vat,
						'vat'=>$vat,
						'created'=>date('Y-m-d'),
						'total_amount'=> $gross_amount,
						'less_vat'=> $less_vat,
						'modified_by'=>$this->session->userdata('personnel_id')
					);


		$this->db->where('order_supplier_id', $order_supplier_id);
		if($this->db->update('order_supplier', $data))
		{

			// $this->db->where('product_id',$product_id);
			// $query =$this->db->get('product');
			// $product_row = $query->row();

			if(!empty($product_id) AND $selling_price > 0)
			{
				if(empty($product_unitprice))
				{
					$array_product['product_unitprice'] = $selling_price;
					$this->db->where('product_id',$product_id);
					$this->db->update('product',$array_product);
				}

				$this->db->where('product_id',$product_id);
				$array_charge['service_charge_amount'] = $selling_price;
				$this->db->update('service_charge',$array_charge);


			}



				return TRUE;

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
			$qty = $result->order_item_quantity;

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

				// $this->db->where($data);
				// $query = $this->db->get('order_supplier');

				// if($query->num_rows() > 0)
				// {
				// 	// $this->db->where($data);
				// 	if($this->db->delete('order_supplier',$data))
				// 	{
				// 		$data['quantity']=$quantity;
				// 		$data['unit_price']=0;

				// 		if($this->db->insert('order_supplier',$data))
				// 		{
				// 			return TRUE;
				// 		}
				// 		else
				// 		{
				// 			return FALSE;
				// 		}
				// 	}
				// }
				// else
				// {

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

				// }
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

				// $this->db->where($data);
				// $query = $this->db->get('order_supplier');

				// if($query->num_rows() > 0)
				// {
				// 	// $this->db->where($data);
				// 	if($this->db->delete('order_supplier',$data))
				// 	{
				// 		$data['quantity']=$quantity;
				// 		$data['unit_price']=0;

				// 		if($this->db->insert('order_supplier',$data))
				// 		{
				// 			return TRUE;
				// 		}
				// 		else
				// 		{
				// 			return FALSE;
				// 		}
				// 	}
				// }
				// else
				// {

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

				// }
			}
			else{
				return FALSE;
			}
		}
	}

	public function get_ordered_list($order_id)
	{
		$this->db->select('product_deductions.quantity_requested AS supplying,product.product_unitprice AS single_price,product.product_packsize AS pack_size,product.product_unitprice, product.*,product_deductions.*,product_deductions.product_deductions_id AS item_id,product_deductions.quantity_requested AS order_item_quantity');
		$this->db->where('product_deductions.order_id = '.$order_id.'  AND product_deductions.product_id = product.product_id ');
		$this->db->order_by('product_deductions_id');
		$query = $this->db->get('product_deductions,product');

		return $query;

	}



	public function add_order_item_supplied($order_id)
	{
		$product_id = $this->input->post('product_id');
		$quantity = 0;//$this->input->post('quantity');
		$in_stock = $this->input->post('in_stock');
		// var_dump($in_stock); die();
		//Check if item exists
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
		$query = $this->db->get('product_deductions');

		if($query->num_rows() > 0)
		{
			return true;
		}

		else
		{

			$data = array(
						  'order_id' => $order_id,
						  'product_id'=> $product_id,
						  'date_requested'=>date('Y-m-d H:i:s'),
						  'search_date'=>date('Y-m-d'),
						  'requested_by'=>$this->session->userdata('personnel_id')
						);
			if($this->db->insert('product_deductions', $data))
			{
				$product_deductions_id = $this->db->insert_id();
				return $product_deductions_id;
			}
			else
			{
				return FALSE;
			}

		}
	}


	public function get_creditors_invoices($creditor_id)
	{

		$this->db->where('is_store = 0 AND order_approval_status = 7 AND supplier_id = '.$creditor_id);
		$this->db->order_by('supplier_invoice_number');
		$query = $this->db->get('orders');


		return $query;
	}
}
