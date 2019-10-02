<section class="panel">
    <header class="panel-heading">
    
        <h2 class="panel-title"><?php echo $title;?></h2>
         <a href="<?php echo site_url();?>messaging/message-templates" class="btn btn-info pull-right" style="margin-top:-25px;">Back to templates</a>
    </header>
    <div class="panel-body">
    	
            
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
        ?>
        
        <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
        <div class="alert alert-info"> 
        	<strong>Include in your messages the following formarts formarts for:</strong> 
        	<div class="row">
		       	<ol class="col-md-4" style="margin-left:0px">
                    <li>Name : [name]</li>
                    
                    
                 </ol>
                <ol class="col-md-4">
                	<li>Phone Number: [Phonenumber]</li>
                </ol>
            </div>
            <hr>
            <div class="row center-align">
            	<strong>Description Example:</strong>
            	<p><strong>Good morning [name]. Your welcome to our Hospital</strong></p>
            </div>

        
        </div>
        <!-- Category Name -->
        <div class="form-group">
            <label class="col-lg-4 control-label">Template Code</label>
            <div class="col-lg-6">
                <input type="text" class="form-control" name="template_code" placeholder="Template Code" value="<?php echo set_value('template_code');?>" required>
            </div>
        </div>
     	<div class="form-group">
            <label class="col-lg-4 control-label">Template Description</label>
            <div class="col-lg-6">
                <textarea class="form-control" name="template_description" placeholder="Template Description"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">Contacts? </label>
            <div class="col-lg-2">
                <div class="radio">
                    <label>
                        <input id="optionsRadios2" type="radio" name="contact_type" value="0" checked="checked" >
                        Patients
                    </label>
                </div>
            </div>
            
            <div class="col-lg-2">
                <div class="radio">
                    <label>
                        <input id="optionsRadios2" type="radio" name="contact_type" value="1">
                        Contacts
                    </label>
                </div>
            </div>
             <div class="col-lg-2">
                <div class="radio">
                    <label>
                        <input id="optionsRadios2" type="radio" name="contact_type" value="2">
                        Contacts and Patients
                    </label>
                </div>
            </div>
        </div>
      
        <div class="form-actions center-align">
            <button class="submit btn btn-primary" type="submit">
                Add template
            </button>
        </div>
        <br />
        <?php echo form_close();?>
    </div>
</section>