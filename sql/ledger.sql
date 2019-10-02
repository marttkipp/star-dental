--  All transaction for invoice and there respective payment
-- Invocie based on patient Vist


CREATE OR REPLACE VIEW `v_all_invoice_payments` AS
SELECT
	visit_charge.visit_charge_id AS transaction_id,
	visit.visit_id AS reference_id,
	visit.invoice_number AS reference_code,
	'' AS transactionCode,
	visit.patient_id AS patient_id,
	'' AS supplier_id,
	'' AS supplier_name,
	service.service_id AS parent_service,
	visit_charge.service_charge_id AS child_service,
	visit.personnel_id AS personnel_id,
	( SELECT personnel.personnel_surname FROM personnel WHERE personnel.personnel_id = visit.personnel_id ) AS personnel_name,
	visit.visit_type AS payment_type,
	( SELECT visit_type_name FROM visit_type WHERE visit_type.visit_type_id = visit.visit_type ) AS payment_type_name,
	'' AS payment_method_id,
	'' AS payment_method_name,
	account.parent_account AS account_parent,
	service.account_id AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	service_charge.service_charge_name AS transaction_name,
	CONCAT( "Charged for ", service.service_name, " : ", service_charge.service_charge_name ) AS transaction_description,
	'0' AS dr_amount,
	visit_charge.visit_charge_amount AS cr_amount,
	visit.visit_date AS transaction_date,
	visit_charge.visit_charge_timestamp AS created_at,
	visit_charge.charged AS `status`,
	'Patient' AS party,
	'Revenue' AS transactionCategory,
	'Invoice Patients' AS transactionClassification,
	'visit_charge' AS transactionTable,
	'visit' AS referenceTable 
FROM
	visit_charge
	JOIN visit ON visit.visit_id = visit_charge.visit_id
	LEFT JOIN service_charge ON service_charge.service_charge_id = visit_charge.service_charge_id
	LEFT JOIN service ON service.service_id = service_charge.service_id
	LEFT JOIN account ON service.account_id = account.account_id

UNION ALL
-- Invoice Payments
-- Invocie based on patient Vist
SELECT
	payments.payment_id AS transaction_id,
	visit.visit_id AS reference_id,
	visit.invoice_number AS reference_code,
	payments.transaction_code AS transactionCode,
	visit.patient_id AS patient_id,
	'' AS supplier_id,
	'' AS supplier_name,
	'' AS parent_service,
	'' AS child_service,
	'' AS personnel_id,
	'' AS personnel_name,
	visit.visit_type AS payment_type,
	(SELECT visit_type_name FROM visit_type WHERE visit_type.visit_type_id = visit.visit_type) AS payment_type_name,
	payments.payment_method_id AS payment_method_id,
	payment_method.payment_method AS payment_method_name,
	account.parent_account AS account_parent,
	account.account_id AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	CONCAT( "Payemnt", " : " ) AS transaction_name,
	CONCAT( "Payment for ", visit.invoice_number ) AS transaction_description,
	payments.amount_paid AS dr_amount,
	'0' AS cr_amount,
	visit.visit_date AS transaction_date,
	payments.payment_created AS created_at,
	payments.payment_status AS `status`,
	'Patient' as party,
	'Revenue Payment' AS transactionCategory,
	'Invoice Patients Payment' AS transactionClassification,
	'payments' AS transactionTable,
	'visit' AS referenceTable 
FROM
	payments
	JOIN visit ON visit.visit_id = payments.visit_id
	LEFT JOIN payment_method ON payments.payment_method_id = payment_method.payment_method_id
	LEFT JOIN account ON payment_method.account_id = account.account_id
	JOIN visit_type ON visit.visit_type = visit_type.visit_type_id;


CREATE OR REPLACE VIEW `v_creditor_all_invoice_payments` AS
SELECT
	`creditor_invoice_item`.`creditor_invoice_item_id` AS transaction_id,
	`creditor_invoice`.`creditor_invoice_id` AS reference_id,
	`creditor_invoice`.`invoice_number` AS reference_code,
	'' AS transactionCode,
	`creditor_invoice`.`property_id` AS patient_id,
	`creditor_invoice`.`creditor_id` AS supplier_id,
	'' AS supplier_name,
	'' AS parent_service,
	'' AS child_service,
	'' AS personnel_id,
	'' AS personnel_name,
	'' AS payment_type,
	'' AS payment_type_name,
	'' AS payment_method_id,
	'' AS payment_method_name,
	account.parent_account AS account_parent,
	`creditor_invoice_item`.`account_to_id` AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	`creditor_invoice_item`.`item_description` AS transaction_name,
	`creditor_invoice_item`.`item_description` AS transaction_description,
	`creditor_invoice_item`.`total_amount` AS dr_amount,
	'0' AS cr_amount,
	`creditor_invoice`.`transaction_date` AS transaction_date,
	`creditor_invoice`.`created` AS created_at,
	`creditor_invoice_item`.`creditor_invoice_item_status` AS `status`,
	'Supplier' AS party,
	'Expense' AS transactionCategory,
	'Creditors Invoices' AS transactionClassification,
	'creditor_invoice_item' AS transactionTable,
	'creditor_invoice' AS referenceTable 
