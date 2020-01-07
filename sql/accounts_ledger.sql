CREATE OR REPLACE VIEW v_account_ledger AS

SELECT
	`account`.`account_id` AS `transactionId`,
	'' AS `referenceId`,
	'' AS `payingFor`,
	'' AS `referenceCode`,
	'' AS `transactionCode`,
	'' AS `patient_id`,
    '' AS `recepientId`,
	account.parent_account AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	account.account_id AS `accountId`,
	account.account_name AS `accountName`,
	CONCAT('Opening Balance as from',' ',`account`.`start_date`) AS `transactionName`,
	CONCAT('Opening Balance as from',' ',' ',`account`.`start_date`) AS `transactionDescription`,
	`account`.`account_opening_balance` AS `dr_amount`,
	'0' AS `cr_amount`,
	`account`.`start_date` AS `transactionDate`,
	`account`.`start_date` AS `createdAt`,
	`account`.`account_status` AS `status`,
	'Income' AS `transactionCategory`,
	'Account Opening Balance' AS `transactionClassification`,
	'' AS `transactionTable`,
	'account' AS `referenceTable`
FROM
account
LEFT JOIN `account_type` ON(
			(
				account_type.account_type_id = account.account_type_id
			)
		)
WHERE account.parent_account = 2

UNION ALL

SELECT
  	`finance_transfered`.`finance_transfered_id` AS `transactionId`,
  	`finance_transfer`.`finance_transfer_id` AS `referenceId`,
  	'' AS `payingFor`,
  	`finance_transfer`.`reference_number` AS `referenceCode`,
  	`finance_transfer`.`document_number` AS `transactionCode`,
  	'' AS `patient_id`,
    '' AS `recepientId`,
  	`account`.`parent_account` AS `accountParentId`,
  	`account_type`.`account_type_name` AS `accountsclassfication`,
  	`finance_transfered`.`account_to_id` AS `accountId`,
  	`account`.`account_name` AS `accountName`,
  	`finance_transfered`.`remarks` AS `transactionName`,
  	 CONCAT('Amount Received from ',(SELECT account_name FROM account WHERE account_id = finance_transfer.account_from_id ),' Ref. ', `finance_transfer`.`reference_number`) AS `transactionDescription`,
  	`finance_transfered`.`finance_transfered_amount` AS `dr_amount`,
     0 AS `cr_amount`,
  	`finance_transfer`.`transaction_date` AS `transactionDate`,
  	`finance_transfer`.`created` AS `createdAt`,
  	`finance_transfer`.`finance_transfer_status` AS `status`,
  	'Transfer' AS `transactionCategory`,
  	'Transfer' AS `transactionClassification`,
  	'finance_transfer' AS `transactionTable`,
  	'finance_transfered' AS `referenceTable`
  FROM
  	(
  		(
  			(
  				`finance_transfered`
  				JOIN `finance_transfer` ON(
  					(
  						finance_transfer.finance_transfer_id = finance_transfered.finance_transfer_id
  					)
  				)
  			)
  			JOIN account ON(
  				(
  					account.account_id = finance_transfered.account_to_id 
  				)
  			)
  		)
  		JOIN `account_type` ON(
  			(
  				account_type.account_type_id = account.account_type_id
  			)
  		)
  	)

UNION ALL


