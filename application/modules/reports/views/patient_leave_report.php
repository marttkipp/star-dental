<?php echo $this->load->view('search/leave_search', '', TRUE);?>
<?php
$result = '';
	if($query->num_rows() > 0)
	{
		$count = $page;
			
		$result .= 
			'
				<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					<thead>
						<tr>
							<th>#</th>
							<th>Payroll No. </th>
							<th>Full Names</th>
							<th>Leave Type</th>
							<th>No. of Days</th>
							<th>Start Date</th>
							<th>End Date</th>
						</tr>
					</thead>
					<tbody>
			';
		foreach($query->result() as $patient_leave)
		{
			$patient_leave_id = $patient_leave->patient_leave_id;
			$patient_surname = $patient_leave->patient_surname;
			$patient_othernames = $patient_leave->patient_othernames;
			$patient_name = $patient_surname.' '.$patient_othernames;
			$payroll_no = $patient_leave->strath_no;
			$start_date = $patient_leave->start_date;
			$end_date = $patient_leave->end_date;
			$leave_type_name = $patient_leave->leave_type_name;
			$leave_days = strtotime($end_date) - strtotime($start_date);
			$patient_leave_days = floor($leave_days / (60 * 60 * 24));
			$count++;
			
			$result .=
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$payroll_no.'</td>
							<td>'.$patient_name.'</td>
							<td>'.$leave_type_name.'</td>
							<td>'.$patient_leave_days.'</td>
							<td>'.date('jS M Y',strtotime($start_date)).'</td>
							<td>'.date('jS M Y',strtotime($end_date)).'</td>
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
		$result .= 'No leaves have been given today';
	}
?>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    
    <div class="panel-body">
    	<?php 
    	$search = $this->session->userdata('leave_report_search');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'reports/close_leave_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
    	echo $result;
		?>
    </div>
    
    <div class="widget-foot">
                                
		<?php if(isset($links)){echo $links;}?>
    
        <div class="clearfix"></div> 
    
    </div>
</section>