
<section class="panel panel-featured panel-featured-info">
    <header class="panel-heading">
        <h2 class="panel-title">Search</h2>
    </header>
      <div class="panel-body">
			<?php
            echo form_open("search-patient-files", array("class" => "form-horizontal"));
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
                        <label class="col-md-4 control-label">I.D. No.: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_national_id" placeholder="I.D. No.">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">First name: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="surname" placeholder="First name">
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
            <div class="row" style="margin-top: 20px;">
            	<div class="col-md-12">
            		 <div class="form-group">
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