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
            $salvage_value = $assets_details[0]->salvage_value;
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
                $salvage_value = set_value('salvage_value');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }

            if($depriciation_type == 0)
            {
                $depriciation_type_straight = '';
                $depriciation_type_reducing = '';
                $no_depreciation = 'checked';
            }
            else if($depriciation_type == 1)
            {
                $depriciation_type_straight = 'checked';
                $depriciation_type_reducing = '';
                $no_depreciation = '';
            }
            else if($depriciation_type == 2)
            {
                 $depriciation_type_straight = '';
                $depriciation_type_reducing = 'checked';
                $no_depreciation = '';

            }
            else
            {
                $depriciation_type_straight = '';
                $depriciation_type_reducing = '';
                $no_depreciation = '';
            }
			
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                      
            	<div class="col-md-6">
                	<div class="form-group">
                            <label class="col-lg-4 control-label">Asset Name</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="asset_name" placeholder="name" value="<?php echo $asset_name;?>" >
                            </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Asset Value</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_cost" id="asset_cost" placeholder="cost" value="<?php echo $asset_value;?>" >
                        </div>
                    </div> 
                    <div class="form-group">
                            <label class="col-lg-4 control-label">Description</label>
                            <div class="col-lg-8">
                                <textarea class="form-control" name="asset_description" placeholder="description"><?php echo $asset_description ;?></textarea>
                              
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
                <div class="col-md-6">
                    <div class="form-group">
                            <label class="col-lg-3 control-label">Depreciation Type</label>
                              <div class="col-lg-3">
                                    <div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="depriciation_type" id="depriciation_type" value="0"  onclick="check_department_type(0)" <?php echo $no_depreciation?> >
                                            No Depreciation
                                        </label>
                                    </div>
                                </div>
                            <div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios2" type="radio" name="depriciation_type" value="1" <?php echo $depriciation_type_straight?> onclick="check_department_type(1)"  >
                                        Straight Line
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-lg-3">
                                <div class="radio">
                                    <label>
                                        <input id="optionsRadios2" type="radio" name="depriciation_type" value="2" <?php echo $depriciation_type_reducing?> onclick="check_department_type(1)">
                                        Reducing Balance
                                    </label>
                                </div>
                            </div>
                    </div> 
                    <div id="straight-line" style="display: none;">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Salvage Value</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="salvage_value" id="salvage_value" placeholder="number" value="<?php echo $salvage_value;?>" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Useful Life</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="usefull_life" id="usefull_life" placeholder="number" value="<?php echo $installment;?>" >
                            </div>
                        </div>
                    </div>
                    
                    <div id="reducing-balance" style="display: none;">
                        <div class="form-group">
                                <label class="col-lg-4 control-label">Rate</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="rate" id="rate" placeholder="number" value="<?php echo $rate;?>" >
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-lg-4 control-label">Salvage</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="salvage" id="salvage" placeholder="number" value="<?php echo $salvage_value;?>" >
                                </div>
                        </div>
                         <div class="form-group">
                                <label class="col-lg-4 control-label">Useful Period</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="installment" id="installment" placeholder="number" value="<?php echo $installment;?>" >
                                </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Purchase date period</label>
                        <div class="col-lg-8">
                           <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker="" class="form-control" name="asset_pd_period" id="asset_pd_period" placeholder="Purchase date period" value="<?php echo $asset_pd_period;?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                </div>   
               
            </div>
            <br/> 
            <div class="col-md-12" style="margin-top:10px;">
                <div class="form-actions center-align">
                    <a class="btn btn-primary" onclick="calculate_amortization()">
                        View Depreciation Chart
                    </a>
                </div>
            </div>
             <div class="col-md-12" style="margin-top:10px;">
                <br/>
                <div id="long_amortize_calculations"></div>
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

<script type="text/javascript">
    
    $(function() {
        var depriciation_type = <?php echo $depriciation_type;?>


        check_department_type(depriciation_type);
    });
    function check_department_type(depriciation_type)
    {
        // var myTarget = document.getElementById("depriciation_type").value;
        // var depriciation_type = myTarget;

            
        var myTarget2 = document.getElementById("straight-line");
        var myTarget3 = document.getElementById("reducing-balance");
        // alert(depriciation_type);
        if(depriciation_type == 1)
        {
            myTarget2.style.display = 'block';
            myTarget3.style.display = 'none'; 
        }
        else if(depriciation_type==2)
        {
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'block';
        }
        calculate_amortization();
    
    }
    function calculate_amortization()
    {
        // var depriciation_type = document.getElementById("depriciation_type").value;
         

         var radios = document.getElementsByName('depriciation_type');

        for (var i = 0, length = radios.length; i < length; i++) {
          if (radios[i].checked) {
            // do whatever you want with the checked radio
            var depriciation_type = radios[i].value;

            // only one radio can be logically checked, don't check the rest
            break;
          }
        }

        
      
       
       
        //get department services


        if(depriciation_type == 1)
        {

            var purchase_date = document.getElementById("asset_pd_period").value;
            var installment =  document.getElementById("usefull_life").value;
            var asset_cost = document.getElementById("asset_cost").value;
            var salvage_value = document.getElementById("salvage_value").value;



            $.get( "<?php echo site_url();?>assets/calculate_amortization/"+depriciation_type+"/"+installment+"/"+asset_cost+"/"+purchase_date+"/"+salvage_value, function( data ) 
            {


                $( "#long_amortize_calculations" ).html( data );
            });


            
        }
        else
        {
           
            var purchase_date = document.getElementById("asset_pd_period").value;

            var installment =  document.getElementById("installment").value;

            var asset_cost = document.getElementById("asset_cost").value;
             // alert(depriciation_type);
            var rate = document.getElementById("rate").value;
            var salvage = document.getElementById("salvage").value;


            $.get( "<?php echo site_url();?>assets/calculate_amortization/"+depriciation_type+"/"+installment+"/"+asset_cost+"/"+purchase_date+"/"+salvage+"/"+rate, function( data ) 
            {

                $( "#long_amortize_calculations" ).html( data );
            });
           
        }


         

    }

    function record_amortization()
    {
        

         // $( "#loan_details" ).html('');

        var long_loans_plan_id = document.getElementById("depriciation_type").value;
        var long_actual_application_date = document.getElementById("asset_pd_period").value;
        var long_proposed_repayments =  document.getElementById("installment").value;
        var long_proposed_amount = document.getElementById("asset_cost").value;
        var rate = document.getElementById("rate").value;
        var duration = document.getElementById("duration").value;
    
        //alert(savings_plan_id);
       
        //get department services
        $.get( "<?php echo site_url();?>assets/asserts/record_amortization/"+long_loans_plan_id+"/"+long_proposed_repayments+"/"+long_proposed_amount+"/"+long_actual_application_date+"/"+rate, function( data ) 
        {
            $( "#amortization_record" ).html( data );
        });

    }




</script>
