<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_management  extends MX_Controller
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('inventory_management_model');
		$this->load->model('products_model');
		$this->load->model('reception/reception_model');
		$this->load->model('pharmacy/pharmacy_model');
		$this->load->model('nurse/nurse_model');
		$this->load->model('accounts/accounts_model');
		$this->load->model('admin/users_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('reception/database');
		$this->load->model('administration/personnel_model');
		$this->load->model('inventory/categories_model');
		$this->load->model('inventory/stores_model');
		// $this->load->model('reception/web_service');


		$this->csv_path = realpath(APPPATH . '../assets/csv');

		$this->load->model('auth/auth_model');
		// if(!$this->auth_model->check_login())
		// {
		// 	redirect('login');
		// }
	}

	public function index()
	{
		$page = explode("/",uri_string());

		$name = strtolower($page[1]);


		// if($name == 'products')
		// {
		// 	$added_items = 'AND category.category_name <> "Drug"';
		// }
		// else
		// {
			$added_items = '';
		// }
		// echo $added_items; die();
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		//var_dump($store_priviledges);die();
		$v_data['store_priviledges'] =  $store_priviledges;
		$store_id = 0;
		$addition = '';

		//$constant  = ' AND product.store_id = store.store_id AND ';
		$constant  = ' AND store_product.owning_store_id = store.store_id  AND store.store_status = 1 AND store.store_deleted = 0';
		$table = '';
		$personnel_id = $this->session->userdata('personnel_id');
		$department_id = $this->reception_model->get_personnel_department($personnel_id);
		if($store_priviledges->num_rows() > 0 AND $department_id <> 1)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			$constant .= ' AND (';
			if($number_rows > 1)
			{
				$v_data['type'] = 3;
			}

			else
			{
				$v_data['type'] = 2;
			}

			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				$store_id = $key->store_id;
				$constant .= ' store_product.owning_store_id ='.$store_id;
				$count++;
				if($count < $number_rows)
				{
					$constant .= ' OR ';
				}
			}
			$constant .= ' )';
		}
		else
		{
			$v_data['type'] = 4;
			$table = '';
			$constant = ' AND  store_product.owning_store_id = store.store_id  AND store.store_status = 1 AND store.store_deleted = 0';
			$addition = '';
		}


		// var_dump($constant); die();


		$is_admin = $this->reception_model->check_if_admin($personnel_id,1);

		// var_dump($department_id);die();
		if($is_admin OR $personnel_id == 0 OR $department_id == 1)
		{
			$where = 'product.category_id = category.category_id '.$constant.' AND product.product_deleted = 0 AND product.product_id = store_product.product_id '.$added_items;
			$table = 'product, category, store,store_product';
		}
		else
		{
			$where = 'product.category_id = category.category_id '.$constant.'  AND product.product_deleted = 0  AND product.product_id = store_product.product_id '.$added_items;
			$table = 'product, category, store, store_product';

		}
		$v_data['department_id'] = $department_id;
		$v_data['is_admin'] = $is_admin;
		$v_data['personnel_id_main'] = $personnel_id;
		// echo $table;die();


		$product_inventory_search = $this->session->userdata('product_inventory_search');

		if(!empty($product_inventory_search))
		{
			$where .= $product_inventory_search;
		}

		// echo $where;die();
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'inventory/products';
		$config['total_rows'] = $this->users_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 10;
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
		$query = $this->products_model->get_all_products($table, $where, $config["per_page"], $page);
		$v_data['all_generics'] = '';//$this->inventory_management_model->get_all_generics();
		$v_data['all_brands'] = '';//$this->inventory_management_model->get_all_brands();
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();


		$v_data['store_id'] = $store_id;
		$v_data['page_name'] = $name;
		if ($query->num_rows() > 0)
		{
			$v_data['query'] = $query;
			$v_data['page'] = $page;
			$v_data['title'] = 'All Products';

			$v_data['all_categories'] = $this->categories_model->all_categories();

			$data['content'] = $this->load->view('products/all_products', $v_data, true);
		}

		else
		{
			$search = $this->session->userdata('product_search');
			$search_result = '';
			if(!empty($search))
			{
				$search_result = '<a href="'.site_url().'inventory/close-product-search" class="btn btn-success">Close Search</a>';
			}
			$v_data['title'] = 'All Products';
			$v_data['query'] = $query;
			$v_data['all_categories'] = $this->categories_model->all_categories();
			$data['content'] = $this->load->view('products/all_products', $v_data, true);
		}
		$data['title'] = 'All Products';

		$this->load->view('admin/templates/general_page', $data);


	}



	/*
	*	Add product
	*
	*/
	public function add_product($product_id = NULL)
	{
		//form validation rules
		$this->form_validation->set_rules('product_name', 'product Name', 'required|xss_clean');
		$this->form_validation->set_rules('products_pack_size', 'Pack Size', 'numeric|xss_clean');
		$this->form_validation->set_rules('quantity', 'Opening Quantity', 'numeric|xss_clean');
		$this->form_validation->set_rules('products_unitprice', 'Unit Price', 'numeric|xss_clean');
		$this->form_validation->set_rules('product_unitprice_insurance', 'Unit Price', 'numeric|xss_clean');
		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->save_product())
			{
				$this->session->userdata('success_message', 'Product has been added successfully');
				redirect('inventory/products');
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to add product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}

		//load the interface
		$data['title'] = 'Add product';
		$v_data['product_id'] = $product_id;
		$v_data['title'] = 'Add product';
		$v_data['all_stores'] = $this->stores_model->all_parent_stores();
		$v_data['all_categories'] = $this->categories_model->all_categories();
		$v_data['drug_types'] = $this->pharmacy_model->get_drug_forms();
		$v_data['drug_brands'] = $this->pharmacy_model->get_drug_brands();
		$v_data['drug_classes'] = $this->pharmacy_model->get_drug_classes();
		$v_data['drug_generics'] = $this->pharmacy_model->get_drug_generics();
		$v_data['drug_dose_units'] = $this->pharmacy_model->get_drug_dose_units();
		$v_data['admin_routes'] = $this->pharmacy_model->get_admin_route();
		$v_data['consumption'] = $this->pharmacy_model->get_consumption();
		$data['content'] = $this->load->view('products/add_product', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}

	/*
	*	Edit product
	*
	*/
	public function edit_product($products_id, $module = NULL)
	{
		//form validation rules
		$this->form_validation->set_rules('product_name', 'product Name', 'required|xss_clean');
		$this->form_validation->set_rules('product_pack_size', 'Pack Size', 'numeric|xss_clean');
		$this->form_validation->set_rules('quantity', 'Opening Quantity', 'numeric|xss_clean');
		$this->form_validation->set_rules('product_unitprice', 'Unit Price', 'numeric|xss_clean');
		$this->form_validation->set_rules('batch_no', 'Batch Number', 'numeric|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->edit_product($products_id))
			{
				$this->session->userdata('success_message', 'product has been editted successfully');

				//back to pharmacy
				if($module == 'a')
				{
					redirect('pharmacy-setup/inventory');
				}
				else
				{
					redirect('inventory/products');
				}
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to edit product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}

		//load the interface
		$data['title'] = 'Edit product';
		$v_data['title'] = 'Edit product';
		$v_data['module'] = $module;
		$product_details = $this->inventory_management_model->get_product_details($products_id);
		//var_dump($product_details);die();
		$v_data['product'] = $product_details;
		$v_data['products_id'] = $products_id;
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();
		$v_data['all_stores'] = $this->stores_model->all_parent_stores();
		$v_data['all_categories'] = $this->categories_model->all_categories();
		$v_data['drug_types'] = $this->pharmacy_model->get_drug_forms();
		$v_data['drug_brands'] = $this->pharmacy_model->get_drug_brands();
		$v_data['drug_classes'] = $this->pharmacy_model->get_drug_classes();
		$v_data['drug_generics'] = $this->pharmacy_model->get_drug_generics();
		$v_data['drug_dose_units'] = $this->pharmacy_model->get_drug_dose_units();
		$v_data['admin_routes'] = $this->pharmacy_model->get_admin_route();
		$v_data['consumption'] = $this->pharmacy_model->get_consumption();

		$data['content'] = $this->load->view('products/edit_product', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}

	public function manage_product($product_id)
	{
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;

		$data['content'] = $this->load->view('store_management', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}
	public function product_purchases($product_id, $product_deductions_id = NULL, $store_parent = NULL)
	{
		//form validation rules
		$this->form_validation->set_rules('purchase_quantity', 'Purchase Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('purchase_pack_size', 'Pack Size', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('expiry_date', 'Expiry Date', 'xss_clean');
		$this->form_validation->set_rules('purchase_date', 'Expiry Date', 'xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{
			if($this->inventory_management_model->purchase_product($product_id, $product_deductions_id))
			{
				$this->session->userdata('success_message', 'product has been purchased successfully');

				if($store_parent == NULL)
				{
					redirect('inventory/product-purchases/'.$product_id);
				}

				else
				{
					$result['message'] = 'success';
					$result['result'] = 'Purchase added successfully';
					echo json_encode($result);
					die();
				}
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to purchase product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();

			if($store_parent != NULL)
			{
				$result['message'] = 'fail';
				$result['result'] = $v_data['validation_errors'];
				echo json_encode($result);
				die();
			}
		}

		//load the interface
		$data['title'] = 'Buy product';
		$product_details = $this->inventory_management_model->get_product_details($product_id);


		$v_data['title'] = 'Buy '.$product_details[0]->product_name;
		$v_data['product_id'] = $product_id;
		$data['content'] = $this->load->view('products/buy_product', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}

	public function edit_product_purchase($purchase_id, $product_id)
	{
		//form validation rules
		$this->form_validation->set_rules('purchase_quantity', 'Purchase Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('purchase_pack_size', 'Pack Size', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('expiry_date', 'Expiry Date', 'xss_clean');
		$this->form_validation->set_rules('purchase_date', 'Purchase Date', 'required|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->edit_product_purchase($purchase_id))
			{
				$this->session->userdata('success_message', 'product has been purchased successfully');
				redirect('inventory/product-purchases/'.$product_id);
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to purchase product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}

		//load the interface
		$data['title'] = 'Edit Purchase';
		$data['sidebar'] = 'pharmacy_sidebar';
		$product_details = $this->inventory_management_model->get_product_details($product_id);
		$purchase_details = $this->inventory_management_model->get_purchase_details($purchase_id);

		$v_data['title'] = 'Edit '.$product_details[0]->product_name.' Purchase';
		$v_data['product_id'] = $product_id;
		$v_data['purchase_details'] = $purchase_details->row();
		$data['content'] = $this->load->view('products/edit_product_purchase', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}

	public function all_product_purchases($product_id)
	{
		$segment = 4;
		$order = 'product_purchase.purchase_date';
		$where = 'product_purchase.product_id = '.$product_id;

		$product_search = $this->session->userdata('product_purchases_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		$table = 'product_purchase';

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/product-purchases/'.$product_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->inventory_management_model->get_product_purchases($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['product_id'] = $product_id;

		$data['title'] = 'product List';
		$v_data['title'] = 'product List';
		$data['sidebar'] = 'pharmacy_sidebar';
		$product_details = $this->inventory_management_model->get_product_details($product_id);


			$v_data['title'] = $product_details[0]->product_name.' Purchases';
			$data['content'] = $this->load->view('products/product_purchases', $v_data, true);


		$this->load->view('admin/templates/general_page', $data);
	}

	public function return_product($product_id,$store_id)
	{
		//form validation rules
		$this->form_validation->set_rules('product_deduction_quantity', 'deduct Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_deduction_pack_size', 'Pack Size', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'required|xss_clean');


		//if form conatins valid data
		if ($this->form_validation->run())
		{
			// var_dump($_POST); die();
			$to_store_id = $this->input->post('store_id');

			if($to_store_id > 0)
			{
				if($this->inventory_management_model->return_product($product_id,$store_id))
				{
					$this->session->set_userdata('success_message', 'product has been deducted successfully');

				}

				else
				{
					$this->session->set_userdata('error_message', 'Unable to deduct product. Please try again');
				}
			}
			else
			{
				$this->session->set_userdata('error_message', 'Unable to deduct product. Please select the store you are returning the drug');
				redirect('inventory/return-product/'.$product_id.'/'.$store_id);
			}
			redirect('inventory/products');
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
			$this->session->set_userdata('error_message', $v_data['validation_errors']);
		}

		//load the interface
		$data['title'] = 'Return Product';
		$data['sidebar'] = 'pharmacy_sidebar';
		$product_details = $this->inventory_management_model->get_inventory_product_details($product_id);
		//var_dump($product_details);die();
		if($product_details->num_rows() > 0)
		{
			$row = $product_details->row();
			$v_data['title'] = 'Return '.$row->product_name;
			$v_data['store_id'] = $row->store_id;
			$v_data['product_id'] = $product_id;
			$v_data['container_types'] = $this->inventory_management_model->get_container_types();
			$data['content'] = $this->load->view('return_product', $v_data, true);
		}

		else
		{
			$data['content'] = 'Could not find product';
		}
		$this->load->view('admin/templates/general_page', $data);
	}
	public function deduct_product($product_id,$store_id)
	{
		//form validation rules
		$this->form_validation->set_rules('product_deduction_quantity', 'deduct Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_deduction_pack_size', 'Pack Size', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('deduction_date', 'Deduction Date', 'required|xss_clean');
		// var_dump($_POST); die();
		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->deduct_product($product_id,$store_id))
			{
				$this->session->userdata('success_message', 'product has been deducted successfully');
				redirect('inventory/deduction-product/'.$product_id.'/'.$store_id);
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to deduct product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}

		//load the interface
		$data['title'] = 'Deduct product';
		$data['sidebar'] = 'pharmacy_sidebar';
		$product_details = $this->inventory_management_model->get_inventory_product_details($product_id);
		//var_dump($product_details);die();
		if($product_details->num_rows() > 0)
		{
			$row = $product_details->row();
			$v_data['title'] = 'Deduct '.$row->product_name;
			$v_data['store_id'] = $row->store_id;
			$v_data['product_id'] = $product_id;
			$v_data['container_types'] = $this->inventory_management_model->get_container_types();
			$data['content'] = $this->load->view('deduct_product', $v_data, true);
		}

		else
		{
			$data['content'] = 'Could not find product';
		}
		$this->load->view('admin/templates/general_page', $data);
	}

	public function edit_product_deduction($product_deduction_id, $product_id,$store_id)
	{
		//form validation rules
		$this->form_validation->set_rules('product_deductions_quantity', 'Deduction Quantity', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('product_deductions_pack_size', 'Pack Size', 'required|numeric|xss_clean');

		$this->form_validation->set_rules('deduction_date', 'Pack Size', 'required|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->edit_product_deduction($product_deduction_id,$product_id))
			{
				$this->session->userdata('success_message', 'product has been deductd successfully');
				redirect('inventory/deduction-product/'.$product_id.'/'.$store_id);
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to deduct product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}

		//load the interface
		$data['title'] = 'Edit Deduction';
		$data['sidebar'] = 'pharmacy_sidebar';
		$product_details = $this->inventory_management_model->get_inventory_product_details($product_id);//var_dump($product_details);die();
		$deduction_details = $this->inventory_management_model->get_deduction_details($product_deduction_id);

		if($product_details->num_rows() > 0)
		{
			$row = $product_details->row();;
			$v_data['title'] = 'Edit '.$row->product_name.' Deduction';
			$v_data['product_id'] = $product_id;
			$v_data['store_id'] = $row->store_id;

			$v_data['container_types'] = $this->inventory_management_model->get_container_types();
			$v_data['deduction_details'] = $deduction_details->row();
			$data['content'] = $this->load->view('products/edit_product_deduction', $v_data, true);
		}

		else
		{
			$data['content'] = 'Could not find deduction details';
		}
		$this->load->view('admin/templates/general_page', $data);
	}


	public function product_deductions($product_id,$store_id)
	{
		$segment = 5;



		$where = 'product_id = '.$product_id;
		$table = 'product_deductions_stock';

		$order = 'product_deductions_stock.product_deductions_stock_date';
		// $where = 'store_product.store_id = product_deductions.store_id AND store_product.store_id = store.store_id AND product_deductions.product_id = product.product_id AND store_product.product_id = product.product_id ';

		$product_search = $this->session->userdata('product_deductions_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		// $v_data['store_id'] = 5;
		$v_data['product_id'] = $product_id;
		// $table = 'product_deductions,store,store_product,product';

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/deduction-product/'.$product_id.'/'.$store_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		//echo $config['total_rows']; die();
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
        $v_data["links"] = $this->pagination->create_links();//echo $where;die();
		$query = $this->inventory_management_model->get_product_deductions_stock($table, $where, $config["per_page"], $page, $order);
		// var_dump($query); die();
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['store_id'] = $store_id;
		// $v_data['product_id'] = $product_id;

		$data['title'] = 'Product Deductions';
		$v_data['title'] = 'Product Deductions';
		// $product_details = $this->inventory_management_model->get_product_details($product_id);

		// $v_data['title'] = $product_details[0]->product_name.' Deductions';
		$data['content'] = $this->load->view('products/deductions', $v_data, true);


		$this->load->view('admin/templates/general_page', $data);

    }

	public function all_product_deductions()
	{
		$segment = 3;

		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
		$addition = '';

		if($store_priviledges->num_rows() > 0)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			//echo $store_priviledges->num_rows(); die();
			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				//echo $store_parent; die();
				$store_id = $key->store_id;
				$count++;

				if($count == 1 AND $number_rows > $count)
				{
					$delimeter = '(';
				}
				else
				{
					$delimeter = '';
				}
				if($number_rows > 1 AND $number_rows == $count)
				{
					$back_delimeter = ')';

				}
				else
				{
					$back_delimeter = '';
				}

				if($count > 0 AND $number_rows != $count)
				{
					$or_addition = 'OR';
				}
				else
				{
					$or_addition = '';
				}
				if($store_parent > 0)
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
				else
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
			}
		}

		$where = $addition.' AND orders.store_id = store.store_id';
		$table = 'orders, store';

		$order = 'product_deductions.product_deductions_date';
		// $where = 'store_product.store_id = product_deductions.store_id AND store_product.store_id = store.store_id AND product_deductions.product_id = product.product_id AND store_product.product_id = product.product_id ';

		$product_search = $this->session->userdata('product_deductions_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		// $table = 'product_deductions,store,store_product,product';

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/s11';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		//echo $config['total_rows']; die();
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
        $v_data["links"] = $this->pagination->create_links();//echo $where;die();
		$query = $this->inventory_management_model->get_product_deductions($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		// $v_data['product_id'] = $product_id;

		$data['title'] = 'Requests Made';
		$v_data['title'] = 'Requests Made';
		// $product_details = $this->inventory_management_model->get_product_details($product_id);

		// $v_data['title'] = $product_details[0]->product_name.' Deductions';
		$data['content'] = $this->load->view('products/product_deductions', $v_data, true);


		$this->load->view('admin/templates/general_page', $data);

    }


    public function search_s11($product_id)
    {
    	$search = ' AND product_deductions.product_id ='.$product_id;

    	$this->session->set_userdata('product_order_search',$search);

    	redirect('inventory/s11');
    }
    public function search_store_ded($product_id)
    {
    	$search = ' AND product_deductions.product_id ='.$product_id;

    	$this->session->set_userdata('product_deduction_search',$search);

    	redirect('inventory/store-deductions');
    }
    public function manage_store()
    {
    	$store_id = 0;
    	$orders_id = 0;
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				$orders_id = $this->input->post('orders_id');
				$store_id = $this->input->post('store_id');

				//import products from excel
				$response = $this->inventory_management_model->import_order_items($this->csv_path, $store_id, $orders_id);

				if($response == FALSE)
				{
				}

				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}

					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}

			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}

    	$data['title'] = 'Store Management';
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
//personnel_store.store_id  = store.store_id AND personnel_store.personnel_id = '.$this->session->userdata('personnel_id').''
		$personnel_id = $this->session->userdata('personnel_id');
		if($personnel_id == 0)
		{
			$where = 'orders.store_id = store.store_id  AND orders.is_store = 1 AND orders.visit_id IS NULL ';
			$table = 'orders, store';
		}
		else
		{
			$where = 'orders.store_id = store.store_id AND personnel_store.store_id = store.store_id AND orders.is_store = 1 AND orders.order_approval_status <> 1 AND personnel_store.personnel_id = '.$this->session->userdata('personnel_id').' ';
			$table = 'orders, store, personnel_store';
		}


		$order = 'orders.order_id';
		// $where = 'store_product.store_id = product_deductions.store_id AND store_product.store_id = store.store_id AND product_deductions.product_id = product.product_id AND store_product.product_id = product.product_id ';

		$product_search = $this->session->userdata('orders_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		// $table = 'product_deductions,store,store_product,product';

		//pagination
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'procurement/store-orders';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->inventory_management_model->get_product_orders($table, $where, $config["per_page"], $page, $order);
		$v_data["suppliers"] = $this->inventory_management_model->get_suppliers();

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$v_data['title'] =  'Store Management';

		$data['content'] = $this->load->view('store_management', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
    }

	public function create_new_order()
	{
		$this->form_validation->set_rules('orders_date', 'Order Date', 'required|xss_clean');
		$this->form_validation->set_rules('store_id', 'Store', 'numeric|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->create_order())
			{
				$this->session->userdata('success_message', 'Product has been added successfully');

			}

			else
			{
				$this->session->userdata('error_message', 'Unable to add product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}
		redirect('procurement/store-orders');
	}

    public function store_requests($orders_id, $store_parent = 1)
    {
		$v_data['orders_id'] = $orders_id;

		$v_data['title'] =  'Receive Order';
		$v_data['store_parent'] =  $store_parent;

		$where1 = 'product_deductions.order_id = orders.order_id AND product_deductions.product_deduction_rejected = 0 AND orders.order_id = '.$orders_id.' AND product_deductions.store_id = store.store_id AND product_deductions.product_id = product.product_id';//echo $where;die();
		$table = 'product_deductions, store, orders, product';

		$search = $this->session->userdata('product_request_search');

		if(!empty($search))
		{
			$where = $where1.$search;
		}

		else
		{
			$where = $where1;
		}
		//pagination
		$segment = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/store-requests/'.$orders_id.'/'.$store_parent;//echo $where;die();
		$config['total_rows'] = $this->reception_model->count_items($table, $where);//var_dump($config['total_rows'] );die();
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

		$page = $v_data['page'] = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$v_data['links'] = $this->pagination->create_links();

		$this->db->distinct();
		$this->db->select('product.product_name, product.product_id, product.product_code');
		$this->db->where($where1);
		$this->db->order_by('product_name');
		$all_products_query = $this->db->get($table);
		$all_products = '<option value="">--Select Item--</option>';

		if($all_products_query->num_rows() > 0)
		{
			foreach($all_products_query->result() as $key)
			{
				$all_products .= '<option value="'.$key->product_id.'">'.$key->product_code.' : '.$key->product_name.'</option>';
			}
		}
		$v_data['all_products'] = $all_products;

		$v_data['query'] = $this->inventory_management_model->get_all_requests_made($table, $where, $config["per_page"], $page,'product_name', $store_parent);

		$this->load->view('store_requests', $v_data);

    }

	public function search_request_product($orders_id, $store_parent)
	{
		$product_id = $this->input->post('product_id');

		if(!empty($product_id))
		{
			$product_id = ' AND product.product_id = \''.$product_id.'\'';
		}
		$search = $product_id;
		$this->session->set_userdata('product_request_search', $search);
		$this->store_requests($orders_id, $store_parent);
	}

   	public function close_request_search($orders_id, $store_parent)
	{
		$this->session->unset_userdata('product_request_search');
		$this->store_requests($orders_id, $store_parent);
	}

   	public function close_inventory_search()
	{
		$this->session->unset_userdata('product_inventory_search');
		$this->session->unset_userdata('inventory_search_end_date');
		$this->session->unset_userdata('inventory_search_start_date');
		$this->index();
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
		$stock_take = $this->input->post('stock_taken');

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
		if(!empty($generic_id) )
		{
			$generic_id = ' AND product.generic_id = '.$generic_id;
		}
		else
		{
			$generic_id = '';
		}

		if($stock_take>=0 AND $stock_take < 2)
		{
			$stock_take = ' AND product.stock_take = 1';
		}
		else
		{
			$stock_take = '';
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

		$search = $product_name.$generic_id.$brand_id.$category_id.$store_id.$product_code.$stocked.$stock_take;
		$this->session->set_userdata('product_inventory_search', $search);
		$this->session->set_userdata('inventory_search_start_date',$start_date);
		$this->session->set_userdata('inventory_search_end_date',$end_date);

		$this->index();
	}

	public function make_order_search($store_id, $order_id)
	{
		$product_code = $this->input->post('product_code');
		$product_name = $this->input->post('product_name');
		$category_id = $this->input->post('category_id');

		if(!empty($product_name))
		{
			$product_name = " AND product.product_name LIKE '%".$product_name."%'";
		}
		else
		{
			$product_name = '';
		}

		if(!empty($category_id))
		{
			$category_id = ' AND product.category_id = '.$category_id;
		}
		else
		{
			$category_id = '';
		}
		if (!empty($product_code))
		{
			$product_code = " AND product.product_code LIKE '%".$product_code."%'";
		}
		else
		{
			$product_code = '';
		}

		$search = $product_name.$category_id.$product_code;
		$this->session->set_userdata('make_order_search', $search);

		$this->make_order($store_id, $order_id);
	}

   	public function close_order_search($store_id, $order_id)
	{
		$this->session->unset_userdata('make_order_search');
		$this->make_order($store_id, $order_id);
	}

	public function make_order($store_id, $order_id)
	{
		//check patient visit type
		// get parent store
		$parent_id = $this->inventory_management_model->get_parent_store($store_id);

		$order = 'product_name';
		if($parent_id > 0)
		{
			$where = 'category.category_id = product.category_id AND product.product_deleted = 0 AND product.stock_take = 1';
		}

		else
		{
			$where = 'category.category_id = product.category_id AND product.product_deleted = 0 AND product.stock_take = 1';
		}
		// echo $where; die();
		$order_search = $this->session->userdata('make_order_search');

		if(!empty($order_search))
		{
			$where .= $order_search;
		}

		$table = 'product, category';
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/make-order/'.$store_id.'/'.$order_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 5;
		$config['per_page'] = 15;
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

		$page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->inventory_management_model->get_parent_products($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = 'Product List';
		$v_data['title'] = 'Product List';
		$v_data['all_categories'] = $this->categories_model->all_categories();

		$v_data['store_id'] = $store_id;
		$v_data['order_id'] = $order_id;
		$data['content'] = $this->load->view('product_list', $v_data, true);

		$data['title'] = 'Product List';
		$this->load->view('admin/templates/no_sidebar', $data);
	}

	public function save_order_products($product_id, $store_id, $order_id){

		$this->db->where('product_id = '.$product_id.' AND owning_store_id = '.$store_id.'');
		$query = $this->db->get('store_product');

		if($query->num_rows() > 0)
		{

		}
		else
		{
			$array['product_id'] = $product_id;
			$array['owning_store_id'] = $store_id;
			$array['created'] = date('Y-m-d');
			$array['created_by'] = $this->session->userdata('personnel_id');
			$this->db->insert('store_product',$array);
		}

		$data = array('store_id' => $store_id, 'order_id' => $order_id, 'product_id'=> $product_id,'date_requested'=>date('Y-m-d H:i:s'),'search_date'=>date('Y-m-d'),'requested_by'=>$this->session->userdata('personnel_id'));

		if($this->db->insert('product_deductions', $data))
		{
			//check if product exists in store product
			$this->inventory_management_model->check_store_product($store_id, $product_id);
		}
	}

	public function save_all_order_products($store_id, $order_id)
	{
		//get products
		$parent_id = $this->inventory_management_model->get_parent_store($store_id);

		if($parent_id != 0)
		{
			$where = 'category.category_id = product.category_id AND product.store_id ='.$parent_id;
		}

		else
		{
		}
			$where = 'category.category_id = product.category_id AND product.store_id ='.$store_id;

		$this->db->where($where);
		$query = $this->db->get('product, category');
		//var_dump($query->num_rows());die();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$product_id = $res->product_id;
				//check if product exist
				$product_exist = $this->product_exists($product_id,$order_id);
				if($product_exist==FALSE)
				{
					$data = array('store_id' => $store_id, 'order_id' => $order_id, 'product_id'=> $product_id,'date_requested'=>date('Y-m-d H:i:s'),'search_date'=>date('Y-m-d'),'requested_by'=>$this->session->userdata('personnel_id'));

					if($this->db->insert('product_deductions', $data))
					{
						//check if product exists in store product
						$this->inventory_management_model->check_store_product($store_id, $product_id);
					}
				}
			}
		}
		redirect('inventory/make-order/'.$store_id.'/'.$order_id);
	}

	public function now_store_requests($store_id, $order_id)
    {
		$v_data['store_id'] =  $store_id;
		$v_data['order_id'] =  $order_id;

		$this->load->view('product_selected_list', $v_data);

    }
    public function update_order_products($product_deductions_id, $quantity)
    {
    	$data = array('quantity_requested' => $quantity,'requested_by'=>$this->session->userdata('personnel_id'));
    	$this->db->where('product_deductions_id ='.$product_deductions_id);
		$this->db->update('product_deductions',$data);
			$data['result']="success";
		echo json_encode($data);
    }
    public function remove_from_order($product_deductions_id)
    {
    	$this->db->where('product_deductions_id ='.$product_deductions_id);
		$this->db->delete('product_deductions');
		$data['result']="success";
		echo json_encode($data);
    }
    public function award_order_products($product_deductions_id,$quantity,$parent_id,$product_id)
    {
    	// $parent_store_qty = $this->inventory_management_model->get_parent_store_inventory_quantity($parent_id,$product_id);
    	$inventory_start_date = $this->inventory_management_model->get_inventory_start_date();
    	$parent_store_qty_units = $this->inventory_management_model->parent_stock_store($inventory_start_date, $product_id,$parent_id);
    	$parent_store_qty = $parent_store_qty_units;
    	if($parent_store_qty >= $quantity)
    	{
    		$data = array('quantity_given' => $quantity,'given_by'=>$this->session->userdata('personnel_id'),'product_deductions_status'=>1);
	    	$this->db->where('product_deductions_id ='.$product_deductions_id);
			$this->db->update('product_deductions',$data);
			$data['result']= "You've successfully awarded the store ".$quantity." ";
    	}
    	else
    	{
    		$data['result']= "Sorry you cannot award the quantity you have only ".$parent_store_qty_units;
    	}


		echo json_encode($data);
    }
    public function receive_order_products($product_deductions_id,$quantity,$product_id,$store_id)
    {
    	// update the products table

		$this->db->select('quantity');
		$this->db->where("product_id = ".$product_id."");
		$query = $this->db->get('product');

		if($query->num_rows())
		{
			$data_query = $query->result();

			$old_quantity = $data_query[0]->quantity;
		}

		$new_quantity = $old_quantity - $quantity;

		/*$data = array('quantity' => $new_quantity);
    	$this->db->where('product_id  ='.$product_id);
		$this->db->update('product',$data);*/

		// dinished updating the products table


		$this->db->select('quantity');
		$this->db->where("product_id = ".$product_id." AND store_id = ".$store_id."");
		$store_query = $this->db->get('store_product');
		$store_quantity = 0;

		if($store_query->num_rows())
		{
			$data_store_query = $store_query->result();

			$store_quantity = $data_store_query[0]->quantity;
		}

		$new_store_quantity = $store_quantity + $quantity;
		$where = 'product_id  ='.$product_id.' AND store_id = '.$store_id;

		//check if product exists in store product
		$this->db->where($where);
		$query = $this->db->get('store_product');

		//if exists
		if($query->num_rows() > 0)
		{
			$data2 = array('quantity' => $new_store_quantity);
			$this->db->where($where);
			$this->db->update('store_product',$data2);
		}
		else
		{
			$data2 = array('quantity' => $new_store_quantity, 'product_id' => $product_id, 'store_id' => $store_id);
			$this->db->insert('store_product',$data2);
		}

    	// update child store
    	$data3 = array('quantity_received' => $quantity,'received_by'=>$this->session->userdata('personnel_id'),'product_deductions_status'=>2);
    	$this->db->where('product_deductions_id ='.$product_deductions_id);
		$this->db->update('product_deductions',$data3);

		$data['result']="You've successfully received ".$quantity."";


		echo json_encode($data);
    }

	public function costing($lab_test_id)
	{
		$this->db->where('lab_test_id', $lab_test_id);
		$lab_query = $this->db->get('lab_test');
		$lab_test_name = '';
		if($lab_query->num_rows() > 0)
		{
			$row = $lab_query->row();
			$lab_test_name = $row->lab_test_name;
		}
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		//$store_id = 9;
		if($store_priviledges->num_rows() > 0)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			if($number_rows > 1)
			{
				$v_data['type'] = 3;
			}

			else
			{
				$v_data['type'] = 2;
			}

			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				$store_id = $key->store_id;
				$constant = ' AND store_product.owning_store_id = store.store_id AND product.product_id = store_product.product_id ';

				if($store_parent > 0)
				{
					if($number_rows == 1)
					{
						$v_data['type'] = 1;
					}
				}
			}
		}
		else
		{
			$v_data['type'] = 4;
			$table = '';
			$constant = '';
			$addition = '';
		}
		// var_dump($constant); die();
		$where = '(store.store_name = "Laboratory") AND store.store_id = store_product.owning_store_id AND product.product_id = store_product.product_id AND product.product_deleted = 0 AND product.stock_take = 1 AND product.category_id = category.category_id '.$constant;
		$table = 'product, category, store, store_product';
		$order_search = $this->session->userdata('make_costing_search');

		if(!empty($order_search))
		{
			$where .= $order_search;
		}

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory_management/costing/'.$lab_test_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = 4;
		$config['per_page'] = 15;
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

		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $v_data["links"] = $this->pagination->create_links();
		$query = $this->inventory_management_model->get_parent_products($table, $where, $config["per_page"], $page, $order = 'product_name');

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = $v_data['title'] = $lab_test_name.' Costing';
		$v_data['all_categories'] = $this->categories_model->all_categories();

		$v_data['lab_test_id'] = $lab_test_id;
		$data['content'] = $this->load->view('costing', $v_data, true);

		$data['title'] = 'Costing';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function make_costing_search($lab_test_id)
	{
		$product_name = $this->input->post('product_name');

		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}

		$search = $product_name;
		$this->session->set_userdata('make_costing_search', $search);

		$this->costing($lab_test_id);
	}

   	public function close_costing_search($lab_test_id)
	{
		$this->session->unset_userdata('make_costing_search');
		$this->costing($lab_test_id);
	}

	public function test_costings($lab_test_id)
    {
		$v_data['lab_test_id'] =  $lab_test_id;

		$this->load->view('test_selected_list', $v_data);
    }

	public function add_costing($product_id, $lab_test_id)
	{
		if($product_id > 0)
		{
			$data = array('product_id' => $product_id, 'lab_test_id' => $lab_test_id);
			$table = 'test_costing';
			//check if it exists
			$this->db->where($data);
			$query = $this->db->get($table);

			if($query->num_rows() == 0)
			{
				//add
				$data['test_costing_units'] = 1;
				if($this->db->insert($table, $data))
				{
					$this->session->set_userdata('success_message', 'Cost added successfully');
				}

				else
				{
					$this->session->set_userdata('error_message', 'Unable to add cost. Please try again');
				}
			}

			else
			{
				$this->session->set_userdata('error_message', 'Cost already exists');
			}
		}

		else
		{
			$this->session->set_userdata('error_message', 'Please select a product to add');
		}

		redirect('inventory_management/costing/'.$lab_test_id);
	}

	public function remove_costing($test_costing_id, $lab_test_id)
	{
		$where = array('test_costing_id' => $test_costing_id);
		$table = 'test_costing';

		//remove
		$this->db->where($where);
		if($this->db->delete($table))
		{
			$this->session->set_userdata('success_message', 'Cost deleted successfully');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to delete cost. Please try again');
		}

		redirect('inventory_management/costing/'.$lab_test_id);
	}

	public function delete_product($product_id)
	{
		$where = array('product_id' => $product_id);
		$table = 'product';
		$array['product_deleted'] = 1;
		$this->db->where($where);

		if($this->db->update($table,$array))
		{
			$where1 = array('product_id' => $product_id);
			$table_two = 'service_charge';
			$array_two['service_charge_delete'] = 1;
			$this->db->where($where1);
			$this->db->update($table_two,$array_two);

			$this->session->set_userdata('success_message', 'You have deleted this product');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to reject. Please try again');
		}

		redirect('inventory/products');
	}
	public function reject_deduction($product_deductions_id)
	{
		$where = array('product_deductions_id' => $product_deductions_id);
		$table = 'product_deductions';
		$array['product_deduction_rejected'] = 1;
		//remove
		$this->db->where($where);
		if($this->db->update($table,$array))
		{
			$this->session->set_userdata('success_message', 'You have rejected this order');
		}

		else
		{
			$this->session->set_userdata('error_message', 'Unable to reject. Please try again');
		}

		redirect('inventory/s11');
	}
    public function update_test_costing($test_costing_id, $quantity)
    {
    	$data = array('test_costing_units' => $quantity,'modified_by'=>$this->session->userdata('personnel_id'));
    	$this->db->where('test_costing_id ='.$test_costing_id);
		$this->db->update('test_costing',$data);
			$data['result']="Units updated successfully";
		echo json_encode($data);
    }
	public function recieve_order($orders_id, $store_parent = 1)
	{
		$data['title'] = $v_data['title'] = 'Receive Order';
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
		$v_data['orders_id'] = $orders_id;
		$v_data['store_parent'] = $store_parent;

		$data['content'] = $this->load->view('recieve_order', $v_data, true);

		$this->load->view('admin/templates/general_page', $data);
	}

	public function view_order($order_id)
	{
		$v_data['order_id'] = $order_id;
		$v_data['title'] =  'Order Details';
		$v_data['query'] = $this->inventory_management_model->get_order_details ($order_id);
		$data['content'] = $this->load->view('view_order', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}



	public function view_ordered_items()
	{


		$segment = 3;

		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
		$addition = '';

		if($store_priviledges->num_rows() > 0)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			//echo $store_priviledges->num_rows(); die();
			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				//echo $store_parent; die();
				$store_id = $key->store_id;
				$count++;

				if($count == 1 AND $number_rows > $count)
				{
					$delimeter = '(';
				}
				else
				{
					$delimeter = '';
				}
				if($number_rows > 1 AND $number_rows == $count)
				{
					$back_delimeter = ')';

				}
				else
				{
					$back_delimeter = '';
				}

				if($count > 0 AND $number_rows != $count)
				{
					$or_addition = 'OR';
				}
				else
				{
					$or_addition = '';
				}
				if($store_parent > 0)
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
				else
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
			}
		}

		// $where = $addition.' AND orders.store_id = store.store_id';
		// $table = 'product_deductions, store, product,orders,visit_charge';

		// $order = 'product_deductions.product_deductions_date';
		// $where = 'product_deductions.store_id = store.store_id AND product.product_deleted = 0 AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = product_deductions.order_id AND visit_charge.visit_charge_id = product_deductions.visit_charge_id ';


		$table = 'product_deductions, store, product,orders,visit_charge';

		$order = 'product_deductions.product_deductions_date';
		$where = 'product_deductions.store_id = store.store_id AND product.product_deleted = 0 AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = product_deductions.order_id AND visit_charge.visit_charge_id = product_deductions.visit_charge_id AND product.stock_take = 1 ';

		$product_search = $this->session->userdata('product_order_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		// $table = 'product_deductions,store,store_product,product';

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/s11';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		//echo $config['total_rows']; die();
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
        $v_data["links"] = $this->pagination->create_links();//echo $where;die();
		$query = $this->inventory_management_model->get_order_items($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		// $v_data['product_id'] = $product_id;

		$data['title'] = 'Requests Made';
		$v_data['title'] = 'Requests Made';
		// $product_details = $this->inventory_management_model->get_product_details($product_id);

		// $v_data['title'] = $product_details[0]->product_name.' Deductions';
		$data['content'] = $this->load->view('view_order_items', $v_data, true);


		$this->load->view('admin/templates/general_page', $data);
	}
	public function approve_order($order_id)
	{
		if($this->inventory_management_model->approve_order($order_id))
		{
			$this->session->userdata('success_message', 'Order approvec successfully');
		}

		else
		{
			$this->session->userdata('error_message', 'Unable to approve your order. Please try again');
		}
		redirect('inventory/manage-orders');
	}
	public function print_order($order_id)
	{
		$this->db->where('branch_id = '.$this->session->userdata('branch_id'));
		$branches = $this->db->get('branch');

		if($branches->num_rows() > 0)
		{
			$row = $branches->result();
			$branch_id = $row[0]->branch_id;
			$data['branch_name'] = $branch_name = $row[0]->branch_name;
			$data['branch_image_name'] = $branch_image_name = $row[0]->branch_image_name;
			$data['branch_address'] = $branch_address = $row[0]->branch_address;
			$data['branch_post_code'] = $branch_post_code = $row[0]->branch_post_code;
			$data['branch_city'] =  $branch_city = $row[0]->branch_city;
			$data['branch_phone'] = $branch_phone = $row[0]->branch_phone;
			$data['branch_email'] = $branch_email = $row[0]->branch_email;
			$data['branch_location']  = $branch_location = $row[0]->branch_location;
		}
		$data['order_id'] = $order_id;
		$data['order_number' ] = $this->inventory_management_model->get_order_number($order_id);
		$data['title'] =  'Order Details';
		$data['query'] = $this->inventory_management_model->get_order_details($order_id);
		$this->load->view('print_order', $data);
	}
	public function manage_orders()
	{
		$suppliers = '<option value="0">--Select Supplier--</option>';

		// $store_suppliers = $this->inventory_management_model->get_nav_suppliers();
		// if($store_suppliers->num_rows() > 0)
		// {
		// 	foreach($store_suppliers->result() as $key)
		// 	{
		// 		$suppliers .= '<option value="'.$key->nav_supplier_id.'">'.$key->Search_Name.'</option>';
		// 	}
		// }
		$v_data['suppliers'] = $suppliers;
		$v_data['title'] =  'Order Management';
		$v_data['query'] = $this->inventory_management_model->get_all_parent_store_orders();
		$data['content'] = $this->load->view('manage_orders', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function view_lpos($order_id)
	{
		$suppliers = '<option value="0">--Select Supplier--</option>';

		$store_suppliers = $this->inventory_management_model->get_nav_suppliers();
		if($store_suppliers->num_rows() > 0)
		{
			foreach($store_suppliers->result() as $key)
			{
				$suppliers .= '<option value="'.$key->nav_supplier_id.'">'.$key->Search_Name.'</option>';
			}
		}
		$v_data['suppliers'] = $suppliers;
		$v_data['order_id'] = $order_id;
		$v_data['title'] = $data['title'] = 'LPO Management';
		$where = 'lpo.order_id = orders.order_id AND lpo.order_id = '.$order_id;
		$table = 'orders, lpo';
		$order = 'lpo.lpo_date, nav_supplier.Search_Name';

		$lpo_search = $this->session->userdata('lpo_search');

		if(!empty($lpo_search))
		{
			$where .= $lpo_search;
		}

		//pagination
		$segment = 3;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'view-lpos/'.$order_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
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
		$query = $this->inventory_management_model->get_all_lpos($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['content'] = $this->load->view('view_lpos', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	public function check_store_parent($store_id)
	{
		//check if store id selected is parent
		if($this->inventory_management_model->check_store_parent($store_id))
		{
			//select all suppliers
			echo '<option value="0">--Select Supplier--</option>';

			$store_suppliers = $this->inventory_management_model->get_all_suppliers();

			foreach($store_suppliers->result() as $key)
			{
				echo '<option value="'.$key->supplier_id.'">'.$key->supplier_name.'</option>';
			}
		}
	}

	public function order_items_import_template()
	{
		$this->inventory_management_model->import_template();
	}

	public function import_drugs_to_order($store_id, $orders_id)
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel
				$response = $this->inventory_management_model->import_order_items($this->csv_path, $store_id, $orders_id);

				if($response == FALSE)
				{
				}

				else
				{
					if($response['check'])
					{
						$v_data['import_response'] = $response['response'];
					}

					else
					{
						$v_data['import_response_error'] = $response['response'];
					}
				}
			}

			else
			{
				$v_data['import_response_error'] = 'Please select a file to import.';
			}
		}

		else
		{
			$v_data['import_response_error'] = 'Please select a file to import.';
		}

		redirect('');
	}

	public function test_order_save($order_id = 1)
	{
		$response = $this->inventory_management_model->create_purchase_header_in_nav($order_id);
		var_dump($response);
	}

	public function test_order_item_save($order_id = 1)
	{
		$response = $this->inventory_management_model->create_purchase_line_in_nav($order_id);
		var_dump($response);
	}
	public function is_store_parent($store_id)
	{
		$parent = 0;
		$this->db->select('store_parent');
		$this->db->where('store_id = '.$store_id);
		$query = $this->db->get('store');

		if($query->num_rows() > 0)
		{
			$query_result = $query->row();
			$parent = $query_result->store_parent;
		}
		$data['parent'] = $parent;
		echo json_encode($data);
	}

	public function product_exists($product_id,$order_id)
	{
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND order_id = '.$order_id);
		$query = $this->db->get('product_deductions');
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function add_navision_order($order_id)
	{
		$this->form_validation->set_rules('nav_supplier_id', 'Supplier', 'required|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{
			if($this->inventory_management_model->create_purchase_header_in_nav($order_id))
			{
				$this->session->set_userdata('success_message', 'Order updated successfully in Navision');
			}
		}

		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		redirect('inventory/manage-orders');
	}

	public function add_navision_request($order_id)
	{
		$this->form_validation->set_rules('nav_supplier_id', 'Supplier', 'required|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{
			if($this->inventory_management_model->create_request_header_in_nav($order_id))
			{
				$this->session->set_userdata('success_message', 'Order updated successfully in Navision');
			}
		}

		else
		{
			$this->session->set_userdata('error_message', validation_errors());
		}
		redirect('inventory/manage-orders');
	}

	public function update_navision_order($order_id, $nav_supplier_id)
	{
		if($this->inventory_management_model->create_purchase_header_in_nav($order_id, $nav_supplier_id))
		{
			$this->session->set_userdata('success_message', 'Order updated successfully in Navision');
		}

		else
		{
			//$this->session->set_userdata('error_message', 'Unable to update');
		}
		redirect('inventory/manage-orders');
	}

	public function update_navision_request($order_id, $nav_supplier_id)
	{
		if($this->inventory_management_model->create_request_header_in_nav($order_id, $nav_supplier_id))
		{
			$this->session->set_userdata('success_message', 'Order updated successfully in Navision');
		}

		else
		{
			//$this->session->set_userdata('error_message', 'Unable to update');
		}
		redirect('inventory/manage-orders');
	}
	public function approve_request_order($order_id)
	{
		if($this->inventory_management_model->approve_order($order_id))
		{
			$this->session->userdata('success_message', 'Order approvec successfully');
		}

		else
		{
			$this->session->userdata('error_message', 'Unable to approve your order. Please try again');
		}
		redirect('inventory/s11');
	}

	public function test_requisition_line()
	{
		$order_id = 6;
		$nav_supplier_id = 1;
		$entry_no = 'STRQ00235319';
		$this->inventory_management_model->create_request_line_in_nav($order_id, $nav_supplier_id, $entry_no);
	}
	public function out_of_stock()
	{

		$branches = $this->db->get('branch');
		$data['branches'] = $branches;
		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
		$store_id = 0;
		$addition = '';

		//$constant  = ' AND product.store_id = store.store_id AND ';
		$constant  = ' AND product.store_id = store.store_id ';
		$table = '';

		if($store_priviledges->num_rows() > 0)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			if($number_rows > 1)
			{
				$v_data['type'] = 3;
			}

			else
			{
				$v_data['type'] = 2;
			}

			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				$store_id = $key->store_id;
				$count++;

				if($count == 1 AND $number_rows > 1)
				{
					$delimeter = '(';
				}
				else
				{
					$delimeter = '';
				}
				if($number_rows > 1 AND $number_rows == $count)
				{
					$back_delimeter = ')';

				}
				else
				{
					$back_delimeter = '';
				}

				if($count > 0 AND $number_rows != $count)
				{
					$or_addition = 'OR';
				}
				else
				{
					$or_addition = '';
				}
				if($store_parent > 0)
				{
					$table = ',store_product';
					//$constant = ' AND store_product.store_id = store.store_id AND product.product_id = store_product.product_id AND ';
					$constant = ' AND store_product.owning_store_id = store.store_id AND product.product_id = store_product.product_id ';
					if($number_rows == 1)
					{
						$v_data['type'] = 1;
					}/*
					$addition .= ' '.$delimeter.' store_product.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';*/
				}
				/*else
				{
					$addition .= ''.$delimeter.'store.store_id = '.$store_id.' '.$back_delimeter.'';
				}*/
			}
		}
		else
		{
			$v_data['type'] = 4;
			$table = '';
			$constant = '';
			$addition = '';
		}//echo $where;;die();
		$personnel_id = $this->session->userdata('personnel_id');
		$where = 'product.category_id = category.category_id '.$constant.' '.$addition.' AND personnel_store.personnel_id = '.$personnel_id.' AND store.store_id = personnel_store.store_id';
		$table = 'personnel_store, product, category, store'.$table;

		//echo $where;die();

		$product_inventory_search = $this->session->userdata('product_inventory_search');

		if(!empty($product_inventory_search))
		{
			$where .= $product_inventory_search;
		}
		$query = $this->products_model->get_all_products_out_of_stock($table, $where);

		$v_data['store_id'] = $store_id;

		$data['title'] = 'Products Out Of Stock';
		$v_data['title'] = $data['title'];
		$v_data['query'] = $query;
		$v_data['branches'] = $branches;

		$this->load->view('products/out_of_stock', $v_data);
	}

	public function update_initial_product_balance()
	{
		$this->inventory_management_model->update_initial_product_balance();
		$this->session->set_userdata('success_message', 'Stock balances updated successfully');
		redirect('inventory/products');
	}

	public function create_new_lpo($order_id)
	{
		$this->form_validation->set_rules('lpo_date', 'LPO Date', 'required|xss_clean');
		$this->form_validation->set_rules('nav_supplier_id', 'Supplier', 'required|xss_clean');

		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->create_new_lpo($order_id))
			{
				$this->session->userdata('success_message', 'LPO has been created successfully');
			}

			else
			{
				$this->session->userdata('error_message', 'Unable to create LPO. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}
		redirect('view-lpos/'.$order_id);
	}

	public function search_lpo_products($lpo_id, $order_id)
	{
		$product_code = $this->input->post('product_code');
		$product_name = $this->input->post('product_name');
		$category_id = $this->input->post('category_id');

		if(!empty($product_name))
		{
			$product_name = " AND product.product_name LIKE '%".$product_name."%'";
		}
		else
		{
			$product_name = '';
		}

		if(!empty($category_id))
		{
			$category_id = ' AND product.category_id = '.$category_id;
		}
		else
		{
			$category_id = '';
		}
		if (!empty($product_code))
		{
			$product_code = " AND product.product_code LIKE '%".$product_code."%'";
		}
		else
		{
			$product_code = '';
		}

		$search = $product_name.$category_id.$product_code;
		$this->session->set_userdata('lpo_product_search', $search);

		$this->make_order($store_id, $order_id);
	}

   	public function close_lpo_products_search($lpo_id, $order_id)
	{
		$this->session->unset_userdata('lpo_product_search');
		$this->lpo_products($store_id, $order_id);
	}

	public function lpo_products($lpo_id, $order_id)
	{
		$order = 'product_name';
		$where = 'category.category_id = product.category_id AND product.product_id IN (SELECT product_id FROM product_deductions WHERE product_deductions.order_id = '.$order_id.')';
		//echo $where; die();
		$order_search = $this->session->userdata('lpo_product_search');

		if(!empty($order_search))
		{
			$where .= $order_search;
		}

		$table = 'product, category';
		$segment = 4;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'add-lpo-items/'.$lpo_id.'/'.$order_id;
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		$config['uri_segment'] = $segment;
		$config['per_page'] = 15;
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
		$query = $this->inventory_management_model->get_parent_products($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;

		$data['title'] = 'Product List';
		$v_data['title'] = 'Product List';
		$v_data['all_categories'] = $this->categories_model->all_categories();

		$v_data['lpo_id'] = $lpo_id;
		$v_data['order_id'] = $order_id;
		$data['content'] = $this->load->view('lpo_products', $v_data, true);

		$data['title'] = 'Product List';
		$this->load->view('admin/templates/no_sidebar', $data);
	}

	public function save_lpo_item($product_id, $lpo_id, $order_id)
	{

		$lpo_item_id = $this->lpo_product_exists($product_id, $lpo_id);
				$items = $this->inventory_management_model->get_product_deduction_quantity($product_id, $order_id);
		if($lpo_item_id == FALSE)
		{
			$quantity = $items['quantity'];
			$lpo_price = $items['lpo_price'];
			$data = array('lpo_item_price' => $lpo_price, 'quantity_requested' => $quantity, 'lpo_id' => $lpo_id, 'product_id'=> $product_id,'created'=>date('Y-m-d H:i:s'),'created_by'=>$this->session->userdata('personnel_id'));

			if($this->db->insert('lpo_item', $data))
			{
				echo 'Success';
			}
			else
			{
				echo 'Unable to add item';
			}
		}
		else
		{
			$quantity = $items['quantity'];
			$lpo_price = $items['lpo_price'];
			$data = array('lpo_item_price' => $lpo_price, 'quantity_requested' => $quantity);
			$this->db->where('lpo_item_id', $lpo_item_id);
			if($this->db->update('lpo_item', $data))
			{
				echo 'Success';
			}
			else
			{
				echo 'Unable to add item';
			}
		}
	}

	public function save_all_lpo_products($lpo_id, $order_id)
	{
		$where = 'category.category_id = product.category_id AND product.product_id IN (SELECT product_id FROM product_deductions WHERE product_deductions.order_id = '.$order_id.')';

		$this->db->where($where);
		$query = $this->db->get('product, category');
		//var_dump($query->num_rows());die();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$product_id = $res->product_id;
				//check if product exist
				$lpo_item_id = $this->lpo_product_exists($product_id, $lpo_id);
				$items = $this->inventory_management_model->get_product_deduction_quantity($product_id, $order_id);
				if($lpo_item_id == FALSE)
				{
					$quantity = $items['quantity'];
					$lpo_price = $items['lpo_price'];
					$data = array('lpo_item_price' => $lpo_price, 'quantity_requested' => $quantity, 'lpo_id' => $lpo_id, 'product_id'=> $product_id,'created'=>date('Y-m-d H:i:s'),'created_by'=>$this->session->userdata('personnel_id'));

					if($this->db->insert('lpo_item', $data))
					{
					}
				}
				else
				{
					$quantity = $items['quantity'];
					$lpo_price = $items['lpo_price'];
					$data = array('lpo_item_price' => $lpo_price, 'quantity_requested' => $quantity);
					$this->db->where('lpo_item_id', $lpo_item_id);
					if($this->db->update('lpo_item', $data))
					{
					}
				}
			}
		}
		//echo 'Success';
		redirect('add-lpo-items/'.$lpo_id.'/'.$order_id);
	}

	public function lpo_product_exists($product_id, $lpo_id)
	{
		$this->db->select('*');
		$this->db->where('product_id = '.$product_id.' AND lpo_id = '.$lpo_id);
		$query = $this->db->get('lpo_item');
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$lpo_item_id = $row->lpo_item_id;
			return $lpo_item_id;
		}
		else
		{
			return FALSE;
		}
	}

	public function selected_lpo_items($lpo_id, $order_id)
    {
		$v_data['lpo_id'] =  $lpo_id;
		$v_data['order_id'] =  $order_id;

		$this->load->view('selected_lpo_items', $v_data);
    }

    public function update_lpo_items($lpo_item_id, $quantity_requested)
    {
    	$data = array(
			'quantity_requested' => $quantity_requested,
			'modified_by'=>$this->session->userdata('personnel_id')
		);
    	$this->db->where('lpo_item_id ='.$lpo_item_id);
		$this->db->update('lpo_item',$data);
		$data['result']="success";
		echo json_encode($data);
    }

    public function remove_from_lpo($lpo_item_id)
    {
    	$this->db->where('lpo_item_id ='.$lpo_item_id);
		$this->db->delete('lpo_item');
		$data['result']="success";
		echo json_encode($data);
    }
	public function approve_lpo($lpo_id, $order_id)
	{
		if($this->inventory_management_model->approve_lpo($lpo_id))
		{
			$this->session->userdata('success_message', 'Order approvec successfully');
		}

		else
		{
			$this->session->userdata('error_message', 'Unable to approve your order. Please try again');
		}
		redirect('view-lpos/'.$order_id);
	}
	public function print_lpo($lpo_id, $order_id)
	{
		$this->db->where('branch_id = '.$this->session->userdata('branch_id'));
		$branches = $this->db->get('branch');

		if($branches->num_rows() > 0)
		{
			$row = $branches->result();
			$branch_id = $row[0]->branch_id;
			$data['branch_name'] = $branch_name = $row[0]->branch_name;
			$data['branch_image_name'] = $branch_image_name = $row[0]->branch_image_name;
			$data['branch_address'] = $branch_address = $row[0]->branch_address;
			$data['branch_post_code'] = $branch_post_code = $row[0]->branch_post_code;
			$data['branch_city'] =  $branch_city = $row[0]->branch_city;
			$data['branch_phone'] = $branch_phone = $row[0]->branch_phone;
			$data['branch_email'] = $branch_email = $row[0]->branch_email;
			$data['branch_location']  = $branch_location = $row[0]->branch_location;
		}
		$data['lpo_id'] = $lpo_id;
		$data['lpo_query' ] = $this->inventory_management_model->get_lpo_details($lpo_id);
		$data['title'] =  'Print LPO';
		$data['query'] = $this->inventory_management_model->selected_lpo_items($lpo_id, $order_id);
		$this->load->view('print_lpo', $data);
	}

	public function download_all_stock_old()
	{

		$where = 'product.category_id = category.category_id AND product.product_deleted = 0 ';
		$table = 'product, category';


		//echo $table;die();

		// $product_inventory_search = $this->session->userdata('product_inventory_search');

		// if(!empty($product_inventory_search))
		// {
		// 	$where .= $product_inventory_search;
		// }

		$query = $this->products_model->get_all_products_download($table, $where);
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();
		$v_data['store_id'] = 5;
		$v_data['query'] = $query;
		$v_data['page'] = 0;
		$v_data['title'] = 'All Products';

		$v_data['all_categories'] = $this->categories_model->all_categories();

		$this->load->view('products/all_products_download', $v_data);
	}
	public function download_all_stock()
	{
		$this->inventory_management_model->export_stock_levels();
	}

	public function view_all_product_deductions()
	{
		$segment = 3;

		$store_priviledges = $this->inventory_management_model->get_assigned_stores();
		$v_data['store_priviledges'] =  $store_priviledges;
		$addition = '';

		if($store_priviledges->num_rows() > 0)
		{
			$count = 0;
			$number_rows = $store_priviledges->num_rows();
			//echo $store_priviledges->num_rows(); die();
			foreach ($store_priviledges->result() as $key)
			{
				# code...
				$store_parent = $key->store_parent;
				//echo $store_parent; die();
				$store_id = $key->store_id;
				$count++;

				if($count == 1 AND $number_rows > $count)
				{
					$delimeter = '(';
				}
				else
				{
					$delimeter = '';
				}
				if($number_rows > 1 AND $number_rows == $count)
				{
					$back_delimeter = ')';

				}
				else
				{
					$back_delimeter = '';
				}

				if($count > 0 AND $number_rows != $count)
				{
					$or_addition = 'OR';
				}
				else
				{
					$or_addition = '';
				}
				if($store_parent > 0)
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
				else
				{
					$addition .= $delimeter.'orders.store_id = '.$store_id.' '.$or_addition.' '.$back_delimeter.'';
				}
			}
		}

		// $where = $addition.' AND orders.store_id = store.store_id';
		$table = 'product_deductions, store, product, orders';

		$order = 'product_deductions.product_deductions_date';
		$where = 'product_deductions.store_id = store.store_id AND product.product_deleted = 0 AND product_deductions.quantity_requested > 0 AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = product_deductions.order_id AND orders.is_store = 1 AND product.stock_take = 1';

		$product_search = $this->session->userdata('product_deduction_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}

		// $table = 'product_deductions,store,store_product,product';

		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url().'inventory/store-deductions';
		$config['total_rows'] = $this->reception_model->count_items($table, $where);
		//echo $config['total_rows']; die();
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
        $v_data["links"] = $this->pagination->create_links();//echo $where;die();
		$query = $this->inventory_management_model->get_order_items_store($table, $where, $config["per_page"], $page, $order);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		// $v_data['product_id'] = $product_id;

		$data['title'] = 'Requests Made';
		$v_data['title'] = 'Requests Made';
		// $product_details = $this->inventory_management_model->get_product_details($product_id);

		// $v_data['title'] = $product_details[0]->product_name.' Deductions';
		$data['content'] = $this->load->view('view_store_order_items', $v_data, true);


		$this->load->view('admin/templates/general_page', $data);



    }

    public function search_orders_requested()
	{
		$product_name = $this->input->post('product_name');
		$drug_status = $this->input->post('drug_status');

		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}

		if(!empty($drug_status))
		{
			if($drug_status == 0)
			{
				$drug_status = '';
			}
			else if($drug_status == 1)
			{
				$drug_status = ' AND (product_deductions.quantity_given IS NULL OR product_deductions.quantity_given =0) AND product_deductions.product_deduction_rejected <> 1';
			}
			else if($drug_status == 2)
			{
				$drug_status = ' AND product_deductions.quantity_given  > 0 ';
			}
			else if($drug_status == 3)
			{
				$drug_status = ' AND product_deductions.product_deduction_rejected = 1 ';
			}
		}
		else
		{
			$drug_status = '';
		}



		$search = $product_name.$drug_status;
		$this->session->set_userdata('product_order_search', $search);
		redirect('inventory/s11');
	}


	 	public function close_request_instanct_search()
	{
		$this->session->unset_userdata('product_order_search');
		redirect('inventory/s11');
	}
	public function close_request_deductions_search()
	{
		$this->session->unset_userdata('product_deduction_search');
		redirect('inventory/store-deductions');
	}

	public function search_store_deductions()
	{
		$product_name = $this->input->post('product_name');
		$drug_status = $this->input->post('drug_status');

		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}

		if(!empty($drug_status))
		{
			if($drug_status == 0)
			{
				$drug_status = '';
			}
			else if($drug_status == 1)
			{
				$drug_status = ' AND (product_deductions.quantity_given IS NULL OR product_deductions.quantity_given =0) AND product_deductions.product_deduction_rejected <> 1';
			}
			else if($drug_status == 2)
			{
				$drug_status = ' AND product_deductions.quantity_given  > 0 ';
			}
			else if($drug_status == 3)
			{
				$drug_status = ' AND product_deductions.product_deduction_rejected = 1 ';
			}
		}
		else
		{
			$drug_status = '';
		}



		$search = $product_name.$drug_status;
		$this->session->set_userdata('product_deduction_search', $search);
		redirect('inventory/store-deductions');
	}

	public function search_product_requested()
	{
		$product_name = $this->input->post('product_name');

		if(!empty($product_name))
		{
			$product_name = ' AND product.product_name LIKE \'%'.$product_name.'%\' ';
		}
		else
		{
			$product_name = '';
		}
		$search = $product_name;
		$this->session->set_userdata('product_price_search', $search);
		redirect('inventory/drug-prices');
	}
	public function close_request_prices_search()
	{
		$this->session->unset_userdata('product_price_search');
		redirect('inventory/drug-prices');
	}

	/*
	*
	*	Default action is to show all the products
	*
	*/
	public function drug_prices()
	{
		$where = 'product.category_id = category.category_id AND product.product_deleted = 0 ';
		$table = 'product, category';

		$product_search = $this->session->userdata('product_price_search');

		if(!empty($product_search))
		{
			$where .= $product_search;
		}
		$segment = 3;
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'inventory/drug-prices';
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
		$query = $this->products_model->get_all_drug_prices($table, $where, $config["per_page"], $page);

		$v_data['query'] = $query;
		$v_data['page'] = $page;
		$v_data['title'] = 'All Products';
		$v_data['all_categories'] = $this->categories_model->all_categories();
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();

		$data['content'] = $this->load->view('products/all_drug_prices', $v_data, true);


		$data['title'] = 'All Products';

		$this->load->view('admin/templates/general_page', $data);
	}

	public function close_order($order_id)
	{
		$array['order_approval_status'] = 1;
		$this->db->where('order_id',$order_id);
		$this->db->update('orders',$array);

		echo json_decode("true");
	}

	public function update_current_stock($product_id,$store_id)
	{
		$this->form_validation->set_rules('amount', 'Opening Stock', 'required|xss_clean');
		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->update_current_stock($product_id,$store_id))
			{
				$this->session->userdata('success_message', 'Product has been updated successfully');

			}

			else
			{
				$this->session->userdata('error_message', 'Unable to update product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}
		redirect('inventory/products');

	}
	public function update_product_prices($product_id)
	{
		$this->form_validation->set_rules('product_unitprice', 'Product Price', 'required|xss_clean');
		$this->form_validation->set_rules('vatable', 'Vatable', 'required|xss_clean');
		//if form conatins valid data
		if ($this->form_validation->run())
		{

			if($this->inventory_management_model->update_product_price($product_id))
			{
				$this->session->userdata('success_message', 'Product has been updated successfully');

			}

			else
			{
				$this->session->userdata('error_message', 'Unable to update product. Please try again');
			}
		}

		else
		{
			$v_data['validation_errors'] = validation_errors();
		}
		redirect('inventory/drug-prices');

	}

	public function add_product_to_store($product_id,$store_id)
	{
		$this->db->where('product_id = '.$product_id.' AND owning_store_id = '.$store_id.'');
		$query = $this->db->get('store_product');

		if($query->num_rows() > 0)
		{
			$this->session->userdata('error_message', 'The product is already assigned to the store');
		}
		else
		{
			$array['product_id'] = $product_id;
			$array['owning_store_id'] = $store_id;
			$array['created'] = date('Y-m-d');
			$array['created_by'] = $this->session->userdata('personnel_id');

			if($this->db->insert('store_product',$array))
			{
				$this->session->userdata('success_message', 'Product has been added successfully to the store');

			}

			else
			{
				$this->session->userdata('error_message', 'Unable to add the product to the store');
			}
		}


		redirect('inventory/products');

	}
	public function drug_trail($drug_id)
	{

		$v_data['title'] = 'Drug Trail';
		$v_data['drug_id'] = $drug_id;

		// $v_data['all_categories'] = $this->categories_model->all_categories();
		$data['content'] = $this->load->view('products/drug_trail', $v_data, true);

		$data['title'] = 'All Products';

		$this->load->view('admin/templates/general_page', $data);


	}


	public function print_drug_trails($drug_id)
	{

		$v_data['title'] = 'Drug Trail';
		$this->db->where('product_id = '.$drug_id);
		$query = $this->db->get('product');
		$product_name = '';
		if($query->num_rows() > 0)
		{

			foreach ($query->result() as $key => $value) {
				# code...
				$product_name = $value->product_name;
			}
		}

		$data['contacts'] = $this->site_model->get_contacts();
		$data['title'] =  $product_name;
		$data['drug_id'] = $drug_id;
		$this->load->view('products/print_order_trails', $data);


	}

	public function print_stock_take()
	{

		$v_data['title'] = 'Drug Trail';
		

		$data['contacts'] = $this->site_model->get_contacts();
		$data['title'] =  '';
		$data['drug_id'] = '';
		$this->load->view('products/print_stock_take', $data);


	}

	public function create_accounts()
	{
		$this->db->where('owning_store_id = 15 AND store_product.product_id IN (SELECT product_id FROM product) ');
		// $this->db->limit(1);
		$query = $this->db->get('store_product');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$product_id = $value->product_id;
				$store_product_id = $value->store_product_id;
				$array['product_id'] = $product_id;
				$array['owning_store_id'] = 5;
				$array['store_quantity'] = 0;
				$array['stock_take'] = NULL;
				$array['created'] = date('Y-m-d H:i:s');
				$this->db->insert('store_product',$array);

				$array_update['stock_take'] = NULL;
				$this->db->where('store_product_id',$store_product_id);
				$this->db->update('store_product',$array_update);

			}
		}
	}

}
