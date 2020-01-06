
create or replace view v_general_ledger as 
select
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
   concat('Opening Balance from', ' ', `creditor`.`start_date`) AS `transactionDescription`,
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
from
   `creditor` 
where
   (
      `creditor`.`debit_id` = 2
   )
union all
select
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
   concat('Opening Balance from', ' ', `creditor`.`start_date`) AS `transactionDescription`,
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
from
   `creditor` 
where
   (
      `creditor`.`debit_id` = 1
   )
union all
select
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
from
   (
((`creditor_invoice_item` 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_invoice_item`.`creditor_invoice_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_invoice_item`.`account_to_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
from
   (
(((`creditor_credit_note_item` 
      join
         `creditor_credit_note` 
         on((`creditor_credit_note`.`creditor_credit_note_id` = `creditor_credit_note_item`.`creditor_credit_note_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_credit_note`.`account_from_id`))) 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_credit_note_item`.`creditor_invoice_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   `finance_purchase`.`finance_purchase_description` AS `transactionDescription`,
   `finance_purchase`.`department_id` AS `department_id`,
   `finance_purchase`.`finance_purchase_amount` AS `dr_amount`,
   0 AS `cr_amount`,
   `finance_purchase`.`transaction_date` AS `transactionDate`,
   `finance_purchase`.`created` AS `createdAt`,
   `finance_purchase`.`transaction_date` AS `referenceDate`,
   `finance_purchase`.`finance_purchase_status` AS `status`,
   'Expense' AS `transactionCategory`,
   'Purchases' AS `transactionClassification`,
   'finance_purchase' AS `transactionTable`,
   '' AS `referenceTable` 
from
   (
(`finance_purchase` 
      join
         `account` 
         on((`account`.`account_id` = `finance_purchase`.`account_to_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
   `finance_purchase_payment`.`finance_purchase_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   `finance_purchase`.`finance_purchase_id` AS `payingFor`,
   `finance_purchase`.`transaction_number` AS `referenceCode`,
   `finance_purchase`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `finance_purchase`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `finance_purchase_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `finance_purchase`.`finance_purchase_description` AS `transactionName`,
   concat(`account`.`account_name`, ' paying for invoice ', `finance_purchase`.`transaction_number`) AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `finance_purchase_payment`.`amount_paid` AS `cr_amount`,
   `finance_purchase`.`transaction_date` AS `transactionDate`,
   `finance_purchase`.`created` AS `createdAt`,
   `finance_purchase`.`transaction_date` AS `referenceDate`,
   `finance_purchase_payment`.`finance_purchase_payment_status` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'finance_purchase' AS `transactionTable`,
   'finance_purchase_payment' AS `referenceTable` 
from
   (
((`finance_purchase_payment` 
      join
         `finance_purchase` 
         on((`finance_purchase`.`finance_purchase_id` = `finance_purchase_payment`.`finance_purchase_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_purchase_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat(' Amount Transfered to ', 
   (
      select
         `account`.`account_name` 
      from
         `account` 
      where
         (
            `account`.`account_id` = `finance_transfered`.`account_to_id`
         )
   )
) AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `finance_transfer`.`finance_transfer_amount` AS `cr_amount`,
   `finance_transfered`.`transaction_date` AS `transactionDate`,
   `finance_transfered`.`created` AS `createdAt`,
   '' AS `referenceDate`,
   `finance_transfer`.`finance_transfer_status` AS `status`,
   'Transfer' AS `transactionCategory`,
   'Transfer' AS `transactionClassification`,
   'finance_transfered' AS `transactionTable`,
   'finance_transfer' AS `referenceTable` 
from
   (
((`finance_transfer` 
      join
         `finance_transfered` 
         on((`finance_transfered`.`finance_transfer_id` = `finance_transfer`.`finance_transfer_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_transfer`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat('Amount Received from ', 
   (
      select
         `account`.`account_name` 
      from
         `account` 
      where
         (
            `account`.`account_id` = `finance_transfer`.`account_from_id`
         )
   )
) AS `transactionDescription`,
   0 AS `department_id`,
   `finance_transfered`.`finance_transfered_amount` AS `dr_amount`,
   0 AS `cr_amount`,
   `finance_transfer`.`transaction_date` AS `transactionDate`,
   `finance_transfer`.`created` AS `createdAt`,
   '' AS `referenceDate`,
   `finance_transfer`.`finance_transfer_status` AS `status`,
   'Transfer' AS `transactionCategory`,
   'Transfer' AS `transactionClassification`,
   'finance_transfer' AS `transactionTable`,
   'finance_transfered' AS `referenceTable` 
from
   (
((`finance_transfered` 
      join
         `finance_transfer` 
         on((`finance_transfer`.`finance_transfer_id` = `finance_transfered`.`finance_transfer_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_transfered`.`account_to_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat('Purchase of supplies') AS `transactionDescription`,
   0 AS `department_id`,
   sum(`order_supplier`.`less_vat`) AS `dr_amount`,
   '0' AS `cr_amount`,
   `orders`.`supplier_invoice_date` AS `transactionDate`,
   `orders`.`created` AS `createdAt`,
   `orders`.`supplier_invoice_date` AS `referenceDate`,
   `orders`.`order_approval_status` AS `status`,
   'Purchases' AS `transactionCategory`,
   'Supplies Invoices' AS `transactionClassification`,
   'order_supplier' AS `transactionTable`,
   'orders' AS `referenceTable` 
from
   (
((((`order_supplier` 
      join
         `orders` 
         on((`orders`.`order_id` = `order_supplier`.`order_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `orders`.`account_id`))) 
      join
         `order_item` 
         on((`order_item`.`order_item_id` = `order_supplier`.`order_item_id`))) 
      join
         `product` 
         on((`product`.`product_id` = `order_item`.`product_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`orders`.`is_store` = 0) 
      and 
      (
         `orders`.`supplier_id` > 0
      )
      and 
      (
         `orders`.`order_approval_status` = 7
      )
      and `order_supplier`.`order_item_id` in 
      (
         select
            `order_item`.`order_item_id` 
         from
            `order_item`
      )
   )
group by
   `order_supplier`.`order_id` 
union all
select
   `order_supplier`.`order_supplier_id` AS `transactionId`,
   `orders`.`order_id` AS `referenceId`,
   '' AS `payingFor`,
   `orders`.`supplier_invoice_number` AS `referenceCode`,
   `orders`.`reference_number` AS `transactionCode`,
   '' AS `patient_id`,
   `orders`.`supplier_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `orders`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   'Credit' AS `transactionName`,
   concat('Credit note of ', ' ', `orders`.`reference_number`) AS `transactionDescription`,
   0 AS `department_id`,
   '0' AS `dr_amount`,
   sum(`order_supplier`.`less_vat`) AS `cr_amount`,
   `orders`.`supplier_invoice_date` AS `transactionDate`,
   `orders`.`created` AS `createdAt`,
   `orders`.`supplier_invoice_date` AS `referenceDate`,
   `orders`.`order_approval_status` AS `status`,
   'Income' AS `transactionCategory`,
   'Supplies Credit Note' AS `transactionClassification`,
   'order_supplier' AS `transactionTable`,
   'orders' AS `referenceTable` 
from
   (
((((`order_supplier` 
      join
         `orders` 
         on((`orders`.`order_id` = `order_supplier`.`order_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `orders`.`account_id`))) 
      join
         `order_item` 
         on((`order_item`.`order_item_id` = `order_supplier`.`order_item_id`))) 
      join
         `product` 
         on((`product`.`product_id` = `order_item`.`product_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`orders`.`is_store` = 3) 
      and 
      (
         `orders`.`supplier_id` > 0
      )
      and 
      (
         `orders`.`order_approval_status` = 7
      )
      and `order_supplier`.`order_item_id` in 
      (
         select
            `order_item`.`order_item_id` 
         from
            `order_item`
      )
   )
group by
   `order_supplier`.`order_id` 
union all
select
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
   concat('Payment for invoice of ', ' ', `creditor_invoice`.`invoice_number`) AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_payment_item`.`creditor_invoice_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 0
   )
union all
select
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
   concat('Payment for invoice of ', ' ', `orders`.`supplier_invoice_number`) AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `orders` 
         on((`orders`.`order_id` = `creditor_payment_item`.`creditor_invoice_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 1
   )
union all
select
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
   concat('Payment of opening balance') AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor` 
         on((`creditor`.`creditor_id` = `creditor_payment_item`.`creditor_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 2
   )
union all
select
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
   concat('Payment on account') AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor` 
         on((`creditor`.`creditor_id` = `creditor_payment_item`.`creditor_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 3
   )
union all
select
   `account_payments`.`account_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   '' AS `referenceCode`,
   '' AS `transactionCode`,
   '' AS `patient_id`,
   '' AS `recepientId`,
   `account_type`.`account_type_id` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `account`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   '' AS `transactionName`,
   `account_payments`.`account_payment_description` AS `transactionDescription`,
   0 AS `department_id`,
   `account_payments`.`amount_paid` AS `dr_amount`,
   '0' AS `cr_amount`,
   `account_payments`.`payment_date` AS `transactionDate`,
   `account_payments`.`payment_date` AS `createdAt`,
   `account_payments`.`payment_date` AS `referenceDate`,
   `account_payments`.`account_payment_deleted` AS `status`,
   'Expense' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'account_payments' AS `transactionTable`,
   '' AS `referenceTable` 
from
   (
(`account_payments` 
      left join
         `account` 
         on((`account_payments`.`account_to_id` = `account`.`account_id`))) 
      left join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`account_payments`.`account_to_type` = 4) 
      and 
      (
         `account_payments`.`account_payment_deleted` = 0
      )
   )
union all
select
   `account_payments`.`account_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   '' AS `referenceCode`,
   '' AS `transactionCode`,
   '' AS `patient_id`,
   '' AS `recepientId`,
   `account_type`.`account_type_id` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `account`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   '' AS `transactionName`,
   `account_payments`.`account_payment_description` AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `account_payments`.`amount_paid` AS `cr_amount`,
   `account_payments`.`payment_date` AS `transactionDate`,
   `account_payments`.`payment_date` AS `createdAt`,
   `account_payments`.`payment_date` AS `referenceDate`,
   `account_payments`.`account_payment_deleted` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'account_payments' AS `transactionTable`,
   '' AS `referenceTable` 
from
   (
(`account_payments` 
      left join
         `account` 
         on((`account_payments`.`account_from_id` = `account`.`account_id`))) 
      left join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`account_payments`.`account_to_type` = 4) 
      and 
      (
         `account_payments`.`account_payment_deleted` = 0
      )
   )
;

CREATE OR REPLACE VIEW v_general_ledger_by_date AS 
select
   * 
from
   v_general_ledger 
ORDER BY
   createdAt;


create or replace view v_general_ledger_aging as 
select
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
   concat('Opening Balance from', ' ', `creditor`.`start_date`) AS `transactionDescription`,
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
from
   `creditor` 
where
   (
      `creditor`.`debit_id` = 2
   )
union all
select
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
   concat('Opening Balance from', ' ', `creditor`.`start_date`) AS `transactionDescription`,
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
from
   `creditor` 
where
   (
      `creditor`.`debit_id` = 1
   )
union all
select
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
from
   (
((`creditor_invoice_item` 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_invoice_item`.`creditor_invoice_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_invoice_item`.`account_to_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
   `creditor_credit_note_item`.`creditor_credit_note_item_id` AS `transactionId`,
   `creditor_credit_note`.`creditor_credit_note_id` AS `referenceId`,
   `creditor_credit_note`.`invoice_number` AS `referenceCode`,
   `creditor_credit_note_item`.`creditor_invoice_id` AS `payingFor`,
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
from
   (
(((`creditor_credit_note_item` 
      join
         `creditor_credit_note` 
         on((`creditor_credit_note`.`creditor_credit_note_id` = `creditor_credit_note_item`.`creditor_credit_note_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_credit_note`.`account_from_id`))) 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_credit_note_item`.`creditor_invoice_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
   `finance_purchase_payment`.`finance_purchase_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   `finance_purchase`.`finance_purchase_id` AS `payingFor`,
   `finance_purchase`.`transaction_number` AS `referenceCode`,
   `finance_purchase`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `finance_purchase`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `finance_purchase_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `finance_purchase`.`finance_purchase_description` AS `transactionName`,
   concat(`account`.`account_name`, ' paying for invoice ', `finance_purchase`.`transaction_number`) AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `finance_purchase_payment`.`amount_paid` AS `cr_amount`,
   `finance_purchase`.`transaction_date` AS `transactionDate`,
   `finance_purchase`.`created` AS `createdAt`,
   `finance_purchase`.`transaction_date` AS `referenceDate`,
   `finance_purchase_payment`.`finance_purchase_payment_status` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'finance_purchase' AS `transactionTable`,
   'finance_purchase_payment' AS `referenceTable` 
from
   (
((`finance_purchase_payment` 
      join
         `finance_purchase` 
         on((`finance_purchase`.`finance_purchase_id` = `finance_purchase_payment`.`finance_purchase_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_purchase_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat(' Amount Transfered to ', 
   (
      select
         `account`.`account_name` 
      from
         `account` 
      where
         (
            `account`.`account_id` = `finance_transfered`.`account_to_id`
         )
   )
) AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `finance_transfer`.`finance_transfer_amount` AS `cr_amount`,
   `finance_transfered`.`transaction_date` AS `transactionDate`,
   `finance_transfered`.`created` AS `createdAt`,
   '' AS `referenceDate`,
   `finance_transfer`.`finance_transfer_status` AS `status`,
   'Transfer' AS `transactionCategory`,
   'Transfer' AS `transactionClassification`,
   'finance_transfered' AS `transactionTable`,
   'finance_transfer' AS `referenceTable` 
from
   (
((`finance_transfer` 
      join
         `finance_transfered` 
         on((`finance_transfered`.`finance_transfer_id` = `finance_transfer`.`finance_transfer_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_transfer`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat('Amount Received from ', 
   (
      select
         `account`.`account_name` 
      from
         `account` 
      where
         (
            `account`.`account_id` = `finance_transfer`.`account_from_id`
         )
   )
) AS `transactionDescription`,
   0 AS `department_id`,
   `finance_transfered`.`finance_transfered_amount` AS `dr_amount`,
   0 AS `cr_amount`,
   `finance_transfer`.`transaction_date` AS `transactionDate`,
   `finance_transfer`.`created` AS `createdAt`,
   '' AS `referenceDate`,
   `finance_transfer`.`finance_transfer_status` AS `status`,
   'Transfer' AS `transactionCategory`,
   'Transfer' AS `transactionClassification`,
   'finance_transfer' AS `transactionTable`,
   'finance_transfered' AS `referenceTable` 
from
   (
((`finance_transfered` 
      join
         `finance_transfer` 
         on((`finance_transfer`.`finance_transfer_id` = `finance_transfered`.`finance_transfer_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `finance_transfered`.`account_to_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
union all
select
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
   concat('Purchase of supplies') AS `transactionDescription`,
   0 AS `department_id`,
   sum(`order_supplier`.`less_vat`) AS `dr_amount`,
   '0' AS `cr_amount`,
   `orders`.`supplier_invoice_date` AS `transactionDate`,
   `orders`.`created` AS `createdAt`,
   `orders`.`supplier_invoice_date` AS `referenceDate`,
   `orders`.`order_approval_status` AS `status`,
   'Purchases' AS `transactionCategory`,
   'Supplies Invoices' AS `transactionClassification`,
   'order_supplier' AS `transactionTable`,
   'orders' AS `referenceTable` 
from
   (
((((`order_supplier` 
      join
         `orders` 
         on((`orders`.`order_id` = `order_supplier`.`order_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `orders`.`account_id`))) 
      join
         `order_item` 
         on((`order_item`.`order_item_id` = `order_supplier`.`order_item_id`))) 
      join
         `product` 
         on((`product`.`product_id` = `order_item`.`product_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`orders`.`is_store` = 0) 
      and 
      (
         `orders`.`supplier_id` > 0
      )
      and 
      (
         `orders`.`order_approval_status` = 7
      )
      and `order_supplier`.`order_item_id` in 
      (
         select
            `order_item`.`order_item_id` 
         from
            `order_item`
      )
   )
group by
   `order_supplier`.`order_id` 
union all
select
   `order_supplier`.`order_supplier_id` AS `transactionId`,
   `orders`.`order_id` AS `referenceId`,
   '' AS `payingFor`,
   `orders`.`supplier_invoice_number` AS `referenceCode`,
   `orders`.`reference_number` AS `transactionCode`,
   '' AS `patient_id`,
   `orders`.`supplier_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `orders`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   'Credit' AS `transactionName`,
   concat('Credit note of ', ' ', `orders`.`reference_number`) AS `transactionDescription`,
   0 AS `department_id`,
   '0' AS `dr_amount`,
   sum(`order_supplier`.`less_vat`) AS `cr_amount`,
   `orders`.`supplier_invoice_date` AS `transactionDate`,
   `orders`.`created` AS `createdAt`,
   `orders`.`supplier_invoice_date` AS `referenceDate`,
   `orders`.`order_approval_status` AS `status`,
   'Income' AS `transactionCategory`,
   'Supplies Credit Note' AS `transactionClassification`,
   'order_supplier' AS `transactionTable`,
   'orders' AS `referenceTable` 
from
   (
((((`order_supplier` 
      join
         `orders` 
         on((`orders`.`order_id` = `order_supplier`.`order_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `orders`.`account_id`))) 
      join
         `order_item` 
         on((`order_item`.`order_item_id` = `order_supplier`.`order_item_id`))) 
      join
         `product` 
         on((`product`.`product_id` = `order_item`.`product_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`orders`.`is_store` = 3) 
      and 
      (
         `orders`.`supplier_id` > 0
      )
      and 
      (
         `orders`.`order_approval_status` = 7
      )
      and `order_supplier`.`order_item_id` in 
      (
         select
            `order_item`.`order_item_id` 
         from
            `order_item`
      )
   )
group by
   `order_supplier`.`order_id` 
union all
select
   `creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
   `creditor_payment`.`creditor_payment_id` AS `referenceId`,
   `creditor_payment`.`reference_number` AS `referenceCode`,
   `creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
   `creditor_payment`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_payment`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `creditor_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `creditor_payment_item`.`description` AS `transactionName`,
   concat('Payment for invoice of ', ' ', `creditor_invoice`.`invoice_number`) AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor_invoice` 
         on((`creditor_invoice`.`creditor_invoice_id` = `creditor_payment_item`.`creditor_invoice_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 0
   )
union all
select
   `creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
   `creditor_payment`.`creditor_payment_id` AS `referenceId`,
   `creditor_payment`.`reference_number` AS `referenceCode`,
   `creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
   `creditor_payment`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_payment`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `creditor_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `creditor_payment_item`.`description` AS `transactionName`,
   concat('Payment for invoice of ', ' ', `orders`.`supplier_invoice_number`) AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `orders` 
         on((`orders`.`order_id` = `creditor_payment_item`.`creditor_invoice_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 1
   )
union all
select
   `creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
   `creditor_payment`.`creditor_payment_id` AS `referenceId`,
   `creditor_payment`.`reference_number` AS `referenceCode`,
   `creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
   `creditor_payment`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_payment`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `creditor_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `creditor_payment_item`.`description` AS `transactionName`,
   concat('Payment of opening balance') AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor` 
         on((`creditor`.`creditor_id` = `creditor_payment_item`.`creditor_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 2
   )
union all
select
   `creditor_payment_item`.`creditor_payment_item_id` AS `transactionId`,
   `creditor_payment`.`creditor_payment_id` AS `referenceId`,
   `creditor_payment`.`reference_number` AS `referenceCode`,
   `creditor_payment_item`.`creditor_invoice_id` AS `payingFor`,
   `creditor_payment`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_payment`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `creditor_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   `creditor_payment_item`.`description` AS `transactionName`,
   concat('Payment on account') AS `transactionDescription`,
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
from
   (
(((`creditor_payment_item` 
      join
         `creditor_payment` 
         on((`creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`))) 
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))) 
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor` 
         on((`creditor`.`creditor_id` = `creditor_payment_item`.`creditor_id`))
   )
where
   (
      `creditor_payment_item`.`invoice_type` = 3
   )
union all
select
   `account_payments`.`account_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   '' AS `referenceCode`,
   '' AS `transactionCode`,
   '' AS `patient_id`,
   '' AS `recepientId`,
   `account_type`.`account_type_id` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `account`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   '' AS `transactionName`,
   `account_payments`.`account_payment_description` AS `transactionDescription`,
   0 AS `department_id`,
   `account_payments`.`amount_paid` AS `dr_amount`,
   '0' AS `cr_amount`,
   `account_payments`.`payment_date` AS `transactionDate`,
   `account_payments`.`payment_date` AS `createdAt`,
   `account_payments`.`payment_date` AS `referenceDate`,
   `account_payments`.`account_payment_deleted` AS `status`,
   'Expense' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'account_payments' AS `transactionTable`,
   '' AS `referenceTable` 
from
   (
(`account_payments` 
      left join
         `account` 
         on((`account_payments`.`account_to_id` = `account`.`account_id`))) 
      left join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`account_payments`.`account_to_type` = 4) 
      and 
      (
         `account_payments`.`account_payment_deleted` = 0
      )
   )
union all
select
   `account_payments`.`account_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   '' AS `referenceCode`,
   '' AS `transactionCode`,
   '' AS `patient_id`,
   '' AS `recepientId`,
   `account_type`.`account_type_id` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `account`.`account_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   '' AS `transactionName`,
   `account_payments`.`account_payment_description` AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   `account_payments`.`amount_paid` AS `cr_amount`,
   `account_payments`.`payment_date` AS `transactionDate`,
   `account_payments`.`payment_date` AS `createdAt`,
   `account_payments`.`payment_date` AS `referenceDate`,
   `account_payments`.`account_payment_deleted` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Purchase Payment' AS `transactionClassification`,
   'account_payments' AS `transactionTable`,
   '' AS `referenceTable` 
from
   (
(`account_payments` 
      left join
         `account` 
         on((`account_payments`.`account_from_id` = `account`.`account_id`))) 
      left join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))
   )
where
   (
(`account_payments`.`account_to_type` = 4) 
      and 
      (
         `account_payments`.`account_payment_deleted` = 0
      )
   )
;
CREATE OR REPLACE VIEW v_general_ledger_aging_by_date AS 
select
   * 
from
   v_general_ledger_aging 
ORDER BY
   createdAt;

