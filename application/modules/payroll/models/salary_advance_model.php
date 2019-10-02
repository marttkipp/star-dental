<?php
class Salary_advance_model extends CI_Model 
{
	public function add_salary_advance()
	{
		
	}
	public function get_all_advances($table, $where, $config, $page, $order, $order_method)
	{
		$this->db->where($where);
		$this->db->order_by($order);
		$query = $this->db->get($table, $config, $page);
		
		return $query;
	}
	public function all_months()
	{
		$this->db->where('month_id > 0');
		$this->db->order_by('month_id');
		$query = $this->db->get('month');
		
		return $query;
	}
	public function get_branch_name($branch_id2)
	{
		$this->db->select('branch_name');
		$this->db->where('branch_id = '.$branch_id2);
		$query = $this->db->get('branch');
		
		if($query->num_rows() > 0){
			foreach($query->result() as $branch)
			{
				$branch_name =$branch->branch_name;
			}
			return $branch_name;
		}	
	}
	public function get_month_name($month)
	{
		$this->db->select('month_name');
		$this->db->where('month_id = '.$month);
		$query = $this->db->get('month');
		
		if($query->num_rows() > 0){
			foreach($query->result() as $month)
			{
				$month_name =$month->month_name;
			}
			return $month_name;
		}
	}
	public function get_branch_code($bank_branch_id)
	{
		$this->db->select('bank_branch_code');
		$this->db->where('bank_branch_id = '.$bank_branch_id);
		$query = $this->db->get('bank_branch');
		
		if($query->num_rows() > 0){
			foreach($query->result() as $bank_code)
			{
				$bank_branch_code =$bank_code->bank_branch_code;
			}
			return $bank_branch_code;
		}
	}
	
	public function advances_template()
	{
		$this->load->library('Excel');
		
		$title = 'Salary Advance Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Employee Number';
		$report[$row_count][1] = 'Salary Advance Amount';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}
	
