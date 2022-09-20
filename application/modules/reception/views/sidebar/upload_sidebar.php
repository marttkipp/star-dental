<div class="panel-body">
	
	<div class="col-md-6"> 

		<?php echo form_open_multipart('radiology/xray/upload_documents/'.$patient_id, array("class" => "form-horizontal", "role" => "form", "id" => "add_result_upload"));?>
				<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id?>">
           
            <div class="form-group">
                <label class="col-lg-12 ">Document Scan * (Multiple Images): </label>
                
                <div class="col-lg-12">
                    <input type="file" class="form-control " name="gallery[]"  value="" multiple>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-12 ">Document Description: </label>
                
                <div class="col-lg-12">
                    <textarea name="document_description" id="document_description" class="form-control" placeholder="Document Description" rows="5"></textarea>
                </div>
            </div>

            <div class="row" style="margin-top:10px;">
		        <div class="col-md-12">
		            <div class="form-actions center-align">
		                <button class="submit btn btn-primary" type="submit">
		                    Upload Scans
		                </button>
		            </div>
		        </div>
		    </div>
		<?php echo form_close();?>


		
	</div>
	<div class="col-md-6">
		<div id="uploads-view"></div>
	</div>
</div>

<div class="row" style="margin-top: 5px;">
	<ul>
		<li style="margin-bottom: 5px;">
			<div class="row">
		        <div class="col-md-12 center-align">
			        <a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
		        </div>
		    </div>
			
		</li>
	</ul>
</div>