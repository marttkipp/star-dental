
<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
        <h2 class="panel-title">Search</h2>
    </header>
      <div class="panel-body">
			<?php
            echo form_open("reception/search_patient_appointments/".$visit.'/'.$page_name, array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-6">
                  
                    
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Patient number: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">First name: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="surname" placeholder="First name">
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-6">
                    
                    
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Visit Date: </label>
                        
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date" placeholder="Visit Date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Other Names: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="othernames" placeholder="Other Names">
                        </div>
                    </div>
                </div>
                
                
            </div>
            <br>
            <div class="row">
                <div class="form-group center-align">
                    <div class="col-lg-12">
                        <div class="center-align">
                            <button type="submit" class="btn btn-info">Search</button>
                        </div>
                    </div>
                </div>
            </div>
            
           
            <?php
            echo form_close();
            ?>
          </div>
       

  </section>