<?php
	if(count($contacts) > 0)
	{
		$email = $contacts['email'];
		$email2 = $contacts['email'];
		$logo = $contacts['logo'];
		$company_name = $contacts['company_name'];
		$phone = $contacts['phone'];
		
		if(!empty($facebook))
		{
			$facebook = '<li class="facebook"><a href="'.$facebook.'" target="_blank" title="Facebook">Facebook</a></li>';
		}
		
	}
	else
	{
		$email = '';
		$facebook = '';
		$twitter = '';
		$linkedin = '';
		$logo = '';
		$company_name = '';
		$google = '';
	}
?>
			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="http://preview.oklerthemes.com/porto-admin/" class="logo">
						<img src="<?php echo base_url().'assets/logo/'.$logo;?>" height="35" alt="<?php echo $company_name;?>" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>

				
				<!-- start: search & user box -->
				<div class="header-right">
					<?php

					// $personnel_id = $this->session->userdata('personnel_id');
					// $department_id = $this->reception_model->get_personnel_department($personnel_id);
					// // var_dump($department_id); die();
					// if($department_id == 4)
					// {
						
					// }
					// else
					// {
						?>
						<a  href="<?php echo site_url().'send-fourty-eight-appointment';?>" class="btn btn-danger" target="_blank" onclick="return confirm('Do you want to send notifications for appointment ?')"><i class="fa fa-recycle"></i> 48 Hour Reminders</a>

					


						<?php
					// }
					?>
					
					<a  href="<?php echo site_url().$this->uri->uri_string();?>" class="btn btn-info" ><i class="fa fa-recycle"></i> Refresh</a>
					<span class="separator"></span>
					<?php
					$image =  $this->session->userdata('image');
					if(empty($image))
					{
						$avator = base_url().'assets/img/avatar.jpg';
					}
					else
					{
						$avator = base_url().'assets/personnel/'.$image;
					}
					?>
			
					<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<figure class="profile-picture">
								<img src="<?php echo $avator;?>" alt="<?php echo $this->session->userdata('first_name');?>" class="img-circle" data-lock-picture="<?php echo $avator;?>" />
							</figure>
							<div class="profile-info" data-lock-name="<?php echo $this->session->userdata('first_name');?>" data-lock-email="<?php echo $this->session->userdata('email');?>">
								<span class="name">
									<?php 
									
									echo $this->session->userdata('first_name');


									
									?>
                                </span>
								<span class="role"><?php echo $this->session->userdata('branch_code');?></span>
							</div>
			
							<i class="fa custom-caret"></i>
						</a>
			
						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="<?php echo site_url()."my-profile";?>"><i class="fa fa-user"></i> My Profile</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="<?php echo site_url()."logout-admin";?>"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->