<section class="panel">
	
	<header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="">
        	 <a href="<?php echo site_url();?>admin/sections/update_all_sequences" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px;" onclick="confirm('Are you sure you want to update all the sequences ? ')">Update Section sequence</a>
	         <a href="<?php echo site_url();?>administration/add-section" class="btn btn-sm btn-success pull-right" style="margin-top: -25px;">Add Section</a>
	    </div>
    </header>

	<div class="panel-body" >
	
    	<?php
        $success = $this->session->userdata('success_message');

		if(!empty($success))
		{
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
    	
		<div class="table-responsive" style="height:80vh;overflow-y:scroll;">
        	<table class="table table-bordered table-condensed table-striped table-linked">

				<thead>
					<tr>
						
						<th>Section name</th>
						<th>Date Created</th>
						<th>Last modified</th>
						<th>Status</th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
        		<?php


					$parents = $this->sections_model->all_parent_sections('section_position');
						

					$sections = '';

					$arrTree = array();
					$arrParents = array();
						if($parents->num_rows() > 0)
						{

							foreach($parents->result() as $res)
							{

								$section_parent = $res->section_parent;
								$section_id = $res->section_id;
								$section_name = $res->section_name;
								$section_icon = $res->section_icon;


								//Seperate the Adam and Eves
								if($section_parent == 0 and !in_array($section_id,$arrParents))
									array_push($arrParents, $section_id);

								//Load all children
								if(!array_key_exists($section_id, $arrTree))
									$arrTree[$section_id] = array("name" => $section_name, "children" => array());
								else if(strlen($arrTree[$section_id]["name"]) == 0)
									$arrTree[$section_id]["name"] = $section_name;

								if($section_parent > 0 and !array_key_exists($section_parent, $arrTree))
									$arrTree[$section_parent] = array("name" => "", "children" => array());

								if($section_parent > 0)
									if(!in_array($section_id, $arrTree[$section_parent]["children"]))
										array_push($arrTree[$section_parent]["children"], $section_id);

							}
						
						foreach($arrParents as $parent)

							
							$this->sections_model->printTree_old(array("tree" => $arrTree, "me" => $parent, "level" => "-"));

						
						
					}

					?>
				</tbody>
        	</table>
			
	
        </div>
	</div>
	
</section>

<script type="text/javascript">
	function update_section_position(section_id)
	{
		 var config_url = $('#config_url').val();


		var section_position = $('#section_position'+section_id).val();
                    
        var url = config_url+"admin/sections/update_sections_position/"+section_id;
    
        $.ajax({
        type:'POST',
        url: url,
        data:{section_position: section_position},
        dataType: 'text',
        success:function(data){
          var data = jQuery.parseJSON(data);
            
           
         

        },
        error: function(xhr, status, error) {
        alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

        }
        });
	}
</script>