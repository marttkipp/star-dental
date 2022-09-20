<?php


$this->db->where('place_id',$place_id);
$query = $this->db->get('place');

if($query->num_rows() > 0)
{
	foreach ($query->result() as $key => $value) {
		// code...
		$place_name = $value->place_name;
	}
}
$branch_id = $this->session->userdata('branch_id');

if($month < 10)
{
	$month = '0'.$month;
}
$community_where ='patients.patient_delete = 0 AND  YEAR(patients.patient_date) = '.$year.' AND patients.about_us = '.$place_id.'  AND MONTH(patients.patient_date) = "'.$month.'" AND place.place_id = patients.about_us AND patients.branch_id ='.$branch_id;
$community_table = 'patients,place';
$patient_rs = $this->dashboard_model->get_content($community_table, $community_where,'*');
// var_dump($patient_rs);die();
$result = '<table class="table table-bordered table-condensed">
				<thead>
					<th>#</th>
					<th>Registration Date</th>
					<th>Patient Number</th>
					<th>Patient</th>
					<th>Phone</th>
					<th>Visit Count</th>
					<th>Revenue</th>
				</thead>
				<tbody>'; 
$multiple_visits = 0;	
$single_visits = 0;			
if($patient_rs->num_rows() > 0)
{
	$count = 0;
	$total_amount = 0;

	foreach ($patient_rs->result() as $key => $value) {
		// code...
		$patient_id = $value->patient_id;
		$patient_surname = $value->patient_surname;
		$patient_othernames = $value->patient_othernames;
		$patient_number = $value->patient_number;
		$patient_date = $value->patient_date;
		$patient_phone1 = $value->patient_phone1;
		$place_name = $value->place_name;
		$count++;


		$where_two ='patients.patient_delete = 0 AND  YEAR(patients.patient_date) = '.$year.' AND patients.about_us = '.$place_id.'  AND MONTH(patients.patient_date) = "'.$month.'" AND YEAR(visit.visit_date) = "'.$year.'" AND MONTH(visit.visit_date) = "'.$month.'" AND visit.patient_id = patients.patient_id  AND patients.patient_id = '.$patient_id.'';
		$table_two = 'patients,visit';
		$select_two = 'COUNT(visit.visit_id) AS number';

		$visits = $this->dashboard_model->count_items_group($table_two, $where_two,$select_two);

		if($visits > 1)
		{
			$multiple_visits++;
		}
		else if($visits == 1)
		{

			$single_visits++;
		}
		else
		{

		}



		$where ='patients.patient_delete = 0 AND  YEAR(patients.patient_date) = '.$year.' AND patients.about_us = '.$place_id.'  AND MONTH(patients.patient_date) = "'.$month.'" AND YEAR(visit.visit_date) = "'.$year.'" AND MONTH(visit.visit_date) = "'.$month.'"  AND visit_charge.visit_id = visit.visit_id AND visit.visit_delete = 0 AND visit_charge.visit_charge_delete = 0 AND visit.patient_id = patients.patient_id AND patients.patient_id = '.$patient_id.' AND visit.branch_id ='.$branch_id;
			
		$table = 'patients,visit_charge,visit';
		$select = 'SUM(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS number';
		$cpm_group = 'patients.patient_id';
		$amount = $this->dashboard_model->count_items_group($table, $where, $select,$cpm_group);
		$total_amount += $amount;



		$result .= '
					<tr>
						<td>'.$count.'</td>
						<td>'.date('jS M Y',strtotime($patient_date)).'</td>
						<td>'.$patient_number.'</td>
						<td>'.$patient_surname.' '.$patient_othernames.'</td>
						<td>'.$patient_phone1.'</td>
						<td>'.$visits.'</td>
						<td>'.number_format($amount,2).'</td>
					</tr>
					';
	}
	$result .= '
					<tr>
						<th colspan="6">Total</th>
						
						<th>'.number_format($total_amount,2).'</th>
					</tr>
					';
}

$result .='</tbody>
			</table>';

$period = $year.'-'.$month.'-01';

?>

<section class="panel" >
	<div class="panel-body" style="height:80vh;overflow-y: scroll;">
		

		<div class="col-md-12" id="sidebar-container" style="margin-bottom: 20px;" >
			<h4> <?php echo $place_name;?> Registration for <?php echo date('M Y',strtotime($period))?></h4>
			<br>
			<?php echo $result;?>
			<br>
			<div class="row">
				<div class="col-md-6">
					<table class="table table-bordered table-striped">
						<tr>
							<th>Single Visits</th>
							<td><?php echo $single_visits;?></td>
						</tr>
						<tr>
							<th>Repeat Visits</th>
							<td><?php echo $multiple_visits;?></td>
						</tr>
					</table>
				</div>
				
			</div>
			
		</div>
	</div>
</section>
<div class="row" style="margin-top: 5px;">
	<ul>
		<li style="margin-bottom: 5px;">
			<div class="row">
		        <div class="col-md-12 center-align">
			        	
			        		<a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
			      
			        		
		               
		        </div>
		    </div>
			
		</li>
	</ul>
</div>