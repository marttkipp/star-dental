 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Search Patients</h2>
    </header>
    
    <!-- Widget content -->
   <div class="panel-body">
    	<div class="padd">
			<?php
            echo form_open("reception/search_patients", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-4">
                   
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Patient number: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient number">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                   
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Patient Phone: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="patient_phone" placeholder="Phone">
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Name: </label>
                        
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="surname" placeholder="Name">
                        </div>
                    </div>
                    
                   
                    
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="center-align">
                        <button type="submit" class="btn btn-info btn-sm">Search</button>
                    </div>
                </div>
            </div>
            <?php
            echo form_close();
            ?>
    	</div>
    </div>
</section>