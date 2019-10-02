<!-- search -->
<?php echo $this->load->view('search/providers', '', TRUE);?>
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
			$providers_search = $this->session->userdata('providers_search');
			$charges_search = $this->session->userdata('charges_search');

			$charges_title = $this->session->userdata('charges_title');

			if(!empty($providers_search) || !empty($charges_search))
			{
				echo '
				<a href="'.site_url().'close-providers-search" class="btn btn-warning btn-sm "> Close Search</a>
				';
				echo '<p>'.$charges_title.'</p>';
			}
		
			if($query->num_rows() > 0)
			{
			$count = 0;
			
				echo  
					'<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
						  <thead>
							<tr>
							  <th>#</th>
							  <th>Doctor\'s name</th>
							  <th>Cash Patients</th>
							  <th>Total Cash Revenue </th>
							  <th>Insurance Patients</th>
							  <th>Total Insurance Revenue</th>
							  <th>Total Revenue</th>
							</tr>
						</thead>
						<tbody>
					';
				$result = $query->result();
				$grand_total = 0;
				$patients_total = 0;
				$cash_patients = 0;
				$insurance_patients = 0;
				$total_cash_collection = 0;
				$total_insurance_collection = 0;
				// var_dump($query->result());
				foreach($query->result() as $key => $res)
				{
					$personnel_id = $res->personnel_id;
					$personnel_onames = $res->personnel_onames;
					$personnel_fname = $res->personnel_fname;
					$personnel_type_id = $res->personnel_type_id;
					$count++;

					$cpm_where = 'visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit_type.visit_type_name = "Cash paying" AND visit_charge.provider_id = '.$personnel_id;

					$charges_search = $this->session->userdata('charges_search');
					if(!empty($charges_search))
					{
						$cpm_where .= $charges_search;
					}


					$cpm_table = 'visit,visit_type,visit_charge';
					$cpm_select = 'COUNT(visit_charge.visit_id) AS number ';
					$cpm_group = NULL;
					$total_cpms =  $this->dashboard_model->count_items_group($cpm_table, $cpm_where,$cpm_select,$cpm_group);

					// cash payer invoices

					$cash_where = 'visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit_type.visit_type_name = "Cash paying" AND visit_charge.provider_id = '.$personnel_id;
					$charges_search = $this->session->userdata('charges_search');
					if(!empty($charges_search))
					{
						$cash_where .= $charges_search;
					}
					$cash_table = 'visit,visit_type,visit_charge';
					$cash_select = 'SUM(visit_charge.visit_charge_amount) AS number';
					$cash_group = NULL;
					$total_cash =  $this->dashboard_model->count_items_group($cash_table, $cash_where,$cash_select,$cash_group);


					// cash payer invoices


					$cpm_where = 'visit.visit_id = visit_charge.visit_id AND visit.visit_type = visit_type.visit_type_id AND visit_type.visit_type_name <> "Cash paying" AND visit_charge.provider_id = '.$personnel_id;
					$charges_search = $this->session->userdata('charges_search');
					if(!empty($charges_search))
					{
						$cpm_where .= $charges_search;
					}
					$cpm_table = 'visit,visit_type,visit_charge';
					$cpm_select = 'COUNT(visit_charge.visit_id) AS number ';
					$cpm_group = NULL;
					$total_insurance_visits =  $this->dashboard_model->count_items_group($cpm_table, $cpm_where,$cpm_select,$cpm_group);


					// insurance payer invoices

					$insurance_where = 'visit.visit_id = visit_charge.visit_id AND visit_charge.visit_charge_delete = 0 AND visit.visit_type = visit_type.visit_type_id AND visit_type.visit_type_name <> "Cash paying" AND visit_charge.provider_id = '.$personnel_id;
					$charges_search = $this->session->userdata('charges_search');
					if(!empty($charges_search))
					{
						$insurance_where .= $charges_search;
					}
					$insurance_table = 'visit,visit_type,visit_charge';
					$insurance_select = 'SUM(visit_charge.visit_charge_amount) AS number';
					$insurance_group = NULL;
					$total_insurance =  $this->dashboard_model->count_items_group($insurance_table, $insurance_where,$insurance_select,$insurance_group);


					// cash payer invoices


					$cash_patients = $cash_patients + $total_cpms ;
					$insurance_patients = $insurance_patients + $total_insurance_visits;

					$total_bill = $total_insurance + $total_cash;
					$total_cash_collection = $total_cash_collection + $total_cash; 
					$total_insurance_collection = $total_insurance_collection + $total_insurance; 

					$grand_total = $grand_total + $total_bill;

					echo '
						<tr>
							<td>'.$count.'</td>
							<td>'.$personnel_fname.' '.$personnel_onames.'</td>
							<td>'.$total_cpms.'</td>
							<td>Kes. '.number_format($total_cash, 2).'</td>
							<td>'.$total_insurance_visits.'</td>
							<td>Kes. '.number_format($total_insurance, 2).'</td>
							<td>Kes. '.number_format($total_bill, 2).'</td>
							<td>
								<a href="'.site_url().'provider-cash-report/'.$personnel_id.'" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Cash </a>
							</td>
							<td>
								<a href="'.site_url().'provider-insurance-report/'.$personnel_id.'" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Insuarance </a>
							</td>
						</tr>
					';
				}
				
				echo 
				'
					
						<tr>
							<td colspan="2">Total</td>
							<td><span class="bold" >'.$cash_patients.' patients</span></td>
							<td><span class="bold">Kes. '.number_format($total_cash_collection, 2).'</span></td>
							<td><span class="bold" >'.$insurance_patients.' patients</span></td>
							<td> <span class="bold">Kes. '.number_format($total_cash_collection,2).'</span></td>
							<td> <span class="bold">Kes. '.number_format($grand_total,2).'</span></td>
							<td></td>
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