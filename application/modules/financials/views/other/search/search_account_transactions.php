<div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Search</h3>

      <div class="box-tools pull-right">
         
      </div>
    </div>
    <div class="box-body">
	<?php
    echo form_open("search-tenants", array("class" => "form-horizontal"));
    ?>
    <div class="row">
    	<div class="col-md-3">
            
            <div class="form-group">
                <label class="col-lg-4 control-label">Account: </label>
               							            
	            <div class="col-lg-8">
	            	<select class="form-control select2">
	            		<option>-----select an account -----</option>
	            	</select>
	            </div>
            </div>
            
           
            
        </div>
        <div class="col-md-3">
            
            <div class="form-group">
                <label class="col-lg-4 control-label">Date From: </label>
               							            
	            <div class="col-lg-8">
	            	<input type="text" class="form-control" name="tenant_name" placeholder="Date From" value="<?php echo date("Y-01-01")?>" id="datepicker">
	            </div>
            </div>
            
           
            
        </div>
        <div class="col-md-3">
             <div class="form-group">
                <label class="col-lg-4 control-label">Date To: </label>
                
                <div class="col-lg-8">
                	<input type="text" class="form-control" name="tenant_phone_number" placeholder="Date To" value="<?php echo date("Y-m-d")?>" id="datepicker2">
                </div>
            </div>
            
        </div>
        <div class="col-md-3">
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
</div>