SELECT
	`finance_purchase_payment`.`finance_purchase_payment_id` AS `transactionId`,
	'' AS `referenceId`,
	`finance_purchase`.`finance_purchase_id` AS `payingFor`,
	`finance_purchase`.`transaction_number` AS `referenceCode`,
	`finance_purchase`.`document_number` AS `transactionCode`,
	'' AS `patient_id`,
  	finance_purchase.creditor_id AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`finance_purchase_payment`.`account_from_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	`finance_purchase`.`finance_purchase_description` AS `transactionName`,
	CONCAT(`account`.`account_name`, ' paying for invoice ',`finance_purchase`.`transaction_number`,' Ref. ', `finance_purchase`.`transaction_number`) AS `transactionDescription`,
	0 AS `dr_amount`,
	`finance_purchase_payment`.`amount_paid` AS `cr_amount`,
	`finance_purchase`.`transaction_date` AS `transactionDate`,
	`finance_purchase`.`created` AS `createdAt`,
	`finance_purchase_payment`.`finance_purchase_payment_status` AS `status`,
	'Expense Payment' AS `transactionCategory`,
	'Purchase Payment' AS `transactionClassification`,
	'finance_purchase' AS `transactionTable`,
	'finance_purchase_payment' AS `referenceTable`
FROM
	(
		(
			(
				`finance_purchase_payment`
				JOIN `finance_purchase` ON(
					(
						finance_purchase.finance_purchase_id = finance_purchase_payment.finance_purchase_id
					)
				)
			)
			JOIN account ON(
				(
					account.account_id = finance_purchase_payment.account_from_id
				)
			)
		)
		JOIN `account_type` ON(
			(
				account_type.account_type_id = account.account_type_id
			)
		)
	)

UNION ALL


SELECT
	`finance_purchase`.`finance_purchase_id` AS `transactionId`,
	'' AS `referenceId`,
	'' AS `payingFor`,
	`finance_purchase`.`transaction_number` AS `referenceCode`,

	`finance_purchase`.`document_number` AS `transactionCode`,
	'' AS `patient_id`,
	`finance_purchase`.`creditor_id` AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`finance_purchase`.`account_to_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	`finance_purchase`.`finance_purchase_description` AS `transactionName`,
	CONCAT(`account`.`account_name`, ' paying for invoice ',`finance_purchase`.`transaction_number`,' Ref. ', `finance_purchase`.`transaction_number`) AS `transactionDescription`,
	`finance_purchase`.`finance_purchase_amount` AS `dr_amount`,
	0 AS `cr_amount`,
	`finance_purchase`.`transaction_date` AS `transactionDate`,
	`finance_purchase`.`created` AS `createdAt`,
	`finance_purchase`.`finance_purchase_status` AS `status`,
	'Expense' AS `transactionCategory`,
	'Purchases' AS `transactionClassification`,
	'finance_purchase' AS `transactionTable`,
	'' AS `referenceTable`
FROM
	(
		(
			(
				`finance_purchase`
				JOIN account ON(
					(
						account.account_id = finance_purchase.account_to_id
					)
				)
			)

		)
		JOIN `account_type` ON(
			(
				account_type.account_type_id = account.account_type_id
			)
		)
	)

