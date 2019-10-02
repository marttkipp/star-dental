<?php echo $this->load->view('search/sick_off_search', '', TRUE);?>
<?php
$search_title = $this->session->userdata('sick_off_title_search');
if(!empty($search_title))
{
	$title_ext = $search_title;
}
else
{
	$title_ext = 'Sick Off Report for '.date('Y-m-d');
}

?>
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            	 <a href="<?php echo site_url();?>print-sick-off" target="_blank" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;"> <i class="fa fa-print"></i> Print Sick Off List</a>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
          <h5 class="center-align"><?php echo $title_ext;?></h5>
          <br>
<?php
		$result = '';
		$search = $this->session->userdata('sick_off_report_search');
		if(!empty($search))
		{
			echo '<a href="'.site_url().'reports/close_sick_off_search" class="btn btn-sm btn-warning">Close Search</a>';
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
								<th>Patient No. </th>
								<th>Name </th>
								<th>Type </th>
								<th>Employee No.</th>
								<th>From Date </th>
								<th>To Date </th>
								<th>Days</th>
								<th>Dept Name</th>
								<th>Type of Visit</th>
								<th>Created by</th>
							</tr>
						</thead>
						<tbody>
				';
				
		
			
			$personnel_query = $this->personnel_model->get_all_personnel();
			
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$from_date = date('jS M Y',strtotime($row->start_date));
				$to_date = date('jS M Y',strtotime($row->end_date));
				$department_name = $row->department_name;
				$no_of_days = $row->no_of_days;
				$patient_id = $row->patient_id;
				$patient_number = $row->patient_number;
				$strath_no = $row->strath_no;
				$personnel_id = $row->personnel_id;
				$gender_id = $row->gender_id;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$last_visit = $row->last_visit;
				$leave_type_name = $row->leave_type_name;

				if($last_visit != NULL)
				{
					$last_visit = 'Re Visit';
				}
				
				else
				{
					$last_visit = 'First Visit';
				}

				if($gender_id == 1)
				{
					$gender = 'Male';
				}
				else
				{
					$gender = 'Female';
				}

				

				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();
					
					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;
						
						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}
						
						else
						{
							$doctor = '-';
						}
					}
				}
				
				else
				{
					$doctor = '-';
				}
				
				$count++;
				
				
					$result .= 
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$patient_number.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$leave_type_name.'</td>
								<td>'.$strath_no.'</td>
								<td>'.$from_date.'</td>
								<td>'.$to_date.'</td>
								<td>'.$no_of_days.'</td>
								<td>'.$department_name.'</td>
								<td>'.$last_visit.'</td>
								<td>'.$doctor.'</td>
								

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
			$result .= "There are no sick off's booked for ".$title_ext;
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