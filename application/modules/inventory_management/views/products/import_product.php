
<div class="row">
    <div class="col-md-12">
		<section class="panel panel-featured panel-featured-info">
		    <header class="panel-heading">
				<a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-info pull-right">Back to products</a>
		        <h2 class="panel-title"><?php echo $title;?></h2>
                
		    </header>
		    <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                <?php
                    if(isset($import_response))
                    {
                        if(!empty($import_response))
                        {
                            echo $import_response;
                        }
                    }
                    
                    if(isset($import_response_error))
                    {
                        if(!empty($import_response_error))
                        {
                            echo '<div class="center-align alert alert-danger">'.$import_response_error.'</div>';
                        }
                    }
                ?>
                    </div>
                </div>
                <?php echo form_open_multipart('inventory_management/products/do_products_import', array("class" => "form-horizontal", "role" => "form"));?>

                <div class="row">
                    <div class="col-md-12">
                        <ul>
                            <li>Download the import template <a href="<?php echo site_url().'inventory_management/products/import_template';?>">here.</a></li>
                            <!--<li>Please categorise your products <strong>ONLY</strong> according to one of the categories <a href="<?php echo site_url().'vendor/import-categories';?>">here.</a></li>-->
                            <li>Save your file as a <strong>CSV (Comma Delimited)</strong> file before importing</li>
                            <li>After adding your products to the import template please import them using the button below</li>
                        </ul>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        /*$data = array(
                              'class'       => 'custom-file-input btn-red btn-width',
                              'name'        => 'import_csv',
                              'onchange'    => 'this.form.submit();',
                              'type'       	=> 'file'
                            );
                    
                        echo form_input($data);*/
                        ?>
                        <div class="fileUpload btn btn-primary">
                            <span>Import products</span>
                            <input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
                        </div>
                    </div>
                </div>
                <?php echo form_close();?>
			</div>
			
		</section>
	</div>
</div>