UNION ALL



  SELECT
  	`finance_transfer`.`finance_transfer_id` AS `transactionId`,
  	`finance_transfered`.`finance_transfered_id` AS `referenceId`,
  	'' AS `payingFor`,
  	`finance_transfer`.`reference_number` AS `referenceCode`,
  	`finance_transfer`.`document_number` AS `transactionCode`,
  	'' AS `patient_id`,
    '' AS `recepientId`,
  	`account`.`parent_account` AS `accountParentId`,
  	`account_type`.`account_type_name` AS `accountsclassfication`,
  	`finance_transfer`.`account_from_id` AS `accountId`,
  	`account`.`account_name` AS `accountName`,
  	`finance_transfer`.`remarks` AS `transactionName`,
  	CONCAT(' Amount Transfered to ',(SELECT account_name FROM account WHERE account_id = finance_transfered.account_to_id )) AS `transactionDescription`,
  	0 AS `dr_amount`,
  	`finance_transfer`.`finance_transfer_amount` AS `cr_amount`,
  	`finance_transfered`.`transaction_date` AS `transactionDate`,
  	`finance_transfered`.`created` AS `createdAt`,
  	`finance_transfer`.`finance_transfer_status` AS `status`,
  	'Transfer' AS `transactionCategory`,
  	'Transfer' AS `transactionClassification`,
  	'finance_transfered' AS `transactionTable`,
  	'finance_transfer' AS `referenceTable`
  FROM
  	(
  		(
  			(
  				`finance_transfer`
  				JOIN `finance_transfered` ON(
  					(
  						finance_transfered.finance_transfer_id = finance_transfer.finance_transfer_id
  					)
  				)
  			)
  			JOIN account ON(
  				(
  					account.account_id = finance_transfer.account_from_id
  				)
  			)
  		)
  		JOIN `account_type` ON(
  			(
  				account_type.account_type_id = account.account_type_id
  			)
  		)
  	)


  UNION ALL
 

  SELECT
	`creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
	`creditor_payment`.`creditor_payment_id` AS `referenceId`,
	`creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
	`creditor_payment`.`reference_number` AS `referenceCode`,
	`creditor_payment`.`document_number` AS `transactionCode`,
	'' AS `patient_id`,
  	`creditor_payment`.`creditor_id` AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`creditor_payment`.`account_from_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	`creditor_payment_item`.`description` AS `transactionName`,
	CONCAT('Payment for invoice of ',' ',`creditor_invoice`.`invoice_number`,' Ref. ', `creditor_payment`.`reference_number`)  AS `transactionDescription`,
	0 AS `dr_amount`,
	`creditor_payment_item`.`amount_paid` AS `cr_amount`,
	`creditor_payment`.`transaction_date` AS `transactionDate`,
	`creditor_payment`.`created` AS `createdAt`,
	`creditor_payment_item`.`creditor_payment_item_status` AS `status`,
	'Expense Payment' AS `transactionCategory`,
	'Creditors Invoices Payments' AS `transactionClassification`,
	'creditor_payment' AS `transactionTable`,
	'creditor_payment_item' AS `referenceTable`
FROM
	(
		(
			(
				`creditor_payment_item`
				JOIN `creditor_payment` ON(
					(
						creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id
					)
				)
			)
			JOIN account ON(
				(
					account.account_id = creditor_payment.account_from_id
				)
			)
		)
		JOIN `account_type` ON(
			(
				account_type.account_type_id = account.account_type_id
			)
		)
		JOIN `creditor_invoice` ON(
			(
				creditor_invoice.creditor_invoice_id = creditor_payment_item.creditor_invoice_id
			)
		)
	)
	WHERE creditor_payment_item.invoice_type = 0

UNION ALL

 SELECT
`creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
`creditor_payment`.`creditor_payment_id` AS `referenceId`,
`creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
`creditor_payment`.`reference_number` AS `referenceCode`,
`creditor_payment`.`document_number` AS `transactionCode`,
'' AS `patient_id`,
`creditor_payment`.`creditor_id` AS `recepientId`,
`account`.`parent_account` AS `accountParentId`,
`account_type`.`account_type_name` AS `accountsclassfication`,
`creditor_payment`.`account_from_id` AS `accountId`,
`account`.`account_name` AS `accountName`,
`creditor_payment_item`.`description` AS `transactionName`,
CONCAT('Payment for invoice of ',' ',`orders`.`supplier_invoice_number`,' Ref. ', `creditor_payment`.`reference_number`)  AS `transactionDescription`,
0 AS `dr_amount`,
`creditor_payment_item`.`amount_paid` AS `cr_amount`,
`creditor_payment`.`transaction_date` AS `transactionDate`,
`creditor_payment`.`created` AS `createdAt`,
`creditor_payment_item`.`creditor_payment_item_status` AS `status`,
'Expense Payment' AS `transactionCategory`,
'Creditors Invoices Payments' AS `transactionClassification`,
'creditor_payment' AS `transactionTable`,
'creditor_payment_item' AS `referenceTable`
FROM
(
	(
		(
			`creditor_payment_item`
			JOIN `creditor_payment` ON(
				(
					creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id  AND creditor_payment.creditor_payment_status = 1
				)
			)
		)
		JOIN account ON(
			(
				account.account_id = creditor_payment.account_from_id
			)
		)
	)
	JOIN `account_type` ON(
		(
			account_type.account_type_id = account.account_type_id
		)
	)
	JOIN `orders` ON(
		(
			orders.order_id = creditor_payment_item.creditor_invoice_id
		)
	)
)
WHERE creditor_payment_item.invoice_type = 1

UNION ALL

SELECT
`creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
`creditor_payment`.`creditor_payment_id` AS `referenceId`,
`creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
`creditor_payment`.`reference_number` AS `referenceCode`,
`creditor_payment`.`document_number` AS `transactionCode`,
'' AS `patient_id`,
`creditor_payment`.`creditor_id` AS `recepientId`,
`account`.`parent_account` AS `accountParentId`,
`account_type`.`account_type_name` AS `accountsclassfication`,
`creditor_payment`.`account_from_id` AS `accountId`,
`account`.`account_name` AS `accountName`,
`creditor_payment_item`.`description` AS `transactionName`,
CONCAT('Payment for creditor invoice',' Ref. ', `creditor_payment`.`reference_number`)  AS `transactionDescription`,
0 AS `dr_amount`,
`creditor_payment_item`.`amount_paid` AS `cr_amount`,
`creditor_payment`.`transaction_date` AS `transactionDate`,
`creditor_payment`.`created` AS `createdAt`,
`creditor_payment_item`.`creditor_payment_item_status` AS `status`,
'Expense Payment' AS `transactionCategory`,
'Creditors Invoices Payments' AS `transactionClassification`,
'creditor_payment' AS `transactionTable`,
'creditor_payment_item' AS `referenceTable`
FROM
(
	(
		(
			`creditor_payment_item`
			JOIN `creditor_payment` ON(
				(
					creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id AND creditor_payment.creditor_payment_status = 1
				)
			)
		)
		JOIN account ON(
			(
				account.account_id = creditor_payment.account_from_id
			)
		)
	)
	JOIN `account_type` ON(
		(
			account_type.account_type_id = account.account_type_id
		)
	)
	JOIN `creditor` ON(
		(
			creditor.creditor_id = creditor_payment_item.creditor_id
		)
	)
)
WHERE creditor_payment_item.invoice_type = 2


UNION ALL

SELECT
`creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
`creditor_payment`.`creditor_payment_id` AS `referenceId`,
`creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
`creditor_payment`.`reference_number` AS `referenceCode`,
`creditor_payment`.`document_number` AS `transactionCode`,
'' AS `patient_id`,
`creditor_payment`.`creditor_id` AS `recepientId`,
`account`.`parent_account` AS `accountParentId`,
`account_type`.`account_type_name` AS `accountsclassfication`,
`creditor_payment`.`account_from_id` AS `accountId`,
`account`.`account_name` AS `accountName`,
`creditor_payment_item`.`description` AS `transactionName`,
CONCAT('Payment on account',' Ref. ', `creditor_payment`.`reference_number`)  AS `transactionDescription`,
0 AS `dr_amount`,
`creditor_payment_item`.`amount_paid` AS `cr_amount`,
`creditor_payment`.`transaction_date` AS `transactionDate`,
`creditor_payment`.`created` AS `createdAt`,
`creditor_payment_item`.`creditor_payment_item_status` AS `status`,
'Expense Payment' AS `transactionCategory`,
'Creditors Invoices Payments' AS `transactionClassification`,
'creditor_payment' AS `transactionTable`,
'creditor_payment_item' AS `referenceTable`
FROM
(
	(
		(
			`creditor_payment_item`
			JOIN `creditor_payment` ON(
				(
					creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id AND creditor_payment.creditor_payment_status = 1
				)
			)
		)
		JOIN account ON(
			(
				account.account_id = creditor_payment.account_from_id
			)
		)
	)
	JOIN `account_type` ON(
		(
			account_type.account_type_id = account.account_type_id
		)
	)
	JOIN `creditor` ON(
		(
			creditor.creditor_id = creditor_payment_item.creditor_id
		)
	)
)
WHERE creditor_payment_item.invoice_type = 3
;




CREATE OR REPLACE VIEW v_account_ledger_by_date AS select * from v_account_ledger ORDER BY createdAt;