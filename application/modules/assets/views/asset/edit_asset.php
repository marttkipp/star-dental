<section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    </div>
            
                    <h2 class="panel-title"><?php echo $title;?></h2>
                </header>
                <div class="panel-body">
                	<div class="row" style="margin-bottom:20px;">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url();?>asset-registry/assets" class="btn btn-info pull-right">Back to Asset</a>
                        </div>
                    </div>
                <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! '.$error.' </div>';
            }
			
			//the visit_type details
			$asset_name = $assets_details[0]->asset_name;
			$asset_status = $assets_details[0]->asset_status;
			$asset_serial_no = $assets_details[0]->asset_serial_no;
			$asset_description = $assets_details[0]->asset_description;
			$asset_model_no = $assets_details[0]->asset_model_no;
			$asset_pd_period = $assets_details[0]->asset_pd_period;
			$ldl_type = $assets_details[0]->ldl_type;
			$asset_value = $assets_details[0]->asset_value;
            $asset_number = $assets_details[0]->asset_number;
			$asset_supplier_no = $assets_details[0]->asset_supplier_no;
			$asset_owner_name = $assets_details[0]->asset_owner_name;
            $asset_cost = $assets_details[0]->asset_cost;
            $duration = $assets_details[0]->duration;
            $installment = $assets_details[0]->installment;
            $rate = $assets_details[0]->rate;
            $depriciation_type = $assets_details[0]->depriciation_type;
            $asset_category_id2 = $assets_details[0]->asset_category_id;
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
				$asset_name= set_value('asset_name');
				$asset_status= set_value('asset_status');
				$asset_serial_no= set_value('asset_serial_no');
				$asset_description= set_value('asset_description');
				$asset_model_no= set_value('asset_model_no');
				$asset_model_no= set_value('asset_model_no');
				$asset_number= set_value('asset_number');
				$asset_owner_name = set_value('asset_owner_name');
                $asset_cost = set_value('asset_cost');
                $duration = set_value('duration');
                $installment = set_value('installment');
                $rate = set_value('rate');
                $depriciation_type = set_value('depriciation_type');
				$ldl_type= set_value('ldl_type');

				$ldl_date = set_value('ldl_date');
				$asset_supplier_no =  set_value('asset_supplier_no');
                $asset_number =  set_value('asset_number');
				$asset_project_no =  set_value('asset_project_no');
				$asset_inservice_period =  set_value('asset_inservice_period');
				$asset_disposal_period =  set_value('asset_disposal_period');
				$asset_category_id2 = set_value('asset_category_id');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }

            if($depriciation_type == 1)
            {
                $depriciation_type_straight = 'checked';
                $depriciation_type_reducing = '';
            }
            else if($depriciation_type == 2)
            {
                 $depriciation_type_straight = '';
                $depriciation_type_reducing = 'checked';

            }
            else
            {
                $depriciation_type_straight = '';
                $depriciation_type_reducing = '';
            }
			
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                         <div class="col-md-12">
            	<div class="col-md-6">
            	<div class="form-group">
                        <label class="col-lg-4 control-label">Asset Name</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_name" placeholder="name" value="<?php echo $asset_name;?>" >
                        </div>
                </div> 
                <div class="form-group">
                        <label class="col-lg-4 control-label">Quantity</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_number" placeholder="number" value="<?php echo $asset_number;?>" >
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-lg-4 control-label">Asset Value</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_cost" placeholder="cost" value="<?php echo $asset_value;?>" >
                        </div>
                </div> 
                <div class="form-group">
                        <label class="col-lg-4 control-label">Description</label>
                        <div class="col-lg-8">
                            <textarea class="form-control" name="asset_description" placeholder="description"><?php echo $asset_description ;?></textarea>
                          
                        </div>
                </div>
                         
              </div>
             <div class="col-md-6">
                 
               <div class="form-group">
                        <label class="col-lg-4 control-label">Depreciation Type</label>
                        <div class="col-lg-4">
                            <div class="radio">
                                <label>
                                    <input id="optionsRadios2" type="radio" name="depriciation_type" value="1" <?php echo $depriciation_type_straight?>  >
                                    Straight Line
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="radio">
                                <label>
                                    <input id="optionsRadios2" type="radio" name="depriciation_type" value="2" <?php echo $depriciation_type_reducing?> >
                                    Reducing Balance
                                </label>
                            </div>
                        </div>
                </div> 
                <div class="form-group">
                        <label class="col-lg-4 control-label">Rate</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="rate" placeholder="number" value="<?php echo $rate;?>" >
                        </div>
                </div>

                <div class="form-group">
                        <label class="col-lg-4 control-label">Duration</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="duration" placeholder="number" value="<?php echo $duration;?>" >
                        </div>
                </div>
                 <div class="form-group">
                        <label class="col-lg-4 control-label">Installment</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="installment" placeholder="number" value="<?php echo $installment;?>" >
                        </div>
                </div>
              
                <div class="form-group">
                        <label class="col-lg-4 control-label">Purchase date period</label>
                        <div class="col-lg-8">
                           <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker="" class="form-control" name="asset_pd_period" placeholder="Purchase date period" value="<?php echo $asset_pd_period;?>">
                            </div>
                        </div>
                </div>
                <div class="form-group">
                            <label class="col-lg-4 control-label">Asset Category </label>
                            <div class="col-lg-8">
                                <select id="asset_category_id" name="asset_category_id" class="form-control">
                                    <option value="">--- None ---</option>
                                    <?php
                                    if($all_categories->num_rows() > 0)
                                    {	
                                        foreach($all_categories->result() as $row):
										
											$asset_category_name = $row->asset_category_name;
											
											$asset_category_id = $row->asset_category_id;
									   if($asset_category_id2 == $asset_category_id)
                                        {
                                            echo "<option value=".$asset_category_id." selected='selected'> ".$asset_category_name."</option>";
                                        }
                                        
                                        else
                                        {
                                            echo "<option value=".$asset_category_id."> ".$asset_category_name."</option>";
                                        }
                                    endforeach;	
                                } 
                                ?>
                                </select>
                            </div>
                </div> 

                                    
                </div>
               </div>   
               
            </div>
            <div class="form-actions center-align" style="margin-top:10px;">
                <button class="submit btn btn-primary" type="submit">
                    Edit Asset
                </button>
            </div>
            <br />
            <?php echo form_close();?>
                </div>
            </section>