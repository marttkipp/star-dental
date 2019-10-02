 
<link href="<?php echo base_url();?>assets/jasny/jasny-bootstrap.css" rel="stylesheet">
<script src="<?php echo base_url();?>assets/jasny/jasny-bootstrap.js"></script>
 <section class="panel">
            <header class="panel-heading">
              <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
              <div class="widget-icons pull-right">
                    <a href="<?php echo base_url();?>procurement/suppliers" class="btn btn-primary pull-right btn-sm">Back to suppliers</a>
              </div>
              <div class="clearfix"></div>
        </header>
        <div class="panel-body">
          
            
            <?php echo form_open("accounts/creditors/add_creditor", array("class" => "form-horizontal"));?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Creditor Name: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_name" placeholder="Creditor Name" >
                                </div>
                            </div>

                     <div class="form-group">
                        <label class="col-lg-4 control-label">Suppliers Category </label>
                            <div class="col-lg-8">
                                <select id="supply_category_id" name="supply_category_id" class="form-control">
                                    <option value="">--- None ---</option>
                                    <?php
                                    if($supply->num_rows() > 0)
                                    {   
                                        foreach($supply->result() as $row):
                                            // $company_name = $row->company_name;
                                            $supply_category_name = $row->supply_category_name;
                                            $supply_category_id = $row->supply_category_id;
                                            
                                            if($supply_category_id == set_value('supply_category_id'))
                                            {
                                                echo "<option value=".$supply_category_id." selected='selected'> ".$supply_category_name."</option>";
                                            }
                                            
                                            else
                                            {
                                                echo "<option value=".$supply_category_id."> ".$supply_category_name."</option>";
                                            }
                                        endforeach; 
                                    } 
                                    ?>
                                </select>
                            </div>
                      </div>
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Email: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_email" placeholder="Email" >
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Phone: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_phone" placeholder="Phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Opening Balance: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="opening_balance" placeholder="Opening Balance" >
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo site_url()?>procurement/suppliers">
                            <input type="hidden" class="form-control" name="creditor_type_id" placeholder="" autocomplete="off" value="1">
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
                            
                            
                        </div>
                        
                        <div class="col-md-6">
                            
                       
                            
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Contact First Name: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_contact_person_name" placeholder="Contact First Name" >
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Contact Other Names: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_contact_person_onames" placeholder="Contact Other Names" >
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-lg-5 control-label">Contact Phone 1: </label>
                                
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" name="creditor_phone" placeholder="Contact Phone 1" >
                                </div>
                            </div>
                            
                           
                            
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class='btn btn-info btn-sm' type='submit' >Add Supplier</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    
                </div>
                <?php echo form_close();?>
		</div>
    
</section>