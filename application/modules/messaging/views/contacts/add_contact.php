<?php
//personnel data
$name =set_value('name');
$Phonenumber =set_value('Phonenumber');
$balance =set_value('balance');
?>
<section class="panel">
	<header class="panel-heading">						
		<h2 class="panel-title">Add Contact</h2>
	</header>
	<div class="panel-body">
          <div class="center-align">
          	<?php
            	$validation_error = validation_errors();
            	$error = $this->session->userdata('error_message');
				$success = $this->session->userdata('success_message');
				
				if(!empty($error))
				{
					echo '<div class="alert alert-danger">'.$error.'</div>';
					$this->session->unset_userdata('error_message');
				}
				
				if(!empty($validation_error))
				{
					echo '<div class="alert alert-danger">'.$validation_error.'</div>';
				}
				
				if(!empty($success))
				{
					echo '<div class="alert alert-success">'.$success.'</div>';
					$this->session->unset_userdata('success_message');
				}
			?>
          </div>
			<?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal"));?>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
        <div class="form-group">
            <label class="col-lg-5 control-label">Name: </label>
            
            <div class="col-lg-7">
            	<input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo $name;?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-lg-5 control-label">Phone Number: </label>
            
            <div class="col-lg-7">
            	<input type="text" class="form-control" name="Phonenumber" placeholder="Phone Number" value="<?php echo $Phonenumber;?>">
            </div>
        </div>
        
        <!--<div class="form-group">
            <label class="col-lg-5 control-label">Balance: </label>
            
            <div class="col-lg-7">
            	<input type="text" class="form-control" name="balance" placeholder="Balance" value="<?php echo $balance;?>">
            </div>
        </div>-->
        
        <div class="form-group">
            <div class="col-lg-7 col-lg-offset-5">
                <div class="center-align">
        
                    <button class="btn btn-info" type="submit">Add contact</button>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php echo form_close();?>
          </div>
      </section>