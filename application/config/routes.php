<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "auth";
$route['404_override'] = '';

/*
*	Auth Routes
*/
$route['login'] = 'auth/login_user';
$route['logout-admin'] = 'auth/logout';

/*
*	Admin Routes
*/
$route['dashboard'] = 'admin/dashboard';
$route['my-profile'] = 'admin/profile';
$route['online-diary'] = 'admin/calendar';
$route['change-password'] = 'admin/users/change_password';

/*
*	administration Routes
*/
$route['administration/configuration'] = 'admin/configuration';
$route['administration/edit-configuration/(:num)'] = 'admin/edit_configuration/$1';

$route['administration/sections'] = 'admin/sections/index';
$route['administration/sections/(:any)/(:any)/(:num)'] = 'admin/sections/index/$1/$2/$3';
$route['administration/add-section'] = 'admin/sections/add_section';
$route['administration/edit-section/(:num)'] = 'admin/sections/edit_section/$1';
$route['administration/edit-section/(:num)/(:num)'] = 'admin/sections/edit_section/$1/$2';
$route['administration/delete-section/(:num)'] = 'admin/sections/delete_section/$1';
$route['administration/delete-section/(:num)/(:num)'] = 'admin/sections/delete_section/$1/$2';
$route['administration/activate-section/(:num)'] = 'admin/sections/activate_section/$1';
$route['administration/activate-section/(:num)/(:num)'] = 'admin/sections/activate_section/$1/$2';
$route['administration/deactivate-section/(:num)'] = 'admin/sections/deactivate_section/$1';
$route['administration/deactivate-section/(:num)/(:num)'] = 'admin/sections/deactivate_section/$1/$2';

#$route['administration/company-profile'] = 'admin/contacts/show_contacts';
$route['administration/branches'] = 'admin/branches/index';
$route['administration/branches/(:any)/(:any)/(:num)'] = 'admin/branches/index/$1/$2/$3';
$route['administration/branches/(:any)/(:any)'] = 'admin/branches/index/$1/$2';
$route['administration/add-branch'] = 'admin/branches/add_branch';
$route['administration/edit-branch/(:num)'] = 'admin/branches/edit_branch/$1';
$route['administration/edit-branch/(:num)/(:num)'] = 'admin/branches/edit_branch/$1/$2';
$route['administration/delete-branch/(:num)'] = 'admin/branches/delete_branch/$1';
$route['administration/delete-branch/(:num)/(:num)'] = 'admin/branches/delete_branch/$1/$2';
$route['administration/activate-branch/(:num)'] = 'admin/branches/activate_branch/$1';
$route['administration/activate-branch/(:num)/(:num)'] = 'admin/branches/activate_branch/$1/$2';
$route['administration/deactivate-branch/(:num)'] = 'admin/branches/deactivate_branch/$1';
$route['administration/deactivate-branch/(:num)/(:num)'] = 'admin/branches/deactivate_branch/$1/$2';

/*
*	HR Routes
*/
$route['human-resource/schedules'] = 'hr/schedules/index';
$route['human-resource/delete-schedule/(:num)'] = 'hr/schedules/delete_schedule/$1';
$route['human-resource/delete-schedule/(:num)/(:num)'] = 'hr/schedules/delete_schedule/$1/$2';
$route['human-resource/activate-schedule/(:num)'] = 'hr/schedules/activate_schedule/$1';
$route['human-resource/activate-schedule/(:num)/(:num)'] = 'hr/schedules/activate_schedule/$1/$2';
$route['human-resource/deactivate-schedule/(:num)'] = 'hr/schedules/deactivate_schedule/$1';
$route['human-resource/deactivate-schedule/(:num)/(:num)'] = 'hr/schedules/deactivate_schedule/$1/$2';
$route['human-resource/schedule-personnel/(:num)'] = 'hr/schedules/schedule_personnel/$1';
$route['human-resource/fill-timesheet/(:num)/(:num)'] = 'hr/schedules/fill_timesheet/$1/$2';
$route['human-resource/doctors-schedule'] = 'hr/schedules/doctors_schedule';
$route['human-resource/schedule-personnel/(:num)/(:any)/(:any)/(:num)'] = 'hr/schedules/schedule_personnel/$1/$2/$3/$4';
$route['human-resource/schedule-personnel/(:num)/(:any)/(:any)'] = 'hr/schedules/schedule_personnel/$1/$2/$3';
$route['human-resource/schedules/(:any)/(:any)/(:num)'] = 'hr/schedules/index/$1/$2/$3';
$route['human-resource/schedules/(:any)/(:any)'] = 'hr/schedules/index/$1/$2';

$route['human-resource/my-account'] = 'admin/dashboard';
$route['human-resource/my-account/edit-about/(:num)'] = 'hr/personnel/my_account/update_personnel_about_details/$1';
$route['human-resource/edit-personnel-account/(:num)'] = 'hr/personnel/update_personnel_account_details/$1';
$route['human-resource/configuration'] = 'hr/configuration';
$route['human-resource/add-job-title'] = 'hr/add_job_title';
$route['human-resource/edit-job-title/(:num)'] = 'hr/edit_job_title/$1';
$route['human-resource/delete-job-title/(:num)'] = 'hr/delete_job_title/$1';
$route['human-resource/personnel'] = 'hr/personnel/index';
$route['human-resource/personnel/(:any)/(:any)/(:num)'] = 'hr/personnel/index/$1/$2/$3';
$route['human-resource/add-personnel'] = 'hr/personnel/add_personnel';
$route['human-resource/edit-personnel/(:num)'] = 'hr/personnel/edit_personnel/$1';
$route['human-resource/edit-store-authorize/(:num)'] = 'hr/personnel/edit_store_authorize/$1';
$route['human-resource/edit-order-authorize/(:num)'] = 'hr/personnel/edit_order_authorize/$1';

$route['human-resource/edit-personnel-about/(:num)'] = 'hr/personnel/update_personnel_about_details/$1';
$route['human-resource/edit-personnel-account/(:num)'] = 'hr/personnel/update_personnel_account_details/$1';
$route['human-resource/edit-personnel/(:num)/(:num)'] = 'hr/personnel/edit_personnel/$1/$2';
$route['human-resource/delete-personnel/(:num)'] = 'hr/personnel/delete_personnel/$1';
$route['human-resource/delete-personnel/(:num)/(:num)'] = 'hr/personnel/delete_personnel/$1/$2';
$route['human-resource/activate-personnel/(:num)'] = 'hr/personnel/activate_personnel/$1';
$route['human-resource/activate-personnel/(:num)/(:num)'] = 'hr/personnel/activate_personnel/$1/$2';
$route['human-resource/deactivate-personnel/(:num)'] = 'hr/personnel/deactivate_personnel/$1';
$route['human-resource/deactivate-personnel/(:num)/(:num)'] = 'hr/personnel/deactivate_personnel/$1/$2';
$route['human-resource/reset-password/(:num)'] = 'hr/personnel/reset_password/$1';
$route['human-resource/update-personnel-roles/(:num)'] = 'hr/personnel/update_personnel_roles/$1';
$route['human-resource/add-emergency-contact/(:num)'] = 'hr/personnel/add_emergency_contact/$1';
$route['human-resource/activate-emergency-contact/(:num)/(:num)'] = 'hr/personnel/activate_emergency_contact/$1/$2';
$route['human-resource/deactivate-emergency-contact/(:num)/(:num)'] = 'hr/personnel/deactivate_emergency_contact/$1/$2';
$route['human-resource/delete-emergency-contact/(:num)/(:num)'] = 'hr/personnel/delete_emergency_contact/$1/$2';

$route['human-resource/add-dependant-contact/(:num)'] = 'hr/personnel/add_dependant_contact/$1';
$route['human-resource/activate-dependant-contact/(:num)/(:num)'] = 'hr/personnel/activate_dependant_contact/$1/$2';
$route['human-resource/deactivate-dependant-contact/(:num)/(:num)'] = 'hr/personnel/deactivate_dependant_contact/$1/$2';
$route['human-resource/delete-dependant-contact/(:num)/(:num)'] = 'hr/personnel/delete_dependant_contact/$1/$2';

$route['human-resource/add-personnel-job/(:num)'] = 'hr/personnel/add_personnel_job/$1';
$route['human-resource/activate-personnel-job/(:num)/(:num)'] = 'hr/personnel/activate_personnel_job/$1/$2';
$route['human-resource/deactivate-personnel-job/(:num)/(:num)'] = 'hr/personnel/deactivate_personnel_job/$1/$2';
$route['human-resource/delete-personnel-job/(:num)/(:num)'] = 'hr/personnel/delete_personnel_job/$1/$2';

$route['human-resource/leave'] = 'hr/leave/calender';
$route['human-resource/leave/(:any)/(:any)'] = 'hr/leave/calender/$1/$2';
$route['human-resource/view-leave/(:any)'] = 'hr/leave/view_leave/$1';
$route['human-resource/add-personnel-leave/(:num)'] = 'hr/personnel/add_personnel_leave/$1';
$route['human-resource/add-personnel-leave/(:num)/(:num)'] = 'hr/personnel/add_personnel_leave/$1/$2';
$route['human-resource/add-leave/(:any)'] = 'hr/leave/add_leave/$1';
$route['human-resource/add-calender-leave'] = 'hr/leave/add_calender_leave';
$route['human-resource/activate-leave/(:num)/(:any)'] = 'hr/leave/activate_leave/$1/$2';
$route['human-resource/deactivate-leave/(:num)/(:any)'] = 'hr/leave/deactivate_leave/$1/$2';
$route['human-resource/delete-leave/(:num)/(:any)'] = 'hr/leave/delete_leave/$1/$2';
$route['human-resource/activate-personnel-leave/(:num)/(:num)'] = 'hr/personnel/activate_personnel_leave/$1/$2';
$route['human-resource/activate-personnel-leave/(:num)/(:num)/(:num)'] = 'hr/personnel/activate_personnel_leave/$1/$2/$3';
$route['human-resource/deactivate-personnel-leave/(:num)/(:num)'] = 'hr/personnel/deactivate_personnel_leave/$1/$2';
$route['human-resource/deactivate-personnel-leave/(:num)/(:num)/(:num)'] = 'hr/personnel/deactivate_personnel_leave/$1/$2/$3';
$route['human-resource/delete-personnel-leave/(:num)/(:num)'] = 'hr/personnel/delete_personnel_leave/$1/$2';
$route['human-resource/delete-personnel-leave/(:num)/(:num)/(:num)'] = 'hr/personnel/delete_personnel_leave/$1/$2/$3';
$route['human-resource/personnel-leave-detail/(:num)'] = 'hr/leave/personnel_leaves/$1';



$route['human-resource/delete-personnel-role/(:num)/(:num)'] = 'hr/personnel/delete_personnel_role/$1/$2';

/*
*	Hospital administration
*/
$route['hospital-administration/import-pharmacy-charges/(:num)'] = 'hospital_administration/services/import_pharmacy_charges/$1';
$route['hospital-administration/import-lab-charges/(:num)'] = 'hospital_administration/services/import_lab_charges/$1';
$route['hospital-administration/dashboard'] = 'administration/index';
$route['hospital-administration/services'] = 'hospital_administration/services/index';
$route['hospital-administration/services/(:any)/(:any)/(:num)'] = 'hospital_administration/services/index/$1/$2/$3';
$route['hospital-administration/services/(:any)/(:any)'] = 'hospital_administration/services/index/$1/$2';
$route['hospital-administration/add-service'] = 'hospital_administration/services/add_service';
$route['hospital-administration/edit-service/(:num)'] = 'hospital_administration/services/edit_service/$1';
$route['hospital-administration/edit-service/(:num)/(:num)'] = 'hospital_administration/services/edit_service/$1/$2';
$route['hospital-administration/delete-service/(:num)'] = 'hospital_administration/services/delete_service/$1';
$route['hospital-administration/delete-service/(:num)/(:num)'] = 'hospital_administration/services/delete_service/$1/$2';
$route['hospital-administration/activate-service/(:num)'] = 'hospital_administration/services/activate_service/$1';
$route['hospital-administration/activate-service/(:num)/(:num)'] = 'hospital_administration/services/activate_service/$1/$2';
$route['hospital-administration/deactivate-service/(:num)'] = 'hospital_administration/services/deactivate_service/$1';
$route['hospital-administration/deactivate-service/(:num)/(:num)'] = 'hospital_administration/services/deactivate_service/$1/$2';
$route['hospital-administration/import-services-template'] = 'hospital_administration/services/import_charges_template';
$route['hospital-administration/import-services/(:num)'] = 'hospital_administration/services/do_charges_import/$1';
$route['hospital-administration/import-charges/(:num)'] = 'hospital_administration/services/import_charges/$1';

$route['hospital-administration/service-charges/(:num)'] = 'hospital_administration/services/service_charges/$1';
$route['hospital-administration/service-charges/(:num)/(:any)/(:any)/(:num)'] = 'hospital_administration/services/service_charges/$1/$2/$3/$4';
$route['hospital-administration/service-charges/(:num)/(:any)/(:any)'] = 'hospital_administration/services/service_charges/$1/$2/$3';
$route['hospital-administration/add-service-charge/(:num)'] = 'hospital_administration/services/add_service_charge/$1';
$route['hospital-administration/edit-service-charge/(:num)/(:num)'] = 'hospital_administration/services/edit_service_charge/$1/$2';
$route['hospital-administration/delete-service-charge/(:num)/(:num)'] = 'hospital_administration/services/delete_service_charge/$1/$2';
$route['hospital-administration/activate-service-charge/(:num)/(:num)'] = 'hospital_administration/services/activate_service_charge/$1/$2';
$route['hospital-administration/deactivate-service-charge/(:num)/(:num)'] = 'hospital_administration/services/deactivate_service_charge/$1/$2';

$route['hospital-administration/visit-types'] = 'hospital_administration/visit_types/index';
$route['hospital-administration/visit-types/(:any)/(:any)/(:num)'] = 'hospital_administration/visit_types/index/$1/$2/$3';
$route['hospital-administration/visit-types/(:any)/(:any)'] = 'hospital_administration/visit_types/index/$1/$2';
$route['hospital-administration/add-visit-type'] = 'hospital_administration/visit_types/add_visit_type';
$route['hospital-administration/edit-visit-type/(:num)'] = 'hospital_administration/visit_types/edit_visit_type/$1';
$route['hospital-administration/delete-visit-type/(:num)'] = 'hospital_administration/visit_types/delete_visit_type/$1';
$route['hospital-administration/activate-visit-type/(:num)'] = 'hospital_administration/visit_types/activate_visit_type/$1';
$route['hospital-administration/deactivate-visit-type/(:num)'] = 'hospital_administration/visit_types/deactivate_visit_type/$1';



