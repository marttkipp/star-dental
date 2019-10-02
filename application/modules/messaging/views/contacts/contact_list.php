<!-- search -->
<?php //echo $this->load->view('search/search_contacts', '', TRUE);?>
<!-- end search -->
<?php
$result = form_open('bulk-delete-contacts/'.$page);

//if users exist display them
if ($query->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
		'
			<table class="table table-hover table-bordered ">
			  <thead>
				<tr>
				  <th></th>
				  <th>#</th>
				  <th>Name</th>
				  <th>Phone</th>
				  <th>Balance</th>
				  <th colspan="2"></th>
				</tr>
			  </thead>
			  <tbody>
		';
	
	foreach ($query->result() as $row)
	{
		

		$entryid = $row->entryid;
		$Name = $row->name;
		$Phonenumber = $row->Phonenumber;
		$balance = $row->balance;
		// $authorise = $row->authorise;
		

		// if($authorise == 1)
		// {
		// 	$buttons = '<a href="'.site_url().'/administration/personnel/activate_personnel/'.$entryid.'" class="btn btn-sm btn-success"onclick="return confirm(\'Do you want to activate '.$Name.' account?\');">Activate account</a>';
		// }
		// else if($authorise == 0)
		// {
		// 	$buttons = '<a href="'.site_url().'/administration/personnel/deactivate_personnel/'.$entryid.'" class="btn btn-sm btn-danger"onclick="return confirm(\'Do you want to deactivate '.$Name.' account?\');">Deactivate account</a>';
		// }
		// else
		// {
		// 	$buttons = '';
		// }
		$checkbox_data = array(
								  'name'        => 'contacts[]',
								  'id'          => 'checkbox'.$entryid,
								  'class'          => 'css-checkbox lrg',
								  'value'       => $entryid
								);
		
		$count++;
		$result .= 
			'
				<tr>
					<td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$entryid.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
					<td>'.$count.'</td>
					<td>'.$Name.'</td>
					<td>'.$Phonenumber.'</td>
					<td>'.$balance.'</td>
					<td><a href="'.site_url().'administration/contacts/edit_contact/'.$entryid.'" class="btn btn-sm btn-primary" title="Edit '.$Name.'"><i class="fa fa-pencil"></i></a></td>
					<td><a href="'.site_url().'delete-contact/'.$entryid.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$Name.'?\');" title="Delete '.$Name.'"><i class="fa fa-trash"></i></a></td>

					
				</tr> 
			';
	}
	
	$result .= 
	'
				  </tbody>
				</table>
	';
	
}

else
{
	$result .= "There are no contacts";
}
$result .= '
			<br>
			<div class="center-align">
				<button type="submit" class="btn btn-sm btn-danger">Delete Selected Contacts</button>
			</div>
			'.form_close();

?>

<section class="panel">
	<header class="panel-heading">						
		<h2 class="panel-title"><?php echo $title;?></h2>
		  <button type="button" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#import_rental_list"  style="margin-top:-25px"><i class="fa fa-upload"></i> Import Contacts</button>
		<a href="<?php echo site_url();?>/add-contact" class="btn btn-sm btn-info pull-right" style="margin-top:-25px"><i class="fa fa-plus"></i> Add Contact</a> 
		<a href="<?php echo site_url();?>messaging/contacts/update_records" class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;margin-right:5px;"><i class="fa fa-plus"></i> Update Records</a> 
	</header>
	<div class="panel-body">
		<?php
	    $success = $this->session->userdata('success_message');

		if(!empty($success)){

					echo '<div class="alert alert-success"> <strong>Success!</strong> '.$success.' </div>';
			$this->session->unset_userdata('success_message');
		}
		
		$error = $this->session->userdata('error_message');
		
		if(!empty($error))
		{
			echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
			$this->session->unset_userdata('error_message');
		}
		?>
		<div class="row" style="margin-bottom:20px;">
	        <!--<div class="col-lg-2 col-lg-offset-8">
	            <a href="<?php echo site_url();?>human-resource/export-personnel" class="btn btn-sm btn-success pull-right">Export</a>
	        </div>-->
	        <div class="col-lg-12">
	        	    <!-- Modal -->
            <div class="modal fade" id="import_rental_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Import Rental List </h4>
                        </div>
                        <div class="modal-body">
                            <?php echo form_open_multipart("contacts/validate-import/1", array("class" => "form-horizontal","role" => "form"));?>
                           
                             <div class="alert alert-info">
				            	Please ensure that you have the following data ready to transfer into the import templage:
				                <ol>
				                    <li>Name</li>
				                    <li>Phone Number</li>
				                    <li>Balance</li>
				                </ol>
				            </div>
				            <div class="row">
				                <div class="col-md-12">
				                    <ul>
				                        <li>Download the import template <a href="<?php echo site_url().'contacts/import-template';?>">here.</a></li>				                        
				                        <li>Save your file as a <strong>CSV (Comma Delimited)</strong> file before importing</li>
				                        <li>After adding your contacts to the import template please import them using the button below</li>
				                    </ul>
				                </div>
				            </div>
				            
				            <div class="row">
				                <div class="col-md-12">
				                    
				                    <div class="fileUpload btn btn-primary">
				                        <span>Import contact List</span>
				                        <input type="file" class="upload" onChange="this.form.submit();" name="import_csv" />
				                    </div>
				                </div>
				            </div>
                            <?php echo form_close();?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- MODAL END -->
	        </div>
	    </div>
		<div class="table-responsive">
	    	
			<?php echo $result;?>

	    </div>
	</div>
	<div class="panel-footer">
		<?php if(isset($links)){echo $links;}?>
	</div>
</section>