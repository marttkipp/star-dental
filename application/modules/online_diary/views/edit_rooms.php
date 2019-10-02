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
                            <a href="<?php echo site_url();?>online-dairies/rooms" class="btn btn-info pull-right">Back to Room</a>
                        </div>
                    </div>
                <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! '.$error.' </div>';
            }
			
			//the visit_type details
			$room_name = $room_dr[0]->room_name;
			$room_description = $room_dr[0]->room_description;
			$room_status = $room_dr[0]->room_status;
			$room_id2 = $room_dr[0]->room_id;
            
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
				$room_name= set_value('room_name');
				$room_status = set_value('room_status');
				$room_id2 = set_value('room_id');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
			
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Room:</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="room_name" placeholder="room name" value="<?php echo $room_name;?>" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Room Description:</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="room_description" placeholder="room description" value="<?php echo $room_description;?>" required>
                      </div>
                    </div>
                </div>
               
            </div>
            <div class="form-actions center-align" style="margin-top:10px;">
                <button class="submit btn btn-primary" type="submit">
                    Edit Room
                </button>
            </div>
            <br />
            <?php echo form_close();?>
                </div>
            </section>