$route['hospital-administration/insurance-scheme'] = 'hospital_administration/insurance_scheme/index';
$route['hospital-administration/insurance-scheme/(:any)/(:any)/(:num)'] = 'hospital_administration/insurance_scheme/index/$1/$2/$3';
$route['hospital-administration/insurance-scheme/(:any)/(:any)'] = 'hospital_administration/insurance_scheme/index/$1/$2';
$route['hospital-administration/add-insurance-scheme'] = 'hospital_administration/insurance_scheme/add_insurance_scheme';
$route['hospital-administration/edit-insurance-scheme/(:num)'] = 'hospital_administration/insurance_scheme/edit_insurance_scheme/$1';
$route['hospital-administration/delete-insurance-scheme/(:num)'] = 'hospital_administration/insurance_scheme/delete_insurance_scheme/$1';
$route['hospital-administration/activate-insurance-scheme/(:num)'] = 'hospital_administration/insurance_scheme/activate_insurance_scheme/$1';
$route['hospital-administration/deactivate-insurance-scheme/(:num)'] = 'hospital_administration/insurance_scheme/deactivate_visit_type/$1';



$route['hospital-administration/departments'] = 'hospital_administration/departments/index';
$route['hospital-administration/departments/(:any)/(:any)/(:num)'] = 'hospital_administration/departments/index/$1/$2/$3';
$route['hospital-administration/departments/(:any)/(:any)'] = 'hospital_administration/departments/index/$1/$2';
$route['hospital-administration/department-accounts/(:num)'] = 'hospital_administration/departments/department_accounts/$1';
$route['hospital-administration/department-accounts/(:num)/(:num)'] = 'hospital_administration/departments/department_accounts/$1/$2';
$route['delete-department-account/(:num)/(:num)'] = 'hospital_administration/departments/delete_department_account/$1/$2';
$route['hospital-administration/add-department'] = 'hospital_administration/departments/add_department';
$route['hospital-administration/edit-department/(:num)'] = 'hospital_administration/departments/edit_department/$1';
$route['hospital-administration/delete-department/(:num)'] = 'hospital_administration/departments/delete_department/$1';
$route['hospital-administration/activate-department/(:num)'] = 'hospital_administration/departments/activate_department/$1';
$route['hospital-administration/deactivate-department/(:num)'] = 'hospital_administration/departments/deactivate_department/$1';



$route['hospital-administration/department-accounts'] = 'hospital_administration/department_accounts/index';
$route['hospital-administration/department-accounts/(:any)/(:any)/(:num)'] = 'hospital_administration/department_accounts/index/$1/$2/$3';
$route['hospital-administration/department-accounts/(:any)/(:any)'] = 'hospital_administration/department_accounts/index/$1/$2';
$route['hospital-administration/add-department-account'] = 'hospital_administration/department_accounts/add_department_account';
$route['hospital-administration/edit-department-accounts/(:num)'] = 'hospital_administration/department_accounts/edit_department_account/$1';
$route['hospital-administration/edit-department-accounts/(:num)/(:num)'] = 'hospital_administration/department_accounts/edit_department_account/$1/$2';
$route['hospital-administration/delete-department-accounts/(:num)'] = 'hospital_administration/department_accounts/delete_department_account/$1';
$route['hospital-administration/delete-department-accounts/(:num)/(:num)'] = 'hospital_administration/department_accounts/delete_department_account/$1/$2';
$route['hospital-administration/activate-department-accounts/(:num)'] = 'hospital_administration/department_accounts/activate_department_account/$1';
$route['hospital-administration/activate-department-accounts/(:num)/(:num)'] = 'hospital_administration/department_accounts/activate_department_account/$1/$2';
$route['hospital-administration/deactivate-department-accounts/(:num)'] = 'hospital_administration/department_accounts/deactivate_department_account/$1';
$route['hospital-administration/deactivate-department-accounts/(:num)/(:num)'] = 'hospital_administration/department_accounts/deactivate_department_account/$1/$2';



$route['hospital-administration/wards'] = 'hospital_administration/wards/index';
$route['hospital-administration/wards/(:any)/(:any)/(:num)'] = 'hospital_administration/wards/index/$1/$2/$3';
$route['hospital-administration/wards/(:any)/(:any)'] = 'hospital_administration/wards/index/$1/$2';
$route['hospital-administration/add-ward'] = 'hospital_administration/wards/add_ward';
$route['hospital-administration/edit-ward/(:num)'] = 'hospital_administration/wards/edit_ward/$1';
$route['hospital-administration/delete-ward/(:num)'] = 'hospital_administration/wards/delete_ward/$1';
$route['hospital-administration/activate-ward/(:num)'] = 'hospital_administration/wards/activate_ward/$1';
$route['hospital-administration/deactivate-ward/(:num)'] = 'hospital_administration/wards/deactivate_ward/$1';

$route['hospital-administration/rooms/(:num)'] = 'hospital_administration/rooms/index/$1';
$route['hospital-administration/rooms/(:num)/(:any)/(:any)/(:num)'] = 'hospital_administration/rooms/index/$1/$2/$3/$4';
$route['hospital-administration/rooms/(:num)/(:any)/(:any)'] = 'hospital_administration/rooms/index/$1/$2/$3';
$route['hospital-administration/add-room/(:num)'] = 'hospital_administration/rooms/add_room/$1';
$route['hospital-administration/edit-room/(:num)/(:num)'] = 'hospital_administration/rooms/edit_room/$1/$2';
$route['hospital-administration/delete-room/(:num)/(:num)'] = 'hospital_administration/rooms/delete_room/$1/$2';
$route['hospital-administration/activate-room/(:num)/(:num)'] = 'hospital_administration/rooms/activate_room/$1/$2';
$route['hospital-administration/deactivate-room/(:num)/(:num)'] = 'hospital_administration/rooms/deactivate_room/$1/$2';

$route['hospital-administration/beds/(:num)'] = 'hospital_administration/beds/index/$1';
$route['hospital-administration/beds/(:num)/(:any)/(:any)/(:num)'] = 'hospital_administration/beds/index/$1/$2/$3/$4';
$route['hospital-administration/beds/(:num)/(:any)/(:any)'] = 'hospital_administration/beds/index/$1/$2/$3';
$route['hospital-administration/add-bed/(:num)'] = 'hospital_administration/beds/add_bed/$1';
$route['hospital-administration/edit-bed/(:num)/(:num)'] = 'hospital_administration/beds/edit_bed/$1/$2';
$route['hospital-administration/delete-bed/(:num)/(:num)'] = 'hospital_administration/beds/delete_bed/$1/$2';
$route['hospital-administration/activate-bed/(:num)/(:num)'] = 'hospital_administration/beds/activate_bed/$1/$2';
$route['hospital-administration/deactivate-bed/(:num)/(:num)'] = 'hospital_administration/beds/deactivate_bed/$1/$2';

$route['hospital-administration/insurance-companies'] = 'hospital_administration/companies/index';
$route['hospital-administration/insurance-companies/(:any)/(:any)/(:num)'] = 'hospital_administration/companies/index/$1/$2/$3';
$route['hospital-administration/insurance-companies/(:any)/(:any)'] = 'hospital_administration/companies/index/$1/$2';
$route['hospital-administration/add-insurance-company'] = 'hospital_administration/companies/add_company';
$route['hospital-administration/edit-insurance-company/(:num)'] = 'hospital_administration/companies/edit_company/$1';
$route['hospital-administration/delete-insurance-company/(:num)'] = 'hospital_administration/companies/delete_company/$1';
$route['hospital-administration/activate-insurance-company/(:num)'] = 'hospital_administration/companies/activate_company/$1';
$route['hospital-administration/deactivate-insurance-company/(:num)'] = 'hospital_administration/companies/deactivate_company/$1';
$route['hospital-administration/update-charges/(:num)'] = 'hospital_administration/update_service_charges/$1';

//payroll data import
$route['hospital-administration/import-invoices'] = 'hospital_administration/import_invoices';
$route['hospital-administration/import-invoices-template'] = 'hospital_administration/import_invoices_template';
$route['hospital-administration/import-invoices-values']= 'hospital_administration/do_invoice_import';


$route['hospital-administration/import-payments'] = 'hospital_administration/import_payments';
$route['hospital-administration/import-payments-template'] = 'hospital_administration/import_payments_template';
$route['hospital-administration/import-payments-values']= 'hospital_administration/do_payment_import';


$route['hospital-administration/import-patients-data'] = 'hospital_administration/import_patients_update';
$route['hospital-administration/import-patients-data-template'] = 'hospital_administration/import_patients_template';
$route['hospital-administration/import-patients-data-values']= 'hospital_administration/do_patients_update_import';



$route['hospital-administration/export-charges/(:num)'] = 'hospital_administration/services/export_charges/$1';
$route['inventory/deduct-product/(:num)/(:num)'] = 'inventory_management/deduct_product/$1/$2';

/*
*	Accounts Routes
*/
$route['accounts/creditors'] = 'accounts/creditors/index';
//$route['accounts/creditors/(:num)'] = 'accounts/creditors/delete_creditor/$1'
$route['accounting/hospital-accounts'] = 'accounting/hospital_accounts/index';
$route['accounting/petty-cash'] = 'accounting/petty_cash/index';
$route['accounting/petty-cash/(:any)/(:any)'] = 'accounting/petty_cash/index/$1/$2';
$route['accounting/petty-cash/(:any)'] = 'accounting/petty_cash/index/$1';
$route['delete-invoice-entry/(:num)'] = 'accounting/petty_cash/delete_invoice_entry/$1';
$route['delete-invoice-ledger-entry/(:num)'] = 'accounting/petty_cash/delete_invoice_ledger_entry/$1';
$route['delete-payment-entry/(:num)'] = 'accounting/petty_cash/delete_payment_entry/$1';
$route['delete-payment-ledger-entry/(:num)'] = 'accounting/petty_cash/delete_payment_ledger_entry/$1';


$route['accounts/change-branch'] = 'accounts/payroll/change_branch';
$route['accounts/print-payroll/(:num)'] = 'accounts/payroll/print_payroll/$1';
$route['accounts/export-payroll/(:num)'] = 'accounts/payroll/export_payroll/$1';
$route['accounts/print-payroll-pdf/(:num)'] = 'accounts/payroll/print_payroll_pdf/$1';
$route['accounts/payroll/print-payslip/(:num)/(:num)'] = 'accounts/payroll/print_payslip/$1/$2';
$route['accounts/payroll/download-payslip/(:num)/(:num)'] = 'accounts/payroll/download_payslip/$1/$2';
$route['accounts/payroll-payslips/(:num)'] = 'accounts/payroll/payroll_payslips/$1';
$route['accounts/salary-data'] = 'accounts/payroll/salaries';
$route['accounts/search-payroll'] = 'accounts/payroll/search_payroll';
$route['accounts/close-payroll-search'] = 'accounts/payroll/close_payroll_search';
$route['accounts/create-payroll'] = 'accounts/payroll/create_payroll';
$route['accounts/deactivate-payroll/(:num)'] = 'accounts/payroll/deactivate_payroll/$1';
$route['accounts/print-payslips'] = 'accounts/payroll/print_payslips';
$route['accounts/payroll/edit-payment-details/(:num)'] = 'accounts/payroll/edit_payment_details/$1';
$route['accounts/payroll/edit_allowance/(:num)'] = 'accounts/payroll/edit_allowance/$1';
$route['accounts/payroll/delete_allowance/(:num)'] = 'accounts/payroll/delete_allowance/$1';
$route['accounts/payroll/edit_deduction/(:num)'] = 'accounts/payroll/edit_deduction/$1';
$route['accounts/payroll/delete_deduction/(:num)'] = 'accounts/payroll/delete_deduction/$1';
$route['accounts/payroll/edit_saving/(:num)'] = 'accounts/payroll/edit_saving/$1';
$route['accounts/payroll/delete_saving/(:num)'] = 'accounts/payroll/delete_saving/$1';
$route['accounts/payroll/edit_loan_scheme/(:num)'] = 'accounts/payroll/edit_loan_scheme/$1';
$route['accounts/payroll/delete_loan_scheme/(:num)'] = 'accounts/payroll/delete_loan_scheme/$1';
$route['accounts/payroll'] = 'accounts/payroll/payrolls';
$route['accounts/payment-details/(:num)'] = 'accounts/payroll/payment_details/$1';
$route['accounts/save-payment-details/(:num)'] = 'accounts/payroll/save_payment_details/$1';
$route['accounts/update-savings/(:num)'] = 'accounts/payroll/update_savings/$1';
$route['accounts/update-loan-schemes/(:num)'] = 'accounts/payroll/update_loan_schemes/$1';
$route['payroll/configuration'] = 'accounts/payroll/payroll_configuration';
$route['accounts/payroll-configuration'] = 'accounts/payroll/payroll_configuration';
$route['accounts/payroll/edit-nssf/(:num)'] = 'accounts/payroll/edit_nssf/$1';
$route['accounts/payroll/edit-nhif/(:num)'] = 'accounts/payroll/edit_nhif/$1';
$route['accounts/payroll/delete-nhif/(:num)'] = 'accounts/payroll/delete_nhif/$1';
$route['accounts/payroll/edit-paye/(:num)'] = 'accounts/payroll/edit_paye/$1';
$route['accounts/payroll/delete-paye/(:num)'] = 'accounts/payroll/delete_paye/$1';
$route['accounts/payroll/edit-payment/(:num)'] = 'accounts/payroll/edit_payment/$1';
$route['accounts/payroll/delete-payment/(:num)'] = 'accounts/payroll/delete_payment/$1';
$route['accounts/payroll/edit-benefit/(:num)'] = 'accounts/payroll/edit_benefit/$1';
$route['accounts/payroll/delete-benefit/(:num)'] = 'accounts/payroll/delete_benefit/$1';
$route['accounts/payroll/edit-allowance/(:num)'] = 'accounts/payroll/edit_allowance/$1';
$route['accounts/payroll/delete-allowance/(:num)'] = 'accounts/payroll/delete_allowance/$1';
$route['accounts/payroll/edit-deduction/(:num)'] = 'accounts/payroll/edit_deduction/$1';
$route['accounts/payroll/edit-relief/(:num)'] = 'accounts/payroll/edit_relief/$1';
$route['accounts/payroll/delete-deduction/(:num)'] = 'accounts/payroll/delete_deduction/$1';
$route['accounts/payroll/edit-other-deduction/(:num)'] = 'accounts/payroll/edit_other_deduction/$1';
$route['accounts/payroll/delete-other-deduction/(:num)'] = 'accounts/payroll/delete_other_deduction/$1';
$route['accounts/payroll/edit-loan-scheme/(:num)'] = 'accounts/payroll/edit_loan_scheme/$1';
$route['accounts/payroll/delete-loan-scheme/(:num)'] = 'accounts/payroll/delete_loan_scheme/$1';
$route['accounts/payroll/edit-saving/(:num)'] = 'accounts/payroll/edit_saving/$1';
$route['accounts/payroll/delete-saving/(:num)'] = 'accounts/payroll/delete_saving/$1';
$route['accounts/payroll/edit-personnel-payments/(:num)'] = 'accounts/payroll/edit_personnel_payments/$1';
$route['accounts/payroll/edit-personnel-allowances/(:num)'] = 'accounts/payroll/edit_personnel_allowances/$1';
$route['accounts/payroll/edit-personnel-benefits/(:num)'] = 'accounts/payroll/edit_personnel_benefits/$1';
$route['accounts/payroll/edit-personnel-deductions/(:num)'] = 'accounts/payroll/edit_personnel_deductions/$1';
$route['accounts/payroll/edit-personnel-other-deductions/(:num)'] = 'accounts/payroll/edit_personnel_other_deductions/$1';
$route['accounts/payroll/edit-personnel-savings/(:num)'] = 'accounts/payroll/edit_personnel_savings/$1';
$route['accounts/payroll/edit-personnel-loan-schemes/(:num)'] = 'accounts/payroll/edit_personnel_loan_schemes/$1';
$route['accounts/payroll/edit-personnel-relief/(:num)'] = 'accounts/payroll/edit_personnel_relief/$1';
$route['accounts/payroll/view-payslip/(:num)'] = 'accounts/payroll/view_payslip/$1';

