<?php

class Financials_model extends CI_Model 
{	
	
	/*
	*	Retrieve all customers
	*	@param string $table
	* 	@param string $where
	*
	*/
	public function get_vehicle_document_types()
	{
		//retrieve all users
		$this->db->from('vehicle_document_types');
		$this->db->select('*');
		$this->db->where('document_status = 1');
		$query = $this->db->get();
		
		return $query;
		
	}
	public function get_vehicle_document_details($vehicle_id)
	{
		//retrieve all users
		$this->db->from('vehicle_upload');
		$this->db->select('*');
		$this->db->where('vehicle_id = '.$vehicle_id);
		$query = $this->db->get();
		
		return $query;
		
	}
	public function get_personnel_vehicles($personnel_id)
	{
		//retrieve all users
		$this->db->from('vehicle, personnel_vehicle');
		$this->db->select('*');
		$this->db->where('vehicle.vehicle_id = personnel_vehicle.vehicle_id AND personnel_vehicle.personnel_vehicle_status = 1 AND personnel_vehicle.personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
		
	}
	public function get_personnel_vehicles_data($personnel_id)
	{
		//retrieve all users
		$this->db->from('vehicle, personnel_vehicle');
		$this->db->select('*');
		$this->db->where('vehicle.vehicle_id = personnel_vehicle.vehicle_id AND personnel_vehicle.personnel_id = '.$personnel_id);
		$query = $this->db->get();
		
		return $query;
		
	}
	
	public function get_all_drivers($table, $where, $per_page, $page, $order = 'personnel.personnel_id', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}


	
	public function get_all_customer_contacts($table, $where, $per_page, $page, $order = 'customer_contacts_first_name', $order_method = 'ASC')
	{
		//retrieve all users
		$this->db->from($table);
		$this->db->select('*');
		$this->db->where($where);
		$this->db->order_by($order, $order_method);
		$query = $this->db->get('', $per_page, $page);
		
		return $query;
	}
	
	

/*
	*	Add a new vehicle
	*	@param string $image_name
	*
	*/
	public function assign_drivers($personnel_id)
	{
		$data2 = array(
				'personnel_vehicle_status' => 0
			);
		$this->db->where('personnel_id', $personnel_id);
		
		if($this->db->update('personnel_vehicle', $data2))
		


		$data = array(
				'vehicle_id'=>$this->input->post('vehicle_id'),
				'personnel_id'=>$personnel_id
				
				
				
				
				#'created_by'=>$this->session->userdata('personnel_id'),
				#'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		if($this->db->insert('personnel_vehicle', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	/*
	*	Add a new vehicle
	*	@param string $image_name
	*
	*/
	public function add_vehicle()
	{
		$data = array(
				'vehicle_name'=>$this->input->post('vehicle_name'),
				'vehicle_plate'=>$this->input->post('vehicle_plate'),
				'vehicle_capacity'=>$this->input->post('vehicle_capacity')
				
				
				#'created_by'=>$this->session->userdata('personnel_id'),
				#'modified_by'=>$this->session->userdata('personnel_id')
			);
			
		if($this->db->insert('vehicle', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	/*
	*	Update an existing customer
	*	@param string $image_name
	*	@param int $customer_id
	*
	*/
	public function update_vehicle($vehicle_id)
	{
		$data = array(

				'vehicle_name'=>$this->input->post('vehicle_name'),
				'vehicle_plate'=>$this->input->post('vehicle_plate'),
				'vehicle_capacity'=>$this->input->post('vehicle_capacity'),
				
				
			);
			
		$this->db->where('vehicle_id', $vehicle_id);
		if($this->db->update('vehicle', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	get a single customer's details
	*	@param int $customer_id
	*
	*/
	public function get_vehicle($vehicle_id)
	{
		//retrieve all users
		$this->db->from('vehicle');
		$this->db->select('*');
		$this->db->where('vehicle_id = '.$vehicle_id);
		$query = $this->db->get();
		
		return $query;
	}
	


	/*
	*	Activate a deactivated customer
	*	@param int $customer_id
	*
	*/
	public function activate_vehicle($vehicle_id)
	{
		$data = array(
				'vehicle_status' => 1
			);
		$this->db->where('vehicle_id', $vehicle_id);
		
		if($this->db->update('vehicle', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	*	Deactivate an activated customer
	*	@param int $customer_id
	*
	*/
	public function deactivate_vehicle($vehicle_id)
	{
		$data = array(
				'vehicle_status' => 0
			);
		$this->db->where('vehicle_id', $vehicle_id);
		
		if($this->db->update('vehicle', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function get_sale_trips($personnel_id)
	{
		$total_sales='0';
		$this->db->select('personnel_vehicle_id');
		$this->db->where('sales.sale_status = 1 AND sales.product_id = 1');
		$query2 = $this->db->get('sales');
		$total2 = $query2->row();
		$total_sales = $total2->total_sales;
		
		return $total_sales;
	}
	public function disburse_mpesa($mpesa_contact_id)
	{
		$phone = $this->get_phone_contact($mpesa_contact_id);
		$phone_number = $this->clean_phone_number($phone);
		//var_dump($phone_number);die();

		$fields_ser = array
		(
			'api_key' => urlencode("1000"),
			'phone_number' => urlencode($phone_number),
			'transaction_id' => urlencode(3),
			'amount' => urlencode("10")
		);

		$base_url = 'https://www.omnis.co.ke/omnis_gateway/';
		$service_url = $base_url.'disburse-payment';
		$response2 = $this->rest_service($service_url, $fields_ser);
		$message = json_decode($response2);
		$message = json_decode($message);
		
		//var_dump($message);die();
		
		if($message->result == 0)
		{
			$data_insert = array(
					'mpesa_contact_id' => $mpesa_contact_id,
					'mpesa_amount' => 10,
					'date_transacted' => date('Y-m-d'),
				);
			$this->db->insert('mpesa_disbursement', $data_insert);
			
			return TRUE;
			
		}
		else
		{
			return FALSE;

		}
		
	}
	public function rest_service($service_url, $fields)
	{
		$fields_string = '';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		// a. initialize
		try{
			$ch = curl_init();
			
			// b. set the options, including the url
			curl_setopt($ch, CURLOPT_URL, $service_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			
			// c. execute and fetch the resulting HTML output
			$output = curl_exec($ch);
			
			//in the case of an error save it to the database
			if ($output === FALSE) 
			{
				$return['result'] = 0;
				$return['message'] = curl_error($ch);
				
				$return = json_encode($response);
			}
			
			else
			{
				$return = $output;
				$return = json_encode($output);
			}
		}
		
		//in the case of an exceptions save them to the database
		catch(Exception $e)
		{
			$response['result'] = 0;
			$response['message'] = $e->getMessage();
			
			$return = json_encode($response);
		}
		
		return $return;
	}
	public function get_phone_contact($mpesa_contact_id){
		$total_batch='0';
		$this->db->select('personnel_phone');
		$this->db->where('personnel_id = '.$mpesa_contact_id);
		$query = $this->db->get('personnel');
		$total = $query->row();
		$total_batch = $total->personnel_phone;

		return $total_batch;
	}
	public function clean_phone_number($phone_number)
	{
		//remove forward slash
		$numbers = explode("/",$phone_number);
		$phone_number = $numbers[0];

		//remove hyphens
		$phone_number = str_replace("-","",$phone_number);

		//remove spaces
		$phone_number = str_replace(" ","",$phone_number);

		if (substr($phone_number, 0, 1) === '0') 
		{
			$phone_number = ltrim($phone_number, '0');
		}
		
		if (substr($phone_number, 0, 3) === '254') 
		{
			$phone_number = ltrim($phone_number, '254');
		}
		
	
		return $phone_number;
	}
	
	
}
?>