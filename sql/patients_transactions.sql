CREATE OR REPLACE VIEW v_transactions AS
SELECT
visit_charge.visit_charge_id AS transaction_id,
visit.visit_id AS reference_id,
visit.invoice_number AS reference_code,
'' AS transactionCode,
visit.patient_id AS patient_id,
'' AS supplier_id,
'' AS supplier_name,
'' AS parent_service,
visit_charge.service_charge_id AS child_service,
visit.personnel_id AS personnel_id,
'' AS personnel_name,
visit.visit_type AS payment_type,
visit_type.visit_type_name AS payment_type_name,
'' AS payment_method_id,
'' AS payment_method_name,
'' AS account_parent,
'0' AS account_id,
'' AS account_classification,
'' AS account_name,
'' AS transaction_name,
CONCAT( "Charged for ", service_charge.service_charge_name ) AS transaction_description,
(visit_charge.visit_charge_amount*visit_charge.visit_charge_units) AS dr_amount,
'0' AS cr_amount,
visit_charge.date AS transaction_date,
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
	LEFT JOIN visit_type ON visit_type.visit_type_id = visit.visit_type
	WHERE visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0

UNION ALL

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
	visit_type.visit_type_name AS payment_type_name,
	payments.payment_method_id AS payment_method_id,
	payment_method.payment_method AS payment_method_name,
	account.parent_account AS account_parent,
	account.account_id AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	CONCAT( "Payemnt", " : " ) AS transaction_name,
	CONCAT( "Payment for ", visit.invoice_number ) AS transaction_description,
	'0' AS dr_amount,
	payments.amount_paid AS cr_amount,
	payments.payment_created AS transaction_date,
	DATE(payments.time) AS created_at,
	payments.payment_status AS `status`,
	'Patient' AS party,
	'Revenue Payment' AS transactionCategory,
	'Invoice Patients Payment' AS transactionClassification,
	'payments' AS transactionTable,
	'visit' AS referenceTable
FROM
	payments
	JOIN visit ON visit.visit_id = payments.visit_id
	LEFT JOIN payment_method ON payments.payment_method_id = payment_method.payment_method_id
	LEFT JOIN account ON payment_method.account_id = account.account_id

	JOIN visit_type ON visit.visit_type = visit_type.visit_type_id
	WHERE payments.cancel = 0 AND payments.payment_type = 1

UNION ALL
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
	visit_type_name AS payment_type_name,
	payments.payment_method_id AS payment_method_id,
	payment_method.payment_method AS payment_method_name,
	account.parent_account AS account_parent,
	account.account_id AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	CONCAT( "Debit Note", " : " ) AS transaction_name,
	CONCAT( "Debit Note for ", visit.invoice_number ) AS transaction_description,
	payments.amount_paid AS dr_amount,
	'0' AS cr_amount,
	payments.payment_created AS transaction_date,
	DATE(payments.time) AS created_at,
	payments.payment_status AS `status`,
	'Patient' AS party,
	'Debit Notes' AS transactionCategory,
	'Debit Note Patients' AS transactionClassification,
	'payments' AS transactionTable,
	'visit' AS referenceTable
FROM
	payments
	JOIN visit ON visit.visit_id = payments.visit_id
	LEFT JOIN payment_method ON payments.payment_method_id = payment_method.payment_method_id
	LEFT JOIN account ON payment_method.account_id = account.account_id
	JOIN visit_type ON visit.visit_type = visit_type.visit_type_id
	WHERE payments.cancel = 0 AND payments.payment_type = 2

UNION ALL

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
	visit_type_name AS payment_type_name,
	payments.payment_method_id AS payment_method_id,
	payment_method.payment_method AS payment_method_name,
	account.parent_account AS account_parent,
	account.account_id AS account_id,
	account.account_type_id AS account_classification,
	account.account_name AS account_name,
	CONCAT( "Credit Note", " : " ) AS transaction_name,
	CONCAT( "Credit Note for ", visit.invoice_number ) AS transaction_description,
	'0' AS dr_amount,
	payments.amount_paid AS cr_amount,
	payments.payment_created AS transaction_date,
	DATE(payments.time) AS created_at,
	payments.payment_status AS `status`,
	'Patient' AS party,
	'Credit Notes' AS transactionCategory,
	'Credit Note Patients' AS transactionClassification,
	'payments' AS transactionTable,
	'visit' AS referenceTable
FROM
	payments
	JOIN visit ON visit.visit_id = payments.visit_id
	LEFT JOIN payment_method ON payments.payment_method_id = payment_method.payment_method_id
	LEFT JOIN account ON payment_method.account_id = account.account_id
	JOIN visit_type ON visit.visit_type = visit_type.visit_type_id
	WHERE payments.cancel = 0 AND payments.payment_type = 3;
CREATE OR REPLACE VIEW v_transactions_by_date  AS select * from v_transactions ORDER BY transaction_date ASC;