$route['accounts/insurance-invoices'] = 'administration/reports/debtors_report_invoices/0';
$route['accounts/insurance-invoices/(:num)'] = 'administration/reports/debtors_report_invoices/$1';

//Always comes last
$route['accounts/payroll/(:any)/(:any)'] = 'accounts/payroll/payrolls/$1/$2';
$route['accounts/payroll/(:any)/(:any)/(:num)'] = 'accounts/payroll/payrolls/$1/$2/$3';
$route['accounts/salary-data/(:any)/(:any)'] = 'accounts/payroll/salaries/$1/$2';
$route['accounts/salary-data/(:any)/(:any)/(:num)'] = 'accounts/payroll/salaries/$1/$2/$3';




$route['accounts/insurance-invoices'] = 'administration/reports/debtors_report_invoices/0';
$route['accounts/insurance-invoices/(:num)'] = 'administration/reports/debtors_report_invoices/$1';

$route['hospital-reports/insurance-invoices'] = 'administration/reports/debtors_report_invoices/0';
$route['hospital-reports/insurance-invoices/(:num)'] = 'administration/reports/debtors_report_invoices/$1';
$route['hospital-reports/providers-report'] = 'administration/reports/providers_report';
$route['hospital-reports/providers-report/(:num)'] = 'administration/reports/providers_report/$1';
$route['provider-cash-report/(:num)'] = 'administration/reports/provider_report_export/$1/1';
$route['provider-insurance-report/(:num)'] = 'administration/reports/provider_report_export/$1/2';
$route['close-providers-search'] = 'administration/reports/close_providers_search';




$route['payroll/add-overtime-hours/(:num)'] = 'accounts/payroll/add_overtime_hours/$1';
$route['accounts/create-data-file/(:num)/(:num)'] = 'accounts/payroll/create_data_file/$1/$2';

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */
//import personnel routes
$route['import/personnel'] = 'hr/personnel/import_personnel';
$route['import/personnel-template'] = 'hr/personnel/import_personnel_template';
$route['import/import-personnel'] = 'hr/personnel/do_personnel_import';

//import personnel emails
$route['import/personnel-emails'] = 'hr/personnel/import_personnel_emails';
$route['import/personnel-emails-template'] = 'hr/personnel/import_personnel_emails_template';
$route['import/import-personnel-emails'] = 'hr/personnel/do_personnel_emails_import';

//import branches routes
$route['import/branches'] = 'admin/branches/import_branches';
$route['import/branches-template'] = 'admin/branches/import_branches_template';
$route['import/import-branches'] = 'admin/branches/do_branches_import';

//payroll data import
$route['import/payroll-data'] = 'hr/import_payroll';
$route['import/payroll-template'] = 'hr/import_payroll_template';
$route['import/import-payroll']= 'hr/do_payroll_import';



//import salary advances
$route['salary-advance/import-salary-advance'] = 'accounts/salary_advance/import_salary_advance';
$route['import/import-salary-advances'] = 'accounts/salary_advance/do_advance_import';
$route['import/advance-template'] = 'accounts/salary_advance/advances_template';
$route['download-salary-advance'] = 'accounts/salary_advance/download_salary_advance';

// p9 form
$route['my-account/p9'] = 'accounts/payroll/generate_p9_form';
$route['accounts/generate_p9_form'] = 'accounts/payroll/p9_form';

//p10 form
$route['accounts/p10'] = 'accounts/payroll/generate_p10_form';
$route['accounts/generate_p10_form'] = 'accounts/payroll/p10_form';

//timesheets
$route['timesheets/add-timesheet'] = 'hr/personnel/add_personnel_timesheet';

//bank reports
$route['accounts/bank'] = 'accounts/payroll/bank';
$route['accounts/generate-bank-report/(:num)'] = 'accounts/payroll/generate_bank_report/$1';

//salary advances
$route['salary-advance'] = 'accounts/salary_advance/index';
$route['accounts/search-advances'] = 'accounts/salary_advance/search_salary_advance';
$route['close-salary-advance-search'] = 'accounts/salary_advance/close_advance_search';
$route['salary-advance/(:any)/(:any)'] = 'accounts/salary_advance/index/$1/$2';


$route['hospital-reports/insurance-report'] = 'administration/reports/insurance_report';
$route['hospital-reports/insurance-report/(:num)'] = 'administration/reports/insurance_report/$1';

//payroll reports routes
$route['accounts/payroll-reports'] = 'accounts/payroll/payroll_report';
$route['accounts/search-payroll-reports'] = 'accounts/payroll/search_payroll_reports';

//import overtime-hours
$route['import/overtime'] = 'accounts/payroll/import_overtime';
$route['import/overtime-template'] = 'accounts/payroll/import_overtime_template';
$route['import/import-overtime'] = 'accounts/payroll/do_overtime_import';

//send payslips to the specific personnel
$route['accounts/send-month-payslips/(:num)'] = 'accounts/payroll/send_monthly_payslips/$1';
$route['accounts/payroll/access-payslip/(:num)/(:num)'] = 'accounts/payroll/access_payslip/$1/$2';

//consultant routes
$route['accounts/cc-payment'] = 'accounts/cc_payment/index';
$route['accounts/change-cc-branch'] = 'accounts/cc_payment/change_branch';
$route['accounts/create-cc-payment'] = 'accounts/cc_payment/create_cc_payment';
$route['accounts/print-cc-paye-report/(:num)'] = 'accounts/cc_payment/print_paye_report/$1';
$route['accounts/print-cc-payment/(:num)'] = 'accounts/cc_payment/print_cc_payment/$1';
$route['accounts/print-cc-month-summary/(:num)/(:num)'] = 'accounts/cc_payment/month_summary/$1/$2';
$route['accounts/print-cc-month-payslips/(:num)'] = 'accounts/cc_payment/print_monthly_payslips/$1';
$route['accounts/search-cc-payment'] = 'accounts/cc_payment/search_cc_payment';
$route['accounts/close-cc-payment-search'] = 'accounts/cc_payment/close_cc_payment_search';
$route['account/cc-salary-data'] = 'accounts/cc_payment/salaries';
$route['accounts/cc-payment/view-payslip/(:num)'] = 'accounts/cc_payment/view_payslip/$1';
$route['accounts/cc-payment-details/(:num)'] = 'accounts/cc_payment/payment_details/$1';

//Always comes last
$route['accounts/payroll/(:any)/(:any)'] = 'accounts/payroll/payrolls/$1/$2';
$route['accounts/payroll/(:any)/(:any)/(:num)'] = 'accounts/payroll/payrolls/$1/$2/$3';
$route['accounts/salary-data/(:any)/(:any)'] = 'accounts/payroll/salaries/$1/$2';
$route['accounts/salary-data/(:any)/(:any)/(:num)'] = 'accounts/payroll/salaries/$1/$2/$3';
$route['accounts/print-month-summary/(:num)/(:num)'] = 'accounts/payroll/month_summary/$1/$2';

/*
*	Inventory Routes
*/
$route['inventory/units-of-measurement'] = 'inventory/unit/index';
$route['inventory/units-of-measurement/(:any)/(:any)/(:num)'] = 'inventory/unit/index/$1/$2/$3';
$route['inventory/add-personnel'] = 'inventory/personnel/add_personnel';
$route['inventory/edit-personnel/(:num)'] = 'inventory/personnel/edit_personnel/$1';
$route['inventory/edit-personnel/(:num)/(:num)'] = 'inventory/personnel/edit_personnel/$1/$2';
$route['inventory/delete-personnel/(:num)'] = 'inventory/personnel/delete_personnel/$1';
$route['inventory/delete-personnel/(:num)/(:num)'] = 'inventory/personnel/delete_personnel/$1/$2';
$route['inventory/activate-personnel/(:num)'] = 'inventory/personnel/activate_personnel/$1';
$route['inventory/activate-personnel/(:num)/(:num)'] = 'inventory/personnel/activate_personnel/$1/$2';
$route['inventory/deactivate-personnel/(:num)'] = 'inventory/personnel/deactivate_personnel/$1';
$route['inventory/deactivate-personnel/(:num)/(:num)'] = 'inventory/personnel/deactivate_personnel/$1/$2';

/*
*	Microfinance Routes
*/
$route['microfinance/individual'] = 'microfinance/individual/index';
$route['microfinance/individual/(:any)/(:any)/(:num)'] = 'microfinance/individual/index/$1/$2/$3';
$route['microfinance/add-individual'] = 'microfinance/individual/add_individual';
$route['microfinance/edit-individual/(:num)'] = 'microfinance/individual/edit_individual/$1';
$route['microfinance/update-individual/(:num)'] = 'microfinance/individual/edit_about/$1';
$route['microfinance/update-emergency/(:num)'] = 'microfinance/individual/edit_emergency/$1';
$route['microfinance/add-position/(:num)'] = 'microfinance/individual/add_position/$1';
$route['microfinance/add-nok/(:num)'] = 'microfinance/individual/add_emergency/$1';
$route['microfinance/delete-individual/(:num)'] = 'microfinance/individual/delete_individual/$1';
$route['microfinance/delete-individual/(:num)/(:num)'] = 'microfinance/individual/delete_individual/$1/$2';
$route['microfinance/activate-individual/(:num)'] = 'microfinance/individual/activate_individual/$1';
$route['microfinance/activate-individual/(:num)/(:num)'] = 'microfinance/individual/activate_individual/$1/$2';
$route['microfinance/deactivate-individual/(:num)'] = 'microfinance/individual/deactivate_individual/$1';
$route['microfinance/deactivate-individual/(:num)/(:num)'] = 'microfinance/individual/deactivate_individual/$1/$2';
$route['microfinance/activate-position/(:num)/(:num)'] = 'microfinance/individual/activate_position/$1/$2';
$route['microfinance/deactivate-position/(:num)/(:num)'] = 'microfinance/individual/deactivate_position/$1/$2';
$route['microfinance/delete-emergency/(:num)/(:num)'] = 'microfinance/individual/delete_emergency/$1/$2';

/*
*	Microfinance Routes
*/
$route['microfinance/groups'] = 'microfinance/group/index';
$route['microfinance/group/(:any)/(:any)/(:num)'] = 'microfinance/group/index/$1/$2/$3';
$route['microfinance/add-group'] = 'microfinance/group/add_group';
$route['microfinance/edit-group/(:num)'] = 'microfinance/group/edit_group/$1';
$route['microfinance/edit-about/(:num)'] = 'microfinance/group/edit_about/$1';
$route['microfinance/add-member/(:num)'] = 'microfinance/group/add_member/$1';
$route['microfinance/edit-group/(:num)/(:num)'] = 'microfinance/group/edit_group/$1/$2';
$route['microfinance/delete-group/(:num)'] = 'microfinance/group/delete_group/$1';
$route['microfinance/delete-group/(:num)/(:num)'] = 'microfinance/group/delete_group/$1/$2';
$route['microfinance/activate-group/(:num)'] = 'microfinance/group/activate_group/$1';
$route['microfinance/activate-group/(:num)/(:num)'] = 'microfinance/group/activate_group/$1/$2';
$route['microfinance/deactivate-group/(:num)'] = 'microfinance/group/deactivate_group/$1';
$route['microfinance/deactivate-group/(:num)/(:num)'] = 'microfinance/group/deactivate_group/$1/$2';

$route['microfinance/savings-plan'] = 'microfinance/savings_plan/index';
$route['microfinance/savings-plan/(:any)/(:any)/(:num)'] = 'microfinance/savings_plan/index/$1/$2/$3';
$route['microfinance/add-savings-plan'] = 'microfinance/savings_plan/add_savings_plan';
$route['microfinance/edit-savings-plan/(:num)'] = 'microfinance/savings_plan/edit_savings_plan/$1';
$route['microfinance/edit-savings-plan/(:num)/(:num)'] = 'microfinance/savings_plan/edit_savings_plan/$1/$2';
$route['microfinance/delete-savings-plan/(:num)'] = 'microfinance/savings_plan/delete_savings_plan/$1';
$route['microfinance/delete-savings-plan/(:num)/(:num)'] = 'microfinance/savings_plan/delete_savings_plan/$1/$2';
$route['microfinance/activate-savings-plan/(:num)'] = 'microfinance/savings_plan/activate_savings_plan/$1';
$route['microfinance/activate-savings-plan/(:num)/(:num)'] = 'microfinance/savings_plan/activate_savings_plan/$1/$2';
$route['microfinance/deactivate-savings-plan/(:num)'] = 'microfinance/savings_plan/deactivate_savings_plan/$1';
$route['microfinance/deactivate-savings-plan/(:num)/(:num)'] = 'microfinance/savings_plan/deactivate_savings_plan/$1/$2';
$route['microfinance/add-individual-plan/(:num)'] = 'microfinance/individual/add_individual_plan/$1';
$route['microfinance/activate-individual-plan/(:num)/(:num)'] = 'microfinance/individual/activate_individual_plan/$1/$2';
$route['microfinance/deactivate-individual-plan/(:num)/(:num)'] = 'microfinance/individual/deactivate_individual_plan/$1/$2';


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */

