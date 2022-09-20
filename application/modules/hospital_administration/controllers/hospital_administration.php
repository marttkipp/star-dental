<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
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
		$this->load->model('reception/reception_model');
		$this->load->model('accounting/accounting_model');
		$this->load->model('services_model');
		$this->load->model('accounts/accounts_model');
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
		redirect('administration/service-charges/'.$service_idd);
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

		redirect('administration/department-accounts/'.$department_id);
	}




	// STart of updating items 


	public function update_patient_bills()
	{
		$this->db->select('visit_bill.*,visit.visit_type,visit.patient_id,visit.visit_date,visit.time_end,visit.time_start,visit.personnel_id,visit.visit_date');
		$this->db->where('visit_bill.visit_parent = visit.visit_id');
		$query = $this->db->get('visit_bill,visit');


		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_id = $value->visit_id;
				$visit_parent = $value->visit_parent;
				$visit_bill_amount = $value->visit_bill_amount;
				$visit_bill_reason = $value->visit_bill_reason;
				$created = $value->visit_date;
				$patient_id = $value->patient_id;
				$personnel_id = $value->personnel_id;
				$visit_type = $value->visit_type;
				$timepicker_start = $value->time_start;
				$timepicker_end = $value->time_end;





				// add a value with negative value to the visit charge table

				$array['service_charge_id'] = 2675;
				$array['visit_id'] = $visit_parent;
				$array['visit_charge_units'] = 1;
				$array['visit_charge_notes'] = $visit_bill_reason;
				$array['visit_charge_amount'] = -$visit_bill_amount;
				$array['date']= $created;
				$array['charge_to']= $visit_type;
				$array['charged']= 1;
				$array['patient_id']= $patient_id;
				// var_dump($array);die();

				$this->db->insert('visit_charge',$array);



				// create a cash invoice

				if($visit_id == $visit_parent)
				{

					// create a visit and enter the details of the visit charge

					// check if there is a debit note if its there then dont create another visit but use the same to 



					$visit_data = array(
											"branch_code" => $this->session->userdata('branch_code'),
											"visit_date" => $created,
											"patient_id" => $patient_id,
											"personnel_id" => $personnel_id,
											"insurance_limit" => '',
											"patient_insurance_number" => '',
											"visit_type" => 1,
											"time_start"=>$timepicker_start,
											"time_end"=>$timepicker_end,
											"appointment_id"=>0,
											"close_card"=>2,
											"procedure_done"=>'',
											"insurance_description"=>'',
											"dental_visit"=>0,
											"parent_visit"=>$visit_parent
										);
					$this->db->insert('visit', $visit_data);
					$visit_idd = $this->db->insert_id();


					$array['service_charge_id'] = 2675;
					$array['visit_id'] = $visit_idd;
					$array['visit_charge_units'] = 1;
					$array['visit_charge_notes'] = $visit_bill_reason;
					$array['visit_charge_amount'] = $visit_bill_amount;
					$array['date']= $created;
					$array['charge_to'] = 1;
					$array['charged']= 1;
					$array['patient_id']= $patient_id;
					$this->db->insert('visit_charge',$array);

					// update payments that have been made by this visit parent that are in cash to have this new id

					$payment_array['visit_id'] = $visit_idd;
					$this->db->where('visit_id ='.$visit_parent.' AND payment_method_id <> 9 AND payment_type = 1');
					$this->db->update('payments',$payment_array);


				}
				else
				{
					$array['service_charge_id'] = 2675;
					$array['visit_id'] = $visit_id;
					$array['visit_charge_units'] = 1;
					$array['visit_charge_notes'] = $visit_bill_reason;
					$array['visit_charge_amount'] = $visit_bill_amount;
					$array['date']= $created;
					$array['charge_to'] = 1;
					$array['charged']= 1;
					$array['patient_id']= $patient_id;

					$this->db->insert('visit_charge',$array);
				}


			}
		}
	}

	public function update_visit_debit_notes()
	{
		$this->db->where('payments.visit_id = visit.visit_id  AND payments.payment_type = 3');
		$this->db->select('payments.*,visit.patient_id AS patient,visit.visit_id,payments.approved_by,payments.payment_id,visit.visit_type');
		$this->db->order_by('payments.payment_id','ASC');
		$query = $this->db->get('payments,visit');

		// var_dump($query);die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$payment_created = $value->payment_created;
				$amount_paid = $value->amount_paid;
				$patient_id = $value->patient;
				$payment_created = $value->payment_created;
				$visit_id = $value->visit_id;
				$created_by = $value->approved_by;
				$payment_id = $value->payment_id;
				$visit_type = $value->visit_type;

				$reason = $value->reason;


				// $document_number = $this->accounts_model->create_visit_credit_note_number();
					
		
				$array['service_charge_id'] = 2675;
				$array['visit_id'] = $visit_id;
				$array['visit_charge_units'] = 1;
				$array['visit_charge_notes'] = $reason;
				$array['visit_charge_comment'] = $reason;
				$array['visit_charge_amount'] = $amount_paid;
				$array['date']= $payment_created;
				$array['charge_to'] = $visit_type;
				$array['charged']= 1;
				$array['patient_id']= $patient_id;

				$this->db->insert('visit_charge',$array);

				
			}
		}
	}

	public function update_visit_invoices()
	{
		// var_dump("sdasda");die();
		$this->db->select('visit_charge.*,visit.*,visit.patient_id AS patient');
		$this->db->where('visit_charge.visit_invoice_id = 0  AND visit.visit_id = visit_charge.visit_id');
		$this->db->group_by('visit_charge.visit_id,visit.visit_type');
		$this->db->order_by('visit_charge.visit_charge_id','ASC');
		$query = $this->db->get('visit_charge,visit');
		// var_dump($query);die();

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_date = $value->visit_date; 
				$visit_id = $value->visit_id; 
				$patient_id = $value->patient; 
				// $date = $value->date; 
				// $time = $value->time; 
				$visit_type = $value->visit_type; 
				$invoice_number = $value->invoice_number; 
				$charge_to = $value->visit_type; 
				$patient_insurance_number = $value->patient_insurance_number; 
				$visit_type = $value->visit_type; 
				$invoice_month = date('m',strtotime($value->visit_date)); 
				$suffix = $value->visit_id; 
				$invoice_year = date('Y',strtotime($value->visit_date));  
				$insurance_description = $value->insurance_description; 
				$parent_visit = $value->parent_visit; 
				$patient_id = $value->patient; 


				if(empty($charge_to))
				{
					$charge_to = $visit_type;
				}
				$branch_code = $this->session->userdata('branch_code');

				// if($parent_visit > 0)
				// {

				// }
				// else
				// {
					$insert_array['visit_invoice_number'] = $visit_id;
					$insert_array['created'] = $value->visit_date;
					$insert_array['patient_id'] = $patient_id;
					$insert_array['visit_id'] = $visit_id;
					$insert_array['prefix'] = $branch_code;
					$insert_array['suffix'] = $visit_id;
					$insert_array['bill_to'] = $charge_to;
					// $insert_array['invoice_month'] = $invoice_month;
					// $insert_array['invoice_year'] = $invoice_year;

					$insert_array['scheme_name'] = $insurance_description;

					$insert_array['member_number'] = $patient_insurance_number;



					if($charge_to != 1)
					{
						$insert_array['preauth_date'] = $visit_date;
					}

					// var_dump($insert_array);die();

					$this->db->insert('visit_invoice',$insert_array);
					$visit_invoice_id = $this->db->insert_id();


					// update the visit_charge
					$update_array['patient_id'] = $patient_id;
					$update_array['visit_invoice_id'] = $visit_invoice_id;
					$this->db->where('visit_id',$visit_id);
					$this->db->update('visit_charge',$update_array);
				// }
				
		

			}
		}
	}

	public function update_visit_payments()
	{

		// update payments set cancel = 1, cancel_description = 'LACKED PAYMENT METHOD' where payment_method_id = 0
		
		$update_array_one['cancel'] = 1;
		$update_array_one['cancel_description'] = 'LACKED PAYMENT METHOD';
		$this->db->where('payment_method_id = 0 AND payment_type = 1');
		$this->db->update('payments',$update_array_one);




		$this->db->where('payments.visit_id = visit.visit_id AND payments.payment_type = 1  AND visit.visit_id = visit_invoice.visit_id');
		$this->db->select('payments.*,visit.patient_id AS patient,visit_invoice.visit_invoice_id,visit.visit_id,payments.approved_by,payments.payment_id');
		$this->db->order_by('payments.payment_id','ASC');
		$query = $this->db->get('payments,visit,visit_invoice');

		// var_dump($query);die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$payment_created = $value->payment_created;
				$amount_paid = $value->amount_paid;
				$patient_id = $value->patient;
				$payment_created = $value->payment_created;
				$visit_invoice_id = $value->visit_invoice_id;
				$visit_id = $value->visit_id;
				$created_by = $value->approved_by;
				$payment_id = $value->payment_id;


				$array['patient_id'] = $patient_id;
				$array['payment_id'] = $payment_id;
				$array['visit_invoice_id'] = $visit_invoice_id;
				$array['visit_id'] = $visit_id;
				$array['payment_item_amount'] = $amount_paid;
				$array['created_by'] = $created_by;
				$array['created'] = $payment_created;
				$array['invoice_type'] = 1;
				$array['payment_item_deleted'] = 0;

				$this->db->insert('payment_item',$array);

				$array_two['payment_date'] = $payment_created;
				$array_two['patient_id'] = $patient_id;
				$array_two['confirm_number'] = $payment_id;
				$this->db->where('payment_id',$payment_id);
				$this->db->update('payments',$array_two);
				
			}
		}
	}

	public function update_visit_credit_notes()
	{
		$this->db->where('payments.visit_id = visit.visit_id AND visit.visit_id = visit_invoice.visit_id AND payments.payment_type = 3');
		$this->db->select('payments.*,visit.patient_id AS patient,visit_invoice.visit_invoice_id,visit.visit_id,payments.approved_by,payments.payment_id');
		$this->db->order_by('payments.payment_id','ASC');
		$query = $this->db->get('payments,visit,visit_invoice');

		// var_dump($query);die();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$payment_created = $value->payment_created;
				$amount_paid = $value->amount_paid;
				$patient_id = $value->patient;
				$payment_created = $value->payment_created;
				$visit_invoice_id = $value->visit_invoice_id;
				$visit_id = $value->visit_id;
				$created_by = $value->approved_by;
				$payment_id = $value->payment_id;

				$reason = $value->reason;


				
				$document_number = $this->accounts_model->create_visit_credit_note_number();
					
		
				$insertarray['patient_id'] = $patient_id;
				$insertarray['visit_id'] = $visit_id;
				$insertarray['created_by'] = $created_by;	

		    	$amount_paid = str_replace(",", "", $amount_paid);
				$amount_paid = str_replace(".00", "", $amount_paid);
				$insertarray['visit_cr_note_amount'] = $amount_paid;
				$insertarray['created'] = $payment_created;
				$insertarray['visit_invoice_id'] = $visit_invoice_id;
		      // var_dump($insertarray);die();
		      	$this->db->insert('visit_credit_note', $insertarray);
		      

		        $visit_credit_note_id = $this->db->insert_id();

				$visit_data = array(
										'service_charge_id'=>2675,
										'visit_id'=>$visit_id,
										'patient_id'=>$patient_id,
										'visit_cr_note_amount'=>$amount_paid,
										'visit_cr_note_units'=>1,
										'created_by'=>$created_by,
										'visit_cr_note_comment'=>$reason,
										'date'=>date("Y-m-d")
									);

	
				$visit_data['visit_credit_note_id'] = $visit_credit_note_id;
				$this->db->insert('visit_credit_note_item', $visit_data);
				
			}
		}
	}


	

	
	public function view_batch_items_old($batch_receipt_id,$insurance_id)
	{

		$where = 'account.account_id = batch_receipts.bank_id AND batch_receipts.batch_receipt_id = batch_payments.batch_receipt_id AND batch_payments.batch_receipt_id ='.$batch_receipt_id;
		$table = 'batch_receipts,account,batch_payments';
		//pagination
		$segment = 5;
		$this->load->library('pagination');
		$config['base_url'] = site_url().'hospital_administration/view_batch_items/'.$batch_receipt_id.'/'.$insurance_id;
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
		$query = $this->hospital_administration_model->get_batch_receipts_payments($table, $where, $config["per_page"], $page);
		
		//change of order method 
		
		
		$data['title'] = 'Payments';		
		$v_data['query'] = $query;
		$v_data['page'] = $page;
		// var_dump($query);die();
		//open the add new product
		$v_data['title'] = 'Batch Items';
		$data['title'] = 'Batch Items';
		$data['content'] = $this->load->view('view_batch_items', $v_data, true);
		$this->load->view('admin/templates/general_page', $data);

	}

	
	public function update_invoices_status()
	{


		$this->db->where('visit_invoice_delete',0);
		$query = $this->db->get('visit_invoice');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_invoice_id = $value->visit_invoice_id;


				$total_invoice_amount = $this->accounts_model->get_visit_invoice_total($visit_invoice_id);
				$total_payments = $this->accounts_model->get_visit_invoice_payments($visit_invoice_id);
				$credit_note = $this->accounts_model->get_visit_invoice_credit_notes($visit_invoice_id);

				$balance = $total_invoice_amount - ($total_payments+$credit_note);
				// update visit invoice


				if($balance > 0)
				{
					$update_array['visit_invoice_status'] = 0;
				}
				else if($balance == 0)
				{
					$update_array['visit_invoice_status'] = 1;
				}
				else if($balance < 0)
				{
					$update_array['visit_invoice_status'] = 2;
				}
				else
				{
					$update_array['visit_invoice_status'] = 3;
				}


				$this->db->where('visit_invoice_id',$visit_invoice_id);
				$this->db->update('visit_invoice',$update_array);
			}
		}
		
	}


	public function update_items()
	{
		$this->db->where('visit_invoice_number > 0');
		$query = $this->db->get('visit_invoice');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$visit_invoice_number = $value->visit_invoice_number;
				$prefix = $value->prefix;
				$suffix = $value->suffix;
				$visit_invoice_id = $value->visit_invoice_id;
				$branch_code = $this->session->userdata('branch_code');

				// if($suffix < 10)
				//  {
				//  	$number = '00000'.$suffix;
				//  }
				//  else if($suffix < 100 AND $suffix >= 10)
				//  {
				//  	$number = '0000'.$suffix;
				//  }
				//  else if($suffix < 1000 AND $suffix >= 100)
				//  {
				//  	$number = '000'.$suffix;
				//  }
				//  else if($suffix < 10000 AND $suffix >= 1000)
				//  {
					$number = '00'.$suffix;
				 // }
				 // else if($suffix < 100000 AND $suffix >= 10000)
				 // {
				 // 	$number = '0'.$suffix;
				 // }

				 $invoice_number = $branch_code.'-INV-'.$number;


				$array['visit_invoice_number'] = $invoice_number;
				$this->db->where('visit_invoice_id',$visit_invoice_id);
				$this->db->update('visit_invoice',$array);
			}
		}

	}

	public function update_dentists()
	{
		$this->db->where('visit_invoice.dentist_id = 0 AND visit.visit_id = visit_invoice.visit_id');
		$this->db->select('visit_invoice.visit_invoice_id,visit.personnel_id');
		$query = $this->db->get('visit_invoice,visit');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				# code...
				$personnel_id = $value->personnel_id;
				
				$visit_invoice_id = $value->visit_invoice_id;
				


				$array['dentist_id'] = $personnel_id;
				$this->db->where('visit_invoice_id',$visit_invoice_id);
				$this->db->update('visit_invoice',$array);
			}
		}

	}
	

	
}
?>