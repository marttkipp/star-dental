<?php

$result = '';
if($query != null)
{
if($query->num_rows() > 0)
{
	foreach ($query->result() as $key => $value) {
		# code...
		$patient_id = $value->patient_id;
		$patient_name = $value->patient_surname.' '.$value->patient_othernames;
		$patient_number = $value->patient_number;
		$patient_phone1 = $value->patient_phone1;

		$result .='<li class="status-online">
				    <a onclick="add_lab_test('.$patient_id.',1,'.$appointment_id.');">
				      <input type="checkbox" name="" class="menu-icon " >
				      <span class="name">'.$patient_name.' - '.$patient_number.' - '.$patient_phone1.'</span>
				    
				    </a>
				  </li>';
	}

	
}
else
{
	$result .='<li class="status-online">
				    <span class="name">No patients with that name</span>
				  </li>';
}

}
else
{
	$result .='<li class="status-online">
				    <span class="name">Kindly search using name , file number or phone number</span>
				  </li>';
}

echo $result;

?>