/*
*	reception Routes
*/
$route['reception'] = 'reception/index';
$route['reception/unclosed-visits'] = 'reception/visit_list/3';
$route['reception/dashboard'] = 'reception/index';
$route['reception/patients-list'] = 'reception/patients';
$route['reception/deleted-visits'] = 'reception/visit_list/2';
$route['reception/visit-history'] = 'reception/visit_list/1';
$route['reception/general-queue'] = 'reception/general_queue/reception';
$route['reception/inpatients'] = 'reception/inpatients/reception';
$route['reception/appointments-list'] = 'reception/appointment_list';
$route['reception/register-other-patient'] = 'reception/register_other_patient';
$route['reception/validate-import'] = 'reception/do_patients_import';
$route['reception/import-template'] = 'reception/import_template';
$route['reception/import-patients'] = 'reception/import_patients';
$route['reception/print-invoice/(:num)/(:any)'] = 'accounts/print_invoice_new/$1/$2';

/*
*	nurse Routes
*/
$route['nurse'] = 'nurse/index';
$route['nurse/dashboard'] = 'nurse/index';
$route['nurse/nurse-queue'] = 'nurse/nurse_queue';
$route['nurse/general-queue'] = 'reception/general_queue/nurse';
$route['nurse/visit-history'] = 'reception/visit_list/1/nurse';
$route['nurse/inpatients'] = 'reception/inpatients/nurse';

/*
*	doctor Routes
*/
$route['doctor'] = 'doctor/index';
$route['doctor/dashboard'] = 'doctor/index';
$route['doctor/doctors-queue'] = 'doctor/doctor_queue';
$route['doctor/general-queue'] = 'reception/general_queue/doctor';
$route['doctor/visit-history'] = 'reception/visit_list/1/doctor';
$route['doctor/patient-treatment'] = 'nurse/patient_treatment_statement/doctor';
$route['doctor/inpatients'] = 'reception/inpatients/doctor';

/*
*	doctor Routes
*/
$route['dental'] = 'dental/index';
$route['dental/dashboard'] = 'dental/index';
$route['dental/dental-queue'] = 'dental/dental_queue';
$route['dental/general-queue'] = 'reception/general_queue/dental';
$route['dental/visit-history'] = 'reception/visit_list/1/dental';
$route['patient-treatment'] = 'nurse/patient_treatment_statement/dental';
$route['patient-treatment/(:num)'] = 'nurse/patient_treatment_statement/dental/$1';


/*
*	doctor Routes
*/
$route['hospital-reports'] = 'hospital-reports/index';
$route['hospital-reports/patient-statements'] = 'administration/patient_statement';
// $route['hospital-reports/all-transactions'] = 'administration/reports/all_reports/admin';
$route['hospital-reports/cash-report'] = 'administration/reports/cash_report/admin';
$route['hospital-reports/cash-report/(:num)'] = 'administration/reports/cash_report/$1';
// $route['hospital-reports/debtors-report'] = 'administration/reports/debtors_report/0';
$route['hospital-reports/department-report'] = 'administration/reports/department_reports';
$route['hospital-reports/doctors-report'] = 'administration/reports/doctor_reports';
$route['hospital-reports/cancelled-reports'] = 'administration/reports/cancelled_payment';
$route['hospital-reports/cancelled-reports/(:num)'] = 'administration/reports/cancelled_payment/$1';
$route['hospital-reports/cancelled-receipts'] = 'administration/reports/cancelled_invoices';
$route['hospital-reports/cancelled-receipts/(:num)'] = 'administration/reports/cancelled_invoices/$1';


/*
*	ultrasound Routes
*/
$route['radiology/ultrasound-outpatients'] = 'radiology/ultrasound/ultrasound_queue/12';
$route['radiology/ultrasound-inpatients'] = 'reception/inpatients/ultrasound';
$route['radiology/x-ray-outpatients'] = 'radiology/xray/xray_queue/12';
$route['radiology/x-ray-inpatients'] = 'reception/inpatients/xray';
$route['radiology/general-queue'] = 'reception/general_queue/radiology';

/*
*	laboratory Routes
*/
$route['laboratory'] = 'laboratory/index';
$route['laboratory/dashboard'] = 'laboratory/index';
$route['laboratory/lab-queue'] = 'laboratory/lab_queue/12';
$route['laboratory/general-queue'] = 'reception/general_queue/laboratory';
$route['laboratory/inpatients'] = 'reception/inpatients/laboratory';

/*
*	theatre Routes
*/
$route['theatre'] = 'theatre/index';
$route['theatre/dashboard'] = 'theatre/index';
$route['theatre/theatre-queue'] = 'theatre/theatre_queue/12';
$route['theatre/general-queue'] = 'reception/general_queue/theatre';
$route['theatre/inpatients'] = 'reception/inpatients/theatre';

/*
*	laboratory setup Routes
*/
$route['laboratory-setup/classes'] = 'lab_charges/classes';
$route['laboratory-setup/tests'] = 'lab_charges/test_list';
$route['laboratory-setup/tests/(:num)'] = 'lab_charges/test_list/lab_test_name/ASC/__/$1';
$route['laboratory-setup/tests/(:any)/(:any)/(:any)/(:num)'] = 'lab_charges/test_list/$1/$2/$3/$4';
$route['laboratory-setup/tests/(:any)/(:any)'] = 'lab_charges/test_list/$1/$2';
$route['laboratory-setup/mapping'] = 'lab_charges/mapping/index';
$route['laboratory-setup/mapping/(:num)'] = 'lab_charges/mapping/index/$1';

/*
*	pharmacy Routes
*/
$route['pharmacy'] = 'pharmacy/index';
$route['pharmacy/dashboard'] = 'pharmacy/index';
$route['pharmacy/pharmacy-queue'] = 'pharmacy/pharmacy_queue/12';
$route['pharmacy/general-queue'] = 'reception/general_queue/pharmacy';
$route['pharmacy/inpatients'] = 'reception/inpatients/pharmacy';
$route['pharmacy/print-prescription/(:num)'] = 'pharmacy/print_prescription/$1';



/*
*	pharmacy setup Routes
*/
$route['pharmacy-setup/classes'] = 'pharmacy/classes';
$route['pharmacy-setup/inventory'] = 'pharmacy/inventory';
$route['pharmacy-setup/brands'] = 'pharmacy/brands';
$route['pharmacy-setup/generics'] = 'pharmacy/generics';
$route['pharmacy-setup/containers'] = 'pharmacy/containers';
$route['pharmacy-setup/types'] = 'pharmacy/types';


/*
*	Inventory Routes
*/
$route['cash-office'] = 'accounts/index';
$route['accounts/accounts-queue'] = 'accounts/accounts_queue/12';
$route['cash-office/dashboard'] = 'accounts/index';
$route['cash-office/accounts-queue'] = 'accounts/accounts_queue/12';
$route['cash-office/general-queue'] = 'reception/general_queue/accounts';
$route['cash-office/closed-visits'] = 'accounts/accounts_closed_visits';
$route['cash-office/inpatients'] = 'reception/inpatients/accounts';
$route['cash-office/un-closed-visits'] = 'accounts/accounts_unclosed_queue';
$route['accounts/un-closed-visits'] = 'accounts/accounts_unclosed_queue';


/*
*	Cloud Routes
*/
$route['cloud/sync-tables'] = 'cloud/sync_tables/index';
$route['cloud/sync-tables/(:any)/(:any)/(:num)'] = 'cloud/sync_tables/index/$1/$2/$3';
$route['cloud/sync-tables/(:any)/(:any)'] = 'cloud/sync_tables/index/$1/$2';
$route['cloud/add-sync-table'] = 'cloud/sync_tables/add_sync_table';
$route['cloud/edit-sync-table/(:num)'] = 'cloud/sync_tables/edit_sync_table/$1';
$route['cloud/delete-sync-table/(:num)'] = 'cloud/sync_tables/delete_sync_table/$1';
$route['cloud/activate-sync-table/(:num)'] = 'cloud/sync_tables/activate_sync_table/$1';
$route['cloud/deactivate-sync-table/(:num)'] = 'cloud/sync_tables/deactivate_sync_table/$1';
$route['pharmacy/validate-import'] = 'pharmacy/do_drugs_import';
$route['pharmacy/import-template'] = 'pharmacy/import_template';
$route['pharmacy/import-drugs'] = 'pharmacy/import_drugs';

/*
*	Inventory Routes
*/
$route['inventory-setup/inventory-categories'] = 'inventory/categories/index';
$route['inventory-setup/categories/(:num)'] = 'inventory/categories/index/$1';
$route['inventory-setup/add-category'] = 'inventory/categories/add_category';
$route['inventory-setup/edit-category/(:num)'] = 'inventory/categories/edit_category/$1';
$route['inventory-setup/inventory-stores'] = 'inventory/stores/index';
$route['inventory-setup/stores/(:num)'] = 'inventory/stores/index/$1';
$route['inventory-setup/add-store'] = 'inventory/stores/add_store';
$route['inventory-setup/edit-store/(:num)'] = 'inventory/stores/edit_store/$1';

$route['inventory-setup/suppliers'] = 'inventory/suppliers/index';
$route['inventory-setup/suppliers/(:num)'] = 'inventory/suppliers/index/$1';
$route['inventory-setup/add-supplier'] = 'inventory/suppliers/add_supplier';
$route['inventory-setup/edit-supplier/(:num)'] = 'inventory/suppliers/edit_supplier/$1';

$route['inventory/orders'] = 'inventory/orders/index';
$route['inventory/orders/(:num)'] = 'inventory/orders/index/$1';
$route['inventory/add-order'] = 'inventory/orders/add_order';
$route['inventory/add-order-item/(:num)/(:any)'] = 'inventory/orders/add_order_item/$1/$2';
$route['inventory/update-order-item/(:num)/(:any)/(:num)'] = 'inventory/orders/update_order_item/$1/$2/$3';
$route['inventory/update-supplier-prices/(:num)/(:any)/(:num)'] = 'inventory/orders/update_supplier_prices/$1/$2/$3';
$route['inventory/send-for-correction/(:num)'] = 'inventory/orders/send_order_for_correction/$1';
$route['inventory/send-for-approval/(:num)'] = 'inventory/orders/send_order_for_approval/$1';
$route['inventory/send-for-approval/(:num)/(:num)'] = 'inventory/orders/send_order_for_approval/$1/$2';
$route['inventory/submit-supplier/(:num)/(:any)'] = 'inventory/orders/submit_supplier/$1/$2';
$route['inventory/generate-lpo/(:num)'] = 'inventory/orders/print_lpo_new/$1';
$route['inventory/generate-rfq/(:num)/(:num)/(:any)'] = 'inventory/orders/print_rfq_new/$1/$2/$3';
$route['inventory/edit_order/(:num)'] = 'inventory/orders/edit_order/$1';

$route['inventory/products'] = 'inventory_management/index';
$route['inventory/products/(:num)'] = 'inventory_management/index/$1';
$route['inventory/add-product'] = 'inventory_management/add_product';
$route['inventory/activate-product/(:num)'] = 'inventory_management/products/activate_product/$1';
$route['inventory/deactivate-product/(:num)'] = 'inventory_management/products/deactivate_product/$1';
$route['inventory/edit-product/(:num)'] = 'inventory_management/edit_product/$1';
$route['inventory/delete-product/(:num)'] = 'inventory_management/delete_product/$1';
$route['inventory/import-products'] = 'inventory_management/products/import_products';
$route['inventory/export-products'] = 'inventory_management/products/export_products';

$route['inventory/product-details/(:num)'] = 'inventory_management/manage_product/$1';
$route['inventory/manage-store'] = 'inventory_management/manage_store';
$route['inventory/store-requests'] ='inventory_management/store_requests';
$route['inventory/selected-items/(:num)'] = 'inventory_management/now_store_requests/$1';
$route['inventory/make-order/(:num)'] = 'inventory_management/make_order/$1';
$route['inventory/make-order/(:num)/(:num)'] = 'inventory_management/make_order/$1/$2';
$route['inventory/save-product-request/(:num)/(:num)'] = 'inventory_management/save_order_products/$1/$2';
$route['inventory/update-store-order/(:num)/(:num)'] = 'inventory_management/update_order_products/$1/$2';
$route['inventory/award-store-order/(:num)/(:num)'] = 'inventory_management/award_order_products/$1/$2';
$route['inventory/receive-store-order/(:num)/(:num)/(:num)/(:num)'] = 'inventory_management/receive_order_products/$1/$2/$3/$4';
$route['inventory/product-purchases/(:num)'] = 'inventory_management/all_product_purchases/$1';
$route['inventory/purchase-product/(:num)'] = 'inventory_management/product_purchases/$1';
$route['inventory/edit-product-purchase/(:num)/(:num)'] = 'inventory_management/edit_product_purchase/$1/$2';

$route['inventory/product-deductions'] = 'inventory_management/all_product_deductions';
$route['inventory/deduction-product/(:num)'] = 'inventory_management/product_deductions/$1';
$route['inventory/edit-product-deduction/(:num)/(:num)'] = 'inventory_management/edit_product_deduction/$1/$2';

// pharmacy orders
$route['pharmacy-setup/pharmacy-orders'] = 'inventory/orders/index';
$route['inventory/search-products'] = 'inventory_management/search_inventory_product';
$route['inventory/close-product-search'] = 'inventory_management/close_inventory_search';



$route['orders'] = 'inventory/orders/index';
$route['dental/save-current-notes/(:num)'] = 'dental/save_current_notes/$1';
$route['dental/save-new-notes/(:num)'] = 'dental/save_new_notes/$1';



