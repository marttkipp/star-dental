
          <section class="panel">
                <header class="panel-heading">
                   
            
                    <h2 class="panel-title"><?php echo $title;?></h2>
                    <a href="<?php echo site_url();?>messaging/message-templates" class="btn btn-sm btn-info pull-right" style="margin-top:-25px">Back to Message Template</a>

                </header>
                <div class="panel-body">
                	 <div class="alert alert-info"> 
                        <strong>Include in your messages the following formarts formarts for:</strong> 
                       <div class="row">
                            <ol class="col-md-4" style="margin-left:0px">
                                <li>Name : [name]</li>
                             </ol>
                        </div>
                        <hr>
                        <div class="row center-align">
                            <strong>Description Example:</strong>
                            <p><strong>Good monring [name]. </strong></p>
                        </div>
                    
                    </div>
                <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! Change a few things up and try submitting again. </div>';
            }
			
			//the message_template details
			$message_template_id = $message_template[0]->message_template_id;
			$message_template_code = $message_template[0]->message_template_code;
			$message_template_description = $message_template[0]->message_template_description;
			$message_template_status = $message_template[0]->message_template_status;
            $contact_type = $message_template[0]->contact_type;
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
				$message_template_id = set_value('message_template_id');
				$message_template_code = set_value('message_template_code');
				$message_template_description = set_value('message_template_description');
				$message_template_status = set_value('message_template_status');
                $contact_type = set_value('contact_type');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
			
            ?>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <!-- message_template Name -->
            <div class="form-group">
                <label class="col-lg-4 control-label">Template Code</label>
                <div class="col-lg-4">
                	<input type="text" class="form-control" name="template_code" placeholder="Template Name" value="<?php echo $message_template_code;?>" required>
                </div>
            </div>
           
           <div class="form-group">
                <label class="col-lg-4 control-label">Template Description</label>
                <div class="col-lg-4">
                	<textarea class="form-control" name="template_description" placeholder="Template Description"><?php echo $message_template_description;?></textarea>
                </div>
            </div>
            <div class="form-group">


                <label class="col-lg-4 control-label">Contacts? </label>

                <?php 

                if(empty($contact_type))
                {
                    ?>
                    <div class="col-lg-4">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios2" type="radio" name="contact_type" value="0" checked="checked" >
                                Patients
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios2" type="radio" name="contact_type" value="1">
                                Contacts
                            </label>
                        </div>
                    </div>
                    <?php
                }
                else
                {
                     
                    ?>
                    <div class="col-lg-4">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios2" type="radio" name="contact_type" value="0"  >
                                Patients
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="radio">
                            <label>
                                <input id="optionsRadios2" type="radio" name="contact_type" value="1" checked="checked">
                                Contacts
                            </label>
                        </div>
                    </div>
                    <?php
                
                }
                ?>
                
            </div>
           
           
            <div class="form-actions center-align">
                <button class="submit btn btn-primary" type="submit">
                    Edit message template
                </button>
            </div>
            <br />
            <?php echo form_close();?>
                </div>
            </section>