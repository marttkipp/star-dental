<?php

$rs2 = $this->dental_model->get_visit_lab_work($visit_id);


echo "
<br/>
<table align='center' class='table table-striped table-hover table-condensed'>
	<tr>
		
		<th>Lab Work</th>
		<th></th>
	</tr>		
";                     
		$total= 0;  
		if(count($rs2) >0){
			foreach ($rs2 as $key1):
				$visit_lab_work_id = $key1->visit_lab_work_id;
				$visit_lab_work = $key1->lab_work_done;
			
				
				echo"
						<tr> 
							<td style='width:90%'>".$visit_lab_work."</td>							
							<td>
								<a class='btn btn-sm btn-danger' href='#' onclick='delete_lab_work(".$visit_lab_work_id.", ".$visit_id.")'><i class='fa fa-trash'></i></a>
							</td>
						</tr>	
				";
				endforeach;

		}
echo"
 </table>
";

?>