$route['accounts/print-paye-report/(:num)'] = 'accounts/payroll/print_paye_report/$1';
$route['accounts/print-nhif-report/(:num)'] = 'accounts/payroll/print_nhif_report/$1';
$route['accounts/print-nssf-report/(:num)'] = 'accounts/payroll/print_nssf_report/$1';
$route['accounts/print-payroll/(:num)'] = 'accounts/payroll/print_payroll/$1';
$route['accounts/print-month-payslips/(:num)'] = 'accounts/payroll/print_monthly_payslips/$1';
$route['accounts/export-payroll/(:num)'] = 'accounts/payroll/export_payroll/$1';
$route['accounts/print-payroll-pdf/(:num)'] = 'accounts/payroll/print_payroll_pdf/$1';
$route['accounts/payroll/print-payslip/(:num)/(:num)'] = 'accounts/payroll/print_payslip/$1/$2';
$route['accounts/payroll/download-payslip/(:num)/(:num)'] = 'accounts/payroll/download_payslip/$1/$2';
$route['accounts/payroll-payslips/(:num)'] = 'accounts/payroll/payroll_payslips/$1';
$route['accounts/salary-data'] = 'accounts/payroll/salaries';
$route['accounts/search-payroll'] = 'accounts/payroll/search_payroll';
$route['accounts/close-payroll-search'] = 'accounts/payroll/close_payroll_search';
$route['accounts/create-payroll'] = 'accounts/payroll/create_payroll';
$route['accounts/deactivate-payroll/(:num)'] = 'accounts/payroll/deactivate_payroll/$1';
$route['accounts/print-payslips'] = 'accounts/payroll/print_payslips';
$route['accounts/payroll/edit-payment-details/(:num)'] = 'accounts/payroll/edit_payment_details/$1';
$route['accounts/payroll/edit_allowance/(:num)'] = 'accounts/payroll/edit_allowance/$1';
$route['accounts/payroll/delete_allowance/(:num)'] = 'accounts/payroll/delete_allowance/$1';
$route['accounts/payroll/edit_deduction/(:num)'] = 'accounts/payroll/edit_deduction/$1';
$route['accounts/payroll/delete_deduction/(:num)'] = 'accounts/payroll/delete_deduction/$1';
$route['accounts/payroll/edit_saving/(:num)'] = 'accounts/payroll/edit_saving/$1';
$route['accounts/payroll/delete_saving/(:num)'] = 'accounts/payroll/delete_saving/$1';
$route['accounts/payroll/edit_loan_scheme/(:num)'] = 'accounts/payroll/edit_loan_scheme/$1';
$route['accounts/payroll/delete_loan_scheme/(:num)'] = 'accounts/payroll/delete_loan_scheme/$1';
$route['accounts/payroll'] = 'accounts/payroll/payrolls';
$route['accounts/all-payroll'] = 'accounts/payroll/all_payrolls';
$route['accounts/payment-details/(:num)'] = 'accounts/payroll/payment_details/$1';
$route['accounts/save-payment-details/(:num)'] = 'accounts/payroll/save_payment_details/$1';
$route['accounts/update-savings/(:num)'] = 'accounts/payroll/update_savings/$1';
$route['accounts/update-loan-schemes/(:num)'] = 'accounts/payroll/update_loan_schemes/$1';
$route['payroll/configuration'] = 'accounts/payroll/payroll_configuration';
$route['accounts/payroll-configuration'] = 'accounts/payroll/payroll_configuration';
$route['accounts/payroll/edit-nssf/(:num)'] = 'accounts/payroll/edit_nssf/$1';
$route['accounts/payroll/edit-nhif/(:num)'] = 'accounts/payroll/edit_nhif/$1';
$route['accounts/payroll/delete-nhif/(:num)'] = 'accounts/payroll/delete_nhif/$1';
$route['accounts/payroll/edit-paye/(:num)'] = 'accounts/payroll/edit_paye/$1';
$route['accounts/payroll/delete-paye/(:num)'] = 'accounts/payroll/delete_paye/$1';
$route['accounts/payroll/edit-payment/(:num)'] = 'accounts/payroll/edit_payment/$1';
$route['accounts/payroll/delete-payment/(:num)'] = 'accounts/payroll/delete_payment/$1';
$route['accounts/payroll/edit-benefit/(:num)'] = 'accounts/payroll/edit_benefit/$1';
$route['accounts/payroll/delete-benefit/(:num)'] = 'accounts/payroll/delete_benefit/$1';
$route['accounts/payroll/edit-allowance/(:num)'] = 'accounts/payroll/edit_allowance/$1';
$route['accounts/payroll/delete-allowance/(:num)'] = 'accounts/payroll/delete_allowance/$1';
$route['accounts/payroll/edit-deduction/(:num)'] = 'accounts/payroll/edit_deduction/$1';
$route['accounts/payroll/edit-relief/(:num)'] = 'accounts/payroll/edit_relief/$1';
$route['accounts/payroll/delete-deduction/(:num)'] = 'accounts/payroll/delete_deduction/$1';
$route['accounts/payroll/edit-other-deduction/(:num)'] = 'accounts/payroll/edit_other_deduction/$1';
$route['accounts/payroll/delete-other-deduction/(:num)'] = 'accounts/payroll/delete_other_deduction/$1';
$route['accounts/payroll/edit-loan-scheme/(:num)'] = 'accounts/payroll/edit_loan_scheme/$1';
$route['accounts/payroll/delete-loan-scheme/(:num)'] = 'accounts/payroll/delete_loan_scheme/$1';
$route['accounts/payroll/edit-saving/(:num)'] = 'accounts/payroll/edit_saving/$1';
$route['accounts/payroll/delete-saving/(:num)'] = 'accounts/payroll/delete_saving/$1';
$route['accounts/payroll/edit-personnel-payments/(:num)'] = 'accounts/payroll/edit_personnel_payments/$1';
$route['accounts/payroll/edit-personnel-allowances/(:num)'] = 'accounts/payroll/edit_personnel_allowances/$1';
$route['accounts/payroll/edit-personnel-benefits/(:num)'] = 'accounts/payroll/edit_personnel_benefits/$1';
$route['accounts/payroll/edit-personnel-deductions/(:num)'] = 'accounts/payroll/edit_personnel_deductions/$1';
$route['accounts/payroll/edit-personnel-other-deductions/(:num)'] = 'accounts/payroll/edit_personnel_other_deductions/$1';
$route['accounts/payroll/edit-personnel-savings/(:num)'] = 'accounts/payroll/edit_personnel_savings/$1';
$route['accounts/payroll/edit-personnel-loan-schemes/(:num)'] = 'accounts/payroll/edit_personnel_loan_schemes/$1';
$route['accounts/payroll/edit-personnel-relief/(:num)'] = 'accounts/payroll/edit_personnel_relief/$1';
$route['accounts/payroll/view-payslip/(:num)'] = 'accounts/payroll/view_payslip/$1';
$route['accounts/payroll/generate-batch-payroll/(:num)/(:num)/(:num)'] = 'accounts/payroll/generate_payroll/$1/$2/$3';
$route['accounts/payroll/generate-batch-payroll/(:num)/(:num)/(:num)/(:num)'] = 'accounts/payroll/generate_payroll/$1/$2/$3/$4';
$route['accounts/payroll/view-batch-payslip/(:num)/(:num)'] = 'accounts/payroll/view_batch_payslip/$1/$2';
$route['accounts/payroll/send-batch-payslip/(:num)/(:num)'] = 'accounts/payroll/send_batch_payslip/$1/$2';
$route['accounts/print-month-summary/(:num)/(:num)'] = 'accounts/payroll/month_summary/$1/$2';
$route['accounts/print-month-payslips2/(:num)'] = 'accounts/payroll/print_monthly_payslips2/$1';
$route['payroll/add-overtime-hours/(:num)'] = 'accounts/payroll/add_overtime_hours/$1';
$route['accounts/create-data-file/(:num)/(:num)'] = 'accounts/payroll/create_data_file/$1/$2';
$route['accounts/list-batches/(:num)/(:num)'] = 'accounts/payroll/list_batches/$1/$2';
$route['accounts/list-batches/(:num)/(:num)/(:num)'] = 'accounts/payroll/list_batches/$1/$2/$3';

$route['online-dairies/rooms'] = 'online_diary/rooms/index';
$route['rooms/add-room'] = 'online_diary/rooms/add_rooms';
$route['rooms/edit-room/(:num)'] = 'online_diary/rooms/edit_rooms/$1';
$route['rooms/activate-room/(:num)'] = 'online_diary/rooms/activate_room/$1';
$route['rooms/deactivate-room/(:num)'] = 'online_diary/rooms/deactivate_room/$1';
$route['rooms/delete-room/(:num)'] = 'online_diary/rooms/delete_room/$1';

$route['asset-registry/asset-category'] = 'assets/asset_category/index';
$route['asset-registry/asset-category/(:num)'] ='assets/asset_category/index/$1';
$route['asset/add-asset-category'] ='assets/asset_category/add_asset_category';
$route['asset-category/edit-asset-category/(:num)'] ='assets/asset_category/edit_asset_category/$1';
$route['asset-category/delete-asset-category/(:num)'] ='assets/asset_category/delete_asset_category/$1';
$route['asset-category/activate-asset-category/(:num)'] ='assets/asset_category/activate_asset_category/$1';
$route['asset-category/deactivate-asset-category/(:num)'] ='assets/asset_category/deactivate_asset_category/$1';
$route['asset-registry/assets'] ='assets/assets/index';
$route['assets/add-asset'] ='assets/assets/add_asset';
$route['assets/edit-asset/(:num)'] ='assets/assets/edit_asset/$1';
$route['assets/delete-asset/(:num)'] ='assets/assets/delete_asset/$1';
$route['assets/activate-asset/(:num)'] ='assets/assets/activate_asset/$1';
$route['assets/deactivate-asset/(:num)'] ='assets/assets/deactivate_asset/$1';

$route['accounts/account-balances'] = 'accounts/petty_cash/account_balances';
$route['accounts/account-balances/activate-account/(:num)'] = 'accounts/petty_cash/activate_account/$1';
$route['accounts/account-balances/deactivate-account/(:num)'] = 'accounts/petty_cash/deactivate_account/$1';
$route['accounts/account-balances/edit-account/(:num)'] = 'accounts/petty_cash/edit_account/$1';
$route['accounts/add-account'] = 'accounts/petty_cash/add_account';
$route['admin/companies/(:num)'] = 'hospital_administration/companies/index/$1';
$route['admin/companies'] = 'hospital_administration/insurance_companies/index';

$route['delete-record/(:num)'] = 'accounts/petty_cash/delete_petty_cash/$1';




// queue
$route['queue'] = 'reception/patients_queue';
$route['queue/(:num)'] = 'reception/patients_queue/$1';


// appointments

$route['appointments'] = 'reception/appointment_list';
$route['appointments/(:num)'] = 'reception/appointment_list/$1';


// patients
$route['patients'] = 'reception/patients';
$route['patients/(:num)'] = 'reception/patients/$1';
$route['add-patient'] = 'reception/add_patient';
$route['edit-patient/(:num)'] = 'reception/edit_patient/$1';



// dental

$route['patient-card/(:num)'] = 'dental/patient_card/$1';
$route['patient-card/(:num)/(:num)'] = 'dental/patient_card/$1/$2';
$route['print-priscription/(:num)/(:num)'] = 'dental/print_prescription/$1/$2';


$route['inventory/product-sales/(:num)'] = 'inventory_management/products/product_sales/$1';
$route['inventory/product-sales/(:num)/(:num)'] = 'inventory_management/products/product_sales/$1/42';


$route['procurement/drugs-sales'] = 'administration/reports/drugs';
$route['procurement/drugs-sales/(:any)/(:any)'] = 'administration/reports/drugs/$1/$2';



$route['messaging/dashboard'] = 'messaging/dashboard';
$route['messages'] = 'messaging/unsent_messages';
$route['messaging/unsent-messages'] = 'messaging/unsent_messages';
$route['messaging/unsent-messages/(:num)'] = 'messaging/unsent_messages/$1';
$route['messaging/sent-messages'] = 'messaging/sent_messages';
$route['messaging/sent-messages/(:num)'] = 'messaging/sent_messages/$1';
$route['messaging/spoilt-messages'] = 'messaging/spoilt_messages';
$route['messaging/spoilt-messages/(:num)'] = 'messaging/spoilt_messages/$1';
// import functions of messages
$route['messaging/validate-import/(:num)'] = 'messaging/do_messages_import/$1';
$route['messaging/import-template'] = 'messaging/import_template';
$route['messaging/import-messages'] = 'messaging/import_messages';
$route['messaging/send-messages'] = 'messaging/send_messages';


$route['messaging/message-templates'] = 'messaging/message_templates';
$route['messaging/add-template'] = 'messaging/add_message_template';
$route['messaging/edit-message-template/(:num)'] = 'messaging/edit_message_template/$1';
$route['messaging/activate-message-template/(:num)'] = 'messaging/activate_message_template/$1';
$route['messaging/deactivate-message-template/(:num)'] = 'messaging/deactivate_message_template/$1';
$route['template-detail/(:num)'] = 'messaging/template_detail/$1';
$route['set-search-parameters/(:num)'] = 'messaging/set_search_parameters/$1';
$route['create-batch-items/(:num)'] = 'messaging/create_batch_items/$1';
$route['create-all-batch/(:num)/(:num)'] ='messaging/create_batch_members/$1/$2';

$route['send-messages/(:num)/(:num)'] = 'messaging/send_batch_messages/$1/$2';
$route['view-senders/(:num)/(:num)'] = 'messaging/members_account/$1/$2';
$route['view-senders/(:num)/(:num)/(:num)'] = 'messaging/members_account/$1/$2/$3';
$route['senders-view/(:num)/(:num)'] = 'messaging/view_persons_for_batch/$1/$2';
$route['template-detail/remove-all_contacts/(:num)/(:num)'] = 'messaging/remove_all_contacts/$1/$2';
$route['senders-view/(:num)/(:num)/(:num)'] = 'messaging/view_persons_for_batch/$1/$2/$3';
$route['view-schedules/(:num)/(:num)'] = 'messaging/view_schedules/$1/$2';
$route['messaging/dashboard'] = 'messaging/dashboard';
$route['delete-message-contact/(:num)/(:num)/(:num)'] = 'messaging/delete_contact/$1/$2/$3';
$route['create-new-schedule/(:num)/(:num)'] = 'messaging/create_new_schedule/$1/$2';

$route['bulk-delete-contacts/(:num)'] = 'administration/contacts/bulk_delete_contacts/$1';
$route['bulk-add-contacts/(:num)/(:num)'] = 'messaging/bulk_add_contacts/$1/$2';
$route['import/custom-contacts-template'] = 'messaging/custom_contacts_template';
$route['import/import-custom-contacts/(:num)/(:num)'] = 'messaging/import_custom_contacts/$1/$2';

$route['activate-schedule/(:num)/(:num)/(:num)'] = 'messaging/activate_schedule/$1/$2/$3';
$route['deactivate-schedule/(:num)/(:num)/(:num)'] = 'messaging/deactivate_schedule/$1/$2/$3';
$route['delete-schedule/(:num)/(:num)/(:num)'] = 'messaging/delete_schedule/$1/$2/$3';

$route['search-members/(:num)/(:num)'] = 'messaging/search_members/$1/$2';
$route['close-search/(:num)/(:num)'] = 'messaging/close_search/$1/$2';





//account balances
$route['accounting/general-journal-entries'] = 'accounting/petty_cash/account_balances';
$route['accounting/general-journal-entries/activate-account/(:num)'] = 'accounting/petty_cash/activate_account/$1';
$route['accounting/general-journal-entries/deactivate-account/(:num)'] = 'accounting/petty_cash/deactivate_account/$1';
$route['accounting/general-journal-entries/edit-account/(:num)'] = 'accounting/petty_cash/edit_account/$1';
$route['accounting/add-account'] = 'accounting/petty_cash/add_account';



