<table class="table table-hover table-bordered col-md-12">			
	<tbody>
		<?php
		// var_dump()

		// $count_visit = $visit_list->num_rows();
		$num_pages = $total_rows/$per_page;

		if($num_pages < 1)
		{
			$num_pages = 0;
		}
		$num_pages = round($num_pages);

		if($page==0)
		{
			$counted = 0;
		}
		else if($page > 0)
		{
			$counted = $per_page*$page;
		}

		if($visit_list->num_rows() > 0)
		{
			foreach ($visit_list->result() as $key => $value) {
				# code...
				$visit_idd = $value->visit_id;
				$inpatient_visit = $value->inpatient;
				$visit_date_date = $value->visit_date;

				if($inpatient_visit == 1)
				{
					$visit_type = 'Admission Visit';
				}
				else
				{
					$visit_type = 'Clinic Visit';
				}
				$counted++;
				echo "<tr onclick='get_visit_detail(".$visit_idd.")'>
						<td>".$counted."</td><td>".$visit_date_date."</td><td>".$visit_type."</td>
					</tr>";
			}
		}
		?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12" style="padding-right: 25px;">
		<div class="pull-right">
			<?php
				$link ='<ul style="list-style:none;">';
				// echo $page;
				if($num_pages > $page)
				{
					// echo "now ".$num_pages." ".$page;
					$last_page = $num_pages -1;

					if($page > 0 AND $page < $last_page)
					{
						// echo $page;
						$page++;
						// echo "now".$page;
						$previous = $page -2;
						$link .='<li onclick="get_next_page('.$previous.','.$visit_id.')" class="pull-left" style="margin-right:20px;" > <i class="fa fa-angle-left"></i> Back</li>  <li onclick="get_next_page('.$page.','.$visit_id.')" class="pull-right"> Next <i class="fa fa-angle-right"></i> </li>';
					}else if($page == $last_page)
					{
						$page++;

						$previous = $page -2;
						// echo "equal".$num_pages." ".$page;
						$link .='<li onclick="get_next_page('.$previous.','.$visit_id.')" class="pull-left"> <i class="fa fa-angle-left"></i> Back</li>';
					}
					else
					{
						$page++;
						$link .='<li onclick="get_next_page('.$page.','.$visit_id.')" class="pull-right"> Next <i class="fa fa-angle-right"></i> </li>';
					}
					// var_dump($link); die();
				}
				$link .='</ul>';
				echo $link;
				
			?>
		</div>
	</div>
</div>
