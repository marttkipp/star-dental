<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hospital_administration extends MX_Controller 
{
	var $csv_path;
	function __construct()
	{
		parent:: __construct();
		
		$this->load->model('auth/auth_model');
		$this->load->model('site/site_model');
		$this->load->model('admin/users_model');
		$this->load->model('admin/sections_model');
		$this->load->model('admin/admin_model');
		$this->load->model('reception/database');
		$this->load->model('hr/personnel_model');
		$this->load->model('administration/administration_model');
		$this->load->model('administration/reports_model');
		$this->load->model('companies_model');
		$this->load->model('visit_types_model');
		$this->load->model('departments_model');
		$this->load->model('wards_model');
		$this->load->model('rooms_model');
		$this->load->model('beds_model');
		$this->load->model('services_model');
		$this->load->model('hospital_administration_model');
		$this->load->model('insurance_scheme_model');
		
		$this->csv_path = realpath(APPPATH . '../assets/csv');
		if(!$this->auth_model->check_login())
		{
			redirect('login');
		}
	}
	
	public function index()
	{
		$this->session->unset_userdata('all_transactions_search');
		
		$data['content'] = $this->load->view('administration/dashboard', '', TRUE);
		
		$data['title'] = 'Dashboard';
		$this->load->view('admin/templates/general_page', $data);
	}

	public function update_service_charges($service_id)
	{
		// get
		// get
		$this->db->where('service_id ='.$service_id.' AND service_charge_status = 1 AND visit_type_id <> 1');
		$query = $this->db->get('service_charge');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key) {
				# code...
				$service_charge_id = $key->service_charge_id;
				$service_id = $key->service_id;
				$service_charge_name = $key->service_charge_name;
				$service_charge_amount = $key->service_charge_amount;

				$this->db->where('visit_type_id <> 1');
				$query_type = $this->db->get('visit_type');

				if($query_type->num_rows() > 0)
				{
					foreach ($query_type->result() as $key_query) {
						$visit_type_id = $key_query->visit_type_id;

						$this->db->where('service_charge_name = "'.$service_charge_name.'" AND service_id = '.$service_id.' AND service_charge_status = 1 AND visit_type_id = '.$visit_type_id);
						$query_update = $this->db->get('service_charge');

						if($query_update->num_rows() == 0)
						{

							$insert_query = array(
										'service_charge_amount'=>$service_charge_amount,
										'service_id'=>$service_id,
										'service_charge_name'=> $service_charge_name,
										'visit_type_id'=> $visit_type_id,
										'service_charge_status'=>1,
										'created'=>date('Y-m-d H:i:s'),
										'created_by'=>$this->session->userdata('personnel_id'),
										'modified_by'=>$this->session->userdata('personnel_id')
									);
							// var_dump($insert_query); die();
							$this->db->insert('service_charge',$insert_query);

						}


					}
				}
			}
		}
		$this->session->set_userdata("success_message", "Successfully updated charges ");
		redirect('hospital-administration/service-charges/'.$service_idd);
	}

	function import_invoices_template()
	{
		//export products template in excel 
		 $this->hospital_administration_model->import_invoice_template();
	}

	function import_invoices()
	{
		//open the add new product
		$v_data['title'] = 'Import invoices';
		$data['title'] = 'Import invoices';
		$data['content'] = $this->load->view('invoices', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	function do_invoice_import()
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->hospital_administration_model->import_csv_invoices($this->csv_path);
				
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
		
		//open the add new product
		$v_data['title'] = 'Import Charges';
		$data['title'] = 'Import Charges';
		$data['content'] = $this->load->view('invoices', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}
	function import_payments_template()
	{
		//export products template in excel 
		 $this->hospital_administration_model->import_payment_template();
	}

	function import_payments()
	{
		//open the add new product
		$v_data['title'] = 'Import payments';
		$data['title'] = 'Import payments';
		$data['content'] = $this->load->view('payments', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	function do_payment_import()
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->hospital_administration_model->import_csv_payments($this->csv_path);
				
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
		
		//open the add new product
		$v_data['title'] = 'Import Charges';
		$data['title'] = 'Import Charges';
		$data['content'] = $this->load->view('payments', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}


	function import_patients_template()
	{
		//export products template in excel 
		 $this->hospital_administration_model->import_patients_data_template();
	}

	function import_patients_update()
	{
		//open the add new product
		$v_data['title'] = 'Import payments';
		$data['title'] = 'Import payments';
		$data['content'] = $this->load->view('patients', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}

	function do_patients_update_import()
	{
		if(isset($_FILES['import_csv']))
		{
			if(is_uploaded_file($_FILES['import_csv']['tmp_name']))
			{
				//import products from excel 
				$response = $this->hospital_administration_model->import_csv_patients_update($this->csv_path);
				
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
		
		//open the add new product
		$v_data['title'] = 'Import Patients';
		$data['title'] = 'Import Patients';
		$data['content'] = $this->load->view('patients', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);
	}


	public function update_patient_records()
	{
		$this->db->where('cardnumber >= 2802 AND cardnumber <3000');
		$query_patient = $this->db->get('patients_old');

		if($query_patient->num_rows() > 0)
		{
			foreach ($query_patient->result() as $key => $value) {
				# code...
				$cardnumber = $value->cardnumber;
				$firstname = $value->firstname;
				$middlename = $value->middlename;
				$surname = $value->surname;
				$dateofbirth = $value->dateofbirth;
				$gender = $value->gender;
				$phonenumber = $value->phonenumber;
				$email = $value->email;
				$occupation = $value->occupation;
				$registrationdate = date('Y-m-d',strtotime($value->registrationdate));

				// check if patient number exist

				$this->db->where('patient_number',$cardnumber);
				$query_patient = $this->db->get('patients');

				if($query_patient->num_rows() == 0)
				{

					if($gender == "Male")
					{
						$array['gender_id'] = 1;
					}
					else
					{
						$array['gender_id'] = 2;
					}
					// do an insert 
					$array['patient_id'] = $cardnumber;
					$array['patient_number'] = $cardnumber;
					$array['patient_phone1'] = $phonenumber;
					$array['patient_email'] = $email;
					$array['patient_surname'] = $surname;
					$array['patient_othernames'] = $firstname.' '.$middlename;
					$array['patient_town'] = $occupation;
					$array['patient_date_of_birth'] = $dateofbirth;
					$array['patient_date'] = $registrationdate;
					$array['is_new'] = 1;

					$this->db->insert('patients',$array);

					


				}
				else
				{
					$array_list['is_new'] = 2;
					$this->db->where('patient_number',$cardnumber);
					$this->db->update('patients',$array_list);
				}


			}
			echo "completed";
		}
	}


	public function update_invoices_payments()
	{

		// select * bills  with this card number

		$this->db->where('is_new',1);
		$query_items = $this->db->get('patients');

		if($query_items->num_rows() > 0)
		{
			foreach ($query_items->result() as $key => $value) {
				# code...
				$patient_id = $value->patient_id;

				// select * bills

				$this->db->where('bills.visit_id = patient_visit.id AND patient_visit.cardnumber = '.$patient_id);
				$query_item = $this->db->get('bills,patient_visit');

				if($query_item->num_rows() > 0)
				{
					foreach ($query_item->result() as $keyviews => $value_charged) {
						# code...
						$invoice_number = $value_charged->invoice_number;
						$amount = $value_charged->amount;
						$visit_date = $value_charged->datein;
						$visit_date = date('Y-m-d',strtotime($visit_date));

						// check if the visit exisit

						$this->db->where('visit_id',$invoice_number);
						$query_invoice = $this->db->get('visit');

						if($query_invoice->num_rows() == 0)
						{
							// insert all invoices and all payments 
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
												"invoice_number"=>$invoice_number,
												"visit_id"=>$invoice_number,
												"is_new"=>1,
											);
							if($this->db->insert('visit', $visit_data))
							{
								// insert into the visit charge table

								
								$visit_charge_data = array(
									"visit_id" => $invoice_number,
									"service_charge_id" => 1,
									"created_by" => $this->session->userdata("personnel_id"),
									"date" => $visit_date,
									"visit_charge_amount" => $amount,
									"charged"=>1,
									"visit_charge_delete"=>0,
									"visit_charge_units"=>1,
									"visit_charge_qty"=>1
								);
								if($this->db->insert('visit_charge', $visit_charge_data))
								{

								}
							}
							$this->db->where('invoice_number = '.$invoice_number);
							$array_update['update'] = 1;
							$this->db->update('bills',$array_update);
						}
						else
						{
							$this->db->where('invoice_number = '.$invoice_number);
							$array_update['update'] = 2;
							$this->db->update('bills',$array_update);
						}

						
					}
				}
				

				$array_list2['invoiced'] = 1;
				$this->db->where('patient_id',$patient_id);
				$this->db->update('patients',$array_list2);
			}
		}

		echo "completed";
		
	}

	public function update_patients_payments()
	{		

		// select * bills
		$this->db->where('bills.id = payments_old.bill_id AND payments_old.checked = 0');
		$this->db->select('payments_old.amount AS paid,payments_old.id AS payment_id,bills.*,payments_old.*');
		$query_item = $this->db->get('bills,payments_old');

		if($query_item->num_rows() > 0)
		{
			foreach ($query_item->result() as $key => $value) {
				# code...
				$visit_id = $value->invoice_number;
				$amount_paid = $value->paid;
				$receipt_number = $value->receipt_number;
				$payment_date = date('Y-m-d',strtotime($value->date_paid));
				$payed_by = $value->paid_by;
				$pay_type = $value->pay_type;
				$payment_id = $value->payment_id;

				if($pay_type == 1)
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

				// check if the receipt is already in
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
					$this->db->where('visit_id = '.$visit_id.' AND transaction_code = "'.$transaction_code.'"');
					$this->db->update('payments',$array_gift);
				}	

				$array_gift2['checked'] = 1;
				$this->db->where('id = '.$payment_id.'');
				$this->db->update('payments_old',$array_gift2);		

			}
		}
		echo "success";
	}



	public function delete_department_account($department_account_id,$department_id)
	{
		$array['deleted'] = 1;
		$array['deleted_by'] = $this->session->userdata('personnel_id');

		$this->db->where('department_account_id',$department_account_id);

		if ($this->db->update('department_account',$array))
		{
			$this->session->set_userdata("success_message", "You have successfully deleted the account from department");
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
		}

		redirect('hospital-administration/department-accounts/'.$department_id);
	}
}
?>