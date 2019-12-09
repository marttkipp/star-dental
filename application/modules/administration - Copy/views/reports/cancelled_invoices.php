<!-- search -->
<!-- end search --> 
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
		// $result = '<a href="'.site_url().'administration/reports/export_cash_report" class="btn btn-sm btn-success pull-right">Export</a>';
   $result = '';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_cash_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
		
		//if users exist display them
		if($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Rejected Date</th>
						  <th>Patient</th>
						  <th>Visit type</th>
						  <th>Amount</th>
						  <th>Reason</th>						
						  </tr>
					  </thead>
					  <tbody>
			';
			foreach ($query->result() as $row)
			{
				$count++;
				$total_invoiced = 0;
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
				$rejected_reason = $row->rejected_reason;
				$rejected_amount = $row->rejected_amount;
				$rejected_date = $row->rejected_date;
				//$created_by = $row->personnel_fname.' '.$row->personnel_onames;

				
				$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$rejected_date.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$rejected_amount.'</td>
								<td>'.$rejected_reason.'</td>
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
			$result .= "There are rejected invoices";
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