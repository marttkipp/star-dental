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
                            <input type="text" class="form-control" name="asset_name" placeholder="name" value="<?php echo set_value('asset_name');?>" >
                        </div>
                </div> 
                <div class="form-group">
                        <label class="col-lg-4 control-label">Quantity</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_number" placeholder="Number" value="<?php echo set_value('asset_number');?>" >
                        </div>
                </div> 
              
                <div class="form-group">
                        <label class="col-lg-4 control-label">Asset Cost</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_cost" placeholder="cost" value="<?php echo set_value('asset_cost');?>" >
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
                        <label class="col-lg-4 control-label">Depreciation Type</label>
                        <div class="col-lg-4">
                            <div class="radio">
                                <label>
                                    <input id="optionsRadios2" type="radio" name="depriciation_type" value="1"  >
                                    Straight Line
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="radio">
                                <label>
                                    <input id="optionsRadios2" type="radio" name="depriciation_type" value="2" >
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
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker="" class="form-control" name="asset_pd_period" placeholder="Purchase date period" value="<?php echo set_value('asset_pd_period');?>">
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
                </div>
               </div>  
               <br/> 
               <div class="col-md-12">
                   <div class="form-actions center-align" style="margin-top:20px;">
                        <button class="submit btn btn-primary" type="submit">
                            Add Asset
                        </button>
                </div>    
               </div>
             
           </div> 

</section>
