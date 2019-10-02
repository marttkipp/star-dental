<section class="panel">
		<header class="panel-heading">
				<h5 class="pull-left"><i class="icon-reorder"></i>Search</h5>
				<div class="clearfix"></div>
		</header>
		<!-- /.box-header -->
		<div class="panel-body">
	<?php
    echo form_open("search-stock-report/".$report_id, array("class" => "form-horizontal"));
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
									<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from<?php echo $report_id?>" placeholder="Date From" value="" autocomplete="off">
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
											<input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to<?php echo $report_id?>" placeholder=" Date to" value="" autocomplete="off">
									</div>
							</div>
					</div>

        </div>
        <input type="hidden" name="redirect_url" value="<?php echo $this->uri->uri_string()?>">
        <div class="col-md-4">
        	<div class="col-md-6">
        	 <button type="submit" class="btn btn-info">Search</button>
        	</div>
        	<div class="col-md-4">
        		<?php 
        		if(isset($category_id))
        		{
        			?>
        			 <a href="<?php echo site_url().'export-report/'.$report_id.'/'.$category_id?>" class="btn btn-success"><i class="fa fa-pdf-o"></i> Export</a>
        			<?php
        		}
        		else
        		{
        			?>
        			 <a href="<?php echo site_url().'export-report/'.$report_id?>" target="_blank" class="btn btn-success"><i class="fa fa-pdf-o"></i> Export</a>
        			<?php
        		}
        		?>
        	
        	</div>
        </div>
    </div>


    <?php
    echo form_close();
    ?>
  </div>
</section>
