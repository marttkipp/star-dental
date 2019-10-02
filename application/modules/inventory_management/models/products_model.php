<?php

class Products_model extends CI_Model 
{	
	/*
	*	Retrieve all products
	*
	*/
	public function all_products($store_id=null)
	{

		if(!empty($store_id))
		{
			$where  =' AND store_id ='.$store_id;
		}
		else
		{
			$where = '';
		}
		$this->db->where('product_status = 1 AND product.product_deleted = 0 '.$where);
		$query = $this->db->get('product');
		
		return $query;
	}
	
	public function get_all_products($table, $where, $per_page, $page, $limit = NULL, $order_by = 'product.created', $order_method = 'DESC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('product.*, category.*, store.*');
		$this->db->where($where);
		$this->db->order_by($order_by, $order_method);
		
		if(isset($limit))
		{
			$query = $this->db->get('', $limit);
		}
		
		else
		{
			$query = $this->db->get('', $per_page, $page);
		}
		
		return $query;
	}


	public function get_all_products_sales($table, $where, $per_page, $page, $limit = NULL, $order_by = 'visit_charge.visit_charge_id', $order_method = 'DESC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('service_charge.*, visit_charge.*, patients.patient_surname,patients.patient_othernames,patients.patient_id,visit_charge.date AS charge_date,visit_charge.time AS charge_time, visit_charge.modified_by AS charge_modified_by, visit_charge.created_by AS charge_created_by ');
		$this->db->where($where);
		$this->db->order_by($order_by, $order_method);
		
		if(isset($limit))
		{
			$query = $this->db->get('', $limit);
		}
		
		else
		{
			$query = $this->db->get('', $per_page, $page);
		}
		
		return $query;
	}

	
	public function add_product()
	{
		
		$code = $this->create_product_code($this->input->post('category_id'));
		
		$data = array(
				'product_name'=>ucwords(strtolower($this->input->post('product_name'))),
				'product_status'=>$this->input->post('product_status'),
				'product_description'=>$this->input->post('product_description'),
				'category_id'=>$this->input->post('category_id'),
				'creditor_id'=>$this->input->post('creditor_id'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id'),
			);
			
		if($this->db->insert('product', $data))
		{
			
			return TRUE;
		}
		else{
			return FALSE;
		}
		
	}
	/*
	*	Update an existing product
	*	@param string $image_name
	*	@param int $product_id
	*
	*/
	public function update_product($product_id)
	{
		$data = array(
				'product_name'=>ucwords(strtolower($this->input->post('product_name'))),
				'product_status'=>$this->input->post('product_status'),
				'product_description'=>$this->input->post('product_description'),
				'category_id'=>$this->input->post('category_id'),
				'creditor_id'=>$this->input->post('creditor_id'),
				'created'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('personnel_id'),
				'modified_by'=>$this->session->userdata('personnel_id'),
			);
			
		$this->db->where('product_id', $product_id);
		if($this->db->update('product', $data))
		{
			//save locations
			
			
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	
	
	/*
	*	get a single product's details
	*	@param int $product_id
	*
	*/
	public function get_product($product_id, $personnel_id = NULL)
	{
		//retrieve all users
		$this->db->from('product, category');
		$this->db->select('product.*, category.category_name');
		
		if($personnel_id == NULL)
		{
			$this->db->where('product.category_id = category.category_id AND product_id = '.$product_id);
		}
		
		else
		{
			$this->db->where('product.category_id = category.category_id AND product_id = '.$product_id.' AND product.created_by = '.$personnel_id);
		}
		$query = $this->db->get();
		
		return $query;
	}
	
	/*
	*	get a single product's details
	*	@param int $product_id
	*
	*/
	public function get_product_shipping($product_id, $personnel_id = NULL)
	{
		//retrieve all users
		$this->db->from('product');
		
		$this->db->where('product_id = '.$product_id.' AND product.created_by = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
	}
	public function recently_viewed_products()
	{
		//retrieve all users
		$this->db->from('product, category');
		$this->db->select('product.*, category.category_name');
		$this->db->where('product.category_id = category.category_id  AND product.product_status = 1');
		$this->db->order_by('product.last_viewed_date','desc');
		$query = $this->db->get('', 10);
		 
		return $query;
	}
	
	/*
	*	Delete an existing product
	*	@param int $product_id
	*
	*/
	public function delete_product($product_id)
	{
		if($this->db->delete('product', array('product_id' => $product_id)))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated product
	*	@param int $product_id
	*
	*/
	public function activate_product($product_id)
	{
		$data = array(
				'product_status' => 1
			);
		$this->db->where('product_id', $product_id);
		
		if($this->db->update('product', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated product
	*	@param int $product_id
	*
	*/
	public function deactivate_product($product_id)
	{
		$data = array(
				'product_status' => 0
			);
		$this->db->where('product_id', $product_id);
		
		if($this->db->update('product', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function create_product_code($category_id)
	{
		//get category_details
		$query = $this->categories_model->get_category($category_id);
		
		if($query->num_rows() > 0)
		{
			$result = $query->result();
			//$category_preffix =  $result[0]->category_preffix;
			$category_preffix =  $result[0]->category_preffix;
			
			//select product code
			$this->db->from('product');
			$this->db->select('MAX(product_code) AS number');
			$this->db->where("product_code LIKE '".$category_preffix."%'");
			$query = $this->db->get();
			
			if($query->num_rows() > 0)
			{
				$result = $query->result();
				$number =  $result[0]->number;
				$number++;//go to the next number
				
				if($number == 1){
					$number = $category_preffix."001";
				}
			}
			else{//start generating receipt numbers
				$number = $category_preffix."001";
			}
		}
		
		else
		{
			$number = '001';
		}
		
		return $number;
	}
	
	public function get_category_id($category_name)
	{
		$this->db->where('category_name = \''.$category_name.'\'');
		$query = $this->db->get('category');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$category_id = $row->category_id;
		}
		
		else
		{
			$category_id = '';
		}
		
		return $category_id;
	}
	public function get_creditor_id($creditor_name)
	{
		$this->db->where('creditor_name = \''.$creditor_name.'\'');
		$query = $this->db->get('creditor');
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$creditor_id = $row->creditor_id;
		}
		
		else
		{
			$creditor_id = '';
		}
		
		return $creditor_id;
	}

	/*
	*	Import Template
	*
	*/
	function import_template()
	{
		$this->load->library('excel');
		
		$title = 'Products Import Template V2';
		$count=0;
		$row_count=0;
		
		$this->db->where('category_status', 1);
		$query = $this->db->get('category');
		$categories = '';
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$category_name = $res->category_name;
				$categories .= $category_name.', ';
			}
		}
		
		$this->db->where('store_parent', 0);
		$query = $this->db->get('store');
		$stores = '';
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$store_name = $res->store_name;
				$stores .= $store_name.', ';
			}
		}
		
		$this->db->where('unit_status', 1);
		$this->db->order_by('unit_name');
		$query = $this->db->get('unit');
		$units = '';
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $res)
			{
				$unit_name = $res->unit_name;
				$units .= $unit_name.', ';
			}
		}
		$report[$row_count][$count] = 'Category ('.$categories.')';
		$count++;
		$report[$row_count][$count] = 'Store ('.$stores.')';
		$count++;
		$report[$row_count][$count] = 'Product Code';
		$count++;
		$report[$row_count][$count] = 'Product Name';
		$count++;
		$report[$row_count][$count] = 'Unit of measure ('.$units.')';
		$count++;
		$report[$row_count][$count] = 'Activate (Yes, No)';
		$count++;
		$report[$row_count][$count] = 'Buying Price';
		$count++;
		$report[$row_count][$count] = 'Selling Price';
		$count++;
		$report[$row_count][$count] = 'Pack Size';
		$count++;
		$report[$row_count][$count] = 'Opening Quantity';
		$count++;
		$report[$row_count][$count] = 'Min Reorder Level';
		$count++;
		$report[$row_count][$count] = 'Description';
		$count++;
		$report[$row_count][$count] = 'Max Reorder Level';
		$count++;
		$report[$row_count][$count] = 'Posting Group';
		$count++;
		$report[$row_count][$count] = 'Category Code';
		$count++;
		$report[$row_count][$count] = 'Group Code';
		
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	/*
	*	Import Categories
	*
	*/
	function import_categories()
	{
		//get vendors categories
		$visits_query = $this->vendor_categories();
		
		$this->load->library('excel');
		//var_dump($visits_query->result());die();
		$title = $this->session->userdata('vendor_name').' Product Categories';
		$count=1;
		$row_count=0;
		
		
		/*
			-----------------------------------------------------------------------------------------
			Document Header
			-----------------------------------------------------------------------------------------
		*/
		
		$report[$row_count][0] = '#';
		$report[$row_count][2] = 'Category';
		$row_count++;
		
		foreach ($visits_query->result() as $row)
		{
			$category_name = $row->category_name;
			
			$report[$row_count][0] = $count;
			$report[$row_count][2] = $category_name;
			
			$row_count++;
			$count++;	
		}
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	public function import_csv_products($upload_path)
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
			$response2 = $this->sort_csv_data($array);
		
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
	
	public function sort_csv_data($array)
	{
		//get vendors categories
		$this->db->where('category_status', 1);
		$categories_query = $this->db->get('category');
		
		//get stores
		$this->db->where('store_parent', 0);
		$stores_query = $this->db->get('store');
		
		//Get Units
		$this->db->where('unit_status', 1);
		$units_query = $this->db->get('unit');
		
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		$count = 0;
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 16))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$items['created_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-condensed table-striped table-hover">
					<tr>
						<th>#</th>
						<th>Category</th>
						<th>Product code</th>
						<th>Product name</th>
						<th>Unit of Measure</th>
						<th>Activate</th>
						<th>Buying price</th>
						<th>Selling price</th>
						<th>Opening Quantity</th>
						<th>Reorder Level</th>
						<th>Posting Group</th>
						<th>Category Code</th>
						<th>Group Code</th>
						<th>Description</th>
						<th>Comment</th>
					</tr>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$count = 0;
				$category_name = ucwords(strtolower($array[$r][$count]));
				$count++;
				$category_id = '';
				if($categories_query->num_rows() > 0)
				{
					foreach($categories_query->result() as $res)
					{
						$db_category_name = ucwords(strtolower($res->category_name));
						
						if($db_category_name == $category_name)
						{
							$category_id = $res->category_id;
						}
					}
				}
				//store
				$store_name = ucwords(strtolower($array[$r][$count]));
				$count++;
				$store_id = '';
				if($stores_query->num_rows() > 0)
				{
					foreach($stores_query->result() as $res)
					{
						$db_store_name = ucwords(strtolower($res->store_name));
						
						if($db_store_name == $store_name)
						{
							$store_id = $res->store_id;
						}
					}
				}
				$creditor_name = ucwords(strtolower($array[$r][$count]));
				$count++;
				$creditor_id = '';
				if($creditors_query->num_rows() > 0)
				{
					foreach($creditors_query->result() as $res)
					{
						$db_creditor_name = ucwords(strtolower($res->creditor_name));
						
						if($db_creditor_name == $creditor_name)
						{
							$creditor_id = $res->creditor_id;
						}
					}
				}
				$product_code = $array[$r][$count];
				if(empty($product_code))
				{
					$product_code = $this->inventory_management_model->create_product_code();
				}
				$items['product_code']  = $product_code;
				$count++;
				$items['product_name'] = $array[$r][$count];
				$count++;
				//units
				$unit_of_measure = ucwords(strtolower($array[$r][$count]));
				$unit_id = '';
				if($units_query->num_rows() > 0)
				{
					foreach($units_query->result() as $res)
					{
						$db_unit_name = ucwords(strtolower($res->unit_name));
						
						if($db_unit_name == $unit_of_measure)
						{
							$unit_id = $res->unit_id;
						}
					}
				}
				$items['unit_id'] = $unit_id;
				$count++;
				$active = ucwords(strtolower($array[$r][$count]));
				if($active == 'Yes')
				{
					$items['product_status'] = 1;
				}
				else
				{
					$items['product_status'] = 0;
				}
				$count++;
				$items['product_buying_price'] = $array[$r][$count];
				$count++;
				$items['product_unitprice'] = $array[$r][$count];
				$count++;
				$items['product_packsize'] = $array[$r][$count];
				$count++;
				$items['quantity'] = $array[$r][$count];
				$count++;
				$items['reorder_level'] = $array[$r][$count];
				$count++;
				$items['product_description'] = $array[$r][$count];
				$count++;
				$items['reorder_level'] = $items['max_reorder_level'] = $array[$r][$count];
				$count++;
				$items['posting_group'] = $array[$r][$count];
				$count++;
				$items['category_code'] = $array[$r][$count];
				$count++;
				$items['category_code'] = $array[$r][$count];
				$count++;
				$items['group_code'] = $array[$r][$count];
				$count++;
				$items['created'] = date('Y-m-d H:i:s');
				$comment = '';
				
				//get category_id
				//$category_id = $this->get_category_id($category_name);
				
				//only continue if category_id exists
				if(!empty($category_id))
				{
					$class = 'success';
					
					$items['category_id'] = $category_id;
					
					//generate product code
					if(empty($items['product_code']))
					{
						//$items['product_code'] = $this->create_product_code($category_id);
					}
					
					//get brand_id
					//$brand_id = $this->get_brand_id($brand_name);
					
					//validate buying price
					if((!is_numeric($items['product_buying_price'])) && (!empty($items['product_buying_price'])))
					{
						$class = 'warning';
						$comment .= '<br/>The buying price is not numeric. Product added with \'0\' as the buying price';
						$items['product_buying_price'] = 0;
					}
					
					else if(empty($items['product_buying_price']))
					{
						$items['product_buying_price'] = 0;
					}
					
					//validate selling price
					if((!is_numeric($items['product_unitprice'])) && (!empty($items['product_unitprice'])))
					{
						$class = 'warning';
						$comment .= '<br/>The selling price is not numeric. Product added with \'0\' as the selling price';
						$items['product_unitprice'] = 0;
					}
					
					else if(empty($items['product_unitprice']))
					{
						$items['product_unitprice'] = 0;
					}
					
					//validate sale price
					if((!is_numeric($items['reorder_level'])) && (!empty($items['reorder_level'])))
					{
						$class = 'warning';
						$comment .= '<br/>The reorder level is not numeric. Product added with \'0\' as the sale price';
						$items['reorder_level'] = 0;
					}
					
					else if(empty($items['reorder_level']))
					{
						$items['reorder_level'] = 0;
					}
					//validate sale price
					if((!is_numeric($items['max_reorder_level'])) && (!empty($items['max_reorder_level'])))
					{
						$class = 'warning';
						$comment .= '<br/>The maximum reorder level is not numeric. Product added with \'0\' as the sale price';
						$items['max_reorder_level'] = 0;
					}
					else if(empty($items['max_reorder_level']))
					{
						$items['max_reorder_level'] = 0;
					}
					//validate product balance
					if(!is_numeric($items['quantity']))
					{
						$class = 'warning';
						$comment .= '<br/>The opening quantity is not numeric. Product added with \'0\' as the balance';
						$items['quantity'] = 0;
					}
					
					else if(empty($items['quantity']))
					{
						$items['quantity'] = 0;
					}
					
					$items['store_id'] = 5;
					$items['is_synced'] = 0;
					$items['created'] = date('Y-m-d H:i:s');
					$items['created_by'] = $this->session->userdata('personnel_id');
					$items['modified_by'] = $this->session->userdata('personnel_id');
					
					//check for the system
					
					$checker = $this->check_product_exisit($product_code);
					
					if($checker == FALSE)
					{
						//save product in the db
						if($this->db->insert('product', $items))
						{
							//add product sore
							if(!empty($store_id))
							{
								$product_store = array(
									'product_id' => $this->db->insert_id(),
									'store_id' => $store_id
								);
								if($this->db->insert('store_product', $product_store))
								{
								}
							}
							$comment .= '<br/>Product successfully added to the database';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add product to the database. Please contact the site administrator. Product code '.$items['product_code'];
						}
					}
					else
					{
					//save product in the db
					    $product_id = $checker;
						$items['is_synced'] = 0;
						$this->db->where('product_id',$product_id);
						if($this->db->update('product', $items))
						{
							//add product sore
							if(!empty($store_id))
							{
								$product_store = array(
									'product_id' => $this->db->insert_id(),
									'store_id' => $store_id
								);
								if($this->db->insert('store_product', $product_store))
								{
								}
							}
							$comment .= '<br/>Product successfully added to the database';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not add product to the database. Please contact the site administrator. Product code '.$items['product_code'];
						}
					
					}
				}
				
				else
				{
					$class = 'danger';
					$comment = 'Unable to save product. Category not available. Please download the list of available categories <a href="'.site_url().'vendor/import-categories">here.</a>';
				}
				
				$response .= '
					<tr class="'.$class.'">
						<td>'.$r.'</td>
						<td>'.$category_name.'</td>
						<td>'.$items['product_name'].'</td>
						<td>'.$unit_of_measure.'</td>
						<td>'.$active.'</td>
						<td>'.$items['product_buying_price'].'</td>
						<td>'.$items['product_unitprice'].'</td>
						<td>'.$items['quantity'].'</td>
						<td>'.$items['reorder_level'].'</td>
						<td>'.$items['posting_group'].'</td>
						<td>'.$items['category_code'].'</td>
						<td>'.$items['group_code'].'</td>
						<td>'.implode(' ', array_slice(explode(' ', $items['product_description']), 0, 10)).'...</td>
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
	
	/*
	*	Import Template
	*
	*/
	function opening_balance_import_template()
	{
		$this->load->library('excel');
		
		$title = 'Products Opening Balance Import Template V1';
		$count=0;
		$row_count=0;
		$report[$row_count][$count] = 'Product Code';
		$count++;
		$report[$row_count][$count] = 'Opening Quantity';
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	public function import_csv_balances($upload_path)
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
			$response2 = $this->sort_balances_data($array);
		
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
	
	public function sort_balances_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		$count = 0;
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 2))
		{
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-condensed table-striped table-hover">
					<tr>
						<th>#</th>
						<th>Product code</th>
						<th>Opening Quantity</th>
						<th>Comment</th>
					</tr>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$count = 0;
				$items['product_code']  = $product_code = $array[$r][$count];
				$count++;
				$items['quantity'] = $array[$r][$count];
				$count++;
				$comment = '';
				
				//only continue if category_id exists
				if(!empty($product_code))
				{
					$class = 'success';
					
					//validate product balance
					if(!is_numeric($items['quantity']))
					{
						$class = 'warning';
						$comment .= '<br/>The opening quantity is not numeric. Product added with \'0\' as the balance';
						$items['quantity'] = 0;
					}
					
					else if(empty($items['quantity']))
					{
						$items['quantity'] = 0;
					}
					
					//check for the system
					$checker = $this->check_product_exisit($product_code);
					
					if($checker == FALSE)
					{
						$class = 'danger';
						$comment .= '<br/>The item has does not exist. Please add the item first';
						$items['quantity'] = 0;
					}
					else
					{
					//save product in the db
					    $product_id = $checker;
						$this->db->where('product_id',$product_id);
						if($this->db->update('product', $items))
						{
							$comment .= '<br/>Balance successfully updated in the database';
						}
						
						else
						{
							$class = 'danger';
							$comment .= '<br/>Internal error. Could not add product to the database. Please contact the site administrator. Product code '.$items['product_code'];
						}
					}
				}
				
				else
				{
					$class = 'danger';
					$comment = 'Product code not available';
				}
				
				$response .= '
					<tr class="'.$class.'">
						<td>'.$r.'</td>
						<td>'.$product_code.'</td>
						<td>'.$items['quantity'].'</td>
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
	public function check_product_exisit($product_code)
	{
		$this->db->where('product_code',$product_code);
		$query = $this->db->get('product');
		if($query->num_rows() > 0)
		{
			foreach($query->result() AS $key)
			{
			   $product_id = $key->product_id;
			}
			return $product_id;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_drugs_deductions($table, $where, $per_page, $page, $order)
	{
		//retrieve all purchases
		$this->db->from($table);
		$this->db->select('product_deductions.*');
		$this->db->where($where);
		$this->db->order_by($order,'DESC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	public function get_product_details($product_id)
	{
		$this->db->where('product_id', $product_id);
		$query = $this->db->get('product');
		
		return $query;
	}
	public function get_all_products_out_of_stock($table, $where,$order_by = 'product.created', $order_method = 'DESC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order_by, $order_method);
		
		
		$query = $this->db->get('');
	
		
		return $query;
	}
	public function import_product_codes_template()
	{
		$this->load->library('Excel');
		
		$title = 'Product Codes Import Template';
		$count=1;
		$row_count=0;
		
		$product_codes[$row_count][0] = 'Product Name';
		$product_codes[$row_count][1] = 'Product Code';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray($product_codes);
		$this->excel->generateXML ($title);
	
	}
	public function import_csv_product_codes($upload_path)
	{
		$this->load->model('admin/file_model');
		
		$response = $this->file_model->upload_csv($upload_path, 'import_csv');
		
		if($response['check'])
		{
			$file_name = $response['file_name'];
			
			$array = $this->file_model->get_array_from_csv($upload_path.'/'.$file_name);
			//var_dump($array); die();
			$response2 = $this->sort_product_code_data($array);
		
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
	function sort_product_code_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array[0]);die();
		
		//if branch exist in array
		if(($total_rows > 0) && ($total_columns == 2))
		{
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Product Name</th>
						  <th>Product Code</th>
						  <th>Comment</th>
						</tr>
					  </thead>
					  <tbody>
			';
			for($r = 1; $r < $total_rows; $r++)
			{
				$product_name = $array[$r][0];
				$product_code = $items['mtiba_code']=$array[$r][1];
				$comment = '';
				if($this->product_exists($product_name) == TRUE)
				{
					if((!empty($product_name)) &&(!empty($items['mtiba_code'])))
					{
						$where = "service_charge_name = '".addslashes($product_name)."'";
						
						$this->db->where($where);
						if($this->db->update('service_charge', $items))
						{
							$comment .= '<br/>Service charge code '.$product_code.' successfully updated for '.$product_name.' service';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Internal error. Could not update product code for the service';
							$class = 'warning';
						}
					}
					
					else
					{
						$comment .= '<br/>Not saved ensure you have a product name entered';
						$class = 'danger';
					}
				}
				else
				{
					$comment .= '<br/>The product '.$product_name.' could not be found in the database.';
					$class = 'danger';
				}
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$r.'</td>
							<td>'.$product_name.'</td>
							<td>'.$items['mtiba_code'].'</td>
							<td>'.$comment.'</td>
						</tr> 
				';
			}
			
			$response .= '</table>';
			
			$return['response'] = $response;
			$return['check'] = TRUE;
		}
		else
		{
			$return['response'] = 'Product data not found ';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	function product_exists($product_name)
	{
		$this->db->where('service_charge_name = "'.addslashes($product_name).'"');
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
}
?>