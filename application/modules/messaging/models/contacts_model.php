<?php

class Contacts_model extends CI_Model 
{

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

	/*
	*	Retrieve all users
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_all_contacts($table, $where, $per_page, $page)
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}


	/*
	*	Import Template
	*
	*/
	function import_template()
	{
		$this->load->library('Excel');
		
		$title = 'Contacts Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Name';
		$report[$row_count][1] = 'Phone Number';
		$report[$row_count][2] = 'Account Balance';

	
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_charges($upload_path, $service_id)
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
			// var_dump($array); die();
			$response2 = $this->sort_csv_charges_data($array, $service_id);
		
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
	public function sort_csv_charges_data($array, $message_category_id)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);
		// var_dump($total_columns); die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 3))
		{
			$count = 0;
			$comment = '';
			// $items['modified_by'] = $this->session->userdata('personnel_id');
			
			//retrieve the data from array
			for($r = 1; $r < $total_rows; $r++)
			{
				$service_charge_insert['name'] = ucwords(strtolower($array[$r][0]));
				$service_charge_insert['Phonenumber'] = $phone = $array[$r][1];
				// $service_charge_insert['balance'] =$array[$r][2];
	
				$count++;

				$this->db->where('Phonenumber = "'.$phone.'"');
				$query_two = $this->db->get('allcounties');

					// var_dump($query_two->num_rows()); die();
				if($query_two->num_rows() == 0 )
				{

					if(empty($service_charge_insert['Phonenumber']))
					{

					}else
					{
						if($this->db->insert('allcounties', $service_charge_insert))
						{
							$comment .= '<br/>Details successfully added to the database';
							$class = 'success';
						}
						
						else
						{
							$comment .= '<br/>Not saved internal error';
							$class = 'danger';
						}
					}

				}
				else
				{
					$comment .= '<br/>Not saved internal error contact is available';
					$class = 'danger';
				}
				
				
		
				
				
			}	
			$return['response'] = TRUE;
			$return['check'] = TRUE;
				
		}
		else
		{
			$return['response'] = FALSE;
			$return['check'] = FALSE;
		}
		
		return $return;
	}
	public function delete_contact($contact_id)
	{
		$this->db->where('entryid', $contact_id);
		
		if($this->db->delete('allcounties'))
		{
			return TRUE;
		}
		
		else
		{
			return FALSE;
		}
	}
	
	public function get_contact($entryid)
	{
		$this->db->where('entryid', $entryid);
		return $this->db->get('allcounties');
	}
	
	/*
	*	Edit personnel
	*
	*/
	public function edit_contact($entryid)
	{
		$data = array(
			'name'=>ucwords(strtolower($this->input->post('name'))),
			//'balance'=>$this->input->post('balance'),
			'Phonenumber'=>$this->input->post('Phonenumber'),
			'Countyname'=>'NAIROBI'
		);
		
		$this->db->where('entryid', $entryid);
		if($this->db->update('allcounties', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Add personnel
	*
	*/
	public function add_contact()
	{
		$data = array(
			'name'=>ucwords(strtolower($this->input->post('name'))),
			//'balance'=>$this->input->post('balance'),
			'Phonenumber'=>$this->input->post('Phonenumber'),
			'Countyname'=>'NAIROBI'
		);
		
		if($this->db->insert('allcounties', $data))
		{
			return $this->db->insert_id();
		}
		else{
			return FALSE;
		}
	}
}

?>