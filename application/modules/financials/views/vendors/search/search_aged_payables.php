<section class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Search </h3>
    </header>
    <div class="panel-body">
	<?php
    echo form_open("search-tenants", array("class" => "form-horizontal"));
    ?>
    <div class="row">
        <div class="col-md-6">

            <div class="form-group">
                <label class="col-lg-4 control-label">Report Date: </label>

	            <div class="col-lg-8">
	            	<input type="text" class="form-control" name="tenant_name" placeholder="Date From" value="<?php echo date("Y-01-01")?>" id="datepicker">
	            </div>
            </div>



        </div>

        <div class="col-md-6">
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
