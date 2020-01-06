<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";


class Creditors extends admin
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('creditors_model');
		$this->load->model('purchases_model');
    $this->load->model('financials/company_financial_model');
	}



  public function creditors_invoices()
  {
    // $v_data['property_list'] = $property_list;


   
    $creditor_id = $this->session->userdata('invoice_creditor_id_searched');


    $where = 'creditor_invoice.creditor_invoice_status = 1 AND creditor_invoice.creditor_id = '.$creditor_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'creditor_invoice';


    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/creditor-invoices';
    $config['total_rows'] = $this->purchases_model->count_items($table, $where);
    $config['uri_segment'] = $segment;
    $config['per_page'] = 20;
    $config['num_links'] = 5;

    $config['full_tag_open'] = '<ul class="pagination pull-right">';
    $config['full_tag_close'] = '</ul>';

    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';

    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';

    $config['next_tag_open'] = '<li>';
    $config['next_link'] = 'Next';
    $config['next_tag_close'] = '</span>';

    $config['prev_tag_open'] = '<li>';
    $config['prev_link'] = 'Prev';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $this->pagination->initialize($config);

    $page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
    $query = $this->creditors_model->get_all_creditors_details($table, $where, $config["per_page"], $page, $order='creditor_invoice.transaction_date', $order_method='DESC');
    $v_data['creditor_invoices'] = $query;
    $v_data['page'] = $page;
    $data['title'] = 'Creditor Invoices';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/creditors_statement', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_creditors_invoice()
  {
    // var_dump($_POST);die();
    $creditor_id = $creditor_id_searched = $this->input->post('creditor_id');

    $search_title = '';

    if(!empty($creditor_id))
    {

      $creditor_id = ' AND creditor.creditor_id = '.$creditor_id.' ';
    }
    $search = $creditor_id;

    $this->session->set_userdata('invoice_creditor_id_searched', $creditor_id_searched);
    $this->session->set_userdata('search_creditors_invoice', $search);
    redirect('accounting/creditor-invoices');
  }

  public function search_creditors_bill($creditor_id)
  {

    $creditor_id = $creditor_id_searched = $creditor_id;

    $search_title = '';

    if(!empty($creditor_id))
    {

      $creditor_id = ' AND creditor.creditor_id = '.$creditor_id.' ';
    }
    $search = $creditor_id;

    $this->session->set_userdata('invoice_creditor_id_searched', $creditor_id_searched);
    $this->session->set_userdata('search_creditors_invoice', $search);
    redirect('accounting/creditor-invoices');
  }

  public function close_searched_invoices_creditor()
  {
    $this->session->unset_userdata('invoice_creditor_id_searched');
    $this->session->unset_userdata('search_creditors_invoice');
    redirect('accounting/creditors');
  }
  public function calculate_value()
  {
    $quantity = $this->input->post('quantity');
    $tax_type_id = $this->input->post('tax_type_id');
    $unit_price = $this->input->post('unit_price');


    if(empty($quantity))
    {
      $quantity = 1;
    }
    if(empty($unit_price))
    {
      $unit_price = 0;
    }
    if(empty($tax_type_id))
    {
      $tax_type_id = 0;
    }

    if($tax_type_id == 0)
    {
      $total_amount = $unit_price *$quantity;
      $vat = 0;
    }
    if($tax_type_id == 1)
    {
      $total_amount = ($unit_price * $quantity)*1.16;
      $vat = ($unit_price * $quantity)*0.16;
    }
    if($tax_type_id == 2)
    {
      $total_amount = ($unit_price * $quantity)*1.05;
      $vat = ($unit_price * $quantity)*0.05;
    }

    $response['message'] = 'success';
    $response['amount'] = $total_amount;
    $response['vat'] = $vat;

    echo json_encode($response);


  }

  public function add_invoice_item($creditor_id,$creditor_invoice_id = NULL)
  {

    $this->form_validation->set_rules('quantity', 'Invoice Item', 'trim|required|xss_clean');
    $this->form_validation->set_rules('unit_price', 'Unit Price', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_to_id', 'Expense Account', 'trim|required|xss_clean');
    $this->form_validation->set_rules('item_description', 'Item', 'trim|required|xss_clean');
    $this->form_validation->set_rules('tax_type_id', 'VAT Type', 'trim|xss_clean');
    $this->form_validation->set_rules('vat_amount', 'VAT Amount', 'trim|xss_clean');
    $this->form_validation->set_rules('total_amount', 'Total Amount', 'trim|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
			// var_dump($_POST);die();
      $this->creditors_model->add_invoice_item($creditor_id,$creditor_invoice_id);
      $this->session->set_userdata("success_message", 'Invoice Item successfully added');
      $response['status'] = 'success';
      $response['message'] = 'Payment successfully added';
    }
    else
    {
      $this->session->set_userdata("error_message", validation_errors());
      $response['status'] = 'fail';
      $response['message'] = validation_errors();

    }
    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);

  }

  public function confirm_invoice_note($creditor_id,$creditor_invoice_id = NULL)
  {
    $this->form_validation->set_rules('vat_charged', 'tax', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_charged', 'Amount Charged', 'trim|xss_clean');
    $this->form_validation->set_rules('invoice_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_number', 'Invoice Number', 'trim|required|xss_clean');
		if ($this->form_validation->run())
		{
			// var_dump($_POST);die();
				$this->creditors_model->confirm_creditor_invoice($creditor_id,$creditor_invoice_id);

				$this->session->set_userdata("success_message", 'Creditor invoice successfully added');
				$response['status'] = 'success';
				$response['message'] = 'Payment successfully added';
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			$response['status'] = 'fail';
			$response['message'] = validation_errors();

		}


    if(!empty($creditor_invoice_id))
    {
        redirect('accounting/creditor-invoices');
    }
    else
    {
      $redirect_url = $this->input->post('redirect_url');
      redirect($redirect_url);
    }

        
  }


  // credit notes

  public function creditors_credit_note()
  {
    // $v_data['property_list'] = $property_list;


     $creditor_id = $this->session->userdata('credit_note_creditor_id_searched');


    $where = 'creditor_credit_note.creditor_credit_note_status AND creditor_credit_note.creditor_id = '.$creditor_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'creditor_credit_note';


    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/creditor-credit-notes';
    $config['total_rows'] = $this->purchases_model->count_items($table, $where);
    $config['uri_segment'] = $segment;
    $config['per_page'] = 20;
    $config['num_links'] = 5;

    $config['full_tag_open'] = '<ul class="pagination pull-right">';
    $config['full_tag_close'] = '</ul>';

    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';

    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';

    $config['next_tag_open'] = '<li>';
    $config['next_link'] = 'Next';
    $config['next_tag_close'] = '</span>';

    $config['prev_tag_open'] = '<li>';
    $config['prev_link'] = 'Prev';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $this->pagination->initialize($config);

    $page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
    $query = $this->creditors_model->get_all_creditors_details($table, $where, $config["per_page"], $page, $order='creditor_credit_note.transaction_date', $order_method='DESC');
    $v_data['creditor_credit_notes'] = $query;
    $v_data['page'] = $page;

    $data['title'] = 'Creditor Credit Notes';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/creditors_credit_notes', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_creditors_credit_notes($creditor_id=null)
  {
    if(!empty($creditor_id))
    {
      $creditor_id = $creditor_id_searched = $creditor_id;
    }
    else
    {
      $creditor_id = $creditor_id_searched = $this->input->post('creditor_id');
    }



    $search_title = '';

    if(!empty($creditor_id))
    {

      $creditor_id = ' AND creditor.creditor_id = '.$creditor_id.' ';
    }
    $search = $creditor_id;
      // var_dump($creditor_id_searched);die();
    $this->session->set_userdata('credit_note_creditor_id_searched', $creditor_id_searched);
    $this->session->set_userdata('search_creditors_credit_notes', $search);
    redirect('accounting/creditor-credit-notes');
  }

  public function close_searched_credit_notes_creditor()
  {
    $this->session->unset_userdata('credit_note_creditor_id_searched');
    $this->session->unset_userdata('search_creditors_credit_notes');
    redirect('accounting/creditors');
  }

  public function add_credit_note_item($creditor_id,$creditor_credit_note_id=NULL)
  {

    $this->form_validation->set_rules('amount', 'Unit Price', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_to_id', 'Invoice', 'trim|required|xss_clean');
    $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
    $this->form_validation->set_rules('tax_type_id', 'VAT Type', 'trim|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $this->creditors_model->add_credit_note_item($creditor_id,$creditor_credit_note_id);
      $this->session->set_userdata("success_message", 'Invoice Item successfully added');
      $response['status'] = 'success';
      $response['message'] = 'Payment successfully added';
    }
    else
    {
      $this->session->set_userdata("error_message", validation_errors());
      $response['status'] = 'fail';
      $response['message'] = validation_errors();

    }
    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);

  }

  public function confirm_credit_note($creditor_id,$creditor_credit_note_id=NULL)
  {
    $this->form_validation->set_rules('vat_charged', 'tax', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_charged', 'Amount Charged', 'trim|xss_clean');
    $this->form_validation->set_rules('credit_note_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_id', 'Invoice ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount', 'trim|xss_clean');
    $this->form_validation->set_rules('credit_note_number', 'Invoice Number', 'trim|xss_clean');

		if ($this->form_validation->run())
		{
        // var_dump($_POST);die();
				$this->creditors_model->confirm_creditor_credit_note($creditor_id,$creditor_credit_note_id);

				$this->session->set_userdata("success_message", 'Creditor invoice successfully added');
				$response['status'] = 'success';
				$response['message'] = 'Payment successfully added';
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			$response['status'] = 'fail';
			$response['message'] = validation_errors();

		}

    if(!empty($creditor_credit_note_id))
    {
     
      redirect('accounting/creditor-credit-notes');
    }
    else
    {
      $redirect_url = $this->input->post('redirect_url');
      redirect($redirect_url);
    }
  }

  // creditors payments_import


  public function creditors_payments()
  {
    // $v_data['property_list'] = $property_list;
      $creditor_id = $this->session->userdata('payment_creditor_id_searched');


    $where = 'creditor_payment.creditor_payment_status = 1 AND account.account_id = creditor_payment.account_from_id AND creditor_payment.creditor_id = '.$creditor_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'creditor_payment,account';

 // $this->db->join('account','account.account_id = creditor_payment.account_from_id','left');

    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/creditor-payments';
    $config['total_rows'] = $this->purchases_model->count_items($table, $where);
    $config['uri_segment'] = $segment;
    $config['per_page'] = 20;
    $config['num_links'] = 5;

    $config['full_tag_open'] = '<ul class="pagination pull-right">';
    $config['full_tag_close'] = '</ul>';

    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';

    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';

    $config['next_tag_open'] = '<li>';
    $config['next_link'] = 'Next';
    $config['next_tag_close'] = '</span>';

    $config['prev_tag_open'] = '<li>';
    $config['prev_link'] = 'Prev';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $this->pagination->initialize($config);

    $page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
        $v_data["links"] = $this->pagination->create_links();
    $query = $this->creditors_model->get_all_creditors_details($table, $where, $config["per_page"], $page, $order='creditor_payment.transaction_date', $order_method='DESC');
    $v_data['creditor_payments'] = $query;
    $v_data['page'] = $page;
    $data['title'] = 'Creditor Payments';

    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/creditors_payments', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_creditors_payments($creditor_id=null)
  {
    if(!empty($creditor_id))
    {
       $creditor_id = $creditor_id_searched = $creditor_id;
    }
    else
    {
       $creditor_id = $creditor_id_searched = $this->input->post('creditor_id');
    }



    $search_title = '';

    if(!empty($creditor_id))
    {

      $creditor_id = ' AND creditor.creditor_id = '.$creditor_id.' ';
    }
    $search = $creditor_id;
      // var_dump($creditor_id_searched);die();
    $this->session->set_userdata('payment_creditor_id_searched', $creditor_id_searched);
    $this->session->set_userdata('search_creditors_payments', $search);
    redirect('accounting/creditor-payments');
  }

  public function close_searched_payments_creditor()
  {
    $this->session->unset_userdata('payment_creditor_id_searched');
    $this->session->unset_userdata('search_creditors_payments');
    redirect('accounting/creditors');
  }
  public function add_payment_item($creditor_id,$creditor_payment_id = NULL)
  {

    $this->form_validation->set_rules('amount_paid', 'Unit Price', 'trim|required|xss_clean');
    // $this->form_validation->set_rules('invoice_id', 'Invoice', 'trim|required|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {

      $this->creditors_model->add_payment_item($creditor_id,$creditor_payment_id);
      $this->session->set_userdata("success_message", 'Payment successfully added');
      $response['status'] = 'success';
      $response['message'] = 'Payment successfully added';
    }
    else
    {
      $this->session->set_userdata("error_message", validation_errors());
      $response['status'] = 'fail';
      $response['message'] = validation_errors();

    }
    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);

  }

  public function confirm_payment($creditor_id,$creditor_payment_id=NULL)
  {
    $this->form_validation->set_rules('reference_number', 'Reference Number', 'trim|required|xss_clean');
    $this->form_validation->set_rules('payment_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_from_id', 'Payment Amount', 'trim|required|xss_clean');

    if ($this->form_validation->run())
    {
        // var_dump($_POST);die();


        $status = $this->creditors_model->confirm_creditor_payment($creditor_id,$creditor_payment_id);

        if($status == TRUE)
        {

          if(!empty($creditor_payment_id))
          {
            $this->session->set_userdata("success_message", 'Payment has been successfully updated');
            $response['status'] = 'success';
            $response['message'] = 'Payment successfully added';
            $redirect_url = $this->input->post('redirect_url');
            redirect('creditor-statement/'.$creditor_id);

          }
          else
          {
            $this->session->set_userdata("success_message", 'Creditor invoice successfully added');
            $response['status'] = 'success';
            $response['message'] = 'Payment successfully added';
            $redirect_url = $this->input->post('redirect_url');
            redirect($redirect_url);
          }

        }
        else
        {
          $this->session->set_userdata("success_message", 'Sorry could not make the update. Please try again');
          $response['status'] = 'success';
          $response['message'] = 'Payment successfully added';
          $redirect_url = $this->input->post('redirect_url');
          redirect($redirect_url);
        }
        

    }
    else
    {
      $this->session->set_userdata("error_message", validation_errors());
      $response['status'] = 'fail';
      $response['message'] = validation_errors();

    }


    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);

    


  }


  /*
  *
  * Add a new creditor
  *
  */
  public function add_creditor()
  {
    //form validation rules
    $this->form_validation->set_rules('creditor_name', 'Name', 'required|xss_clean');
    $this->form_validation->set_rules('creditor_email', 'Email', 'xss_clean');
    $this->form_validation->set_rules('creditor_phone', 'Phone', 'xss_clean');
    $this->form_validation->set_rules('creditor_location', 'Location', 'xss_clean');
    $this->form_validation->set_rules('creditor_building', 'Building', 'xss_clean');
    $this->form_validation->set_rules('creditor_floor', 'Floor', 'xss_clean');
    $this->form_validation->set_rules('creditor_address', 'Address', 'xss_clean');
    $this->form_validation->set_rules('creditor_post_code', 'Post code', 'xss_clean');
    $this->form_validation->set_rules('creditor_city', 'City', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_name', 'Contact Name', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_onames', 'Contact Other Names', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
    $this->form_validation->set_rules('creditor_description', 'Description', 'xss_clean');
    $this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
    $this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');

    // var_dump($_POST); die();
    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $creditor_id = $this->creditors_model->add_creditor();
      if($creditor_id > 0)
      {
        $this->session->set_userdata("success_message", "Creditor added successfully");
        $redirect_url = $this->input->post('redirect_url');
        if(!empty($redirect_url))
        {
          redirect($redirect_url);
        }
        else
        {
          redirect('accounting/creditors');
        }
      }

      else
      {
        $this->session->set_userdata("error_message","Could not add creditor. Please try again");

        $redirect_url = $this->input->post('redirect_url');
        if(!empty($redirect_url))
        {
          redirect($redirect_url);
        }
        else
        {
          redirect('accounting/creditors');
        }
      }
    }
    $data['title'] = 'Add creditor';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/add_creditor', $v_data, true);

    $this->load->view('admin/templates/general_page', $data);
  }

  /*
  *
  * Add a new creditor
  *
  */
  public function edit_creditor($creditor_id)
  {
    //form validation rules
    $this->form_validation->set_rules('creditor_name', 'Name', 'required|xss_clean');
    $this->form_validation->set_rules('creditor_email', 'Email', 'xss_clean');
    $this->form_validation->set_rules('creditor_phone', 'Phone', 'xss_clean');
    $this->form_validation->set_rules('creditor_location', 'Location', 'xss_clean');
    $this->form_validation->set_rules('creditor_building', 'Building', 'xss_clean');
    $this->form_validation->set_rules('creditor_floor', 'Floor', 'xss_clean');
    $this->form_validation->set_rules('creditor_address', 'Address', 'xss_clean');
    $this->form_validation->set_rules('creditor_post_code', 'Post code', 'xss_clean');
    $this->form_validation->set_rules('creditor_city', 'City', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_name', 'Contact Name', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_onames', 'Contact Other Names', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
    $this->form_validation->set_rules('creditor_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
    $this->form_validation->set_rules('creditor_description', 'Description', 'xss_clean');
    $this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
    $this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $creditor_id = $this->creditors_model->edit_creditor($creditor_id);
      if($creditor_id > 0)
      {
        $this->session->set_userdata("success_message", "Creditor updated successfully");
        redirect('accounting/creditors');
      }

      else
      {
        $this->session->set_userdata("error_message","Could not add creditor. Please try again");
        redirect('finance/edit-creditor/'.$creditor_id);
      }
    }
    $data['title'] = 'Add creditor';
    $v_data['title'] = $data['title'];
    $v_data['creditor'] = $this->creditors_model->get_creditor($creditor_id);
    $data['content'] = $this->load->view('creditors/edit_creditor', $v_data, true);

    $this->load->view('admin/templates/general_page', $data);
  }

  public function transfer_creditor_bills()
  {
    $this->db->from('account_invoices');
    $this->db->select('*');
    $this->db->where('account_to_type = 2 AND account_invoice_deleted = 0 AND account_from_id > 0 and sync_status = 0');

    $query = $this->db->get();
    // var_dump($query);die();
    if($query->num_rows() > 0)
    {
      foreach ($query->result() as $key => $value) {
        # code...
        $invoice_amount = $value->invoice_amount;
        $account_to_id = $value->account_to_id;
        $account_invoice_status = $value->account_invoice_status;
        $account_invoice_description = $value->account_invoice_description;
        $account_from_id = $value->account_from_id;
        $created = $value->created;
        $created_by = $value->created_by;
        $invoice_number = $value->invoice_number;
        $department_id = $value->department_id;
        $invoice_date = $value->invoice_date;
        $account_invoice_id = $value->account_invoice_id;

        $exploded = explode('-', $invoice_date);

        $year = $exploded[0];
        $month = $exploded[1];

        $document_number = $this->creditors_model->create_invoice_number();

        $invoice['amount'] = $invoice_amount;
        $invoice['creditor_invoice_status'] = 1;
        $invoice['vat_charged'] = 0;
        $invoice['total_amount'] = $invoice_amount;
        $invoice['created'] = $created;
        $invoice['created_by'] = $created_by;
        $invoice['transaction_date'] = $invoice_date;
        $invoice['invoice_number'] = $invoice_number;
        $invoice['document_number'] = $document_number;
        $invoice['creditor_id'] = $account_from_id;
        $invoice['invoice_year'] = $year;
        $invoice['invoice_month'] = $month;


        $this->db->insert('creditor_invoice',$invoice);
        $creditor_invoice_id = $this->db->insert_id();


        $invoice_item['creditor_invoice_id'] = $creditor_invoice_id;
        $invoice_item['item_description'] = $account_invoice_description;
        $invoice_item['unit_price'] = $invoice_amount;
        $invoice_item['total_amount'] = $invoice_amount;
        $invoice_item['created'] = $created;
        $invoice_item['created_by'] = $created_by;
        $invoice_item['quantity'] = 1;
        $invoice_item['year'] = $year;
        $invoice_item['month'] = $month;
        $invoice_item['creditor_id'] = $account_from_id;
        $invoice_item['account_to_id'] = $account_from_id;
        $this->db->insert('creditor_invoice_item',$invoice_item);

        $update_item['sync_status'] = 1;
        $this->db->where('account_invoice_id',$account_invoice_id);
        $this->db->update('account_invoices',$update_item);


      }
    }
  }



  public function transfer_creditor_payments()
  {
    $this->db->from('account_payments');
    $this->db->select('*');
    $this->db->where('account_to_type = 2 AND account_payment_deleted = 0 AND account_to_id > 0 AND sync_status = 0');

    $query = $this->db->get();
    // var_dump($query);die();
    if($query->num_rows() > 0)
    {
      foreach ($query->result() as $key => $value) {
        # code...
        $amount_paid = $value->amount_paid;
        $account_to_id = $value->account_to_id;
        $account_payment_status = $value->account_payment_status;
        $account_payment_description = $value->account_payment_description;
        $account_from_id = $value->account_from_id;
        $created = $value->created;
        $created_by = $value->created_by;
        $receipt_number = $value->receipt_number;
        $payment_to = $value->payment_to;
        $payment_date = $value->payment_date;
        $account_payment_id = $value->account_payment_id;


        $exploded = explode('-', $payment_date);

        $year = $exploded[0];
        $month = $exploded[1];

        $document_number = $this->creditors_model->create_credit_payment_number();

        $invoice['total_amount'] = $amount_paid;
        $invoice['creditor_payment_status'] = 1;
        $invoice['created'] = $created;
        $invoice['created_by'] = $created_by;
        $invoice['transaction_date'] = $payment_date;
        $invoice['reference_number'] = $receipt_number;
        $invoice['document_number'] = $document_number;
        $invoice['creditor_id'] = $account_to_id;
        $invoice['account_from_id'] = $account_from_id;
        $invoice['payment_year'] = $year;
        $invoice['payment_month'] = $month;


        $this->db->insert('creditor_payment',$invoice);
        $creditor_payment_id = $this->db->insert_id();

        $invoice_item['creditor_payment_id'] = $creditor_payment_id;
        $invoice_item['description'] = $account_payment_description;
        $invoice_item['amount_paid'] = $amount_paid;
        $invoice_item['created'] = $created;
        $invoice_item['created_by'] = $created_by;
        $invoice_item['year'] = $year;
        $invoice_item['month'] = $month;
        $invoice_item['creditor_id'] = $account_to_id;
        $invoice_item['invoice_type'] = 2;
        $this->db->insert('creditor_payment_item',$invoice_item);

          $update_item['sync_status'] = 1;
        $this->db->where('account_payment_id',$account_payment_id);
        $this->db->update('account_payments',$update_item);
      }
    }
  }
	public function merge_credit_notes()
	{

		$this->db->select('*');
		$this->db->where('is_store = 3 and supplier_id > 0');
		$query = $this->db->get('orders');

		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $key => $value) {
				// code...
				$supplier_invoice_number = $value->supplier_invoice_number;
				$order_id = $value->order_id;

				$this->db->select('*');
				$this->db->where('is_store = 0 and supplier_id > 0 and supplier_invoice_number = "'.$supplier_invoice_number.'"');
				$this->db->limit(1);
				$query2 = $this->db->get('orders');
				if($query2->num_rows() == 1)
				{
					// update the reference_id
					$rows = $query2->row();
					$reference_id = $rows->order_id;

					$update_array['reference_id'] = $reference_id;
					$this->db->where('is_store = 3 and order_id ='.$order_id);
					$this->db->update('orders',$update_array);
				}
			}
		}
	}




  public function creditors_list()
  {
    $data['title'] = 'Vendor Expenses';
    $v_data['title'] = $data['title'];

		// var_dump($v_date); die();
    $data['content'] = $this->load->view('creditors/creditors_accounts', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }
	public function delete_creditor_payment_item($creditor_payment_item_id,$creditor_id,$creditor_payment_id = NULL)
	{
		$this->db->where('creditor_payment_item_id',$creditor_payment_item_id);
		$this->db->delete('creditor_payment_item');

    if(!empty($creditor_payment_id))
    {
        redirect('edit-creditor-payment/'.$creditor_payment_id);
    }
		else
    {
      redirect('accounting/creditor-payments');
    }
	}


	public function delete_creditor_invoice_item($creditor_invoice_item_id,$creditor_id,$creditor_invoice_id = NUll)
	{
		$this->db->where('creditor_invoice_item_id',$creditor_invoice_item_id);
		$this->db->delete('creditor_invoice_item');

    if(!empty($creditor_invoice_id))
    {
        redirect('creditor-invoice/edit-creditor-invoice/'.$creditor_invoice_id);
    }
    else
    {
          redirect('accounting/creditor-invoices');
    }
		
	}


  public function allocate_creditor_payment($creditor_payment_id,$creditor_payment_item_id,$creditor_id)
  {


    $data['title'] = 'Allocating Creditor Payment';
    $v_data['title'] = $data['title'];
    $v_data['creditor_id'] = $creditor_id;
    $v_data['creditor_payment_id'] = $creditor_payment_id;
    $v_data['creditor_payment_item_id'] = $creditor_payment_item_id;


    // var_dump($v_date); die();
    $data['content'] = $this->load->view('creditors/allocate_creditor_payment', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function delete_payment_item($creditor_payment_id,$creditor_payment_item_id,$creditor_payment_item_id_db,$creditor_id)
  {

    // var_dump($creditor_payment_item_id_db);die();
    $this->db->where('creditor_payment_item_id',$creditor_payment_item_id_db);
    $this->db->delete('creditor_payment_item');

    redirect('allocate-payment/'.$creditor_payment_id.'/'.$creditor_payment_item_id.'/'.$creditor_id);
  }
  

  public function search_creditors()
  {
    $creditor_name = $year_from = $this->input->post('creditor_name');
    $redirect_url = $this->input->post('redirect_url');
    $creditor_search = '';
     if(!empty($creditor_name))
    {
      $creditor_search .= 'payables LIKE \'%'.$creditor_name.'%\'';

    }

    $search = $creditor_search;

    

    // var_dump($date_to); die();
    $this->session->set_userdata('creditor_search',$search);
    redirect($redirect_url);

  }
  public function close_creditor_creditor_search()
  {

    $this->session->unset_userdata('creditor_search');


    redirect('accounting/creditors');

  }

  public function get_invoice_details($creditor_invoice_id)
  {
    $data['creditor_invoice_id'] = $creditor_invoice_id;
    $this->load->view('creditors/view_creditor_invoice', $data);  
  }
  public function get_payment_details($creditor_payment_id)
  {
    $data['creditor_payment_id'] = $creditor_payment_id;
    $this->load->view('creditors/view_creditor_payment', $data);  
  }

  public function delete_creditor_invoice($creditor_invoice_id)
  {
    $update_query['creditor_invoice_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('creditor_invoice_id',$creditor_invoice_id);
    $this->db->update('creditor_invoice',$update_query);

    redirect('accounting/creditor-invoices');
  }

  public function edit_creditor_invoice($creditor_invoice_id)
  {

      $data['title'] = 'Edit Creditor Invoice';
      $v_data['creditor_invoice_id'] = $creditor_invoice_id;
      $creditor_id = $this->session->userdata('invoice_creditor_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('creditors/edit_creditor_invoice', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);

  }


  public function delete_creditor_payment($creditor_payment_id)
  {
    $update_query['creditor_payment_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('creditor_payment_id',$creditor_payment_id);
    $this->db->update('creditor_payment',$update_query);

    redirect('accounting/creditor-payments');
  }

  public function edit_creditor_payment($creditor_payment_id)
  {

      $data['title'] = 'Edit Creditor Invoice';
      $v_data['creditor_payment_id'] = $creditor_payment_id;
      $creditor_id = $this->session->userdata('payment_creditor_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('creditors/edit_creditor_payment', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);

  }

   public function delete_credit_note_item($creditor_credit_note_item_id,$creditor_credit_note_id=NULL)
  {
   
    $this->db->where('creditor_credit_note_item_id',$creditor_credit_note_item_id);
    $this->db->delete('creditor_credit_note_item',$update_query);

    if(!empty($creditor_credit_note_id))
    {
      redirect('edit-creditor-credit-note/'.$creditor_credit_note_id);
    }
    else
    {
      redirect('accounting/creditor-credit-notes');
    }
  }

  public function delete_creditor_credit_note($creditor_credit_note_id,$creditor_id)
  {


    $update_query['creditor_credit_note_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('creditor_credit_note_id',$creditor_credit_note_id);
    $this->db->update('creditor_credit_note',$update_query);

    redirect('accounting/creditor-credit-notes');
  }
  public function edit_creditor_credit_note($creditor_credit_note_id)
  {
     $data['title'] = 'Edit Creditor Credit Note';
      $v_data['creditor_credit_note_id'] = $creditor_credit_note_id;
      $creditor_id = $this->session->userdata('credit_note_creditor_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('creditors/edit_creditor_credit_note', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);
  }

  public function get_credit_note_details($creditor_credit_note_id)
  {
    $data['creditor_credit_note_id'] = $creditor_credit_note_id;
    $this->load->view('creditors/view_creditor_credit_notes', $data); 
  }

}
?>
