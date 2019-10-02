<?php

class department_accounts_model extends CI_Model 
{

	public function get_all_department_accounts($table, $where, $per_page, $page, $order, $order_method)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->join('departments', 'departments.department_id = department_accounts.department_id', 'LEFT');
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

 //   public function all_department_accounts()
	
	// {
	// 	$this->db->where(array('department_account_status' => 1, 'branch_code' => $this->session->userdata('branch_code')));
	// 	$this->db->order_by('department_account_name');
	// 	$query = $this->db->get('department_accounts');
		
	// 	return $query;
	// }

	public function get_all_department_accounts_charges($table, $where, $per_page, $page, $order, $order_method)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}

	public function all_accounts()
	{
		$this->db->where('account_status = 1');
		$this->db->order_by('account_name');
		$query = $this->db->get('account');
		
		return $query;
	}

	public function all_department_accounts()
	{
		$this->db->where('department_account_status = 1');
		//$this->db->order_by('department_account_name');
		$query = $this->db->get('department_accounts');
		
		return $query;
	}
	
	
	public function get_department_accounts_names($department_account_id)
	{
		$table = "department_accounts";
		$where = "department_accounts_id =". $department_account_id;
		$items = "department_accounts_name";
		$order = "department_accounts_name";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				$department_accounts_name = $key->department_accounts_name;
			endforeach;
		}
		return $department_accounts_name;
	}
	
	public function get_department_accounts_department($department_account_id)
	{
		$table = "department_accounts";
		$where = "department_account_id =". $department_account_id;
		$items = "department_id";
		$order = "department_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			foreach ($result as $key):
				$department_id = $key->department_id;
			endforeach;
		}
		return $department_id;
	}

	public function submit_department_accounts_charges($department_account_id)
	{
		$account_id = $this->input->post('account_id');
		$department_id = $this->input->post('department_id');
		
		$visit_data = array(
			'account_id'=>$account_id,
			'department_id'=>$department_id,
			'department_accounts_charge_status'=>1,
			'created'=>date('Y-m-d H:i:s'),
			'created_by'=>$this->session->userdata('personnel_id'),
			'modified_by'=>$this->session->userdata('personnel_id')
		);
		if($this->db->insert('department_accounts_charge', $visit_data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}

	public function check_department_accounts_charge_exist($department_account_id,$patient_type,$department_accounts_charge_name)
	{
		$table = "department_accounts_charge";
		$where = "department_accounts_charge_name = '$department_accounts_charge_name' AND department_account_id = ".$department_account_id." AND visit_type_id = ".$patient_type;
		$items = "*";
		$order = "department_accounts_charge_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;	
		}
		
	}
	public function get_department_accounts_charge_data($department_accounts_charge_id)
	{
		$table = "department_accounts_charge";
		$where = "department_accounts_charge_id = ".$department_accounts_charge_id;
		$items = "*";
		$order = "department_accounts_charge_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);

		return $result;

	}
	public function submit_department_accounts()
	{
		$account_id = $this->input->post('account_id');
		$department_id = $this->input->post('department_id');

		$visit_data = array('account_id'=>$account_id,
		'department_id'=>$department_id,
		'created'=>date('Y-m-d'),
		'created_by'=>$this->session->userdata('personnel_id'),

		);
		if($this->db->insert('department_accounts', $visit_data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function check_department_accounts_exist($department_accounts_name)
	{
		$table = "department_accounts";
		$where = "department_accounts_name = '$department_accounts_name'";
		$items = "*";
		$order = "department_account_id";
		
		$result = $this->database->select_entries_where($table, $where, $items, $order);
		
		if(count($result) > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;	
		}
	}
	
	// public function delete_department_accounts($department_account_id)
	// {
	// 	$data['department_accounts_delete'] = 1;
		
	// 	//delete department_accounts charges
		
	// 	$this->db->where('department_account_id', $department_account_id);
	// 	if($this->db->delete('department_accounts_charge'))
	// 	{
	// 		$this->db->where('department_account_id', $department_account_id);
	// 		if($this->db->update('department_accounts', $data))
	// 		{
	// 			return TRUE;
	// 		}
			
	// 		else
	// 		{
	// 			return FALSE;
	// 		}
	// 	}
		
	// 	else
	// 	{
	// 		return FALSE;
	// 	}
	// }
	
	public function delete_department_accounts($department_account_id)
	{
		$data['department_account_delete'] = 1;
		$this->db->where('department_account_id', $department_account_id);
		if($this->db->update('department_accounts', $data))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	public function get_all_patient_visit($table, $where, $per_page, $page, $items = '*')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select($items);
		$this->db->where($where);
		$this->db->order_by('visit_date','desc');
		$query = $this->db->get();
		
		return $query;
	}

	public function patient_account_balance($patient_id)
	{
		//retrieve all users
		$this->db->from('visit');
		$this->db->select('*');
		$this->db->where('patient_id = '.$patient_id);
		$this->db->order_by('visit_date','desc');
		$query = $this->db->get();

		$total_invoiced_amount = 0;
		$total_paid_amount = 0;

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$visit_id = $row->visit_id;
				$visit_date = $row->visit_date;
				$visit_date = $row->visit_date;
				$total_invoice = $this->accounts_model->total_invoice($visit_id);
				$total_payments = $this->accounts_model->total_payments($visit_id);

				$total_paid_amount = $total_paid_amount + $total_payments;
				$total_invoiced_amount = $total_invoiced_amount + $total_invoice;
				
				$invoice_number =  $visit_id;
			}
			$difference = $total_invoiced_amount -$total_paid_amount;
		}
		else
		{
			$difference = $total_invoiced_amount -$total_paid_amount;
		}

		return $difference;
	}
	
	/*
	*	Activate a deactivated department_accounts
	*	@param int $department_account_id
	*
	*/
	public function activate_department_accounts($department_account_id)
	{
		$data = array(
				'department_account_status' => 1
			);
		$this->db->where('department_account_id', $department_account_id);
		
		if($this->db->update('department_accounts', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated department_accounts
	*	@param int $department_account_id
	*
	*/
	public function deactivate_department_accounts($department_account_id)
	{
		$data = array(
				'department_account_status' => 0
			);
		$this->db->where('department_account_id', $department_account_id);
		
		if($this->db->update('department_accounts', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Activate a deactivated department_accounts_charge
	*	@param int $department_accounts_charge_id
	*
	*/
	public function activate_department_accounts_charge($department_accounts_charge_id)
	{
		$data = array(
				'department_accounts_charge_status' => 1
			);
		$this->db->where('department_accounts_charge_id', $department_accounts_charge_id);
		
		if($this->db->update('department_accounts_charge', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated department_accounts
	*	@param int $department_accounts_id
	*
	*/
	public function deactivate_department_accounts_charge($department_accounts_charge_id)
	{
		$data = array(
				'department_accounts_charge_status' => 0
			);
		$this->db->where('department_accounts_charge_id', $department_accounts_charge_id);
		
		if($this->db->update('department_accounts_charge', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	public function get_department_id($department_account_id)
	{
		$table = "department_accounts";
		$where = "department_accounts_id = ".$department_accounts_id;
		
		$this->db->where($where);
		$result = $this->db->get($table);
		$department_id = 0;
		
		if($result->num_rows() > 0)
		{
			$row = $result->row();
			$department_id = $row->department_id;
		}

		return $department_id;
	}
	public function get_unsynced_laboratory_charges()
	{
		$this->db->where('lab_test_delete = 0 AND is_synced = 0');
		$query = $this->db->get('lab_test');
		
		return $query;
	}
	public function import_lab_charges($department_account_id)
	{
		//get lab tests
		$this->db->where('lab_test_delete = 0 AND is_synced = 0');
		$tests = $this->db->get('lab_test');
		
		if($tests->num_rows() > 0)
		{
			foreach($tests->result() as $res)
			{
				$lab_test_id = $res->lab_test_id;
				$lab_test_name = $res->lab_test_name;
				$price = $res->lab_test_price;
	
				// get all the visit type
				$this->db->where('visit_type_status', 1);
				$visit_type_query = $this->db->get('visit_type');
	
				if($visit_type_query->num_rows() > 0)
				{
					foreach ($visit_type_query->result() as $key) {
					
						$visit_type_id = $key->visit_type_id;
						// department_accounts charge entry
						$department_accounts_charge_insert = array(
										"department_accounts_charge_name" => $lab_test_name,
										"department_account_id" => $department_account_id,
										"visit_type_id" => $visit_type_id,
										"lab_test_id" => $lab_test_id,
										"department_accounts_charge_amount" => $price,
										'department_accounts_charge_status' => 1,
									);
						
						if($this->department_accounts_charge_exists($lab_test_name, $visit_type_id))
						{
							$this->db->where(array('department_accounts_charge_name' => $lab_test_name, 'visit_type_id' => $visit_type_id));
							if($this->db->update('department_accounts_charge', $department_accounts_charge_insert))
							{
								$update_array = array('is_synced'=>1);
								$this->db->where('lab_test_id ='.$lab_test_id);
								$this->db->update('lab_test',$update_array);
							}
							
							else
							{
							}
						}
						
						else
						{
							$department_accounts_charge_insert['created'] = date('Y-m-d H:i:s');
							$department_accounts_charge_insert['created_by'] = $this->session->userdata('personnel_id');
							$department_accounts_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
							// var_dump($department_accounts_charge_insert); die();
							if($this->db->insert('department_accounts_charge', $department_accounts_charge_insert))
							{
								$update_array = array('is_synced'=>1);
								$this->db->where('lab_test_id ='.$lab_test_id);
								$this->db->update('lab_test',$update_array);
							}
							
							else
							{
							}
						}
					}
				}
			}
			
			$this->session->set_userdata('success_message', 'Charges created successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'There are no lab tests.');
		}
		
		return TRUE;
	}
	
	public function import_bed_charges($department_account_id)
	{
		//get lab tests
		$this->db->where('bed_status', 1);
		$tests = $this->db->get('bed');
		
		if($tests->num_rows() > 0)
		{
			foreach($tests->result() as $res)
			{
				$bed_id = $res->bed_id;
				$bed_number = $res->bed_number;
				$cash_price = $res->cash_price;
				$insurance_price = $res->insurance_price;
	
				// get all the visit type
				$this->db->where('visit_type_status', 1);
				$visit_type_query = $this->db->get('visit_type');
	
				if($visit_type_query->num_rows() > 0)
				{
					foreach ($visit_type_query->result() as $key) {
					
						$visit_type_id = $key->visit_type_id;
						
						if($visit_type_id == 1)
						{
							$price = $cash_price;
						}
						
						else
						{
							$price = $cash_price;
						}
						// department_accounts charge entry
						$department_accounts_charge_insert = array(
										"department_accounts_charge_name" => $bed_number,
										"department_account_id" => $department_account_id,
										"visit_type_id" => $visit_type_id,
										//"lab_test_id" => $bed_id,
										"department_accounts_charge_amount" => $price,
										'department_accounts_charge_status' => 1,
									);
						
						if($this->department_accounts_charge_exists($bed_number, $visit_type_id))
						{
							$this->db->where(array('department_accounts_charge_name' => $bed_number, 'visit_type_id' => $visit_type_id));
							if($this->db->update('department_accounts_charge', $department_accounts_charge_insert))
							{
							}
							
							else
							{
							}
						}
						
						else
						{
							$department_accounts_charge_insert['created'] = date('Y-m-d H:i:s');
							$department_accounts_charge_insert['created_by'] = $this->session->userdata('personnel_id');
							$department_accounts_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
							
							if($this->db->insert('department_accounts_charge', $department_accounts_charge_insert))
							{
							}
							
							else
							{
							}
						}
					}
				}
			}
			
			$this->session->set_userdata('success_message', 'Charges created successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'There are no lab tests.');
		}
		
		return TRUE;
	}

	public function get_unsynced_pharmacy_charges()
	{
		$this->db->where('product_status = 1 AND is_synced = 0');
		$query = $this->db->get('product');
		
		return $query;
	}
	
	public function import_pharmacy_charges($department_account_id)
	{
		//get lab tests
		$this->db->where('product_status = 1 AND is_synced = 0');
		$products = $this->db->get('product');
		
		if($products->num_rows() > 0)
		{
			foreach($products->result() as $res)
			{
				$product_id = $res->product_id;
				$product_name = $res->product_name;
				$product_unitprice = $res->product_unitprice;
				$product_unitprice_insurance = $res->product_unitprice_insurance;
				
	
				// get all the visit type
				$this->db->where('visit_type_status', 1);
				$visit_type_query = $this->db->get('visit_type');
	
				if($visit_type_query->num_rows() > 0)
				{
					// foreach ($visit_type_query->result() as $key) {
					
						$visit_type_id = 1;//$key->visit_type_id;
						if(empty($product_unitprice))
						{
							$product_unitprice = 0;
						}
						if(!empty($product_unitprice_insurance))
						{
							$product_unitprice_insurance = $product_unitprice;
						}
						else
						{
							$product_unitprice_insurance = $product_unitprice;
						}
						if($visit_type_id == 1)
						{
							// department_accounts charge entry
							$department_accounts_charge_insert = array(
										"department_accounts_charge_name" => $product_name,
										"department_account_id" => $department_account_id,
										"visit_type_id" => $visit_type_id,
										"product_id" => $product_id,
										"department_accounts_charge_amount" => $product_unitprice,
										'department_accounts_charge_status' => 1,
									);

						}
						else
						{
							// department_accounts charge entry
							$department_accounts_charge_insert = array(
										"department_accounts_charge_name" => $product_name,
										"department_account_id" => $department_account_id,
										"visit_type_id" => $visit_type_id,
										"product_id" => $product_id,
										"department_accounts_charge_amount" => $product_unitprice_insurance,
										'department_accounts_charge_status' => 1,
									);
						}
						
						
						if($this->department_accounts_charge_exists($product_name, $visit_type_id))
						{
							$this->db->where(array('department_accounts_charge_name' => $product_name, 'visit_type_id' => $visit_type_id));
							if($this->db->update('department_accounts_charge', $department_accounts_charge_insert))
							{
							}
							
							else
							{
							}
						}
						
						else
						{
							$department_accounts_charge_insert['created'] = date('Y-m-d H:i:s');
							$department_accounts_charge_insert['created_by'] = $this->session->userdata('personnel_id');
							$department_accounts_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
							
							if($this->db->insert('department_accounts_charge', $department_accounts_charge_insert))
							{
							}
							
							else
							{
							}
						}


					// }
				}

				$update_array = array('is_synced'=>1);
				$this->db->where('product_id ='.$product_id);
				$this->db->update('product',$update_array);

			}
			
			$this->session->set_userdata('success_message', 'Charges created successfully');
		}
		
		else
		{
			$this->session->set_userdata('error_message', 'There are no lab tests.');
		}
		
		return TRUE;
	}
	
	public function department_accounts_charge_exists($department_accounts_charge_name, $visit_type_id)
	{
		$this->db->where(array('department_accounts_charge_name' => $department_accounts_charge_name, 'visit_type_id' => $visit_type_id, 'department_accounts_charge_delete' => 0));
		$query = $this->db->get('department_accounts_charge');
		
		if($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	/*
	*	Import Template
	*
	*/
	function import_charges_template()
	{
		$this->load->library('Excel');
		
		$title = 'Oasis department_accounts Charges Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Charge name';
		$report[$row_count][1] = 'Cash paying rate';
		$report[$row_count][2] = 'Insurance rate';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	public function import_csv_charges($upload_path, $department_account_id)
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
			$response2 = $this->sort_csv_charges_data($array, $department_account_id);
		
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
	public function sort_csv_charges_data($array, $department_account_id)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 3))
		{
			$count = 0;
			$comment = '';
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Patient type</th>
						  <th>Charge name</th>
						  <th>Amount</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$department_accounts_charge_name = $array[$r][0];
				$cash_paying_rate = ucwords(strtolower($array[$r][1]));
				$insurance_rate = ucwords(strtolower($array[$r][2]));
				
				// get all the visit type
				$this->db->where('visit_type_status', 1);
				$visit_type_query = $this->db->get('visit_type');
	
				if($visit_type_query->num_rows() > 0)
				{
					// foreach ($visit_type_query->result() as $key) 
					// {
						$count++;
						$visit_type_id = 1;//$key->visit_type_id;
						$visit_type_name = "Cash paying";//$key->visit_type_name;
						
						//cash paying
						if($visit_type_id == 1)
						{
							$department_accounts_charge_amount = str_replace(' ', '', $cash_paying_rate);
						}
						
						else
						{
							$department_accounts_charge_amount = str_replace(' ', '', $insurance_rate);
						}
						
						// department_accounts charge entry
						$department_accounts_charge_insert = array(
										"department_accounts_charge_name" => $department_accounts_charge_name,
										"department_account_id" => $department_account_id,
										"visit_type_id" => $visit_type_id,
										"department_accounts_charge_amount" => $department_accounts_charge_amount,
										'department_accounts_charge_status' => 1,
									);
						
						// if($this->department_accounts_charge_exists($department_accounts_charge_name, $visit_type_id))
						// {
						// 	$this->db->where(array('department_accounts_charge_name' => $department_accounts_charge_name, 'visit_type_id' => $visit_type_id));
						// 	if($this->db->update('department_accounts_charge', $department_accounts_charge_insert))
						// 	{
						// 		//number exists
						// 		$comment .= '<br/>Charge exists. It has been updated';
						// 		$class = 'warning';
						// 	}
							
						// 	else
						// 	{
						// 		$comment .= '<br/>Not saved internal error';
						// 		$class = 'danger';
						// 	}
						// }
						
						// else
						// {
							$department_accounts_charge_insert['created'] = date('Y-m-d H:i:s');
							$department_accounts_charge_insert['created_by'] = $this->session->userdata('personnel_id');
							$department_accounts_charge_insert['modified_by'] = $this->session->userdata('personnel_id');
							
							if($this->db->insert('department_accounts_charge', $department_accounts_charge_insert))
							{
								$comment .= '<br/>Charge successfully added to the database';
								$class = 'success';
							}
							
							else
							{
								$comment .= '<br/>Not saved internal error';
								$class = 'danger';
							}
						// }
				
						$response .= '
								<tr class="'.$class.'">
									<td>'.$count.'</td>
									<td>'.$visit_type_name.'</td>
									<td>'.$department_accounts_charge_name.'</td>
									<td>'.$department_accounts_charge_amount.'</td>
								</tr> 
						';
					// }
				}
			}
			
			$response .= '</table>';
			
			$return['response'] = $response;
			$return['check'] = TRUE;
		}
		
		//if no products exist
		else
		{
			$return['response'] = 'Charges data not found';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	
	public function get_visit_types()
	{
		$data['visit_type_status'] = 1;
		$this->db->where($data);
		$this->db->order_by('visit_type_name');
		
		return $this->db->get('visit_type');
	}
}
?>