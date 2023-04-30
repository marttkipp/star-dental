<!-- search -->
<?php echo $this->load->view('search/cash', '', TRUE);?>
<!-- end search -->
<?php echo $this->load->view('cash_statistics', '', TRUE);?>
 
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
						  <th>Category</th>
						  <th>Visit Date</th>
						  <th>Payment Date</th>
						  <th>Time recorded</th>
						  <th>Patient</th>
						  <th>Category</th>
						  <th>Service</th>
						  <th>Amount</th>
						  <th>Method</th>
						  <th>Transaction Code</th>
						  <th>Recorded by</th>
						</tr>
					  </thead>
					  <tbody>
			';
			$total_amount = 0;
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
				$service_name = $row->service_name;
				$created_by = $row->personnel_fname.' '.$row->personnel_onames;

				if($visit_date == $payment_created)
				{
					$class = 'class="default"';
					$view = 'Normal Payment';
				}
				else 
				{
					$class = 'class="info"';
					$view = 'Debt Payment';
				}
				$total_amount += $amount_paid;
				
				$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td '.$class.'>'.$view.'</td>
								<td '.$class.'>'.$visit_date.'</td>
								<td '.$class.'>'.$payment_created.'</td>
								<td>'.$time.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$service_name.'</td>
								<td>'.number_format($amount_paid, 2).'</td>
								<td>'.$payment_method.'</td>
								<td>'.$transaction_code.'</td>
								<td>'.$created_by.'</td>
							</tr> 
					';
			}

			$result .= 
						'
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th> Total</th>
								<th>'.number_format($total_amount,2).'</th>
								<th></th>
								<th></th>
								<th></th>
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