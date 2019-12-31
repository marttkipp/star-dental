<!-- search -->
<?php

 // echo $this->load->view('patients/search_patient', '', TRUE);
 ?>
 <?php echo $this->load->view('search/procedures_search', '', TRUE);?>
<!-- end search -->

<section class="panel ">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="pull-right">
	          <!-- <a href="<?php echo site_url();?>queues/outpatient-queue" class="btn btn-primary btn-sm pull-right " style="margin-top:-25px"><i class="fa fa-arrow-up"></i> Outpatient Queue</a>
	           <a href="<?php echo site_url();?>queues/inpatient-queue" class="btn btn-success btn-sm pull-right " style="margin-top:-25px;margin-right:5px;"><i class="fa fa-arrow-up"></i> Inpatient Queue</a> -->
	            <a href="<?php echo site_url();?>reports/export-procedures" target="_blank" class="btn btn-sm btn-success pull-right" style="margin-top:-25px;margin-right: 5px;"> <i class="fa fa-print"></i> Export List</a>
	    </div>
    </header>

        <!-- Widget content -->
        <div class="panel-body">
          <div class="padd">
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
				
		$search = $this->session->userdata('procedure_report_search');
		
		if(!empty($search))
		{
			echo '
			<a href="'.site_url().'reports/close_procedures_search" class="btn btn-warning btn-sm ">Close Search</a>
			';
		}
	
		
		$result = '';
		
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			
				$result .= 
				'
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Procedure Name</th>
						  <th>Procedure Count</th>
						  <th>Rate per procedure</th>
						  <th>Revenue</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{

				$service_charge_name = $row->service_charge_name;
				$total_count = $row->total_count;
				$total_revenue = $row->total_revenue;
				$service_charge_amount = $row->service_charge_amount;
				$service_charge_id = $row->service_charge_id;
				
				
				$count++;
				
			
				
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$service_charge_name.' </td>
							<td>'.$total_count.' </td>
							<td>'.number_format($service_charge_amount,2).' </td>
							<td>'.number_format($total_revenue,2).' </td>
							<td><a href="'.site_url().'reports/export-procedures/'.$service_charge_id.'" class="btn btn-xs btn-info" target="_blank"> export report </a></td>

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
			$result .= "There are no procedures";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->

      </div>
    </section>