<?php

class Products_model extends CI_Model 
{	
	/*
	*	Retrieve all products
	*
	*/
	public function all_products($store_id=null)
	{
		// var_dump($store_id); die();
		if(!empty($store_id))
		{
			$where  =' AND store_product.owning_store_id ='.$store_id;
		}
		else
		{
			$where = '';
		}
		$this->db->where('product_status = 1 AND store_product.product_id = product.product_id AND product.stock_take = 1  AND product.product_deleted = 0 '.$where);
		$query = $this->db->get('product,store_product');
		
		return $query;
	}
	
	public function get_all_products($table, $where, $per_page, $page, $limit = NULL, $order_by = 'product.created', $order_method = 'DESC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('product.*,product.stock_take as stock_take, category.*, store.*,store_product.store_product_id,store_product.owning_store_id,store_product.store_quantity');
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


	public function get_all_products_sales_old($table, $where, $per_page, $page, $limit = NULL, $order_by = 'visit.close_card,visit.visit_type', $order_method = 'ASC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('service_charge.*, visit_charge.*, patients.patient_surname,patients.patient_othernames,patients.patient_id,visit_charge.date AS charge_date,visit_charge.time AS charge_time, visit_charge.modified_by AS charge_modified_by, visit_charge.created_by AS charge_created_by,visit.close_card,visit.visit_id,visit_type.visit_type_name');
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
		$report[$row_count][$count] = 'Vatable (Yes/ No)';
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
		$this->db->where('store_id > 0');
		$stores_query = $this->db->get('store');
		
		//Get Units
		$this->db->where('unit_status', 1);
		$units_query = $this->db->get('unit');
		
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);

		// var_dump($total_columns); die();
		$count = 0;
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 17))
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
									<th>Vatable (Yes / No)</th>
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
				// $creditor_name = ucwords(strtolower($array[$r][$count]));
				// $count++;
				// $creditor_id = '';
				// if($creditors_query->num_rows() > 0)
				// {
				// 	foreach($creditors_query->result() as $res)
				// 	{
				// 		$db_creditor_name = ucwords(strtolower($res->creditor_name));
						
				// 		if($db_creditor_name == $creditor_name)
				// 		{
				// 			$creditor_id = $res->creditor_id;
				// 		}
				// 	}
				// }
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
				$items['vatable'] = $array[$r][$count];
				$count++;
				// $items['group_code'] = $array[$r][$count];
				// $count++;
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
					
					$items['store_id'] = $store_id;
					$items['is_synced'] = 0;
					$items['created'] = date('Y-m-d H:i:s');
					$items['created_by'] = $this->session->userdata('personnel_id');
					$items['modified_by'] = $this->session->userdata('personnel_id');
					
					//check for the system
					$product_name = $items['product_name'];
					$checker = $this->check_product_exisit($product_code);
					// $checker = FALSE;
					if($checker == FALSE)
					{
						//save product in the db
						if($this->db->insert('product', $items))
						{
							$product_idd = $this->db->insert_id();
							//add product sore
							if(!empty($store_id))
							{
								// $this->db->where('product_id = '.$product_idd.' AND owning_store_id = '.$store_id);
								// $store_query = $this->db->get('store_product');
								$product_store = array(
									'product_id' => $product_idd,
									'owning_store_id' => $store_id,
									'store_quantity' => $items['quantity']
								);
								// var_dump($product_store);
								// if($store_query->num_rows() > 0)
								// {
								// 	foreach ($store_query->result() as $key => $value_row) {
								// 			# code...
								// 		$store_product_id = $value_row->store_product_id;
								// 	}	
								// 	$this->db->update('store_product', $product_store);
									

								// }
								// else
								// {
									if($this->db->insert('store_product', $product_store))
									{
										
									}

								// }
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

					   //add product sore
						if(!empty($store_id))
						{
							// $this->db->where('product_id = '.$product_idd.' AND owning_store_id = '.$store_id);
							// $store_query = $this->db->get('store_product');
							$product_store = array(
								'product_id' => $product_id,
								'owning_store_id' => $store_id,
								'store_quantity' => $items['quantity']
							);
							// var_dump($product_store);
							// if($store_query->num_rows() > 0)
							// {
							// 	foreach ($store_query->result() as $key => $value_row) {
							// 			# code...
							// 		$store_product_id = $value_row->store_product_id;
							// 	}	
							// 	$this->db->update('store_product', $product_store);
								

							// }
							// else
							// {
								if($this->db->insert('store_product', $product_store))
								{
									
								}

							// }
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
		$this->db->where('product_code = "'.$product_code.'" AND product_deleted = 0 ');
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

	public function check_product_exisit_name($product_name)
	{
		$this->db->where('product_name = "'.$product_name.'" AND product_deleted = 0');
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
		$this->db->select('product_deductions_stock.*');
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
	public function get_all_products_download($table, $where)
	{
		$this->db->select('product.*, category.*');
		$this->db->where($where);
		$query = $this->db->get($table);
		
		return $query;
	}
	public function get_all_drug_prices($table, $where, $per_page, $page, $limit = NULL, $order_by = 'product.product_unitprice', $order_method = 'ASC')
	{
		//var_dump($table);
		//var_dump($where);die();
		$this->db->from($table);
		$this->db->select('product.*, category.*');
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

	public function get_product_sales($drug_id)
	{
		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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
		

		$where = 'visit.patient_id = patients.patient_id AND service_charge.service_charge_id = visit_charge.service_charge_id AND service_charge.service_charge_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit_charge.charged = 1 AND visit_type.visit_type_id = visit.visit_type AND visit.visit_id = visit_charge.visit_id AND visit_charge.product_id = '.$drug_id;
		$table = 'visit,patients,service_charge,visit_charge,visit_type';


		$this->db->from($table);
		$this->db->select('visit_charge.visit_id AS invoice_id, visit.invoice_number AS invoice_number,visit_charge.visit_charge_timestamp AS visit_date,visit_charge.visit_charge_units AS quantity, patients.patient_surname, patients.patient_othernames,patients.patient_type,visit_type.visit_type_name,visit_charge.visit_charge_comment');
		$this->db->where($where);

		$this->db->order_by('visit_date','ASC');
		$this->db->group_by('visit_charge.visit_id');
		$query = $this->db->get();
		return $query;
	}
	public function get_all_drug_purchases($drug_id)
	{
		


		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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
		

		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0 AND creditor.creditor_id = orders.supplier_id AND order_item.product_id = ".$drug_id;
		$table = 'order_item,order_supplier,product,orders,creditor';


		$this->db->from($table);
		$this->db->select('orders.order_id AS invoice_id, orders.supplier_invoice_number AS receipt_number,orders.supplier_invoice_date AS purchase_date,(order_supplier.quantity_received * order_supplier.pack_size) AS received_quantity,creditor.creditor_name AS description');
		$this->db->where($where);
		$this->db->order_by('orders.supplier_invoice_date','ASC');
		$this->db->group_by('orders.supplier_invoice_number');
		$query = $this->db->get();
		return $query;
	}
	

	public function get_all_store_deductions($drug_id)
	{
		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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


		$table = "product_deductions, store, product, orders";
		$where = "product_deductions.store_id = store.store_id AND product_deductions.quantity_requested > 0  AND product_deductions.product_id = product.product_id AND product_deductions.order_id = orders.order_id AND orders.order_id = product_deductions.order_id AND is_store = 1 AND product_deductions.product_deduction_rejected = 0 AND product.product_id =".$drug_id;
		$order = "product_deductions_pack_size";
		
		$this->db->from($table);
		$this->db->select('orders.order_id AS invoice_id, orders.order_number AS deduction_number,orders.orders_date AS deduction_date, product_deductions.quantity_given AS deducted_quantity,store.store_name AS description');
		$this->db->where($where);
		$this->db->order_by('orders.orders_date','ASC');
		$query = $this->db->get();
		return $query;
	}

	public function get_all_store_transfers($drug_id)
	{
		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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
		$where = "product_deductions.order_id = orders.order_id AND product_deductions.product_id = product.product_id AND orders.supplier_id > 0 AND creditor.creditor_id = orders.supplier_id AND orders.is_store = 2 AND orders.order_approval_status = 7 AND product.product_id = ".$drug_id;
		$table = 'product_deductions,product,orders,creditor';
		
		$this->db->from($table);
		$this->db->select('orders.order_id AS invoice_id, orders.order_number AS deduction_number,orders.orders_date AS deduction_date, (product_deductions.quantity_given * product_deductions.pack_size) AS deducted_quantity,creditor.creditor_name AS description');
		$this->db->where($where);
		$this->db->order_by('orders.orders_date','ASC');
		$query = $this->db->get();
		return $query;
	}
	public function get_all_store_credit_note($drug_id)
	{
		


		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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

		$where = "order_item.order_item_id = order_supplier.order_item_id AND order_item.product_id = product.product_id AND orders.order_id = order_item.order_id  AND product.product_deleted = 0 AND orders.supplier_id > 0 AND orders.is_store = 3 AND product.product_id = ".$drug_id;
		$table = 'order_item,order_supplier,product,orders';
		$select = 'SUM(quantity_received*pack_size) AS total_purchases';
		
		
		$this->db->from($table);
		$this->db->select('orders.order_id AS invoice_id, orders.supplier_invoice_number AS receipt_number,orders.supplier_invoice_date AS purchase_date,(order_supplier.quantity_received * order_supplier.pack_size) AS received_quantity');
		$this->db->where($where);
		$this->db->order_by('orders.supplier_invoice_date','ASC');
		$this->db->group_by('order_supplier.order_supplier_id');
		$query = $this->db->get();
		return $query;
	}
	public function get_product_opening_stock($product_id)
	{

		$this->db->from('store_product');
		$this->db->select('store_product.store_product_id AS invoice_id, store_product.store_product_id AS receipt_number,store_product.created AS stock_take_date,store_product.store_quantity AS opening_quantity');
		$this->db->where('product_id = '.$product_id);
		$this->db->order_by('store_product.created','ASC');
		$query = $this->db->get();
		return $query;
	}
	public function get_all_drug_additions($drug_id)
	{

		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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
		$where = "product_purchase.product_id = ".$drug_id;
		$table = 'product_purchase';
		
		$this->db->from($table);
		$this->db->select('product_purchase.purchase_id AS invoice_id, product_purchase.purchase_id AS deduction_number,product_purchase.purchase_date AS date_added, (purchase_quantity * purchase_pack_size) AS added_quantity,purchase_description as description');
		$this->db->where($where);
		$this->db->order_by('product_purchase.purchase_date','ASC');
		$query = $this->db->get();
		return $query;

	}

	public function get_all_drug_deductions($drug_id)
	{

		$date_from = $this->session->userdata('creditor_date_from');
		$date_to = $this->session->userdata('creditor_date_to');

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
		$where = "product_deductions_stock.product_id = ".$drug_id;
		$table = 'product_deductions_stock';
		
		$this->db->from($table);
		$this->db->select('product_deductions_stock.product_deductions_stock_id AS invoice_id, product_deductions_stock.product_deductions_stock_id AS deduction_number,product_deductions_stock.product_deductions_stock_date AS date_added, (product_deductions_stock_quantity * product_deductions_stock_pack_size) AS added_quantity,deduction_description as description');
		$this->db->where($where);
		$this->db->order_by('product_deductions_stock.product_deductions_stock_date','ASC');
		$query = $this->db->get();
		return $query;

	}
	public function get_product_trail($drug_id)
	{

		$bills = $this->get_product_sales($drug_id);
		$opening_stock = $this->get_product_opening_stock($drug_id);
		$drug_purchases = $this->get_all_drug_purchases($drug_id);
		$drug_additions = $this->get_all_drug_additions($drug_id);
		$stock_trail = $this->get_all_store_deductions($drug_id);
		$drug_transfers = $this->get_all_store_transfers($drug_id);
		$drug_credit_note = $this->get_all_store_credit_note($drug_id);
		$drug_deductions = $this->get_all_drug_deductions($drug_id);



		$x=0;

		$bills_result = '';
		$last_date = '';
		$current_year = date('Y');
		$total_invoices = $bills->num_rows();
		$invoices_count = 0;
		$total_invoice_balance = 0;
		$total_arrears = 0;
		$total_payment_amount = 0;
		$result = '';
		$total_credit_notes_amount = 0;

		if($bills->num_rows() > 0)
		{
			foreach ($bills->result() as $supplier) {
				# code...
				$invoice_date_bill = $supplier->visit_date;
				$supplier_invoice_number = $supplier->invoice_id;
				$quantity = $supplier->quantity;
				$invoice_number = $supplier->invoice_number;
				$patient_surname = $supplier->patient_surname;
				$patient_type = $supplier->patient_type;
				$patient_othernames = $supplier->patient_othernames;
				$visit_type_name = $supplier->visit_type_name;
				$visit_charge_comment = $supplier->visit_charge_comment;
				$patients = $patient_surname.' '.$patient_othernames.' - '.$visit_type_name;
				$invoice_explode = explode('-', $invoice_date_bill);
				$drug_sale_description = strtoupper($patients);
				$invoices_count++;
				if($opening_stock->num_rows() > 0)
				{
					foreach ($opening_stock->result() as $opening_stock_value) {
						# code...
						$stock_take_date = $opening_stock_value->stock_take_date;
						$supplier_invoice_number = $opening_stock_value->invoice_id;
						$opening_quantity = $opening_stock_value->opening_quantity;
						$receipt_number = $opening_stock_value->receipt_number;


						if(($stock_take_date <= $invoice_date_bill) && ($stock_take_date > $last_date) && ($opening_quantity > 0))
						{
							$total_arrears += $opening_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($stock_take_date)).' </td>
									<td>-</td>
									<td>STOCK TAKE</td>
									<td>-</td>
									<td>'.$opening_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $opening_quantity;

						}


					}
				}


				if($drug_additions->num_rows() > 0)
				{
					foreach ($drug_additions->result() as $additions_key) {
						# code...
						$date_added = $additions_key->date_added;
						$supplier_invoice_number = $additions_key->invoice_id;
						$added_quantity = $additions_key->added_quantity;
						$receipt_number = '-';
						$addition_description = $additions_key->description;


						if(($date_added <= $invoice_date_bill) && ($date_added > $last_date) && ($added_quantity > 0))
						{
							$total_arrears += $added_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_added)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>ADDITION</td>
									<td>'.strtoupper($addition_description).'</td>
									<td>'.$added_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $added_quantity;

						}


					}
				}
				if($drug_purchases->num_rows() > 0)
				{
					foreach ($drug_purchases->result() as $payments_key) {
						# code...
						$purchase_date = $payments_key->purchase_date;
						$supplier_invoice_number = $payments_key->invoice_id;
						$received_quantity = $payments_key->received_quantity;
						$receipt_number = $payments_key->receipt_number;
						$creditor_name = $payments_key->description;


						if(($purchase_date <= $invoice_date_bill) && ($purchase_date > $last_date) && ($received_quantity > 0))
						{
							$total_arrears += $received_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($purchase_date)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>PURCHASE</td>
									<td>'.strtoupper($creditor_name).'</td>
									<td>'.$received_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $received_quantity;

						}


					}
				}

				if(($quantity > 0))
				{
					$total_arrears -= $quantity;
					$total_invoice_balance -= $quantity;

					if($patient_type == 0)
					{
						$description = $drug_sale_description.'<br> LOCATION : '.$visit_charge_comment;
					}
					else
					{
						$description = 'WALKIN <br> LOCATION : '.$visit_charge_comment;
					}
				
						$result .= 
						'
							<tr>
								<td>'.date('d M Y',strtotime($invoice_date_bill)).' </td>
								<td>'.strtoupper($invoice_number).'</td>
								<td>SALES</td>
								<td>'.$description.'</td>
								<td></td>
								<td>'.$quantity.'</td>
								<td>'.$total_arrears.'</td>
							</tr> 
						';
					
				}


				

				if($drug_transfers->num_rows() > 0)
				{
					foreach ($drug_transfers->result() as $transfers) {
						# code...
						$transfer_date = $transfers->deduction_date;
						$supplier_invoice_number = $transfers->invoice_id;
						$transfered_quantity = $transfers->deducted_quantity;
						$transfer_number = $transfers->deduction_number;
						$transfer_description = $transfers->description;


						if(($transfer_date <= $invoice_date_bill) && ($transfer_date > $last_date) && ($transfered_quantity > 0))
						{

							$total_arrears -= $transfered_quantity;
							$total_invoice_balance -= $transfered_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($transfer_date)).' </td>
									<td>'.strtoupper($transfer_number).'</td>
									<td>TRANSFER</td>
									<td>MAIN STORE - '.strtoupper($transfer_description).'</td>
									<td></td>
									<td>'.$transfered_quantity.'</td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							

						}
					}
				}


				if($drug_credit_note->num_rows() > 0)
				{
					foreach ($drug_credit_note->result() as $credit_note) {
						# code...
						$credit_date = $credit_note->purchase_date;
						$supplier_invoice_number = $credit_note->invoice_id;
						$credited_quantity = $credit_note->received_quantity;
						$credit_number = $credit_note->receipt_number;


						if(($credit_date <= $invoice_date_bill) && ($credit_date > $last_date) && ($credited_quantity > 0))
						{

							$total_arrears -= $credited_quantity;
							$total_invoice_balance -= $credited_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($credit_date)).' </td>
									<td>'.strtoupper($credit_number).'</td>
									<td>CREDIT NOTE</td>
									<td>STOCK TAKE</td>
									<td></td>
									<td>'.$credited_quantity.'</td>
									<td>'.abs($total_arrears).'</td>
								</tr> 
							';
							

						}
					}
				}
				if($drug_deductions->num_rows() > 0)
				{
					foreach ($drug_deductions->result() as $deductions_key) {
						# code...
						$date_deducted = $deductions_key->date_added;
						$supplier_invoice_number = $deductions_key->invoice_id;
						$deducted_quantity = $deductions_key->added_quantity;
						$receipt_number = '-';
						$deduction_description = $deductions_key->description;


						if(($date_deducted <= $invoice_date_bill) && ($date_deducted > $last_date) && ($deducted_quantity > 0))
						{
							$total_arrears -= $deducted_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_deducted)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>DEDUCTION</td>
									<td>'.strtoupper($deduction_description).'</td>
									<td>'.$deducted_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_invoice_balance += $deducted_quantity;

						}

					}
				}


				
				if($stock_trail->num_rows() > 0)
				{
					foreach ($stock_trail->result() as $trail_key) {
						# code...
						$deduction_date = $trail_key->deduction_date;
						$supplier_invoice_number = $trail_key->invoice_id;
						$deducted_quantity = $trail_key->deducted_quantity;
						$receipt_number = $trail_key->deduction_number;
						$deduction_description = $trail_key->description;


						if(($deduction_date <= $invoice_date_bill) && ($deduction_date > $last_date) && ($deducted_quantity > 0))
						{
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($deduction_date)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>STORE DEDUCTION</td>
									<td>MAIN STORE - '.strtoupper($deduction_description).' TRANSFER</td>
									<td></td>
									<td>'.$deducted_quantity.'</td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							

						}

					}
				}
				
			  $last_date = $invoice_date_bill;
			}


			if($total_invoices == $invoices_count)
			{
				if($drug_purchases->num_rows() > 0)
				{
					foreach ($drug_purchases->result() as $payments_key) {
						# code...
						$purchase_date = $payments_key->purchase_date;
						$supplier_invoice_number = $payments_key->invoice_id;
						$received_quantity = $payments_key->received_quantity;
						$receipt_number = $payments_key->receipt_number;
						$creditor_name = $payments_key->description;

						if(($received_quantity > 0) && ($purchase_date > $invoice_date_bill))
						{
						
							$total_arrears += $received_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($purchase_date)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>PURCHASE</td>
									<td>'.strtoupper($creditor_name).'</td>
									<td>'.$received_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $received_quantity;

						}


					}
				}

				if($drug_additions->num_rows() > 0)
				{
					foreach ($drug_additions->result() as $additions_key) {
						# code...
						$date_added = $additions_key->date_added;
						$supplier_invoice_number = $additions_key->invoice_id;
						$added_quantity = $additions_key->added_quantity;
						$receipt_number = '-';
						$addition_description = $additions_key->description;

						if(($added_quantity > 0) && ($date_added > $invoice_date_bill))
						{
						
							$total_arrears += $added_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_added)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>ADDITION</td>
									<td>'.strtoupper($addition_description).'</td>
									<td>'.$added_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $added_quantity;

						}


					}
				}


				if($drug_credit_note->num_rows() > 0)
				{
					foreach ($drug_credit_note->result() as $credit_note) {
						# code...
						$credit_date = $credit_note->purchase_date;
						$supplier_invoice_number = $credit_note->invoice_id;
						$credited_quantity = $credit_note->received_quantity;
						$credit_number = $credit_note->receipt_number;

						if(($credited_quantity > 0) && ($credit_date > $invoice_date_bill))
						{
							$total_arrears -= $credited_quantity;
							$total_invoice_balance -= $credited_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($credit_date)).' </td>
									<td>'.strtoupper($credit_number).'</td>
									<td>CREDIT NOTE</td>
									<td>STOCK TAKE</td>
									<td></td>
									<td>'.$credited_quantity.'</td>
									<td>'.abs($total_arrears).'</td>
								</tr> 
							';
							

						}
					}
				}

				if($drug_transfers->num_rows() > 0)
				{
					foreach ($drug_transfers->result() as $transfers) {
						# code...
						$transfer_date = $transfers->deduction_date;
						$supplier_invoice_number = $transfers->invoice_id;
						$transfered_quantity = $transfers->deducted_quantity;
						$transfer_number = $transfers->deduction_number;


						if(($transfered_quantity > 0) && ($transfer_date > $invoice_date_bill))
						{
							$total_arrears -= $transfered_quantity;
							$total_invoice_balance -= $transfered_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($transfer_date)).' </td>
									<td>'.strtoupper($transfer_number).'</td>
									<td>TRANSFER</td>
									<td>STOCK TAKE</td>
									<td></td>
									<td>'.$transfered_quantity.'</td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							

						}
					}
				}

				if($opening_stock->num_rows() > 0)
				{
					foreach ($opening_stock->result() as $opening_stock_value) {
						# code...
						$stock_take_date = $opening_stock_value->stock_take_date;
						$supplier_invoice_number = $opening_stock_value->invoice_id;
						$opening_quantity = $opening_stock_value->opening_quantity;
						$receipt_number = $opening_stock_value->receipt_number;

						if(($opening_quantity > 0) && ($stock_take_date > $invoice_date_bill))
						{
						
							$total_arrears += $opening_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($stock_take_date)).' </td>
									<td>-</td>
									<td>STOCK TAKE</td>
									<td>-</td>
									<td>'.$opening_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_payment_amount += $opening_quantity;

						}


					}
				}

				if($drug_deductions->num_rows() > 0)
				{
					foreach ($drug_deductions->result() as $deductions_key) {
						# code...
						$date_deducted = $deductions_key->date_added;
						$supplier_invoice_number = $deductions_key->invoice_id;
						$deducted_quantity = $deductions_key->added_quantity;
						$receipt_number = '-';
						$deduction_description = $deductions_key->description;

						if(($date_deducted > 0) && ($date_deducted > $invoice_date_bill))
						{

							$total_arrears -= $deducted_quantity;
							$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_deducted)).' </td>
									<td>'.strtoupper($receipt_number).'</td>
									<td>DEDUCTION</td>
									<td>'.strtoupper($deduction_description).'</td>
									<td>'.$deducted_quantity.'</td>
									<td></td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
							
							$total_invoice_balance += $deducted_quantity;

						}

					}
				}
			}
		}




			
						
		//display loan
		$result .= 
		'
			<tr>
				<th colspan="4">Total</th>
				<th>'.$total_payment_amount.'</th>
				<th>'.abs($total_invoice_balance).'</th>
				<td>'.$total_arrears.'</td>
			</tr> 
		';
		



		$response['total_arrears'] = $total_arrears;
		$response['total_invoice_balance'] = $total_invoice_balance;
		$response['total_credit_notes_amount'] = $total_credit_notes_amount;
		$response['result'] = $result;
		$response['total_payment_amount'] = $total_payment_amount;

		// var_dump($response); die();

		return $response;
	}


	public function get_drug_trail_report($product_id)
	{
		$select_statement  = "
							SELECT 
								* 
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
							AND store.store_id = store_product.owning_store_id AND store_product.product_id = ".$product_id."

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
							AND  product_return_stock.product_id = ".$product_id."

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
							AND product_deductions.product_id = ".$product_id.") AS data ORDER BY data.transactionDate ASC  ";
	// $this->db->order_by('data.transactionDate','ASC');
	$query = $this->db->query($select_statement);
	$result = '';
	$total_invoices = 0;
	$total_payments = 0;
	$total_arrears = 0;
	if($query->num_rows() > 0)
	{
		foreach ($query->result() as $key => $value) {
			# code...

			$date_added = $value->transactionDate;
			$transactionClassification = $value->transactionClassification;
			$transactionDescription = $value->transactionDescription;
			$dr_quantity = $value->dr_quantity;
			$cr_quantity = $value->cr_quantity;
			$store_name = $value->store_name;

			$total_arrears += $dr_quantity - $cr_quantity;

			$total_payments += $dr_quantity;
			$total_invoices += $cr_quantity;

			$result .= 
							'
								<tr>
									<td>'.date('d M Y',strtotime($date_added)).' </td>
									<td>'.$store_name.'</td>
									<td>'.$transactionClassification.'</td>
									<td>'.strtoupper($transactionDescription).'</td>
									<td>'.$dr_quantity.'</td>
									<td>'.$cr_quantity.'</td>
									<td>'.$total_arrears.'</td>
								</tr> 
							';
		}
	}
			
			$result .= 
		'
			<tr>
				<th colspan="4">Total</th>
				<th>'.$total_payments.'</th>
				<th>'.abs($total_invoices).'</th>
				<td>'.$total_arrears.'</td>
			</tr> 
		';
		



		$response['total_arrears'] = $total_arrears;
		$response['total_invoice_balance'] = $total_invoices;
		$response['total_credit_notes_amount'] = 0;
		$response['result'] = $result;
		$response['total_payment_amount'] = $total_payments;

		// var_dump($response); die();

		return $response;
	}


	public function stock_take_drugs()
	{
		$select_statement ='SELECT 
								product.product_id,product.product_unitprice,store.store_name,product.product_name,store_product.store_quantity,store_product.owning_store_id,product.regenerate_id,category.category_name
							FROM product,store_product,store,category
							WHERE
							product.product_id = store_product.product_id 
							AND product.category_id = category.category_id 
							AND store.store_id = store_product.owning_store_id  
							AND product.product_deleted = 0 
							AND product.regenerate_id > 0
							AND product.product_id >= 9316
							ORDER BY product.product_id';
		$query = $this->db->query($select_statement);

		return $query;
	}

	public function get_opening_stock($store_id,$product_id)
	{

		$select_statement ='SELECT 
								store_product.stock_take
							FROM store_product,product 
							WHERE
							store_product.product_id = '.$product_id.'
							AND store_product.owning_store_id = '.$store_id.'
							AND product.product_id = store_product.product_id  ';
		$query = $this->db->query($select_statement);

		$number = 0;

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$number = $value->stock_take;
			}
		}


		return $number;
	
	}
}
?>