
CREATE OR REPLACE VIEW v_creditor_ledger_aging AS
-- Creditr Invoices

SELECT
	`creditor`.`creditor_id` AS `transactionId`,
	'' AS `referenceId`,
	'' AS `payingFor`,
	'' AS `referenceCode`,
	'' AS `transactionCode`,
	'' AS `patient_id`,
    `creditor`.`creditor_id` AS `recepientId`,
	'' AS `accountParentId`,
	'' AS `accountsclassfication`,
	'' AS `accountId`,
	'' AS `accountName`,
	'' AS `transactionName`,
	CONCAT('Opening Balance from',' ',`creditor`.`start_date`) AS `transactionDescription`,
  	0 AS `department_id`,
	`creditor`.`opening_balance` AS `dr_amount`,
	'0' AS `cr_amount`,
	`creditor`.`start_date` AS `transactionDate`,
	`creditor`.`start_date` AS `createdAt`,
	`creditor`.`start_date` AS `referenceDate`,
	`creditor`.`creditor_status` AS `status`,
	'Expense' AS `transactionCategory`,
	'Creditor Opening Balance' AS `transactionClassification`,
	'' AS `transactionTable`,
	'creditor' AS `referenceTable`
FROM
creditor
WHERE debit_id = 2

UNION ALL

SELECT
	`creditor`.`creditor_id` AS `transactionId`,
	'' AS `referenceId`,
	'' AS `payingFor`,
	'' AS `referenceCode`,
	'' AS `transactionCode`,
	'' AS `patient_id`,
  `creditor`.`creditor_id` AS `recepientId`,
	'' AS `accountParentId`,
	'' AS `accountsclassfication`,
	'' AS `accountId`,
	'' AS `accountName`,
	'' AS `transactionName`,
	CONCAT('Opening Balance from',' ',`creditor`.`start_date`) AS `transactionDescription`,
  0 AS `department_id`,
	'0' AS `dr_amount`,
	`creditor`.`opening_balance` AS `cr_amount`,
	`creditor`.`start_date` AS `transactionDate`,
	`creditor`.`start_date` AS `createdAt`,
	`creditor`.`start_date` AS `referenceDate`,
	`creditor`.`creditor_status` AS `status`,
	'Expense' AS `transactionCategory`,
	'Creditor Opening Balance' AS `transactionClassification`,
	'' AS `transactionTable`,
	'creditor' AS `referenceTable`
FROM
creditor
WHERE debit_id = 1

UNION ALL
SELECT
	`creditor_invoice_item`.`creditor_invoice_item_id` AS `transactionId`,
	`creditor_invoice`.`creditor_invoice_id` AS `referenceId`,
	'' AS `payingFor`,
	`creditor_invoice`.`invoice_number` AS `referenceCode`,
	`creditor_invoice`.`document_number` AS `transactionCode`,
	'' AS `patient_id`,
  `creditor_invoice`.`creditor_id` AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`creditor_invoice_item`.`account_to_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	`creditor_invoice_item`.`item_description` AS `transactionName`,
	`creditor_invoice_item`.`item_description` AS `transactionDescription`,
	
  	'0' AS `department_id`,
	`creditor_invoice_item`.`total_amount` AS `dr_amount`,
	'0' AS `cr_amount`,
	`creditor_invoice`.`transaction_date` AS `transactionDate`,
	`creditor_invoice`.`created` AS `createdAt`,
	`creditor_invoice`.`transaction_date` AS `referenceDate`,
	`creditor_invoice_item`.`creditor_invoice_item_status` AS `status`,
	'Expense' AS `transactionCategory`,
	'Creditors Invoices' AS `transactionClassification`,
	'creditor_invoice_item' AS `transactionTable`,
	'creditor_invoice' AS `referenceTable`
FROM
	(
		(
			(
				`creditor_invoice_item`
				JOIN `creditor_invoice` ON(
					(
						creditor_invoice.creditor_invoice_id = creditor_invoice_item.creditor_invoice_id AND creditor_invoice.creditor_invoice_status = 1
					)
				)
			)
			JOIN account ON(
				(
					account.account_id = creditor_invoice_item.account_to_id
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
	`creditor_credit_note_item`.`creditor_credit_note_item_id` AS `transactionId`,
	`creditor_credit_note`.`creditor_credit_note_id` AS `referenceId`,
	`creditor_credit_note_item`.`creditor_invoice_id` AS `payingFor`,
	`creditor_credit_note`.`invoice_number` AS `referenceCode`,
	`creditor_credit_note`.`document_number` AS `transactionCode`,
	'' AS `patient_id`,
  `creditor_credit_note`.`creditor_id` AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`creditor_credit_note`.`account_from_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	`creditor_credit_note_item`.`description` AS `transactionName`,
	`creditor_credit_note_item`.`description` AS `transactionDescription`,
  0 AS `department_id`,
	0 AS `dr_amount`,
	`creditor_credit_note_item`.`credit_note_amount` AS `cr_amount`,
	`creditor_credit_note`.`transaction_date` AS `transactionDate`,
	`creditor_credit_note`.`created` AS `createdAt`,
	`creditor_invoice`.`transaction_date` AS `referenceDate`,
	`creditor_credit_note_item`.`creditor_credit_note_item_status` AS `status`,
	'Expense Payment' AS `transactionCategory`,
	'Creditors Credit Notes' AS `transactionClassification`,
	'creditor_credit_note' AS `transactionTable`,
	'creditor_credit_note_item' AS `referenceTable`
