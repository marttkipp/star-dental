<?php
//lender data
$lender_name = set_value('lender_name');
$lender_email = set_value('lender_email');
$lender_phone = set_value('lender_phone');
$lender_location = set_value('lender_location');
$lender_building = set_value('lender_building');
$lender_floor = set_value('lender_floor');
$lender_address = set_value('lender_address');
$lender_post_code = set_value('lender_post_code');
$lender_city = set_value('lender_city');
$start_date = set_value('start_date');
$lender_contact_person_name = set_value('lender_contact_person_name');
$lender_contact_person_onames = set_value('lender_contact_person_onames');
$lender_contact_person_phone1 = set_value('lender_contact_person_phone1');
$lender_contact_person_phone2 = set_value('lender_contact_person_phone2');
$lender_contact_person_email = set_value('lender_contact_person_email');
$lender_description = set_value('lender_description');
$opening_balance  = set_value('opening_balance');
$balance_brought_forward = set_value('balance_brought_forward');
?>
           <section class="panel">
                <header class="panel-heading">
                    <h3 class="panel-title"><?php echo $title;?> </h3>
                    <div class="box-tools pull-right">
                        <a href="<?php echo site_url();?>accounting/lenders" class="btn btn-sm btn-primary" ><i class="fa fa-arrow-left"></i> Back to debtor bills</a>
                    </div>
                </header>

              <div class="panel-body">

                    <!-- Adding Errors -->
                    <?php
						$success = $this->session->userdata('success_message');
						$error = $this->session->userdata('error_message');

						if(!empty($success))
						{
							echo '
								<div class="alert alert-success">'.$success.'</div>
							';

							$this->session->unset_userdata('success_message');
						}

						if(!empty($error))
						{
							echo '
								<div class="alert alert-danger">'.$error.'</div>
							';

							$this->session->unset_userdata('error_message');
						}

						$validation_errors = validation_errors();

						if(!empty($validation_errors))
						{
							echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
						}

						$validation_errors = validation_errors();

						if(!empty($validation_errors))
						{
							echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
						}
                    ?>

                    <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
<div class="row">
	<div class="col-md-6">

        <div class="form-group">
            <label class="col-lg-5 control-label">lender Name: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_name" placeholder="lender Name" value="<?php echo $lender_name;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Email: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_email" placeholder="Email" value="<?php echo $lender_email;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Phone: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_phone" placeholder="Phone" value="<?php echo $lender_phone;?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-5 control-label">Opening Balance: </label>

            <div class="col-lg-7">
                <input type="text" class="form-control" name="opening_balance" placeholder="Opening Balance" value="<?php echo $opening_balance;?>">
            </div>
        </div>
        <div class="form-group">
			<label class="col-lg-5 control-label">Prepayment ?</label>
			<div class="col-lg-3">
				<div class="radio">
					<label>
					<input id="optionsRadios5" type="radio" value="1" name="debit_id">
					Yes
					</label>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="radio">
					<label>
					<input id="optionsRadios6" type="radio" value="2" name="debit_id" checked="checked">
					No
					</label>
				</div>
			</div>
		</div>
      <div class="form-group">
        <label class="col-md-5 control-label">Start date: </label>

        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </span>
                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="lender_account_date" placeholder="Transaction date" id="datepicker" value="<?php echo $start_date;?>" autocomplete="off">
            </div>
        </div>
      </div>
        <div class="form-group">
            <label class="col-lg-5 control-label">Location: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_location" placeholder="Location" value="<?php echo $lender_location;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Building: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_building" placeholder="Building" value="<?php echo $lender_building;?>">
            </div>
        </div>


        <div class="form-group">
            <label class="col-lg-5 control-label">Floor: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_floor" placeholder="Floor" value="<?php echo $lender_floor;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Address: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_address" placeholder="Address" value="<?php echo $lender_address;?>">
            </div>
        </div>

	</div>

    <div class="col-md-6">

        <div class="form-group">
            <label class="col-lg-5 control-label">Post code: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_post_code" placeholder="Post code" value="<?php echo $lender_post_code;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">City: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_city" placeholder="City" value="<?php echo $lender_city;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Contact First Name: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_contact_person_name" placeholder="Contact First Name" value="<?php echo $lender_contact_person_name;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Contact Other Names: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_contact_person_onames" placeholder="Contact Other Names" value="<?php echo $lender_contact_person_onames;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Contact Phone 1: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_phone" placeholder="Contact Phone 1" value="<?php echo $lender_phone;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Contact Phone 2: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_phone" placeholder="Contact Phone 2" value="<?php echo $lender_phone;?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-lg-5 control-label">Contact Email: </label>

            <div class="col-lg-7">
            	<input type="text" class="form-control" name="lender_contact_person_email" placeholder="Contact Email" value="<?php echo $lender_contact_person_email;?>">
            </div>
        </div>

    </div>
</div>

<div class="row" style="margin-top:10px;">
	<div class="col-md-12">

        <div class="form-group">
            <label class="col-lg-2 control-label">Description: </label>

            <div class="col-lg-9">
            	<textarea class="form-control" name="lender_description" rows="5"><?php echo $lender_phone;?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top:10px;">
	<div class="col-md-12">
        <div class="form-actions text-center">
            <button class="submit btn btn-primary" type="submit">
                Add lender
            </button>
        </div>
    </div>
</div>
    <?php echo form_close();?>
</div>
</section>
