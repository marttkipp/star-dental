<?php
	$month = $this->payroll_model->get_months();
    $branches = $this->branches_model->all_branches();
	$all_personnel = $this->personnel_model->all_personnel();
?>	<div class="row">
        <section class="panel">
            <header class="panel-heading">						
                <h2 class="panel-title">Generate Payslips</h2>
            </header>
            <div class="panel-body">
            	<?php
                $error = $this->session->userdata('error_message');
				
				if(!empty($error))
				{
					echo '<div class="alert alert-danger">'.$error.'</div>';
					$this->session->unset_userdata('error_message');
				}
				?>
          		<div class="padd">
             		<div class="col-sm-8" align="center">
                            	<section class="panel">
                                    <header class="panel-heading">						
                                        <h2 class="panel-title">Generate Payslips</h2>
                                    </header>
                                    <div class="panel-body">
                                    	<?php 
										echo form_open('payroll/generate-personnel-payslips-duration', array('target' => '_blank'));
										?>
                                        <div class="form-group">
                                            <label class="col-lg-5 control-label">Branch: </label>
                                            
                                            <div class="col-lg-7">
                                                <select name="branch_id" class="form-control">
                                                    <?php
                                                        if($branches->num_rows() > 0){
                                                            foreach ($branches->result() as $row):{
                                                                $branch_name = $row->branch_name;
                                                                $branch_id = $row->branch_id;                                                                
                                                                    echo "<option value=".$branch_id.">".$row->branch_name."</option>";
                                                                }
                                                            endforeach;
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-5 control-label">Personnel: </label>
                                            
                                            <div class="col-lg-7">
                                                <select name="personnel_id" class="form-control">
                                                    <?php
                                                        if($all_personnel->num_rows() > 0){
                                                            foreach ($all_personnel->result() as $row):{
                                                                $personnel_fname = $row->personnel_fname;
																$personnel_onames = $row->personnel_onames;
																$personnel_number = $row->personnel_number;
																$personnel_name = $personnel_number.'-'.$personnel_fname. ' '.$personnel_onames;
                                                                $personnel_id = $row->personnel_id;                                                                
                                                                    echo "<option value=".$personnel_id.">".$personnel_name."</option>";
                                                                }
                                                            endforeach;
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-5 control-label">Year: </label>
                                            
                                            <div class="col-lg-7">
                                                <input type="text" name="year" class="form-control" size="54" value="<?php echo date("Y");?>" />
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-lg-5 control-label">From Month: </label>
                                            
                                            <div class="col-lg-7">
                                                <select name="from_month" class="form-control">
                                                    <?php
                                                        if($month->num_rows() > 0){
                                                            foreach ($month->result() as $row):
                                                                $mth = $row->month_name;
                                                                $mth_id = $row->month_id;
                                                                if($mth == date("M")){
                                                                    echo "<option value=".$mth_id." selected>".$row->month_name."</option>";
                                                                }
                                                                else{
                                                                    echo "<option value=".$mth_id.">".$row->month_name."</option>";
                                                                }
                                                            endforeach;
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-lg-5 control-label"> To Month: </label>
                                            
                                            <div class="col-lg-7">
                                                <select name="to_month" class="form-control">
                                                    <?php
                                                        if($month->num_rows() > 0){
                                                            foreach ($month->result() as $row):
                                                                $mth = $row->month_name;
                                                                $mth_id = $row->month_id;
                                                                if($mth == date("M")){
                                                                    echo "<option value=".$mth_id." selected>".$row->month_name."</option>";
                                                                }
                                                                else{
                                                                    echo "<option value=".$mth_id.">".$row->month_name."</option>";
                                                                }
                                                            endforeach;
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row" style="margin-top:10px;">
                                            <div class="col-lg-7 col-lg-offset-5">
                                                <div class="form-actions center-align">
                                                    <button class="submit btn btn-primary" type="submit">
                                                        Create
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php echo form_close();?>
                                    </div>
                                </section>
                            </div>
            	</div>
            </div>
        </section>
        </div>