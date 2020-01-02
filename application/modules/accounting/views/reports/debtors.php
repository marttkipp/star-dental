<!-- search -->
<?php echo $this->load->view('search_debtors', '', TRUE);?>
<!-- end search -->
<?php echo $this->load->view('transaction_statistics', '', TRUE);?>
 

<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>             

          <!-- Widget content -->
          <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
		$result = '<a href="'.site_url().'administration/reports/export_cash_report" class="btn btn-sm btn-success pull-right">Export</a>';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_cash_search" class="btn btn-sm btn-warning">Close Search</a>';
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
						  <th>Payment Date</th>
						  <th>Patient Number</th>
						  <th>Patient</th>
						  <th>Category</th>
						  <th>Amount</th>
						  <th>Method</th>
						  <th>Receipt No.</th>
						  <th>Type</th>
						  <th>Recorded by</th>
						</tr>
					  </thead>
					  <tbody>
			';
			foreach ($query->result() as $row)
			{
				$count++;
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$payment_created = date('jS M Y',strtotime($row->payment_created));
				$time = date('H:i a',strtotime($row->time));
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$patient_table_visit_type = $visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$payment_method = $row->payment_method;
				$amount_paid = $row->amount_paid;
				$transaction_code = $row->transaction_code;
				$confirm_number = $row->confirm_number;
				$patient_number = $row->patient_number;
				$created_by = $row->personnel_fname.' '.$row->personnel_onames;


				if($visit_date == $payment_created)
				{
					$type = 'Normal Payment';
				}
				else
				{
					$type = 'Debt Repayment';
				}
				
				$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$visit_date.'</td>
								<td>'.$payment_created.'</td>
								<td>'.$patient_number.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.number_format($amount_paid, 2).'</td>
								<td>'.$payment_method.'</td>
								<td>'.$visit_id.'</td>
								<td>'.$type.'</td>
								<td>'.$created_by.'</td>
							</tr> 
					';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no payments";
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