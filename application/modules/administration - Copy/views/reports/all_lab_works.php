<!-- search -->
<?php echo $this->load->view('search/invoices', '', TRUE);?>
<!-- end search -->
<?php //echo $this->load->view('transaction_statistics', '', TRUE);?>
 
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>             

          <!-- Widget content -->
        <div class="panel-body">
		<?php
		$error = $this->session->userdata('error_message');
		$success = $this->session->userdata('success_message');
		
		if(!empty($error))
		{
			echo '<div class="alert alert-danger">'.$error.'</div>';
			$this->session->unset_userdata('error_message');
		}
		
		if(!empty($success))
		{
			echo '<div class="alert alert-success">'.$success.'</div>';
			$this->session->unset_userdata('success_message');
		}
		$result = '';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_invoice_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Visit Date</th>
						  <th>Invoice</th>
						  <th>Patient</th>
						  <th>Phone</th>
						  <th>Category</th>
						  <th>Doctor</th>
						  <th>Lab Work</th>
						  <th>Lab Charge</th>
						  <th>Amount Charged</th>
						  <th colspan="1"></th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}
				
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$invoice_number = $this->session->userdata('branch_code').'-INV-00'.$visit_id;//$row->invoice_number;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_phone1 = $row->patient_phone1;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$close_card = $row->close_card;
				$hold_card = $row->hold_card;
				$lab_work_done = $row->lab_work_done;
				$amount_to_charge = $row->amount_to_charge;
				$visit_lab_work_id = $row->visit_lab_work_id;

				// this is to check for any credit note or debit notes
				$payments_value = $this->accounts_model->total_payments($visit_id);

				$invoice_total = $this->accounts_model->total_invoice($visit_id);

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);
				// end of the debit and credit notes


				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}
						
						else
						{
							$doctor = '-';
						}
					}
				}
				
				else
				{
					$doctor = '-';
				}
				
				$count++;
				
				//payment data
				$cash = $this->reports_model->get_all_visit_payments($visit_id);
				$charges = '';
				
				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced += $visit_charge;
					
					//$charges .= '<td>'.$visit_charge.'</td>';
				}
				if($hold_card == 1)
				{
					$button ='<td><a href="'.site_url().'reception/unhold_card/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to unhold this card?\');">Unhold Card</a></td>';
				}
				else
				{
					if($close_card == 1)
					{
						$button ='<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
								 <td><a href="'.site_url().'administration/reports/open_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to open card ?\');" class="btn btn-sm btn-info" >Open Card</a></td>';
					}
					else
					{
						$button ='<td><a href="'.site_url().'administration/reports/end_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to close visit ?\');" class="btn btn-sm btn-danger" >Close Card</a></td>';
					}
				}
				// payment value ///
				
				 $result.= form_open("administration/reports/receipt_lab_charge/".$visit_lab_work_id, array("class" => "form-horizontal"));
					$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$visit_date.'</td>
								<td>'.$invoice_number.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$patient_phone1.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$doctor.'</td>
								<td>'.$lab_work_done.'</td>
								<td>'.number_format($amount_to_charge,2).'</td>
								<td><input type="text" name="amount'.$visit_lab_work_id.'" class="form-control" value=""/></td>
								<td><button type="submit" class="btn btn-sm btn-warning" > Update Lab </button></td>
								
								
							</tr> 
					';
				 $result .= form_close();
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no visits";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        
		</section>
    </div>
  </div>