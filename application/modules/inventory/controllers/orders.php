<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Orders extends MX_Controller
{ 
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('admin/users_model');
		$this->load->model('inventory_management/products_model');
		$this->load->model('orders_model');
		$this->load->model('suppliers_model');
		$this->load->model('categories_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('administration/personnel_model');
		$this->load->model('hr/personnel_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('accounting/creditors_model');
		$this->load->model('inventory/stores_model');
		$this->load->model('reception/database');
		$this->load->model('reception/reception_model');
		$this->load->model('inventory_management/inventory_management_model');
	}
    
	/*
	*
	*	Default action is to show all the orders
	*
	*/
	public function index() 
	{
		// get my approval roles

		$where = 'orders.order_status_id = order_status.order_status_id AND orders.supplier_id IS NULL';
		$table = 'orders, order_status';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/general-orders';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = 4;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_orders($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		// $v_data['level_status'] = $this->orders_model->order_level_status();
		$v_data['title'] = "All Orders";
		$data['content'] = $this->load->view('orders/all_orders', $v_data, true);
		
		$data['title'] = 'All orders';
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add a new order
	*
	*/
	public function add_order() 
	{
		//form validation rules
		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			$order_id = $this->orders_model->add_order();
			//update order
			if($order_id > 0)
			{
				redirect('inventory/orders/'.$order_id);
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update order. Please try again');
			}
		}
		
		// $store_priviledges = $this->inventory_management_model->get_assigned_stores();
		// $v_data['store_priviledges'] =  $store_priviledges;
		//open the add new order
		$data['title'] = 'Add Order';
		$v_data['title'] = 'Add Order';
		$v_data['order_status_query'] = $this->orders_model->get_order_status();

		$data['content'] = $this->load->view('orders/add_order', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}
    

    public function add_order_item($order_id,$order_number)
    {

		$this->form_validation->set_rules('product_id', 'Product', 'required|xss_clean');
		$this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('in_stock', 'In Stock', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_order_item($order_id))
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		else
		{

		}

		$order_details = $this->orders_model->get_order_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$data['content'] = $this->load->view('orders/order_item', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
    }
    public function add_supplier_items()
    {
    	$this->form_validation->set_rules('creditor_id', 'creditor', 'required|xss_clean');
		$this->form_validation->set_rules('order_product_id', 'Product', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('quantity_to_deliver', 'QTY', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('unit_price_supplier', 'Unit Price', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_supplier_items())
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);

    }

    public function print_lpo_new($order_id,$creditor_id)
	{
		$data = array('supplier_order_id'=>$order_id,'creditor_id'=>$creditor_id);

		$data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('orders/views/lpo_print', $data);
		
	}
	public function print_rfq_new($order_id,$supplier_id,$order_number)
	{
		$data = array('order_id'=>$order_id,'supplier_id'=>$supplier_id,'order_number'=>$order_number);

		$data['contacts'] = $this->site_model->get_contacts();
		
		$this->load->view('orders/views/request_for_quotation', $data);
		
	}

    public function update_order_item($order_id,$order_number,$order_item_id)
    {
    	$this->form_validation->set_rules('quantity', 'Quantity', 'numeric|required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
	    	if($this->orders_model->update_order_item($order_id,$order_item_id))
			{
				$this->session->set_userdata('success_message', 'Order Item updated successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Order Item was not updated');
			}
		}
		else
		{
			$this->session->set_userdata('success_message', 'Sorry, Please enter a number in the field');
		}
		redirect('inventory/add-order-item/'.$order_id.'/'.$order_number.'');

    }
    public function update_supplier_prices($order_id,$order_number,$order_item_id)
    {
    	$this->form_validation->set_rules('unit_price', 'Unit Price', 'numeric|required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
	    	if($this->orders_model->update_order_item_price($order_id,$order_item_id))
			{
				$this->session->set_userdata('success_message', 'Order Item updated successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Order Item was not updated');
			}
		}
		else
		{
			$this->session->set_userdata('success_message', 'Sorry, Please enter a number in the field');
		}
		redirect('inventory/add-order-item/'.$order_id.'/'.$order_number.'');

    }
    public function submit_supplier($order_id,$order_number)
    {
    	$this->form_validation->set_rules('supplier_id', 'Quantity', 'numeric|required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_supplier_to_order($order_id))
			{
				$this->session->set_userdata('success_message', 'Order Item updated successfully');
			}
			else
			{
				$this->session->set_userdata('success_message', 'Order Item updated successfully');
			}
		}
		else
		{
			$this->session->set_userdata('success_message', 'Order Item updated successfully');
		}
		redirect('inventory/add-order-item/'.$order_id.'/'.$order_number.'');
    }
	/*
	*
	*	Edit an existing order
	*	@param int $order_id
	*
	*/
	public function edit_order($order_id) 
	{
		//form validation rules
		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('user_id', 'Customer', 'required|xss_clean');
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			//update order
			if($this->orders_model->update_order($order_id))
			{
				$this->session->set_userdata('success_message', 'Order updated successfully');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update order. Please try again');
			}
		}
		
		//open the add new order
		$data['title'] = 'Edit Order';
		
		//select the order from the database
		$query = $this->orders_model->get_order($order_id);
		
		if ($query->num_rows() > 0)
		{
			$v_data['order'] = $query->row();
			$query = $this->products_model->all_products();
			$v_data['products'] = $query->result();#
			$v_data['payment_methods'] = $this->orders_model->get_payment_methods();
			
			$data['content'] = $this->load->view('orders/edit_order', $v_data, true);
		}
		
		else
		{
			$data['content'] = 'Order does not exist';
		}
		
		$this->load->view('admin/templates/general_page', $data);
	}
    
	/*
	*
	*	Add products to an order
	*	@param int $order_id
	*	@param int $product_id
	*	@param int $quantity
	*
	*/
	public function add_product($order_id, $product_id, $quantity, $price)
	{
		if($this->orders_model->add_product($order_id, $product_id, $quantity, $price))
		{
			redirect('edit-order/'.$order_id);
		}
	}
    
	/*
	*
	*	Add products to an order
	*	@param int $order_id
	*	@param int $order_item_id
	*	@param int $quantity
	*
	*/
	public function update_cart($order_id, $order_item_id, $quantity)
	{
		if($this->orders_model->update_cart($order_item_id, $quantity))
		{
			redirect('edit-order/'.$order_id);
		}
	}
    
	/*
	*
	*	Delete an existing order
	*	@param int $order_id
	*
	*/
	public function delete_order($order_id)
	{
		//delete order
		$this->db->delete('orders', array('order_id' => $order_id));
		$this->db->delete('order_item', array('order_item_id' => $order_id));
		redirect('procurement/general-orders');
	}

	public function remove_supplier_order($order_id,$order_number,$order_supplier_id)
	{
		$this->db->delete('order_supplier', array('order_supplier_id' => $order_supplier_id));
		redirect('inventory/add-order-item/'.$order_id.'/'.$order_number);
	}
    
	/*
	*
	*	Add products to an order
	*	@param int $order_item_id
	*
	*/
	public function delete_order_item($order_id, $order_item_id)
	{
		if($this->orders_model->delete_order_item($order_item_id))
		{
			redirect('edit-order/'.$order_id);
		}
	}

	public function delete_supplier_order_item($order_item_id, $order_supplier_id,$order_id)
	{
		if($this->db->delete('order_item', array('order_item_id' => $order_item_id)))
		{
			if($this->db->delete('order_supplier', array('order_supplier_id' => $order_supplier_id)))
			{
				
			}
			else{
				
			}
		}
		else{
			
		}

		redirect('procurement/supplier-invoice-detail/'.$order_id);
	}
    
	/*
	*
	*	Complete an order
	*	@param int $order_id
	*
	*/
	public function finish_order($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>7
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/general-orders');
	}

	public function finish_supplier_order($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>7
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/suppliers-invoices');
	}
	public function open_supplier_order($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>0
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/supplier-invoice-detail/'.$order_id);
	}

	public function open_transfer_order($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>0
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/order-invoice-detail/'.$order_id);
	}
	public function finish_credit_note($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>7
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/suppliers-credit-note');
	}
	public function send_order_for_correction($order_id)
	{

    	$data = array(
					'order_approval_status'=>0,
					'order_status_id'=>1
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/general-orders');
	}

    public function send_order_for_approval($order_id,$order_status= NULL)
    {
    	if($order_status == NULL)
    	{
    		$order_status = 1;
    	}
    	else
    	{
    		$order_status = $order_status;
    	}
    	
		$this->orders_model->update_order_status($order_id,$order_status);


		redirect('procurement/general-orders');
    }
	/*
	*
	*	Cancel an order
	*	@param int $order_id
	*
	*/
	public function cancel_order($order_id)
	{
		$data = array(
					'order_status'=>3
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('all-orders');
	}
    
	/*
	*
	*	Deactivate an order
	*	@param int $order_id
	*
	*/
	public function deactivate_order($order_id)
	{
		$data = array(
					'order_status'=>1
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('all-orders');
	}
	public function update_invoice_charges()
	{

		$this->form_validation->set_rules('invoice_number', 'Invoice Number', 'required|xss_clean');
		$this->form_validation->set_rules('mark_up', 'Mark up', 'xss_clean');
		$this->form_validation->set_rules('discount', 'Dicount', 'xss_clean');
		$this->form_validation->set_rules('vat', 'vat', 'xss_clean');
		$this->form_validation->set_rules('quantity_received', 'Quantity Received', 'required|xss_clean');
		$this->form_validation->set_rules('order_supplier_id', 'Order Supplier Id', 'required|xss_clean');
		$this->form_validation->set_rules('product_name', 'Product Name', 'required|xss_clean');
		$this->form_validation->set_rules('total_amount', 'Total Amount', 'required|xss_clean');
		$this->form_validation->set_rules('expiry_date', 'Expiry Date', 'required|xss_clean');
		$this->form_validation->set_rules('pack_size', 'Pack Size', 'required|xss_clean');

		$form_id = $this->input->post('form_id');

		if(!empty($form_id))
		{
			// $this->form_validation->set_rules('buying_unit_price', 'Buying Price', 'required|xss_clean');
		}
		// var_dump($_POST); die();
		//if form has been submitted
		if ($this->form_validation->run())
		{
	    	if($this->orders_model->update_invoice_charges())
			{
				$this->session->set_userdata('success_message', 'You have successfully updated the charges');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Sorry, please try again later');
			}
		}
		else
		{
			$this->session->set_userdata('success_message', 'Sorry, Please enter a number in the field');
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);


	}

public function suppliers_invoices()
	{

		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_id', 'Supplier', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			

			$store_id = $this->input->post('store_id');
			$supplier_id = $this->input->post('supplier_id');
			// var_dump($store_id); die();
			if($store_id > 0 AND $supplier_id > 0)
			{
				$order_id = $this->orders_model->add_supplier_order();
				//update order
				if($order_id > 0)
				{
					$this->session->set_userdata('success_message', 'You have successfully added an order');
				}
				
				else
				{
					$this->session->set_userdata('error_message', 'Could not update order. Please try again');
				}
			}
			else
			{
				$this->session->set_userdata('error_message', 'Could not create order. Ensure that all fields are selected');
			}
			
		}
		else
		{
			$this->session->set_userdata('error_message', 'To Create an order endure you have added all the item required');
		}
		$personnel_id = $this->session->userdata('personnel_id');
		$department_id = $this->reception_model->get_personnel_department($personnel_id);
		
		if($department_id == 0 OR $department_id == 9 OR $department_id == 1)
		{
			$department_status = '';
		}
		else
		{
			
			$department_status = ' AND orders.order_approval_status <> 7';
		}
		$where = 'orders.order_status_id = order_status.order_status_id AND orders.supplier_id > 0 AND orders.is_store <> 3 '.$department_status;
		$table = 'orders, order_status';
		//pagination

		
        $search = $this->session->userdata('supplier_order_search');      
        $where .= $search;  
		
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/suppliers-invoices';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_orders_suppliers($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		// $v_data['level_status'] = $this->orders_model->order_level_status();
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$v_data['title'] = "Suppliers Invoices";
		$data['content'] = $this->load->view('orders/all_suppliers_orders', $v_data, true);
		
		$data['title'] = 'All orders';
		
		$this->load->view('admin/templates/general_page', $data);

	}
	public function suppliers_invoice_detail($order_id)
	{

		$this->form_validation->set_rules('product_id', 'Product', 'required|xss_clean');
		// $this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('in_stock', 'In Stock', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('creditor_id', 'Supplier', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_order_item_supplier($order_id))
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		else
		{

		}

		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;


		$where = 'product.product_id = order_item.product_id AND product.product_deleted = 0 AND product.stock_take = 1 AND order_item.order_id = '.$order_id;
		$table = 'order_item, product';
		//pagination
		$segment = 4;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/supplier-invoice-detail/'.$order_id;
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_supplier_order_items($table, $where, $config["per_page"], $page);
		
		$v_data['order_item_query'] = $query;
		$v_data['page'] = $page;

		$v_data['contacts'] = $this->site_model->get_contacts();

		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$data['content'] = $this->load->view('orders/suppliers_order_items', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);

	}

	public function update_orders_date($order_id)
	{
		$this->form_validation->set_rules('supplier_invoice_date', 'Date', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_invoice_number', 'Invoice number', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{

			$data = array(
					'supplier_invoice_date'=>$this->input->post('supplier_invoice_date'),
					'supplier_invoice_number'=>$this->input->post('supplier_invoice_number'),
				);
				
			$this->db->where('order_id = '.$order_id);
			if($this->db->update('orders', $data))
			{
				$this->session->set_userdata('success_message', 'Successfully updated invoice information');
			}
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong. please try again');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Something went wrong. please try again');
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);
	}
	public function get_stock_quantity($product_id)
	{

		$total_quantity = $this->input->post('quantity');
		// $quantity = $this->inventory_management_model->get_product_quantity($product_id);

		
		// $inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		// $sales = $this->inventory_management_model->get_drug_units_sold($inventory_start_date, $product_id,$search_start_date= NULL,$search_end_date = NULL, $branch_code=NULL);

		// $procurred = $this->inventory_management_model->item_proccured($inventory_start_date, $product_id,$store_id=NULL,$search_start_date= NULL,$search_end_date = NULL);

		// $deductions = $this->inventory_management_model->item_deductions($inventory_start_date, $product_id,$store_id=NULL,$search_start_date= NULL,$search_end_date = NULL);
		// $purchases = $this->inventory_management_model->item_purchases($inventory_start_date, $product_id,$store_id=NULL,$search_start_date=NULL,$search_end_date=NULL);
       

		// if(!empty($total_quantity))
		// {
		// 	$total_quantity = $total_quantity;
		// }
		// else
		// {
		// 	$total_quantity = 0;
		// }
  //       $in_stock = ($quantity + $purchases + $procurred) - $sales - $deductions;

        $inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
    	$parent_store_qty = $this->inventory_management_model->parent_store_stock($inventory_start_date,$product_id,5);
    	$child_store_stock = $this->inventory_management_model->child_store_stock($inventory_start_date, $product_id,6);

        $response['message'] = 'success';
		$response['in_stock'] = $parent_store_qty;
		$response['total_quantity'] = $total_quantity + $parent_store_qty + $child_store_stock;

      echo json_encode($response);
       
	}

	public function delete_order_supply($order_id)
        	
        {
        		//delete category image
        		$query = $this->assets_model->get_order_supply($order_id);
        		
        		if ($query->num_rows() > 0)
        		{
        			$result = $query->result();
        			
        		}
        		$this->assets_model->delete_order_supply($order_id);
        		$this->session->set_userdata('success_message', 'Supply has been deleted');
        		redirect('procurement/suppliers-invoices');
        }


    public function product_supplies()
	{
		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0";
		$table = 'order_item,order_supplier,product,orders';


		$supplier_search = $this->session->userdata('product_purchased_search');
		
		if(!empty($supplier_search))
		{
			$where .= $supplier_search;
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/product-supplies';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_supplied_items($table, $where, $config["per_page"], $page);
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['title'] = 'All Supplies';
		//$v_data['child_suppliers'] = $this->suppliers_model->all_child_suppliers();
		$data['content'] = $this->load->view('orders/supplied_orders', $v_data, true);
		$data['title'] = 'All suppliers';
		
		$this->load->view('admin/templates/general_page', $data);
	}


	public function search_inventory_product()
	{
		$stocked = $this->input->post('stocked');
		$product_code = $this->input->post('product_code');
		$product_name = $this->input->post('product_name');
		$brand_id = $this->input->post('brand_id');
		$generic_id = $this->input->post('generic_id');
		$category_id = $this->input->post('category_id');
		$store_id = $this->input->post('store_id');
		$start_date = $this->input->post('date_from');
		$end_date = $this->input->post('date_end');
		
		if($stocked == 1)
		{
			$stocked = ' AND product.quantity > 0 ';
		}
		else if($stocked == 2)
		{
			$stocked = ' AND (product.quantity = 0 OR product.quantity = \'\') ';
		}
		else
		{
			$stocked = '';
		}
		
		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}
		if(!empty($product_code))
		{
			$product_code = ' AND product.product_code = \''.$product_code.'\'';
		}
		else
		{
			$product_code = '';
		}
		if(!empty($generic_id))
		{
			$generic_id = ' AND product.generic_id = '.$generic_id;
		}
		else
		{
			$generic_id = '';
		}
		
		if(!empty($brand_id))
		{
			$brand_id = ' AND product.brand_id = '.$brand_id;
		}
		else
		{
			$brand_id = '';
		}
		
		if(!empty($category_id))
		{
			$category_id = ' AND product.category_id = '.$category_id;
		}
		else
		{
			$category_id = '';
		}
		if(!empty($store_id))
		{
			$store_id = ' AND store.store_id = '.$store_id;
		}
		else
		{
			$store_id = '';
		}
		
		$search = $product_name.$generic_id.$brand_id.$category_id.$store_id.$product_code.$stocked;
		$this->session->set_userdata('product_inventory_search', $search);
		$this->session->set_userdata('inventory_search_start_date',$start_date);
		$this->session->set_userdata('inventory_search_end_date',$end_date);
		
		$this->index();
	}	

	public function goods_received_notes($order_id)
	{

		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();


		$where = 'product.product_id = order_item.product_id AND order_item.order_id = '.$order_id;
		$table = 'order_item, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('suppliers/print_goods_received_note', $v_data);

	}
	public function print_credit_note($order_id)
	{

		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();


		


		$where = 'product.product_id = order_item.product_id AND order_item.order_id = '.$order_id;
		$table = 'order_item, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('suppliers/print_credit_note', $v_data);

	}


	public function drug_transfers()
	{

		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_id', 'Supplier', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			$order_id = $this->orders_model->add_transfer_order();
			//update order
			if($order_id > 0)
			{
				$this->session->set_userdata('success_message', 'You have successfully added an order');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update order. Please try again');
			}
		}

		$where = 'orders.order_status_id = order_status.order_status_id  AND orders.is_store = 2 ';
		$table = 'orders, order_status';


		 $search = $this->session->userdata('transfer_order_search');      
        $where .= $search;  
		//pagination
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/drug-transfers';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_orders_suppliers($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		// $v_data['level_status'] = $this->orders_model->order_level_status();
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$v_data['title'] = "Suppliers Invoices";
		$data['content'] = $this->load->view('orders/drug_transfers', $v_data, true);
		
		$data['title'] = 'All orders';
		
		$this->load->view('admin/templates/general_page', $data);

	}


	public function order_invoice_detail($order_id)
	{

		$this->form_validation->set_rules('product_id', 'Product', 'required|xss_clean');
		// $this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('in_stock', 'In Stock', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('creditor_id', 'Supplier', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_order_item_supplied($order_id))
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		else
		{

		}

		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$created = $value->created;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['created'] = $created;


		$where = 'product.product_id = product_deductions.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = '.$order_id;
		$table = 'product_deductions, product,orders';
		//pagination
		$segment = 4;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/order-invoice-detail/'.$order_id;
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_order_order_items($table, $where, $config["per_page"], $page);
		
		$v_data['order_item_query'] = $query;
		$v_data['page'] = $page;

		$v_data['contacts'] = $this->site_model->get_contacts();

		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$data['content'] = $this->load->view('orders/order_item_views', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);

	}

	public function award_order_products($product_deductions_id,$product_id,$order_id)
    {
    	// $parent_store_qty = $this->inventory_management_model->get_parent_store_inventory_quantity($parent_id,$product_id);

    	$this->form_validation->set_rules('quantity_given', 'Quantity', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
		
				$quantity = $this->input->post('quantity_given');
				$pack_size = $this->input->post('pack_size');
				$quantity = $quantity;
		    	$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
		    	$parent_store_qty = $this->inventory_management_model->parent_stock_store($inventory_start_date,$product_id,6);
		   
		    	if($parent_store_qty >= $quantity)
		    	{
		    		$data = array('quantity_given' => $quantity,'pack_size'=>$pack_size,'given_by'=>$this->session->userdata('personnel_id'),'product_deductions_status'=>1);
			    	$this->db->where('product_deductions_id ='.$product_deductions_id);
					$this->db->update('product_deductions',$data);
					$data['result']= "You've successfully awarded the store ".$quantity."";
					$this->session->set_userdata('success_message', "You've successfully awarded the store ".$quantity."");

		    	}
		    	else
		    	{
		    		$data['result']= "Sorry you cannot award the quantity you have placed ".$parent_store_qty;
		    		$this->session->set_userdata('error_message', "Sorry you cannot award the quantity you have placed".$parent_store_qty."");
		    	}
		   
		}
		else
		{
			$this->session->set_userdata('error_message', 'Sorry could not award the product');
		}
		
		redirect('procurement/order-invoice-detail/'.$order_id);
    }

    public function delete_transfer_order_item($product_deductions_id,$order_id)
	{
		if($this->db->delete('product_deductions', array('product_deductions_id' => $product_deductions_id)))
		{
			$this->session->set_userdata('success_message', "You've successfully removed the item");	
		}
		else
		{
			$this->session->set_userdata('success_message', "Sorry please try again");
		}

		redirect('procurement/order-invoice-detail/'.$order_id);
	}

	public function finish_transfer_order($order_id)
	{
		$data = array(
					'order_status_id'=>2,
					'order_approval_status'=>7
				);
				
		$this->db->where('order_id = '.$order_id);
		$this->db->update('orders', $data);
		
		redirect('procurement/drug-transfers');
	}
	public function print_supplier_invoice($order_id)
	{

		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();


		$where = 'product.product_id = product_deductions.product_id AND product_deductions.order_id = '.$order_id;
		$table = 'product_deductions, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('suppliers/print_goods_received_note', $v_data);

	}

	public function goods_transfered($order_id)
	{

		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();


		$where = 'product.product_id = product_deductions.product_id AND product_deductions.order_id = '.$order_id;
		$table = 'product_deductions, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('suppliers/print_transfered_note', $v_data);

	}


	public function print_supplier_credit_note($order_id)
	{

		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_id'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();


		$where = 'product.product_id = product_deductions.product_id AND product_deductions.order_id = '.$order_id;
		$table = 'product_deductions, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('suppliers/print_transfered_note', $v_data);

	}
	public function search_products_purchased()
	{
		$supplier_id = $this->input->post('supplier_id');
		$product_name = $this->input->post('product_name');
		$invoice_number = $this->input->post('invoice_number');
	
		// var_dump($_POST); die();
		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}
		if(!empty($invoice_number))
		{
			$invoice_number = ' AND order_supplier.invoice_number = \''.$invoice_number.'\'';
		}
		else
		{
			$invoice_number = '';
		}
		if(!empty($supplier_id))
		{
			$supplier_id = ' AND orders.supplier_id = '.$supplier_id;
		}
		else
		{
			$supplier_id = '';
		}
		
		
		
		$search = $product_name.$supplier_id.$invoice_number;
		$this->session->set_userdata('product_purchased_search', $search);

		redirect('procurement/product-supplies');
	}
	public function close_product_purchased_search()
	{
		$this->session->unset_userdata('product_purchased_search');
		redirect('procurement/product-supplies');
	}



	public function credit_notes()
	{

		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_id', 'Supplier', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			$order_id = $this->orders_model->add_credit_note_order();
			//update order
			if($order_id > 0)
			{
				$this->session->set_userdata('success_message', 'You have successfully added an supplier credit note');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update order. Please try again');
			}
		}

		$where = 'orders.order_status_id = order_status.order_status_id  AND orders.is_store = 3 ';
		$table = 'orders, order_status';
		//pagination
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/suppliers-credit-note';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_orders_suppliers($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		// $v_data['level_status'] = $this->orders_model->order_level_status();
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$v_data['title'] = "Credit Notes";
		$data['content'] = $this->load->view('orders/credit_notes', $v_data, true);
		
		$data['title'] = 'ALL Credit Notes';
		
		$this->load->view('admin/templates/general_page', $data);

	}

	public function credit_note_detail($order_id)
	{
		$this->form_validation->set_rules('product_id', 'Product', 'required|xss_clean');
		// $this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('in_stock', 'In Stock', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('creditor_id', 'Supplier', 'required|numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_order_item_supplier($order_id))
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		else
		{

		}

		$order_details = $this->orders_model->get_order_supplier_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
				$creditor_name = $value->creditor_name;
				$creditor_email = $value->creditor_email;
				$creditor_phone = $value->creditor_phone;
				$created = $value->created;
				$creditor_id = $value->creditor_id;
				$creditor_location = $value->creditor_location;
				$supplier_invoice_number = $value->supplier_invoice_number;
				$supplier_invoice_date = $value->supplier_invoice_date;
				$reference_number = $value->reference_number;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['creditor_name'] = $creditor_name;
		$v_data['store_name'] = $store_name;
		$v_data['creditor_email'] = $creditor_email;
		$v_data['creditor_phone'] = $creditor_phone;
		$v_data['creditor_id_value'] = $creditor_id;
		$v_data['creditor_location'] = $creditor_location;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_number'] = $supplier_invoice_number;
		$v_data['supplier_invoice_date'] = $supplier_invoice_date;
		$v_data['reference_number'] = $reference_number;
		$v_data['created'] = $created;


		// $where = 'product.product_id = product_deductions.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = '.$order_id;
		// $table = 'product_deductions, product,orders';
		
		$where = 'product.product_id = order_item.product_id AND order_item.order_id = '.$order_id;
		$table = 'order_item, product';
		//pagination
		$segment = 4;
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/credit-note-detail/'.$order_id;
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_supplier_order_items($table, $where, $config["per_page"], $page);
		// var_dump($query); die();
		$v_data['order_item_query'] = $query;
		$v_data['page'] = $page;

		$v_data['contacts'] = $this->site_model->get_contacts();

		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$data['content'] = $this->load->view('orders/credit_note_detail', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);

	}



	public function update_notes_date($order_id)
	{
		$this->form_validation->set_rules('supplier_invoice_date', 'Date', 'required|xss_clean');
		$this->form_validation->set_rules('supplier_invoice_number', 'Invoice number', 'required|xss_clean');
		$this->form_validation->set_rules('reference_number', 'Reference Number', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{

			$data = array(
					'supplier_invoice_date'=>$this->input->post('supplier_invoice_date'),
					'supplier_invoice_number'=>$this->input->post('supplier_invoice_number'),
					'reference_number'=>$this->input->post('reference_number'),
				);
				
			$this->db->where('order_id = '.$order_id);
			if($this->db->update('orders', $data))
			{
				$this->session->set_userdata('success_message', 'Successfully updated invoice information');
			}
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong. please try again');
			}
		}
		else
		{
			$this->session->set_userdata('error_message', 'Something went wrong. please try again');
		}
		$redirect_url = $this->input->post('redirect_url');
		redirect($redirect_url);
	}
	public function update_supplier_invoices()
	{
		$this->db->where('order_supplier_id > 0 AND less_vat IS NULL');
		$query = $this->db->get('order_supplier');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$order_supplier_id = $value->order_supplier_id;
				$unit_price = $value->unit_price;
				$pack_size = $value->pack_size;
				$quantity_received = $value->quantity_received;
				$discount = $value->discount;
				$vat = $value->vat;


				if(empty($pack_size))
				{
					$purchase_unit = 0;
				}
				else
				{
					$purchase_unit = ($unit_price/$pack_size);
				}

				$total_items_price = ($quantity_received) * $unit_price;
				if($discount > 0)
				 {

				 	$current_price = $total_items_price - (($discount/100)*$total_items_price);
				 }
				 else
				 {
				 	$current_price = $total_items_price;
				 }


				 if($vat > 0)
				 {

					$vat_capture = (0.16 *$current_price);
					// $current_price = $current_price+$vat_capture;
					// $total_vat += $vat_capture;
				 }
				
				 if($order_supplier_id == 9243)
				 {
				 	 // var_dump($current_price); die();
				 }

				
				$gross_amount = $purchase_unit * ($quantity_received*$pack_size);

				$less_vat = $current_price;//$gross_amount * ((100 - $discount)/100);
				$gross_amount = $less_vat * ((100+$vat)/100);
				// var_dump($gross_amount); die();

				$update_array['total_amount'] = $gross_amount;
				$update_array['less_vat'] = $less_vat;
				$this->db->where('order_supplier_id',$order_supplier_id);
				$this->db->update('order_supplier',$update_array);
			}
		}
	}

	 public function search_supplier_orders()    	
	{

		$invoice_number = $this->input->post('invoice_number');
		$supplier_id = $this->input->post('supplier_id');


		if(!empty($invoice_number))
		{
			$invoice_number = ' AND orders.supplier_invoice_number LIKE \'%'.mysql_real_escape_string($invoice_number).'%\' ';
		}
		// var_dump($supplier_id); die();
	    if(!empty($supplier_id))
        {
            $supplier_id =' AND orders.supplier_id = '.$supplier_id;
        }
        else
        {
        	$supplier_id = '';
        }
		
		
		$search = $invoice_number.$supplier_id;

        // var_dump($search); die();
		$this->session->set_userdata('supplier_order_search', $search);
		
		redirect('procurement/suppliers-invoices');
		
	}
	public function close_supplier_order_search()
	{
		$this->session->unset_userdata('supplier_order_search');
		redirect('procurement/suppliers-invoices');
	}



	 public function search_transfer_orders()    	
	{

		$invoice_number = $this->input->post('invoice_number');
		$supplier_id = $this->input->post('supplier_id');


		if(!empty($invoice_number))
		{
			$invoice_number = ' AND orders.order_number LIKE \'%'.mysql_real_escape_string($invoice_number).'%\' ';
		}
		// var_dump($supplier_id); die();
	    if(!empty($supplier_id))
        {
            $supplier_id =' AND orders.supplier_id = '.$supplier_id;
        }
        else
        {
        	$supplier_id = '';
        }
		
		
		$search = $invoice_number.$supplier_id;

        // var_dump($search); die();
		$this->session->set_userdata('transfer_order_search', $search);
		
		redirect('procurement/drug-transfers');
		
	}
	public function close_transfer_order_search()
	{
		$this->session->unset_userdata('transfer_order_search');
		redirect('procurement/drug-transfers');
	}


	// general orders function 

	public function general_orders() 
	{
		// get my approval roles

		$where = 'orders.order_status_id = order_status.order_status_id AND orders.supplier_id IS NULL';
		$table = 'orders, order_status';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/general-orders';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = 3;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_orders($table, $where, $config["per_page"], $page);
		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		// $v_data['level_status'] = $this->orders_model->order_level_status();
		$v_data['title'] = "All Orders";
		$data['content'] = $this->load->view('general_orders/all_general_orders', $v_data, true);
		
		$data['title'] = 'All orders';
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function add_general_order_item($order_id,$order_number)
    {

		$this->form_validation->set_rules('product_id', 'Product', 'required|xss_clean');
		$this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|xss_clean');
		// $this->form_validation->set_rules('in_stock', 'In Stock', 'numeric|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			if($this->orders_model->add_order_item($order_id))
			{
				$this->session->set_userdata('success_message', 'Order created successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Something went wrong, please try again');
			}
		}
		else
		{

		}

		$order_details = $this->orders_model->get_order_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$store_parent = $this->inventory_management_model->get_parent_store($store_id);

		if($store_parent == 0)
		{
			$store_parent = $store_id;
		}
		$v_data['products_query'] = $this->products_model->all_products($store_parent);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$data['content'] = $this->load->view('general_orders/general_order_item', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
    }

    /*
	*
	*	Add a new order
	*
	*/
	public function add_general_order() 
	{
		//form validation rules
		$this->form_validation->set_rules('order_instructions', 'Order Instructions', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
			$order_id = $this->orders_model->add_order();
			//update order
			if($order_id > 0)
			{
				redirect('procurement/general-orders');
			}
			
			else
			{
				$this->session->set_userdata('error_message', 'Could not update order. Please try again');
			}
		}
		
		// $store_priviledges = $this->inventory_management_model->get_assigned_stores();
		// $v_data['store_priviledges'] =  $store_priviledges;
		//open the add new order
		$data['title'] = 'Add Order';
		$v_data['title'] = 'Add Order';
		$v_data['order_status_query'] = $this->orders_model->get_order_status();

		$data['content'] = $this->load->view('general_orders/add_general_order', $v_data, true);
		
		$this->load->view('admin/templates/general_page', $data);
	}

	public function delete_general_order_item($order_item_id,$order_id,$order_number)
	{
		if($this->db->delete('order_item', array('order_item_id' => $order_item_id)))
		{
			
		}
		else{
			
		}

		redirect('inventory/add-general-order-item/'.$order_id.'/'.$order_number);
	}

	public function update_general_order_item($order_id,$order_number,$order_item_id)
    {
    	$this->form_validation->set_rules('quantity', 'Quantity', 'numeric|required|xss_clean');
		
		//if form has been submitted
		if ($this->form_validation->run())
		{
	    	if($this->orders_model->update_order_item($order_id,$order_item_id))
			{
				$this->session->set_userdata('success_message', 'Order Item updated successfully');
			}	
			else
			{
				$this->session->set_userdata('error_message', 'Order Item was not updated');
			}
		}
		else
		{
			$this->session->set_userdata('success_message', 'Sorry, Please enter a number in the field');
		}
		redirect('inventory/add-general-order-item/'.$order_id.'/'.$order_number.'');

    }

    public function send_general_order_for_approval($order_id,$order_status= NULL)
    {
    	if($order_status == NULL)
    	{
    		$order_status = 1;
    	}
    	else
    	{
    		$order_status = $order_status;
    	}
    	
		$this->orders_model->update_order_status($order_id,$order_status);


		redirect('procurement/general-orders');
    }

    public function print_general_order($order_id)
	{

		// var_dump($order_id); die();
		$v_data['contacts'] = $this->site_model->get_contacts();
		$order_details = $this->orders_model->get_order_details($order_id);
		$store_name = '';
		if($order_details->num_rows() > 0)
		{
			foreach ($order_details->result() as $key => $value) {
				# code...
				$store_id = $value->store_id;
				$store_name = $value->store_name;
				$order_number = $value->order_number;
			}
		}

		$v_data['title'] = 'Add Order Item to '.$order_number;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['order_status_query'] = $this->orders_model->get_order_status();
		$v_data['products_query'] = $this->products_model->all_products($store_id);
		$v_data['order_number'] = $order_number;
		$v_data['order_id'] = $order_id;
		$v_data['store_name'] = $store_name;
		$v_data['order_item_query'] = $this->orders_model->get_order_items($order_id);
		$v_data['order_suppliers'] = $this->orders_model->get_order_suppliers($order_id);
		$v_data['suppliers_query'] = $this->suppliers_model->all_suppliers();
		$v_data['order_number'] = $order_number;


		$where = 'product.product_id = order_item.product_id AND order_item.order_id = '.$order_id;
		$table = 'order_item, product';

		$v_data['query'] = $this->orders_model->get_creditors_detail_summary($where,$table);

		$this->load->view('general_orders/print_order_items', $v_data);

	}

	public function product_expiries()
	{
		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0 AND order_supplier.expiry_recorded = 1";
		$table = 'order_item,order_supplier,product,orders';


		$supplier_search = $this->session->userdata('product_purchased_search');
		
		if(!empty($supplier_search))
		{
			$where .= $supplier_search;
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'procurement/product-expiries';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 20;
		$config['num_links'] = 5;
		
		
		$config['full_tag_open'] = '<ul class="pagination pull-right">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Next';
		$config['next_tag_close'] = '</span>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Prev';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		
		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->orders_model->get_all_expiry_items($table, $where, $config["per_page"], $page);
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['title'] = 'All Supplies';
		//$v_data['child_suppliers'] = $this->suppliers_model->all_child_suppliers();
		$data['content'] = $this->load->view('orders/supplied_expiries', $v_data, true);
		$data['title'] = 'All suppliers';
		
		$this->load->view('admin/templates/general_page', $data);
	}

	

}
?>