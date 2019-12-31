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

 Date: 03/03/2019 12:28:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for finance_purchase
-- ----------------------------
DROP TABLE IF EXISTS `finance_purchase`;
CREATE TABLE `finance_purchase`  (
  `finance_purchase_id` int(50) NOT NULL AUTO_INCREMENT,
  `finance_purchase_amount` double NOT NULL,
  `finance_purchase_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `property_id` int(11) NOT NULL,
  `account_to_id` int(11) NOT NULL,
  `quantity` int(11) NULL DEFAULT 1,
  `finance_purchase_status` int(11) NULL DEFAULT 1,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NULL DEFAULT NULL,
  `modified_by` int(11) NULL DEFAULT NULL,
  `last_modified` datetime NULL DEFAULT NULL,
  `transaction_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `creditor_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`finance_purchase_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of finance_purchase
-- ----------------------------
-- INSERT INTO `finance_purchase` VALUES (1, 4000, 'fixing of cctv', 0, 5, 1, 1, '2019-02-27 09:19:02', 0, NULL, '2019-02-27 09:19:02', '4444', 1, '2019-02-27', 0);

-- ----------------------------
-- Table structure for finance_purchase_payment
-- ----------------------------
DROP TABLE IF EXISTS `finance_purchase_payment`;
CREATE TABLE `finance_purchase_payment`  (
  `finance_purchase_payment_id` int(50) NOT NULL AUTO_INCREMENT,
  `finance_purchase_id` int(50) NOT NULL COMMENT 'finance table purchase id ',
  `document_number` int(50) NOT NULL,
  `transaction_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'reference number from account',
  `account_from_id` int(11) NULL DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NULL DEFAULT NULL,
  `last_modified` datetime NULL DEFAULT NULL,
  `finance_purchase_payment_status` int(11) NULL DEFAULT 1,
  `transaction_date` date NOT NULL,
  `amount_paid` double NOT NULL,
  PRIMARY KEY (`finance_purchase_payment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of finance_purchase_payment
-- ----------------------------
-- INSERT INTO `finance_purchase_payment` VALUES (1, 1, 1, '444553', 3, '2019-02-27 09:19:39', 0, '2019-02-27 09:19:39', 1, '2019-02-27', 4000);

-- ----------------------------
-- Table structure for finance_transfer
-- ----------------------------
DROP TABLE IF EXISTS `finance_transfer`;
CREATE TABLE `finance_transfer`  (
  `finance_transfer_id` int(50) NOT NULL AUTO_INCREMENT,
  `finance_transfer_amount` double NULL DEFAULT NULL,
  `reference_number` int(50) NULL DEFAULT NULL,
  `account_from_id` int(11) NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `created_by` int(50) NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `finance_transfer_status` int(11) NULL DEFAULT 1,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `remarks` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`finance_transfer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for finance_transfered
-- ----------------------------
DROP TABLE IF EXISTS `finance_transfered`;
CREATE TABLE `finance_transfered`  (
  `finance_transfered_id` int(50) NOT NULL AUTO_INCREMENT,
  `finance_transfer_id` int(50) NULL DEFAULT NULL,
  `finance_transfered_amount` double NULL DEFAULT NULL,
  `account_to_id` int(11) NULL DEFAULT NULL,
  `transaction_date` date NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `document_number` int(50) NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `remarks` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_by` int(50) NULL DEFAULT NULL,
  PRIMARY KEY (`finance_transfered_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