// accounting and company financials
$route['accounting/ledger-entry'] = 'accounting/petty_cash/ledger';
$route['accounting/ledger-entry/(:any)/(:any)'] = 'accounting/petty_cash/ledger/$1/$2';
$route['accounting/ledger-entry/(:any)'] = 'accounting/petty_cash/ledger/$1';
$route['accounting/write-cheque'] = 'accounting/petty_cash/write_cheque';
$route['accounting/write-cheque/(:num)'] = 'accounting/petty_cash/write_cheque/$1';
$route['accounting/providers'] = 'accounting/creditors/providers';
$route['accounting/providers/(:num)'] = 'accounting/creditors/providers/$1';
$route['update-provider-balance/(:num)'] =  'accounting/creditors/update_opening_balance/$1';
$route['accounting/provider-statement/(:num)/(:num)'] = 'accounting/creditors/provider_statement/$1/$2';
$route['accounting/cash-provider-statement/(:num)'] = 'accounting/creditors/cash_provider_statement/$1';
$route['company-financials/profit-and-loss'] = 'accounting/company_financial/profit_and_loss';
$route['company-financials/balance-sheet'] = 'accounting/company_financial/balance_sheet';

$route['accounting/creditors'] = 'accounting/creditors/index';
$route['accounting/creditors/(:num)'] = 'accounting/creditors/index/$1';
// $route['delete-creditor-invoice/(:num)'] = 'accounting/petty_cash/delete_invoice_entry/$1';
// $route['delete-creditor-invoice-entry/(:num)/(:num)'] = 'accounting/creditors/delete_creditor_invoice/$1/$2';
$route['delete-creditor-payment-entry/(:num)/(:num)'] = 'accounting/creditors/delete_creditor_payment/$1/$2';
$route['delete-creditor-payment/(:num)'] = 'accounting/petty_cash/delete_payment_entry/$1';



// payroll

$route['payroll/change-branch'] = 'payroll/payroll/change_branch';
$route['payroll/print-payroll/(:num)'] = 'payroll/payroll/print_payroll/$1';
$route['payroll/export-payroll/(:num)'] = 'payroll/payroll/export_payroll/$1';
$route['payroll/print-payroll-pdf/(:num)'] = 'payroll/payroll/print_payroll_pdf/$1';
$route['payroll/payroll/print-payslip/(:num)/(:num)'] = 'payroll/payroll/print_payslip/$1/$2';
$route['payroll/payroll/download-payslip/(:num)/(:num)'] = 'payroll/payroll/download_payslip/$1/$2';
$route['payroll/payroll-payslips/(:num)'] = 'payroll/payroll/payroll_payslips/$1';
$route['payroll/salary-data'] = 'payroll/payroll/salaries';
$route['payroll/search-payroll'] = 'payroll/payroll/search_payroll';
$route['payroll/close-payroll-search'] = 'payroll/payroll/close_payroll_search';
$route['payroll/create-payroll'] = 'payroll/payroll/create_payroll';
$route['payroll/deactivate-payroll/(:num)'] = 'payroll/payroll/deactivate_payroll/$1';
$route['payroll/print-payslips'] = 'payroll/payroll/print_payslips';
$route['payroll/payroll/edit-payment-details/(:num)'] = 'payroll/payroll/edit_payment_details/$1';
$route['payroll/payroll/edit_allowance/(:num)'] = 'payroll/payroll/edit_allowance/$1';
$route['payroll/payroll/delete_allowance/(:num)'] = 'payroll/payroll/delete_allowance/$1';
$route['payroll/payroll/edit_deduction/(:num)'] = 'payroll/payroll/edit_deduction/$1';
$route['payroll/payroll/delete_deduction/(:num)'] = 'payroll/payroll/delete_deduction/$1';
$route['payroll/payroll/edit_saving/(:num)'] = 'payroll/payroll/edit_saving/$1';
$route['payroll/payroll/delete_saving/(:num)'] = 'payroll/payroll/delete_saving/$1';
$route['payroll/payroll/edit_loan_scheme/(:num)'] = 'payroll/payroll/edit_loan_scheme/$1';
$route['payroll/payroll/delete_loan_scheme/(:num)'] = 'payroll/payroll/delete_loan_scheme/$1';
$route['payroll/payroll'] = 'payroll/payroll/payrolls';
$route['payroll/payment-details/(:num)'] = 'payroll/payroll/payment_details/$1';
$route['payroll/save-payment-details/(:num)'] = 'payroll/payroll/save_payment_details/$1';
$route['payroll/update-savings/(:num)'] = 'payroll/payroll/update_savings/$1';
$route['payroll/update-loan-schemes/(:num)'] = 'payroll/payroll/update_loan_schemes/$1';
$route['payroll/configuration'] = 'payroll/payroll/payroll_configuration';
$route['payroll/payroll-configuration'] = 'payroll/payroll/payroll_configuration';
$route['payroll/payroll/edit-nssf/(:num)'] = 'payroll/payroll/edit_nssf/$1';
$route['payroll/payroll/edit-nhif/(:num)'] = 'payroll/payroll/edit_nhif/$1';
$route['payroll/payroll/delete-nhif/(:num)'] = 'payroll/payroll/delete_nhif/$1';
$route['payroll/payroll/edit-paye/(:num)'] = 'payroll/payroll/edit_paye/$1';
$route['payroll/payroll/delete-paye/(:num)'] = 'payroll/payroll/delete_paye/$1';
$route['payroll/payroll/edit-payment/(:num)'] = 'payroll/payroll/edit_payment/$1';
$route['payroll/payroll/delete-payment/(:num)'] = 'payroll/payroll/delete_payment/$1';
$route['payroll/payroll/edit-benefit/(:num)'] = 'payroll/payroll/edit_benefit/$1';
$route['payroll/payroll/delete-benefit/(:num)'] = 'payroll/payroll/delete_benefit/$1';
$route['payroll/payroll/edit-allowance/(:num)'] = 'payroll/payroll/edit_allowance/$1';
$route['payroll/payroll/delete-allowance/(:num)'] = 'payroll/payroll/delete_allowance/$1';
$route['payroll/payroll/edit-deduction/(:num)'] = 'payroll/payroll/edit_deduction/$1';
$route['payroll/payroll/edit-relief/(:num)'] = 'payroll/payroll/edit_relief/$1';
$route['payroll/payroll/delete-deduction/(:num)'] = 'payroll/payroll/delete_deduction/$1';
$route['payroll/payroll/edit-other-deduction/(:num)'] = 'payroll/payroll/edit_other_deduction/$1';
$route['payroll/payroll/delete-other-deduction/(:num)'] = 'payroll/payroll/delete_other_deduction/$1';
$route['payroll/payroll/edit-loan-scheme/(:num)'] = 'payroll/payroll/edit_loan_scheme/$1';
$route['payroll/payroll/delete-loan-scheme/(:num)'] = 'payroll/payroll/delete_loan_scheme/$1';
$route['payroll/payroll/edit-saving/(:num)'] = 'payroll/payroll/edit_saving/$1';
$route['payroll/payroll/delete-saving/(:num)'] = 'payroll/payroll/delete_saving/$1';
$route['payroll/payroll/edit-personnel-payments/(:num)'] = 'payroll/payroll/edit_personnel_payments/$1';
$route['payroll/payroll/edit-personnel-allowances/(:num)'] = 'payroll/payroll/edit_personnel_allowances/$1';
$route['payroll/payroll/edit-personnel-benefits/(:num)'] = 'payroll/payroll/edit_personnel_benefits/$1';
$route['payroll/payroll/edit-personnel-deductions/(:num)'] = 'payroll/payroll/edit_personnel_deductions/$1';
$route['payroll/payroll/edit-personnel-other-deductions/(:num)'] = 'payroll/payroll/edit_personnel_other_deductions/$1';
$route['payroll/payroll/edit-personnel-savings/(:num)'] = 'payroll/payroll/edit_personnel_savings/$1';
$route['payroll/payroll/edit-personnel-loan-schemes/(:num)'] = 'payroll/payroll/edit_personnel_loan_schemes/$1';
$route['payroll/payroll/edit-personnel-relief/(:num)'] = 'payroll/payroll/edit_personnel_relief/$1';
$route['payroll/payroll/view-payslip/(:num)'] = 'payroll/payroll/view_payslip/$1';

$route['accounts/insurance-invoices'] = 'administration/reports/debtors_report_invoices/0';
$route['accounts/insurance-invoices/(:num)'] = 'administration/reports/debtors_report_invoices/$1';

//Always comes last
$route['payroll/payroll/(:any)/(:any)'] = 'payroll/payroll/payrolls/$1/$2';
$route['payroll/payroll/(:any)/(:any)/(:num)'] = 'payroll/payroll/payrolls/$1/$2/$3';
$route['payroll/salary-data/(:any)/(:any)'] = 'payroll/payroll/salaries/$1/$2';
$route['payroll/salary-data/(:any)/(:any)/(:num)'] = 'payroll/payroll/salaries/$1/$2/$3';



$route['payroll/print-paye-report/(:num)'] = 'payroll/payroll/print_paye_report/$1';
$route['payroll/print-nhif-report/(:num)'] = 'payroll/payroll/print_nhif_report/$1';
$route['payroll/print-nssf-report/(:num)'] = 'payroll/payroll/print_nssf_report/$1';
$route['payroll/print-payroll/(:num)'] = 'payroll/payroll/print_payroll/$1';
$route['payroll/print-month-payslips/(:num)'] = 'payroll/payroll/print_monthly_payslips/$1';
$route['payroll/print-monthly-payslips-data/(:num)'] = 'payroll/payroll/print_monthly_payslips_data/$1';
$route['payroll/export-payroll/(:num)'] = 'payroll/payroll/export_payroll/$1';
$route['payroll/print-payroll-pdf/(:num)'] = 'payroll/payroll/print_payroll_pdf/$1';
$route['payroll/payroll/print-payslip/(:num)/(:num)'] = 'payroll/payroll/print_payslip/$1/$2';
$route['payroll/payroll/download-payslip/(:num)/(:num)'] = 'payroll/payroll/download_payslip/$1/$2';
$route['payroll/payroll-payslips/(:num)'] = 'payroll/payroll/payroll_payslips/$1';
$route['payroll/salary-data'] = 'payroll/payroll/salaries';
$route['payroll/search-payroll'] = 'payroll/payroll/search_payroll';
$route['payroll/close-payroll-search'] = 'payroll/payroll/close_payroll_search';
$route['payroll/create-payroll'] = 'payroll/payroll/create_payroll';
$route['payroll/deactivate-payroll/(:num)'] = 'payroll/payroll/deactivate_payroll/$1';
$route['payroll/print-payslips'] = 'payroll/payroll/print_payslips';
$route['payroll/payroll/edit-payment-details/(:num)'] = 'payroll/payroll/edit_payment_details/$1';
$route['payroll/payroll/edit_allowance/(:num)'] = 'payroll/payroll/edit_allowance/$1';
$route['payroll/payroll/delete_allowance/(:num)'] = 'payroll/payroll/delete_allowance/$1';
$route['payroll/payroll/edit_deduction/(:num)'] = 'payroll/payroll/edit_deduction/$1';
$route['payroll/payroll/delete_deduction/(:num)'] = 'payroll/payroll/delete_deduction/$1';
$route['payroll/payroll/edit_saving/(:num)'] = 'payroll/payroll/edit_saving/$1';
$route['payroll/payroll/delete_saving/(:num)'] = 'payroll/payroll/delete_saving/$1';
$route['payroll/payroll/edit_loan_scheme/(:num)'] = 'payroll/payroll/edit_loan_scheme/$1';
$route['payroll/payroll/delete_loan_scheme/(:num)'] = 'payroll/payroll/delete_loan_scheme/$1';
$route['payroll/payroll'] = 'payroll/payroll/payrolls';
$route['payroll/all-payroll'] = 'payroll/payroll/all_payrolls';
$route['payroll/payment-details/(:num)'] = 'payroll/payroll/payment_details/$1';
$route['payroll/save-payment-details/(:num)'] = 'payroll/payroll/save_payment_details/$1';
$route['payroll/update-savings/(:num)'] = 'payroll/payroll/update_savings/$1';
$route['payroll/update-loan-schemes/(:num)'] = 'payroll/payroll/update_loan_schemes/$1';
$route['payroll/configuration'] = 'payroll/payroll/payroll_configuration';
$route['payroll/payroll-configuration'] = 'payroll/payroll/payroll_configuration';
$route['payroll/payroll/edit-nssf/(:num)'] = 'payroll/payroll/edit_nssf/$1';
$route['payroll/payroll/edit-nhif/(:num)'] = 'payroll/payroll/edit_nhif/$1';
$route['payroll/payroll/delete-nhif/(:num)'] = 'payroll/payroll/delete_nhif/$1';
$route['payroll/payroll/edit-paye/(:num)'] = 'payroll/payroll/edit_paye/$1';
$route['payroll/payroll/delete-paye/(:num)'] = 'payroll/payroll/delete_paye/$1';
$route['payroll/payroll/edit-payment/(:num)'] = 'payroll/payroll/edit_payment/$1';
$route['payroll/payroll/delete-payment/(:num)'] = 'payroll/payroll/delete_payment/$1';
$route['payroll/payroll/edit-benefit/(:num)'] = 'payroll/payroll/edit_benefit/$1';
$route['payroll/payroll/delete-benefit/(:num)'] = 'payroll/payroll/delete_benefit/$1';
$route['payroll/payroll/edit-allowance/(:num)'] = 'payroll/payroll/edit_allowance/$1';
$route['payroll/payroll/delete-allowance/(:num)'] = 'payroll/payroll/delete_allowance/$1';
$route['payroll/payroll/edit-deduction/(:num)'] = 'payroll/payroll/edit_deduction/$1';
$route['payroll/payroll/edit-relief/(:num)'] = 'payroll/payroll/edit_relief/$1';
$route['payroll/payroll/delete-deduction/(:num)'] = 'payroll/payroll/delete_deduction/$1';
$route['payroll/payroll/edit-other-deduction/(:num)'] = 'payroll/payroll/edit_other_deduction/$1';
$route['payroll/payroll/delete-other-deduction/(:num)'] = 'payroll/payroll/delete_other_deduction/$1';
$route['payroll/payroll/edit-loan-scheme/(:num)'] = 'payroll/payroll/edit_loan_scheme/$1';
$route['payroll/payroll/delete-loan-scheme/(:num)'] = 'payroll/payroll/delete_loan_scheme/$1';
$route['payroll/payroll/edit-saving/(:num)'] = 'payroll/payroll/edit_saving/$1';
$route['payroll/payroll/delete-saving/(:num)'] = 'payroll/payroll/delete_saving/$1';
$route['payroll/payroll/edit-personnel-payments/(:num)'] = 'payroll/payroll/edit_personnel_payments/$1';
$route['payroll/payroll/edit-personnel-allowances/(:num)'] = 'payroll/payroll/edit_personnel_allowances/$1';
$route['payroll/payroll/edit-personnel-benefits/(:num)'] = 'payroll/payroll/edit_personnel_benefits/$1';
$route['payroll/payroll/edit-personnel-deductions/(:num)'] = 'payroll/payroll/edit_personnel_deductions/$1';
$route['payroll/payroll/edit-personnel-other-deductions/(:num)'] = 'payroll/payroll/edit_personnel_other_deductions/$1';
$route['payroll/payroll/edit-personnel-savings/(:num)'] = 'payroll/payroll/edit_personnel_savings/$1';
$route['payroll/payroll/edit-personnel-loan-schemes/(:num)'] = 'payroll/payroll/edit_personnel_loan_schemes/$1';
$route['payroll/payroll/edit-personnel-relief/(:num)'] = 'payroll/payroll/edit_personnel_relief/$1';
$route['payroll/payroll/view-payslip/(:num)'] = 'payroll/payroll/view_payslip/$1';
$route['payroll/payroll/generate-batch-payroll/(:num)/(:num)/(:num)'] = 'payroll/payroll/generate_payroll/$1/$2/$3';
$route['payroll/payroll/generate-batch-payroll/(:num)/(:num)/(:num)/(:num)'] = 'payroll/payroll/generate_payroll/$1/$2/$3/$4';
$route['payroll/payroll/view-batch-payslip/(:num)/(:num)'] = 'payroll/payroll/view_batch_payslip/$1/$2';
$route['payroll/payroll/send-batch-payslip/(:num)/(:num)'] = 'payroll/payroll/send_batch_payslip/$1/$2';
$route['payroll/print-month-summary/(:num)/(:num)'] = 'payroll/payroll/month_summary/$1/$2';
$route['payroll/print-month-payslips2/(:num)'] = 'payroll/payroll/print_monthly_payslips2/$1';
$route['payroll/add-overtime-hours/(:num)'] = 'payroll/payroll/add_overtime_hours/$1';
$route['payroll/create-data-file/(:num)/(:num)'] = 'payroll/payroll/create_data_file/$1/$2';
$route['payroll/list-batches/(:num)/(:num)'] = 'payroll/payroll/list_batches/$1/$2';
$route['payroll/list-batches/(:num)/(:num)/(:num)'] = 'payroll/payroll/list_batches/$1/$2/$3';



