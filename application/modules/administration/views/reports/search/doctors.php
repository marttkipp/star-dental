
    <!-- Widget -->
    <section class="panel">


        <!-- Widget head -->
        <header class="panel-heading">
			<h2 class="panel-title"><?php echo $title;?></h2>
        </header>             

        <!-- Widget content -->
        <div class="panel-body">
            <?php
            echo form_open("administration/reports/search_doctors", array("class" => "form-horizontal"));
            ?>
            <div class="row">

                <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visit Date From: </label>
                            
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_from" placeholder="visit date from">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Visit Date To: </label>
                            
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_to" placeholder="visit date to">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="center-align">
                            <button type="submit" class="btn btn-info btn-sm">Search</button>
                        </div>
                    </div>
            </div>
          
            <?php
            echo form_close();
            ?>
    	 </div>
        </section>