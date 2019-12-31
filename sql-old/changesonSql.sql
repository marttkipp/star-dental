-- Addition of Account Id in the service Table
ALTER TABLE `service` ADD `account_id` INT(50) NOT NULL AFTER `service_id`;
ALTER TABLE `payment_method` ADD `account_id` INT(50) NOT NULL AFTER `payment_method`;
ALTER TABLE `orders` ADD `account_id` INT(50) NOT NULL AFTER `deduction_type_id`;
ALTER TABLE `orders` ADD `reference_id` INT(50) NOT NULL AFTER `account_id`;
ALTER TABLE `finance_purchase` ADD `department_id` INT(50) NOT NULL AFTER `creditor_id`;
ALTER TABLE `account` ADD `paying_account` INT(50) NOT NULL AFTER `inventory_status`;
ALTER TABLE `branch` ADD `inventory_start_date` DATE() NOT NULL;
ALTER TABLE `store` ADD `store_deleted` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `visit_charge` ADD `provider_id` int(1) NOT NULL DEFAULT 0;
