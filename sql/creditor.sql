/*
 Navicat Premium Data Transfer

 Source Server         : Marto Server
 Source Server Type    : MySQL
 Source Server Version : 50560
 Source Host           : localhost:3306
 Source Schema         : bradley

 Target Server Type    : MySQL
 Target Server Version : 50560
 File Encoding         : 65001

 Date: 01/03/2019 10:41:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for creditor
-- ----------------------------
-- DROP TABLE IF EXISTS `creditor`;
-- CREATE TABLE `creditor`  (
--   `creditor_id` int(11) NOT NULL AUTO_INCREMENT,
--   `creditor_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_phone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_location` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_contact_person_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_contact` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
--   `creditor_status` tinyint(1) NOT NULL DEFAULT 0,
--   `creditor_contact_person_onames` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_contact_person_phone1` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_contact_person_phone2` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_contact_person_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
--   `created_by` int(11) NULL DEFAULT NULL,
--   `created` datetime NULL DEFAULT NULL,
--   `modified_by` int(11) NULL DEFAULT NULL,
--   `last_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `branch_code` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_building` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_floor` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_address` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_post_code` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_city` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
--   `creditor_opening_balance` double NULL DEFAULT NULL,
--   `debit_creditor_opening_balance` double NULL DEFAULT NULL,
--   `opening_balance` double(20, 5) NULL DEFAULT NULL,
--   `debit_id` int(11) NULL DEFAULT 0,
--   `creditor_type_id` int(11) NULL DEFAULT NULL,
--   PRIMARY KEY (`creditor_id`) USING BTREE
-- ) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of creditor
-- ----------------------------
-- INSERT INTO `creditor` VALUES (1, 'Allan', 'dkcheruto@gmail.com', '076727121', 'sad', '343', '', 0, 'allan', '0', '0', 'allan@gmassil.com', '076727121', 0, '2019-02-27 09:23:42', 0, '2019-02-27 06:23:42', 'SID', 'ssd', 'sd', 'sd', '242', '43', NULL, NULL, 0.00000, 2, 1);

-- ----------------------------
-- Table structure for creditor_credit_note
-- ----------------------------
DROP TABLE IF EXISTS `creditor_credit_note`;
CREATE TABLE `creditor_credit_note`  (
  `creditor_credit_note_id` int(50) NOT NULL AUTO_INCREMENT,
  `amount` double NULL DEFAULT NULL COMMENT 'amount before tax',
  `account_to_id` int(50) NULL DEFAULT NULL,
  `creditor_credit_note_status` int(50) NULL DEFAULT 1,
  `vat_charged` double NULL DEFAULT NULL COMMENT 'tax charged',
  `total_amount` double(11, 0) NULL DEFAULT NULL COMMENT 'amount adter tax',
  `created` date NULL DEFAULT NULL,
  `created_by` int(11) NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `invoice_year` int(50) NULL DEFAULT NULL,
  `invoice_month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `account_from_id` int(50) NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_credit_note_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for creditor_credit_note_item
-- ----------------------------
DROP TABLE IF EXISTS `creditor_credit_note_item`;
CREATE TABLE `creditor_credit_note_item`  (
  `creditor_credit_note_item_id` int(50) NOT NULL AUTO_INCREMENT,
  `creditor_credit_note_id` int(50) NULL DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `vat_type_id` int(11) NULL DEFAULT 0,
  `credit_note_charged_vat` double NULL DEFAULT NULL,
  `credit_note_amount` double NULL DEFAULT NULL,
  `creditor_credit_note_item_status` int(11) NULL DEFAULT 0,
  `created` date NULL DEFAULT NULL,
  `created_by` int(50) NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `creditor_invoice_id` int(50) NULL DEFAULT 0,
  `account_to_id` int(50) NULL DEFAULT NULL,
  `year` int(11) NULL DEFAULT NULL,
  `month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `personnel_id` int(50) NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_credit_note_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for creditor_invoice
-- ----------------------------
DROP TABLE IF EXISTS `creditor_invoice`;
CREATE TABLE `creditor_invoice`  (
  `creditor_invoice_id` int(50) NOT NULL AUTO_INCREMENT,
  `amount` double NULL DEFAULT NULL COMMENT 'amount before tax',
  `creditor_invoice_status` int(50) NULL DEFAULT 1,
  `vat_charged` double NULL DEFAULT NULL COMMENT 'tax charged',
  `total_amount` double(11, 0) NULL DEFAULT NULL COMMENT 'amount adter tax',
  `created` date NULL DEFAULT NULL,
  `created_by` int(11) NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `invoice_number` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `invoice_year` int(50) NULL DEFAULT NULL,
  `invoice_month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `property_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_invoice_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of creditor_invoice
-- ----------------------------
-- INSERT INTO `creditor_invoice` VALUES (1, 2000, 1, 0, 2000, '2019-02-27', 0, '2019-02-27', '2000', 1, 2019, '02', 1, NULL);

-- ----------------------------
-- Table structure for creditor_invoice_item
-- ----------------------------
DROP TABLE IF EXISTS `creditor_invoice_item`;
CREATE TABLE `creditor_invoice_item`  (
  `creditor_invoice_item_id` int(50) NOT NULL AUTO_INCREMENT,
  `creditor_invoice_id` int(50) NULL DEFAULT NULL,
  `item_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `unit_price` double NULL DEFAULT NULL,
  `vat_type_id` int(11) NULL DEFAULT 0,
  `vat_amount` double NULL DEFAULT NULL,
  `total_amount` double NULL DEFAULT NULL,
  `creditor_invoice_item_status` int(11) NULL DEFAULT 0,
  `created` date NULL DEFAULT NULL,
  `created_by` int(50) NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `quantity` int(50) NULL DEFAULT 1,
  `account_to_id` int(50) NULL DEFAULT NULL,
  `year` int(11) NULL DEFAULT NULL,
  `month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_invoice_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of creditor_invoice_item
-- ----------------------------
-- INSERT INTO `creditor_invoice_item` VALUES (1, 1, '1', 2000, 0, 0, 2000, 1, '2019-02-27', 0, 1, 1, 12, 2019, '02');

-- ----------------------------
-- Table structure for creditor_payment
-- ----------------------------
DROP TABLE IF EXISTS `creditor_payment`;
CREATE TABLE `creditor_payment`  (
  `creditor_payment_id` int(50) NOT NULL AUTO_INCREMENT,
  `creditor_payment_status` int(50) NULL DEFAULT 1,
  `total_amount` double(11, 0) NULL DEFAULT NULL COMMENT 'amount adter tax',
  `created` date NULL DEFAULT NULL,
  `created_by` int(11) NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `payment_year` int(50) NULL DEFAULT NULL,
  `payment_month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `account_from_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_payment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for creditor_payment_item
-- ----------------------------
DROP TABLE IF EXISTS `creditor_payment_item`;
CREATE TABLE `creditor_payment_item`  (
  `creditor_payment_item_id` int(50) NOT NULL AUTO_INCREMENT,
  `creditor_payment_id` int(50) NULL DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `amount_paid` double NULL DEFAULT NULL,
  `creditor_payment_item_status` int(11) NULL DEFAULT 0,
  `created` date NULL DEFAULT NULL,
  `created_by` int(50) NULL DEFAULT NULL,
  `creditor_id` int(50) NULL DEFAULT NULL,
  `creditor_invoice_id` int(50) NULL DEFAULT 0,
  `year` int(11) NULL DEFAULT NULL,
  `month` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invoice_type` int(11) NULL DEFAULT 0,
  `invoice_number` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`creditor_payment_item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