	//import personnel
	public function import_csv_salary_advance($upload_path)
	{
		//load the file model
		$this->load->model('admin/file_model');
		
		$response = $this->file_model->upload_csv($upload_path, 'import_csv');
		
		if($response['check'])
		{
			$file_name = $response['file_name'];
			
			$array = $this->file_model->get_array_from_csv($upload_path.'/'.$file_name);
			//var_dump($array); die();
			$response2 = $this->sort_adavances_data($array);
		
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
	
	public function sort_adavances_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die
		$count = 0;
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 2))
		{
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Member Number</th>
						  <th>Payroll Amount</th>
						  <th>Comment</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$branch_id =$items['branch_id'] = $this->input->post('branch_id');
				$month_id = $items['month_id'] = $this->input->post('month_id');
				$personnel_number = $array[$r][0];
				$items['advance_amount'] = $array[$r][1];	
				$count++;
				$comment = $items['personnel_id'] = $personnel_id = '';
				if(!empty($personnel_number))
				{
					//get personnel_id for that number
					$personnel_id = $this->get_personnel_id($personnel_number,$branch_id);
					//var_dump($personnel_id);die();
					if((!empty($personnel_id))&&($personnel_id !=0))
					{
						// check if the number already exists in salary advance for that month
						if($this->check_advance_existance($personnel_id, $branch_id, $month_id))
						{
							$items2['branch_id'] = $items['branch_id'];
							$items2['month_id'] = $items['month_id'];
							$items2['advance_amount'] = $items['advance_amount'];
							
							$this->db->where('personnel_id',$personnel_id); 
							$this->db->update('salary_advance', $items2);
							//update the personnel other decuction table with the salary advance  updated deduction amount
								$advance_deduction = array(
									'personnel_other_deduction_amount' => $items2['advance_amount'],
									'personnel_other_deduction_date' =>date('Y-m-d H-i-s')
									);
								$this->db->where('personnel_id ='.$personnel_id.' AND other_deduction_id = 1');
								if($this->db->update('personnel_other_deduction',$advance_deduction))
								{
									$comment .= '<br/>Duplicate member number entered for the month, salary advance amount updated successfully.Salary Advance Updated and Deducted from the personnel';
									$class = 'warning';
								}
							
						}
						else
						{
							$items['personnel_id'] = $personnel_id;
							// number does not exisit save advance in the db
							if($this->db->insert('salary_advance', $items))
							{
								//check if the personnel has a salary advance
								if($this->personnel_has_advance($personnel_id))
								{
									//update the personnel other decuction table with the salary advance deduction
									$advance_deduction = array(
										'personnel_other_deduction_amount' => $items['advance_amount'],
										'personnel_other_deduction_date' =>date('Y-m-d H-i-s')
										);
									$this->db->where('personnel_id ='.$personnel_id.' AND other_deduction_id = 1');
									if($this->db->update('personnel_other_deduction',$advance_deduction))
									{
										$comment .= '<br/>Advance Payment added to the database.Salary Advance Updated and Deducted from the personnel';
										$class = 'success';
									}
									else
									{
										$comment .= '<br/>Salary advances added successfully but could not be deducted from the personnel.';
										$class = 'warning';
									}
								}
								else
								{
									//insert a new salary advance for that personnel
									$advance_deduction = array(
										'personnel_other_deduction_amount' => $items['advance_amount'],
										'other_deduction_id' =>1,
										'personnel_other_deduction_date' =>date('Y-m-d H-i-s'),
										'personnel_id' =>$personnel_id
										);
									
									if($this->db->insert('personnel_other_deduction',$advance_deduction))
									{
										$comment .= '<br/>Advance Payment added to the database.Salary Advance Added Successfully and Deducted from the personnel';
										$class = 'success';
									}
									else
									{
										$comment .= '<br/>Salary advances added successfully but could not be deducted from the personnel.';
										$class = 'warning';
									}
								}
							}
							
							else
							{
								$comment .= '<br/>Internal error. Could not add personnel to the database. Please contact the site administrator';
								$class = 'warning';
							}
						}
						
					}
					else
					{
						$comment .= '<br/>Internal error. Could not add advance to the database. Ensure the member number belongs to that branch';
						$class = 'danger';
					}
				}
				
				else
				{
					$comment .= '<br/>Not saved ensure you have a member number entered';
					$class = 'danger';
				}
				
				
				$response .= '
					
						<tr class="'.$class.'">
							<td>'.$count.'</td>
							<td>'.$personnel_number.'</td>
							<td>'.$items['advance_amount'].'</td>
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
			$return['response'] = 'Salary Advance data not found ';
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	
	public function check_advance_existance($personnel_id, $branch_id, $month_id)
	{
		$data = array(
			'personnel_id' => $personnel_id,
			'branch_id' => $branch_id,
			'month_id' =>$month_id
		);
		$this->db->select('*');
		$this->db->where($data);
		$query = $this->db->get('salary_advance');
		if ($query->num_rows()>0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function get_personnel_id($personnel_number,$branch_id)
	{
		//var_dump($personnel_number);die();
		$data = array(
			'personnel_number' =>$personnel_number,
			'branch_id' =>$branch_id
		);
		$this->db->select('personnel_id');
		$this->db->where($data);
		$query = $this->db->get('personnel');
		//var_dump($query);die();
		if($query->num_rows() > 0){
			foreach($query->result() as $personnel)
			{
				$personnel_id =$personnel->personnel_id;
			}
			return $personnel_id;
		}
		
	}
	public function personnel_has_advance($personnel_id)
	{
		$data = array(
			'personnel_id' => $personnel_id,
			'other_deduction_id' => 1
		);
		$this->db->select('*');
		$this->db->where($data);
		$query = $this->db->get('personnel_other_deduction');
		if ($query->num_rows()>0)
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