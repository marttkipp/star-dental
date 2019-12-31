ALTER TABLE `visit` ADD `scheme_name` VARCHAR(255) NOT NULL AFTER `rejected_by`, ADD `insurance_number` VARCHAR(255) NOT NULL AFTER `scheme_name`, ADD `invoice_number` VARCHAR(255) NOT NULL AFTER `insurance_number`;


ALTER TABLE `patients` ADD `scheme_name` VARCHAR(255) NOT NULL AFTER `ward_id`, ADD `insurance_number` VARCHAR(255) NOT NULL AFTER `scheme_name`;
ALTER TABLE `visit` ADD `mcc` INT NOT NULL AFTER `invoice_number`;
ALTER TABLE `visit_charge` ADD `charged` INT NOT NULL DEFAULT '1' AFTER `visit_lab_test_id`;
ALTER TABLE `patients` ADD `patient_year` INT NOT NULL AFTER `about_us_view`;
ALTER TABLE `visit_type` ADD `visit_type_preffix` VARCHAR(200) NOT NULL AFTER `insurance_company_id`;
ALTER TABLE `branch` ADD `inventory_start_date` DATE NOT NULL AFTER `branch_working_hours`;
ALTER TABLE `service_charge` ADD `vatable` INT(0) NOT NULL AFTER `product_id`;
ALTER TABLE `visit_charge` ADD `store_id` INT NOT NULL DEFAULT '5' AFTER `charged`;
ALTER TABLE `visit_charge` ADD `buying_price` FLOAT NOT NULL AFTER `store_id`;
ALTER TABLE `visit_charge` ADD `product_id` INT NOT NULL AFTER `buying_price`;
ALTER TABLE `payment_method` ADD `account_id` INT NOT NULL AFTER `payment_method`;
ALTER TABLE `visit` ADD `hold_card` INT NOT NULL DEFAULT '0' AFTER `mcc`;
ALTER TABLE `patients` ADD `patient_type` INT NOT NULL DEFAULT '0' AFTER `patient_year`;
ALTER TABLE `service` ADD `account_id` INT(50) NOT NULL AFTER `service_id`;
ALTER TABLE `creditor` ADD `start_date` DATE NOT NULL AFTER `creditor_city`;
ALTER TABLE `creditor` ADD `opening_balance` DOUBLE NOT NULL AFTER `start_date`;
ALTER TABLE `creditor` ADD `debit_id` INT NOT NULL DEFAULT '0' AFTER `opening_balance`;
ALTER TABLE `finance_purchase` ADD `department_id` INT(50) NOT NULL AFTER `creditor_id`;
ALTER TABLE `petty_cash` ADD `petty_cash_delete` INT NOT NULL DEFAULT '0' AFTER `from_account_id`;
ALTER TABLE `visit` ADD `parent_visit` INT NOT NULL DEFAULT '0' AFTER `hold_card`;