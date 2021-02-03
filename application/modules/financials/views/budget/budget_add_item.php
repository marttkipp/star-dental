<?php
if(!empty($month_id))
{
	if($month_id < 10)
	{
		$month_id = '0'.$month_id;
	}
   
}

// var_dump($account_id);die();


?>
<div>
	<section class="panel">
	    <header class="panel-heading">
	            <h5 class="pull-left"><i class="icon-reorder"></i>Add Budget Item</h5>
	          <div class="widget-icons pull-right">
	              
	          </div>
	          <div class="clearfix"></div>
	    </header>
	    <div class="panel-body">
	        <div class="padd">
	        	<?php echo form_open("finance/creditors/confirm_payment/".$budget_year, array("class" => "form-horizontal","id" => "confirm-budget-item"));?>
		        	<div class="col-md-12">
		        		<div class="col-md-4">
							<div class="form-group">
					            <label class="col-md-4 control-label">Account: </label>
					            
					            <div class="col-md-8">
					            	<?php
					            	$accounts = $this->budget_model->get_all_expense_account();


					            	?>
					            	<select class="form-control" name="account_id" id="account_id">
					            		
									<?php

										if(empty($account_id))
										{
											$changed = '<option value="">--- Account ---</option>';
										}
										else
										{
											$changed = '';
										}
		                                
										

		                                 if($accounts->num_rows() > 0)
		                                 {
		                                     foreach($accounts->result() as $row):
		                                         // $company_name = $row->company_name;
		                                         $account_name = $row->account_name;
		                                         $account_id_db = $row->account_id;
		                                         $parent_account = $row->parent_account;

		                                         if($parent_account != $current_parent)
		                                         {
		                                         	  $account_from_name = $this->budget_model->get_account_name($parent_account);
		                                         	$changed .= '<optgroup label="'.$account_from_name.'">';
		                                         }
		                                         if(!empty($account_id) AND $account_id == $account_id_db)
		                                         {

		                                         	$changed .= "<option value=".$account_id_db." selected> ".$account_name."</option>";
		                                         }
		                                       	 else
		                                       	 {
		                                       	 	$changed .= "<option value=".$account_id_db."> ".$account_name."</option>";
		                                       	 }
		                                       	 $current_parent = $parent_account;
		                                       	 if($parent_account != $current_parent)
		                                         {
		                                         	$changed .= '</optgroup>';
		                                         }

		                                     	 
		                                     	
		                                     endforeach;
		                                 }
		                                 echo $changed;


		                                 ?>

								</select>
							
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
					            <label class="col-md-4 control-label">Year: </label>
					            
					            <div class="col-md-8">
					            	<select id="budget_year" name="budget_year" class="form-control">	
						                <?php

						                $start_year = 2019;
								        $end_year  = date('Y') + 1;
								       
								   		for ($i=$start_year; $i <= $end_year; $i++) { 
								   			# code...
											if($i == $budget_year)
											{
			                                        echo "<option value=".$i." selected>".$i."</option>";
		                                    }
		                                    else{
		                                        echo "<option value=".$i.">".$i."</option>";
		                                    }
								   		}          
						                
		                                ?>
		                             </select> 
					            </div>
					        </div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
					            <label class="col-md-4 control-label">Month: </label>
					            
					            <div class="col-md-8">
					            	<select id="budget_month" name="budget_month" class="form-control">
					            			
					                  <?php
					                  $month = $this->budget_model->get_month();
	                                    if($month->num_rows() > 0){
	                                        foreach ($month->result() as $row):
	                                            $mth = $row->month_name;
	                                            $mth_id = $row->month_id;

	                                            if($mth_id < 10)
						                        {
						                            $mth_id = '0'.$mth_id;
						                        }
	                                            if($month_id == NULL)
	                                            {

	                                            	if($mth_id == date("m"))
	                                            	{
	                                            		echo "<option value=".$mth_id." selected>".$row->month_name."</option>";
	                                            	}
	                                            	else
	                                            	{
	                                            		echo "<option value=".$mth_id.">".$row->month_name."</option>";
	                                            	}
	                                                
	                                            }
	                                            else{
	                                               if($mth_id == $month_id)
	                                            	{
	                                            		echo "<option value=".$mth_id." selected>".$row->month_name."</option>";
	                                            	}
	                                            	else
	                                            	{
	                                            		echo "<option value=".$mth_id.">".$row->month_name."</option>";
	                                            	}
	                                            }
	                                        endforeach;
	                                    }
	                                ?>
	                             	</select>   
					            </div>
					        </div>
						</div>
					</div>
					
					<div class="col-md-12" style="margin-top: 10px;">
						
						<div class="col-md-4">
							<div class="form-group">
					            <label class="col-md-4 control-label">Amount: </label>
					            
					            <div class="col-md-8">
					            	<input type="number" class="form-control" name="budget_amount" id="budget_amount" required="required" autocomplete="off">
					            </div>
					        </div>
					    </div>

					    <div class="col-md-4">
							<div class="form-group">
					            <label class="col-md-4 control-label">Department: </label>
					            
					            <div class="col-md-8">
					               <select id="department_id" name="department_id" class="form-control">	
						                <?php
						                $departments = $this->budget_model->get_departments();
						               
								   		 if($departments->num_rows() > 0){
	                                        foreach ($departments->result() as $row):
	                                            $department_name = $row->department_name;
	                                            $department_id = $row->department_id;
	                                            
	                                            echo "<option value=".$department_id.">".$row->department_name."</option>";
	                                            
	                                        endforeach;
	                                    }        
						                
		                                ?>
		                             </select> 
					            </div>
					        </div>
						</div>
					    <div class="col-md-4">
							<div class="form-group">				            
					            <div class="center-align">
					            	<button type="submit"  class="btn btn-sm btn-success" ><i class="fa fa-folder-closed"></i> SUBMIT ENTRY </button>
					            </div>
					        </div>
					    </div>
		        	</div>
		            
		           
		        <?php echo form_close();?>

		         <hr>
	        	<div class="col-md-12" style="padding:10px;">
	        		<div id="visit-payment-div" ></div>
	        	</div>
	        </div>

	    </div>
	</section>
	<div class="col-md-12">					        	
		<div class="center-align">
			<a  class="btn btn-sm btn-warning" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR </a>
		</div>  
	</div>
</div>