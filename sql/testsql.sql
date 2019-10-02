CREATE OR REPLACE VIEW v_aged_payables AS
-- Creditr Invoices
SELECT
	creditor.creditor_id AS recepientId,
	creditor.creditor_name as payables,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 ))
				 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
        END
  ) AS `coming_due`,
	(
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
			END
  ) AS `thirty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 ))
				 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
			END
  ) AS `sixty_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 ))
				 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
		END
  ) AS `ninety_days`,
  (
    CASE
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
       THEN 0 -- Output
      WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
       THEN
       -- Add the paymensts made to the specifc invoice
        Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 ))
				 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
			END
  ) AS `over_ninety_days`,

  (

    (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) )  = 0, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
  		END
    )-- Getting the Value for 0 Days
    + (
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 1 AND 30, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
  		END
    ) --  AS `1-30 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 31 AND 60, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
				END
    ) -- AS `31-60 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) BETWEEN 61 AND 90, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
  		END
    ) -- AS `61-90 Days`
    +(
      CASE
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 )) = 0 -- condtion to see if the date has no bills
         THEN 0 -- Output
        WHEN Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 )) > 0 -- Checking if the value of bills in the date range is greater than Zero
         THEN
         -- Add the paymensts made to the specifc invoice
          Sum(IF( DATEDIFF( CURDATE( ), date( v_general_ledger.transactionDate ) ) >90, v_general_ledger.dr_amount, 0 ))
					 - (SELECT COALESCE ( sum( v_general_ledger.cr_amount ), 0 ) FROM v_general_ledger WHERE ( v_general_ledger.recepientId = creditor.creditor_id ))
  		END
    ) -- AS `>90 Days`
  ) AS `Total`

	FROM
		creditor
	LEFT JOIN v_general_ledger ON v_general_ledger.recepientId = creditor.creditor_id
	where v_general_ledger.recepientId > 0 AND v_general_ledger.transactionDate >= creditor.start_date
	 GROUP BY creditor.creditor_id;