//import salary advances
$route['salary-advance/import-salary-advance'] = 'payroll/salary_advance/import_salary_advance';
$route['import/import-salary-advances'] = 'payroll/salary_advance/do_advance_import';
$route['import/advance-template'] = 'payroll/salary_advance/advances_template';
$route['download-salary-advance'] = 'payroll/salary_advance/download_salary_advance';

// p9 form
$route['payroll/p9'] = 'payroll/payroll/generate_p9_form';
#$route['payroll/generate_p9_form'] = 'payroll/payroll/p9_form';
$route['payroll/generate_p9_form'] = 'payroll/payroll/p9_js_form';
$route['payroll/get-p9-data/(:num)'] = 'payroll/payroll/get_p9_data/$1';
//p10 form
/*$route['payroll/p10'] = 'payroll/payroll/generate_p10_form';*/
$route['payroll/p10'] = 'payroll/payroll/generate_p10_form';
#$route['payroll/generate_p10_form'] = 'payroll/payroll/p10_js_form';
$route['payroll/generate_p10_form'] = 'payroll/payroll/p10_form';
$route['payroll/get-p10-data/(:num)'] = 'payroll/payroll/get_p10_data/$1';

//timesheets
$route['timesheets/add-timesheet'] = 'hr/personnel/add_personnel_timesheet';

//bank reports
$route['payroll/bank'] = 'payroll/payroll/bank';
$route['payroll/generate-bank-report/(:num)'] = 'payroll/payroll/generate_bank_report/$1';

//salary advances
$route['salary-advance'] = 'payroll/salary_advance/index';
$route['payroll/search-advances'] = 'payroll/salary_advance/search_salary_advance';
$route['close-salary-advance-search'] = 'payroll/salary_advance/close_advance_search';
$route['salary-advance/(:any)/(:any)'] = 'payroll/salary_advance/index/$1/$2';

//payroll reports routes
$route['payroll/payroll-reports'] = 'payroll/payroll/payroll_report';
$route['payroll/search-payroll-reports'] = 'payroll/payroll/search_payroll_reports';

//import overtime-hours
$route['import/overtime'] = 'payroll/payroll/import_overtime';
$route['import/overtime-template'] = 'payroll/payroll/import_overtime_template';
$route['import/import-overtime'] = 'payroll/payroll/do_overtime_import';


// asset registry

$route['asset-registry/asset-category'] = 'assets/asset_category/index';
$route['asset-registry/asset-category/(:num)'] ='assets/asset_category/index/$1';
$route['asset/add-asset-category'] ='assets/asset_category/add_asset_category';
$route['asset-category/edit-asset-category/(:num)'] ='assets/asset_category/edit_asset_category/$1';
$route['asset-category/delete-asset-category/(:num)'] ='assets/asset_category/delete_asset_category/$1';
$route['asset-category/activate-asset-category/(:num)'] ='assets/asset_category/activate_asset_category/$1';
$route['asset-category/deactivate-asset-category/(:num)'] ='assets/asset_category/deactivate_asset_category/$1';
$route['asset-registry/assets'] ='assets/assets/index';
$route['assets/add-asset'] ='assets/assets/add_asset';
$route['assets/edit-asset/(:num)'] ='assets/assets/edit_asset/$1';
$route['assets/delete-asset/(:num)'] ='assets/assets/delete_asset/$1';
$route['assets/activate-asset/(:num)'] ='assets/assets/activate_asset/$1';
$route['assets/deactivate-asset/(:num)'] ='assets/assets/deactivate_asset/$1';

$route['update-charges'] = 'inventory/orders/update_invoice_charges';
$route['patient-uploads/(:num)'] = 'reception/patient_uploads/$1';
$route['add-upload/(:num)'] = 'reception/add_patient_scan/$1';
$route['delete-upload/(:num)/(:num)'] = 'reception/delete_document_scan/$1$2';




$route['cash-office/invoices'] = 'administration/reports/all_invoices';
$route['cash-office/invoices/(:num)'] = 'administration/reports/all_invoices/$1';
$route['view-doctors-patients/(:num)/(:any)/(:any)'] = 'administration/reports/doctor_patients_view/$1/$2/$3';
$route['view-doctors-patients/(:num)/(:any)/(:any)/(:num)'] = 'administration/reports/doctor_patients_view/$1/$2/$3/$4';
$route['charge-sheet/(:num)'] = 'accounts/charge_sheet/$1';
$route['receipt-payment/(:num)/(:num)'] = 'accounts/receipt_payment/$1/$2';
$route['prescribe-drugs/(:num)'] = 'pharmacy/pharmacy_charge_sheet/$1';

$route['update-charge-sheet/(:num)/(:num)/(:num)/(:num)'] = 'pharmacy/update_charge_sheet/$1/$2/$3/$4';






// procurement

$route['procurement/suppliers'] = 'inventory/suppliers/index';
$route['procurement/suppliers/(:num)'] = 'inventory/suppliers/index/$1';
$route['procurement/add-supplier'] = 'inventory/suppliers/add_supplier';
$route['procurement/edit-supplier/(:num)'] = 'inventory/suppliers/edit_supplier/$1';
$route['procurement/delete-supplier/(:num)'] = 'inventory/suppliers/delete_supplier/$1';
$route['procurement/activate-supplier/(:num)'] = 'inventory/suppliers/activate_supplier/$1';
$route['procurement/deactivate-supplier/(:num)'] = 'inventory/suppliers/deactivate_supplier/$1';
$route['procurement/product-supplies'] = 'inventory/orders/product_supplies';
$route['procurement/product-supplies/(:num)'] = 'inventory/orders/product_supplies/$1';
$route['procurement/general-orders'] = 'inventory/orders/index';
$route['procurement/general-orders/(:num)'] = 'inventory/orders/index/$1';
$route['remove-item/(:num)/(:any)/(:num)'] = 'inventory/orders/remove_supplier_order/$1/$2/$3';
$route['procurement/suppliers-invoices'] = 'inventory/orders/suppliers_invoices';
$route['procurement/suppliers-invoices/(:num)'] = 'inventory/orders/suppliers_invoices/$1';
$route['procurement/delete-invoices/(:num)'] = 'inventory/orders/delete_order_supply/$1';
$route['procurement/supplier-invoice-detail/(:num)'] = 'inventory/orders/suppliers_invoice_detail/$1';
$route['update-invoice-date/(:num)'] = 'inventory/orders/update_orders_date/$1';
$route['delete-order-item/(:num)/(:num)/(:num)'] = 'inventory/orders/nurse/inpatient_car/$1/$2/$3';
$route['procurement/drugs-sales'] = 'administration/reports/drugs';
$route['procurement/drugs-sales/(:any)/(:any)'] = 'administration/reports/drugs/$1/$2';


$route['accounts/invoices'] = 'administration/reports/all_invoices';
$route['accounts/invoices/(:num)'] = 'administration/reports/all_invoices/$1';

$route['patient-invoices'] = 'administration/reports/doctor_invoices';
$route['patient-invoices/(:num)'] = 'administration/reports/doctor_invoices/$1';

$route['accounts/lab-works'] = 'administration/reports/all_lab_works';
$route['accounts/lab-works/(:num)'] = 'administration/reports/all_lab_works/$1';
$route['view-doctor-patients/(:num)/(:any)/(:num)'] = 'administration/reports/doctor_patients_view/$1/$2/$3';
$route['view-doctor-patients/(:num)/(:any)/(:num)/(:num)/(:num)'] = 'administration/reports/doctor_patients_view/$1/$2/$3/$4/$5';
$route['view-doctor-patients/(:num)/(:any)/(:num)/(:num)'] = 'administration/reports/doctor_patients_view/$1/$2/$3/$4';


$route['creditor-statement/(:num)'] = 'accounting/creditors/statement/$1';

$route['send-appointment-reminders'] = 'reception/send_appointments';
$route['print-sick-off/(:num)'] = 'dental/print_sick_leave/$1';
$route['print-prescription/(:num)'] = 'dental/print_prescription/$1';
$route['print-patient-statement/(:num)'] = 'administration/print_individual_statement/$1';
$route['human-resource/personnel-leave-detail/(:num)'] = 'hr/leave/personnel_leaves/$1';


$route['hospital-reports/all-transactions'] = 'accounting/reports/debtors';
$route['hospital-reports/all-transactions/(:num)'] = 'accounting/reports/debtors/$1';
$route['search-debtors-report'] = 'accounting/reports/search_debtors_report';
$route['hospital-reports/visit-time-report'] = 'administration/reports/all_time_reports';
$route['hospital-reports/visit-time-report/(:num)'] = 'administration/reports/all_time_reports/$1';


$route['creditor-statement/(:num)'] = 'accounting/creditors/statement/$1';
$route['accounts-transactions/(:num)'] = 'accounting/petty_cash/get_transactions/$1';
$route['visit-transactions/(:num)'] = 'accounting/company_financial/search_visit_transactions/$1';


$route['accounting/debtors-statements'] = 'accounting/debtors/index';
$route['accounting/debtors-statements/(:num)'] = 'accounting/debtors/index/$1';
$route['accounting/debtor-statement/(:num)'] = 'accounting/debtors/debtor_statement/$1';
$route['accounting/debtor-statement/(:num)/(:num)'] = 'accounting/debtors/debtor_statement/$1/$2';
$route['export-debtor-invoices/(:num)/(:any)/(:any)'] = 'accounting/debtors/export_debtor_statement/$1/$2/$3';
$route['update-debtor-balance/(:num)'] =  'accounting/debtors/update_opening_balance/$1';


// management reports


$route['management-reports/patients-turnover'] = 'admin/patients_turnover';
$route['management-reports/procedures-report'] = 'reports/procedures_report';
$route['management-reports/procedures-report/(:num)'] = 'reports/procedures_report/$1';
$route['management-reports/appointments-report'] = 'reports/appointments_report';
$route['management-reports/appointments-report/(:num)'] = 'reports/appointments_report/$1';
$route['reports/export-procedures'] = 'reports/export_procedures_report';
$route['reports/export-procedures/(:num)'] = 'reports/export_visit_procedures_report/$1';
$route['export-appointments-report'] = 'reports/export_appointment_report';



$route['preauths'] = 'administration/reports/all_preauths';
$route['preauths/(:num)'] = 'administration/reports/all_preauths/$1';
$route['download-preauths'] = 'administration/reports/export_preauths/$1';




// finance

// purchases

$route['accounting/purchases'] = 'finance/purchases/all_purchases';
$route['accounting/purchases/(:num)'] = 'finance/purchases/all_purchases/$1';


$route['accounting/landlord-transactions'] = 'finance/landlord/all_transactions';
$route['accounting/landlord-transactions/(:num)'] = 'finance/landlord/all_transactions/$1';



// fiance write cheques
$route['accounting/accounts-transfer'] = 'finance/transfer/write_cheque';
$route['accounting/accounts-transfer/(:num)'] = 'finance/transfer/write_cheque/$1';
$route['reverse-transfer-entry/(:num)'] = 'finance/transfer/reverse_transfer/$1';

$route['remove-transfer-entry/(:num)'] = 'finance/transfer/transfer_delete_record/$1';
$route['edit-transfer-entry/(:num)'] = 'finance/transfer/edit_transfer_record/$1';

$route['accounting/purchase-payments'] = 'finance/purchases/purchase_payments';


// petty cash

$route['accounting/petty-cash'] = 'finance/purchases/petty_cash';
$route['accounting/petty-cash/(:any)/(:any)'] = 'finance/purchases/petty_cash/$1/$2';
$route['accounting/petty-cash/(:any)'] = 'finance/purchases/petty_cash/$1';
$route['print-petty-cash'] = 'finance/purchases/print_petty_cash';





// bills
$route['accounting/creditors'] = 'finance/creditors/creditors_list';
$route['accounting/creditor-invoices'] = 'finance/creditors/creditors_invoices';
$route['accounting/creditor-invoices/(:num)'] = 'finance/creditors/creditors_invoices/$1';
$route['search-creditor-invoices'] = 'finance/creditors/search_creditors_invoice';
$route['search-creditor-bill/(:num)'] = 'finance/creditors/search_creditors_bill/$1';
$route['close-search-creditors-invoices'] = 'finance/creditors/close_searched_invoices_creditor';

