<!-- search -->
<?php echo $this->load->view('search/doctors', '', TRUE);?>
<div class="row">
    <div class="col-md-12">
		<!-- Widget -->
		<section class="panel">


			<!-- Widget head -->
			<header class="panel-heading">
				<h2 class="panel-title"><?php echo $title;?></h2>
			</header>             

			<!-- Widget content -->
			<div class="panel-body">


          	<?php

          	$search = $this->session->userdata('doctors_search');
		
			if(!empty($search))
			{
				echo '
				<a href="'.site_url().'reception/close_patient_search" class="btn btn-warning btn-sm ">Close Search</a>
				';
			}
		
			if($doctor_results->num_rows() > 0)
			{
			$count = $full = $percentage = $daily = $hourly = 0;
			
				echo  
					'
						<a href="'.site_url().'administration/reports/doctor_reports_export/'.$date_from.'/'.$date_to.'" class="btn btn-sm btn-success pull-right">Export</a> <br/>
						<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
						  <thead>
							<tr>
							  <th>#</th>
							  <th>Doctor\'s name</th>
							  <th>New Patients</th>
							  <th>Revisits</th>
							  <th>Total Patients</th>
							  <th>Total Cash Invoices</th>
							  <th>Total Insurance Invoices</th>
							  <th>Total Invoices</th>
							  <th colspan="2">Actions</th>
							</tr>
						</thead>
						<tbody>
					';
				$result = $doctor_results->result();
				$grand_total = 0;
				$patients_total = 0;
				$insurance_grand = 0;
				$total_revisits = 0;
				$total_new = 0;

				
				foreach($result as $res)
				{
					$personnel_id = $res->personnel_id;
					$personnel_onames = $res->personnel_onames;
					$personnel_fname = $res->personnel_fname;
					$personnel_type_id = $res->personnel_type_id;
					$count++;
					
					//get service total
					$total = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to,1);
					$total_insurance = $this->reports_model->get_total_collected($personnel_id, $date_from, $date_to,2);
					
					$new = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to,1);
					$revisit = $this->reports_model->get_total_patients($personnel_id, $date_from, $date_to,2);
					$patients = $new+$revisit;
					$grand_total += $total;
					$patients_total += $patients;
					$insurance_grand = $total_insurance;
					$total_new += $new;
					$total_revisits += $revisit;
					
					

					if(empty($date_to))
					{
						$date_to = $date_from;
					}
					
					echo '
						<tr>
							<td>'.$count.'</td>
							<td>Dr. '.$personnel_fname.' '.$personnel_onames.'</td>
							<td>'.$new.'</td>
							<td>'.$revisit.'</td>
							<td>'.$patients.'</td>
							<td>'.number_format($total, 2).'</td>
							<td>'.number_format($total_insurance, 2).'</td>
							<td>'.number_format($total+$total_insurance, 2).'</td>

							<td>
								<a href="'.site_url().'view-doctors-patients/'.$personnel_id.'/'.$date_from.'/'.$date_to.'" class="btn btn-warning btn-sm fa fa-folder-open"> View Patients</a>
							</td>
							<td>
								<a href="'.site_url().'administration/reports/doctor_patients_export/'.$personnel_id.'/'.$date_from.'/'.$date_to.'" class="btn btn-success btn-sm fa fa-excel">Export Patients</a>
							</td>
						</tr>
					';
				}
				
				echo 
				'
					
						<tr>
							<td colspan="2">Total</td>
							<td><span class="bold" >'.$total_new.' patients</span></td>
							<td><span class="bold" >'.$total_revisits.' patients</span></td>
							<td><span class="bold" >'.$patients_total.' patients</span></td>
							<td><span class="bold">'.number_format($grand_total, 2).'</span></td>
							<td><span class="bold">'.number_format($insurance_grand, 2).'</span></td>
							<td><span class="bold">'.number_format($grand_total+$insurance_grand, 2).'</span></td>
						</tr>
					</tbody>
				</table>
				';
			}
			?>
       		</div>
			<div class="widget-foot">
								
				<?php if(isset($links)){echo $links;}?>
			
				<div class="clearfix"></div> 
			
			</div>
		</section>
	</div>
</div>