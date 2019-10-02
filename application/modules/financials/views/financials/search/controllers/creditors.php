<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "./application/modules/admin/controllers/admin.php";


class Creditors extends admin
{
	function __construct()
	{
		parent:: __construct();
		$this->load->model('creditors_model');
		$this->load->model('purchases_model');
	}



  public function creditors_invoices()
  {
    // $v_data['property_list'] = $property_list;

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
    redirect('accounting/creditor-bills');
  }

  public function close_searched_invoices_creditor()
  {
    $this->session->unset_userdata('invoice_creditor_id_searched');
    $this->session->unset_userdata('search_creditors_invoice');
    redirect('accounting/creditor-bills');
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

  public function add_invoice_item($creditor_id)
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
      $this->creditors_model->add_invoice_item($creditor_id);
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

  public function confirm_invoice_note($creditor_id)
  {
    $this->form_validation->set_rules('vat_charged', 'tax', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_charged', 'Amount Charged', 'trim|xss_clean');
    $this->form_validation->set_rules('invoice_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_number', 'Invoice Number', 'trim|required|xss_clean');
		if ($this->form_validation->run())
		{
				$this->creditors_model->confirm_creditor_invoice($creditor_id);

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


    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);
  }


  // credit notes

  public function creditors_credit_note()
  {
    // $v_data['property_list'] = $property_list;

    $data['title'] = 'Creditor Credit Notes';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/creditors_credit_notes', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_creditors_credit_notes()
  {

    $creditor_id = $creditor_id_searched = $this->input->post('creditor_id');

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
    redirect('accounting/creditor-credit-notes');
  }

  public function add_credit_note_item($creditor_id)
  {

    $this->form_validation->set_rules('amount', 'Unit Price', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_id', 'Invoice', 'trim|required|xss_clean');
    $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
    $this->form_validation->set_rules('tax_type_id', 'VAT Type', 'trim|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {
      $this->creditors_model->add_credit_note_item($creditor_id);
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

  public function confirm_credit_note($creditor_id)
  {
    $this->form_validation->set_rules('vat_charged', 'tax', 'trim|xss_clean');
		$this->form_validation->set_rules('amount_charged', 'Amount Charged', 'trim|xss_clean');
    $this->form_validation->set_rules('credit_note_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount', 'Amount', 'trim|xss_clean');
    $this->form_validation->set_rules('credit_note_number', 'Invoice Number', 'trim|xss_clean');

		if ($this->form_validation->run())
		{
        // var_dump($_POST);die();
				$this->creditors_model->confirm_creditor_credit_note($creditor_id);

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


    $redirect_url = $this->input->post('redirect_url');
    redirect($redirect_url);
  }

  // creditors payments_import


  public function creditors_payments()
  {
    // $v_data['property_list'] = $property_list;

    $data['title'] = 'Creditor Payments';
    $v_data['title'] = $data['title'];
    $data['content'] = $this->load->view('creditors/creditors_payments', $v_data, true);
    $this->load->view('admin/templates/general_page', $data);
  }


  public function search_creditors_payments()
  {

    $creditor_id = $creditor_id_searched = $this->input->post('creditor_id');

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
    redirect('accounting/creditor-payments');
  }

  public function add_payment_item($creditor_id)
  {

    $this->form_validation->set_rules('amount_paid', 'Unit Price', 'trim|required|xss_clean');
    $this->form_validation->set_rules('invoice_id', 'Invoice', 'trim|required|xss_clean');

    //if form conatins invalid data
    if ($this->form_validation->run())
    {

      $this->creditors_model->add_payment_item($creditor_id);
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

  public function confirm_payment($creditor_id)
  {
    $this->form_validation->set_rules('reference_number', 'Reference Number', 'trim|required|xss_clean');
    $this->form_validation->set_rules('payment_date', 'Invoice Date ', 'trim|required|xss_clean');
    $this->form_validation->set_rules('amount_paid', 'Amount', 'trim|required|xss_clean');
    $this->form_validation->set_rules('account_from_id', 'Payment Amount', 'trim|required|xss_clean');

    if ($this->form_validation->run())
    {
        // var_dump($_POST);die();
        $this->creditors_model->confirm_creditor_payment($creditor_id);

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
          redirect('accounting/creditor-bills');
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
          redirect('accounting/creditor-bills');
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
        redirect('accounting/creditor-bills');
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


}
?>
