        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                <h2 class="panel-title pull-right">Active branch: <?php echo $branch_name;?></h2>
                <h2 class="panel-title">Search</h2>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
            <?php
            echo form_open("search-all-debtors-report", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Patient Phone: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="patient_phone" placeholder="Patient phone">
                        </div>
                    </div>

                </div>
                
                <div class="col-md-4">                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Patient Name: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="patient_name" placeholder="Patient Name">
                        </div>
                    </div>
                    
                </div>
                
                <div class="col-md-3">
                    <!--   -->
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Patient number: </label>
                        
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="patient_number" placeholder="Patient number">
                        </div>
                    </div>
                </div> 
                <div class="col-md-1"> 
                    
                    <div class="form-group">
                        <div class="col-lg-8 col-lg-offset-4">
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