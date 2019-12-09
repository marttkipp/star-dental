<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            	  <div class="pull-right">
            	  	
            	   
			    </div>
            </header>             

          <!-- Widget content -->
        <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
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
						  <th>Category</th>
						  <th>Doctor</th>
						  <th>Time Out</th>
						  <th>Invoice Total</th>
						  <th>Payments</th>
						  <th>Balance</th>
						</tr>
					  </thead>
					  <tbody>
			';
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			$total_invoice = 0;
			$total_payments = 0;
			$total_balance = 0;
			
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

				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
					$seconds = strtotime($row->visit_time_out) - strtotime($row->visit_time);//$row->waiting_time;
					$days    = floor($seconds / 86400);
					$hours   = floor(($seconds - ($days * 86400)) / 3600);
					$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
					$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
					
					$total_time = $days.' '.$hours.':'.$minutes.':'.$seconds;
				}
				else
				{
					$visit_time_out = '-';
					$total_time = '-';
				}
				
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$invoice_number = $row->invoice_number;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;

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
				$total_payments += $payments_value;
				$total_invoice += $invoice_total;
				$total_balance += $balance;

				// payment value ///
				
					$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$visit_date.'</td>
								<td>'.$visit_id.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$doctor.'</td>
								<td>'.$total_time.'</td>
								<td>'.$invoice_total.'</td>
								<td>'.$payments_value.'</td>
								<td>'.($balance).'</td>
							</tr> 
						';
			}
			$result .= 
						'
							<tr>
								<td colspan=7>Totals</td>
								<td>'.$total_invoice.'</td>
								<td>'.$total_payments.'</td>
								<td>'.$total_balance.'</td>
							</tr> 
						';

		
			
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