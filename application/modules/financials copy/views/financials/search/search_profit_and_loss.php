<section class="panel">
		<header class="panel-heading">
				<h5 class="pull-left"><i class="icon-reorder"></i>Search </h5>
				<div class="clearfix"></div>
		</header>
		<!-- /.box-header -->
		<div class="panel-body">
			<div class="row">
	<?php
    echo form_open("financials/company_financial/search_income_statement", array("class" => "form-horizontal"));
    ?>

        <div class="col-md-3">
            <div class="form-group">
                <label class="col-md-4 control-label">Date From: </label>

                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Visit Date" value="" autocomplete="off">
                    </div>
                </div>
            </div>
            <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">


        </div>
        <div class="col-md-3">
          <div class="form-group">
              <label class="col-md-4 control-label">Date To: </label>

              <div class="col-md-8">
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                      </span>
                      <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to" placeholder="Visit Date" value="" autocomplete="off">
                  </div>
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



    <?php
    echo form_close();
    ?>
		<div class="col-md-3">
			 <div class="form-group">
						<div class="col-lg-8 col-lg-offset-4">
							<div class="center-align">
									<a href="<?php echo site_url().'print-income-statement'?>" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i> Print Income Statement</a>
				</div>
						</div>
				</div>
		</div>
		</div>
  </div>
</section>
