<?php
$row = $personnel->row();

$personnel_onames = $row->personnel_onames;
$personnel_id = $row->personnel_id;
$personnel_fname = $row->personnel_fname;
$branch_working_hours = $row->branch_working_hours;
$branch_name = $row->branch_name;

$personnel_overtime_hours = '';
$overtime_type = '';
$overtime_type_rate = '';
$normal_rate = '';
$normal_amount = '';
$holiday_rate = '';
$holiday_amount = '';
$normal = '';
$holiday = '';
$normal_overtime_type = '';
$normal_overtime_type_rate = '';
$holiday_overtime_type = '';
$holiday_overtime_type_rate = '';

if($overtime_hours->num_rows() > 0)
{
	foreach($overtime_hours->result() as $row)
	{
		$personnel_overtime_hours = $row->personnel_overtime_hours;
		$overtime_type = $row->overtime_type;
		$overtime_type_rate = $row->overtime_type_rate;

		if($overtime_type == 1)
		{
			$normal = $personnel_overtime_hours;
			$normal_overtime_type = $overtime_type;
			$normal_overtime_type_rate = $overtime_type_rate;
			
			if($overtime_type_rate == 1)
			{
				$normal_rate = 'checked';
			}
			else if($overtime_type_rate == 2)
			{
				$normal_amount = 'checked';
			}
		}
		
		else if($overtime_type == 2)
		{
			$holiday = $personnel_overtime_hours;
			$holiday_overtime_type = $overtime_type;
			$holiday_overtime_type_rate = $overtime_type_rate;
			
			if($overtime_type_rate == 1)
			{
				$holiday_rate = 'checked';
			}
			else if($overtime_type_rate == 2)
			{
				$holiday_amount = 'checked';
			}
		}
	}
}

?>	
    <div class="row">
        <section class="panel">
            <header class="panel-heading">						
                <h2 class="panel-title"><?php echo $title;?>
                <a href="<?php echo site_url().'payroll/salary-data'?>" class="btn btn-info pull-right"><i class="fa fa-arrow-left"></i> Back to personnel</a></h2>
            </header>
            <div class="panel-body">
            	<h4 class="center-align">Overtime for <?php echo $personnel_fname.' '.$personnel_onames;?> : <?php echo $branch_name;?></h4>
                
				<?php
                $error = $this->session->userdata('error_message');
                
                if(!empty($error))
                {
                    echo '<div class="alert alert-danger">'.$error.'</div>';
                    $this->session->unset_userdata('error_message');
                }
                $success = $this->session->userdata('success_message');
                
                if(!empty($success))
                {
                    echo '<div class="alert alert-success">'.$success.'</div>';
                    $this->session->unset_userdata('success_message');
                }
                ?>
                
            	<div class="row">
                	<div class="col-md-6">
                    	<section class="panel">
            				<header class="panel-heading">		
                    			<h2 class="panel-title">Normal Overtime</h2>
                            </header>
                            
                            <div class="panel-body">
								<?php 
                                echo form_open($this->uri->uri_string());
                                ?>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Type: </label>
                                    <input type="hidden" name="overtime_type" value="1">
                                    
                                    <div class="col-lg-4">
                                        <input type="radio" name="overtime_type_rate" class="form-control" value="1"<?php echo $normal_rate;?>> Rate
                                    </div>
                                    
                                    <div class="col-lg-3">
                                        <input type="radio" name="overtime_type_rate" class="form-control" value="2"<?php echo $normal_amount;?>> Amount
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Overtime: </label>
                                    
                                    <div class="col-lg-7">
                                        <input type="text" name="personnel_overtime_hours" class="form-control" value="<?php echo $normal;?>" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Normal Overtime: </label>
                                    
                                    <div class="col-lg-7">
                                        <input type="text" class="form-control" value="<?php echo $this->payroll_model->calculate_single_overtime($normal, $normal_overtime_type, $normal_overtime_type_rate, $branch_working_hours, $personnel_id);?>" readonly />
                                    </div>
                                </div>
                                
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-lg-8 col-lg-offset-3">
                                        <div class="form-actions center-align">
                                            <button class="submit btn btn-primary" type="submit">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </section>
                    </div>
                    
                	<div class="col-md-6">
                    	<section class="panel">
            				<header class="panel-heading">		
                    			<h2 class="panel-title">Holiday Overtime</h2>
                            </header>
                            
                            <div class="panel-body">
								<?php 
                                echo form_open($this->uri->uri_string());
                                ?>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Type: </label>
                                    <input type="hidden" name="overtime_type" value="2">
                                    <div class="col-lg-4">
                                        <input type="radio" name="overtime_type_rate" class="form-control" value="1"<?php echo $holiday_rate;?>> Rate
                                    </div>
                                    
                                    <div class="col-lg-3">
                                        <input type="radio" name="overtime_type_rate" class="form-control" value="2"<?php echo $holiday_amount;?>> Amount
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Overtime: </label>
                                    
                                    <div class="col-lg-7">
                                        <input type="text" name="personnel_overtime_hours" class="form-control" value="<?php echo $holiday;?>" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-lg-5 control-label">Holiday Overtime: </label>
                                    
                                    <div class="col-lg-7">
                                        <input type="text" class="form-control" value="<?php echo $this->payroll_model->calculate_single_overtime($holiday, $holiday_overtime_type, $holiday_overtime_type_rate, $branch_working_hours, $personnel_id);?>" readonly />
                                    </div>
                                </div>
                                
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-lg-8 col-lg-offset-3">
                                        <div class="form-actions center-align">
                                            <button class="submit btn btn-primary" type="submit">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close();?>
                            </div>
                        </section>
                    </div>
                    
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Total Overtime: </label>
                            
                            <div class="col-lg-3">
                                <input type="text" class="form-control" value="<?php echo $personnel_overtime;?>" readonly />
                            </div>
                            
                            <label class="col-lg-3 control-label">Branch Hours: </label>
                            <div class="col-lg-3">
                                <input type="text" class="form-control" value="<?php echo $branch_working_hours;?>" readonly />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>