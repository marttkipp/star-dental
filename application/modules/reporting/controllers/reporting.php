<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "./application/modules/accounts/controllers/accounts.php";

class Reporting extends Accounts
{	
	var $attachments_path;
	function __construct()
	{
		parent:: __construct();
		$this->load->model('administration/reports_model');
		$this->load->model('inventory_management/inventory_management_model');
		$this->load->model('admin/email_model');
		

		$this->attachments_path = realpath(APPPATH . '../assets/attachments');
		
	}

	function daily_report()
	{
		$date_tomorrow = date('Y-m-d');
		// $date_tomorrow = date("Y-m-d", strtotime("-1 day", strtotime($date_tomorrow)));
		$visit_date = date('jS M Y',strtotime($date_tomorrow));
		$branch = $this->config->item('branch_name');
		$message['subject'] =  $branch.' '.$visit_date.' report';


		$text =  $this->load->view('daily_report', '',true);
		// echo $text; die();
		$message['text'] =$text;
		$contacts = $this->site_model->get_contacts();
		$sender_email =$this->config->item('sender_email');//$contacts['email'];
		$shopping = "";
		$from = $sender_email; 
		
		$button = '';
		$sender['email']= $sender_email; 
		$sender['name'] = $contacts['company_name'];
		$receiver['name'] = $subject;
		// $payslip = $title;

		$sender_email = $sender_email;
		$tenant_email .= $this->config->item('recepients_email');
		$email_array = explode('/', $tenant_email);
		$total_rows_email = count($email_array);

		for($x = 0; $x < $total_rows_email; $x++)
		{
			$receiver['email'] = $email_tenant = $email_array[$x];

			$this->email_model->send_sendgrid_mail($receiver, $sender, $message, NULL);	
			

		}

		$response['status'] = 'success';

		echo json_encode($response);
		// echo '<script language="JavaScript">';
		// echo 'window.self.close();';
		// echo '</script>';

	}

	public function send_drugs_sold()
	{
		$v_data['contacts'] = $this->site_model->get_contacts();


		$table = 'visit, pres, service_charge,visit_charge,product';
		$where = 'pres.service_charge_id = service_charge.service_charge_id AND pres.visit_id = visit.visit_id AND visit.visit_delete = 0 AND pres.visit_charge_id = visit_charge.visit_charge_id AND visit_charge.charged = 1 AND service_charge.product_id = product.product_id AND visit.visit_date = "'.date('Y-m-d').'" ';
		
		$v_data['inventory_start_date'] = $this->inventory_management_model->get_inventory_start_date();

		$v_data['query'] = $this->reports_model->get_all_drugs_sold($where,$table);

		$html = $this->load->view('drugs_sold', $v_data, true);


		$date_tomorrow = date('Y-m-d');
		$visit_date = date('jS M Y',strtotime($date_tomorrow));
		$branch = $this->config->item('branch_name');
		$message['subject'] =  $branch.' '.$visit_date.' DRUGS SALES REPORT';

		$message['text'] = $html;
		$contacts = $this->site_model->get_contacts();
		$sender_email =$this->config->item('sender_email');//$contacts['email'];
		$shopping = "";
		$from = $sender_email; 
		
		$button = '';
		$sender['email']= $sender_email; 
		$sender['name'] = $contacts['company_name'];
		$receiver['name'] = $subject;
		// $payslip = $title;

		$sender_email = $sender_email;
		$tenant_email .= $this->config->item('recepients_email');;
		// var_dump($tenant_email); die();
		$email_array = explode('/', $tenant_email);
		$total_rows_email = count($email_array);
		
		for($x = 0; $x < $total_rows_email; $x++)
		{
			$receiver['email'] = $email_tenant = $email_array[$x];

			$this->email_model->send_sendgrid_mail($receiver, $sender, $message, $payslip=null);		
			

		}

	}
}
?>
