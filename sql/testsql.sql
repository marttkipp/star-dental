create or replace view v_creditor_ledger as 
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
   'Opening Balance' AS `transactionName`,
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
   'Opening Balance' AS `transactionName`,
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
   `creditor_invoice`.`creditor_invoice_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   `creditor_invoice`.`invoice_number` AS `referenceCode`,
   `creditor_invoice`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_invoice`.`creditor_id` AS `recepientId`,
   '' AS `accountParentId`,
   '' AS `accountsclassfication`,
   '' AS `accountId`,
   '' AS `accountName`,
   'Invoice' AS `transactionName`,
   concat('Invoice', ':', `creditor_invoice`.`invoice_number`) AS `transactionDescription`,
   '0' AS `department_id`,
   (SELECT SUM(`creditor_invoice_item`.`total_amount`) FROM creditor_invoice_item WHERE `creditor_invoice`.`creditor_invoice_id` = `creditor_invoice_item`.`creditor_invoice_id`) AS `dr_amount`,
   '0' AS `cr_amount`,
   `creditor_invoice`.`transaction_date` AS `transactionDate`,
   `creditor_invoice`.`created` AS `createdAt`,
   `creditor_invoice`.`transaction_date` AS `referenceDate`,
   `creditor_invoice`.`creditor_invoice_status` AS `status`,
   'Expense' AS `transactionCategory`,
   'Creditors Invoices' AS `transactionClassification`,
   'creditor_invoice_item' AS `transactionTable`,
   'creditor_invoice' AS `referenceTable` 
from creditor_invoice WHERE creditor_invoice_status = 1
  
union all
select
   `creditor_credit_note`.`creditor_credit_note_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   `creditor_credit_note`.`invoice_number` AS `referenceCode`,
   `creditor_credit_note`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_credit_note`.`creditor_id` AS `recepientId`,
   '' AS `accountParentId`,
   '' AS `accountsclassfication`,
   '' AS `accountId`,
   '' AS `accountName`,
   'Credit Note' AS `transactionName`,
   concat('Credit Note for',' ',creditor_invoice.invoice_number) AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   (SELECT sum(`creditor_credit_note_item`.`credit_note_amount`) FROM `creditor_credit_note_item` WHERE `creditor_credit_note`.`creditor_credit_note_id` = `creditor_credit_note_item`.`creditor_credit_note_id`) AS `cr_amount`,
   `creditor_credit_note`.`transaction_date` AS `transactionDate`,
   `creditor_credit_note`.`created` AS `createdAt`,
   `creditor_invoice`.`transaction_date` AS `referenceDate`,
   `creditor_credit_note`.`creditor_credit_note_status` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Creditors Credit Notes' AS `transactionClassification`,
   'creditor_credit_note' AS `transactionTable`,
   'creditor_credit_note_item' AS `referenceTable` 
from creditor_credit_note
 join`creditor_invoice` on((`creditor_invoice`.`creditor_invoice_id` = `creditor_credit_note`.`creditor_invoice_id`))
WHERE creditor_credit_note_status= 1

union all

select
   `order_supplier`.`order_supplier_id` AS `transactionId`,
   `orders`.`order_id` AS `referenceId`,
   '' AS `payingFor`,
   `orders`.`supplier_invoice_number` AS `referenceCode`,
   '' AS `transactionCode`,
   '' AS `patient_id`,
   `orders`.`supplier_id` AS `recepientId`,
   '' AS `accountParentId`,
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
   `creditor_payment`.`creditor_payment_id` AS `transactionId`,
   '' AS `referenceId`,
   '' AS `payingFor`,
   `creditor_payment`.`reference_number` AS `referenceCode`,
   `creditor_payment`.`document_number` AS `transactionCode`,
   '' AS `patient_id`,
   `creditor_payment`.`creditor_id` AS `recepientId`,
   `account`.`parent_account` AS `accountParentId`,
   `account_type`.`account_type_name` AS `accountsclassfication`,
   `creditor_payment`.`account_from_id` AS `accountId`,
   `account`.`account_name` AS `accountName`,
   'Payment' AS `transactionName`,
   concat('Payment on account') AS `transactionDescription`,
   0 AS `department_id`,
   0 AS `dr_amount`,
   (SELECT SUM(`creditor_payment_item`.`amount_paid`) FROM creditor_payment_item WHERE `creditor_payment`.`creditor_payment_id` = `creditor_payment_item`.`creditor_payment_id`)AS `cr_amount`,
   `creditor_payment`.`transaction_date` AS `transactionDate`,
   `creditor_payment`.`created` AS `createdAt`,
   `creditor_payment`.`created` AS `referenceDate`,
   `creditor_payment`.`creditor_payment_status` AS `status`,
   'Expense Payment' AS `transactionCategory`,
   'Creditors Invoices Payments' AS `transactionClassification`,
   'creditor_payment' AS `transactionTable`,
   'creditor_payment_item' AS `referenceTable` 
from
   (
(((`creditor_payment` 
     
      join
         `account` 
         on((`account`.`account_id` = `creditor_payment`.`account_from_id`))))
      join
         `account_type` 
         on((`account_type`.`account_type_id` = `account`.`account_type_id`))) 
      join
         `creditor` 
         on((`creditor`.`creditor_id` = `creditor_payment`.`creditor_id`))
   )
where
   (
      `creditor_payment`.`creditor_payment_status` = 1
   );

CREATE OR REPLACE VIEW v_creditor_ledger_by_date AS 
select
   * 
from
   v_creditor_ledger 
ORDER BY
   transactionDate;
