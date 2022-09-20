<?php
	$personnel_id = $this->session->userdata('personnel_id');
	// var_dump($personnel_id);die();
	if($personnel_id == 0)
	{
		$parents = $this->sections_model->all_parent_sections('section_position');
	}
	
	else
	{
		$personnel_roles = $this->sections_model->get_personnel_roles($personnel_id);
		
		$parents = $personnel_roles;
	}

// var_dump($parents->result());die();
$sections = '';

$arrTree = array();
$arrParents = array();
$arrOrphans = array();

	if($parents->num_rows() > 0)
	{

		foreach($parents->result() as $res)
		{

			$section_parent = $res->section_parent;
			$section_id = $res->section_id;
			$section_name = $res->section_name;
			$section_icon = $res->section_icon;
			$section_sequence = $res->section_sequence;


			//Seperate the Adam and Eves
			if($section_parent == 0 and !in_array($section_id,$arrParents))
				array_push($arrParents, $section_id);

			//Load all children
			if(!array_key_exists($section_id, $arrTree))
				$arrTree[$section_id] = array("name" => $section_name, "children" => array());
			else if(strlen($arrTree[$section_id]["name"]) == 0)
				$arrTree[$section_id]["name"] = $section_name;

			if($section_parent > 0 and !array_key_exists($section_parent, $arrTree)){
				//Let's skip this
				//$arrTree[$section_parent] = array("name" => "err","children" => array());
				$arrOrphans[$section_id] = $section_sequence;
			}

			else if($section_parent > 0)
				if(!in_array($section_id, $arrTree[$section_parent]["children"]))
					array_push($arrTree[$section_parent]["children"], $section_id);

		}


		// if(!empty($arrOrphanIds))
		// {



		//Load any orphan's parent's details if the parent was not loaded
		// $strOrphans = implode(",", $arrOrphanIds);
		$arrBranches = array();
		foreach($arrOrphans as $serial){
			$arrSerial = explode(".", $serial);
			$new_serial = "";
			foreach($arrSerial as $part){
				$new_serial .= (strlen($new_serial)>0?".":"") . $part;

				if(!in_array($new_serial, $arrBranches))
					array_push($arrBranches, $new_serial);
			}
		}

		// echo "<pre>";
		// echo "<br>Getting the Orphans";
		// echo json_encode($arrOrphans, JSON_PRETTY_PRINT);

		// echo "<br>Getting the Branches";
		// echo json_encode($arrBranches, JSON_PRETTY_PRINT);
		// echo "<br>Current Tree";
		// echo json_encode($arrTree, JSON_PRETTY_PRINT);
		// echo "</pre>";


		//WHERE tchildren.section_id in ($strOrphans) 
		// AND tchildren.section_id in (".implode(",", array_keys($arrOrphans)).")
		$sql = "SELECT tparents.section_sequence AS parent_sequence, tparents.section_id AS parent_id,tparents.section_name AS parent_name, tparents.section_parent AS grandparent,
					 tchildren.section_sequence AS child_sequence, tchildren.section_id as child_id, tchildren.section_name as child_name,tparents.section_icon
		 		FROM section tparents
		  		INNER JOIN section tchildren ON tchildren.section_parent = tparents.section_id
		   		WHERE tparents.section_sequence in ('".implode("','", $arrBranches)."') 
		   		AND tparents.section_status = 1  ORDER BY tparents.section_id ASC";
		// echo "<br>Searching for Orphans' parents: $sql";
		$queryOrphans = $this->db->query($sql);


		if($queryOrphans->num_rows() > 0)
		{
			$arrChecked = array();
			foreach ($queryOrphans->result() as $key => $value) {
				// code...
				$parent_sequence = $value->parent_sequence;
				$child_sequence = $value->child_sequence;
				if(in_array($parent_sequence, $arrBranches) and in_array($child_sequence, $arrBranches)){
					// echo "<br>Checking parent $parent_sequence";
					$parent_id = $value->parent_id;
					$child_id = $value->child_id;
					$parent_name = $value->parent_name;
					$section_icon = $value->section_icon;
					$child_name = $value->child_name;
					$grandparent = $value->grandparent;


					// echo "<br>Checking parent $parent_sequence-$parent_name and $child_id=$child_name";

					//if(!in_array($section_parent, $arrChecked)){
						// unset($arrTree[$section_parent]); // this one here ?. You're ight . th. Let me call you back in 2 minutes. ok
						// array_push($arrChecked, $section_parent); //I'm calling you on Whatsapp
					// }

					if($grandparent == 0 and !in_array($parent_id,$arrParents))
						array_push($arrParents, $parent_id);
					if(!array_key_exists($parent_id,$arrTree))
						$arrTree[$parent_id] = array("name" => $parent_name, "children" => array());
					
					else if(strlen($arrTree[$parent_id]["name"]) == 0)
						$arrTree[$parent_id]["name"] = $parent_name;

					
					if(!in_array($child_id, $arrTree[$parent_id]["children"]))
						array_push($arrTree[$parent_id]["children"], $child_id);
				}
				// else
					// echo "<br>Skipping parent $parent_sequence";
			}
			unset($arrChecked);
		}
				 // echo "<br>Search SQL: $sql";

	// }

		// ksort($arrTree);

		// echo "<pre>";

		// echo "Printing parents : ".json_encode($arrParents, JSON_PRETTY_PRINT);

		// echo json_encode($arrTree, JSON_PRETTY_PRINT);
		// // echo json_encode($arrOrphanIds, JSON_PRETTY_PRINT);

		// echo "</pre>";
		// unset($arrOrphans);
		// unset($arrOrphanIds);
	foreach($arrParents as $parent)

		$sections .=$this->admin_model->printTree(array("tree" => $arrTree, "me" => $parent, "level" => "-"));

	
	
}

	

	$page = explode("/",uri_string());
	$total = count($page);
	$section_title = ucfirst($page[0]);


	
	
