<div id="dental-formula"></div>
<div class="row">
	<div class="col-md-12" >
		<?php
			$one = $this->dental_model->get_dentine_value($patient_id,1);
			$two = $this->dental_model->get_dentine_value($patient_id,2);
			$three = $this->dental_model->get_dentine_value($patient_id,3);
			$four = $this->dental_model->get_dentine_value($patient_id,4);
			$five = $this->dental_model->get_dentine_value($patient_id,5);
			$six = $this->dental_model->get_dentine_value($patient_id,6);
			$seven = $this->dental_model->get_dentine_value($patient_id,7);
			$eight = $this->dental_model->get_dentine_value($patient_id,8);
			$nine = $this->dental_model->get_dentine_value($patient_id,9);
			$ten = $this->dental_model->get_dentine_value($patient_id,10);
			$eleven = $this->dental_model->get_dentine_value($patient_id,11);
			$twelve = $this->dental_model->get_dentine_value($patient_id,12);
			$thirteen = $this->dental_model->get_dentine_value($patient_id,13);
			$fourteen = $this->dental_model->get_dentine_value($patient_id,14);
			$fifteen = $this->dental_model->get_dentine_value($patient_id,15);
			$sixteen = $this->dental_model->get_dentine_value($patient_id,16);
			$seventeen = $this->dental_model->get_dentine_value($patient_id,17);
			$eighteen = $this->dental_model->get_dentine_value($patient_id,18);
			$nineteen = $this->dental_model->get_dentine_value($patient_id,19);
			$twenty = $this->dental_model->get_dentine_value($patient_id,20);
			$twenty_one = $this->dental_model->get_dentine_value($patient_id,21);
			$twenty_two = $this->dental_model->get_dentine_value($patient_id,22);
			$twenty_three = $this->dental_model->get_dentine_value($patient_id,23);
			$twenty_four = $this->dental_model->get_dentine_value($patient_id,24);
			$twenty_five = $this->dental_model->get_dentine_value($patient_id,25);
			$twenty_six = $this->dental_model->get_dentine_value($patient_id,26);
			$twenty_seven = $this->dental_model->get_dentine_value($patient_id,27);
			$twenty_eight = $this->dental_model->get_dentine_value($patient_id,28);
			$twenty_nine = $this->dental_model->get_dentine_value($patient_id,29);
			$thirty = $this->dental_model->get_dentine_value($patient_id,30);
			$thirty_one = $this->dental_model->get_dentine_value($patient_id,31);
			$thirty_two = $this->dental_model->get_dentine_value($patient_id,32);


		?>

		<div >
			<div class="col-lg-6 col-md-6 col-sm-6" style="border-left: 2px solid #000;border-bottom: 2px solid #000;">
			 	<div class="col-md-12">
			 		<h3 class="center-align">RIGHT</h3>
			 		<br>
			 		<table align='center' class='table table-striped table-condensed table-bordered'>
			 			<tr>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $one;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $two;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $three;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $four;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $five;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $six;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $seven;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;" ><?php echo $eight;?></td>
			 			</tr>
			 			<tr >
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(1)"> 8 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(2)"> 7 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(3)"> 6 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(4)"> 5 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(5)"> 4 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(6)"> 3 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(7)"> 2 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;" onclick="check_department_type(8)"> 1 </td>
			 				
			 			</tr>
			 		</table>
			 	</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6"  style="border-right: 2px solid #000; border-left: 2px solid #000;border-bottom: 2px solid #000; ">			
			 	<div class="col-md-12">
			 		<h3 class="center-align">LEFT</h3>
			 		<br>
			 		<table align='center' class='table table-striped table-condensed table-bordered'>
			 			<tr>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $nine;?> </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $ten;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $eleven;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twelve;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $thirteen;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $fourteen;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $fifteen;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $sixteen;?></td>
			 			</tr>
			 			<tr >
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(9)"> 1 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(10)"> 2 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(11)"> 3 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(12)"> 4 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(13)"> 5 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(14)"> 6 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(15)"> 7 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(16)"> 8 </td>
			 				
			 			</tr>
			 		</table>
			 	</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6" style="border-left: 2px solid #000;">
			 	<div class="col-md-12">
			 		<table align='center' class='table table-striped table-condensed table-bordered'>
			 			<tr >
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(17)"> 8 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(18)"> 7 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(19)"> 6 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(20)"> 5 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(21)"> 4 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(22)"> 3 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(23)"> 2 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(24)"> 1 </td>
			 				
			 			</tr>
			 			<tr>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $seventeen;?> </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $eighteen;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $nineteen;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_one;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_two;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_three;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_four;?></td>
			 			</tr>
			 			
			 		</table>
			 	</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6"  style="border-right: 2px solid #000; border-left: 2px solid #000; ">			
			 	<div class="col-md-12">
			 		<table align='center' class='table table-striped table-condensed table-bordered'>
			 			<tr >
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(25)"> 1 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(26)"> 2 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(27)"> 3 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(28)"> 4 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(29)"> 5 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(30)"> 6 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(31)"> 7 </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"  onclick="check_department_type(32)"> 8 </td>
			 				
			 			</tr>
			 			<tr>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_five;?> </td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_six;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_seven;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_eight;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $twenty_nine;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $thirty;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $thirty_one;?></td>
			 				<td class="center-align" style="height: 35px;width: 55px;"><?php echo $thirty_two;?></td>
			 			</tr>
			 			
			 		</table>
			 	</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
</script>
