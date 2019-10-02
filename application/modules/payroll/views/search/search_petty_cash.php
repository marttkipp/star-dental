 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Search</h2>
    </header>
    
    <!-- Widget content -->
    <div class="panel-body">
        <div class="padd">
            <?php
            echo form_open("accounts/petty_cash/search_petty_cash", array("class" => "form-horizontal"));
            ?>
            <div class="row">
            	 <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Account: </label>
                        <div class="col-md-8">
                            <select class="form-control" name="account_id">
                                <option value="">-- Select account --</option>
                                <?php
                                if($accounts->num_rows() > 0)
                                {
                                    foreach($accounts->result() as $res)
                                    {
                                        $account_id = $res->account_id;
                                        $account_name = $res->account_name;
                                        ?>
                                        <option value="<?php echo $account_id;?>"><?php echo $account_name;?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Date from: </label>
                        
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" id='datetimepicker1' data-plugin-datepicker class="form-control" name="date_from" placeholder="Date from">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Date to: </label>
                        
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to" placeholder="Date to">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="center-align">
                <button type="submit" class="btn btn-info btn-sm">Search Account</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</section>