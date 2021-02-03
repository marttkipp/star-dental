<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";


class Lenders extends admin
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('lenders_model');
		$this->load->model('purchases_model');
    $this->load->model('accounting/debtors_model');
    $this->load->model('financials/company_financial_model');
	}



  public function lenders_invoices()
  {
    // $v_data['property_list'] = $property_list;


   
    $lender_id = $this->session->userdata('invoice_lender_id_searched');


    $where = 'lender_invoice.lender_invoice_status = 1 AND lender_invoice.lender_id = '.$lender_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'lender_invoice';


    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/lender-invoices';
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
    $query = $this->lenders_model->get_all_lenders_details($table, $where, $config["per_page"], $page, $order='lender_invoice.transaction_date', $order_method='DESC');
    $v_data['lender_invoices'] = $query;
    $v_data['page'] = $page;
    $data['title'] = 'lender Invoices';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('lenders/lenders_statement', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_lenders_invoice()
  {
    // var_dump($_POST);die();
    $lender_id = $lender_id_searched = $this->input->post('lender_id');

    $search_title = '';

    if(!empty($lender_id))
    {

      $lender_id = ' AND lender.lender_id = '.$lender_id.' ';
    }
    $search = $lender_id;

    $this->session->set_userdata('invoice_lender_id_searched', $lender_id_searched);
    $this->session->set_userdata('search_lenders_invoice', $search);
    redirect('accounting/lender-invoices');
  }

  public function search_lenders_bill($lender_id)
  {

    $lender_id = $lender_id_searched = $lender_id;

    $search_title = '';

    if(!empty($lender_id))
    {

      $lender_id = ' AND lender.lender_id = '.$lender_id.' ';
    }
    $search = $lender_id;

    $this->session->set_userdata('invoice_lender_id_searched', $lender_id_searched);
    $this->session->set_userdata('search_lenders_invoice', $search);
    redirect('accounting/lender-invoices');
  }

  public function close_searched_invoices_lender()
  {
    $this->session->unset_userdata('invoice_lender_id_searched');
    $this->session->unset_userdata('search_lenders_invoice');
    redirect('accounting/lenders');
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

  public function add_invoice_item($lender_id,$lender_invoice_id = NULL)
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
      $this->lenders_model->add_invoice_item($lender_id,$lender_invoice_id);
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

  public function confirm_invoice_note($lender_id,$lender_invoice_id = NULL)
  {
    $this->form_validation->set_rules('vat_charged', 'tax', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_charged', 'Amount Charged', 'trim|xss_clean');
    $this->form_validation->set_rules('invoice_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_number', 'Invoice Number', 'trim|required|xss_clean');
		if ($this->form_validation->run())
		{
			// var_dump($_POST);die();
				$this->lenders_model->confirm_lender_invoice($lender_id,$lender_invoice_id);

				$this->session->set_userdata("success_message", 'lender invoice successfully added');
				$response['status'] = 'success';
				$response['message'] = 'Payment successfully added';
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			$response['status'] = 'fail';
			$response['message'] = validation_errors();

		}


    if(!empty($lender_invoice_id))
    {
        redirect('accounting/lender-invoices');
    }
    else
    {
      $redirect_url = $this->input->post('redirect_url');
      redirect($redirect_url);
    }

        
  }


  // credit notes

  public function lenders_credit_note()
  {
    // $v_data['property_list'] = $property_list;


     $lender_id = $this->session->userdata('credit_note_lender_id_searched');


    $where = 'lender_credit_note.lender_credit_note_status AND lender_credit_note.lender_id = '.$lender_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'lender_credit_note';


    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/lender-credit-notes';
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
    $query = $this->lenders_model->get_all_lenders_details($table, $where, $config["per_page"], $page, $order='lender_credit_note.transaction_date', $order_method='DESC');
    $v_data['lender_credit_notes'] = $query;
    $v_data['page'] = $page;

    $data['title'] = 'lender Credit Notes';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('lenders/lenders_credit_notes', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_lenders_credit_notes($lender_id=null)
  {
    if(!empty($lender_id))
    {
      $lender_id = $lender_id_searched = $lender_id;
    }
    else
    {
      $lender_id = $lender_id_searched = $this->input->post('lender_id');
    }



    $search_title = '';

    if(!empty($lender_id))
    {

      $lender_id = ' AND lender.lender_id = '.$lender_id.' ';
    }
    $search = $lender_id;
      // var_dump($lender_id_searched);die();
    $this->session->set_userdata('credit_note_lender_id_searched', $lender_id_searched);
    $this->session->set_userdata('search_lenders_credit_notes', $search);
    redirect('accounting/lender-credit-notes');
  }

  public function close_searched_credit_notes_lender()
  {
    $this->session->unset_userdata('credit_note_lender_id_searched');
    $this->session->unset_userdata('search_lenders_credit_notes');
    redirect('accounting/lenders');
  }

  public function add_credit_note_item($lender_id,$lender_credit_note_id=NULL)
  {

    $this->form_validation->set_rules('amount', 'Unit Price', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_to_id', 'Invoice', 'trim|required|xss_clean');
    $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
    $this->form_validation->set_rules('tax_type_id', 'VAT Type', 'trim|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $this->lenders_model->add_credit_note_item($lender_id,$lender_credit_note_id);
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

  public function confirm_credit_note($lender_id,$lender_credit_note_id=NULL)
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
				$this->lenders_model->confirm_lender_credit_note($lender_id,$lender_credit_note_id);

				$this->session->set_userdata("success_message", 'lender invoice successfully added');
				$response['status'] = 'success';
				$response['message'] = 'Payment successfully added';
		}
		else
		{
			$this->session->set_userdata("error_message", validation_errors());
			$response['status'] = 'fail';
			$response['message'] = validation_errors();

		}

    if(!empty($lender_credit_note_id))
    {
     
      redirect('accounting/lender-credit-notes');
    }
    else
    {
      $redirect_url = $this->input->post('redirect_url');
      redirect($redirect_url);
    }
  }

  // lenders payments_import


  public function lenders_payments()
  {
    // $v_data['property_list'] = $property_list;
      $lender_id = $this->session->userdata('payment_lender_id_searched');


    $where = 'lender_payment.lender_payment_status = 1 AND account.account_id = lender_payment.account_from_id AND lender_payment.lender_id = '.$lender_id;

    $search_purchases = $this->session->userdata('search_purchases');
    if($search_purchases)
    {
      $where .= $search_purchases;
    }
    $table = 'lender_payment,account';

 // $this->db->join('account','account.account_id = lender_payment.account_from_id','left');

    $segment = 3;
    $this->load->library('pagination');
    $config['base_url'] = site_url().'accounting/lender-payments';
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
    $query = $this->lenders_model->get_all_lenders_details($table, $where, $config["per_page"], $page, $order='lender_payment.transaction_date', $order_method='DESC');
    $v_data['lender_payments'] = $query;
    $v_data['page'] = $page;
    $data['title'] = 'lender Payments';
// var_dump('sdsada');die();
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('lenders/lenders_payments', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_lenders_payments($lender_id=null)
  {
    if(!empty($lender_id))
    {
       $lender_id = $lender_id_searched = $lender_id;
    }
    else
    {
       $lender_id = $lender_id_searched = $this->input->post('lender_id');
    }



    $search_title = '';

    if(!empty($lender_id))
    {

      $lender_id = ' AND lender.lender_id = '.$lender_id.' ';
    }
    $search = $lender_id;
      // var_dump($lender_id_searched);die();
    $this->session->set_userdata('payment_lender_id_searched', $lender_id_searched);
    $this->session->set_userdata('search_lenders_payments', $search);
    redirect('accounting/lender-payments');
  }

  public function close_searched_payments_lender()
  {
    $this->session->unset_userdata('payment_lender_id_searched');
    $this->session->unset_userdata('search_lenders_payments');
    redirect('accounting/lenders');
  }
  public function add_payment_item($lender_id,$lender_payment_id = NULL)
  {

    $this->form_validation->set_rules('amount_paid', 'Unit Price', 'trim|required|xss_clean');
    // $this->form_validation->set_rules('invoice_id', 'Invoice', 'trim|required|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {

      $this->lenders_model->add_payment_item($lender_id,$lender_payment_id);
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

  public function confirm_payment($lender_id,$lender_payment_id=NULL)
  {
    $this->form_validation->set_rules('reference_number', 'Reference Number', 'trim|required|xss_clean');
    $this->form_validation->set_rules('payment_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_from_id', 'Payment Amount', 'trim|required|xss_clean');

    if ($this->form_validation->run())
    {
        // var_dump($_POST);die();


        $status = $this->lenders_model->confirm_lender_payment($lender_id,$lender_payment_id);

        if($status == TRUE)
        {

          if(!empty($lender_payment_id))
          {
            $this->session->set_userdata("success_message", 'Payment has been successfully updated');
            $response['status'] = 'success';
            $response['message'] = 'Payment successfully added';
            $redirect_url = $this->input->post('redirect_url');
            redirect('lender-statement/'.$lender_id);

          }
          else
          {
            $this->session->set_userdata("success_message", 'lender invoice successfully added');
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
  * Add a new lender
  *
  */
  public function add_lender()
  {
    //form validation rules
    $this->form_validation->set_rules('lender_name', 'Name', 'required|xss_clean');
    $this->form_validation->set_rules('lender_email', 'Email', 'xss_clean');
    $this->form_validation->set_rules('lender_phone', 'Phone', 'xss_clean');
    $this->form_validation->set_rules('lender_location', 'Location', 'xss_clean');
    $this->form_validation->set_rules('lender_building', 'Building', 'xss_clean');
    $this->form_validation->set_rules('lender_floor', 'Floor', 'xss_clean');
    $this->form_validation->set_rules('lender_address', 'Address', 'xss_clean');
    $this->form_validation->set_rules('lender_post_code', 'Post code', 'xss_clean');
    $this->form_validation->set_rules('lender_city', 'City', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_name', 'Contact Name', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_onames', 'Contact Other Names', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
    $this->form_validation->set_rules('lender_description', 'Description', 'xss_clean');
    $this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
    $this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');

    // var_dump($_POST); die();
    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $lender_id = $this->lenders_model->add_lender();
      if($lender_id > 0)
      {
        $this->session->set_userdata("success_message", "lender added successfully");
        $redirect_url = $this->input->post('redirect_url');
        if(!empty($redirect_url))
        {
          redirect($redirect_url);
        }
        else
        {
          redirect('accounting/lenders');
        }
      }

      else
      {
        $this->session->set_userdata("error_message","Could not add lender. Please try again");

        $redirect_url = $this->input->post('redirect_url');
        if(!empty($redirect_url))
        {
          redirect($redirect_url);
        }
        else
        {
          redirect('accounting/lenders');
        }
      }
    }
    $data['title'] = 'Add lender';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('lenders/add_lender', $v_data, true);

    $this->load->view('admin/templates/general_page', $data);
  }

  /*
  *
  * Add a new lender
  *
  */
  public function edit_lender($lender_id)
  {
    //form validation rules
    $this->form_validation->set_rules('lender_name', 'Name', 'required|xss_clean');
    $this->form_validation->set_rules('lender_email', 'Email', 'xss_clean');
    $this->form_validation->set_rules('lender_phone', 'Phone', 'xss_clean');
    $this->form_validation->set_rules('lender_location', 'Location', 'xss_clean');
    $this->form_validation->set_rules('lender_building', 'Building', 'xss_clean');
    $this->form_validation->set_rules('lender_floor', 'Floor', 'xss_clean');
    $this->form_validation->set_rules('lender_address', 'Address', 'xss_clean');
    $this->form_validation->set_rules('lender_post_code', 'Post code', 'xss_clean');
    $this->form_validation->set_rules('lender_city', 'City', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_name', 'Contact Name', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_onames', 'Contact Other Names', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_phone1', 'Contact Phone 1', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_phone2', 'Contact Phone 2', 'xss_clean');
    $this->form_validation->set_rules('lender_contact_person_email', 'Contact Email', 'valid_email|xss_clean');
    $this->form_validation->set_rules('lender_description', 'Description', 'xss_clean');
    $this->form_validation->set_rules('balance_brought_forward', 'Balance BroughtF','xss_clean');
    $this->form_validation->set_rules('debit_id', 'Balance BroughtF','xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $lender_id = $this->lenders_model->edit_lender($lender_id);
      if($lender_id > 0)
      {
        $this->session->set_userdata("success_message", "lender updated successfully");
        redirect('accounting/lenders');
      }

      else
      {
        $this->session->set_userdata("error_message","Could not add lender. Please try again");
        redirect('finance/edit-lender/'.$lender_id);
      }
    }
    $data['title'] = 'Add lender';
    $v_data['title'] = $data['title'];
    $v_data['lender'] = $this->lenders_model->get_lender($lender_id);
    $data['content'] = $this->load->view('lenders/edit_lender', $v_data, true);

    $this->load->view('admin/templates/general_page', $data);
  }

  public function transfer_lender_bills()
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

        $document_number = $this->lenders_model->create_invoice_number();

        $invoice['amount'] = $invoice_amount;
        $invoice['lender_invoice_status'] = 1;
        $invoice['vat_charged'] = 0;
        $invoice['total_amount'] = $invoice_amount;
        $invoice['created'] = $created;
        $invoice['created_by'] = $created_by;
        $invoice['transaction_date'] = $invoice_date;
        $invoice['invoice_number'] = $invoice_number;
        $invoice['document_number'] = $document_number;
        $invoice['lender_id'] = $account_from_id;
        $invoice['invoice_year'] = $year;
        $invoice['invoice_month'] = $month;


        $this->db->insert('lender_invoice',$invoice);
        $lender_invoice_id = $this->db->insert_id();


        $invoice_item['lender_invoice_id'] = $lender_invoice_id;
        $invoice_item['item_description'] = $account_invoice_description;
        $invoice_item['unit_price'] = $invoice_amount;
        $invoice_item['total_amount'] = $invoice_amount;
        $invoice_item['created'] = $created;
        $invoice_item['created_by'] = $created_by;
        $invoice_item['quantity'] = 1;
        $invoice_item['year'] = $year;
        $invoice_item['month'] = $month;
        $invoice_item['lender_id'] = $account_from_id;
        $invoice_item['account_to_id'] = $account_from_id;
        $this->db->insert('lender_invoice_item',$invoice_item);

        $update_item['sync_status'] = 1;
        $this->db->where('account_invoice_id',$account_invoice_id);
        $this->db->update('account_invoices',$update_item);


      }
    }
  }



  public function transfer_lender_payments()
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

        $document_number = $this->lenders_model->create_credit_payment_number();

        $invoice['total_amount'] = $amount_paid;
        $invoice['lender_payment_status'] = 1;
        $invoice['created'] = $created;
        $invoice['created_by'] = $created_by;
        $invoice['transaction_date'] = $payment_date;
        $invoice['reference_number'] = $receipt_number;
        $invoice['document_number'] = $document_number;
        $invoice['lender_id'] = $account_to_id;
        $invoice['account_from_id'] = $account_from_id;
        $invoice['payment_year'] = $year;
        $invoice['payment_month'] = $month;


        $this->db->insert('lender_payment',$invoice);
        $lender_payment_id = $this->db->insert_id();

        $invoice_item['lender_payment_id'] = $lender_payment_id;
        $invoice_item['description'] = $account_payment_description;
        $invoice_item['amount_paid'] = $amount_paid;
        $invoice_item['created'] = $created;
        $invoice_item['created_by'] = $created_by;
        $invoice_item['year'] = $year;
        $invoice_item['month'] = $month;
        $invoice_item['lender_id'] = $account_to_id;
        $invoice_item['invoice_type'] = 2;
        $this->db->insert('lender_payment_item',$invoice_item);

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




  public function lenders_list()
  {
    $data['title'] = 'Vendor Expenses';
    $v_data['title'] = $data['title'];

		// var_dump($v_date); die();
    $data['content'] = $this->load->view('lenders/lenders_accounts', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }
	public function delete_lender_payment_item($lender_payment_item_id,$lender_id,$lender_payment_id = NULL)
	{
		$this->db->where('lender_payment_item_id',$lender_payment_item_id);
		$this->db->delete('lender_payment_item');

    if(!empty($lender_payment_id))
    {
        redirect('edit-lender-payment/'.$lender_payment_id);
    }
		else
    {
      redirect('accounting/lender-payments');
    }
	}


	public function delete_lender_invoice_item($lender_invoice_item_id,$lender_id,$lender_invoice_id = NUll)
	{
		$this->db->where('lender_invoice_item_id',$lender_invoice_item_id);
		$this->db->delete('lender_invoice_item');

    if(!empty($lender_invoice_id))
    {
        redirect('lender-invoice/edit-lender-invoice/'.$lender_invoice_id);
    }
    else
    {
          redirect('accounting/lender-invoices');
    }
		
	}


  public function allocate_lender_payment($lender_payment_id,$lender_payment_item_id,$lender_id)
  {


    $data['title'] = 'Allocating lender Payment';
    $v_data['title'] = $data['title'];
    $v_data['lender_id'] = $lender_id;
    $v_data['lender_payment_id'] = $lender_payment_id;
    $v_data['lender_payment_item_id'] = $lender_payment_item_id;


    // var_dump($v_date); die();
    $data['content'] = $this->load->view('lenders/allocate_lender_payment', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function delete_payment_item($lender_payment_id,$lender_payment_item_id,$lender_payment_item_id_db,$lender_id)
  {

    // var_dump($lender_payment_item_id_db);die();
    $this->db->where('lender_payment_item_id',$lender_payment_item_id_db);
    $this->db->delete('lender_payment_item');

    redirect('allocate-payment/'.$lender_payment_id.'/'.$lender_payment_item_id.'/'.$lender_id);
  }
  

  public function search_lenders()
  {
    $lender_name = $year_from = $this->input->post('lender_name');
    $redirect_url = $this->input->post('redirect_url');
    $lender_search = '';
     if(!empty($lender_name))
    {
      $lender_search .= 'payables LIKE \'%'.$lender_name.'%\'';

    }

    $search = $lender_search;

    

    // var_dump($date_to); die();
    $this->session->set_userdata('lender_search',$search);
    redirect($redirect_url);

  }
  public function close_lender_lender_search()
  {

    $this->session->unset_userdata('lender_search');


    redirect('accounting/lenders');

  }

  public function get_invoice_details($lender_invoice_id)
  {
    $data['lender_invoice_id'] = $lender_invoice_id;
    $this->load->view('lenders/view_lender_invoice', $data);  
  }


  public function get_suppliers_invoice_details($order_id)
  {
    $data['order_id'] = $order_id;
    $this->load->view('lenders/view_supplier_invoice', $data);  
  }
  public function get_payment_details($lender_payment_id)
  {
    $data['lender_payment_id'] = $lender_payment_id;
    $this->load->view('lenders/view_lender_payment', $data);  
  }

  public function delete_lender_invoice($lender_invoice_id)
  {
    $update_query['lender_invoice_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('lender_invoice_id',$lender_invoice_id);
    $this->db->update('lender_invoice',$update_query);

    redirect('accounting/lender-invoices');
  }

  public function edit_lender_invoice($lender_invoice_id)
  {

      $data['title'] = 'Edit lender Invoice';
      $v_data['lender_invoice_id'] = $lender_invoice_id;
      $lender_id = $this->session->userdata('invoice_lender_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('lenders/edit_lender_invoice', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);

  }


  public function delete_lender_payment($lender_payment_id)
  {
    $update_query['lender_payment_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('lender_payment_id',$lender_payment_id);
    $this->db->update('lender_payment',$update_query);

    redirect('accounting/lender-payments');
  }

  public function edit_lender_payment($lender_payment_id)
  {

      $data['title'] = 'Edit lender Invoice';
      $v_data['lender_payment_id'] = $lender_payment_id;
      $lender_id = $this->session->userdata('payment_lender_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('lenders/edit_lender_payment', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);

  }

   public function delete_credit_note_item($lender_credit_note_item_id,$lender_credit_note_id=NULL)
  {
   
    $this->db->where('lender_credit_note_item_id',$lender_credit_note_item_id);
    $this->db->delete('lender_credit_note_item',$update_query);

    if(!empty($lender_credit_note_id))
    {
      redirect('edit-lender-credit-note/'.$lender_credit_note_id);
    }
    else
    {
      redirect('accounting/lender-credit-notes');
    }
  }

  public function delete_lender_credit_note($lender_credit_note_id,$lender_id)
  {


    $update_query['lender_credit_note_status'] = 2;
    $update_query['last_modified_by'] = $this->session->userdata('personnel_id');
    $this->db->where('lender_credit_note_id',$lender_credit_note_id);
    $this->db->update('lender_credit_note',$update_query);

    redirect('accounting/lender-credit-notes');
  }
  public function edit_lender_credit_note($lender_credit_note_id)
  {
     $data['title'] = 'Edit lender Credit Note';
      $v_data['lender_credit_note_id'] = $lender_credit_note_id;
      $lender_id = $this->session->userdata('credit_note_lender_id_searched');
      $v_data['title'] = $data['title'];
      $data['content'] = $this->load->view('lenders/edit_lender_credit_note', $v_data, true);
      $this->load->view('admin/templates/general_page', $data);
  }

  public function get_credit_note_details($lender_credit_note_id)
  {
    $data['lender_credit_note_id'] = $lender_credit_note_id;
    $this->load->view('lenders/view_lender_credit_notes', $data); 
  }

}
?>
