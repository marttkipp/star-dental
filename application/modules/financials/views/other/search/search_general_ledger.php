<section class="panel">
		<header class="panel-heading">
				<h5 class="pull-left"><i class="icon-reorder"></i>Search</h5>
				<div class="clearfix"></div>
		</header>
		<!-- /.box-header -->
		<div class="panel-body">
	<?php
    echo form_open("search-tenants", array("class" => "form-horizontal"));
    ?>
    <div class="row">
        <div class="col-md-4">

					<div class="form-group">
							<label class="col-md-4 control-label">Date From: </label>

							<div class="col-md-8">
									<div class="input-group">
											<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
											</span>
											<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Date From" value="" autocomplete="off">
									</div>
							</div>
					</div>
        </div>
        <div class="col-md-4">
					<div class="form-group">
							<label class="col-md-4 control-label">Date To: </label>

							<div class="col-md-8">
									<div class="input-group">
											<span class="input-group-addon">
													<i class="fa fa-calendar"></i>
											</span>
											<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to" placeholder=" Date to" value="" autocomplete="off">
									</div>
							</div>
					</div>

        </div>
        <div class="col-md-4">
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
