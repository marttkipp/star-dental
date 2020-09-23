<?php
$objective_result = '';
if($query->num_rows() > 0)
{
	$count = $page;
	$objective_result .='
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th><a href="'.site_url().'medical-reports/objective-findings/visit.visit_date/'.$order_method.'">Visit Date</a></th>
						<th>Visit ID</th>
						<th><a href="'.site_url().'medical-reports/objective-findings/visit.branch_code/'.$order_method.'">HC Seen</a></th>
						<th><a href="'.site_url().'medical-reports/objective-findings/staff.department_name/'.$order_method.'">Department</a></th>
						<th><a href="'.site_url().'medical-reports/objective-findings/objective_findings_class.objective_findings_class_name/'.$order_method.'">Class</a></th>
						<th><a href="'.site_url().'medical-reports/objective-findings/objective_findings.objective_findings_name/'.$order_method.'">Objective Findings</a></th>
					</tr>
				</thead>
				<tbody>
			';
	foreach($query->result() as $visit_symptoms)
	{
		$visit_id = $visit_symptoms->visit_id;
		$objective_findings_name = $visit_symptoms->objective_findings_name;
		$patient_id = $visit_symptoms->patient_id;
		$branch_code = $visit_symptoms->branch_code;
		$department_name = $visit_symptoms->department_name;
		$objective_findings_class_name = $visit_symptoms->objective_findings_class_name;
		$visit_date = date('jS M Y',strtotime($visit_symptoms->visit_date));
		$count++;
		
		//branch Code
		if($branch_code =='OSE')
		{
			$branch_code = 'Main HC';
		}
		else
		{
			$branch_code = 'Oserengoni';
		}
				
		$objective_result .='
					<tr>
						<td>'.$count.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$visit_id.'</td>
						<td>'.$branch_code.'</td>
						<td>'.$department_name.'</td>
						<td>'.$objective_findings_class_name.'</td>
						<td>'.$objective_findings_name.'</td>
					</tr>';
	}
	$objective_result .='
				</tbody>
			</table>';
}
else
{
	$objective_result.='No objevtive findings were added for any of the patients seen';
}
echo $this->load->view('administration/reports/graphs/objectives', '', TRUE);
?>
<div class="row">
    <div class="col-md-12">
        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>
            <!-- Widget content -->
            <div class="panel-body">
            	<div class="row">
                	<div class="col-md-4 col-md-offset-8">
                    	<a class="btn btn-sm btn-warning" id="open_search" onclick="open_search_box()" pull-right><i class="fa fa-search"></i> Open Search</a>
                    	<a class="btn btn-sm btn-info" id="close_search" style="display:none;" onclick="close_search()"><i class="fa fa-search-minus"></i> Close Search</a>
                        <?php
                        $search = $this->session->userdata('all_objectives_search');
						if(!empty($search))
						{
						?>
                    	<a class="btn btn-sm btn-danger" href="<?php echo site_url().'administration/reports/clear_objectives_search';?>"><i class="fa fa-search"></i> Clear Search</a>
                        <?php }?>
                    	<a class="btn btn-sm btn-success" target="_blank" href="<?php echo site_url().'administration/reports/export_objective_findings';?>">Download</a>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
                        <div id="search_section" style="display:none;">
                            <?php echo $this->load->view("administration/reports/search/objectives", '', TRUE);?>
                        </div>
                    </div>
                </div>
                <div class="row">
                	<div class="col-md-12">
            			<?php echo $objective_result;?>
                    </div>
                </div>
            	
            </div>
            <!--<a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});">EXCEL DOWNLOAD</a>-->
            <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">

	function open_search_box()
	{
		var myTarget2 = document.getElementById("search_section");
		var button = document.getElementById("open_search");
		var button2 = document.getElementById("close_search");

		myTarget2.style.display = '';
		button.style.display = 'none';
		button2.style.display = '';
	}
	
	function close_search()
	{
		var myTarget2 = document.getElementById("search_section");
		var button = document.getElementById("open_search");
		var button2 = document.getElementById("close_search");

		myTarget2.style.display = 'none';
		button.style.display = '';
		button2.style.display = 'none';
	}
</script>