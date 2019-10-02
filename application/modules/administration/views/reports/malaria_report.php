<?php
echo $this->load->view('reports/search/search_malaria');
$result = '';
if($query->num_rows() > 0)
{
	$count = $page;
	$result .= 
		'
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th>Payroll No</th>
						<th>Visit Date</th>
						<th>Patient Names</th>
						<th>Count</th>
						<th>Result (+ve/-ve) </th>
					</tr>
				</thead>
				<tbody>
		';
	foreach($query->result() as $malaria_results)
	{
		$patient_surname = $malaria_results->patient_surname;
		$patient_othernames = $malaria_results->patient_othernames;
		$patient_name = $patient_surname.' '.$patient_othernames;
		$payroll_no = $malaria_results->strath_no;
		$visit_lab_test_results = $malaria_results->visit_lab_test_results;
		
		//if((!empty($visit_lab_test_results)) &&($visit_lab_test_results <= 0))
		if($visit_lab_test_results <= 0)
		{
			$malaria_status = "-ve";
		}
		elseif((!empty($visit_lab_test_results)) &&($visit_lab_test_results > 0))
		{
			$malaria_status = "+ve";
		}
		else
		{
			$malaria_status = " ";
		}
		$visit_date = date('jS M Y',strtotime($malaria_results->visit_date));
		$count ++;
		
		$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$payroll_no.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$patient_name.'</td>
						<td>'.$visit_lab_test_results.'</td>
						<td>'.$malaria_status.'</td>
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
	$result .= 'No tests have been done';
}
?>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    
    <div class="panel-body">
    	<div class="row" >
            <div class="col-lg-2 pull-right">
                <a href="<?php echo site_url();?>reports/download_all_malaria" target="_blank"" class="btn btn-sm btn-default pull-right">Download All</a>
            </div>
        </div>
    	<?php 
    	$search = $this->session->userdata('malaria');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_malaria_search" class="btn btn-sm btn-warning">Close Search</a>';
		}
    	echo $result;
		?>
    </div>
    <a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});">EXCEL DOWNLOADS</a>
    <div class="widget-foot">
                                
		<?php if(isset($links)){echo $links;}?>
    
        <div class="clearfix"></div> 
    
    </div>
</section>