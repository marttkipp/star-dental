
<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
        <h2 class="panel-title">Search</h2>
    </header>
      <div class="panel-body">
			<?php
            echo form_open("reports/search_appointment_report_search", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-3">
                  
                    <div class="form-group">
                        <label class="col-md-4 control-label">Names: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_name" placeholder="Patient Name">
                        </div>
                    </div>
                    <div class="form-group">
						<label class="col-lg-3 control-label">Report Type? </label>
			            <div class="col-lg-3">
			                <div class="radio">
			                    <label>
			                        <input id="optionsRadios1" type="radio" name="close_card" value="0" checked="checked" >
			                        ALL
			                    </label>
			                </div>
			            </div>
			            
			            <div class="col-lg-3">
			                <div class="radio">
			                    <label>
			                        <input id="optionsRadios1" type="radio" name="close_card" value="1">
			                        Showed
			                    </label>
			                </div>
			            </div>
			            <div class="col-lg-3">
			                <div class="radio">
			                    <label>
			                        <input id="optionsRadios1" type="radio" name="close_card" value="2">
			                        No Showed
			                    </label>
			                </div>
			            </div>
					</div>
                  
                    
                </div>
                
                <div class="col-md-3">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Date From: </label>
                        
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="Visit Date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">  
                     <div class="form-group">
                        <label class="col-md-4 control-label">Date To: </label>
                        
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="Visit Date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class="col-md-3">
                	 <div class="form-group center-align">
	                    <div class="col-lg-12">
	                        <div class="center-align">
	                            <button type="submit" class="btn btn-info">Search</button>
	                        </div>
	                    </div>
	                </div>
                </div>
                
                
            </div>
            
           
            <?php
            echo form_close();
            ?>
          </div>
       

  </section>