FROM
	(
		(
			(
				`creditor_credit_note_item`
				JOIN `creditor_credit_note` ON(
					(
						creditor_credit_note.creditor_credit_note_id = creditor_credit_note_item.creditor_credit_note_id AND creditor_credit_note.creditor_credit_note_status = 1
					)
				)
			)
			JOIN account ON(
				(
					account.account_id = creditor_credit_note.account_from_id
				)
			)
			JOIN creditor_invoice ON(
				(
					creditor_invoice.creditor_invoice_id = creditor_credit_note_item.creditor_invoice_id
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
	`order_supplier`.`order_supplier_id` AS `transactionId`,
	`orders`.`order_id` AS `referenceId`,
	'' AS `payingFor`,
	`orders`.`supplier_invoice_number` AS `referenceCode`,
	'' AS `transactionCode`,
	'' AS `patient_id`,
  `orders`.`supplier_id` AS `recepientId`,
	`account`.`parent_account` AS `accountParentId`,
	`account_type`.`account_type_name` AS `accountsclassfication`,
	`orders`.`account_id` AS `accountId`,
	`account`.`account_name` AS `accountName`,
	'Drug Purchase' AS `transactionName`,
	CONCAT('Purchase of supplies') AS `transactionDescription`,
  0 AS `department_id`,
	SUM(`order_supplier`.`less_vat`) AS `dr_amount`,
	'0' AS `cr_amount`,
	`orders`.`supplier_invoice_date` AS `transactionDate`,
	`orders`.`created` AS `createdAt`,
	`orders`.`supplier_invoice_date` AS `referenceDate`,
	`orders`.`order_approval_status` AS `status`,
	'Purchases' AS `transactionCategory`,
	'Supplies Invoices' AS `transactionClassification`,
	'order_supplier' AS `transactionTable`,
	'orders' AS `referenceTable`
FROM
	(
		(
			(
				`order_supplier`
				JOIN `orders` ON(
					(
						orders.order_id = order_supplier.order_id
					)
				)
			)
			JOIN account ON(
				(
					account.account_id = orders.account_id
				)
			)


			JOIN order_item ON(
				(
					order_item.order_item_id = order_supplier.order_item_id
				)
			)
		JOIN product ON(
				(
					product.product_id = order_item.product_id
				)
			)
		)
		JOIN `account_type` ON(
			(
				account_type.account_type_id = account.account_type_id
			)
		)
	)

	WHERE orders.is_store = 0 AND orders.supplier_id > 0 and orders.order_approval_status = 7 AND order_supplier.order_item_id IN (select order_item.order_item_id FROM order_item)
	GROUP BY order_supplier.order_id

	UNION ALL

		SELECT
		`order_supplier`.`order_supplier_id` AS `transactionId`,
		`orders`.`order_id` AS `referenceId`,
		'' AS `payingFor`,
		`orders`.`supplier_invoice_number` AS `referenceCode`,
		`orders`.`reference_number`  AS `transactionCode`,
		'' AS `patient_id`,
	  `orders`.`supplier_id` AS `recepientId`,
		`account`.`parent_account` AS `accountParentId`,
		`account_type`.`account_type_name` AS `accountsclassfication`,
		`orders`.`account_id` AS `accountId`,
		`account`.`account_name` AS `accountName`,
		'Credit' AS `transactionName`,
		CONCAT('Credit note of ',' ',`orders`.`reference_number`) AS `transactionDescription`,
   		 0 AS `department_id`,
		'0' AS `dr_amount`,
		SUM(`order_supplier`.`less_vat`) AS `cr_amount`,
		`orders`.`supplier_invoice_date` AS `transactionDate`,
		`orders`.`created` AS `createdAt`,
		`orders`.`supplier_invoice_date` AS `referenceDate`,
		`orders`.`order_approval_status` AS `status`,
		'Income' AS `transactionCategory`,
		'Supplies Credit Note' AS `transactionClassification`,
		'order_supplier' AS `transactionTable`,
		'orders' AS `referenceTable`

	FROM
		(
			(
				(
					`order_supplier`
					JOIN `orders` ON(
						(
							orders.order_id = order_supplier.order_id
						)
					)
				)
				JOIN account ON(
					(
						account.account_id = orders.account_id
					)
				)


				JOIN order_item ON(
					(
						order_item.order_item_id = order_supplier.order_item_id
					)
				)
			JOIN product ON(
					(
						product.product_id = order_item.product_id
					)
				)
			)
			JOIN `account_type` ON(
				(
					account_type.account_type_id = account.account_type_id
				)
			)
		)

		WHERE orders.is_store = 3 AND orders.supplier_id > 0 and orders.order_approval_status = 7 AND order_supplier.order_item_id IN (select order_item.order_item_id FROM order_item)
		GROUP BY order_supplier.order_id

		UNION ALL
		-- credit invoice payments

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
			CONCAT('Payment for invoice of ',' ',`creditor_invoice`.`invoice_number`)  AS `transactionDescription`,
      		0 AS `department_id`,
			0 AS `dr_amount`,
			`creditor_payment_item`.`amount_paid` AS `cr_amount`,
			`creditor_payment`.`transaction_date` AS `transactionDate`,
			`creditor_payment`.`created` AS `createdAt`,
			`creditor_invoice`.`transaction_date` AS `referenceDate`,
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
			CONCAT('Payment for invoice of ',' ',`orders`.`supplier_invoice_number`)  AS `transactionDescription`,
      0 AS `department_id`,
			0 AS `dr_amount`,
			`creditor_payment_item`.`amount_paid` AS `cr_amount`,
			`creditor_payment`.`transaction_date` AS `transactionDate`,
			`creditor_payment`.`created` AS `createdAt`,
			`orders`.`supplier_invoice_date` AS `referenceDate`,
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
			CONCAT('Payment of opening balance')  AS `transactionDescription`,
      		0 AS `department_id`,
			0 AS `dr_amount`,
			`creditor_payment_item`.`amount_paid` AS `cr_amount`,
			`creditor_payment`.`transaction_date` AS `transactionDate`,
			`creditor_payment`.`created` AS `createdAt`,
			`creditor`.`start_date` AS `referenceDate`,
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
			CONCAT('Payment on account')  AS `transactionDescription`,
      		0 AS `department_id`,
			0 AS `dr_amount`,
			`creditor_payment_item`.`amount_paid` AS `cr_amount`,
			`creditor_payment`.`transaction_date` AS `transactionDate`,
			`creditor_payment`.`created` AS `createdAt`,
			`creditor_payment`.`created` AS `referenceDate`,
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
			WHERE creditor_payment_item.invoice_type = 3;
CREATE OR REPLACE VIEW v_aged_payables AS
-- Creditr Invoices
SELECT
	creditor.creditor_id AS recepientId,
	creditor.creditor_name as payables,
	creditor.branch_id as branch_id,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 ))
        - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.cr_amount, 0 ))
        END
  ) AS `coming_due`,
	(
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 ))
				  - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  BETWEEN 1 AND 30, v_creditor_ledger_aging.cr_amount, 0 ))
			END
  ) AS `thirty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.cr_amount, 0 ))
			END
  ) AS `sixty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  BETWEEN 61 AND 90, v_creditor_ledger_aging.cr_amount, 0 ))
		END
  ) AS `ninety_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 ))
				 - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  >90, v_creditor_ledger_aging.cr_amount, 0 ))
			END
  ) AS `over_ninety_days`,

  (

    (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.dr_amount, 0 ))
					 - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  = 0, v_creditor_ledger_aging.cr_amount, 0 ))
  		END
    )-- Getting the Value for 0 Days
    + (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 1 AND 30, v_creditor_ledger_aging.dr_amount, 0 ))
 - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  BETWEEN 1 AND 30, v_creditor_ledger_aging.cr_amount, 0 ))
  		END
    ) --  AS `1-30 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 31 AND 60, v_creditor_ledger_aging.dr_amount, 0 ))

				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  BETWEEN 31 AND 60, v_creditor_ledger_aging.cr_amount, 0 ))
				END

    ) -- AS `31-60 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) BETWEEN 61 AND 90, v_creditor_ledger_aging.cr_amount, 0 ))
  		END
    ) -- AS `61-90 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) ) >90, v_creditor_ledger_aging.dr_amount, 0 ))
				  - Sum(IF( DATEDIFF( CURDATE( ), date( v_creditor_ledger_aging.referenceDate ) )  >90, v_creditor_ledger_aging.cr_amount, 0 ))
  		END
    ) -- AS `>90 Days`
  ) AS `Total`,
SUM(v_creditor_ledger_aging.dr_amount) AS total_dr,
SUM(v_creditor_ledger_aging.cr_amount) AS total_cr

	FROM
		creditor
	LEFT JOIN v_creditor_ledger_aging ON v_creditor_ledger_aging.recepientId = creditor.creditor_id AND v_creditor_ledger_aging.recepientId > 0 AND v_creditor_ledger_aging.referenceDate >= creditor.start_date

	 GROUP BY creditor.creditor_id;