<?php
echo $this->load->view('reports/search/search_cholinestrase');
$result = $format_header = '';
if($lab_test_formats->num_rows() > 0)
{
	foreach($lab_test_formats->result() as $row)
	{
		$lab_test_format_name = $row->lab_test_formatname;
		
		if($lab_test_format_name != '% Change')
		{
			$format_header .= '<th>'.$lab_test_format_name.'</th>';
		}
	}
}
if($query->num_rows() > 0)
{
	$count = $page;
	$result .= 
		'
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th>Visit Code</th>
						<th>Payroll No</th>
						<th>Visit Date</th>
						<th>Patient Names</th>
						<th>ID No</th>
						<th>Department</th>
						<th>Company</th>
						'.$format_header.'
						<th>% Change</th>
					</tr>
				</thead>
				<tbody>
		';
	foreach($query->result() as $cholinestrase_results)
	{
		$patient_surname = $cholinestrase_results->patient_surname;
		$patient_othernames = $cholinestrase_results->patient_othernames;
		$patient_name = $patient_surname.' '.$patient_othernames;
		$patient_national_id = $cholinestrase_results->patient_national_id;
		$payroll_no = $cholinestrase_results->strath_no;
		$visit_id = $cholinestrase_results->visit_id;
		$department_name  =$cholinestrase_results->department_name;
		$visit_type_name = $cholinestrase_results->visit_type_name;
		$visit_date = date('jS M Y',strtotime($cholinestrase_results->visit_date));
		$test_results = $this->reports_model->get_cholinestrase_results($visit_id);
		$count ++;
		$format_result = '';
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$visit_id.'</td>
				<td>'.$payroll_no.'</td>
				<td>'.$visit_date.'</td>
				<td>'.$patient_name.'</td>
				<td>'.$patient_national_id.'</td>
				<td>'.$department_name.'</td>
				<td>'.$visit_type_name.'</td>
		';
		$per_change = $base_line_calc = $current_calc = 0;
		
		if($lab_test_formats->num_rows() > 0)
		{
			foreach($lab_test_formats->result() as $row)
			{
				$lab_test_format_id = $row->lab_test_format_id;
				$lab_test_format_name = $row->lab_test_formatname;
				
				if($lab_test_format_name != '% Change')
				{
					$format_check = 0;
					
					if($test_results->num_rows() > 0)
					{
						foreach($test_results->result() as $res)
						{
							//var_dump($test_results->result()); die();
							$lab_visit_result_format = $res->lab_visit_result_format;
							$lab_visit_results_result = $res->lab_visit_results_result;
							$visit_id_result = $res->visit_id;
							
							if(($visit_id == $visit_id_result) && ($lab_test_format_id == $lab_visit_result_format))
							{
								if($lab_test_format_name == 'Base Line')
								{
									$base_line_calc = $lab_visit_results_result;
								}
								if($lab_test_format_name == 'Current')
								{
									$current_calc = $lab_visit_results_result;
								}
								$format_result .= '<td>'.$lab_visit_results_result.'</td>';
								$format_check = 1;
							}
						}
					}
					if($format_check == 0)
					{
						$format_result .= '<td></td>';
					}
				}
			}
		}
			
		//calculate % change
		$baseleine = $base_line_calc;
		$current = $current_calc;
		
		if($baseleine > 0)
		{
			$per_change = (($current - $baseleine)/$baseleine)* 100;
		}
		if($per_change <= -50)
		{
			$class = 'danger';
		}
		else
		{
			$class = '';
		}
		$format_result .= '<td class="'.$class.'">'.number_format($per_change, 2).'</td>';
		
		$result .= $format_result.'</tr>';
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
                <a href="<?php echo site_url();?>reports/download_all_cholinestrase" target="_blank"" class="btn btn-sm btn-default pull-right">Download All</a>
            </div>
        </div>
        
        <?php
    	$search = $this->session->userdata('cholinestrase');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_cholinestrase_search" class="btn btn-sm btn-warning">Close Search</a>';
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