$route['creditor-invoice/delete-creditor-invoice/(:num)'] = 'finance/creditors/delete_creditor_invoice/$1';
$route['creditor-invoice/edit-creditor-invoice/(:num)'] = 'finance/creditors/edit_creditor_invoice/$1';
$route['delete-creditor-invoice-entry/(:num)/(:num)'] = 'accounting/creditors/delete_creditor_invoice/$1/$2';


$route['finance/add-creditor'] = 'finance/creditors/add_creditor';
$route['finance/edit-creditor/(:num)'] = 'finance/creditors/edit_creditor/$1';




// credit notes

$route['accounting/creditor-credit-notes'] = 'finance/creditors/creditors_credit_note';
$route['search-creditor-credit-notes'] = 'finance/creditors/search_creditors_credit_notes';
$route['search-creditor-credit-notes/(:num)'] = 'finance/creditors/search_creditors_credit_notes/$1';
$route['close-search-creditors-credit-notes'] = 'finance/creditors/close_searched_credit_notes_creditor';
$route['delete-credit-note-item/(:num)'] = 'finance/creditors/delete_credit_note_item/$1';
$route['delete-credit-note-item/(:num)/(:num)'] = 'finance/creditors/delete_credit_note_item/$1/$2';
$route['delete-creditor-credit-note/(:num)/(:num)'] = 'finance/creditors/delete_creditor_credit_note/$1/$2';
$route['edit-creditor-credit-note/(:num)'] = 'finance/creditors/edit_creditor_credit_note/$1';




// payments_import

$route['accounting/creditor-payments'] = 'finance/creditors/creditors_payments';
$route['accounting/creditor-payments/(:num)'] = 'finance/creditors/creditors_payments/$1';
$route['search-creditor-payments'] = 'finance/creditors/search_creditors_payments';
$route['search-creditor-payments/(:num)'] = 'finance/creditors/search_creditors_payments/$1';
$route['close-search-creditors-payments'] = 'finance/creditors/close_searched_payments_creditor';
$route['delete-creditor-payment-item/(:num)/(:num)'] = 'finance/creditors/delete_creditor_payment_item/$1/$2';
$route['delete-creditor-payment-item/(:num)/(:num)/(:num)'] = 'finance/creditors/delete_creditor_payment_item/$1/$2/$3';
$route['delete-creditor-invoice-item/(:num)/(:num)'] = 'finance/creditors/delete_creditor_invoice_item/$1/$2';
$route['delete-creditor-invoice-item/(:num)/(:num)/(:num)'] = 'finance/creditors/delete_creditor_invoice_item/$1/$2/$3';

$route['delete-creditor-payment/(:num)'] = 'finance/creditors/delete_creditor_payment/$1';
$route['edit-creditor-payment/(:num)'] = 'finance/creditors/edit_creditor_payment/$1';


$route['company-financials'] = 'financials/company_financial/index';
$route['company-financials/profit-and-loss'] = 'financials/company_financial/profit_and_loss';
$route['print-income-statement'] = 'financials/company_financial/print_income_statement';
$route['company-financials/balance-sheet'] = 'financials/company_financial/balance_sheet';
$route['print-balance-sheet'] = 'financials/company_financial/print_balance_sheet';


$route['accounting/expense-ledger/(:num)'] = 'financials/company_financial/expense_ledger/$1';
$route['accounting/expense-ledger/(:num)/(:num)'] = 'financials/company_financial/expense_ledger/$1/$2';
$route['accounting/print-expenses-ledger']= 'financials/company_financial/print_expense_ledger';


$route['account-transactions/(:num)'] =  'financials/company_financial/account_ledger/$1';
$route['account-transactions/(:num)/(:num)'] = 'financials/company_financial/account_ledger/$1/$2';
$route['accounts-receivables'] = 'financials/company_financial/search_customer_income_list';
$route['customer-invoices/(:num)'] = 'financials/company_financial/search_customer_invoices/$1';


$route['accounts-payables'] = 'financials/company_financial/search_creditor_expense_list';




$route['company-financials/services-bills/(:num)']  = 'financials/company_financial/services_bills/$1';
$route['company-financials/services-bills/(:num)/(:num)']  = 'financials/company_financial/services_bills/$1/$2';


$route['company-financials/aged-receivables'] = 'financials/company_financial/aged_receivables';
$route['company-financials/sales-taxes'] = 'financials/company_financial/sales_taxes';
$route['company-financials/customer-income'] = 'financials/company_financial/customers_income';
$route['company-financials/vendor-expenses'] = 'financials/company_financial/vendor_expenses';
$route['company-financials/aged-payables'] = 'financials/company_financial/aged_payables';
$route['creditor-statement/(:num)'] = 'financials/company_financial/creditor_statement/$1';
$route['print-creditor-statement/(:num)'] = 'financials/company_financial/print_creditor_statement/$1';


$route['view-closing-stock'] = 'financials/company_financial/view_closing_stock';
$route['view-purchases']  = 'financials/company_financial/view_purchases';
$route['view-return-outwards']  = 'financials/company_financial/view_return_outwards';
$route['view-other-deductions']  = 'financials/company_financial/view_other_deductions';
$route['view-current-stock']  = 'financials/company_financial/view_current_stock';
$route['view-other-additions']  = 'financials/company_financial/view_other_additions';


$route['view-closing-stock/(:num)'] = 'financials/company_financial/view_closing_stock/$1';
$route['view-purchases/(:num)']  = 'financials/company_financial/view_purchases/$1';
$route['view-return-outwards/(:num)']  = 'financials/company_financial/view_return_outwards/$1';
$route['view-other-deductions/(:num)']  = 'financials/company_financial/view_other_deductions/$1';
$route['view-current-stock/(:num)']  = 'financials/company_financial/view_current_stock/$1';
$route['view-other-additions/(:num)']  = 'financials/company_financial/view_other_additions/$1';

$route['search-stock-report/(:num)'] = 'financials/company_financial/search_stock_report/$1';

$route['company-financials/general-ledger'] = 'financials/company_financial/general_ledger';
$route['company-financials/account-transactions'] = 'financials/company_financial/account_transactions';



$route['messaging/contacts'] = 'messaging/contacts/index';
$route['messaging/contacts/(:num)'] = 'messaging/contacts/index/$1';
$route['add-contact'] = 'messaging/contacts/add_contact';
$route['edit-contact/(:num)'] = 'messaging/contacts/edit_contact/$1';
$route['contacts'] = 'messaging/contacts/index';
$route['contacts/(:num)'] = 'messaging/contacts/index/$1';
$route['delete-contact/(:num)'] = 'messaging/contacts/delete_contact/$1';
$route['contacts/validate-import/(:num)'] = 'messaging/contacts/do_messages_import/$1';
$route['contacts/import-template'] = 'messaging/contacts/import_template';
$route['contacts/import-messages'] = 'messaging/contacts/import_messages';


$route['view-creditor-invoice/(:num)'] = 'finance/creditors/print_creditor_invoice/$1';



// $route['online-diary'] = 'admin/uhdc_calendar';


$route['print-quotation/(:num)'] = 'accounts/print_quote/$1';

// $route['inventory/drug-trail'] = 'inventory_management/drug_trail';
$route['inventory/drug-trail/(:num)'] = 'inventory_management/drug_trail/$1';
$route['print-drug-trail/(:num)'] = 'inventory_management/print_drug_trails/$1';



$route['inventory/product-deductions'] = 'inventory_management/all_product_deductions';
$route['inventory/deduction-product/(:num)'] = 'inventory_management/product_deductions/$1';
$route['inventory/edit-product-deduction/(:num)/(:num)'] = 'inventory_management/edit_product_deduction/$1/$2';


$route['inventory/deduction-product/(:num)/(:num)'] = 'inventory_management/product_deductions/$1/$2';
$route['inventory/edit-product-deduction/(:num)/(:num)'] = 'inventory_management/edit_product_deduction/$1/$2';

$route['search-pharmacy-sales'] = 'pharmacy/search_drugs_sales';
$route['deductions/(:num)/(:num)'] = 'inventory_management/products/deductions/$1/$2';
$route['inventory/deduct-product/(:num)/(:num)'] = 'inventory_management/deduct_product/$1/$2';
$route['inventory/return-product/(:num)/(:num)'] = 'inventory_management/return_product/$1/$2';
$route['approve-request-order/(:num)'] = 'inventory_management/approve_request_order/$1';
$route['inventory/print-product-out-stock'] = 'inventory_management/out_of_stock';
$route['import/product-codes'] = 'inventory_management/products/import_product_codes';
$route['import/import_product_codes-template'] = 'inventory_management/products/import_product_codes_template';



$route['procurement/general-orders'] = 'inventory/orders/index';
$route['procurement/general-orders/(:num)'] = 'inventory/orders/index/$1';
$route['remove-item/(:num)/(:any)/(:num)'] = 'inventory/orders/remove_supplier_order/$1/$2/$3';
$route['inventory/delete-product/(:num)'] = 'inventory_management/delete_product/$1';
$route['inventory/import-products'] = 'inventory_management/products/import_products';
$route['inventory/export-products'] = 'inventory_management/products/export_products';

$route['procurement/drug-transfers'] = 'inventory/orders/drug_transfers';
$route['procurement/drug-transfers/(:num)'] = 'inventory/orders/drug_transfers/$1';
$route['procurement/order-invoice-detail/(:num)'] = 'inventory/orders/order_invoice_detail/$1';
$route['procurement/delete-invoices/(:num)'] = 'inventory/orders/suppliers_invoices/$1';
$route['update-invoice-date/(:num)'] = 'inventory/orders/update_orders_date/$1';
$route['delete-order-item/(:num)/(:num)/(:num)'] = 'inventory/orders/delete_supplier_order_item/$1/$2/$3';


$route['add-to-store/(:num)/(:num)'] = 'inventory_management/add_product_to_store/$1/$2';
$route['delete-transfer-item/(:num)/(:num)'] = 'inventory/orders/delete_transfer_order_item/$1/$2/$3';

$route['inventory/finish-transfer-order/(:num)'] = 'inventory/orders/finish_transfer_order/$1';
$route['goods-transfered-notes/(:num)'] = 'inventory/orders/goods_transfered/$1';
$route['search-products-purchased'] = 'inventory/orders/search_products_purchased';
$route['close-product-purchased-search'] = 'inventory/orders/close_product_purchased_search';
$route['regenerate-product/(:num)'] = 'inventory_management/products/regenerate_product/$1';


$route['inventory/s11'] = 'inventory_management/view_ordered_items';
$route['inventory/s11/(:num)'] = 'inventory_management/view_ordered_items/$1';
$route['reject-request/(:num)'] =  'inventory_management/reject_deduction/$1';

$route['procurement/store-orders'] = 'inventory_management/manage_store';
$route['procurement/store-orders/(:num)'] = 'inventory_management/manage_store/$1';
$route['inventory/store-deductions'] = 'inventory_management/view_all_product_deductions';
$route['inventory/store-deductions/(:num)'] = 'inventory_management/view_all_product_deductions/$1';
$route['inventory/search-instant-orders'] = 'inventory_management/search_orders_requested';
$route['inventory/search-store-deductions'] = 'inventory_management/search_store_deductions';
$route['inventory/drug-prices'] = 'inventory_management/drug_prices';
$route['inventory/drug-prices/(:num)'] = 'inventory_management/drug_prices/$1';
$route['inventory/search-product-prices'] = 'inventory_management/search_product_requested';


$route['procurement/general-orders'] = 'inventory/orders/general_orders';
$route['procurement/general-orders/(:num)'] = 'inventory/orders/general_orders/$1';
$route['remove-item/(:num)/(:any)/(:num)'] = 'inventory/orders/remove_supplier_order/$1/$2/$3';
$route['delete-product/(:num)'] = 'inventory_management/delete_product/$1';
$route['inventory/import-products'] = 'inventory_management/products/import_products';
$route['inventory/export-products'] = 'inventory_management/products/export_products';
$route['inventory/add-general-order'] = 'inventory/orders/add_general_order';
$route['inventory/add-general-order-item/(:num)/(:any)'] = 'inventory/orders/add_general_order_item/$1/$2';
$route['inventory/delete-general-order-item/(:num)/(:num)/(:any)'] = 'inventory/orders/delete_general_order_item/$1/$2/$3';

$route['inventory/update-general-order-item/(:num)/(:any)/(:num)'] = 'inventory/orders/update_general_order_item/$1/$2/$3';
$route['inventory/send-general-order-for-approval/(:num)/(:num)'] = 'inventory/orders/send_general_order_for_approval/$1/$2';

$route['inventory/print-general-order/(:num)'] = 'inventory/orders/print_general_order/$1';


$route['company-financials/accounts-ledgers'] = 'financials/ledgers/accounts_ledgers';
$route['company-financials/accounts-ledgers/(:num)'] = 'financials/ledgers/accounts_ledgers/(:num)';
$route['print-account-ledger']  = 'financials/ledgers/print_account_ledger';
$route['export-account-ledger'] = 'financials/ledgers/export_account_ledger';



$route['accounting/general-journal-entries'] = 'financials/company_financial/account_balances';
$route['accounting/general-journal-entries/activate-account/(:num)'] = 'financials/company_financial/activate_account/$1';
$route['accounting/general-journal-entries/deactivate-account/(:num)'] = 'financials/company_financial/deactivate_account/$1';
$route['accounting/general-journal-entries/edit-account/(:num)'] = 'financials/company_financial/edit_account/$1';
$route['accounting/add-account'] = 'financials/company_financial/add_account';







$route['accounting/charts-of-accounts'] = 'financials/company_financial/account_balances';
$route['accounting/charts-of-accounts/(:num)'] = 'financials/company_financial/account_balances/$1';
$route['accounting/charts-of-accounts/activate-account/(:num)'] = 'financials/company_financial/activate_account/$1';
$route['accounting/charts-of-accounts/deactivate-account/(:num)'] = 'financials/company_financial/deactivate_account/$1';
$route['accounting/charts-of-accounts/edit-account/(:num)'] = 'financials/company_financial/edit_account/$1';
$route['accounting/add-account'] = 'financials/company_financial/add_account';


$route['accounting/direct-payments'] = 'finance/transfer/direct_payments';
$route['accounting/direct-payments/(:num)'] = 'finance/transfer/direct_payments/$1';
$route['delete-payment-direct-payments/(:num)'] = 'accounting/petty_cash/delete_direct_payment/$1';


$route['accounting/journal-entry'] = 'finance/transfer/journal_entry';
$route['accounting/journal-entry/(:num)'] = 'finance/transfer/journal_entry/$1';
$route['delete-journal-entry/(:num)'] = 'accounting/petty_cash/delete_journal_entry/$1';



