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
				'installment'=>$this->input->post('installment'),
				'depriciation_type'=>$this->input->post('depriciation_type'),
				'rate'=>$this->input->post('rate'),
				'asset_value'=>$this->input->post('asset_cost'),
				'created'=>date('Y-m-d H:i:s')
				
			);

			
		$this->db->where('asset_id', $asset_id);
		if($this->db->update('assets_details', $data))
		{

			// create amortization chart 

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

			 $this->db->where('asset_id',$asset_id);
			 $this->db->delete('asset_amortization');


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
			       
			        	// var_dump($repayment_date); die();
			        $insert_array['interest_amount'] = $interest_payment;
			        $insert_array['principal_amount'] = $principal_payment;
			        $insert_array['amortizationDate'] = $repayment_date;
			        $insert_array['personnel_id'] = $personnel_id;
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