<?php

 $insurance_company = $this->reception_model->get_patient_insurance_company($patient_id);

?>
		<style type="text/css">
			#insured_company{display:none;}
		</style>
		<section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Initiate Visit </h2>
                <div class="pull-right" style="margin-top:-25px;">
                    <a href="<?php echo site_url();?>patients" class="btn btn-primary btn-sm ">  <i class="fa fa-angle-left"></i> Patients list</a>
                </div>
            </header>
		        <!-- Widget content -->
		        <div class="panel-body">                	
                	
                	<div class="well well-sm info" style="margin-top:0px;">
                        <h5 style="margin:0;">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>First name:</strong>
                                        </div>
                                        <div class="col-md-8">
                                            <?php echo $patient_surname.' '.$patient_othernames;?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Insurance Company:</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo $insurance_company;?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Balance:</strong>
                                        </div>
                                        <div class="col-md-8">
                                            Kes <?php echo number_format($account_balance, 2);?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a href="<?php echo site_url();?>administration/individual_statement/<?php echo $patient_id;?>/2" class="btn btn-sm btn-success pull-right" target="_blank" style="margin-top: 5px;">Statement</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h5>
                    </div>
                    
					<?php 
                        $validation_error = validation_errors();
                        
                        if(!empty($validation_error))
                        {
                            echo '<div class="alert alert-danger center-align">'.$validation_error.'</div>';
                        }
						
						$error = $this->session->userdata('error_message');
						$success = $this->session->userdata('success_message');
						
						if(!empty($error))
						{
							echo '<div class="alert alert-danger">'.$error.'</div>';
							$this->session->unset_userdata('error_message');
						}
						
						if(!empty($success))
						{
							echo '<div class="alert alert-success">'.$success.'</div>';
							$this->session->unset_userdata('success_message');
						}
                      
                    ?>
                    
					<?php $this->load->view('visit/initiate_outpatient');?>
                    
                </div>
        	</section>

