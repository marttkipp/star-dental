<!-- search -->
<!-- Widget -->
<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                  <h2 class="panel-title">Visit Time Report search</h2>
            </header>             
            <div class="panel-body">

    			<?php
                echo form_open("administration/reports/search_time", array("class" => "form-horizontal"));
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visit Type: </label>
                            
                            <div class="col-lg-8">
                                <select class="form-control" name="visit_type_id">
                                	<option value="">---Select Visit Type---</option>
                                    <?php
                                        if(count($type) > 0){
                                            foreach($type as $row):
                                                $type_name = $row->visit_type_name;
                                                $type_id= $row->visit_type_id;
                                                    ?><option value="<?php echo $type_id; ?>" ><?php echo $type_name ?></option>
                                            <?php	
                                            endforeach;
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Doctor: </label>
                            
                            <div class="col-lg-8">
                                <select class="form-control" name="personnel_id">
                                	<option value="">---Select Doctor---</option>
                                    <?php
    									if(count($doctors) > 0){
    										foreach($doctors as $row):
    											$fname = $row->personnel_fname;
    											$onames = $row->personnel_onames;
    											$personnel_id = $row->personnel_id;
    											echo "<option value=".$personnel_id.">".$onames." ".$fname."</option>";
    										endforeach;
    									}
    								?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visit Date From: </label>
                            
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Visit Date From">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visit Date To: </label>
                            
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Visit Date To">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <br>
                <div class="center-align">
                	<button type="submit" class="btn btn-info btn-sm">Search</button>
                </div>
                <?php
                echo form_close();
                ?>
	       </div>
        </section>
    </div>
</div>
<!-- end search -->
<?php //echo $this->load->view('transaction_statistics', '', TRUE);?>
 
<div class="row">
    <div class="col-md-12">

     <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                 <h2 class="panel-title">Visit Time Report</h2>
            </header>             
            <div class="panel-body">
          
<?php
		//$result = '<a href="'.site_url().'/administration/reports/export_transactions" class="btn btn-success pull-right">Export</a>';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_time_reports_search" class="btn btn-warning">Close Search</a>';
		}
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			$total_time = 0;
			$result = '<a href="'.site_url().'/administration/reports/export_time_report" class="btn btn-success pull-right">Export</a>';
			$result .= 
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Visit Date</th>
						  <th>Patient</th>
						  <th>Category</th>
                          <th>Attended</th>
						  <th>Start Time</th>
						  <th>End Time</th>
						  <th>Total Time (Days h:m:s)</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
					$seconds = strtotime($row->visit_time_out) - strtotime($row->visit_time);//$row->waiting_time;
					$days    = floor($seconds / 86400);
					$hours   = floor(($seconds - ($days * 86400)) / 3600);
					$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
					$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
					
					//$total_time = date('H:i',(strtotime($row->visit_time_out) - strtotime($row->visit_time)));//date('H:i',$row->waiting_time);
					$total_time = $days.' '.$hours.':'.$minutes.':'.$seconds;
				}
				else
				{
					$visit_time_out = '-';
					$total_time = '-';
				}
					
				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
                $visit_type_name = $row->visit_type_name;
                $patient_onames = $row->patient_othernames;
                $patient_surname = $row->patient_surname;
                 $personnel_fname = $row->personnel_fname;
                 $personnel_onames = $row->personnel_onames;
				
				// $patient = $this->reception_model->patient_names2($patient_id, $visit_id);
				// $visit_type = $patient['visit_type'];
				// $patient_type = $patient['patient_type'];
				// $patient_othernames = $patient['patient_othernames'];
				// $patient_surname = $patient['patient_surname'];
				// $patient_date_of_birth = $patient['patient_date_of_birth'];
				// $gender = $patient['gender'];
				// $faculty = $patient['faculty'];
				
				$count++;
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$visit_date.'</td>
							<td>'.$patient_surname.' '.$patient_onames.'</td>
							<td>'.$visit_type_name.'</td>
                            <td>Dr. '.$personnel_onames.' '.$personnel_fname.'</td>
							<td>'.$visit_time.'</td>
							<td>'.$visit_time_out.'</td>
							<td>'.$total_time.'</td>
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
			$result = "There are no visits";
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

    </section>
</div>
</div>