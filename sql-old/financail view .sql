--  The 1 View
-- create view for all invoice and payments together

CREATE OR REPLACE VIEW  `v_all_invoice_payments` AS
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
	LEFT JOIN service ON service.service_id = service_charge.service_id
	LEFT JOIN account ON service.account_id = account.account_id
	WHERE visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0

UNION ALL-- Invoice Payments

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
	( SELECT visit_type_name FROM visit_type WHERE visit_type.visit_type_id = visit.visit_type ) AS payment_type_name,
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
	( SELECT visit_type_name FROM visit_type WHERE visit_type.visit_type_id = visit.visit_type ) AS payment_type_name,
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
	( SELECT visit_type_name FROM visit_type WHERE visit_type.visit_type_id = visit.visit_type ) AS payment_type_name,
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
--  creditor Invoice Payments

CREATE OR REPLACE VIEW `v_creditor_all_invoice_payments` AS -- Bills
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
	) UNION ALL-- creditor memo Payment
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
	(
	(
	( `creditor_credit_note_item` JOIN `creditor_credit_note` ON ( ( `creditor_credit_note`.`creditor_credit_note_id` = `creditor_credit_note_item`.`creditor_credit_note_id` ) ) )
	JOIN `account` ON ( ( `account`.`account_id` = `creditor_credit_note`.`account_from_id` ) )
	)
	JOIN `account_type` ON ( ( `account_type`.`account_type_id` = `account`.`account_type_id` ) )
	);
