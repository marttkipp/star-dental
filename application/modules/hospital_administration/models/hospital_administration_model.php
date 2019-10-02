<?php

class Hospital_administration_model extends CI_Model 
{	

	function import_invoice_template()
	{
		$this->load->library('Excel');
		
		$title = 'Invoices Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Visit ID';
		$report[$row_count][1] = 'Invoice Number';
		$report[$row_count][2] = 'Patient No';
		$report[$row_count][3] = 'Invoice Date';
		$report[$row_count][4] = 'Invoiced amount';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_invoices($upload_path)
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
			$response2 = $this->sort_csv_invoices_data($array);
		
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
	public function sort_csv_invoices_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 5))
		{
			$count = 0;
			$comment = '';
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Charge name</th>
						  <th>Amount</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
				//retrieve the data from array
				for($r = 1; $r < $total_rows; $r++)
				{
					$visit_id = $array[$r][0];
					$invoice_number = $array[$r][1];
					$patient_id = $array[$r][2];
					$visit_date = $array[$r][3];
					$amount_invoiced = $array[$r][4];



					$visit_data = array(
										"branch_code" => $this->session->userdata('branch_code'),
										"visit_date" => $visit_date,
										"patient_id" => $patient_id,
										"personnel_id" => $this->session->userdata('personell_id'),
										"insurance_limit" => '',
										"patient_insurance_number" => '',
										"visit_type" => 1,
										"time_start"=>date('H:i:s'),
										"time_end"=>date('H:i:s'),
										"appointment_id"=>0,
										"close_card"=>0,
										"procedure_done"=>'',
										"insurance_description"=>'',
										"visit_id"=>$visit_id
										//"room_id"=>$room_id,
									);
					if($this->db->insert('visit', $visit_data))
					{
						// insert into the visit charge table
			
						
						$visit_charge_data = array(
							"visit_id" => $visit_id,
							"service_charge_id" => 1,
							"created_by" => $this->session->userdata("personnel_id"),
							"date" => $visit_date,
							"visit_charge_amount" => $amount_invoiced,
							"charged"=>1,
							"visit_charge_delete"=>0,
							"visit_charge_units"=>1,
							"visit_charge_qty"=>1
						);
						if($this->db->insert('visit_charge', $visit_charge_data))
						{

						}
					}
					$count++;
					$response .= '
									<tr class="">
										<td>'.$count.'</td>
										<td>'.$invoice_number.'</td>
										<td>'.$patient_id.'</td>
										<td>'.$visit_date.'</td>
										<td>'.$amount_invoiced.'</td>
									</tr> 
							';
				
				$response .= '</table>';
				
				$return['response'] = $response;
				$return['check'] = TRUE;
			}
		}
		
		//if no products exist
		else
		{
			$return['response'] = 'Charges data not found';
			$return['check'] = FALSE;
		}
		
		return $return;
	}


	// payments
	function import_payment_template()
	{
		$this->load->library('Excel');
		
		$title = 'payments Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Receipt No';
		$report[$row_count][1] = 'Invoice number';
		$report[$row_count][2] = 'Payment Date';
		$report[$row_count][3] = 'Payed By';
		$report[$row_count][4] = 'payment type';
		$report[$row_count][5] = 'payment amount';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_payments($upload_path)
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
			$response2 = $this->sort_csv_payments_data($array);
		
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
	public function sort_csv_payments_data($array)
	{
		//count total rows
		$total_rows = count($array);
		$total_columns = count($array[0]);//var_dump($array);die();
		
		//if products exist in array
		if(($total_rows > 0) && ($total_columns == 6))
		{
			$count = 0;
			$comment = '';
			$items['modified_by'] = $this->session->userdata('personnel_id');
			$response = '
				<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Charge name</th>
						  <th>Amount</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
				//retrieve the data from array
				for($r = 1; $r < $total_rows; $r++)
				{
					$receipt_number = $array[$r][0];
					$visit_id = $array[$r][1];
					$payment_date = $array[$r][2];
					$payed_by = $array[$r][3];
					$payment_type = $array[$r][4];
					$amount_paid = $array[$r][5];

					if($payment_type == 1)
					{
						$payment_method = 1;
					}
					else
					{
						$payment_method = 9;
					}

					$type_payment = 1;
					$transaction_code = $receipt_number;
					$payment_service_id = 3;
					$change = 0;



					$this->db->where('visit_id = '.$visit_id.' AND amount_paid = "'.$amount_paid.'" AND payment_created = "'.$payment_date.'" ');
					$query_amount = $this->db->get('payments');
					if($query_amount->num_rows() == 0)
					{
						$data = array(
							'visit_id' => $visit_id,
							'payment_method_id'=>$payment_method,
							'amount_paid'=>$amount_paid,
							'personnel_id'=>$this->session->userdata("personnel_id"),
							'payment_type'=>$type_payment,
							'transaction_code'=>$transaction_code,
							'payment_service_id'=>$payment_service_id,
							'change'=>$change,
							'payment_created'=>$payment_date,
							'payed_by'=>$payed_by,
							'payment_created_by'=>$this->session->userdata("personnel_id"),
							'approved_by'=>$this->session->userdata("personnel_id"),
							'date_approved'=>$payment_date,
							'is_new'=>1
						);

						$this->db->insert('payments', $data);
					}
					else
					{
						$array_gift['is_new'] = 2;
						$this->db->where('visit_id = '.$visit_id.' AND amount_paid = "'.$amount_paid.'" AND payment_created = "'.$payment_date.'"');
						$this->db->update('payments',$array_gift);
					}	
					
					$count++;
					$response .= '
									<tr class="">
										<td>'.$count.'</td>
										<td>'.$receipt_number.'</td>
										<td>'.$amount_paid.'</td>
										<td>'.$payment_date.'</td>
									</tr> 
							';
				
				
				
				
			}
			$response .= '</tbody></table>';

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

	function import_patients_data_template()
	{
		$this->load->library('Excel');
		
		$title = 'payments Import Template';
		$count=1;
		$row_count=0;
		
		$report[$row_count][0] = 'Patient No';
		$report[$row_count][1] = 'Phone ';
		$report[$row_count][2] = 'Email';
		
		$row_count++;
		
		//create the excel document
		$this->excel->addArray ( $report );
		$this->excel->generateXML ($title);
	}

	public function import_csv_patients_update($upload_path)
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
			$response2 = $this->sort_csv_patient_update_data($array);
		
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
	public function sort_csv_patient_update_data($array)
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
						  <th>Patient Number</th>
						  <th>Phone</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
				//retrieve the data from array
				for($r = 1; $r < $total_rows; $r++)
				{
					$patient_number = $array[$r][0];
					$phone = $array[$r][1];
					$email = $array[$r][2];

			



					$data = array(
								'patient_number' => $patient_number,
								'patient_phone1'=>$phone,
							);
					$this->db->where('patient_number',$patient_number);

					$this->db->update('patients', $data);
					
					$count++;
					$response .= '
									<tr class="">
										<td>'.$count.'</td>
										<td>'.$patient_number.'</td>
										<td>'.$phone.'</td>
									</tr> 
							';
				
				
				
				
			}
			$response .= '</tbody></table>';

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


}
?>