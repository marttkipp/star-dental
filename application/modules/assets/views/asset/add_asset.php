<section class="panel">
    <header class="panel-heading">

        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    <div class="panel-body">
    <div class="row" style="margin-bottom:20px;">
                 <div class="col-lg-12">
                        <a href="<?php echo site_url();?>asset-registry/assets" class="btn btn-info btn-sm pull-right">Back to Assets</a>
                  </div>
                </div>
            
          <link href="<?php echo base_url()."assets/themes/jasny/css/jasny-bootstrap.css"?>" rel="stylesheet"/>
          <div class="padd">
            <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! Change a few things up and try submitting again. </div>';
            }
            
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
			$success = $this->session->userdata('success_message');
			$error = $this->session->userdata('error_message');
			
			if(!empty($success))
			{
				echo '<div class="alert alert-success">'.$success.'</div>';
				$this->session->unset_userdata('success_message');
			}
			
			if(!empty($error))
			{
				echo '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}

			?>
		 <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                <div class="col-md-12">
                	<div class="col-md-6">
                    	<div class="form-group">
                                <label class="col-lg-4 control-label">Asset Name</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="asset_name" placeholder="Name" value="<?php echo set_value('asset_name');?>" >
                                </div>
                        </div> 
                       
                      
                        <div class="form-group">
                                <label class="col-lg-4 control-label">Asset Cost</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="asset_cost" id="asset_cost" placeholder="Cost" value="<?php echo set_value('asset_cost');?>" >
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
                                            // $company_name = $row->company_name;
                                            $asset_category_name = $row->asset_category_name;
                                            $asset_category_id = $row->asset_category_id;
                                            
                                            if($asset_category_id == set_value('asset_category_id'))
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

                        <div class="form-group">
                                <label class="col-lg-4 control-label">Description</label>
                                <div class="col-lg-8">
                                    <textarea class="form-control" name="asset_description" placeholder="description"><?php echo set_value('asset_description');?></textarea>
                                 
                                </div>
                        </div>  
                          
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                                <label class="col-lg-3 control-label">Depreciation Type</label>
                                <div class="col-lg-3">
                                    <div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="depriciation_type" id="depriciation_type" value="0"  onclick="check_department_type(0)" >
                                            No Depreciation
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="depriciation_type" id="depriciation_type" value="1"  onclick="check_department_type(1)" >
                                            Straight Line
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3">
                                    <div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="depriciation_type" id="depriciation_type" value="2"  onclick="check_department_type(2)">
                                            Reducing Balance
                                        </label>
                                    </div>
                                </div>
                        </div> 

                        <div id="straight-line" style="display: none;">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Salvage Value</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="salvage_value" id="salvage_value" placeholder="number" value="<?php echo set_value('salvage_value');?>" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label">Useful Life</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="usefull_life" id="usefull_life" placeholder="number" value="<?php echo set_value('usefull_life');?>" >
                                </div>
                            </div>
                        </div>
                        
                        <div id="reducing-balance" style="display: none;">
                            <div class="form-group">
                                    <label class="col-lg-4 control-label">Rate</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="rate" id="rate" placeholder="number" value="<?php echo set_value('rate');?>" >
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label class="col-lg-4 control-label">Salvage</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="salvage" id="salvage" placeholder="number" value="<?php echo set_value('salvage');?>" >
                                    </div>
                            </div>
                             <div class="form-group">
                                    <label class="col-lg-4 control-label">Useful Period</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="installment" id="installment" placeholder="number" value="<?php echo set_value('installment');?>" >
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
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker="" class="form-control" name="asset_pd_period" id="asset_pd_period" placeholder="Purchase date period" value="<?php echo set_value('asset_pd_period');?>" autocomplete="off">
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
                      

               <div class="col-md-12">
                   <div class="form-actions center-align" style="margin-top:20px;">
                        <button class="submit btn btn-primary" type="submit">
                            Add Asset
                        </button>
                </div>    
               </div>
             
           </div> 
                </form>
        </div>
     </div>
     </div>

</section>

<script type="text/javascript">
    

    function check_department_type(depriciation_type)
    {
        // var myTarget = document.getElementById("depriciation_type").value;
        // var depriciation_type = myTarget;

            
        var myTarget2 = document.getElementById("straight-line");
        var myTarget3 = document.getElementById("reducing-balance");
        
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

        // alert(depriciation_type);
      
       
       
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
