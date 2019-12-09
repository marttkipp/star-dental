<!-- Widget -->
<div class="widget boxed">
    <!-- Widget head -->
    <div class="widget-head">
        <h4 class="pull-left"><i class="icon-reorder"></i>Search Service Charge</h4>
        <div class="widget-icons pull-right">
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
            <a href="#" class="wclose"><i class="icon-remove"></i></a>
        </div>
    
        <div class="clearfix"></div>
    
    </div>             
    
    <!-- Widget content -->
    <div class="widget-content">
        <div class="padd">
            
            <?php echo form_open("administration/expense_search/".$module."", array("class" => "form-horizontal"));?>
              <div class="row">
                    <div class="col-md-4">
                          <div class="form-group">
                              <label class="col-lg-4 control-label">Expense name</label>
                              <div class="col-lg-8">
                                  <input type="text" class="form-control" name="expense_name" placeholder="Expense  Name" value="">
                              </div>
                          </div>
                           
                           
                    
                    
                         
                    </div>
                     <div class="col-md-4">
							<div class="form-group">
								<label class="col-lg-4 control-label">Expense From: </label>
								
								<div class="col-lg-8">
									<div id="datetimepicker1" class="input-append">
										<input data-format="yyyy-MM-dd" class="form-control" type="text" name="expense_date_from" placeholder="Expense Date From">
										<span class="add-on" style="cursor:pointer;">
											&nbsp;<i data-time-icon="icon-time" data-date-icon="icon-calendar">
											</i>
										</span>
									</div>
								</div>
							</div>
							
                    </div>
					<div class="col-md-4">
							<div class="form-group">
								<label class="col-lg-4 control-label">Expense To: </label>
								
								<div class="col-lg-8">
									<div id="datetimepicker_other_patient" class="input-append">
										<input data-format="yyyy-MM-dd" class="form-control" type="text" name="expense_date_to" placeholder="Expense Date To">
										<span class="add-on" style="cursor:pointer;">
											&nbsp;<i data-time-icon="icon-time" data-date-icon="icon-calendar">
											</i>
										</span>
									</div>
								</div>
							</div>
					</div>
                     
                </div>
            
            <div class="center-align">
                <button type="submit" class="btn btn-info btn-lg">Search</button>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>