--
--
-- DROP VIEW
-- IF
-- 	EXISTS `v_transaction_all_invoice_payments`;
-- CREATE VIEW `chepsoo`.`v_transaction_all_invoice_payments` AS SELECT
-- *
-- FROM
-- 	v_all_invoice_payments UNION ALL
-- SELECT
-- 	*
-- FROM
-- 	v_creditor_all_invoice_payments;
--
-- -- Account Payables
CREATE OR REPLACE VIEW `v_account_payables` AS
SELECT
	`creditor_invoice`.`creditor_id` AS `creditor_id`,
	`creditor`.`creditor_name` AS `payables`,
	(
CASE

	WHEN (
	sum(
IF
(
( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
	`creditor_invoice_item`.`total_amount`,
	0
	)
	) = 0
	) THEN
		0
		WHEN (
			sum(
			IF
				(
					( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
					`creditor_invoice_item`.`total_amount`,
					0
				)
			) > 0
			) THEN
			(
				(
					sum(
					IF
						(
							( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
							`creditor_invoice_item`.`total_amount`,
							0
						)
					) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
				) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
			)
		END
		) AS `coming_due`,
		(
		CASE

				WHEN (
					sum(
					IF
						(
							( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
							`creditor_invoice_item`.`total_amount`,
							0
						)
					) = 0
					) THEN
					0
					WHEN (
						sum(
						IF
							(
								( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
								`creditor_invoice_item`.`total_amount`,
								0
							)
						) > 0
						) THEN
						(
							(
								(
									sum(
									IF
										(
											( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
											`creditor_invoice_item`.`total_amount`,
											0
										)
									) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
								) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
							) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
						)
					END
					) AS `thirty_days`,
					(
					CASE

							WHEN (
								sum(
								IF
									(
										( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
										`creditor_invoice_item`.`total_amount`,
										0
									)
								) = 0
								) THEN
								0
								WHEN (
									sum(
									IF
										(
											( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
											`creditor_invoice_item`.`total_amount`,
											0
										)
									) > 0
									) THEN
									(
										(
											(
												sum(
												IF
													(
														( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
														`creditor_invoice_item`.`total_amount`,
														0
													)
												) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
											) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
										) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
									)
								END
								) AS `sixty_days`,
								(
								CASE

										WHEN (
											sum(
											IF
												(
													( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
													`creditor_invoice_item`.`total_amount`,
													0
												)
											) = 0
											) THEN
											0
											WHEN (
												sum(
												IF
													(
														( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
														`creditor_invoice_item`.`total_amount`,
														0
													)
												) > 0
												) THEN
												(
													(
														(
															sum(
															IF
																(
																	( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																	`creditor_invoice_item`.`total_amount`,
																	0
																)
															) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
														) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
													) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
												)
											END
											) AS `ninety_days`,
											(
											CASE

													WHEN (
														sum(
														IF
															(
																( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																`creditor_invoice_item`.`total_amount`,
																0
															)
														) = 0
														) THEN
														0
														WHEN (
															sum(
															IF
																(
																	( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																	`creditor_invoice_item`.`total_amount`,
																	0
																)
															) > 0
															) THEN
															(
																(
																	(
																		sum(
																		IF
																			(
																				( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																				`creditor_invoice_item`.`total_amount`,
																				0
																			)
																		) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																	) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
															)
														END
														) AS `over_ninety_days`,
														(
															(
																(
																	(
																		(
																		CASE

																				WHEN (
																					sum(
																					IF
																						(
																							( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
																							`creditor_invoice_item`.`total_amount`,
																							0
																						)
																					) = 0
																					) THEN
																					0
																					WHEN (
																						sum(
																						IF
																							(
																								( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
																								`creditor_invoice_item`.`total_amount`,
																								0
																							)
																						) > 0
																						) THEN
																						(
																							(
																								(
																									sum(
																									IF
																										(
																											( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) = 0 ),
																											`creditor_invoice_item`.`total_amount`,
																											0
																										)
																									) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																								) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																							) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																						)
																					END
																						) + (
																					CASE

																							WHEN (
																								sum(
																								IF
																									(
																										( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																										`creditor_invoice_item`.`total_amount`,
																										0
																									)
																								) = 0
																								) THEN
																								0
																								WHEN (
																									sum(
																									IF
																										(
																											( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																											`creditor_invoice_item`.`total_amount`,
																											0
																										)
																									) > 0
																									) THEN
																									(
																										(
																											(
																												sum(
																												IF
																													(
																														( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																														`creditor_invoice_item`.`total_amount`,
																														0
																													)
																												) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																											) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																										) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																									)
																								END
																								)
																								) + (
																							CASE

																									WHEN (
																										sum(
																										IF
																											(
																												( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																												`creditor_invoice_item`.`total_amount`,
																												0
																											)
																										) = 0
																										) THEN
																										0
																										WHEN (
																											sum(
																											IF
																												(
																													( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																													`creditor_invoice_item`.`total_amount`,
																													0
																												)
																											) > 0
																											) THEN
																											(
																												(
																													(
																														sum(
																														IF
																															(
																																( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																`creditor_invoice_item`.`total_amount`,
																																0
																															)
																														) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																													) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																												) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																											)
																										END
																										)
																										) + (
																									CASE

																											WHEN (
																												sum(
																												IF
																													(
																														( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																														`creditor_invoice_item`.`total_amount`,
																														0
																													)
																												) = 0
																												) THEN
																												0
																												WHEN (
																													sum(
																													IF
																														(
																															( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																															`creditor_invoice_item`.`total_amount`,
																															0
																														)
																													) > 0
																													) THEN
																													(
																														(
																															(
																																sum(
																																IF
																																	(
																																		( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																		`creditor_invoice_item`.`total_amount`,
																																		0
																																	)
																																) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																															) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																														) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																													)
																												END
																												)
																												) + (
																											CASE

																													WHEN (
																														sum(
																														IF
																															(
																																( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																																`creditor_invoice_item`.`total_amount`,
																																0
																															)
																														) = 0
																														) THEN
																														0
																														WHEN (
																															sum(
																															IF
																																(
																																	( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																																	`creditor_invoice_item`.`total_amount`,
																																	0
																																)
																															) > 0
																															) THEN
																															(
																																(
																																	(
																																		sum(
																																		IF
																																			(
																																				( ( to_days( curdate( ) ) - to_days( cast( `creditor_invoice`.`transaction_date` AS date ) ) ) > 90 ),
																																				`creditor_invoice_item`.`total_amount`,
																																				0
																																			)
																																		) - ( SELECT COALESCE ( sum( `creditor_payment_item`.`amount_paid` ), 0 ) FROM `creditor_payment_item` WHERE ( `creditor_payment_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																																	) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																																) - ( SELECT COALESCE ( sum( `creditor_credit_note_item`.`credit_note_amount` ), 0 ) FROM `creditor_credit_note_item` WHERE ( `creditor_credit_note_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) )
																															)
																														END
																														)
																													) AS `Total`
																												FROM
																													(
																														( `creditor_invoice` LEFT JOIN `creditor_invoice_item` ON ( ( `creditor_invoice_item`.`creditor_invoice_id` = `creditor_invoice`.`creditor_invoice_id` ) ) )
																														LEFT JOIN `creditor` ON ( ( ( `creditor_invoice`.`creditor_id` = `creditor`.`creditor_id` ) AND ( `creditor_invoice`.`creditor_id` = `creditor`.`creditor_id` ) ) )
																													) UNION ALL
																												SELECT
																													`finance_purchase`.`creditor_id` AS `creditor_id`,
																													`creditor`.`creditor_name` AS `payables`,
																													(
																													CASE

																															WHEN (
																																sum(
																																IF
																																	(
																																		( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																		`finance_purchase`.`finance_purchase_amount`,
																																		0
																																	)
																																) = 0
																																) THEN
																																0
																																WHEN (
																																	sum(
																																	IF
																																		(
																																			( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																			`finance_purchase`.`finance_purchase_amount`,
																																			0
																																		)
																																	) > 0
																																	) THEN
																																	(
																																		sum(
																																		IF
																																			(
																																				( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																				`finance_purchase`.`finance_purchase_amount`,
																																				0
																																			)
																																			) - (
																																		SELECT COALESCE
																																			( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																		FROM
																																			( `finance_purchase_payment` JOIN `finance_purchase` )
																																		WHERE
																																			( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																		)
																																	)
																																END
																																) AS `coming_due`,
																																(
																																CASE

																																		WHEN (
																																			sum(
																																			IF
																																				(
																																					( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																					`finance_purchase`.`finance_purchase_amount`,
																																					0
																																				)
																																			) = 0
																																			) THEN
																																			0
																																			WHEN (
																																				sum(
																																				IF
																																					(
																																						( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																						`finance_purchase`.`finance_purchase_amount`,
																																						0
																																					)
																																				) > 0
																																				) THEN
																																				(
																																					sum(
																																					IF
																																						(
																																							( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																							`finance_purchase`.`finance_purchase_amount`,
																																							0
																																						)
																																						) - (
																																					SELECT COALESCE
																																						( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																					FROM
																																						( `finance_purchase_payment` JOIN `finance_purchase` )
																																					WHERE
																																						( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																					)
																																				)
																																			END
																																			) AS `thirty_days`,
																																			(
																																			CASE

																																					WHEN (
																																						sum(
																																						IF
																																							(
																																								( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																								`finance_purchase`.`finance_purchase_amount`,
																																								0
																																							)
																																						) = 0
																																						) THEN
																																						0
																																						WHEN (
																																							sum(
																																							IF
																																								(
																																									( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																									`finance_purchase`.`finance_purchase_amount`,
																																									0
																																								)
																																							) > 0
																																							) THEN
																																							(
																																								sum(
																																								IF
																																									(
																																										( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																										`finance_purchase`.`finance_purchase_amount`,
																																										0
																																									)
																																									) - (
																																								SELECT COALESCE
																																									( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																								FROM
																																									( `finance_purchase_payment` JOIN `finance_purchase` )
																																								WHERE
																																									( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																								)
																																							)
																																						END
																																						) AS `sixty_days`,
																																						(
																																						CASE

																																								WHEN (
																																									sum(
																																									IF
																																										(
																																											( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																											`finance_purchase`.`finance_purchase_amount`,
																																											0
																																										)
																																									) = 0
																																									) THEN
																																									0
																																									WHEN (
																																										sum(
																																										IF
																																											(
																																												( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																												`finance_purchase`.`finance_purchase_amount`,
																																												0
																																											)
																																										) > 0
																																										) THEN
																																										(
																																											sum(
																																											IF
																																												(
																																													( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																													`finance_purchase`.`finance_purchase_amount`,
																																													0
																																												)
																																												) - (
																																											SELECT COALESCE
																																												( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																											FROM
																																												( `finance_purchase_payment` JOIN `finance_purchase` )
																																											WHERE
																																												( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																											)
																																										)
																																									END
																																									) AS `ninety_days`,
																																									(
																																									CASE

																																											WHEN (
																																												sum(
																																												IF
																																													(
																																														( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																														`finance_purchase`.`finance_purchase_amount`,
																																														0
																																													)
																																												) = 0
																																												) THEN
																																												0
																																												WHEN (
																																													sum(
																																													IF
																																														(
																																															( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																															`finance_purchase`.`finance_purchase_amount`,
																																															0
																																														)
																																													) > 0
																																													) THEN
																																													(
																																														sum(
																																														IF
																																															(
																																																( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																																`finance_purchase`.`finance_purchase_amount`,
																																																0
																																															)
																																															) - (
																																														SELECT COALESCE
																																															( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																														FROM
																																															( `finance_purchase_payment` JOIN `finance_purchase` )
																																														WHERE
																																															( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																														)
																																													)
																																												END
																																												) AS `over_ninety_days`,
																																												(
																																													(
																																														(
																																															(
																																																(
																																																CASE

																																																		WHEN (
																																																			sum(
																																																			IF
																																																				(
																																																					( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																																					`finance_purchase`.`finance_purchase_amount`,
																																																					0
																																																				)
																																																			) = 0
																																																			) THEN
																																																			0
																																																			WHEN (
																																																				sum(
																																																				IF
																																																					(
																																																						( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																																						`finance_purchase`.`finance_purchase_amount`,
																																																						0
																																																					)
																																																				) > 0
																																																				) THEN
																																																				(
																																																					sum(
																																																					IF
																																																						(
																																																							( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) = 0 ),
																																																							`finance_purchase`.`finance_purchase_amount`,
																																																							0
																																																						)
																																																						) - (
																																																					SELECT COALESCE
																																																						( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																																					FROM
																																																						( `finance_purchase_payment` JOIN `finance_purchase` )
																																																					WHERE
																																																						( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																																					)
																																																				)
																																																			END
																																																				) + (
																																																			CASE

																																																					WHEN (
																																																						sum(
																																																						IF
																																																							(
																																																								( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																																								`finance_purchase`.`finance_purchase_amount`,
																																																								0
																																																							)
																																																						) = 0
																																																						) THEN
																																																						0
																																																						WHEN (
																																																							sum(
																																																							IF
																																																								(
																																																									( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																																									`finance_purchase`.`finance_purchase_amount`,
																																																									0
																																																								)
																																																							) > 0
																																																							) THEN
																																																							(
																																																								sum(
																																																								IF
																																																									(
																																																										( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 1 AND 30 ),
																																																										`finance_purchase`.`finance_purchase_amount`,
																																																										0
																																																									)
																																																									) - (
																																																								SELECT COALESCE
																																																									( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																																								FROM
																																																									( `finance_purchase_payment` JOIN `finance_purchase` )
																																																								WHERE
																																																									( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																																								)
																																																							)
																																																						END
																																																						)
																																																						) + (
																																																					CASE

																																																							WHEN (
																																																								sum(
																																																								IF
																																																									(
																																																										( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																																										`finance_purchase`.`finance_purchase_amount`,
																																																										0
																																																									)
																																																								) = 0
																																																								) THEN
																																																								0
																																																								WHEN (
																																																									sum(
																																																									IF
																																																										(
																																																											( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																																											`finance_purchase`.`finance_purchase_amount`,
																																																											0
																																																										)
																																																									) > 0
																																																									) THEN
																																																									(
																																																										sum(
																																																										IF
																																																											(
																																																												( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 31 AND 60 ),
																																																												`finance_purchase`.`finance_purchase_amount`,
																																																												0
																																																											)
																																																											) - (
																																																										SELECT COALESCE
																																																											( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																																										FROM
																																																											( `finance_purchase_payment` JOIN `finance_purchase` )
																																																										WHERE
																																																											( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																																										)
																																																									)
																																																								END
																																																								)
																																																								) + (
																																																							CASE

																																																									WHEN (
																																																										sum(
																																																										IF
																																																											(
																																																												( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																																												`finance_purchase`.`finance_purchase_amount`,
																																																												0
																																																											)
																																																										) = 0
																																																										) THEN
																																																										0
																																																										WHEN (
																																																											sum(
																																																											IF
																																																												(
																																																													( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																																													`finance_purchase`.`finance_purchase_amount`,
																																																													0
																																																												)
																																																											) > 0
																																																											) THEN
																																																											(
																																																												sum(
																																																												IF
																																																													(
																																																														( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) BETWEEN 61 AND 90 ),
																																																														`finance_purchase`.`finance_purchase_amount`,
																																																														0
																																																													)
																																																													) - (
																																																												SELECT COALESCE
																																																													( sum( `finance_purchase_payment`.`amount_paid` ), 0 )
																																																												FROM
																																																													( `finance_purchase_payment` JOIN `finance_purchase` )
																																																												WHERE
																																																													( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` )
																																																												)
																																																											)
																																																										END
																																																										)
																																																										) + (
																																																									CASE

																																																											WHEN (
																																																												sum(
																																																												IF
																																																													(
																																																														( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																																														`finance_purchase`.`finance_purchase_amount`,
																																																														0
																																																													)
																																																												) = 0
																																																												) THEN
																																																												0
																																																												WHEN (
																																																													sum(
																																																													IF
																																																														(
																																																															( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																																															`finance_purchase`.`finance_purchase_amount`,
																																																															0
																																																														)
																																																													) > 0
																																																													) THEN
																																																													(
																																																														sum(
																																																														IF
																																																															(
																																																																( ( to_days( curdate( ) ) - to_days( cast( `finance_purchase`.`transaction_date` AS date ) ) ) > 90 ),
																																																																`finance_purchase`.`finance_purchase_amount`,
																																																																0
																																																															)
																																																														) - ( SELECT COALESCE ( sum( `finance_purchase_payment`.`amount_paid` ), 0 ) FROM `finance_purchase_payment` WHERE ( `finance_purchase_payment`.`finance_purchase_id` = `finance_purchase`.`finance_purchase_id` ) )
																																																													)
																																																												END
																																																												)
																																																											) AS `Total`
																																																										FROM
																																																											( `finance_purchase` LEFT JOIN `creditor` ON ( ( `finance_purchase`.`creditor_id` = `creditor`.`creditor_id` ) ) )
																																																									GROUP BY
`creditor`.`creditor_id`;

-- Account Recievables
CREATE OR REPLACE VIEW `v_accounts_recievables` AS
SELECT
	visit.patient_id AS receivables_id,
	'Individual' AS receivables_type,
	CONCAT( "Patient ", patients.patient_surname, " Patient Number : ", patients.patient_number ) AS receivable_Name,-- DATEDIFF( CURDATE( ), visit_charge.date ) AS dueDate,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) = 0, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `0 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `1 - 30 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `31 - 60 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `61 - 90 Days`,
	( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `>90 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) = 0, visit_charge.visit_charge_amount, 0 ) ) + Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) ) -- 1-30 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) ) -- 31-60 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) ) -- 61-90 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) -- >90 days

	) AS `total owed`,
	(
		(
			( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) = 0, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
			) + (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 1-30 days
		+ (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 31-60 days
		+ (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 61-90 days
		+ (
			( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- >90 days

	) AS `total owed with Payment`
FROM
	visit_charge
	JOIN visit ON visit.visit_id = visit_charge.visit_id
	LEFT JOIN patients ON patients.patient_id = visit.patient_id
WHERE
	visit.visit_type = "1"
GROUP BY
visit.patient_id
union ALL
/*With the 3rd party paying the bills*/
SELECT
	visit.visit_type AS receivables_id,
	'Organizational' AS receivables_type,
	visit_type.visit_type_name AS receivable_Name,-- DATEDIFF( CURDATE( ), visit_charge.date ) AS dueDate,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) = 0, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `0 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `1 - 30 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `31 - 60 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) )
	) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `61 - 90 Days`,
	( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) ) AS `>90 Days`,
	(
	Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) = 0, visit_charge.visit_charge_amount, 0 ) ) + Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) ) -- 1-30 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) ) -- 31-60 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) ) -- 61-90 days
	+ Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) -- >90 days

	) AS `total owed`,
	(
		(
			( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) = 0, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
			) + (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 1-30 days
		+ (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 31-60 days
		+ (
			(
				Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, visit_charge.visit_charge_amount, 0 ) )
			) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- 61-90 days
		+ (
			( Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) ) ) - ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id ) )
		) -- >90 days

	) AS `total owed with Payment`
FROM
	visit_charge
	JOIN visit ON visit.visit_id = visit_charge.visit_id
	LEFT JOIN visit_type ON visit_type.visit_type_id = visit.visit_type
WHERE
	visit.visit_type != "1"
GROUP BY
visit.visit_type