?>	
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"crossorigin="anonymous"></script>



<script type="text/javascript">
//	window.addEventListener("resize", function() {
//		"use strict"; window.location.reload(); 
//	});


	document.addEventListener("DOMContentLoaded", function(){
        
		// alert("sasajhsak");
    	/////// Prevent closing from click inside dropdown
		document.querySelectorAll('.dropdown-menu').forEach(function(element){
			element.addEventListener('click', function (e) {
			  e.stopPropagation();
			});
		})



		// make it as accordion for smaller screens
		if (window.innerWidth < 992) {

			// close all inner dropdowns when parent is closed
			document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown){
				everydropdown.addEventListener('hidden.bs.dropdown', function () {
					// after dropdown is hidden, then find all submenus
					  this.querySelectorAll('.submenu').forEach(function(everysubmenu){
					  	// hide every submenu as well
					  	everysubmenu.style.display = 'none';
					  });
				})
			});
			
			document.querySelectorAll('.dropdown-menu a').forEach(function(element){
				element.addEventListener('click', function (e) {
		
				  	let nextEl = this.nextElementSibling;
				  	if(nextEl && nextEl.classList.contains('submenu')) {	
				  		// prevent opening link if link needs to open dropdown
				  		e.preventDefault();
				  		console.log(nextEl);
				  		if(nextEl.style.display == 'block'){
				  			nextEl.style.display = 'none';
				  		} else {
				  			nextEl.style.display = 'block';
				  		}

				  	}
				});
			})
		}
		// end if innerWidth

	}); 
	// DOMContentLoaded  end
</script>

<header class="page-header">
	<div id="navbar">    
	  	<nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
              <!-- <a class="navbar-brand" href="#">HR</a> -->
            </div>
            
           
                
                
		 <div class="collapse navbar-collapse" id="main_nav">
			<ul class="nav navbar-nav">
				<?php echo $sections;?>
	        	
	        </ul>
         </div>
						
           
        </nav>
	</div>
</header>