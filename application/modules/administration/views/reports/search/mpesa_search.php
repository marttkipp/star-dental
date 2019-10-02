
    <!-- Widget -->
    <section class="panel">


        <!-- Widget head -->
        <header class="panel-heading">
			<h2 class="panel-title"><?php echo $title;?></h2>
        </header>             

        <!-- Widget content -->
        <div class="panel-body">
            <?php
            echo form_open("administration/reports/search_mpesa", array("class" => "form-horizontal"));
            ?>
            <div class="row">
                 <div class="col-md-6">
                    
                 <div class="form-group">
                    <label class="col-md-4 control-label">MPESA Payments From :</label>
            
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payments_from" placeholder="MPESA Payments From">
                        </div>
                    </div>
                </div>
                </div>
                
                 <div class="col-md-6">
                    
                    <label class="col-md-4 control-label">MPESA Payments To :</label>
            
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payments_to" placeholder="MPESA Payments To">
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
        </section>