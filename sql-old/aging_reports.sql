CREATE OR REPLACE VIEW v_aged_payables AS
-- Creditr Invoices
SELECT
	creditor.creditor_id AS recepientId,
	creditor.creditor_name as payables,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 ))
        - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.cr_amount, 0 ))
        END
  ) AS `coming_due`,
	(
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 ))
				  - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  BETWEEN 1 AND 30, v_general_ledger.cr_amount, 0 ))
			END
  ) AS `thirty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.cr_amount, 0 ))
			END
  ) AS `sixty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  BETWEEN 61 AND 90, v_general_ledger.cr_amount, 0 ))
		END
  ) AS `ninety_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 ))
				 - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  >90, v_general_ledger.cr_amount, 0 ))
			END
  ) AS `over_ninety_days`,

  (

    (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.dr_amount, 0 ))
					 - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  = 0, v_general_ledger.cr_amount, 0 ))
  		END
    )-- Getting the Value for 0 Days
    + (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 ))
 - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  BETWEEN 1 AND 30, v_general_ledger.cr_amount, 0 ))
  		END
    ) --  AS `1-30 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 ))

				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  BETWEEN 31 AND 60, v_general_ledger.cr_amount, 0 ))
				END

    ) -- AS `31-60 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 ))
				   - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) BETWEEN 61 AND 90, v_general_ledger.cr_amount, 0 ))
  		END
    ) -- AS `61-90 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) ) >90, v_general_ledger.dr_amount, 0 ))
				  - Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.referenceDate ) )  >90, v_general_ledger.cr_amount, 0 ))
  		END
    ) -- AS `>90 Days`
  ) AS `Total`

	FROM
		creditor
	LEFT JOIN v_general_ledger ON v_general_ledger.recepientId = creditor.creditor_id AND v_general_ledger.recepientId > 0 AND v_general_ledger.referenceDate >= creditor.start_date

	 GROUP BY creditor.creditor_id;


		CREATE OR REPLACE VIEW v_aged_receivables AS
		SELECT
			visit.visit_type AS receivables_id,
			'Organizational' AS receivables_type,
			visit_type.visit_type_name AS receivable_Name,-- DATEDIFF( CURDATE( ), visit_charge.date ) AS dueDate,
			(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) )  = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) )  = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
		-- Sum(IF( DATEDIFF( CURDATE( ), date( orders.supplier_invoice_date ) )  = 0, order_supplier.total_amount, 0 ))
							Sum( IF ( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
							- ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) )
					END
			)AS `current`,
			(
					CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
					Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
					+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
					- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
					+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
				END
			)AS `thirty_days`,
			(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
						Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
						+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
						- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
						+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
			) `sixty_days`,
			(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
							Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
							- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
					)AS `ninetydays`,
					(
							CASE
								WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) > 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
								 THEN 0 -- Output
								WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) > 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
								 THEN
										Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) )
										+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
										- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
										+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
					)AS `over_ninetydays`,
			(
				(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) )  = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) )  = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
		-- Sum(IF( DATEDIFF( CURDATE( ), date( orders.supplier_invoice_date ) )  = 0, order_supplier.total_amount, 0 ))
							Sum( IF ( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) = 0, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
							- ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) )
					END
				)

				+
				(
					CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
					Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 1 AND 30, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
					+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
					- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
					+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
				END
				)
				+
				(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
						Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 31 AND 60, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
						+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
						- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
						+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
				)
				+
				(
				CASE
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
		       THEN 0 -- Output
		      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
		       THEN
							Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) BETWEEN 61 AND 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
							- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
							+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
					)
			+
					(
							CASE
								WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) > 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) = 0 -- condtion to see if the date has no bills
								 THEN 0 -- Output
								WHEN Sum(IF( DATEDIFF( CURDATE( ), date( visit_charge.date ) ) > 90, (visit_charge.visit_charge_amount*visit_charge.visit_charge_units), 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
								 THEN
										Sum( IF ( DATEDIFF( CURDATE( ), visit_charge.date ) > 90, visit_charge.visit_charge_amount, 0 ) )
										+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 3) )
										- (( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 1 ) )
										+ ( SELECT COALESCE ( sum( payments.amount_paid ), 0 ) FROM payments WHERE ( payments.visit_id = visit.visit_id AND payments.cancel = 0 AND payments.payment_type = 2) ) )
					END
					)

				) AS `total_owed`

		FROM
			visit_charge
			JOIN visit ON visit.visit_id = visit_charge.visit_id
			LEFT JOIN visit_type ON visit_type.visit_type_id = visit.visit_type
			WHERE visit.visit_delete = 0 AND visit_charge.charged = 1 AND visit_charge.visit_charge_delete = 0
			GROUP BY visit_type.visit_type_id;