FROM
	(
	(
	( `creditor_invoice_item` JOIN `creditor_invoice` ON ( ( `creditor_invoice`.`creditor_invoice_id` = `creditor_invoice_item`.`creditor_invoice_id` ) ) )
	JOIN `account` ON ( ( `account`.`account_id` = `creditor_invoice_item`.`account_to_id` ) ) 
	)
	JOIN `account_type` ON ( ( `account_type`.`account_type_id` = `account`.`account_type_id` ) ) 
	) UNION ALL-- Bill Payments
SELECT
	`creditor_payment_item`.`creditor_payment_item_id` AS transaction_id,
	`creditor_payment`.`creditor_payment_id` AS reference_id,
	`creditor_payment`.`reference_number` AS reference_code,
	`creditor_payment_item`.`creditor_invoice_id` AS transactionCode,
	'' AS patient_id,
	creditor_payment.creditor_id AS supplier_id,
	'' AS supplier_name,
	'' AS parent_service,
	'' AS child_service,
	'' AS personnel_id,
	'' AS personnel_name,
	'' AS payment_type,
	'' AS payment_type_name,
	'' AS payment_method_id,
	'' AS payment_method_name,
	account.parent_account AS account_parent,
	`creditor_payment`.`account_from_id` AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	`creditor_payment_item`.`description` AS transaction_name,
	`creditor_payment_item`.`description` AS transaction_description,
	'' AS dr_amount,
	`creditor_payment_item`.`amount_paid` AS cr_amount,
	`creditor_payment`.`transaction_date` AS transaction_date,
	`creditor_payment`.`created` AS created_at,
	`creditor_payment_item`.`creditor_payment_item_status` AS `status`,
	'Supplier' AS party,
	'Expense Payment' AS transactionCategory,
	'Creditors Invoices Payments' AS transactionClassification,
	'creditor_payment' AS transactionTable,
	'creditor_payment_item' AS referenceTable 
FROM
	(
	(
	( `creditor_payment_item` JOIN `creditor_payment` ON ( ( `creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id` ) ) )
	JOIN `account` ON ( ( `account`.`account_id` = `creditor_payment`.`account_from_id` ) ) 
	)
	JOIN `account_type` ON ( ( `account_type`.`account_type_id` = `account`.`account_type_id` ) ) 
	) 

	UNION ALL-- creditor memo Payment
SELECT
	`creditor_credit_note_item`.`creditor_credit_note_item_id` AS transaction_id,
	`creditor_credit_note`.`creditor_credit_note_id` AS reference_id,
	`creditor_credit_note`.`invoice_number` AS reference_code,
	`creditor_credit_note_item`.`creditor_invoice_id` AS transactionCode,
	'' AS patient_id,
	creditor_credit_note.creditor_id AS supplier_id,
	'' AS supplier_name,
	'' AS parent_service,
	'' AS child_service,
	'' AS personnel_id,
	'' AS personnel_name,
	'' AS payment_type,
	'' AS payment_type_name,
	'' AS payment_method_id,
	'' AS payment_method_name,
	`account`.`parent_account` AS account_parent,
	`creditor_credit_note`.`account_from_id` AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	`creditor_credit_note_item`.`description` AS transaction_name,
	`creditor_credit_note_item`.`description` AS transaction_description,
	'0' AS dr_amount,
	`creditor_credit_note_item`.`credit_note_amount` AS cr_amount,
	`creditor_credit_note`.`transaction_date` AS transaction_date,
	`creditor_credit_note`.`created` AS created_at,
	`creditor_credit_note_item`.`creditor_credit_note_item_status` AS `status`,
	'Supplier' AS party,
	'Expense Payment' AS transactionCategory,
	'Creditors Credit Notes Invoices Payments' AS transactionClassification,
	'creditor_credit_note' AS transactionTable,
	'creditor_credit_note_item' AS referenceTable 
FROM
	
	`creditor_credit_note_item` 
	LEFT JOIN `creditor_credit_note` ON `creditor_credit_note`.`creditor_credit_note_id` = `creditor_credit_note_item`.`creditor_credit_note_id`
	LEFT JOIN `account` ON `account`.`account_id` = `creditor_credit_note`.`account_from_id`
	LEFT JOIN `account_type` ON `account_type`.`account_type_id` = `account`.`account_type_id` ;

