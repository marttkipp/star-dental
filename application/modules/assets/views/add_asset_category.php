<section class="panel">
    <header class="panel-heading">

        <h2 class="panel-title"><?php echo $title;?></h2>
    </header>
    <div class="panel-body">
    <div class="row" style="margin-bottom:20px;">
                 <div class="col-lg-12">
                        <a href="<?php echo site_url();?>asset-registry/asset-category" class="btn btn-info btn-sm pull-right">Back to visit types</a>
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
            	<div class="col-md-6">
            	<div class="form-group">
                        <label class="col-lg-4 control-label">Asset Category Name</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_category_name" placeholder="asset category" value="<?php echo set_value('asset_category_name');?>" >
                        </div>
                </div>  

                    </div>
                 </div>
             <div class="form-actions center-align" style="margin-top:10px;">
                        <button class="submit btn btn-primary" type="submit">
                            Add Asset Category
                        </button>
                    </div>    
           </div> 

</section>
