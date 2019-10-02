<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Patient Treatment Search</h2>
    </header>
    <div class="panel-body">
    
          <div class="padd">
			<?php
			
			
			echo form_open("nurse/search_patient_treatment_statement/".$module, array("class" => "form-horizontal"));
			
            
            ?>
            <div class="row">
                <div class="col-md-6">
                   
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Patient Number: </label>
                        
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient Number">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                   
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Name: </label>
                        
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="surname" placeholder="Name">
                        </div>
                    </div>
                </div>
                
            </div>
            <br>
            <div class="center-align">
            	<button type="submit" class="btn btn-info btn-sm">Search</button>
            </div>
            <?php
            echo form_close();
            ?>
    	</div>
    </div>
</section>
