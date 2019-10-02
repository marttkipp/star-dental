<section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    </div>
            
                    <h2 class="panel-title"><?php echo $title;?></h2>
                </header>
                <div class="panel-body">
                	<div class="row" style="margin-bottom:20px;">
                        <div class="col-lg-12">
                            <a href="<?php echo site_url();?>asset-registry/asset-category" class="btn btn-info pull-right">Back to Asset Category</a>
                        </div>
                    </div>
                <!-- Adding Errors -->
            <?php
            if(isset($error)){
                echo '<div class="alert alert-danger"> Oh snap! '.$error.' </div>';
            }
			
			//the visit_type details
			$asset_category_name = $asset_category[0]->asset_category_name;
			$asset_category_status = $asset_category[0]->asset_category_status;
			$asset_category_id2 = $asset_category[0]->asset_category_id;
            
            $validation_errors = validation_errors();
            
            if(!empty($validation_errors))
            {
				$asset_category_name= set_value('asset_category_name');
				$asset_category_status = set_value('asset_category_status');
				$asset_category_id2 = set_value('asset_category_id');
				
                echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
            }
			
            ?>
            
            <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Edit category:</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="asset_category_name" placeholder="asset category" value="<?php echo $asset_category_name;?>" required>
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="form-actions center-align" style="margin-top:10px;">
                <button class="submit btn btn-primary" type="submit">
                    Edit Asset category
                </button>
            </div>
            <br />
            <?php echo form_close();?>
                </div>
            </section>