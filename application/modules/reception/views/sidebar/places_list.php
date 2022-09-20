<?php


$result = '';
if($places->num_rows() > 0)
{
	$count=0;
    $places = $places->result();
    
    foreach($places as $res)
    {
        $place_id1 = $res->place_id;
        $place_name = $res->place_name;
        $branch_id = $res->branch_id;
        $count++;
        if($branch_id > 0)
        {
        	$button ='<a class="btn btn-sm btn-danger" onclick="delete_place('.$place_id1.')"><i class="fa fa-trash"></i></a>';

        }
        else
        {
        	$button = '';
        }
        $result .= '<tr>
        				<td>'.$count.'</td>
        				<td>'.$place_name.'</td>
        				<td>'.$button.'</td>
        			</tr>';
       
    }
}
                     
?>
<table class="table table-bordered table-condensed">
	<thead>
		<th></th>
		<th>Name</th>
		<th>Action</th>
	</thead>
	<tbody>
		<?php echo $result;?>
	</tbody>
</table>