<div class="row">
	<section class="panel panel-featured panel-featured-info">
	    <header class="panel-heading">
	        <h2 class="panel-title"><?php echo $title;?></h2>
	    </header>
	      <div class="panel-body">
				<?php
	            echo form_open("admin/search_diary", array("class" => "form-horizontal"));
	            ?>
	            <div class="row">
	                <div class="col-md-6">
	                    
	                    <div class="form-group">
	                        <label class="col-md-4 control-label">Doctor: </label>
	                        
	                        <div class="col-md-8">
	                            <select class="form-control" name="doctor_id">
	                            	<option value="">---Select Doctor---</option>
	                                <?php
										if(count($doctors) > 0){
											foreach($doctors as $row):
												$fname = $row->personnel_fname;
												$onames = $row->personnel_onames;
												$personnel_id = $row->personnel_id;
												$doctor_id = $this->session->userdata('doctor_id');
												if($doctor_id == $personnel_id)
												{
													echo "<option value=".$personnel_id." selected>".$onames." ".$fname."</option>";
												}
												else
												{
													echo "<option value=".$personnel_id.">".$onames." ".$fname."</option>";
												}
												
											endforeach;
										}
									?>
	                            </select>
	                        </div>
	                    </div>
	                    
	                    
	                </div>
	                
	                <div class="col-md-6">
	                	<div class="form-group center-align">
		                    <div class="col-lg-12">
		                        <div class="center-align">
		                            <button type="submit" class="btn btn-info">Search</button>
		                        </div>
		                    </div>
		                </div>
	                    
	                </div>
	                
	                
	            </div>
	           
	            
	           
	            <?php
	            echo form_close();
	            ?>
	          </div>
	       

	  </section>
</div>
