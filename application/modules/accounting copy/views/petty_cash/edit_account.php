 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
                <a href="<?php echo base_url();?>accounting/general-journal-entries" class="btn btn-primary pull-right btn-sm">Back to accounts</a>
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
        <?php
        if(isset($error)){
            echo '<div class="alert alert-danger"> Oh snap! Change a few things up and try submitting again. </div>';
        }
    	
    	//the store details
    	$account_id = $query->account_id;
		$account_name = $query->account_name;
		$accont_status = $query->accont_status;
		$account_opening_balance = $query->account_opening_balance;
        $account_type_id2 = $query->account_type_id;
        $account_id2 = $query->parent_account;
        
        $validation_errors = validation_errors();
        
        if(!empty($validation_errors))
        {
    		$store_id = set_value('store_id');
    		$account_name = set_value('account_name');
    		$account_opening_balance = set_value('account_balance');
    		$account_type_id2 = set_value('account_type_id');
            $account_id2 = set_value('parent_account');
    		
            echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
        }
    	
        ?>
        
        <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-lg-4 control-label">Acount Name</label>
                    <div class="col-lg-4">
                    	<input type="text" class="form-control" name="account_name" placeholder="Account Name" value="<?php echo $account_name;?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-4 control-label">Opening Balance</label>
                    <div class="col-lg-4">
                    	<input type="text" class="form-control" name="account_balance" placeholder="Account Balance" value="<?php echo $account_opening_balance;?>" required>
                    </div>
                </div>
               
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-lg-4 control-label">Parent Account </label>
                    <div class="col-lg-8">
                        <select id="parent_account" name="parent_account" class="form-control">
                            <option value="">--- None ---</option>
                            <?php
                            if($parent_accounts->num_rows() > 0)
                            {   
                                foreach($parent_accounts->result() as $row):
                                
                                    $account_name = $row->account_name;
                                    
                                    $account_id = $row->account_id;
                               if($account_id2 == $account_id)
                                {
                                    echo "<option value=".$account_id." selected='selected'> ".$account_name."</option>";
                                }
                                
                                else
                                {
                                    echo "<option value=".$account_id."> ".$account_name."</option>";
                                }
                            endforeach; 
                        } 
                        ?>
                        </select>
                    </div>
              </div> 
                <div class="form-group">
                    <label class="col-lg-4 control-label">Account Type </label>
                    <div class="col-lg-8">
                        <select id="account_type_id" name="account_type_id" class="form-control">
                            <option value="">--- None ---</option>
                            <?php
                            if($types->num_rows() > 0)
                            {	
                                foreach($types->result() as $row):
								
									$account_type_name = $row->account_type_name;
									
									$account_type_id = $row->account_type_id;
							   if($account_type_id2 == $account_type_id)
                                {
                                    echo "<option value=".$account_type_id." selected='selected'> ".$account_type_name."</option>";
                                }
                                
                                else
                                {
                                    echo "<option value=".$account_type_id."> ".$account_type_name."</option>";
                                }
                            endforeach;	
                        } 
                        ?>
                        </select>
                    </div>
              </div> 
                <!-- Activate checkbox -->
           <div class="form-group">
                <label class="col-lg-4 control-label">Activate Account?</label>
                <div class="col-lg-4">
                    <div class="radio">
                        <label>
                        	<?php
                            if($account_status == 1){echo '<input id="optionsRadios1" type="radio" checked value="1" name="account_status">';}
							else{echo '<input id="optionsRadios1" type="radio" value="1" name="account_status">';}
							?>
                            Yes
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                        	<?php
                            if($account_status == 0){echo '<input id="optionsRadios1" type="radio" checked value="0" name="account_status">';}
							else{echo '<input id="optionsRadios1" type="radio" value="0" name="account_status">';}
							?>
                            No
                        </label>
                    </div>
                </div>
            </div>
                <div class="form-actions center-align">
                    <button class="submit btn btn-primary btn-sm" type="submit">
                        Edit accounts
                    </button>
                </div>
            </div>
        <?php echo form_close();?>
    </div>
    </section>