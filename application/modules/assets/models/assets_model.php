<?php
    class Assets_model extends CI_Model 
    {	

 public function get_all_asset($table, $where, $config, $page, $order, $order_method = 'ASC')
    	
     {
    		//retrieve all users
    		$this->db->select('*');
    		$this->db->where($where);
    		$this->db->order_by($order, $order_method);
    		$query = $this->db->get($table, $config, $page);
    		
    		return $query;
	  }		

 	public function add_asset_details()
	{
		$depriciation_type = $interest_id = $this->input->post('depriciation_type');
		$usefull_life = 0;
		$rate = 0;
		$salvage_value = 0;
		
		if($depriciation_type == 1)
        {

            $usefull_life = $this->input->post('usefull_life'); 
            $rate = 1; 
            $salvage_value = $this->input->post('salvage_value'); 
        }
        else if($depriciation_type == 2)
        {
          
            $usefull_life = $this->input->post('installment'); 
            $rate = $this->input->post('rate'); 
            $salvage_value = $this->input->post('salvage'); 
        }
        $purchase_date = $first_date = $this->input->post('asset_pd_period');
        // $actual_first_date = date('M Y', strtotime($purchase_date));
        $explode = explode('-', $purchase_date);
        $month = $explode[1];
        $day = $explode[2];
		$data = array(
				'asset_name'=>ucwords(strtolower($this->input->post('asset_name'))),
				'asset_id'=>$this->input->post('asset_id'),
				'asset_serial_no'=>$this->input->post('asset_serial_no'),
				'asset_model_no'=>$this->input->post('asset_model_no'),
				'asset_description'=>$this->input->post('asset_description'),
				'asset_pd_period'=>$this->input->post('asset_pd_period'),
				'ldl_type'=>$this->input->post('ldl_type'),
				'ldl_date'=>$this->input->post('ldl_date'),
				'asset_supplier_no'=>$this->input->post('asset_supplier_no'),
				'asset_project_no'=>$this->input->post('asset_project_no'),
				'asset_owner_name'=>$this->input->post('asset_owner_name'),
				'asset_inservice_period'=>$this->input->post('asset_inserivce_period'),
				'asset_category_id'=>$this->input->post('asset_category_id'),
				'asset_disposal_period'=>$this->input->post('asset_disposal_period'),
				'asset_category_id' =>$this->input->post('asset_category_id'),
				'duration'=>$this->input->post('duration'),
				'installment'=>$usefull_life,
				'depriciation_type'=>$this->input->post('depriciation_type'),
				'rate'=>$rate,
				'asset_number'=>$this->input->post('asset_number'),
				'asset_value'=>$this->input->post('asset_cost'),
				'salvage_value'=>$salvage_value,
				'created'=>date('Y-m-d H:i:s')
				
			);


			
		if($this->db->insert('assets_details', $data))
		{
			$asset_id = $this->db->insert_id();


		


			$cummulative_interest = 0;
			$cummulative_principal = 0;
			$start_balance = $loan_amount;
			$total_days = 0;
			$count = 0;
			$depreciation_amount = 0;

			$loan_amount = $this->input->post('asset_cost');
			$salvage_value = $salvage_value;
			$no_of_repayments = $usefull_life;


			if($interest_id == 1)
			{

				$depreciation_amount = ($loan_amount - $salvage_value) / $no_of_repayments;
				$installment_type_duration = $no_of_repayments;
				$balance = $loan_amount;
			}

			if($interest_id == 2)
			{
			
				$inital_value = $loan_amount - $salvage_value; // less salvage value after passing
				$balance = $loan_amount - $salvage_value; //month 

				$interest_rate = $rate;

			}
			if($interest_id == 0)
			{
				$inital_value = $loan_amount;
				$balance = $inital_value;
				$no_of_repayments = 50;
			}

			for($r = 0; $r < $no_of_repayments; $r++)
			{
				//Amount Calculation

				if($interest_id == 1)
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));
					
					$main_amount = $balance;
					$interest_cal = $depreciation_amount;
					$principal = 0;
					$balance = $balance - $depreciation_amount;

					$count++;

				}
				else if($interest_id == 2)
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));

					$main_amount = $balance;
					$interest_cal = $main_amount * ($interest_rate/100);
					$principal = 0;
					$balance = $main_amount - $interest_cal;
					$count++;
					
				}
				else
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));

					$main_amount = $balance;
					$interest_cal = 0;
					$principal = 0;
					$balance = $main_amount;
					$count++;
				}

				if($interest_id == 1)
				{

					$insert_array['interest_amount'] = $interest_cal;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			        $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = $interest_cal;
			        $insert_array['cummulativePrincipal'] = $interest_cal;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);



					
				}
				else if($interest_id == 2)
				{

					if($count == $no_of_repayments)
					{
						$balance += $salvage_value;
					}


					$insert_array['interest_amount'] = $interest_cal;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			         $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = $interest_cal;
			        $insert_array['cummulativePrincipal'] = $interest_cal;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);

					$main_amount = $balance;

				} 
				else
				{
					$insert_array['interest_amount'] = 0;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			        $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = 0;
			        $insert_array['cummulativePrincipal'] = 0;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);
				}   
			} 	
			return TRUE; 
		}
		else
		{
			return FALSE;
		}
    }


     public function add_asset_details_old()
	{

		$data = array(
				'asset_name'=>ucwords(strtolower($this->input->post('asset_name'))),
				'asset_id'=>$this->input->post('asset_id'),
				'asset_serial_no'=>$this->input->post('asset_serial_no'),
				'asset_model_no'=>$this->input->post('asset_model_no'),
				'asset_description'=>$this->input->post('asset_description'),
				'asset_pd_period'=>$this->input->post('asset_pd_period'),
				'ldl_type'=>$this->input->post('ldl_type'),
				'ldl_date'=>$this->input->post('ldl_date'),
				'asset_supplier_no'=>$this->input->post('asset_supplier_no'),
				'asset_project_no'=>$this->input->post('asset_project_no'),
				'asset_owner_name'=>$this->input->post('asset_owner_name'),
				'asset_inservice_period'=>$this->input->post('asset_inserivce_period'),
				'asset_category_id'=>$this->input->post('asset_category_id'),
				'asset_disposal_period'=>$this->input->post('asset_disposal_period'),
				'asset_category_id' =>$this->input->post('asset_category_id'),
				'duration'=>$this->input->post('duration'),
				'installment'=>$this->input->post('installment'),
				'depriciation_type'=>$this->input->post('depriciation_type'),
				'rate'=>$this->input->post('rate'),
				'asset_number'=>$this->input->post('asset_number'),
				'asset_value'=>$this->input->post('asset_cost'),
				'created'=>date('Y-m-d H:i:s')
				
			);
			
		if($this->db->insert('assets_details', $data))
		{
			$asset_id = $this->db->insert_id();

			// asset value is the asset value on purchase
			$asset_value = $this->input->post('asset_cost');

			// no of repayments is the period that you can allow to have the product in your castody

			$no_of_repayments = $this->input->post('installment');

			// first date is the purchase date of the product
			$first_date = $this->input->post('asset_pd_period');

			//interest id:  1 for straight line and 2 for redusing balance
			$interest_id = $this->input->post('depriciation_type');

			// interest rate could mean the rate at which the product is appretiating in percentage
			$interest_rate = $this->input->post('rate');

			// installment type duration is the period interval the product is appretiating or depreciating
			$installment_type_duration = $this->input->post('duration');

			


			if($asset_value > 0)
			{
			   
			    $cummulative_interest = 0;
			    $cummulative_principal = 0;
			    $start_balance = $asset_value;
			    $total_days = 0;
			   
			    //display all payment dates
			    for($r = 0; $r < $no_of_repayments; $r++)
			    {
			        $total_days += $installment_type_duration;
			        $count = $r+1;
			        $repayment_date = strtotime($first_date. ' + '.$total_days.' days');
			        $repayment_date = date('Y-m-d', strtotime($first_date. ' + '.$total_days.' days'));
			        $payment_date = date('jS M Y', strtotime($first_date. ' + '.$total_days.' days'));
			       
			        //straight line
			        if($interest_id == 1)
			        {
			            //$interest_payment = ($asset_value * ($interest_rate/100)) / $no_of_repayments;
			            $interest_payment = ($asset_value * ($interest_rate/100));
			        }
			       
			        //reducing balance
			        else
			        {
			            //$interest_payment = ($start_balance * ($interest_rate/100)) / $no_of_repayments;
			            $interest_payment = ($start_balance * ($interest_rate/100));
			        }
			        $principal_payment = round(($asset_value / $no_of_repayments),-3);
			        $end_balance = $start_balance - $principal_payment;
			        $cummulative_interest += $interest_payment;
			        $cummulative_principal += $principal_payment;
			       
			        if ($count == $no_of_repayments)
			        {
			            $principal_payment = $start_balance;
			            $end_balance = $start_balance - $principal_payment;
			            $cummulative_principal = $asset_value;
			        }
			       

			        $insert_array['interest_amount'] = $interest_payment;
			        $insert_array['principal_amount'] = $principal_payment;
			        $insert_array['amortizationDate'] = $repayment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $start_balance;
			        $insert_array['endBalance'] = $end_balance;
			        $insert_array['cummulativeInterest'] = $cummulative_interest;
			        $insert_array['cummulativePrincipal'] = $cummulative_principal;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);
			       
			       
			        $start_balance -= $principal_payment;
			    }   

			   
			   
			}
			return TRUE;
		}
		else{
			return FALSE;
		}
     }
    public function update_asset($asset_id)
	{

		$depriciation_type = $interest_id = $this->input->post('depriciation_type');

		$usefull_life = 0;
		$rate = 0;
		$salvage_value = 0;
		if($depriciation_type == 1)
        {

            $usefull_life = $this->input->post('usefull_life'); 
            $rate = 1; 
            $salvage_value = $this->input->post('salvage_value'); 
        }
        else if($depriciation_type == 2)
        {
          
            $usefull_life = $this->input->post('installment'); 
            $rate = $this->input->post('rate'); 
            $salvage_value = $this->input->post('salvage'); 
        }
        $purchase_date = $first_date = $this->input->post('asset_pd_period');
        // $actual_first_date = date('M Y', strtotime($purchase_date));
        $explode = explode('-', $purchase_date);
        $month = $explode[1];
        $day = $explode[2];


		$data = array(
				'asset_name'=>$this->input->post('asset_name'),
				'asset_model_no'=>$this->input->post('asset_model_no'),
				'asset_serial_no'=>$this->input->post('asset_serial_no'),
				'asset_description'=>$this->input->post('asset_description'),
				'asset_pd_period'=>$this->input->post('asset_pd_period'),
				'ldl_type'=>$this->input->post('ldl_type'),
				'ldl_date'=>$this->input->post('ldl_date'),
				'asset_supplier_no'=>$this->input->post('asset_supplier_no'),
				'asset_project_no'=>$this->input->post('asset_project_no'),
				'asset_owner_name'=>$this->input->post('asset_owner_name'),
				'asset_inservice_period'=>$this->input->post('asset_inserivce_period'),
				'asset_disposal_period'=>$this->input->post('asset_disposal_period'),
				'asset_category_id'=>$this->input->post('asset_category_id'),
				'asset_number'=>$this->input->post('asset_number'),
				'duration'=>$this->input->post('duration'),
				'installment'=>$usefull_life,
				'depriciation_type'=>$this->input->post('depriciation_type'),
				'rate'=>$rate,
				'asset_number'=>$this->input->post('asset_number'),
				'asset_value'=>$this->input->post('asset_cost'),
				'salvage_value'=>$salvage_value,
				'created'=>date('Y-m-d H:i:s')
				
			);

			
		$this->db->where('asset_id', $asset_id);
		if($this->db->update('assets_details', $data))
		{


			 $this->db->where('asset_id',$asset_id);
			 $this->db->delete('asset_amortization');



			$cummulative_interest = 0;
			$cummulative_principal = 0;
			$start_balance = $loan_amount;
			$total_days = 0;
			$count = 0;
			$depreciation_amount = 0;

			$loan_amount = $this->input->post('asset_cost');
			$salvage_value = $salvage_value;
			$no_of_repayments = $usefull_life;


			if($interest_id == 1)
			{

				$depreciation_amount = ($loan_amount - $salvage_value) / $no_of_repayments;
				$installment_type_duration = $no_of_repayments;
				$balance = $loan_amount;
			}

			if($interest_id == 2)
			{
			
				$inital_value = $loan_amount - $salvage_value; // less salvage value after passing
				$balance = $loan_amount - $salvage_value; //month 

				$interest_rate = $rate;

			}

			if($interest_id == 0)
			{
				$inital_value = $loan_amount;
				$balance = $inital_value;
				$no_of_repayments = 50;
			}

			for($r = 0; $r < $no_of_repayments; $r++)
			{
				
				
				//Amount Calculation

				if($interest_id == 1)
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));
					
					$main_amount = $balance;
					$interest_cal = $depreciation_amount;
					$principal = 0;
					$balance = $balance - $depreciation_amount;

					$count++;

				}
				else if($interest_id == 2)
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));

					$main_amount = $balance;
					$interest_cal = $main_amount * ($interest_rate/100);
					$principal = 0;
					$balance = $main_amount - $interest_cal;
					$count++;
					
				}
				else
				{
					$payment_date = date('Y', strtotime($first_date. ' + '.$r.' year'));

					$main_amount = $balance;
					$interest_cal = 0;
					$principal = 0;
					$balance = $main_amount;
					$count++;
				}

				if($interest_id == 1)
				{

					$insert_array['interest_amount'] = $interest_cal;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			        $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = $interest_cal;
			        $insert_array['cummulativePrincipal'] = $interest_cal;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);



					
				}
				else if($interest_id == 2)
				{

					if($count == $no_of_repayments)
					{
						$balance += $salvage_value;
					}


					$insert_array['interest_amount'] = $interest_cal;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			         $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = $interest_cal;
			        $insert_array['cummulativePrincipal'] = $interest_cal;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);

					$main_amount = $balance;

				} 
				else
				{
					$insert_array['interest_amount'] = 0;
			        $insert_array['principal_amount'] = $loan_amount;
			        $insert_array['amortizationDate'] = $payment_date.'-'.$month.'-'.$day;
			        $insert_array['amortizationYear'] = $payment_date;
			        $insert_array['personnel_id'] = $this->session->userdata('personnel_id');
			        $insert_array['startBalance'] = $main_amount;
			        $insert_array['endBalance'] = $balance;
			        $insert_array['cummulativeInterest'] = 0;
			        $insert_array['cummulativePrincipal'] = 0;
			        $insert_array['repayment'] = $r;
			        $insert_array['asset_id'] = $asset_id;

			        $this->db->where('asset_id',$asset_id);
			        $this->db->insert('asset_amortization',$insert_array);
				}   
			} 	
			return TRUE; 
		}
		else{
			return FALSE;
		}
	}

 public function get_asset($asset_id)
	
	  {
		//retrieve all users
		$this->db->from('assets_details');
		$this->db->select('*');
		$this->db->where('asset_id = '.$asset_id);
		$query = $this->db->get();
		
		return $query;    	
 
     }	

   public function delete_asset($asset_id)
	{
		if($this->db->delete('assets_details', array('asset_id' => $asset_id)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

  }


   public function activate_asset($asset_id)
	 {
		$data = array(
				'asset_status' => 1
			);
		$this->db->where('asset_id', $asset_id);
		
		if($this->db->update('assets_details', $data))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}

	 }
public function deactivate_asset($asset_id)
	{
		$data = array(
				'asset_status' => 0
			);
		$this->db->where('asset_id', $asset_id);
		
		if($this->db->update('assets_details', $data))
		{
			return TRUE;
		}
		else{
			return FALSE;
		}
	}	 
	  

}	

?>