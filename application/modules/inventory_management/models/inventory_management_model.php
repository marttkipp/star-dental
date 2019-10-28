<?php

class Inventory_management_model extends CI_Model
{
	public function get_suppliers()
	{
		$this->db->order_by('supplier_name');
		return $this->db->get('supplier');
	}
	public function check_store_product($store_id, $product_id)
	{
		$this->db->where(array('owning_store_id' => $store_id, 'product_id' => $product_id));
		$query = $this->db->get('store_product');

		if($query->num_rows() == 0)
		{
			$this->db->insert('store_product', array('store_id' => $store_id, 'product_id' => $product_id));
		}
	}


	public function get_pharmacy_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code)
	{
		$table = 'visit, pres, service_charge,visit_charge';
		$where = 'pres.service_charge_id = service_charge.service_charge_id AND visit_charge.visit_charge_id = pres.visit_charge_id AND pres.visit_id = visit.visit_id AND visit.visit_delete = 0 AND service_charge.product_id = '. $product_id.' AND visit.visit_date >= "'.$inventory_start_date.'"';
		if($branch_code != NULL)
		{
			$where .= ' AND visit.branch_code = "'.$branch_code.'"';
		}
		if(($search_start_date != NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND visit.visit_date >= "'.$search_start_date.'" AND visit.visit_date<= "'.$search_end_date.'"';
		}

		else if(($search_start_date == NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND visit.visit_date = "'.$search_end_date.'"';
		}

		else if(($search_start_date != NULL) && ($search_end_date == NULL))
		{
			 $where .= ' AND visit.visit_date = "'.$search_start_date.'"';
		}
		$items = "SUM(visit_charge.visit_charge_units) AS total_sold";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}
	public function get_drug_units_sold($inventory_start_date, $product_id, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{
		$table = "visit_charge";
		$where = 'visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0 AND visit_charge.product_id ='.$product_id;

		if($branch_code != NULL)
		{
			$where .= ' AND visit_charge.branch_code = "'.$branch_code.'"';
		}
		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit_charge.date >= "'.$start_date.'" AND visit_charge.date<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit_charge.date <= "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND visit_charge.date >= "'.$start_date.'"';
		}

		$items = "SUM(visit_charge.visit_charge_units) AS total_sold,product_id";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}


	public function get_drug_units_sold_today($visit_type_id, $product_id, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{
		$table = "visit_charge,visit";
		$where = 'visit_charge.charged = 1 AND visit.visit_id = visit_charge.visit_id AND visit.visit_delete = 0 AND visit.visit_type = '.$visit_type_id.' AND visit_charge.product_id ='.$product_id;

		if($branch_code != NULL)
		{
			$where .= ' AND visit_charge.branch_code = "'.$branch_code.'"';
		}
		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit.visit_date >= "'.$start_date.'" AND visit.visit_date<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit.visit_date <= "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND visit.visit_date >= "'.$start_date.'"';
		}
		// var_dump($where); die();
		$items = "SUM(visit_charge.visit_charge_units) AS total_sold,product_id";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}

	public function view_get_drug_units_sold($inventory_start_date, $product_id, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{
		$table = "v_product_sales";
		$where = 'v_product_sales.product_id ='.$product_id;
		$items = "total_sold";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}

	public function get_drug_units_sold_in_visit($visit_id)
	{
		$table = "visit_charge, service_charge";
		$where = 'visit_charge.visit_id = '.$visit_id.' AND visit_charge.service_charge_id = service_charge.service_charge_id AND product_id > 0';
		$items = "visit_charge_units, service_charge.product_id";
		$order = "product_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		$response = array();
		if(count($result) > 0)
		{
			foreach ($result as $key)
			{
				# code...
				$product_id = $key->product_id;
				$visit_charge_units = $key->visit_charge_units;

				$item = array(
					'visit_charge_units' => $visit_charge_units,
					'product_id' => $product_id
				);

				array_push($response, $item);
			}
		}
		return $response;
	}

	public function get_product_list($table, $where, $per_page, $page, $order)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('product.*,store.store_name,store.in_service_charge_status,store.store_id');
		$this->db->where($where);
		$this->db->order_by($order,'asc');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}


	public function item_purchases($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
  		$table = "product_purchase, product";
		$where = "product_purchase.purchase_date >= '".$inventory_start_date."' AND product.product_id = ".$product_id." AND product_purchase.product_id = product.product_id";
		$items = "product_purchase.purchase_pack_size, product_purchase.purchase_quantity";
		$order = "purchase_pack_size";

		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND product_purchase.purchase_date >= "'.$start_date.'" AND product_purchase.purchase_date<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND product_purchase.purchase_date = "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND product_purchase.purchase_date = "'.$start_date.'"';
		}

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$purchase_pack_size = $row2->purchase_pack_size;
				$purchase_quantity = $row2->purchase_quantity;
				$total = $total + ($purchase_pack_size * $purchase_quantity);
			}
		}
		return $total;
	}



	public function product_purchases($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0 AND orders.is_store < 2 AND product.product_id = ".$product_id;
		$table = 'order_item,order_supplier,product,orders';
		$select = 'SUM(quantity_received*pack_size) AS total_purchases';
		$this->db->where($where);
		$this->db->select($select);
		$query_supplier = $this->db->get($table);
		$total_purchases = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$total_purchases = $query_rs->total_purchases;
		}
		return $total_purchases;
	}
	public function product_credit_notes($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0 AND orders.is_store = 3 AND product.product_id = ".$product_id;
		$table = 'order_item,order_supplier,product,orders';
		$select = 'SUM(quantity_received*pack_size) AS total_purchases';
		$this->db->where($where);
		$this->db->select($select);
		$query_supplier = $this->db->get($table);
		$total_purchases = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$total_purchases = $query_rs->total_purchases;
		}
		return $total_purchases;
	}

	public function product_transfers($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$where = "product_deductions.order_id = orders.order_id AND product_deductions.product_id = product.product_id AND orders.supplier_id > 0 AND orders.is_store = 2 AND orders.order_approval_status = 7 AND product.product_id = ".$product_id;
		$table = 'product_deductions,product,orders';
		$select = 'SUM(quantity_given*pack_size) AS total_transfers,product.product_id';
		$this->db->where($where);
		$this->db->select($select);
		$query_supplier = $this->db->get($table);
		$total_transfers = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_transfers = $query_rs->total_transfers;

		}

		if(empty($total_transfers))
		{
			$total_transfers = 0;
		}
		return $total_transfers;
	}


	public function view_product_transfers($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$where = "v_product_transfers.product_id = ".$product_id;
		$table = 'v_product_transfers';
		$select = 'v_product_transfers.total_transfers';
		$this->db->where($where);
		$this->db->select($select);
		$query_supplier = $this->db->get($table);
		$total_transfers = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_transfers = $query_rs->total_transfers;
		}

		if(empty($total_transfers))
		{
			$total_transfers = 0;
		}
		return $total_transfers;
	}

	public function get_product_quantity($product_id)
	{
		$table = "product";
		$where = "product.product_id = ".$product_id;
		$items = "quantity";
		$order = "product_id";


		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$quantity = $row2->quantity;
				$total = $total + $quantity;
			}
		}
		return $total;
	}

	public function item_proccured_old($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
  		$table = "order_item, order_supplier,product";
		$where = "order_supplier.created >= '".$inventory_start_date."' AND order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = ".$product_id." AND order_item.product_id = product.product_id";
		$items = "order_supplier.quantity_received,order_supplier.pack_size";
		$order = "order_supplier_id";

		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND order_supplier.created >= "'.$start_date.'" AND order_supplier.created<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND order_supplier.created <= "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND order_supplier.created >= "'.$start_date.'"';
		}

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
	public function item_proccured($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
  		$table = "order_item, order_supplier,product";
		$where = "order_supplier.created >= '".$inventory_start_date."' AND order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = ".$product_id." AND order_item.product_id = product.product_id";
		$items = "SUM(order_supplier.quantity_received*order_supplier.pack_size) AS total_purchases";
		$order = "order_supplier_id";

		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND order_supplier.created >= "'.$start_date.'" AND order_supplier.created<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND order_supplier.created <= "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND order_supplier.created >= "'.$start_date.'"';
		}

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total_purchases = 0;
		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$total_purchases = $row2->total_purchases;
			}
		}
		return $total_purchases;
	}
	public function item_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		if($store_id == NULL)
		{
			$table = "product_deductions, product";
			$where = "product_deductions.product_deductions_date >= '".$inventory_start_date."' AND product_deductions.product_deduction_rejected = 0 AND product.product_id = ".$product_id." AND product_deductions.product_id = product.product_id";
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
			$items = "product_deductions.product_deductions_pack_size, product_deductions.product_deductions_quantity,quantity_given";
			$order = "product_deductions_pack_size";

			$result = $this->database->select_entries_where($table, $where, $items, $order);

			$total = 0;

			if(count($result) > 0){

				foreach ($result as $row2)
				{
					// $product_deductions_pack_size = $row2->product_deductions_pack_size;
					// $product_deductions_quantity = $row2->product_deductions_quantity;
					$quantity_given = $row2->quantity_given;
					// $total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
					$total = $total + $quantity_given;
				}
			}
		}
		return $total;
	}


	public function stock_item_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$table = "product_deductions_stock, product";
		$where = "product.product_id = ".$product_id." AND product_deductions_stock.product_id = product.product_id AND product_deductions_stock.store_id = ".$store_id;
		$items = "product_deductions_stock.product_deductions_stock_pack_size, product_deductions_stock.product_deductions_stock_quantity,quantity_given";
		$order = "product_deductions_stock_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$product_deductions_stock_pack_size = $row2->product_deductions_stock_pack_size;
				$product_deductions_stock_quantity = $row2->product_deductions_stock_quantity;
				$quantity_given = $row2->quantity_given;
				$total = $total + ($product_deductions_stock_pack_size * $product_deductions_stock_quantity);

			}
		}

		return $total;
	}
	public function product_deducted($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 1 AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$product_id;
		$items = "SUM(quantity_given) AS quantity_given,product.product_id";
		$order = "product_deductions_pack_size";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}


	public function view_product_deducted($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_product_deducted_main";
		$where = "v_product_deducted_main.product_id =".$product_id;
		$items = "quantity_given";
		$order = "v_product_deducted_main.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}
	public function product_added($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 1 AND orders.store_id = ".$store_id." AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$product_id;
		$items = "SUM(quantity_given) AS quantity_given,product.product_id";
		$order = "product_deductions_pack_size";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}


	public function view_product_added($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_product_additions_stores";
		$where = "v_product_additions_stores.store_id = ".$store_id." AND v_product_additions_stores.product_id=".$product_id;
		$items = "quantity_given";
		$order = "v_product_additions_stores.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}

	public function v_not_awarded_requests($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_not_awarded_requests";
		$where = "v_not_awarded_requests.product_id = ".$product_id;
		$items = "total_requested";
		$order = "v_not_awarded_requests.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$total_requested = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_requested = $query_rs->total_requested;
		}
		return $total_requested;


	}

	public function v_parent_items_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_parent_item_deductions";
		$where = "v_parent_item_deductions.product_id = ".$product_id;
		$items = "total_quantity_given";
		$order = "v_parent_item_deductions.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$total_quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_quantity_given = $query_rs->total_quantity_given;
		}
		return $total_quantity_given;


	}


	public function view_product_purchases($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_product_purchases";
		$where = "v_product_purchases.product_id = ".$product_id;
		$items = "total_purchases";
		$order = "v_product_purchases.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$total_purchases = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_purchases = $query_rs->total_purchases;
		}
		return $total_purchases;


	}


	public function view_product_deductions_store($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "v_product_additions_stores";
		$where = "v_product_additions_stores.product_id=".$product_id;
		$items = "SUM(quantity_given) AS total_given";
		$order = "v_product_additions_stores.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$total_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$total_given = $query_rs->total_given;
		}
		return $total_given;


	}

	public function product_disbersed($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		if(empty($store_id))
		{
			$store_values = '';
		}
		else
		{
			$store_values = ' AND product_deductions.store_id = '.$store_id;
		}
		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id ".$store_values." AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND product_deductions.quantity_requested > 0 AND product_deductions.product_deduction_rejected = 0 AND orders.order_id = product_deductions.order_id AND (product_deductions.visit_charge_id IS NOT NULL OR product_deductions.visit_charge_id <> 0) AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$product_id;
		$items = "SUM(quantity_given) AS quantity_given,product.product_id";
		$order = "product_deductions_pack_size";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}


	public function view_product_disbersed_main($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$table = "v_product_disbersed_main";
		$where = "v_product_disbersed_main.product_id =".$product_id;
		$items = "quantity_given";
		$order = "v_product_disbersed_main.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}
	public function view_product_disbersed_store($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		$table = "v_product_disbersed_store";
		$where = "v_product_disbersed_store.product_id =".$product_id." AND v_product_disbersed_store.store_id = ".$store_id;
		$items = "quantity_given";
		$order = "v_product_disbersed_store.product_id";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}
	public function parent_item_deductions_old($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		//  get all child stores
		$this->db->where('store_parent',$store_id);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}
		$table = "product_deductions, product,orders";
		$where = "product.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0 AND product_deductions.product_id = product.product_id AND orders.order_id = product_deductions.order_id AND orders.is_store < 2 AND product_deductions.quantity_given > 0 ".$child_where;
		$items = "product_deductions.product_deductions_pack_size, product_deductions.product_deductions_quantity,quantity_given";
		$order = "product_deductions_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				// $product_deductions_pack_size = $row2->product_deductions_pack_size;
				// $product_deductions_quantity = $row2->product_deductions_quantity;
				$quantity_given = $row2->quantity_given;
				// $total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
				$total = $total + $quantity_given;
			}
		}
		return $total;
	}
	public function parent_item_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		//  get all child stores
		$this->db->where('store_parent',$store_id);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}
		$table = "product_deductions,orders";
		$where = "product_deductions.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0 AND orders.order_id = product_deductions.order_id AND orders.is_store < 2 AND product_deductions.quantity_given > 0 ".$child_where;
		$items = "SUM(quantity_given) AS total_quantity_given";
		$order = "product_deductions_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total_quantity_given = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$total_quantity_given = $row2->total_quantity_given;
			}
		}
		return $total_quantity_given;
	}


	public function parent_transfer_item_deductions_old($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		//  get all child stores
		$this->db->where('store_parent',$store_id);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}
		$table = "product_deductions, product,orders";
		$where = "product.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0 AND product_deductions.product_id = product.product_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 2 AND product_deductions.quantity_given > 0 ".$child_where;
		$items = "product_deductions.product_deductions_pack_size, product_deductions.product_deductions_quantity,quantity_given";
		$order = "product_deductions_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				// $product_deductions_pack_size = $row2->product_deductions_pack_size;
				// $product_deductions_quantity = $row2->product_deductions_quantity;
				$quantity_given = $row2->quantity_given;
				// $total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
				$total = $total + $quantity_given;
			}
		}
		return $total;
	}

	public function parent_transfer_item_deductions($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{

		//  get all child stores
		$this->db->where('store_parent',$store_id);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}
		$table = "product_deductions, product,orders";
		$where = "product.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0 AND product_deductions.product_id = product.product_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 2 AND product_deductions.quantity_given > 0 ".$child_where;
		$items = "SUM(quantity_given) AS total_transfers";
		$order = "product_deductions_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total_transfers = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$total_transfers = $row2->total_transfers;
			}
		}
		return $total_transfers;
	}

	public function item_deductions_requested_old($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$store_parent = $this->get_parent_store($store_id);

		if($store_parent == 0)
		{
			$store_parent = $store_id;
		}
		//  get all child stores
		$this->db->where('store_parent',$store_parent);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}

		// var_dump($child_where); die();
		$table = "product_deductions, product";
		$where = "product.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0  AND product_deductions.product_id = product.product_id AND product_deductions.visit_charge_id > 0 AND (product_deductions.quantity_given IS NULL OR product_deductions.quantity_given = 0) ".$child_where;
		$items = "product_deductions.product_deductions_pack_size, product_deductions.quantity_requested,quantity_givens";
		$order = "product_deductions_pack_size";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				// $product_deductions_pack_size = $row2->product_deductions_pack_size;
				// $product_deductions_quantity = $row2->product_deductions_quantity;
				$quantity_requested = $row2->quantity_requested;
				// $total = $total + ($product_deductions_pack_size * $product_deductions_quantity);
				$total = $total + $quantity_requested;
			}
		}
		return $total;
	}

	public function item_deductions_requested($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$store_parent = $this->get_parent_store($store_id);

		if($store_parent == 0)
		{
			$store_parent = $store_id;
		}
		//  get all child stores
		$this->db->where('store_parent',$store_parent);
		$query = $this->db->get('store');
		$child_where ='';
		if($query->num_rows() > 0)
		{
			$store_count = 0;
			$total_count = $query->num_rows();
			$child_where .=' AND ( ';
			foreach ($query->result() as $key => $value) {
				# code...
				$child_store_id = $value->store_id;

				$child_where .= ' product_deductions.store_id = '.$child_store_id;
				$store_count++;
				if($store_count < $total_count)
				{
					$child_where .= ' OR ';
				}
			}
			$child_where .='  ) ';
		}

		// var_dump($child_where); die();
		$table = "product_deductions";
		$where = "product_deductions.product_id = ".$product_id." AND product_deductions.product_deduction_rejected = 0  AND product_deductions.visit_charge_id > 0  AND (product_deductions.quantity_given IS NULL OR product_deductions.quantity_given = 0) ".$child_where;
		$items = "SUM(product_deductions.quantity_requested) AS total_requested";
		$order = "product_deductions_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		$total_requested = 0;

		if(count($result) > 0){

			foreach ($result as $row2)
			{
				$total_requested = $row2->total_requested;
			}
		}
		if(empty($total_requested))
		{
			$total_requested = 0;
		}
		return $total_requested;
	}

	public function get_product_units_sold($product_id)
	{
		$table = "visit_charge, service_charge";
		$where = "visit_charge.service_charge_id = service_charge.service_charge_id AND service_charge.product_id = ". $product_id;
		$items = "SUM(visit_charge.visit_charge_units) AS total_sold";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}

	public function purchase_product($product_id, $product_deductions_id = NULL)
	{
		if($product_deductions_id != NULL)
		{
			//check if exists
			$this->db->where('product_deductions_id', $product_deductions_id);
			$query = $this->db->get('product_purchase');

			if($query->num_rows() == 0)
			{
				$array = array(
					'product_deductions_id'=>$product_deductions_id,
					'product_id'=>$product_id,
					'purchase_quantity'=>$this->input->post('purchase_quantity'),
					'purchase_pack_size'=>$this->input->post('purchase_pack_size'),
					'created'=>date('Y-m-d H:i:s'),
					'purchase_date'=>$this->input->post('purchase_date'),
					'created_by'=>$this->session->userdata('personnel_id'),
					'modified_by'=>$this->session->userdata('personnel_id'),
					'expiry_date'=>$this->input->post('expiry_date'),
					'store_id'=>5
				);
				if($this->db->insert('product_purchase', $array))
				{
					return TRUE;
				}
				else{
					return FALSE;
				}
			}

			else
			{
				$array = array(
					'product_deductions_id'=>$product_deductions_id,
					'product_id'=>$product_id,
					'purchase_quantity'=>$this->input->post('purchase_quantity'),
					'purchase_pack_size'=>$this->input->post('purchase_pack_size'),
					'purchase_description'=>$this->input->post('purchase_description'),
					'created'=>date('Y-m-d H:i:s'),
					'modified_by'=>$this->session->userdata('personnel_id'),
					'expiry_date'=>$this->input->post('expiry_date'),
					'purchase_date'=>date('Y-m-d'),
					'store_id'=>5
				);
				$this->db->where('product_deductions_id', $product_deductions_id);
				if($this->db->update('product_purchase', $array))
				{
					return TRUE;
				}
				else{
					return FALSE;
				}
			}
		}

		else
		{
			$array = array(
				'product_deductions_id'=>$product_deductions_id,
				'product_id'=>$product_id,
				'purchase_quantity'=>$this->input->post('purchase_quantity'),
				'purchase_pack_size'=>$this->input->post('purchase_pack_size'),
				'purchase_description'=>$this->input->post('purchase_description'),
				'created'=>date('Y-m-d H:i:s'),
				'purchase_date'=>date('Y-m-d'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'expiry_date'=>$this->input->post('expiry_date'),
				'store_id'=>5
			);
			if($this->db->insert('product_purchase', $array))
			{
				return TRUE;
			}
			else{
				return FALSE;
			}
		}
	}

	public function edit_product_purchase($product_purchase_id)
	{
		$array = array(
			//'product_id'=>$product_id,
			'purchase_quantity'=>$this->input->post('purchase_quantity'),
			'purchase_pack_size'=>$this->input->post('purchase_pack_size'),
			'purchase_description'=>$this->input->post('purchase_description'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'expiry_date'=>$this->input->post('expiry_date'),
			'purchase_date'=>$this->input->post('purchase_date')
		);
		$this->db->where('purchase_id', $product_purchase_id);
		if($this->db->update('product_purchase', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function get_purchase_details($product_purchase_id)
	{
		$this->db->where('purchase_id', $product_purchase_id);
		$query = $this->db->get('product_purchase');

		return $query;
	}

	public function get_product_deductions_stock($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);

		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order,'ASC');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}

	public function get_product_deductions($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);
		$this->db->join('personnel','orders.created_by = personnel.personnel_id','left');
		// $this->db->select('store_product.*,store.store_name,product_deductions.*,product.product_name,product.product_id,product.quantity AS parent_store_qty ');
		//$this->db->select('store.store_name, orders.*, product.product_name, product.product_id, product.quantity AS parent_store_qty ');
		$this->db->select('store.store_name, orders.*, personnel.*');
		$this->db->where($where);
		$this->db->order_by($order,'ASC');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}

	public function deduct_product($product_id,$store_id)
	{
		// $product_details = $this->get_inventory_product_details($product_id);
		// if($product_details->num_rows() > 0)
		// {
		// 	$row = $product_details->row();
		// 	$store_id = $row->store_id;
		// }
		$array = array(
			'product_id'=>$product_id,
			'deduction_description'=>$this->input->post('deduction_description'),
			'product_deductions_stock_quantity'=>$this->input->post('product_deduction_quantity'),
			'product_deductions_stock_pack_size'=>$this->input->post('product_deduction_pack_size'),
			'given_by'=>$this->session->userdata('personnel_id'),
			'store_id'=>$store_id,
			'product_deductions_stock_date'=>$this->input->post('deduction_date')
		);
		if($this->db->insert('product_deductions_stock', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function return_product($product_id,$store_id)
	{

		$array = array(
			'product_id'=>$product_id,
			'deduction_description'=>$this->input->post('deduction_description'),
			'product_deductions_stock_quantity'=>$this->input->post('product_deduction_quantity'),
			'product_deductions_stock_pack_size'=>$this->input->post('product_deduction_pack_size'),
			'given_by'=>$this->session->userdata('personnel_id'),
			'from_store_id'=>$store_id,
			'to_store_id'=>$this->input->post('store_id')
		);
		if($this->db->insert('product_return_stock', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function edit_product_deduction($product_deduction_id)
	{
		// var_dump($_POST); die();
		$array = array(
			'deduction_description'=>$this->input->post('deduction_description'),
			'product_deductions_stock_quantity'=>$this->input->post('product_deductions_quantity'),
			'product_deductions_stock_pack_size'=>$this->input->post('product_deductions_pack_size'),
			'product_deductions_stock_date'=>$this->input->post('deduction_date')
		);
		$this->db->where('product_deductions_stock_id', $product_deduction_id);
		if($this->db->update('product_deductions_stock', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function get_deduction_details($product_deduction_id)
	{
		$this->db->where('product_deductions_stock_id', $product_deduction_id);
		$query = $this->db->get('product_deductions_stock');

		return $query;
	}


	public function get_product_purchase_details($product_id)
	{
		$table = "product_purchase";
		$where = "product_id = '$product_id'";
		$items = "*";
		$order = "product_id";

		$result = $this->database->select_entries_where($table, $where, $items, $order);

		return $result;
	}
	public function get_all_generics()
	{
		$table = "generic";
		$items = "*";
		$order = "generic_id";
		$where = 'generic_id > 0';
		$this->db->order_by('generic_name');
		$result = $this->db->get($table);

		return $result;
	}
	public function get_all_brands()
	{
		$table = "brand";
		$items = "*";
		$order = "brand_id";
		$where = 'brand_id > 0';

		$this->db->order_by('brand_name');
		$result = $this->db->get($table);

		return $result;
	}
	public function save_product()
	{
		$name = ucwords(strtolower($this->input->post('product_name')));
		$unit_of_measure = $this->input->post('unit_of_measure');

		if($this->input->post('category_id') == 2)
		{
			// get the administration route nam
			// $this->db->where('drug_type_id = '.$this->input->post('drug_type_id'));
			// $query = $this->db->get('drug_type');

			// if($query->num_rows() > 0)
			// {
			// 	foreach ($query->result() as $key) {
			// 		# code...
			// 		$drug_type_name = $key->drug_type_name;
			// 	}
			// }
		}
		$product_name = $name;

		$product_code = $this->input->post('product_code');

		if(empty($product_code))
		{
			$product_code = $this->create_product_code();
		}
		$array = array(
			'product_name'=>$product_name,
			'product_status'=>1,
			'product_description'=>$this->input->post('product_description'),
			'category_id'=>$this->input->post('category_id'),
			'quantity'=>$this->input->post('quantity'),
			'batch_no'=>$this->input->post('batch_no'),
			'product_unitprice'=> $this->input->post('product_unitprice'),
			'product_unitprice_insurance'=> $this->input->post('product_unitprice_insurance'),
			'product_code'=>$product_code,
			'store_id'=>$this->input->post('store_id'),
			'created'=>date('Y-m-d'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'product_packsize'=>$this->input->post('product_pack_size'),
			'unit_of_measure'=>$this->input->post('unit_of_measure'),
			'reorder_level'=>$this->input->post('reorder_level'),
			'max_reorder_level'=>$this->input->post('max_reorder_level'),
			'brand_id'=>$this->input->post('brand_id'),
			'class_id'=>$this->input->post('class_id'),
			'generic_id'=>$this->input->post('generic_id'),
			'vatable'=>$this->input->post('vatable'),
			'drug_type_id'=>$this->input->post('drug_type_id'),
			'is_synced'=>1,
			'stock_take'=>1
		);
		//save product in the db
		if($this->db->insert('product', $array))
		{
			//calculate the price of the drug
			$product_id = $this->db->insert_id();

			// parent store
			 $store_id = $this->input->post('store_id');
			if($store_id == 5)
			{
				$product_store_quantity = array(
								'product_id' => $product_id,
								'owning_store_id' => 5,
								'store_quantity' => $this->input->post('quantity'),
								'created'=>date('Y-m-d')
							);
				$this->db->insert('store_product', $product_store_quantity);

				if($this->input->post('category_id') == 2)
				{

					$product_store_quantity = array(
										'product_id' => $product_id,
										'owning_store_id' => 6,
										'store_quantity' => $this->input->post('quantity'),
										'created'=>date('Y-m-d')
									);
					$this->db->insert('store_product', $product_store_quantity);
				}
			}
			else
			{
				$product_store_quantity = array(
								'product_id' => $product_id,
								'owning_store_id' => $store_id,
								'store_quantity' => 0
							);
				$this->db->insert('store_product', $product_store_quantity);
			}



			$category_id = $this->input->post('category_id');
			if($category_id == 2)
			{
				$service_id = 4;
			}
			else if($category_id == 3)
			{
				$service_id = 9;
			}
			else if($category_id == 4)
			{
				$service_id = 8;
			}
			else if($category_id == 5)
			{
				$service_id = 6;
			}
			else
			{
				$service_id = 5;
			}
			$product_unitprice = $this->input->post('product_unitprice');

			// update store items on the charged place


			if($category_id == 2)
			{

				$service_charge_insert = array(
												"service_charge_name" => $product_name,
												"service_id" => $service_id,
												"visit_type_id" => 1,
												"product_id" => $product_id,
												"service_charge_amount" => $product_unitprice,
												'service_charge_status' => 1,
												"vatable"=>$this->input->post('vatable')
												);

				$service_charge_insert['created'] = date('Y-m-d H:i:s');
				$service_charge_insert['created_by'] = $this->session->userdata('personnel_id');
				$service_charge_insert['modified_by'] = $this->session->userdata('personnel_id');

				$this->db->insert('service_charge', $service_charge_insert);

			}


			return TRUE;
		}

		else
		{
			return FALSE;
		}
	}

	public function edit_product($product_id)
	{
		$name = ucwords(strtolower($this->input->post('product_name')));
		$unit_of_measure = $this->input->post('unit_of_measure');


		if($this->input->post('category_id') == 2)
		{
			// get the administration route nam
			$this->db->where('drug_type_id = '.$this->input->post('drug_type_id'));
			$query = $this->db->get('drug_type');

			if($query->num_rows() > 0)
			{
				foreach ($query->result() as $key) {
					# code...
					$drug_type_name = $key->drug_type_name;
				}
			}
		}
		$product_name = $name;
		$array = array(
			'product_name'=>$product_name,
			'product_status'=>1,
			'product_description'=>$this->input->post('product_description'),
			'category_id'=>$this->input->post('category_id'),
			'quantity'=>$this->input->post('quantity'),
			'batch_no'=>$this->input->post('batch_no'),
			'product_unitprice'=> $this->input->post('product_unitprice'),
			'product_unitprice_insurance'=> $this->input->post('product_unitprice_insurance'),
			//'store_id'=>$this->input->post('store_id'),
			'created'=>date('Y-m-d H:i:s'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'product_packsize'=>$this->input->post('product_pack_size'),
			'unit_of_measure'=>$this->input->post('unit_of_measure'),
			'reorder_level'=>$this->input->post('reorder_level'),
			'brand_id'=>$this->input->post('brand_id'),
			'class_id'=>$this->input->post('class_id'),
			'generic_id'=>$this->input->post('generic_id'),
			'drug_type_id'=>$this->input->post('drug_type_id'),
			'vatable'=>$this->input->post('vatable'),
			'is_synced'=>1,
			'stock_take'=>1
		);

		$this->db->where('product_id', $product_id);
		if($this->db->update('product', $array))
		{
			//edit service charge


			$product_store_quantity = array(
							'owning_store_id' => 5,
							'store_quantity' => $this->input->post('quantity')
						);
			$this->db->where('product_id = '.$product_id.' AND owning_store_id = 5');
			$this->db->update('store_product', $product_store_quantity);

			$category_id = $this->input->post('category_id');

			if($category_id == 2)
			{
				$service_id = 4;
			}
			else if($category_id == 3)
			{
				$service_id = 9;
			}
			else if($category_id == 4)
			{
				$service_id = 8;
			}
			else if($category_id == 5)
			{
				$service_id = 6;
			}
			else
			{
				$service_id = 5;
			}

			if($category_id == 2)
			{

				$product_unitprice = $this->input->post('product_unitprice');
				$service_charge_insert = array(
												// "service_charge_amount" => $product_unitprice,
												"service_id" => $service_id,
												"service_charge_name" => $product_name,
												"visit_type_id" => 1,
												'service_charge_status' => 1,
												'vatable'=>$this->input->post('vatable'),
												'service_charge_amount'=>$this->input->post('product_unitprice')
												);
				$service_charge_check = array(
												"visit_type_id" => 1,
												"product_id" => $product_id,
												);
				$this->db->where($service_charge_check);
				$query = $this->db->get('service_charge');

				// var_dump($query->num_rows()); die();
				if($query->num_rows() == 0)
				{
					$service_charge_insert['created'] = date('Y-m-d H:i:s');
					$service_charge_insert['product_id'] = $product_id;
					$service_charge_insert['created_by'] = $this->session->userdata('personnel_id');
					$service_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
					$service_charge_insert['vatable'] = $this->input->post('vatable');

					$this->db->insert('service_charge', $service_charge_insert);
				}
				else
				{
					$this->db->where(array('product_id' => $product_id));
					$this->db->update('service_charge', $service_charge_insert);
				}

			}

			
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function get_product_details($product_id)
	{
		//retrieve all users
		$this->db->from('product, category');
		$this->db->select('product.*, category.category_name');
		$this->db->where('product.category_id = category.category_id AND product.product_id = '.$product_id);
		$query = $this->db->get();

		return $query->result();
	}
	public function get_inventory_product_details($product_id)
	{
		$this->db->from('product, category');
		$this->db->select('product.*, category.category_name');
		$this->db->where('product.category_id = category.category_id AND product.product_id = '.$product_id);
		$query = $this->db->get();

		return $query;
	}
	public function get_product_purchases($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);
		$this->db->select('product_purchase.*');
		$this->db->where($where);
		$this->db->order_by($order,'DESC');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}

	public function get_store_inventory_quantity($sub_store_id,$product_id)
	{
		$this->db->where('owning_store_id = '.$sub_store_id.' AND product_id ='.$product_id);
		$this->db->select('store_quantity AS quantity');
		$query = $this->db->get('store_product');

		if($query->num_rows() > 0)
		{
			$product_store = $query->result();
			foreach ($product_store as $key) {
				# code...
				$quantity = $key->quantity;
			}

			return $quantity;
		}
		else
		{
			return 0;
		}

	}


	public function get_parent_store_inventory_quantity($parent_store_id,$product_id)
	{
		$this->db->where('owning_store_id = '.$parent_store_id.' AND product_id ='.$product_id);
		$this->db->select('store_quantity AS quantity');
		$query = $this->db->get('store_product');
		$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		$procurred = $this->inventory_management_model->item_proccured($inventory_start_date, $product_id,$parent_store_id);
		if($query->num_rows() > 0)
		{
			$product_store = $query->result();
			foreach ($product_store as $key) {
				# code...
				$quantity = $key->quantity;
			}

			return $quantity+$procurred;
		}
		else
		{
			return $procurred;
		}

	}
	public function check_if_can_access($product_id, $store_id)
	{
		$this->db->select('*');
		$this->db->where('personnel_store.store_id  = '.$store_id.' AND personnel_store.personnel_id = '.$this->session->userdata('personnel_id').'');
		$this->db->order_by('personnel_store.store_id','DESC');
		$this->db->limit(1);
		$query = $this->db->get('personnel_store');

		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	}
	public function get_assigned_stores()
	{
		$personnel_id = $this->session->userdata('personnel_id');
		if($personnel_id == 0)
		{
			$where = 'personnel_store.store_id  = store.store_id ';
		}
		else
		{

		}
		$where = 'personnel_store.store_id  = store.store_id AND personnel_store.personnel_id = '.$this->session->userdata('personnel_id');
		//echo $where;
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('personnel_store.store_id','DESC');
		// $this->db->limit(1);
		$query = $this->db->get('personnel_store,store');

		return $query;
	}
	public function check_store_child($store_id)
	{
		$this->db->select('*');
		$this->db->where('store.store_id  = '.$store_id.' AND store.store_parent > 0 ');
		$this->db->order_by('store.store_id','DESC');
		$this->db->limit(1);
		$query = $this->db->get('store');

		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	}
	public function get_orders_on_days_requests($date)
	{
		$this->db->select('*');
		$this->db->where('product_deductions.search_date = "'.$date.'" AND product_deductions.product_deduction_rejected = 0  AND product_deductions.product_id = product.product_id AND product_deductions.store_id  = store.store_id ');
		$this->db->order_by('product_deductions.product_deductions_id','DESC');
		// $this->db->limit(1);
		// $this->db->group_by('product_deductions.search_date');
		$query = $this->db->get('product_deductions,product,store');

		return $query;

	}

	public function get_all_requests_made($table, $where, $per_page, $page, $order, $store_parent = 1)
	{
		$this->db->from($table);
		if($store_parent == 0)
		{
			$this->db->select('product_deductions.*, store.*, orders.*, product.product_name, product_purchase.purchase_quantity, product_purchase.purchase_pack_size, product_purchase.expiry_date, product_purchase.purchase_id');
			$this->db->join('product_purchase','product_purchase.product_deductions_id = product_deductions.product_deductions_id','left');
		}
		$this->db->where($where);
		$this->db->order_by($order,'asc');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}
	public function get_parent_products($table, $where, $per_page, $page, $order)
	{
		$this->db->from($table);
		$this->db->select('*');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$this->db->where($where);
		$this->db->order_by($order,'asc');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}
	public function get_parent_store($store_id)
	{
		$this->db->select('store_parent');
		$this->db->where('store.store_id ='.$store_id);
		// $this->db->limit(1);
		$query = $this->db->get('store');
		$parent_store = 0;
		foreach ($query->result() as $key) {
			# code...
			$parent_store = $key->store_parent;
		}
		return $parent_store;
	}
	public function get_store_request($store_id = NULL, $order_id)
	{
		$this->db->select('*');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$this->db->where('product.product_id = product_deductions.product_id AND product_deductions.product_deduction_rejected = 0 AND product_deductions.order_id ='.$order_id);
		$query = $this->db->get('product_deductions,product');
		return $query;
	}
	public function get_test_costing($lab_test_id)
	{
		$this->db->select('*');
		$this->db->where('product.product_id = test_costing.product_id AND test_costing.lab_test_id = '.$lab_test_id);
		$query = $this->db->get('test_costing, product');
		return $query;
	}

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
				$number = "ORD00000001";
			}
			if($number == 1)
			{
				$number = "ORD00000001";
			}

		}
		else{//start generating receipt numbers
			// $number = 1;
			$number = "ORD00000001";
		}
		return $number;


		// //select product code
		// $preffix = "FTORD";
		// //select product code
		// $this->db->from('orders');
		// $this->db->where("order_number LIKE '%".$preffix."%'");

		// $query = $this->db->get();
		// $max = 0;
		// if($query->num_rows() > 0)
		// {
		// 	foreach($query->result() as $result)
		// 	{
		// 		$number =  $result->order_number;
		// 		$real_number = str_replace($preffix, "", $number) * 1;

		// 		if($real_number > $max)
		// 		{
		// 			$max = $real_number;
		// 		}
		// 	}

		// 	$max = $max+1;//go to the next number

		// 	$number = $preffix.sprintf('%04d', $max);
		// }
		// else{//start generating receipt numbers
		// 	$number = $preffix.sprintf('%04d', 1);
		// }
		// return $number;
	}

	public function create_order()
	{
		$store_id = $this->input->post('store_id');
		//check if store is parent store
		$order_type = $this->is_store_parent($store_id);

		$array = array(
			'orders_date'=>$this->input->post('orders_date'),
			'store_id'=>$store_id,
			'order_number'=>$this->create_order_number(),
			'created'=>date('Y-m-d H:i:s'),
			'supplier_id'=>$this->input->post('supplier_id'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id'),
			'order_type_id'=>$order_type,
			'is_store'=>1
		);
		if($this->db->insert('orders', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function get_product_orders($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);
		$this->db->select('orders.*, store.store_name, store.store_parent, personnel.personnel_fname, personnel.personnel_onames');
		$this->db->join('personnel', 'orders.created_by = personnel.personnel_id', 'left');
		$this->db->where($where);
		$this->db->order_by($order,'DESC');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}

	public function get_store_products($store_id)
	{
		$this->db->where('store_id = '.$sub_store_id.' AND product_id ='.$product_id);
		$this->db->select('quantity');
		$query = $this->db->get('store_product');

		if($query->num_rows() > 0)
		{
			$product_store = $query->result();
			foreach ($product_store as $key) {
				# code...
				$quantity = $key->quantity;
			}

			return $quantity;
		}
		else
		{
			return 0;
		}

	}
	public function get_all_orders()
	{
		$this->db->where('orders.store_id = store.store_id AND personnel_store.store_id = store.store_id AND personnel_store.personnel_id = '.$this->session->userdata('personnel_id'));
		$this->db->select('orders.*, store.store_name, store.store_parent');
		$query = $this->db->get('orders, store, personnel_store');

		return $query;
	}
	public function get_store_requests($store_id)
	{
		$this->db->where('orders.store_id = '.$store_id.' AND orders.order_type_id = 1 AND orders.store_id = store.store_id');
		$this->db->select('orders.*, store.store_name, store.store_parent, personnel.*');
		$this->db->join('personnel', 'orders.created_by = personnel.personnel_id', 'left');
		$this->db->order_by('orders_date', 'DESC');
		$query = $this->db->get('orders, store');

		return $query;
	}
	public function get_all_requests($store_id)
	{
		$this->db->where('orders.store_id IN (SELECT store_id FROM store WHERE store_parent = '.$store_id.') AND orders.order_type_id = 1 AND orders.store_id = store.store_id');
		$this->db->select('orders.*, store.store_name, store.store_parent, personnel.*');
		$this->db->join('personnel', 'orders.created_by = personnel.personnel_id', 'left');
		$this->db->order_by('orders_date', 'DESC');
		$query = $this->db->get('orders, store');

		return $query;
	}
	public function is_store_parent($store_id)
	{
		$order_type = 0;

		$this->db->select('store_parent');
		$this->db->where('store_id = '.$store_id);
		$query = $this->db->get('store');
		$parent_row =$query->row();

		$store_parent = $parent_row->store_parent;
		if($store_parent == 0)
		{
			$order_type = 2;
		}
		else
		{
			$order_type = 1;
		}
		return $order_type;
	}
	public function get_order_details($order_id)
	{
		$this->db->select('*');
		$this->db->where('product_deductions.store_id = store.store_id AND product_deductions.product_deduction_rejected = 0 AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = '.$order_id);
		$this->db->join('category', 'category.category_id = product.category_id', 'left');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$query = $this->db->get('product_deductions, store, product, orders');

		return $query;
	}
	public function get_total_products($product_id)
	{
		$instore_qty = 0;
		$total_product_purchased = 0;
		//get product qty
		$this->db->select('quantity');
		$this->db->where('product_id = '.$product_id);
		$query = $this->db->get('product');

		$quantity_row = $query->row();
		$quantity = $quantity_row->quantity;

		//get all purchases for that prdt
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id);
		$product_purchase_query = $this->db->get('product_purchase');
		if($product_purchase_query->num_rows() >0)
		{
			foreach($product_purchase_query->result() as $purchase_query)
			{
				$product_purchase = 0;
				$purchase_quantity = $purchase_query->purchase_quantity;
				$purchase_pack_size = $purchase_query->purchase_pack_size;
				$products_purchased = $purchase_quantity * $purchase_pack_size;
				$total_product_purchased+=$products_purchased;
			}
		}

		$instore_qty = $quantity + $total_product_purchased;
		return $instore_qty;
	}
	public function get_approval_id($personnel_id)
	{
		$approval_level_id = 0;

		$this->db->from('personnel_approval');
		$this->db->select('approval_status_id');
		$this->db->where('personnel_id = '.$personnel_id);
		$query = $this->db->get();

		$approval_row = $query->row();
		$approval_level_id =0;
		if($query->num_rows() > 0)
		{

		$approval_level_id = $approval_row->approval_status_id;
		}

		return $approval_level_id;
	}
	public function get_all_parent_store_orders()
	{
		$this->db->where('orders.order_type_id = 2 AND orders.store_id = store.store_id');
		$this->db->select('orders.*, store.store_name, store.store_parent, personnel.*');
		$this->db->join('personnel', 'orders.created_by = personnel.personnel_id', 'left');
		$query = $this->db->get('orders, store');

		return $query;
	}
	public function get_all_lpos($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);
		$this->db->select('lpo.*, orders.order_number, personnel.*, nav_supplier.*');
		$this->db->join('nav_supplier', 'lpo.nav_supplier_id = nav_supplier.nav_supplier_id', 'left');
		$this->db->join('personnel', 'lpo.created_by = personnel.personnel_id', 'left');
		$this->db->where($where);
		$this->db->order_by($order,'DESC');
		$query = $this->db->get('', $per_page, $page);

		return $query;
	}
	public function approve_order($order_id)
	{
		$approval = array(
			'order_approval_status'=>1);
		$this->db->where('order_id = '.$order_id);
		if($this->db->update('orders',$approval))
		{
			/*if($this->create_purchase_header_in_nav($order_id))
			{
				return TRUE;
			}

			else
			{
				return FALSE;
			}*/
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_order_number($order_id)
	{
		$order_number = '';
		$this->db->select('order_number');
		$this->db->where('order_id = '.$order_id);
		$query = $this->db->get('orders');

		$row = $query->row();
		$order_number = $row->order_number;

		return $order_number;
	}
	public function get_order_creator($order_id)
	{
		$personnel_name = '';
		$this->db->select('personnel.*');
		$this->db->where('orders.created_by = personnel.personnel_id AND orders.order_id = '.$order_id);
		$query = $this->db->get('personnel, orders');
		$row = $query->row();
		$personnel_fname = $row->personnel_fname;
		$personnel_onames = $row->personnel_onames;

		$personnel_name = $personnel_fname.' '.$personnel_onames;
		return $personnel_name;

	}
	public function check_store_parent($store_id)
	{
		$this->db->select('store_parent');
		$this->db->where('store_id = '.$store_id);
		$query = $this->db->get('store');

		$store_row = $query->row();
		$parent = $store_row->store_parent;

		if($parent == 0)
		{
			return  TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_all_suppliers()
	{
		$this->db->select('supplier.*');
		$this->db->where('supplier_status = 1');
		$query = $this->db->get('supplier');

		return $query;
	}
	public function get_nav_suppliers()
	{
		$this->db->select('nav_supplier.*');
		$query = $this->db->get('nav_supplier');

		return $query;
	}
	/*
	*	Import Template
	*
	*/
	function import_template()
	{
		$this->load->library('excel');

		$title = 'Order Items Import Template V1';
		$count=0;
		$row_count=0;

		$report[$row_count][$count] = 'Product Code';
		$count++;
		$report[$row_count][$count] = 'Product Name';
		$count++;
		$report[$row_count][$count] = 'Quantity';
		$count++;
		$report[$row_count][$count] = 'Price';

		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_order_items($upload_path, $store_id, $orders_id)
	{
		//load the file model
		$this->load->model('admin/file_model');
		/*
			-----------------------------------------------------------------------------------------
			Upload csv
			-----------------------------------------------------------------------------------------
		*/
		$response = $this->file_model->upload_csv($upload_path, 'import_csv');

		if($response['check'])
		{
			$file_name = $response['file_name'];

			$array = $this->file_model->get_array_from_csv($upload_path.'/'.$file_name);
			//var_dump($array); die();
			$response2 = $this->sort_csv_data($array, $store_id, $orders_id);

			if($this->file_model->delete_file($upload_path."\\".$file_name, $upload_path))
			{
			}

			return $response2;
		}

		else
		{
			$this->session->set_userdata('error_message', $response['error']);
			return FALSE;
		}
	}

	public function sort_csv_data($array, $store_id, $order_id)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		$count = 0;
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 4))
		{
			/*$items['modified_by'] = $this->session->userdata('personnel_id');
			$items['created_by'] = $this->session->userdata('personnel_id');*/
			$response = '
				<table class="table table-condensed table-striped table-hover">
					<tr>
						<th>#</th>
						<th>Product code</th>
						<th>Product name</th>
						<th>Quantity</th>
						<th>Price</th>
						<th>Comment</th>
					</tr>
			';

			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$items = array();
				$comment = '';
				$class = 'success';
				$count = 0;
				$product_code = $array[$r][$count];
				$count++;
				$product_name = $array[$r][$count];
				$count++;
				$items['quantity_requested'] = $array[$r][$count];
				$count++;
				$items['lpo_price'] = $array[$r][$count];
				$count++;

				if(!empty($product_code) && ($product_code != 'NEW'))
				{
					//check if product exists
					$this->db->where('product_code', $product_code);
					$query = $this->db->get('product');

					if($query->num_rows() > 0)
					{
						$row = $query->row();
						$items['product_id'] = $row->product_id;
						$items['store_id'] = $store_id;
						$items['order_id'] = $order_id;

						if($this->db->insert('product_deductions', $items))
						{
							$comment .= '<br/>Product successfully added to the order';
						}

						else
						{
							$class = 'danger';
							$comment .= '<br/>Internal error. Could not add product to the database. Please contact the site administrator. Product code '.$product_code;
						}
					}
					else
					{
						$class = 'danger';
						$comment .= '<br/>Product not found. Product code '.$product_code;
					}
				}

				else
				{
					$items_array['product_name'] = $product_name;
					$items_array['product_code'] = $this->create_product_code();

					if($this->db->insert('product', $items_array))
					{
						$product_id = $this->db->insert_id();
						$items['product_id'] = $product_id;
						$items['store_id'] = $store_id;
						$items['order_id'] = $order_id;

						if($this->db->insert('product_deductions', $items))
						{
							$comment .= '<br/>Product successfully added to the order';
						}

						else
						{
							$class = 'danger';
							$comment .= '<br/>Internal error. Could not add product to the database. Please contact the site administrator. Product code '.$product_code;
						}
					}
					else
					{
						$class = 'danger';
						$comment .= '<br/>Unable to add new product under product name '.$product_name;
					}
				}

				$response .= '
					<tr class="'.$class.'">
						<td>'.$r.'</td>
						<td>'.$product_code.'</td>
						<td>'.$product_name.'</td>
						<td>'.$items['quantity_requested'].'</td>
						<td>'.$items['lpo_price'].'</td>
						<td>'.$comment.'</td>
					</tr>
				';
			}

			$response .= '</table>';

			$return['response'] = $response;
			$return['check'] = TRUE;
		}

		//if no products exist
		else
		{
			$return['response'] = 'Product data not found';
			$return['check'] = FALSE;
		}

		return $return;
	}

	public function create_product_code()
	{
		//select product code
		$preffix = "ST";
		$this->db->from('product');
		$this->db->where("product_code LIKE '%".$preffix."%'");

		$query = $this->db->get();
		$max = 0;
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $result)
			{
				$number =  $result->product_code;
				$real_number = str_replace($preffix, "", $number) * 1;

				if($real_number > $max)
				{
					$max = $real_number;
				}
			}

			$max = $max+1;//go to the next number
			//echo $real_number.'<br/>';die();
			$number = $preffix.sprintf('%010d', $max);
		}
		else{//start generating receipt numbers
			$number = $preffix.sprintf('%010d', 1);
		}
		return $number;

	}

	public function update_nav_supplier_id($nav_supplier_id, $order_id)
	{
		$this->db->where('order_id', $order_id);
		if($this->db->update('orders', array('nav_supplier_id' => $nav_supplier_id)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function create_purchase_header_in_nav($order_id, $nav_supplier_id = NULL)
	{
		$this->db->select('orders.*');
		$this->db->where('orders.order_id = '.$order_id);
		$query = $this->db->get('orders');

		if($query->num_rows() > 0)
		{
			//Supplier details
			if($nav_supplier_id == NULL)
			{
				$nav_supplier_id = $this->input->post('nav_supplier_id');
				$this->update_nav_supplier_id($nav_supplier_id, $order_id);
			}
			$this->db->where('nav_supplier_id', $nav_supplier_id);
			$sup_query = $this->db->get('nav_supplier');

			if($sup_query->num_rows() > 0)
			{
				$row2 = $sup_query->row();
				$supplier_name = $row2->Search_Name;
				$supplier_no = $row2->No_;
				$supplier_address = $row2->Address;
				$supplier_payment_terms = $row2->Payment_Terms_Code;
				$supplier_posting_group = $row2->Vendor_Posting_Group;
				$supplier_invoice_disc_code = $row2->Invoice_Disc_Code;
				$supplier_vat = $row2->VAT_Registration_No_;
				$supplier_bus_posting = $row2->Gen_Bus_Posting_Group;
				$supplier_country = $row2->Country_Region_Code;
				$supplier_vat_posting = $row2->VAT_Bus_Posting_Group;
				//order details
				$row = $query->row();
				$nav_id = $row->nav_id;
				$orders_date = explode(" ", $row->orders_date);
				$order_date = $orders_date[0];
				$personnel_id = $this->session->userdata('personnel_id');
				$created_by = $this->auth_model->get_nav_session($personnel_id);
				$table_name = 'No_ Series Line';
				$field = 'Last No_ Used';
				$series_code = 'P-QUO';
				$preffix = 'PQ';

				if(empty($nav_id) || ($nav_id == NULL))
				{
					$entry_no = $this->web_service->get_next_series_number($table_name, $field, $series_code, $preffix);
				}

				else
				{
					$entry_no = $nav_id;
				}

				$columns =
				'INSERT INTO [ODC-NAV].[dbo].[Oserian Development Co_ Ltd$Purchase Header]
				([Document Type], [No_], [Buy-from Vendor No_], [Pay-to Vendor No_], [Pay-to Name], [Pay-to Name 2], [Pay-to Address], [Pay-to Address 2], [Pay-to City], [Pay-to Contact], [Your Reference], [Ship-to Code], [Ship-to Name], [Ship-to Name 2], [Ship-to Address], [Ship-to Address 2], [Ship-to City], [Ship-to Contact], [Order Date], [Posting Date], [Expected Receipt Date], [Posting Description], [Payment Terms Code], [Due Date], [Payment Discount %], [Pmt_ Discount Date], [Shipment Method Code], [Location Code], [Shortcut Dimension 1 Code], [Shortcut Dimension 2 Code], [Vendor Posting Group], [Currency Code], [Currency Factor], [Prices Including VAT], [Invoice Disc_ Code], [Language Code], [Purchaser Code], [Order Class], [No_ Printed], [On Hold], [Applies-to Doc_ Type], [Applies-to Doc_ No_], [Bal_ Account No_], [Receive], [Invoice], [Receiving No_], [Posting No_], [Last Receiving No_], [Last Posting No_], [Vendor Order No_], [Vendor Shipment No_], [Vendor Invoice No_], [Vendor Cr_ Memo No_], [VAT Registration No__ PIN No_], [Sell-to Customer No_], [Reason Code], [Gen_ Bus_ Posting Group], [Transaction Type], [Transport Method], [VAT Country_Region Code], [Buy-from Vendor Name], [Buy-from Vendor Name 2], [Buy-from Address], [Buy-from Address 2], [Buy-from City], [Buy-from Contact], [Pay-to Post Code], [Pay-to County], [Pay-to Country_Region Code], [Buy-from Post Code], [Buy-from County], [Buy-from Country_Region Code], [Ship-to Post Code], [Ship-to County], [Ship-to Country_Region Code], [Bal_ Account Type], [Order Address Code], [Entry Point], [Correction], [Document Date], [Area], [Transaction Specification], [Payment Method Code], [No_ Series], [Posting No_ Series], [Receiving No_ Series], [Tax Area Code], [Tax Liable], [VAT Bus_ Posting Group], [Applies-to ID], [VAT Base Discount %], [Status], [Invoice Discount Calculation], [Invoice Discount Value], [Send IC Document], [IC Status], [Buy-from IC Partner Code], [Pay-to IC Partner Code], [IC Direction], [Prepayment No_], [Last Prepayment No_], [Prepmt_ Cr_ Memo No_], [Last Prepmt_ Cr_ Memo No_], [Prepayment %], [Prepayment No_ Series], [Compress Prepayment], [Prepayment Due Date], [Prepmt_ Cr_ Memo No_ Series], [Prepmt_ Posting Description], [Prepmt_ Pmt_ Discount Date], [Prepmt_ Payment Terms Code], [Prepmt_ Payment Discount %], [Quote No_], [Doc_ No_ Occurrence], [Campaign No_], [Buy-from Contact No_], [Pay-to Contact No_], [Responsibility Center], [Posting from Whse_ Ref_], [Requested Receipt Date], [Promised Receipt Date], [Lead Time Calculation], [Inbound Whse_ Handling Time], [Vendor Authorization No_], [Return Shipment No_], [Return Shipment No_ Series], [Ship], [Last Return Shipment No_], [Assigned User ID], [Request-By No_], [Request-By Name], [Current approval Status], [Valid To Date], [Purchase Requisition No_], [Store Requisition No_], [External Doc No_], [Date Received], [Time Received], [BizTalk Purchase Quote], [BizTalk Purch_ Order Cnfmn_], [BizTalk Purchase Invoice], [BizTalk Purchase Receipt], [BizTalk Purchase Credit Memo], [Date Sent], [Time Sent], [BizTalk Request for Purch_ Qte], [BizTalk Purchase Order], [Vendor Quote No_], [BizTalk Document Sent], [Requester ID], [Requisition No_], [Training Schedule No_], [Training Plan No_], [Training LPO No_ Series], [Item Catergory], [Vendor Invoice Register No_], [Alternative Vendor Name], [Original Currency Factor], [Requestor ID], [Document Creator], [Document Creation Date], [Approver ID], [Approval Date], [Last Modification ID], [Last Modification Date], [Request Date], [Approver Name], [Request User Name], [Last Modification Name], [Training], [Crop], [Variety], [Color], [Style], [Pack Rate], [Vehicle No_], [Received By_], [Driver Name], [Trailer No_], [Temp Posting Description], [Archive Automatically])';

				$insert = "
				VALUES
				('1', '".$entry_no."', '".$supplier_no."', '".$supplier_no."', '".$supplier_name."', '', '".$supplier_address."', '', '', '', '', '', '".$supplier_name."', '', '".$supplier_address."', '', '', '', '".$order_date." 00:00:00.000', '".date('Y-m-d')." 00:00:00.000', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '".$supplier_payment_terms."', '1753-01-01 00:00:00.000', '0', '1753-01-01 00:00:00.000', '', '', '24', '', '".$supplier_posting_group."', '', '0', '0', '".$supplier_invoice_disc_code."', '', '', '', '0', '', '0', '', '', '0', '0', '', '', '', '', '', '', '', '', '".$supplier_vat."', '', '', '".$supplier_bus_posting."', '', '', '".$supplier_country."', '".$supplier_name."', '', '".$supplier_address."', '', '', '', '', '', '".$supplier_country."', '', '', '".$supplier_country."', '', '', '".$supplier_country."', '0', '', '', '0', '1753-01-01 00:00:00.000', '', '', 'EFT', 'P-RETORD', 'P-CR+', '', '', '0', '".$supplier_vat_posting."', '', '0', '1', '0', '0', '0', '0', '', '', '0', '', '', '', '', '0', '', '1', '1753-01-01 00:00:00.000', '', '', '1753-01-01 00:00:00.000', '".$supplier_payment_terms."', '0', '', '1', '', '', '', '', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '', '', '', '', 'P-SHPT', '1', '', '', '', '', '4', '1753-01-01 00:00:00.000', '', '', '', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '0', '0', '0', '0', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '0', '0', '', '0', '".$created_by."', '', '', '', '', '', '', '', '0', '".$created_by."', '".$created_by."', '1753-01-01 00:00:00.000', '".$created_by."', '1753-01-01 00:00:00.000', '".$created_by."', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '".$created_by."', '".$created_by."', '".$created_by."', '0', '', '', '', '', '0', '', '', '', '', '', '0');";

				$sql = $columns.$insert;
				//echo $nav_id; die();

				if(empty($nav_id) || ($nav_id == NULL))
				{
					$response = $this->web_service->bulk_insert($sql);

					$response = json_decode($response, TRUE);

					if($response['result'] == 'true')
					{

						if($this->update_nav_id($order_id, $entry_no))
						{
							$update_table_name = 'No_ Series Line';
							$response = $this->web_service->update_last_no_used($update_table_name, $entry_no, $series_code);
							$response = json_decode($response, TRUE);
							//var_dump($response); die();
							if($response['result'] == 'true')
							{
								$this->create_purchase_line_in_nav($order_id, $nav_supplier_id, $entry_no);
							}

							else
							{
								$this->session->set_userdata('error_message', $response['message']);
							}
						}

						else
						{
							$this->session->set_userdata('error_message', 'Unable to update order ID');
						}
					}

					else
					{
						$this->session->set_userdata('error_message', $response['message'][0][2]);
					}
				}

				else
				{
					$this->create_purchase_line_in_nav($order_id, $nav_supplier_id, $entry_no);
					//$this->session->set_userdata('error_message', 'This item has already been created in Navision');
				}

				//echo $response; die();
				//return $response;
			}
		}

	}

	public function create_purchase_line_in_nav($order_id, $nav_supplier_id, $entry_no)
	{
		$this->db->select('orders.*');
		$this->db->where('orders.order_id = '.$order_id);
		$query = $this->db->get('orders');

		if($query->num_rows() > 0)
		{
			//Supplier details
			$this->db->where('nav_supplier_id', $nav_supplier_id);
			$sup_query = $this->db->get('nav_supplier');

			if($sup_query->num_rows() > 0)
			{
				$row2 = $sup_query->row();
				$supplier_name = $row2->Search_Name;
				$supplier_no = $row2->No_;
				$supplier_address = $row2->Address;
				$supplier_payment_terms = $row2->Payment_Terms_Code;
				$supplier_posting_group = $row2->Vendor_Posting_Group;
				$supplier_invoice_disc_code = $row2->Invoice_Disc_Code;
				$supplier_vat = $row2->VAT_Registration_No_;
				$supplier_bus_posting = $row2->Gen_Bus_Posting_Group;
				$supplier_country = $row2->Country_Region_Code;
				$supplier_vat_posting = $row2->VAT_Bus_Posting_Group;
				//order details
				$row = $query->row();
				$orders_date = explode(" ", $row->orders_date);
				$order_date = $orders_date[0];
				$personnel_id = $this->session->userdata('personnel_id');
				$created_by = $this->auth_model->get_nav_session($personnel_id);

				$current_query = $this->inventory_management_model->get_order_details($order_id);
				$response = $this->web_service->delete_purchase_line($entry_no);
				//echo $current_query->num_rows(); die();
				if($current_query->num_rows() > 0)
				{
					$count = 0;
					$sql = '';
					$table_name = 'Purchase Line';
					$field = 'Line No_';
					$line_entry_no = $this->web_service->get_next_entry_number($table_name, $field);
					foreach ($current_query->result() as $key)
					{
						$product_id = $key->product_id;
						$product_name = $key->product_name;
						$product_code = $key->product_code;
						$product_buying_price = $key->product_buying_price;
						$quantity_requested = $key->quantity_requested;
						$product_deductions_id = $key->product_deductions_id;
						$posting_group = $key->posting_group;
						$category_code = $key->category_code;
						$group_code = $key->group_code;
						$unit_of_measure = $key->unit_of_measure;
						$purchase_amount = $product_buying_price * $quantity_requested;

						$columns =
						'INSERT INTO [ODC-NAV].[dbo].[Oserian Development Co_ Ltd$Purchase Line]
						([Document Type], [Document No_], [Line No_], [Buy-from Vendor No_], [Type], [No_], [Location Code], [Posting Group], [Expected Receipt Date], [Description], [Description 2], [Unit of Measure], [Quantity], [Outstanding Quantity], [Qty_ to Invoice], [Qty_ to Receive], [Direct Unit Cost], [Unit Cost (LCY)], [VAT %], [Line Discount %], [Line Discount Amount], [Amount], [Amount Including VAT], [Unit Price (LCY)], [Allow Invoice Disc_], [Gross Weight], [Net Weight], [Units per Parcel], [Unit Volume], [Appl_-to Item Entry], [Shortcut Dimension 1 Code], [Shortcut Dimension 2 Code], [Job No_], [Indirect Cost %], [Outstanding Amount], [Qty_ Rcd_ Not Invoiced], [Amt_ Rcd_ Not Invoiced], [Quantity Received], [Quantity Invoiced], [Receipt No_], [Receipt Line No_], [Profit %], [Pay-to Vendor No_], [Inv_ Discount Amount], [Vendor Item No_], [Sales Order No_], [Sales Order Line No_], [Drop Shipment], [Gen_ Bus_ Posting Group], [Gen_ Prod_ Posting Group], [VAT Calculation Type], [Transaction Type], [Transport Method], [Attached to Line No_], [Entry Point], [Area], [Transaction Specification], [Tax Area Code], [Tax Liable], [Tax Group Code], [Use Tax], [VAT Bus_ Posting Group], [VAT Prod_ Posting Group], [Currency Code], [Outstanding Amount (LCY)], [Amt_ Rcd_ Not Invoiced (LCY)], [Blanket Order No_], [Blanket Order Line No_], [VAT Base Amount], [Unit Cost], [System-Created Entry], [Line Amount], [VAT Difference], [Inv_ Disc_ Amount to Invoice], [VAT Identifier], [IC Partner Ref_ Type], [IC Partner Reference], [Prepayment %], [Prepmt_ Line Amount], [Prepmt_ Amt_ Inv_], [Prepmt_ Amt_ Incl_ VAT], [Prepayment Amount], [Prepmt_ VAT Base Amt_], [Prepayment VAT %], [Prepmt_ VAT Calc_ Type], [Prepayment VAT Identifier], [Prepayment Tax Area Code], [Prepayment Tax Liable], [Prepayment Tax Group Code], [Prepmt Amt to Deduct], [Prepmt Amt Deducted], [Prepayment Line], [Prepmt_ Amount Inv_ Incl_ VAT], [Prepmt_ Amount Inv_ (LCY)], [IC Partner Code], [Job Task No_], [Job Line Type], [Job Unit Price], [Job Total Price], [Job Line Amount], [Job Line Discount Amount], [Job Line Discount %], [Job Unit Price (LCY)], [Job Total Price (LCY)], [Job Line Amount (LCY)], [Job Line Disc_ Amount (LCY)], [Job Currency Factor], [Job Currency Code], [Prod_ Order No_], [Variant Code], [Bin Code], [Qty_ per Unit of Measure], [Unit of Measure Code], [Quantity (Base)], [Outstanding Qty_ (Base)], [Qty_ to Invoice (Base)], [Qty_ to Receive (Base)], [Qty_ Rcd_ Not Invoiced (Base)], [Qty_ Received (Base)], [Qty_ Invoiced (Base)], [FA Posting Date], [FA Posting Type], [Depreciation Book Code], [Salvage Value], [Depr_ until FA Posting Date], [Depr_ Acquisition Cost], [Maintenance Code], [Insurance No_], [Budgeted FA No_], [Duplicate in Depreciation Book], [Use Duplication List], [Responsibility Center], [Cross-Reference No_], [Unit of Measure (Cross Ref_)], [Cross-Reference Type], [Cross-Reference Type No_], [Item Category Code], [Nonstock], [Purchasing Code], [Product Group Code], [Special Order], [Special Order Sales No_], [Special Order Sales Line No_], [Completely Received], [Requested Receipt Date], [Promised Receipt Date], [Lead Time Calculation], [Inbound Whse_ Handling Time], [Planned Receipt Date], [Order Date], [Allow Item Charge Assignment], [Return Qty_ to Ship], [Return Qty_ to Ship (Base)], [Return Qty_ Shipped Not Invd_], [Ret_ Qty_ Shpd Not Invd_(Base)], [Return Shpd_ Not Invd_], [Return Shpd_ Not Invd_ (LCY)], [Return Qty_ Shipped], [Return Qty_ Shipped (Base)], [Return Shipment No_], [Return Shipment Line No_], [Return Reason Code], [Part Number], [BalanceQty], [WHT Code], [Routing No_], [Operation No_], [Work Center No_], [Finished], [Prod_ Order Line No_], [Overhead Rate], [MPS Order], [Planning Flexibility], [Safety Lead Time], [Routing Reference No_], [Prepayment VAT Difference], [Prepmt VAT Diff_ to Deduct], [Prepmt VAT Diff_ Deducted], [Include in Purch_ Order], [Item Usage Type], [Crop], [Variety], [Color], [Style], [Pack Rate], [Sell To Customer No_])';

						$insert = "
						VALUES
						('0', '".$entry_no."', '".$line_entry_no."', '".$supplier_no."', '2', '".$product_code."', '4', '".$posting_group."', '".$order_date."', '".$product_name."', '', '".$unit_of_measure."', '".$quantity_requested."', '0', '0', '0', '".$product_buying_price."', '".$product_buying_price."', '0', '0', '0', '".$purchase_amount."', '".$purchase_amount."', '".$purchase_amount."', '1', '0', '0', '0', '0', '0', '24', '', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '".$supplier_no."', '0', '', '', '0', '0', '".$supplier_bus_posting."', '".$posting_group."', '0', '', '', '0', '', '', '', '', '0', '', '0', 'VATABLE', 'ZERORATED', '', '0', '0', '', '0', '0', '".$product_buying_price."', '0', '".$purchase_amount."', '0', '0', 'ZERO RATED', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '', '1', '".$unit_of_measure."', '".$quantity_requested."', '0', '0', '0', '0', '0', '0', '1753-01-01 00:00:00.000', '0', '', '0', '0', '0', '', '', '', '', '0', '', '', '', '0', '', '".$category_code."', '0', '', '".$group_code."', '0', '', '0', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '', '', '1753-01-01 00:00:00.000', '".$order_date."', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '', '', '0', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2', '', '', '', '', '0', '');";
						$sql .= $columns.$insert;
						$line_entry_no = $line_entry_no + 1;
						//echo $sql; die();

					}
					//echo $sql."<br/>"; die();
					$response = $this->web_service->bulk_insert($sql);
					$response = json_decode($response, TRUE);
					if($response['result'] == 'true')
					{
						return TRUE;
					}

					else
					{
						$this->session->set_userdata('error_message', $response['message'][0][2]);
						return FALSE;
					}
				}
			}
		}
	}

	public function create_request_header_in_nav($order_id, $nav_supplier_id = NULL)
	{
		$this->db->select('orders.*');
		$this->db->where('orders.order_id = '.$order_id);
		$query = $this->db->get('orders');

		if($query->num_rows() > 0)
		{
			//Supplier details
			if($nav_supplier_id == NULL)
			{
				$nav_supplier_id = $this->input->post('nav_supplier_id');
			}
			$this->db->where('nav_supplier_id', $nav_supplier_id);
			$sup_query = $this->db->get('nav_supplier');
			$today = date('Y-m-d')." 00:00:00.000";

			if($sup_query->num_rows() > 0)
			{
				$row2 = $sup_query->row();
				$supplier_name = $row2->Search_Name;
				$supplier_no = $row2->No_;
				$supplier_address = $row2->Address;
				$supplier_payment_terms = $row2->Payment_Terms_Code;
				$supplier_posting_group = $row2->Vendor_Posting_Group;
				$supplier_invoice_disc_code = $row2->Invoice_Disc_Code;
				$supplier_vat = $row2->VAT_Registration_No_;
				$supplier_bus_posting = $row2->Gen_Bus_Posting_Group;
				$supplier_country = $row2->Country_Region_Code;
				$supplier_vat_posting = $row2->VAT_Bus_Posting_Group;
				//order details
				$row = $query->row();
				$orders_date = explode(" ", $row->orders_date);
				$order_date = $orders_date[0];
				$personnel_id = $this->session->userdata('personnel_id');
				$personnel_number = $this->session->userdata('personnel_number');
				$created_by = $this->auth_model->get_nav_session($personnel_id);

				$nav_requisition_id = $row->nav_requisition_id;
				$table_name = 'No_ Series Line';
				$field = 'Last No_ Used';
				$series_code = 'REQ-STORE';
				$preffix = 'STRQ';

				if(empty($nav_requisition_id) || ($nav_requisition_id == NULL))
				{
					$entry_no = $this->web_service->get_next_series_number($table_name, $field, $series_code, $preffix);
				}

				else
				{
					$entry_no = $nav_requisition_id;
				}
				//var_dump($entry_no); die();
				$columns =
				'INSERT INTO [ODC-NAV].[dbo].[Oserian Development Co_ Ltd$NFL Requisition Header]
				([Document Type], [No_], [Buy-from Vendor No_], [Pay-to Vendor No_], [Pay-to Name], [Pay-to Name 2], [Pay-to Address], [Pay-to Address 2], [Pay-to City], [Pay-to Contact], [Your Reference], [Ship-to Code], [Ship-to Name], [Ship-to Name 2], [Ship-to Address], [Ship-to Address 2], [Ship-to City], [Ship-to Contact], [Order Date], [Posting Date], [Expected Receipt Date], [Posting Description], [Payment Terms Code], [Due Date], [Payment Discount %], [Pmt_ Discount Date], [Shipment Method Code], [Location Code], [Shortcut Dimension 1 Code], [Shortcut Dimension 2 Code], [Vendor Posting Group], [Currency Code], [Currency Factor], [Prices Including VAT], [Invoice Disc_ Code], [Language Code], [Purchaser Code], [Order Class], [No_ Printed], [On Hold], [Applies-to Doc_ Type], [Applies-to Doc_ No_], [Bal_ Account No_], [Receive], [Invoice], [Receiving No_], [Posting No_], [Last Receiving No_], [Last Posting No_], [Vendor Order No_], [Vendor Shipment No_], [Vendor Invoice No_], [Vendor Cr_ Memo No_], [VAT Registration No_], [Sell-to Customer No_], [Reason Code], [Gen_ Bus_ Posting Group], [Transaction Type], [Transport Method], [VAT Country_Region Code], [Buy-from Vendor Name], [Buy-from Vendor Name 2], [Buy-from Address], [Buy-from Address 2], [Buy-from City], [Buy-from Contact], [Pay-to Post Code], [Pay-to County], [Pay-to Country_Region Code], [Buy-from Post Code], [Buy-from County], [Buy-from Country_Region Code], [Ship-to Post Code], [Ship-to County], [Ship-to Country_Region Code], [Bal_ Account Type], [Order Address Code], [Entry Point], [Correction], [Document Date], [Area], [Transaction Specification], [Payment Method Code], [No_ Series], [Posting No_ Series], [Receiving No_ Series], [Tax Area Code], [Tax Liable], [VAT Bus_ Posting Group], [Applies-to ID], [VAT Base Discount %], [Status], [Invoice Discount Calculation], [Invoice Discount Value], [Send IC Document], [IC Status], [Buy-from IC Partner Code], [Pay-to IC Partner Code], [IC Direction], [Prepayment No_], [Last Prepayment No_], [Prepmt_ Cr_ Memo No_], [Last Prepmt_ Cr_ Memo No_], [Prepayment %], [Prepayment No_ Series], [Compress Prepayment], [Prepayment Due Date], [Prepmt_ Cr_ Memo No_ Series], [Prepmt_ Posting Description], [Prepmt_ Pmt_ Discount Date], [Prepmt_ Payment Terms Code], [Prepmt_ Payment Discount %], [Quote No_], [Doc_ No_ Occurrence], [Campaign No_], [Buy-from Contact No_], [Pay-to Contact No_], [Responsibility Center], [Posting from Whse_ Ref_], [Requested Receipt Date], [Promised Receipt Date], [Lead Time Calculation], [Inbound Whse_ Handling Time], [Vendor Authorization No_], [Return Shipment No_], [Return Shipment No_ Series], [Ship], [Last Return Shipment No_], [Assigned User ID], [Request-By No_], [Employee Name], [Purchase Requisition No_], [Store Requisition No_], [Requestor ID], [External Reference No_], [Current approval Status], [Approver ID], [Return Date], [Valid to Date], [Date Received], [Time Received], [BizTalk Purchase Quote], [BizTalk Purch_ Order Cnfmn_], [BizTalk Purchase Invoice], [BizTalk Purchase Receipt], [BizTalk Purchase Credit Memo], [Date Sent], [Time Sent], [BizTalk Request for Purch_ Qte], [BizTalk Purchase Order], [Vendor Quote No_], [BizTalk Document Sent], [Budget Name], [Item Category], [Emergency], [Marked Emergency By], [Archived Purchase Req_ No], [Request-By Name], [Linked to MA], [MA Store Requsition], [Delivery Note No_], [Last Modification ID], [Last Modification date], [Last Modification Name], [Approval Date], [Request Date], [Shortcut Dimension 1 Name], [Shortcut Dimension 2 Name], [Request User Name], [Approver Name], [Document Creation Date], [From Store Req Archive No_], [From Store Req Archive No_2], [Warranty Issue], [Material Application No], [Document Creator], [Issuer ID], [From Service Order], [Service Order No_], [Charge To No_], [Charge To Name], [Service Shipment No_], [MA Store Return], [Field], [Crop Division], [Crop Code], [Crop Variety], [Crop Name])';

				$insert = "
				VALUES
				('0', '".$entry_no."', '', '', '', '', '', '', '', '', '', '', '".$supplier_name."', '', 'Oserian', '', 'Naivasha', '', '".$order_date." 00:00:00.000', '".$today."', '1753-01-01 00:00:00.000', 'Medical Center Requisition', '', '1753-01-01 00:00:00.000', '0', '1753-01-01 00:00:00.000', '', '27', '24', '', '', '', '0', '0', '', '', '', '', '0', '', '0', '', '', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '', '0', '".$today."', '', '', '', '', 'REQ-STORE', '', '', '0', '', '', '0', '1', '0', '0', '0', '0', '', '', '0', '', '', '', '', '0', '', '1', '1753-01-01 00:00:00.000', '', '', '1753-01-01 00:00:00.000', '', '0', '', '0', '', '', '', '', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '', '', '', '', '', '0', '', '', '9994', 'Main Dispensary', '', '', '".$created_by."', '', '4', '".$created_by."', '1753-01-01 00:00:00.000', '".$today."', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '0', '0', '0', '0', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '0', '0', '', '0', '', '', '0', '', '', 'Main  Dispensary', '0', '0', '', '".$created_by."', '".$today."', '".strtolower($created_by)."', '".$today."', '".$today."', 'Health Center', '', '".strtolower($created_by)."', '".$created_by."', '".$today."', '', '', '0', '', '".$created_by."', '', '0', '', '9994', 'Main  Dispensary', '', '0', '', '', '', '', '');";

				$sql = $columns.$insert;
				//echo $sql; die();
				if(empty($nav_requisition_id) || ($nav_requisition_id == NULL))
				{
					$response = $this->web_service->bulk_insert($sql);

					$response = json_decode($response, TRUE);
					//var_dump($response['message'][0][2]); die();

					if($response['result'] == 'true')
					{

						if($this->update_nav_requisition_id($order_id, $entry_no))
						{
							$update_table_name = 'No_ Series Line';
							$response = $this->web_service->update_last_no_used($update_table_name, $entry_no, $series_code);
							$response = json_decode($response, TRUE);
							//var_dump($response); die();
							if($response['result'] == 'true')
							{
								$this->create_request_line_in_nav($order_id, $nav_supplier_id, $entry_no);
							}

							else
							{
								$this->session->set_userdata('error_message', $response['message']);
							}
						}

						else
						{
							$this->session->set_userdata('error_message', 'Unable to update order ID');
						}
					}

					else
					{
						$this->session->set_userdata('error_message', $response['message'][0][2]);
					}
				}

				else
				{
					$this->create_request_line_in_nav($order_id, $nav_supplier_id, $entry_no);
					//$this->session->set_userdata('error_message', 'This item has already been created in Navision');
				}

			}
		}

	}

	public function create_request_line_in_nav($order_id, $nav_supplier_id, $entry_no)
	{
		$this->db->select('orders.*');
		$this->db->where('orders.order_id = '.$order_id);
		$query = $this->db->get('orders');

		if($query->num_rows() > 0)
		{
			//Supplier details
			//$nav_supplier_id = $this->input->post('nav_supplier_id');
			$this->db->where('nav_supplier_id', $nav_supplier_id);
			$sup_query = $this->db->get('nav_supplier');
			$today = date('Y-m-d')." 00:00:00.000";

			if($sup_query->num_rows() > 0)
			{
				$row2 = $sup_query->row();
				$supplier_name = $row2->Search_Name;
				$supplier_no = $row2->No_;
				$supplier_address = $row2->Address;
				$supplier_payment_terms = $row2->Payment_Terms_Code;
				$supplier_posting_group = $row2->Vendor_Posting_Group;
				$supplier_invoice_disc_code = $row2->Invoice_Disc_Code;
				$supplier_vat = $row2->VAT_Registration_No_;
				$supplier_bus_posting = $row2->Gen_Bus_Posting_Group;
				$supplier_country = $row2->Country_Region_Code;
				$supplier_vat_posting = $row2->VAT_Bus_Posting_Group;
				//order details
				$row = $query->row();
				$orders_date = explode(" ", $row->orders_date);
				$order_date = $orders_date[0];
				$personnel_id = $this->session->userdata('personnel_id');
				$created_by = $this->auth_model->get_nav_session($personnel_id);

				//$current_query = $this->inventory_management_model->get_store_request(NULL, $order_id);
				$response = $this->web_service->delete_order_items($entry_no);
				$current_query = $this->inventory_management_model->get_order_details($order_id);
				if($current_query->num_rows() > 0)
				{
					//Delete order items
					//var_dump($response); die();
					$count = 0;
					$sql = '';
					$table_name = 'NFL Requisition Line';
					$field = 'Line No_';
					$line_entry_no = $this->web_service->get_next_entry_number($table_name, $field);
					foreach ($current_query->result() as $key)
					{
						if($count == 0)
						{
							$product_id = $key->product_id;
							$product_name = $key->product_name;
							$product_code = $key->product_code;
							$product_buying_price = $key->product_buying_price;
							$quantity_requested = $key->quantity_requested;
							$product_deductions_id = $key->product_deductions_id;
							$posting_group = $key->posting_group;
							$category_code = $key->category_code;
							$group_code = $key->group_code;
							$unit_of_measure = $key->unit_of_measure;
							$purchase_amount = $product_buying_price * $quantity_requested;

							$columns =
							'INSERT INTO [ODC-NAV].[dbo].[Oserian Development Co_ Ltd$NFL Requisition Line]
							([Document Type], [Document No_], [Line No_], [Buy-from Vendor No_], [Type], [No_], [Location Code], [Posting Group], [Expected Receipt Date], [Description], [Descriptioln 2], [Unit of Measure], [Quantity], [Outstanding Quantity], [Qty_ to Invoice], [Qty_ to Receive], [Direct Unit Cost], [Unit Cost (LCY)], [VAT %], [Line Discount %], [Line Discount Amount], [Amount], [Amount Including VAT], [Unit Price (LCY)], [Allow Invoice Disc_], [Gross Weight], [Net Weight], [Units per Parcel], [Unit Volume], [Appl_-to Item Entry], [Shortcut Dimension 1 Code], [Shortcut Dimension 2 Code], [Job No_], [Indirect Cost %], [Outstanding Amount], [Qty_ Rcd_ Not Invoiced], [Amt_ Rcd_ Not Invoiced], [Quantity Received], [Quantity Invoiced], [Receipt No_], [Receipt Line No_], [Profit %], [Pay-to Vendor No_], [Inv_ Discount Amount], [Vendor Item No_], [Sales Order No_], [Sales Order Line No_], [Drop Shipment], [Gen_ Bus_ Posting Group], [Gen_ Prod_ Posting Group], [VAT Calculation Type], [Transaction Type], [Transport Method], [Attached to Line No_], [Entry Point], [Area], [Transaction Specification], [Tax Area Code], [Tax Liable], [Tax Group Code], [Use Tax], [VAT Bus_ Posting Group], [VAT Prod_ Posting Group], [Currency Code], [Outstanding Amount (LCY)], [Amt_ Rcd_ Not Invoiced (LCY)], [Blanket Order No_], [Blanket Order Line No_], [VAT Base Amount], [Unit Cost], [System-Created Entry], [Line Amount], [VAT Difference], [Inv_ Disc_ Amount to Invoice], [VAT Identifier], [IC Partner Ref_ Type], [IC Partner Reference], [Prepayment %], [Prepmt_ Line Amount], [Prepmt_ Amt_ Inv_], [Prepmt_ Amt_ Incl_ VAT], [Prepayment Amount], [Prepmt_ VAT Base Amt_], [Prepayment VAT %], [Prepmt_ VAT Calc_ Type], [Prepayment VAT Identifier], [Prepayment Tax Area Code], [Prepayment Tax Liable], [Prepayment Tax Group Code], [Prepmt Amt to Deduct], [Prepmt Amt Deducted], [Prepayment Line], [Prepmt_ Amount Inv_ Incl_ VAT], [Prepmt_ Amount Inv_ (LCY)], [IC Partner Code], [Job Task No_], [Job Line Type], [Job Unit Price], [Job Total Price], [Job Line Amount], [Job Line Discount Amount], [Job Line Discount %], [Job Unit Price (LCY)], [Job Total Price (LCY)], [Job Line Amount (LCY)], [Job Line Disc_ Amount (LCY)], [Job Currency Factor], [Job Currency Code], [Prod_ Order No_], [Variant Code], [Bin Code], [Qty_ per Unit of Measure], [Unit of Measure Code], [Quantity (Base)], [Outstanding Qty_ (Base)], [Qty_ to Invoice (Base)], [Qty_ to Receive (Base)], [Qty_ Rcd_ Not Invoiced (Base)], [Qty_ Received (Base)], [Qty_ Invoiced (Base)], [FA Posting Date], [FA Posting Type], [Depreciation Book Code], [Salvage Value], [Depr_ until FA Posting Date], [Depr_ Acquisition Cost], [Maintenance Code], [Insurance No_], [Budgeted FA No_], [Duplicate in Depreciation Book], [Use Duplication List], [Responsibility Center], [Cross-Reference No_], [Unit of Measure (Cross Ref_)], [Cross-Reference Type], [Cross-Reference Type No_], [Item Category Code], [Nonstock], [Purchasing Code], [Product Group Code], [Special Order], [Special Order Sales No_], [Special Order Sales Line No_], [Completely Received], [Requested Receipt Date], [Promised Receipt Date], [Lead Time Calculation], [Inbound Whse_ Handling Time], [Planned Receipt Date], [Order Date], [Allow Item Charge Assignment], [Return Qty_ to Ship], [Return Qty_ to Ship (Base)], [Return Qty_ Shipped Not Invd_], [Ret_ Qty_ Shpd Not Invd_(Base)], [Return Shpd_ Not Invd_], [Return Shpd_ Not Invd_ (LCY)], [Return Qty_ Shipped], [Return Qty_ Shipped (Base)], [Return Shipment No_], [Return Shipment Line No_], [Return Reason Code], [Qty_ Requested], [Request-By No_], [Request-By Name], [G_L Expense A_c], [Include in Purch_ Order], [Inventory Charge A_c], [Total Cost], [Transfer to Item Jnl], [Make Purchase Req_], [Qty To Transfer to Item Jnl], [Qty To Make Purch_ Req_], [Transferred To Item Jnl], [Transferred To Purch_ Req_], [Currentbeingused], [Total Qty To Item Jnl], [Total Qty To Purch_ Req], [Qty Returned], [Archive No_], [Qty_ Not Returned], [Store Issue Line No], [FA No_], [Routing No_], [Operation No_], [Work Center No_], [Finished], [Prod_ Order Line No_], [Overhead Rate], [MPS Order], [Planning Flexibility], [Safety Lead Time], [Routing Reference No_], [Zone Code], [Reason Code], [Warranty Issue], [Qty to Transfer Order], [Transfer to Transfer Order], [Transferred to Transfer Order], [Total Qty to Transfer Order], [Remarks], [Weight], [Volume], [Diagnosis], [Issue Type], [Date of Manufacture], [Serial No], [Shortcut Dimension 1 Name], [Shortcut Dimension 2 Name], [Last Modification ID], [Last Modification Date], [Last Modification Name], [From Store Req Archive No_], [From Store Req Archive No_2], [Non Stock Item Code], [Vendor Name], [Job Line No_], [Line Amount Excl_ VAT], [Field], [Field Section], [Crop Division], [Crop], [Variety], [Service Order No_], [Supplier Item No_], [Order No_], [Order Line No_], [Customer No_], [MA Chem Fert_ Line No_])';

							$insert = "
							VALUES
							('0', '".$entry_no."', '".$line_entry_no."', '', '2', '".$product_code."', '4', '".$posting_group."', '".$today."', '".$product_name."', '', '".ucwords(strtolower($unit_of_measure))."', '".$quantity_requested."', '0', '".$quantity_requested."', '".$quantity_requested."', '".$product_buying_price."', '".$product_buying_price."', '0', '0', '0', '0', '0', '".$product_buying_price."', '1', '0', '0', '0', '0', '0', '24', '', '', '0', '0', '0', '0', '".$quantity_requested."', '".$quantity_requested."', '', '0', '0', '', '0', '', '', '0', '0', '', '".$posting_group."', '0', '', '', '0', '', '', '', '', '0', '', '0', '', 'ZERORATED', '', '0', '0', '', '0', '0', '".$product_buying_price."', '0', '0', '0', '0', 'ZERO RATED', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '', '1', '".$unit_of_measure."', '0', '0', '0', '0', '0', '0', '0', '1753-01-01 00:00:00.000', '0', '', '0', '0', '0', '', '', '', '', '0', '', '', '', '0', '', '".$category_code."', '0', '', '".$group_code."', '0', '', '0', '0', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '', '', '1753-01-01 00:00:00.000', '1753-01-01 00:00:00.000', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '', '".$quantity_requested."', '9994', 'Main Dispensary', '', '0', '70000195', '".$purchase_amount."', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '', '', '', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '1753-01-01 00:00:00.000', '', 'Health Center', '', '".$created_by."', '".$today."', '".strtolower($created_by)."', '', '', '', '', '0', '0', '', '', '', '', '', '', '', '', '0', '', '0');";
							$sql .= $columns.$insert;
							$line_entry_no = $line_entry_no + 1;
							//break;
						}
					}
					$count++;
				}
				//echo $sql."<br/>";die();
				$response = $this->web_service->bulk_insert($sql);
				$response = json_decode($response, TRUE);
				if($response['result'] == 'true')
				{
					$this->session->set_userdata('success_message', 'Nav updated successfully');
					return TRUE;
				}

				else
				{
					$this->session->set_userdata('error_message', $response['message'][0][2]);
					return FALSE;
				}
			}
		}

	}
	function get_container_types()
	{
		$table = "container_type";
		$where = "container_type_id >= 0 ";
		$items = "*";
		$order = "container_type_name";

		// $result = $this->database->select_entries_where($table, $where, $items, $order);
		$this->db->where($where);
		$result = $this->db->get($table);

		return $result;
	}
	public function get_all_child_stores($store_id)
	{
		$this->db->select('*');
		$this->db->where('store_parent = '.$store_id);
		$query = $this->db->get('store');

		return $query;
	}
	public function get_requests($child_store_id)
	{
		$this->db->select('*');
		$this->db->where('store_id = '.$child_store_id);
		$query = $this->db->get('orders');

		return $query;
	}
	public function get_child_sales($inventory_start_date, $orders_id, $product_id,$start_date,$end_date)
	{
		$total = 0;
		$where = 'product_deductions.store_id = store.store_id AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = '.$orders_id.' AND product.product_id = '.$product_id;

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
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('product_deductions, store, product, orders');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row2)
			{
				$quantity_received = $row2->quantity_received;
				$total = $total + $quantity_received;
			}
		}
		return $total;

	}

	public function update_nav_id($order_id, $entry_no)
	{
		$this->db->where('order_id', $order_id);
		if($this->db->update('orders', array('nav_id' => $entry_no)))
		{
			return TRUE;
		}

		else
		{
			return FALSE;
		}
	}

	public function update_nav_requisition_id($order_id, $entry_no)
	{
		$this->db->where('order_id', $order_id);
		if($this->db->update('orders', array('nav_requisition_id' => $entry_no)))
		{
			return TRUE;
		}

		else
		{
			return FALSE;
		}
	}

	public function child_item_purchases($inventory_start_date, $product_id, $store_id, $start_date = NULL, $end_date = NULL)
	{
		$total = 0;
		$where = 'product_deductions.product_deductions_date >= "'.$inventory_start_date.'" AND product_deductions.store_id = store.store_id AND product_deductions.product_id = product.product_id  AND product.product_id = '.$product_id.' AND product_deductions.store_id = '.$store_id;

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
		$this->db->select('*');
		$this->db->where($where);
		$query = $this->db->get('product_deductions, store, product');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row2)
			{
				$quantity_received = $row2->quantity_received;
				$total = $total + $quantity_received;
			}
		}
		return $total;
	}
	public function get_status($order_approval_status)
	{
		$status_name = '';
		$this->db->select('*');
		$this->db->where('order_status_id = \''.$order_approval_status."'");
		$query = $this->db->get('order_status');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row2)
			{
				$status_name = $row2->order_status_name;
			}
		}
		return $status_name;
	}

	public function get_lpo_status($lpo_status)
	{
		$status_name = '';
		$this->db->select('*');
		$this->db->where('lpo_status_id = \''.$lpo_status."'");
		$query = $this->db->get('lpo_status');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row2)
			{
				$status_name = $row2->lpo_status_name;
			}
		}
		return $status_name;
	}

	public function get_inventory_start_date()
	{
		$this->db->where('branch_id  = 2');
		$query = $this->db->get('branch');

		$inventory_start_date = '';
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$inventory_start_date = $row->inventory_start_date;
		}

		return $inventory_start_date;
	}
	public function get_store_opening_quantity($product_id,$store_id)
	{
		$this->db->where('product_id ='.$product_id.' AND owning_store_id = '.$store_id);
		$query = $this->db->get('store_product');

		$store_quantity = 0;
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$store_quantity = $row->store_quantity;
		}

		return $store_quantity;

	}

	public function update_initial_product_balance()
	{
		$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		$search_start_date = $search_end_date = $branch_code = $store_id = NULL;
		$query = $this->db->get('product');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$product_id = $res->product_id;
				$opening_balance = $res->quantity;

				$purchases = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
				$deductions = $this->inventory_management_model->item_deductions($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date);
				 //$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date);
				$sales = $this->inventory_management_model->get_pharmacy_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code);
				 $in_stock = ($opening_balance + $purchases) - $sales - $deductions;

				 $this->db->where('product_id', $product_id);
				 if($this->db->update('product', array('product_balance' => $in_stock)))
				 {
				 }
			}
		}
	}

	public function update_product_balance($product_id, $quantity, $type)
	{
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('product');

		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$opening_balance = $res->product_balance;

				if($type == 1)
				{
					$in_stock = $opening_balance + $quantity;
				}

				else
				{
					$in_stock = $opening_balance - $quantity;
				}

				$this->db->where('product_id', $product_id);
				if($this->db->update('product', array('product_balance' => $in_stock)))
				{
				}
			}
		}
	}

	public function end_visit_products($visit_id)
	{
		$items = $this->inventory_management_model->get_drug_units_sold_in_visit($visit_id);
		$type = 2;//subtract from inventory
		if(is_array($items))
		{
			$total_items = count($items);
			if($total_items > 0)
			{
				for($r = 0; $r < $total_items; $r++)
				{
					$quantity = $items[$r]['visit_charge_units'];
					$product_id = $items[$r]['product_id'];

					$response = $this->inventory_management_model->update_product_balance($product_id, $quantity, $type);
				}
			}
		}
	}

	public function create_lpo_number()
	{
		$preffix = "LPO-".date('Y')."-";
		$this->db->from('lpo');
		$this->db->where("lpo_number LIKE '%".$preffix."%'");

		$query = $this->db->get();
		$max = 0;
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $result)
			{
				$number =  $result->lpo_number;
				$real_number = str_replace($preffix, "", $number) * 1;

				if($real_number > $max)
				{
					$max = $real_number;
				}
			}

			$max = $max+1;//go to the next number

			$number = $preffix.sprintf('%04d', $max);
		}
		else{//start generating receipt numbers
			$number = $preffix.sprintf('%04d', 1);
		}
		return $number;
	}

	public function create_new_lpo($order_id)
	{
		$array = array(
			'lpo_date'=>$this->input->post('lpo_date'),
			'nav_supplier_id'=>$this->input->post('nav_supplier_id'),
			'lpo_status_id'=>1,
			'order_id'=>$order_id,
			'lpo_number'=>$this->create_lpo_number(),
			'created'=>date('Y-m-d H:i:s'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id')
		);
		if($this->db->insert('lpo', $array))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function selected_lpo_items($lpo_id = NULL, $order_id)
	{
		$this->db->select('product.*, category.category_name, unit.unit_name, lpo_item.*');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$this->db->join('category', 'product.category_id = category.category_id', 'left');
		$this->db->where('product.product_id = lpo_item.product_id AND lpo_item.lpo_id ='.$lpo_id);
		$this->db->order_by('product.product_name');
		$query = $this->db->get('lpo_item, product');
		return $query;
	}

	public function get_product_deduction_quantity($product_id, $order_id)
	{
    	$where = array(
			'product_id' => $product_id,
			'order_id'=>$order_id
		);
    	$this->db->where($where);
		$query = $this->db->get('product_deductions');

		$quantity = 0;
		$items = array();
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$items['quantity'] = $row->quantity_requested;
			$items['lpo_price'] = $row->lpo_price;
		}

		return $items;
	}
	public function approve_lpo($lpo_id)
	{
		$approval = array(
			'lpo_status_id'=> 2
		);
		$this->db->where('lpo_id = '.$lpo_id);
		if($this->db->update('lpo',$approval))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function get_lpo_details($lpo_id)
	{
		$this->db->select('lpo.*, personnel.*, nav_supplier.*');
		$this->db->join('nav_supplier', 'lpo.nav_supplier_id = nav_supplier.nav_supplier_id', 'left');
		$this->db->join('personnel', 'lpo.created_by = personnel.personnel_id', 'left');
		$this->db->where('lpo_id', $lpo_id);
		return $this->db->get('lpo');
	}
	public function consumables_sold($inventory_start_date, $store_product_id,$search_start_date,$search_end_date)
	{
		$total=  0;
		$table = "visit, visit_consumable";
		$where = "visit_consumable.store_product_id = ".$store_product_id." AND visit_consumable.visit_id = visit.visit_id";
		$order = "visit.visit_id";

		if(($search_start_date != NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND visit.visit_date >= "'.$search_start_date.'" AND visit.visit_date <= "'.$search_end_date.'"';
		}

		else if(($search_start_date == NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND visit.visit_date = "'.$search_end_date.'"';
		}

		else if(($search_start_date != NULL) && ($search_end_date == NULL))
		{
			 $where .= ' AND visit.visit_date = "'.$search_end_date.'"';
		}
		$this->db->select('SUM(visit_charge_units) AS total');
		$this->db->where($where);
		$query = $this->db->get($table);

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$total = $row->total;
		}

		return $total;
	}
	public function store_product_recieved($inventory_start_date, $product_id,$store_id,$search_start_date,$search_end_date)
	{
		$where = 'product_id = '.$product_id.' AND store_id = '.$store_id.' AND product_deductions_status = 2 ';
		$table = 'product_deductions';

		if(($search_start_date != NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND product_deductions.date_requested >= "'.$search_start_date.'" AND product_deductions.date_requested <= "'.$search_end_date.'"';
		}

		else if(($search_start_date == NULL) && ($search_end_date != NULL))
		{
			 $where .= ' AND product_deductions.date_requested = "'.$search_end_date.'"';
		}

		else if(($search_start_date != NULL) && ($search_end_date == NULL))
		{
			 $where .= ' AND product_deductions.date_requested = "'.$search_end_date.'"';
		}

		$this->db->select('SUM(quantity_received) AS total');
		$this->db->where($where);
		$query = $this->db->get($table);

		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$total = $row->total;
		}

		return $total;
	}

	function get_child_store_stock()
	{

		// $this->db->query('SELECT (SELECT SUM(visit_charge.visit_charge_units)
		// 							FROM (`visit_charge`, `service_charge`)
		// 							WHERE visit_charge.service_charge_id = service_charge.service_charge_id
		// 							AND visit_charge.charged = 1 AND service_charge.product_id = 2300) AS total_sales,
		// 						( )'
		// 				)
	}
	function child_store_stock_old($inventory_start_date,$product_id,$store_id)
	{
		// $sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id);

		// $pending_procurement = $this->inventory_management_model->item_deductions_requested($inventory_start_date, $product_id,$store_id);
		// $purchases_units = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id,$store_id);
		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);

		// $deductions = $this->inventory_management_model->parent_item_deductions($inventory_start_date, $product_id,$store_id);
		// // var_dump($sales); die();

		// $total_stock = ($opening_quantity + $deductions + $pending_procurement + $purchases_units ) - $sales;
			$purchases_units = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id,$store_id);
    	$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,$store_id);
    	$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id);
    	$store_requests = $purchases = $this->inventory_management_model->product_added($inventory_start_date, $product_id,$store_id);

    	$purchases = $store_requests + $s11 + $purchases_units;
    	// var_dump($purchases); die();
    	$in_stock = ($opening_quantity + $purchases) - $sales;
    	// $in_stock = $pending_procurement;

		return $opening_quantity;
	}

	function child_store_stock($inventory_start_date,$product_id,$store_id)
	{
				$quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);
				$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,$store_id);
				$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date=null,$search_end_date=null, $branch_code=NULL);

				$store_requests = $purchases = $this->inventory_management_model->product_added($inventory_start_date, $product_id,$store_id);

				$purchases = $store_requests + $s11;
				$subtractions = $this->inventory_management_model->product_store_additions($product_id,$store_id);
				$unit_quantity = 0;
				if($store_id == 14)
				{
					// $get laboratory deductions
					$unit_quantity = $this->inventory_management_model->get_tests_units($product_id);
					// var_dump($unit_quantity); die();

					// $sales
				}
				// var_dump($unit_quantity); die();
				$in_stock = ($quantity + $purchases) - ($sales + $unit_quantity + $subtractions);
			// $opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);
    	// $s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,$store_id);
    	// $sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id);
    	// $store_requests = $purchases = $this->inventory_management_model->product_added($inventory_start_date, $product_id,$store_id);
			//
    	// $purchases = $store_requests + $s11;
    	// $in_stock = ($opening_quantity + $purchases) - $sales;

		return $in_stock;
	}
	function parent_store_stock($inventory_start_date,$product_id,$store_id)
	{
		$store_parent = $this->get_parent_store($store_id);

		if($store_parent == 0)
		{
			$store_parent = $store_id;
		}
		//  get all child stores


		$pending_procurement = $this->inventory_management_model->item_deductions_requested($inventory_start_date, $product_id,$store_id);
		$credit_notes = $this->inventory_management_model->product_credit_notes($inventory_start_date, $product_id,$store_id);
		$deductions = $this->inventory_management_model->parent_item_deductions($inventory_start_date, $product_id,$store_id);
		$procurred = $this->inventory_management_model->item_proccured($inventory_start_date, $product_id,$store_id);
		$other_deductions = $this->inventory_management_model->parent_transfer_item_deductions($inventory_start_date, $product_id,$store_id);
		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_parent);

		// var_dump($deductions); die();
		$total_stock = ($opening_quantity + $procurred) - ($deductions + $pending_procurement+$other_deductions+$credit_notes);

		return $total_stock;
	}

	function total_child_store_stock($inventory_start_date,$product_id,$store_id)
	{
		$sales = $this->inventory_management_model->view_get_drug_units_sold($inventory_start_date, $product_id);

		$pending_procurement = $this->inventory_management_model->v_not_awarded_requests($inventory_start_date, $product_id,$store_id);

		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);

		$deductions = $this->inventory_management_model->v_parent_items_deductions($inventory_start_date, $product_id,$store_id);
		// var_dump($sales); die();

		$total_stock = ($opening_quantity + $deductions + $pending_procurement ) - $sales;

		return $total_stock;
	}

	function total_parent_store_stock($inventory_start_date,$product_id,$store_id)
	{
		// $store_parent = $this->get_parent_store($store_id);

		// if($store_parent == 0)
		// {
		// 	$store_parent = $store_id;
		// }
		$store_parent = 5;

		//  get all child stores

		$pending_procurement = $this->inventory_management_model->v_not_awarded_requests($inventory_start_date, $product_id,$store_id);
		$deductions = $this->inventory_management_model->v_parent_items_deductions($inventory_start_date, $product_id,$store_id);
		$procurred = $this->inventory_management_model->view_product_purchases($inventory_start_date, $product_id,$store_id);
		$other_deductions = $this->inventory_management_model->view_product_transfers($inventory_start_date, $product_id,$store_id);
		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_parent);

		// var_dump($deductions); die();
		$total_stock = ($opening_quantity + $procurred) - ($deductions + $pending_procurement+$other_deductions);

		return $total_stock;
	}
	function get_opening_quantity_store($inventory_start_date,$product_id,$store_id)
	{
		$this->db->where('product_id = '.$product_id.' AND owning_store_id = '.$store_id.' ');
		$query = $this->db->get('store_product');
		$opening_quantity =0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$opening_quantity = $value->store_quantity;
			}
		}
		return $opening_quantity;
	}


	public function create_forced_order($store_id,$visit_id)
	{
		//check if store is parent store
		// $order_type = $this->is_store_parent($store_id);


		$this->db->where('visit_id = '.$visit_id.' AND store_id = '.$store_id.' ');
		$query = $this->db->get('orders');
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				return $value->order_id;
			}
		}
		else
		{
			$array = array(
				'orders_date'=>date('Y-m-d'),
				'order_number'=>$this->create_order_number(),
				'store_id'=>$store_id,
				'order_number'=>$visit_id,
				'created'=>date('Y-m-d H:i:s'),
				'supplier_id'=>0,
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id'),
				'order_type_id'=>1,
				'visit_id'=>$visit_id
			);
			if($this->db->insert('orders', $array))
			{
				$order_id = $this->db->insert_id();
				return $order_id;
			}
			else{
				return FALSE;
			}

		}

	}
	public function save_parent_deductions($product_id, $store_id, $order_id,$quantity,$visit_charge_id)
	{

		$this->db->where('visit_charge_id = '.$visit_charge_id.' AND order_id = '.$order_id.' AND product_id = '.$product_id.' ');
		$query = $this->db->get('product_deductions');
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$product_deduction_id = $value->product_deductions_id;
			}

			// var_dump($product_deduction_id); die();
			$data = array('store_id' => $store_id, 'quantity_requested'=>$quantity,'product_deduction_rejected' => 0 ,'order_id' => $order_id, 'product_id'=> $product_id,'date_requested'=>date('Y-m-d H:i:s'),'search_date'=>date('Y-m-d'),'requested_by'=>$this->session->userdata('personnel_id'),'visit_charge_id'=>$visit_charge_id);
			$this->db->where('product_deductions_id',$product_deduction_id);

			if($this->db->update('product_deductions', $data))
			{
				$product_deduction_id = $this->db->insert_id();

				return TRUE;
			}
			else
			{
				return FALSE;
			}


		}
		else
		{

			$data = array('store_id' => $store_id, 'quantity_requested'=>$quantity, 'order_id' => $order_id, 'product_id'=> $product_id,'date_requested'=>date('Y-m-d H:i:s'),'search_date'=>date('Y-m-d'),'requested_by'=>$this->session->userdata('personnel_id'),'visit_charge_id'=>$visit_charge_id);

			if($this->db->insert('product_deductions', $data))
			{
				$product_deduction_id = $this->db->insert_id();


				return TRUE;
			}
			else
			{
				return FALSE;
			}

		}

	}


	public function get_order_items($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
				// $table = 'product_deductions, store, product,orders,visit_charge';
		$this->db->select('product.product_id AS drug_id, product.*,store.*,orders.*,visit_charge.*,product_deductions.*,personnel.*,category.*,unit.*');
		$this->db->where($where);

		$this->db->join('category', 'category.category_id = product.category_id', 'left');
		$this->db->join('personnel','visit_charge.created_by = personnel.personnel_id','left');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$this->db->order_by($order,'DESC');
		$query = $this->db->get($table, $per_page, $page);

		return $query;
	}
	public function get_order_items_store($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		// $this->db->select('*');
			// $table = 'product_deductions, store, product, orders';
		$this->db->select('product.product_id AS drug_id,product.store_id AS parent_store, product.*,store.*,orders.*,product_deductions.*,personnel.*,category.*,unit.*');
		$this->db->where($where);

		$this->db->join('category', 'category.category_id = product.category_id', 'left');
		$this->db->join('personnel','orders.created_by = personnel.personnel_id','left');
		$this->db->join('unit', 'product.unit_id = unit.unit_id', 'left');
		$this->db->order_by($order,'DESC');
		$query = $this->db->get($table, $per_page, $page);

		return $query;
	}

	public function update_current_stock($product_id,$store_id)
	{
		$data = array('store_quantity' => $this->input->post('amount'));
		$this->db->where('product_id ='.$product_id.' AND owning_store_id = '.$store_id.' ');
		if($this->db->update('store_product', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function update_product_price($product_id)
	{
		$data = array('product_unitprice' => $this->input->post('product_unitprice'),'vatable'=>$this->input->post('vatable'));
		$this->db->where('product_id ='.$product_id.'');
		if($this->db->update('product', $data))
		{

			// update service charge
			$array['service_charge_amount'] = $this->input->post('product_unitprice');
			$array['vatable'] = $this->input->post('vatable');
			$this->db->where('product_id ='.$product_id.'');
			$this->db->update('service_charge', $array);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function parent_stock_store_old($inventory_start_date, $product_id,$store_id)
	{
		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,5);
		$purchases = $this->inventory_management_model->product_purchases($inventory_start_date, $product_id,$store_id);
    	$transfers = $this->inventory_management_model->view_product_transfers($inventory_start_date, $product_id,$store_id);
    	// deduction 1
    	$store_deductions = $this->inventory_management_model->view_product_deducted($inventory_start_date, $product_id,$store_id);
    	// deduction 2
    	$s11 = $this->inventory_management_model->view_product_disbersed_main($inventory_start_date, $product_id,null);
    	//deduction 3
    	$total_store_deductions =  $transfers + $s11 + $store_deductions;
    	$sales = $transfers;
    	$deductions = $s11 + $store_deductions;
    	$in_stock = ($opening_quantity + $purchases) - $total_store_deductions;



    	return $in_stock;
	}
	public function parent_stock_store($inventory_start_date, $product_id,$store_id)
	{
	   $quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);
		$purchases = $this->inventory_management_model->product_purchases($inventory_start_date, $product_id,$store_id);


		$additions = $this->inventory_management_model->product_additions($product_id);
		$subtraction = $this->inventory_management_model->product_subtractions($product_id);
		$credit_notes = $this->inventory_management_model->product_credit_notes($inventory_start_date, $product_id,$store_id);
		$transfers = $this->inventory_management_model->product_transfers($inventory_start_date, $product_id,$store_id);
		 $additions += $this->inventory_management_model->product_store_additions($product_id,$store_id);

		if($store_id == 15)
		{
			$transfers += $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date,$search_end_date, $branch_code=NULL);
		}
		// deduction 1
		$store_deductions = $this->inventory_management_model->product_deducted($inventory_start_date, $product_id,$store_id);
		// deduction 2
		$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,null);


		$total_store_deductions =  $transfers + $s11 + $store_deductions;
		$sales = $transfers;
		$deductions = $s11 + $store_deductions + $credit_notes;

		// var_dump($credit_notes); die();
		$in_stock = ($quantity + $purchases + $additions) - $total_store_deductions - $credit_notes - $subtraction;



		// $opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,5);
		// $credit_notes = $this->inventory_management_model->product_credit_notes($inventory_start_date, $product_id,5);
		// $purchases = $this->inventory_management_model->product_purchases($inventory_start_date, $product_id,$store_id);
    // 	$transfers = $this->inventory_management_model->product_transfers($inventory_start_date, $product_id,$store_id);
    // 	// deduction 1
    // 	$store_deductions = $this->inventory_management_model->product_deducted($inventory_start_date, $product_id,$store_id);
		//
		//
    // 	$additions = $this->inventory_management_model->product_additions($product_id);
    //     $subtraction = $this->inventory_management_model->product_subtractions($product_id);
    //     $addition = $this->inventory_management_model->product_store_additions($product_id,$store_id);
    // 	// deduction 2
    // 	$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,null);
    // 	//deduction 3
    // 	$total_store_deductions =  $transfers + $s11 + $store_deductions+$credit_notes +$subtraction;
    // 	$sales = $transfers;
    // 	$deductions = $s11 + $store_deductions;
    // 	$in_stock = ($opening_quantity + $purchases + $additions) - $total_store_deductions + $addition;

    	return $in_stock;
	}
	public function child_stock_store($inventory_start_date, $product_id,$store_id)
	{
		$opening_quantity = $this->inventory_management_model->get_opening_quantity_store($inventory_start_date, $product_id,$store_id);
    	$s11 = $this->inventory_management_model->product_disbersed($inventory_start_date, $product_id,$store_id); ;
    	$sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date=null,$search_end_date=null, $branch_code=NULL);
    	$store_requests  = $this->inventory_management_model->product_added($inventory_start_date, $product_id,$store_id);
    	$subtraction = $this->inventory_management_model->product_store_additions($product_id,$store_id);
    	$purchases = $store_requests + $s11;
    	// var_dump($subtraction); die();
    	$in_stock = ($opening_quantity + $purchases) - $sales - $subtraction;

    	return $in_stock;
	}

	function export_stock_levels()
	{
		$this->load->library('excel');
		$where = 'product.category_id = category.category_id AND product.product_deleted = 0 AND product.category_id = 2';
		$table = 'product, category';


		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('product.product_unitprice','DESC');
		$query = $this->db->get($table);

		$title = 'Stock Level report '.date('jS M Y H:i a',strtotime(date('Y-m-d H:i:s')));
		$col_count = 0;
		$inventory_start_date= $this->inventory_management_model->get_inventory_start_date();
		if($query->num_rows() > 0)
		{
			$count = 0;
			/*
				-----------------------------------------------------------------------------------------
				Document Header
				-----------------------------------------------------------------------------------------
			*/
			$row_count = 0;
			$report[$row_count][$col_count] = '#';
			$col_count++;
			$report[$row_count][$col_count] = 'Code';
			$col_count++;
			$report[$row_count][$col_count] = 'Product Name';
			$col_count++;
			$report[$row_count][$col_count] = 'Category';
			$col_count++;
			$report[$row_count][$col_count] = 'VAT status';
			$col_count++;
			$report[$row_count][$col_count] = 'Main Store';
			$col_count++;
			$report[$row_count][$col_count] = 'Pharmacy';
			$col_count++;
			$report[$row_count][$col_count] = 'Total';
			$col_count++;
			$report[$row_count][$col_count] = 'Selling Price';
			$col_count++;
			$current_column = $col_count ;

			foreach ($query->result() as $row)
			{
				$count++;
				$row_count++;
				$col_count = 0;

				$total_invoiced = 0;

				$product_id = $row->product_id;
				$product_name = $row->product_name;
				$product_code = $row->product_code;
				$product_status = $row->product_status;
				$product_description = $row->product_description;
				$category_id = $row->category_id;
				$created = $row->created;
				$created_by = $row->created_by;
				$last_modified = $row->last_modified;
				$modified_by = $row->modified_by;
				$category_name = $row->category_name;
				$store_id = $row->store_id;
				$reorder_level = $row->reorder_level;
				$parent_store = $row->store_id;
				$vatable = $row->vatable;




				$product_unitprice = $row->product_unitprice;

                $product_deleted = $row->product_deleted;


				//status
				if($product_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}


				if($vatable == 1)
				{
					$vatable_status = 'Yes';
					$status_value ='<div class="col-lg-3">
	                                    <div class="radio">
	                                        <label>
	                                            <input id="optionsRadios1" type="radio" checked value="1" name="vatable">
	                                            Yes
	                                        </label>
	                                    </div>
	                                </div>
	                                <div class="col-lg-3">
	                                    <div class="radio">
	                                        <label>
	                                            <input id="optionsRadios2" type="radio" value="0" name="vatable">
	                                            No
	                                        </label>
	                                    </div>
	                                </div>';
				}
				else
				{
					$vatable_status = 'No';
					$status_value ='<div class="col-lg-3">
	                                    <div class="radio">
	                                        <label>
	                                            <input id="optionsRadios1" type="radio"  value="1" name="vatable">
	                                            Yes
	                                        </label>
	                                    </div>
	                                </div>
	                                <div class="col-lg-3">
	                                    <div class="radio">
	                                        <label>
	                                            <input id="optionsRadios2" type="radio" checked value="0" name="vatable">
	                                            No
	                                        </label>
	                                    </div>
	                                </div>';
				}



				$button = '';
				//create deactivated status display
				if($product_status == 0)
				{
					$status = '<span class="label label-danger">Deactivated</span>';
					if($parent_store == 1)
					{
						$button .= '<a class="btn btn-info btn-sm" href="'.site_url().'inventory/activate-product/'.$product_id.'" onclick="return confirm(\'Do you want to activate '.$product_name.'?\');">Activate</a>';
					}
				}
				//create activated status display
				else if($product_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					if($parent_store == 1)
					{
						$button .= '<a class="btn btn-default btn-sm" href="'.site_url().'inventory/deactivate-product/'.$product_id.'" onclick="return confirm(\'Do you want to deactivate '.$product_name.'?\');">Deactivate</a>';
					}
				}






                $child = 0;
                $parent = 0;

            	$store_id = 5;
            	$store_id = $owning_store_id;


                $purchases = 0;
                $sales = 0;
                $purchases = 0;
                $deductions = 0;
                $in_stock = 0;
                $pending_procurement = 0;
                $opening_quantity = 0;
                $total_store_deductions = 0;
                $child_stock = 0;

                $stock_level = $this->inventory_management_model->get_stock_purchases_new($product_id,5);

                $purchases = $stock_level['dr_quantity'];
                $deductions = $stock_level['cr_quantity'];
               	
               	$parent_stock = $purchases - $deductions;

               	$child_stock_level = $this->inventory_management_model->get_stock_purchases_new($product_id,6);

                $child_purchases = $child_stock_level['dr_quantity'];
                $child_deductions = $child_stock_level['cr_quantity'];
               	
                $child_stock = $child_purchases - $child_deductions;


				$total_stock = $child_stock+$parent_stock;

				$report[$row_count][$col_count] = $count;
				$col_count++;
				$report[$row_count][$col_count] = $product_code;
				$col_count++;
				$report[$row_count][$col_count] = $product_name;
				$col_count++;
				$report[$row_count][$col_count] = $category_name;
				$col_count++;
				$report[$row_count][$col_count] = $vatable_status;
				$col_count++;
				$report[$row_count][$col_count] = $parent_stock;
				$col_count++;
				$report[$row_count][$col_count] = $child_stock;
				$col_count++;
				$report[$row_count][$col_count] = $total_stock;
				$col_count++;
				$report[$row_count][$col_count] = $product_unitprice;
				$col_count++;
			}
		}

		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}


	public function product_additions($product_id)
	{
		$where = "product_purchase.product_id = ".$product_id;
		$table = 'product_purchase';

		$this->db->from($table);
		$this->db->select('product_purchase.purchase_id AS invoice_id, product_purchase.purchase_id AS deduction_number,product_purchase.purchase_date AS date_added, (purchase_quantity * purchase_pack_size) AS total_purchases,purchase_description as description');
		$this->db->where($where);
		$this->db->order_by('product_purchase.purchase_date','ASC');
		$query_supplier = $this->db->get();
		$total_purchases = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$total_purchases = $query_rs->total_purchases;
		}
		return $total_purchases;
	}



	public function product_subtractions($product_id)
	{
		$where = "product_deductions_stock.product_id = ".$product_id;
		$table = 'product_deductions_stock';

		$this->db->from($table);
		$this->db->select('product_deductions_stock.product_deductions_stock_id AS invoice_id, product_deductions_stock.product_deductions_stock_id AS deduction_number,product_deductions_stock.product_deductions_stock_date AS date_added, (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS subtracted_quantity,deduction_description as description');
		$this->db->where($where);
		$this->db->order_by('product_deductions_stock.product_deductions_stock_date','ASC');
		$query_supplier = $this->db->get();

		$subtracted_quantity = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$subtracted_quantity = $query_rs->subtracted_quantity;
		}
		return $subtracted_quantity;
	}
	public function product_store_additions($product_id,$store_id)
	{

		$where = "product_return_stock.product_id = ".$product_id;
		$table = 'product_return_stock';
		if($store_id == 5)
		{
			$where .= ' AND to_store_id = '.$store_id;
		}
		else
		{
			$where .= ' AND from_store_id = '.$store_id;
		}
		$this->db->from($table);
		$this->db->select('product_return_stock.product_deductions_stock_id AS invoice_id, product_return_stock.product_deductions_stock_id AS deduction_number,product_return_stock.product_deductions_stock_date AS date_added,SUM(product_deductions_stock_quantity * product_deductions_stock_pack_size) AS subtracted_quantity,deduction_description as description');
		$this->db->where($where);
		$this->db->order_by('product_return_stock.product_deductions_stock_date','ASC');
		$query_supplier = $this->db->get();

		$subtracted_quantity = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$subtracted_quantity = $query_rs->subtracted_quantity;
		}
		return $subtracted_quantity;
	}
	public function get_tests_units($product_id)
	{
		$where = "product_id = ".$product_id;
		$table = 'test_costing';

		$this->db->from($table);
		$this->db->select('test_costing_units AS total_units,lab_test_id');
		$this->db->where($where);
		$query_supplier = $this->db->get();

		$quantity_checked = 0;
		$value_tests = 0;
		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->result();
			$total_units_value = 0;
			foreach ($query_rs as $key => $value) {
				# code...

				$total_units = $value->total_units;
				$lab_test_id = $value->lab_test_id;

				$where = "service_charge.lab_test_id = lab_test.lab_test_id
							AND lab_test.lab_test_delete = 0
							AND service_charge.service_charge_delete = 0
							AND visit_charge.visit_charge_delete = 0
							AND service_charge.service_charge_id = visit_charge.service_charge_id
							AND service_charge.lab_test_id = ".$lab_test_id;
				$table = 'visit_charge,service_charge,lab_test';

				$this->db->from($table);
				$this->db->select('SUM(visit_charge_units) AS total_units');
				$this->db->where($where);
				$query_supplier_new = $this->db->get();

				if($query_supplier_new->num_rows() > 0)
				{
					$query_rs = $query_supplier_new->row();

					$amount_value = $query_rs->total_units;
					if(empty($amount_value))
					{
						$amount_value = 0;
					}

					$total_value = $total_units * $amount_value;
					$value_tests += $total_value;
					// echo $total_value;
				}

			}

		}

		// var_dump($value_tests); die();
		return $value_tests;
	}
	function get_other_stores_stock($inventory_start_date,$product_id,$store_id)
	{
				$quantity = $this->inventory_management_model->get_other_stores_opening_stock($inventory_start_date, $product_id,$store_id);
				$s11 = $this->inventory_management_model->get_other_product_disbersed($inventory_start_date, $product_id,$store_id);
				$sales = $this->inventory_management_model->get_other_stores_drugs_sold($inventory_start_date, $product_id,$search_start_date=null,$search_end_date=null, $branch_code=NULL);

				$store_requests = $purchases = $this->inventory_management_model->get_other_product_added($inventory_start_date, $product_id,$store_id);

				$purchases = $store_requests + $s11;
				$subtractions = $this->inventory_management_model->product_other_store_additions($product_id,$store_id);
				$unit_quantity = 0;
				if($store_id == 14)
				{
					// $get laboratory deductions
					$unit_quantity = $this->inventory_management_model->get_tests_units($product_id);
					// var_dump($unit_quantity); die();

					// $sales
				}
				// var_dump($unit_quantity); die();
				$in_stock = ($quantity + $purchases) - ($sales + $unit_quantity + $subtractions);


		return $in_stock;
	}
	function get_other_stores_opening_stock($inventory_start_date,$product_id,$store_id)
	{
		$this->db->where('product_id = '.$product_id.' AND owning_store_id <> 5 ');
		$query = $this->db->get('store_product');
		$opening_quantity =0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$opening_quantity = $value->store_quantity;
			}
		}
		return $opening_quantity;
	}

	public function get_other_product_disbersed($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		if(empty($store_id))
		{
			$store_values = '';
		}
		else
		{
			$store_values = ' AND product_deductions.store_id <> 5';
		}
		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id ".$store_values." AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND product_deductions.quantity_requested > 0 AND product_deductions.product_deduction_rejected = 0 AND orders.order_id = product_deductions.order_id AND (product_deductions.visit_charge_id IS NOT NULL OR product_deductions.visit_charge_id <> 0) AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$product_id;
		$items = "SUM(quantity_given) AS quantity_given,product.product_id";
		$order = "product_deductions_pack_size";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}

	public function get_other_stores_drugs_sold($inventory_start_date, $product_id, $start_date = NULL, $end_date = NULL, $branch_code = NULL)
	{
		$table = "visit_charge";
		$where = 'visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0 AND visit_charge.product_id ='.$product_id;

		if($branch_code != NULL)
		{
			$where .= ' AND visit_charge.branch_code = "'.$branch_code.'"';
		}
		if(($start_date != NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit_charge.date >= "'.$start_date.'" AND visit_charge.date<= "'.$end_date.'"';
		}

		else if(($start_date == NULL) && ($end_date != NULL))
		{
			 $where .= ' AND visit_charge.date <= "'.$end_date.'"';
		}

		else if(($start_date != NULL) && ($end_date == NULL))
		{
			 $where .= ' AND visit_charge.date >= "'.$start_date.'"';
		}

		$items = "SUM(visit_charge.visit_charge_units) AS total_sold,product_id";
		$order = "total_sold";

		$result = $this->database->select_entries_where($table, $where, $items, $order);
		$total_sold = 0;
		if(count($result) > 0)
		{
			foreach ($result as $key) {
				# code...
				$total_sold = $key->total_sold;
			}
		}
		return $total_sold;
	}

	public function get_other_product_added($inventory_start_date, $product_id, $store_id = NULL, $start_date = NULL, $end_date = NULL)
	{
		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 1 AND orders.store_id <> 5 AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$product_id;
		$items = "SUM(quantity_given) AS quantity_given,product.product_id";
		$order = "product_deductions_pack_size";

		$total = 0;

		$this->db->where($where);
		$this->db->select($items);
		$query_supplier = $this->db->get($table);
		$quantity_given = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();
			$quantity_given = $query_rs->quantity_given;
		}
		return $quantity_given;


	}

	public function product_other_store_additions($product_id,$store_id)
	{

		$where = "product_return_stock.product_id = ".$product_id;
		$table = 'product_return_stock';

		$where .= ' AND from_store_id <> 5';

		$this->db->from($table);
		$this->db->select('product_return_stock.product_deductions_stock_id AS invoice_id, product_return_stock.product_deductions_stock_id AS deduction_number,product_return_stock.product_deductions_stock_date AS date_added,SUM(product_deductions_stock_quantity * product_deductions_stock_pack_size) AS subtracted_quantity,deduction_description as description');
		$this->db->where($where);
		$this->db->order_by('product_return_stock.product_deductions_stock_date','ASC');
		$query_supplier = $this->db->get();

		$subtracted_quantity = 0;

		if($query_supplier->num_rows() > 0)
		{
			$query_rs = $query_supplier->row();

			$subtracted_quantity = $query_rs->subtracted_quantity;
		}
		return $subtracted_quantity;
	}







	// products 
	public function get_stock_purchases_new($product_id,$store_id)
	{
		// $table = 'v_stock_level';
		$where = 'store_id = '.$store_id.' AND product_id = '.$product_id;
		// $this->db->select('SUM(dr_quantity) AS starting_value');
		// $this->db->where($where);
		$select_query = "
						SELECT 
						    SUM(data.dr_amount) AS dr_amount,
						    SUM(data.dr_quantity) AS dr_quantity, 
						    SUM(data.cr_amount) AS cr_amount ,
						    SUM(data.cr_quantity) AS cr_quantity
						FROM 
							(SELECT
							    `store_product`.`store_product_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								store_product.owning_store_id AS `store_id`,
							    '' AS `receiving_store`,
								product.product_name AS `product_name`,
							    store.store_name AS `store_name`,
								CONCAT('Opening Balance of',' ',`product`.`product_name`) AS `transactionDescription`,
							    `store_product`.`store_quantity` AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(`product`.`product_unitprice` * `store_product`.`store_quantity` ) AS `dr_amount`,
								'0' AS `cr_amount`,
								`store_product`.`created` AS `transactionDate`,
								`product`.`product_status` AS `status`,
							    `product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Opening Stock' AS `transactionClassification`,
								'store_product' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM
							store_product,product,store
							WHERE  product.product_id = store_product.product_id AND product.product_deleted = 0
							AND store.store_id = store_product.owning_store_id AND store_product.owning_store_id = ".$store_id." AND store_product.product_id = ".$product_id."

							UNION ALL

							SELECT
							  `order_supplier`.`order_supplier_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								orders.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Purchase of',' ',`product`.`product_name`) AS `transactionDescription`,
								(quantity_received*pack_size) AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(order_supplier.total_amount) AS `dr_amount`,
								'0' AS `cr_amount`,
								`orders`.`supplier_invoice_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Supplier Purchases' AS `transactionClassification`,
								'order_item' AS `transactionTable`,
								'orders' AS `referenceTable`
							FROM (`order_item`, `order_supplier`, `product`, `orders`,store)
							WHERE
							`order_item`.`order_item_id` = order_supplier.order_item_id
							AND order_item.product_id = product.product_id
							AND orders.order_id = order_item.order_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.is_store < 2
							AND orders.order_approval_status = 7
							AND product.product_id
							AND store.store_id = orders.store_id
							AND orders.store_id = ".$store_id." AND order_item.product_id = ".$product_id."


							UNION ALL

							SELECT
								`product_purchase`.`purchase_id` AS transactionId,
							    `product_purchase`.`product_id` AS `product_id`,
							    `product`.`category_id` AS `category_id`,
								 product_purchase.store_id AS `store_id`,
								 '' AS `receiving_store`,
								 product.product_name AS `product_name`,
							  	store.store_name AS `store_name`,
								product_purchase.purchase_description AS `transactionDescription`,
							    (purchase_quantity * purchase_pack_size) AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(`product`.`product_unitprice` * (purchase_quantity * purchase_pack_size) ) AS `dr_amount`,
								'0' AS `cr_amount`,
								`product_purchase`.`purchase_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
							  `product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_purchase' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_purchase`,product,store)
							WHERE product.product_id = product_purchase.product_id AND product.product_deleted = 0
							AND store.store_id = product_purchase.store_id
							AND product_purchase.store_id = ".$store_id." AND product_purchase.product_id = ".$product_id."


							UNION ALL


							SELECT
							`product_deductions_stock`.`product_deductions_stock_id` AS transactionId,
							`product_deductions_stock`.`product_id` AS `product_id`,
							`product`.`category_id` AS `category_id`,
							 product_deductions_stock.store_id AS `store_id`,
							 '' AS `receiving_store`,
							 product.product_name AS `product_name`,
							store.store_name AS `store_name`,
							 product_deductions_stock.deduction_description AS `transactionDescription`,
							 '0' AS `dr_quantity`,
							 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS  `cr_quantity`,
							'0' AS `dr_amount`,
							 (`product`.`product_unitprice` * (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
							`product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`,
							`product`.`product_status` AS `status`,
							`product`.`product_deleted` AS `product_deleted`,
							'Expense' AS `transactionCategory`,
							'Product Deductions' AS `transactionClassification`,
							'product_deductions_stock' AS `transactionTable`,
							'product' AS `referenceTable`
							FROM (`product_deductions_stock`,product,store)
							WHERE product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_deductions_stock.store_id
							AND product_deductions_stock.store_id = ".$store_id." AND product_deductions_stock.product_id = ".$product_id."


							UNION ALL

							SELECT
								`order_supplier`.`order_supplier_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								orders.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Credit note of',' ',`product`.`product_name`) AS `transactionDescription`,
								 '0' AS `dr_quantity`,
							   (quantity_received*pack_size) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(order_supplier.total_amount) AS `cr_amount`,
								`orders`.`supplier_invoice_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Supplier Credit Note' AS `transactionClassification`,
								'order_item' AS `transactionTable`,
								'orders' AS `referenceTable`
							FROM (`order_item`, `order_supplier`, `product`, `orders`,store)
							WHERE `order_item`.`order_item_id` = order_supplier.order_item_id
							AND order_item.product_id = product.product_id
							AND orders.order_id = order_item.order_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.order_approval_status = 7
							AND orders.is_store = 3
							AND store.store_id = orders.store_id
							AND orders.store_id = ".$store_id." AND order_item.product_id = ".$product_id."


							UNION ALL


							SELECT
								`product_return_stock`.`product_deductions_stock_id` AS transactionId,
								`product_return_stock`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								 product_return_stock.from_store_id AS `store_id`,
								 product_return_stock.to_store_id AS `receiving_store`,
								 product.product_name AS `product_name`,
								 store.store_name AS `store_name`,
								 CONCAT('Store Transfer') AS `transactionDescription`,
								 '0' AS `dr_quantity`,
								 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(product.product_unitprice* (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
								`product_return_stock`.`product_deductions_stock_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_return_stock' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_return_stock`,product,store)
							WHERE product.product_id = product_return_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_return_stock.from_store_id
							AND product_return_stock.from_store_id = ".$store_id." AND product_return_stock.product_id = ".$product_id."

							UNION ALL

							SELECT
								`product_return_stock`.`product_deductions_stock_id` AS transactionId,
								`product_return_stock`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								 product_return_stock.to_store_id AS `store_id`,
								 product_return_stock.from_store_id AS `receiving_store`,
								 product.product_name AS `product_name`,
								 store.store_name AS `store_name`,
								 CONCAT('Store Transfer') AS `transactionDescription`,
								 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS `dr_quantity`,
								 '0' AS `cr_quantity`,
								 (product.product_unitprice* (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `dr_amount`,
								 '0' AS `cr_amount`,
								`product_return_stock`.`product_deductions_stock_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Store Deduction' AS `transactionClassification`,
								'product_return_stock' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_return_stock`,product,store)
							WHERE product.product_id = product_return_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_return_stock.to_store_id
							AND product_return_stock.to_store_id = ".$store_id." AND product_return_stock.product_id = ".$product_id."


							UNION ALL

							-- drug sale

							SELECT
								`visit_charge`.`visit_charge_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								visit_charge.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Sale',' ',`product`.`product_name`) AS `transactionDescription`,
								'0' AS `dr_quantity`,
							    (visit_charge.visit_charge_units) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(visit_charge.visit_charge_units * visit_charge.buying_price) AS `cr_amount`,
								`visit_charge`.`date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Drug Sales' AS `transactionClassification`,
								'visit_charge' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`visit_charge`,product,store)
							WHERE `visit_charge`.`charged` = 1
							AND visit_charge.visit_charge_delete = 0
							AND product.product_id = visit_charge.product_id AND product.product_deleted = 0 
							AND store.store_id = visit_charge.store_id
							AND visit_charge.store_id = ".$store_id."
							AND visit_charge.product_id = ".$product_id."


							UNION ALL

							-- store deductions
							SELECT
							  `product_deductions`.`product_deductions_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								`product_deductions`.`store_id` AS `store_id`,
								`product`.`store_id` AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Added',' ',`product`.`product_name`) AS `transactionDescription`,
								 product_deductions.quantity_given AS `dr_quantity`,
							   '0' AS `cr_quantity`,
								 (product.product_unitprice * product_deductions.quantity_given) AS `dr_amount`,
								 '0' AS `cr_amount`,
								`product_deductions`.`search_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_deductions' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_deductions`, `store`, `product`, `orders`)
							WHERE `product_deductions`.`store_id` = store.store_id
							AND product_deductions.quantity_requested > 0
							AND product.product_deleted = 0
							AND product_deductions.product_id = product.product_id
							AND product_deductions.order_id = orders.order_id
							AND orders.order_id = product_deductions.order_id
							AND (orders.is_store = 1 OR orders.is_store = 0)
							AND product_deductions.product_deduction_rejected = 0
							AND product_deductions.store_id = ".$store_id." AND product_deductions.product_id = ".$product_id."
							

							UNION ALL
							
							SELECT
							  `product_deductions`.`product_deductions_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								product.store_id AS `store_id`,
								product_deductions.store_id AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Deducted',' ',`product`.`product_name`) AS `transactionDescription`,
								 '0' AS `dr_quantity`,
							     product_deductions.quantity_given AS `cr_quantity`,
								 '0' AS `dr_amount`,
								 (product.product_unitprice * product_deductions.quantity_given) AS `cr_amount`,
								`product_deductions`.`search_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Store Deduction' AS `transactionClassification`,
								'product_deductions' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_deductions`, `store`, `product`, `orders`)
							WHERE store.store_id = product_deductions.store_id
							AND product_deductions.quantity_requested > 0
							AND product_deductions.product_id = product.product_id
							AND product_deductions.order_id = orders.order_id
							AND orders.order_id = product_deductions.order_id
							AND product.product_deleted = 0
							AND (orders.is_store = 1 OR orders.is_store = 0)
							AND product_deductions.product_deduction_rejected = 0
							AND product.store_id = ".$store_id." AND product_deductions.product_id = ".$product_id."


							UNION ALL 

							SELECT
							`product_deductions`.`product_deductions_id` AS `transactionId`,
							`product`.`product_id` AS `product_id`,
							`product`.`category_id` AS `category_id`,
							`store`.`store_id` AS `store_id`,
							'' AS `receiving_store`,
							product.product_name AS `product_name`,
							store.store_name AS `store_name`,
							CONCAT('Product Added',' ',`product`.`product_name`) AS `transactionDescription`,
							  '0'  AS `dr_quantity`,
							 (quantity_given*pack_size) AS `cr_quantity`,
							 '0' AS `dr_amount`,
							 (product.product_unitprice * (quantity_given*pack_size) ) AS `cr_amount`,
							`product_deductions`.`search_date` AS `transactionDate`,
							`product`.`product_status` AS `status`,
							`product`.`product_deleted` AS `product_deleted`,
							'Expense' AS `transactionCategory`,
							'Drug Transfer' AS `transactionClassification`,
							'product_deductions' AS `transactionTable`,
							'product' AS `referenceTable`
							FROM (`product_deductions`, `product`, `orders`,store)
							WHERE `product_deductions`.`order_id` = orders.order_id
							AND product_deductions.product_id = product.product_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.is_store = 2
							AND orders.order_approval_status = 7
							AND `orders`.`store_id` = store.store_id
							AND orders.store_id = ".$store_id." AND product_deductions.product_id = ".$product_id."
						) AS data";
		// $this->db->group_by('product_id');
		$query = $this->db->query($select_query);
		// var_dump($query->result());die();

		$result = $query->row();

		$dr_quantity = 0;
		$cr_quantity = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$dr_quantity = $value->dr_quantity;
				$cr_quantity = $value->cr_quantity;
			}
		}

		$response['cr_quantity'] = $cr_quantity;
		$response['dr_quantity'] = $dr_quantity;
		return  $response;
	}



	public function get_all_stock($product_id)
	{
		// $table = 'v_stock_level';
		// $where = 'store_id = '.$store_id.' AND product_id = '.$product_id;
		// $this->db->select('SUM(dr_quantity) AS starting_value');
		// $this->db->where($where);
		$select_query = "
						SELECT 
						    SUM(data.dr_amount) AS dr_amount,
						    SUM(data.dr_quantity) AS dr_quantity, 
						    SUM(data.cr_amount) AS cr_amount ,
						    SUM(data.cr_quantity) AS cr_quantity
						FROM 
							(SELECT
							    `store_product`.`store_product_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								store_product.owning_store_id AS `store_id`,
							    '' AS `receiving_store`,
								product.product_name AS `product_name`,
							    store.store_name AS `store_name`,
								CONCAT('Opening Balance of',' ',`product`.`product_name`) AS `transactionDescription`,
							    `store_product`.`store_quantity` AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(`product`.`product_unitprice` * `store_product`.`store_quantity` ) AS `dr_amount`,
								'0' AS `cr_amount`,
								`store_product`.`created` AS `transactionDate`,
								`product`.`product_status` AS `status`,
							    `product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Opening Stock' AS `transactionClassification`,
								'store_product' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM
							store_product,product,store
							WHERE  product.product_id = store_product.product_id AND product.product_deleted = 0
							AND store.store_id = store_product.owning_store_id  AND store_product.product_id = ".$product_id."

							UNION ALL

							SELECT
							  `order_supplier`.`order_supplier_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								orders.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Purchase of',' ',`product`.`product_name`) AS `transactionDescription`,
								(quantity_received*pack_size) AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(order_supplier.total_amount) AS `dr_amount`,
								'0' AS `cr_amount`,
								`orders`.`supplier_invoice_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Supplier Purchases' AS `transactionClassification`,
								'order_item' AS `transactionTable`,
								'orders' AS `referenceTable`
							FROM (`order_item`, `order_supplier`, `product`, `orders`,store)
							WHERE
							`order_item`.`order_item_id` = order_supplier.order_item_id
							AND order_item.product_id = product.product_id
							AND orders.order_id = order_item.order_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.is_store < 2
							AND orders.order_approval_status = 7
							AND product.product_id
							AND store.store_id = orders.store_id
							AND order_item.product_id = ".$product_id."


							UNION ALL

							SELECT
								`product_purchase`.`purchase_id` AS transactionId,
							    `product_purchase`.`product_id` AS `product_id`,
							    `product`.`category_id` AS `category_id`,
								 product_purchase.store_id AS `store_id`,
								 '' AS `receiving_store`,
								 product.product_name AS `product_name`,
							  	store.store_name AS `store_name`,
								product_purchase.purchase_description AS `transactionDescription`,
							    (purchase_quantity * purchase_pack_size) AS `dr_quantity`,
							    '0' AS `cr_quantity`,
								(`product`.`product_unitprice` * (purchase_quantity * purchase_pack_size) ) AS `dr_amount`,
								'0' AS `cr_amount`,
								`product_purchase`.`purchase_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
							  `product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_purchase' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_purchase`,product,store)
							WHERE product.product_id = product_purchase.product_id AND product.product_deleted = 0
							AND store.store_id = product_purchase.store_id
							AND product_purchase.product_id = ".$product_id."


							UNION ALL


							SELECT
							`product_deductions_stock`.`product_deductions_stock_id` AS transactionId,
							`product_deductions_stock`.`product_id` AS `product_id`,
							`product`.`category_id` AS `category_id`,
							 product_deductions_stock.store_id AS `store_id`,
							 '' AS `receiving_store`,
							 product.product_name AS `product_name`,
							store.store_name AS `store_name`,
							 product_deductions_stock.deduction_description AS `transactionDescription`,
							 '0' AS `dr_quantity`,
							 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS  `cr_quantity`,
							'0' AS `dr_amount`,
							 (`product`.`product_unitprice` * (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
							`product_deductions_stock`.`product_deductions_stock_date` AS `transactionDate`,
							`product`.`product_status` AS `status`,
							`product`.`product_deleted` AS `product_deleted`,
							'Expense' AS `transactionCategory`,
							'Product Deductions' AS `transactionClassification`,
							'product_deductions_stock' AS `transactionTable`,
							'product' AS `referenceTable`
							FROM (`product_deductions_stock`,product,store)
							WHERE product.product_id = product_deductions_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_deductions_stock.store_id
							AND product_deductions_stock.product_id = ".$product_id."


							UNION ALL

							SELECT
								`order_supplier`.`order_supplier_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								orders.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Credit note of',' ',`product`.`product_name`) AS `transactionDescription`,
								 '0' AS `dr_quantity`,
							   (quantity_received*pack_size) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(order_supplier.total_amount) AS `cr_amount`,
								`orders`.`supplier_invoice_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Supplier Credit Note' AS `transactionClassification`,
								'order_item' AS `transactionTable`,
								'orders' AS `referenceTable`
							FROM (`order_item`, `order_supplier`, `product`, `orders`,store)
							WHERE `order_item`.`order_item_id` = order_supplier.order_item_id
							AND order_item.product_id = product.product_id
							AND orders.order_id = order_item.order_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.order_approval_status = 7
							AND orders.is_store = 3
							AND store.store_id = orders.store_id
							AND order_item.product_id = ".$product_id."


							UNION ALL


							SELECT
								`product_return_stock`.`product_deductions_stock_id` AS transactionId,
								`product_return_stock`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								 product_return_stock.from_store_id AS `store_id`,
								 product_return_stock.to_store_id AS `receiving_store`,
								 product.product_name AS `product_name`,
								 store.store_name AS `store_name`,
								 CONCAT('Store Transfer') AS `transactionDescription`,
								 '0' AS `dr_quantity`,
								 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(product.product_unitprice* (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `cr_amount`,
								`product_return_stock`.`product_deductions_stock_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_return_stock' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_return_stock`,product,store)
							WHERE product.product_id = product_return_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_return_stock.from_store_id
							AND product_return_stock.product_id = ".$product_id."

							UNION ALL

							SELECT
								`product_return_stock`.`product_deductions_stock_id` AS transactionId,
								`product_return_stock`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								 product_return_stock.to_store_id AS `store_id`,
								 product_return_stock.from_store_id AS `receiving_store`,
								 product.product_name AS `product_name`,
								 store.store_name AS `store_name`,
								 CONCAT('Store Transfer') AS `transactionDescription`,
								 (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS `dr_quantity`,
								 '0' AS `cr_quantity`,
								 (product.product_unitprice* (product_deductions_stock_quantity * product_deductions_stock_pack_size)) AS `dr_amount`,
								 '0' AS `cr_amount`,
								`product_return_stock`.`product_deductions_stock_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Store Deduction' AS `transactionClassification`,
								'product_return_stock' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_return_stock`,product,store)
							WHERE product.product_id = product_return_stock.product_id AND product.product_deleted = 0
							AND store.store_id = product_return_stock.to_store_id
							AND product_return_stock.product_id = ".$product_id."


							UNION ALL

							-- drug sale

							SELECT
								`visit_charge`.`visit_charge_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								visit_charge.store_id AS `store_id`,
								'' AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Sale',' ',`product`.`product_name`) AS `transactionDescription`,
								'0' AS `dr_quantity`,
							    (visit_charge.visit_charge_units) AS `cr_quantity`,
								 '0' AS `dr_amount`,
								(visit_charge.visit_charge_units * visit_charge.buying_price) AS `cr_amount`,
								`visit_charge`.`date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Drug Sales' AS `transactionClassification`,
								'visit_charge' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`visit_charge`,product,store)
							WHERE `visit_charge`.`charged` = 1
							AND visit_charge.visit_charge_delete = 0
							AND product.product_id = visit_charge.product_id AND product.product_deleted = 0 
							AND store.store_id = visit_charge.store_id
							AND visit_charge.product_id = ".$product_id."


							UNION ALL

							-- store deductions
							SELECT
							  `product_deductions`.`product_deductions_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								`product_deductions`.`store_id` AS `store_id`,
								`product`.`store_id` AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Added',' ',`product`.`product_name`) AS `transactionDescription`,
								 product_deductions.quantity_given AS `dr_quantity`,
							   '0' AS `cr_quantity`,
								 (product.product_unitprice * product_deductions.quantity_given) AS `dr_amount`,
								 '0' AS `cr_amount`,
								`product_deductions`.`search_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Income' AS `transactionCategory`,
								'Product Addition' AS `transactionClassification`,
								'product_deductions' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_deductions`, `store`, `product`, `orders`)
							WHERE `product_deductions`.`store_id` = store.store_id
							AND product_deductions.quantity_requested > 0
							AND product.product_deleted = 0
							AND product_deductions.product_id = product.product_id
							AND product_deductions.order_id = orders.order_id
							AND orders.order_id = product_deductions.order_id
							AND (orders.is_store = 1 OR orders.is_store = 0)
							AND product_deductions.product_deduction_rejected = 0
							AND product_deductions.product_id = ".$product_id."
							

							UNION ALL
							
							SELECT
							  `product_deductions`.`product_deductions_id` AS `transactionId`,
								`product`.`product_id` AS `product_id`,
								`product`.`category_id` AS `category_id`,
								product.store_id AS `store_id`,
								product_deductions.store_id AS `receiving_store`,
								product.product_name AS `product_name`,
								store.store_name AS `store_name`,
								CONCAT('Product Deducted',' ',`product`.`product_name`) AS `transactionDescription`,
								 '0' AS `dr_quantity`,
							     product_deductions.quantity_given AS `cr_quantity`,
								 '0' AS `dr_amount`,
								 (product.product_unitprice * product_deductions.quantity_given) AS `cr_amount`,
								`product_deductions`.`search_date` AS `transactionDate`,
								`product`.`product_status` AS `status`,
								`product`.`product_deleted` AS `product_deleted`,
								'Expense' AS `transactionCategory`,
								'Store Deduction' AS `transactionClassification`,
								'product_deductions' AS `transactionTable`,
								'product' AS `referenceTable`
							FROM (`product_deductions`, `store`, `product`, `orders`)
							WHERE store.store_id = product_deductions.store_id
							AND product_deductions.quantity_requested > 0
							AND product_deductions.product_id = product.product_id
							AND product_deductions.order_id = orders.order_id
							AND orders.order_id = product_deductions.order_id
							AND product.product_deleted = 0
							AND (orders.is_store = 1 OR orders.is_store = 0)
							AND product_deductions.product_deduction_rejected = 0
							AND product_deductions.product_id = ".$product_id."


							UNION ALL 

							SELECT
							`product_deductions`.`product_deductions_id` AS `transactionId`,
							`product`.`product_id` AS `product_id`,
							`product`.`category_id` AS `category_id`,
							`store`.`store_id` AS `store_id`,
							'' AS `receiving_store`,
							product.product_name AS `product_name`,
							store.store_name AS `store_name`,
							CONCAT('Product Added',' ',`product`.`product_name`) AS `transactionDescription`,
							  '0'  AS `dr_quantity`,
							 (quantity_given*pack_size) AS `cr_quantity`,
							 '0' AS `dr_amount`,
							 (product.product_unitprice * (quantity_given*pack_size) ) AS `cr_amount`,
							`product_deductions`.`search_date` AS `transactionDate`,
							`product`.`product_status` AS `status`,
							`product`.`product_deleted` AS `product_deleted`,
							'Expense' AS `transactionCategory`,
							'Drug Transfer' AS `transactionClassification`,
							'product_deductions' AS `transactionTable`,
							'product' AS `referenceTable`
							FROM (`product_deductions`, `product`, `orders`,store)
							WHERE `product_deductions`.`order_id` = orders.order_id
							AND product_deductions.product_id = product.product_id
							AND product.product_deleted = 0
							AND orders.supplier_id > 0
							AND orders.is_store = 2
							AND orders.order_approval_status = 7
							AND `orders`.`store_id` = store.store_id
							AND product_deductions.product_id = ".$product_id."
						) AS data";
		// $this->db->group_by('product_id');
		$query = $this->db->query($select_query);
		// var_dump($query->result());die();

		$result = $query->row();

		$dr_quantity = 0;
		$cr_quantity = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$dr_quantity = $value->dr_quantity;
				$cr_quantity = $value->cr_quantity;
			}
		}

		$response['cr_quantity'] = $cr_quantity;
		$response['dr_quantity'] = $dr_quantity;
		return  $response;
	}

	public function get_stock_purchases($product_id,$store_id)
	{
		$table = 'v_stock_level';
		$where = 'store_id = '.$store_id.' AND product_id = '.$product_id;
		$this->db->select('SUM(dr_quantity) AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value = 0;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}


	public function get_stock_deductions($product_id,$store_id)
	{
		$table = 'v_stock_level';
		$where = 'store_id = '.$store_id.' AND product_id = '.$product_id;
		$this->db->select('SUM(cr_quantity)  AS starting_value');
		$this->db->where($where);
		$query = $this->db->get($table);


		$result = $query->row();

		$starting_value  =0 ;
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$starting_value = $value->starting_value;
			}
		}
		return  $starting_value;
	}

}
