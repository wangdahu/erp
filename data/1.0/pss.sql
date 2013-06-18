/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:33306
Source Database       : pss

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2012-08-16 14:14:05
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `pss_back_buy`
-- ----------------------------
DROP TABLE IF EXISTS `pss_back_buy`;
CREATE TABLE `pss_back_buy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT '外键，关联pss_buy_order.id',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `back_id` int(11) NOT NULL COMMENT '退货人',
  `back_name` varchar(50) NOT NULL DEFAULT '' COMMENT '退货人',
  `back_dept_id` int(11) NOT NULL,
  `back_dept` varchar(50) NOT NULL,
  `buyer_id` int(11) NOT NULL COMMENT '采购员用户id',
  `buyer` varchar(50) NOT NULL COMMENT '采购员',
  `buyer_dept_id` int(11) NOT NULL,
  `buyer_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(50) NOT NULL COMMENT '供应商名称',
  `supplier_address` varchar(100) NOT NULL,
  `supplier_linkman` varchar(50) NOT NULL COMMENT '供应商联系人',
  `supplier_phone` varchar(100) NOT NULL,
  `remark` text,
  `total_price` decimal(10,2) NOT NULL COMMENT '总额',
  `approval_id` int(11) NOT NULL COMMENT 'task Id',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `back_id` (`back_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `order_id` (`order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购退货单';

-- ----------------------------
-- Records of pss_back_buy
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_back_buy_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_back_buy_item`;
CREATE TABLE `pss_back_buy_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `back_buy_id` int(11) NOT NULL COMMENT '外键，关联pss_back_buy.id',
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `storehouse_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `back_buy_id` (`back_buy_id`),
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购退货明细';

-- ----------------------------
-- Records of pss_back_buy_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_back_sales`
-- ----------------------------
DROP TABLE IF EXISTS `pss_back_sales`;
CREATE TABLE `pss_back_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '外键，关联pss_sales_order.id',
  `no` varchar(50) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `back_id` int(11) NOT NULL COMMENT '退货人',
  `back_name` varchar(50) NOT NULL DEFAULT '' COMMENT '退货人',
  `back_dept_id` int(11) NOT NULL,
  `back_dept` varchar(50) NOT NULL,
  `salesman_id` int(11) NOT NULL COMMENT '业务员用户id',
  `salesman` varchar(50) NOT NULL COMMENT '业务员',
  `salesman_dept_id` int(11) NOT NULL,
  `salesman_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `customer_name` varchar(50) NOT NULL COMMENT '客户名称',
  `customer_address` varchar(100) NOT NULL,
  `customer_linkman` varchar(50) NOT NULL COMMENT '客户联系人',
  `customer_phone` varchar(100) NOT NULL,
  `delivery_date` date NOT NULL,
  `balance_date` date NOT NULL COMMENT '结算期限',
  `remark` text,
  `total_price` decimal(10,2) NOT NULL,
  `approval_id` int(11) NOT NULL COMMENT 'task ID',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `order_id` (`order_id`),
  KEY `back_id` (`back_id`),
  KEY `salesman_id` (`salesman_id`),
  KEY `customer_id` (`customer_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售退货单';

-- ----------------------------
-- Records of pss_back_sales
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_back_sales_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_back_sales_item`;
CREATE TABLE `pss_back_sales_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `back_sales_id` int(11) NOT NULL COMMENT '外键，关联pss_back_sales.id',
  `stock_id` int(11) NOT NULL,
  `storehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `back_sales_id` (`back_sales_id`),
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售退货明细';

-- ----------------------------
-- Records of pss_back_sales_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_billing`
-- ----------------------------
DROP TABLE IF EXISTS `pss_billing`;
CREATE TABLE `pss_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL COMMENT '单据编号',
  `operator` varchar(50) NOT NULL COMMENT '记账员',
  `operator_id` int(11) NOT NULL COMMENT '记账员id',
  `type` tinyint(4) NOT NULL COMMENT '类型 0=收入 1=支出',
  `balance_type` tinyint(4) NOT NULL COMMENT '结算方式 0=现金 1=转账',
  `cheque` varchar(50) DEFAULT '' COMMENT '支票号',
  `partner_type` tinyint(4) NOT NULL COMMENT '收支对象类型 0=customer 1=supplier',
  `partner_id` int(11) NOT NULL COMMENT '对象id ',
  `partner_name` varchar(100) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收支表';

-- ----------------------------
-- Records of pss_billing
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_billing_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_billing_item`;
CREATE TABLE `pss_billing_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '收支科目 0,1,2,3,4,5',
  `price` decimal(10,2) NOT NULL,
  `remark` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `billing_id` (`billing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收支明细';

-- ----------------------------
-- Records of pss_billing_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_assignment`;
CREATE TABLE `pss_buy_assignment` (
  `assign_id` int(11) NOT NULL COMMENT '用户id或角色id',
  `product_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=用户,1=角色',
  PRIMARY KEY (`assign_id`,`product_id`,`type`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购分配表';

-- ----------------------------
-- Records of pss_buy_assignment
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_order`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_order`;
CREATE TABLE `pss_buy_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(20) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `buyer_id` int(11) NOT NULL COMMENT '采购员用户id',
  `buyer` varchar(50) NOT NULL COMMENT '采购员',
  `buyer_dept_id` int(11) NOT NULL,
  `buyer_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(50) NOT NULL COMMENT '供应商名称',
  `supplier_address` varchar(100) NOT NULL,
  `supplier_linkman` varchar(50) NOT NULL COMMENT '供应商联系人',
  `supplier_phone` varchar(100) NOT NULL,
  `delivery_date` date NOT NULL,
  `balance_date` date NOT NULL COMMENT '结算期限',
  `remark` text,
  `total_price` decimal(10,2) NOT NULL COMMENT '总额',
  `status` tinyint(4) NOT NULL COMMENT '订单状态',
  `is_history` tinyint(4) NOT NULL,
  `approval_id` int(11) DEFAULT NULL COMMENT 'task ID',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `buyer_id` (`buyer_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单';

-- ----------------------------
-- Records of pss_buy_order
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_order_item`;
CREATE TABLE `pss_buy_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '外键，关联pss_buy_order.id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单明细';

-- ----------------------------
-- Records of pss_buy_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_urged`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_urged`;
CREATE TABLE `pss_buy_urged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) NOT NULL COMMENT '催办人',
  `to_uid` int(11) NOT NULL COMMENT '催办对象',
  `item_id` int(11) NOT NULL COMMENT '销售清单id',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `created` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from_uid` (`from_uid`),
  KEY `to_uid` (`to_uid`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_buy_urged
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_urged_relate`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_urged_relate`;
CREATE TABLE `pss_buy_urged_relate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urged_id` int(11) NOT NULL COMMENT 'pss_buy_urged.id外键',
  `from_uid` int(11) NOT NULL COMMENT '催办人id',
  `from_name` varchar(50) NOT NULL COMMENT '催办人',
  `to_uid` int(11) NOT NULL COMMENT '被催办人id',
  `to_name` varchar(50) NOT NULL COMMENT '被催办人',
  PRIMARY KEY (`id`),
  KEY `urged_id` (`urged_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_buy_urged_relate
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_buy_urged_reply`
-- ----------------------------
DROP TABLE IF EXISTS `pss_buy_urged_reply`;
CREATE TABLE `pss_buy_urged_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urged_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '回复人',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `urged_id` (`urged_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_buy_urged_reply
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_customer`
-- ----------------------------
DROP TABLE IF EXISTS `pss_customer`;
CREATE TABLE `pss_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '客户类型(0=企业, 1=个人)',
  `no` varchar(50) DEFAULT NULL COMMENT '客户编号',
  `business` varchar(100) DEFAULT NULL COMMENT '主营业务',
  `country` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `address` varchar(100) DEFAULT '',
  `phone` varchar(50) DEFAULT '',
  `fax` varchar(50) DEFAULT '',
  `followman` varchar(50) NOT NULL DEFAULT '',
  `followman_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `followman_id` (`followman_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户表';

-- ----------------------------
-- Records of pss_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_customer_linkman`
-- ----------------------------
DROP TABLE IF EXISTS `pss_customer_linkman`;
CREATE TABLE `pss_customer_linkman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT '外键，关联pss_customer.id',
  `name` varchar(50) NOT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `department` varchar(50) DEFAULT '' COMMENT '部门',
  `post` varchar(50) DEFAULT '' COMMENT '职务',
  `in_no` varchar(50) DEFAULT '' COMMENT 'in号',
  `email` varchar(50) DEFAULT '',
  `fax` varchar(50) DEFAULT '',
  `im_no` varchar(100) DEFAULT '' COMMENT 'QQ/MSN',
  `mobile` text COMMENT '手机',
  `phone_type` varchar(100) DEFAULT '',
  `phone` text COMMENT '电话',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户联系人';

-- ----------------------------
-- Records of pss_customer_linkman
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_form_flow`
-- ----------------------------
DROP TABLE IF EXISTS `pss_form_flow`;
CREATE TABLE `pss_form_flow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(50) NOT NULL COMMENT '表单名称',
  `flow_id` int(11) NOT NULL COMMENT '流程ID',
  `deleted` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除，0未删除，1删除',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_name`) USING BTREE,
  KEY `flow_id` (`flow_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='审批流程绑定表单';

-- ----------------------------
-- Records of pss_form_flow
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_number`
-- ----------------------------
DROP TABLE IF EXISTS `pss_number`;
CREATE TABLE `pss_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_number
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_pay_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_pay_item`;
CREATE TABLE `pss_pay_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT '销售单id',
  `operator` varchar(50) NOT NULL COMMENT '记账员',
  `operator_id` int(11) NOT NULL COMMENT '记账员id',
  `price` decimal(10,2) NOT NULL COMMENT '实收金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_pay_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_product`
-- ----------------------------
DROP TABLE IF EXISTS `pss_product`;
CREATE TABLE `pss_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `cate_id` int(11) NOT NULL COMMENT '分类',
  `unit_id` int(11) NOT NULL COMMENT '单位id',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌id',
  `no` varchar(50) NOT NULL COMMENT '产品型号',
  `code` varchar(100) DEFAULT '' COMMENT '产品编码',
  `jan_code` varchar(100) DEFAULT NULL COMMENT '产品条码',
  `user_id` int(11) NOT NULL COMMENT '录入人id',
  `safe_quantity` int(11) DEFAULT NULL COMMENT '安全库存',
  `min_quantity` int(11) DEFAULT NULL COMMENT '最少定量',
  `producting_place` varchar(50) DEFAULT '' COMMENT '生产地',
  `buy_price` decimal(10,2) DEFAULT NULL COMMENT '采购价',
  `sales_price` decimal(10,2) DEFAULT NULL COMMENT '销售价',
  `discount_price` decimal(10,2) DEFAULT NULL COMMENT '折扣价',
  `wholesales_price` decimal(10,2) DEFAULT NULL COMMENT '批发价',
  `low_price` decimal(10,2) DEFAULT NULL COMMENT '销售低价',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '参考成本',
  `photo` varchar(255) DEFAULT '' COMMENT '图片',
  `remark` text COMMENT '备注',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '修改时间',
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标志',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `unit_id` (`unit_id`),
  KEY `brand_id` (`brand_id`),
  KEY `safe_quantity` (`safe_quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品';

-- ----------------------------
-- Records of pss_product
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_product_brand`
-- ----------------------------
DROP TABLE IF EXISTS `pss_product_brand`;
CREATE TABLE `pss_product_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品品牌';

-- ----------------------------
-- Records of pss_product_brand
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_product_cate`
-- ----------------------------
DROP TABLE IF EXISTS `pss_product_cate`;
CREATE TABLE `pss_product_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品分类';

-- ----------------------------
-- Records of pss_product_cate
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_product_detail`
-- ----------------------------
DROP TABLE IF EXISTS `pss_product_detail`;
CREATE TABLE `pss_product_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `en_intro` text COMMENT '英文介绍',
  `en_remark` text COMMENT '英文备注',
  `size` varchar(100) DEFAULT '' COMMENT '尺寸',
  `volume` varchar(50) DEFAULT '' COMMENT '体积',
  `gross_weight` varchar(50) DEFAULT '' COMMENT '毛重',
  `weight` varchar(50) DEFAULT '' COMMENT '净重',
  `packaging` varchar(50) DEFAULT '' COMMENT '包装',
  `material` varchar(50) DEFAULT '' COMMENT '材质',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品明细';

-- ----------------------------
-- Records of pss_product_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_product_unit`
-- ----------------------------
DROP TABLE IF EXISTS `pss_product_unit`;
CREATE TABLE `pss_product_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT '',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品单位';

-- ----------------------------
-- Records of pss_product_unit
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_receive_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_receive_item`;
CREATE TABLE `pss_receive_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT 'pss_receive.id的外键',
  `order_id` int(11) NOT NULL COMMENT '销售单id',
  `operator` varchar(50) NOT NULL COMMENT '记账员',
  `operator_id` int(11) NOT NULL COMMENT '记账员id',
  `price` decimal(10,2) NOT NULL COMMENT '实收金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pss_receive_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_sales_order`
-- ----------------------------
DROP TABLE IF EXISTS `pss_sales_order`;
CREATE TABLE `pss_sales_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `salesman_id` int(11) NOT NULL COMMENT '业务员用户id',
  `salesman` varchar(50) NOT NULL COMMENT '业务员',
  `salesman_dept_id` int(11) NOT NULL,
  `salesman_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `customer_name` varchar(50) NOT NULL COMMENT '客户名称',
  `customer_address` varchar(100) NOT NULL,
  `customer_linkman` varchar(50) NOT NULL COMMENT '客户联系人',
  `customer_phone` varchar(100) NOT NULL,
  `delivery_date` date NOT NULL,
  `balance_date` date NOT NULL COMMENT '结算期限',
  `remark` text,
  `total_price` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '订单状态',
  `is_history` tinyint(4) NOT NULL,
  `approval_id` int(11) DEFAULT NULL,
  `approval_status` int(11) NOT NULL DEFAULT '1' COMMENT '审批id',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`) USING BTREE,
  KEY `salesman_id` (`salesman_id`),
  KEY `customer_id` (`customer_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售单';

-- ----------------------------
-- Records of pss_sales_order
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_sales_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_sales_order_item`;
CREATE TABLE `pss_sales_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '外键，关联pss_sales_order.id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售单明细';

-- ----------------------------
-- Records of pss_sales_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock`;
CREATE TABLE `pss_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `storehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '外键，关联pss_storehouse.id',
  `quantity` int(11) DEFAULT '0' COMMENT '商品数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `storehouse_product` (`storehouse_id`,`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='库存表';

-- ----------------------------
-- Records of pss_stock
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock_allocate`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock_allocate`;
CREATE TABLE `pss_stock_allocate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `allocate_man_id` int(11) NOT NULL COMMENT '调拨人id',
  `allocate_name` varchar(50) NOT NULL COMMENT '调拨人',
  `allocate_dept_id` int(11) NOT NULL,
  `allocate_dept` varchar(50) NOT NULL,
  `storehouse_id` int(11) NOT NULL,
  `remark` text,
  `approval_id` int(11) NOT NULL COMMENT 'task ID',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `allocate_man_id` (`allocate_man_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='调拨单';

-- ----------------------------
-- Records of pss_stock_allocate
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock_allocate_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock_allocate_item`;
CREATE TABLE `pss_stock_allocate_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `allocate_id` int(11) NOT NULL COMMENT '外键，pss_stock_allocate的主键',
  `from_stock_id` int(11) NOT NULL COMMENT '库存id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `from_storehouse_id` int(11) NOT NULL COMMENT '调拨源仓库 关联pss_storehouse.id',
  `to_storehouse_id` int(11) NOT NULL COMMENT '调拨目标仓库 关联pss_storehouse.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  PRIMARY KEY (`id`),
  KEY `allocate_id` (`allocate_id`),
  KEY `from_stock_id` (`from_stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='调拨明细';

-- ----------------------------
-- Records of pss_stock_allocate_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock_in`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock_in`;
CREATE TABLE `pss_stock_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人id',
  `buy_order_id` int(11) DEFAULT NULL COMMENT '关联采购订单',
  `in_id` int(11) NOT NULL COMMENT '入库人id',
  `in_name` varchar(50) NOT NULL COMMENT '入库人',
  `buyer_id` int(11) NOT NULL COMMENT '采购员',
  `buyer` varchar(50) NOT NULL COMMENT '采购员',
  `in_dept_id` int(11) NOT NULL,
  `in_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(50) NOT NULL COMMENT '供应商名称',
  `supplier_address` varchar(100) NOT NULL,
  `supplier_linkman` varchar(50) NOT NULL COMMENT '供应商联系人',
  `supplier_phone` varchar(100) NOT NULL,
  `remark` text,
  `total_price` decimal(10,2) NOT NULL,
  `approval_id` int(11) DEFAULT NULL COMMENT 'task ID',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `buy_order_id` (`buy_order_id`),
  KEY `in_id` (`in_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入库单';

-- ----------------------------
-- Records of pss_stock_in
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock_item`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock_item`;
CREATE TABLE `pss_stock_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL COMMENT '外键，pss_stock_in.id或pss_stock_out.id',
  `type` tinyint(4) NOT NULL COMMENT '0=入库,1=出库',
  `stock_id` int(11) NOT NULL COMMENT '库存id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联pss_product.id',
  `storehouse_id` int(11) NOT NULL COMMENT '外键，关联pss_storehouse.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`,`type`),
  KEY `stock_id` (`stock_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入库/出库 明细';

-- ----------------------------
-- Records of pss_stock_item
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_stock_out`
-- ----------------------------
DROP TABLE IF EXISTS `pss_stock_out`;
CREATE TABLE `pss_stock_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `sales_order_id` int(11) DEFAULT NULL,
  `out_id` int(11) NOT NULL COMMENT '出库人id',
  `out_name` varchar(50) NOT NULL COMMENT '出库人',
  `out_dept_id` int(11) NOT NULL,
  `out_dept` varchar(50) NOT NULL,
  `corp_name` varchar(50) NOT NULL COMMENT '我方公司名称',
  `address` varchar(100) NOT NULL,
  `linkman` varchar(50) NOT NULL COMMENT '我方联系人',
  `phone` varchar(50) NOT NULL COMMENT '电话',
  `customer_id` int(11) NOT NULL COMMENT '客户id',
  `customer_name` varchar(50) NOT NULL COMMENT '客户名称',
  `customer_address` varchar(100) NOT NULL,
  `customer_linkman` varchar(50) NOT NULL COMMENT '客户联系人',
  `customer_phone` varchar(100) NOT NULL,
  `remark` text,
  `total_price` decimal(10,2) NOT NULL,
  `approval_id` int(11) DEFAULT NULL COMMENT 'task Id',
  `approval_status` int(11) DEFAULT '1',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `out_id` (`out_id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `approval_id` (`approval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='出货单';

-- ----------------------------
-- Records of pss_stock_out
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_storehouse`
-- ----------------------------
DROP TABLE IF EXISTS `pss_storehouse`;
CREATE TABLE `pss_storehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `storeman_id` int(11) NOT NULL COMMENT '仓管人id',
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='仓库表';

-- ----------------------------
-- Records of pss_storehouse
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_supplier`
-- ----------------------------
DROP TABLE IF EXISTS `pss_supplier`;
CREATE TABLE `pss_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `no` varchar(50) DEFAULT NULL COMMENT '供应商编号',
  `business` varchar(100) DEFAULT NULL COMMENT '主营业务',
  `country` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `address` varchar(100) DEFAULT '',
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `followman` varchar(50) NOT NULL DEFAULT '',
  `followman_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`),
  KEY `followman_id` (`followman_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商表';

-- ----------------------------
-- Records of pss_supplier
-- ----------------------------

-- ----------------------------
-- Table structure for `pss_supplier_linkman`
-- ----------------------------
DROP TABLE IF EXISTS `pss_supplier_linkman`;
CREATE TABLE `pss_supplier_linkman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL COMMENT '外键，关联pss_supplier.id',
  `name` varchar(50) NOT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `department` varchar(50) DEFAULT '' COMMENT '部门',
  `post` varchar(50) DEFAULT '' COMMENT '职务',
  `in_no` varchar(50) DEFAULT '' COMMENT 'in号',
  `email` varchar(50) DEFAULT '',
  `fax` varchar(50) DEFAULT '',
  `im_no` varchar(100) DEFAULT '' COMMENT 'QQ/MSN',
  `mobile` text COMMENT '手机',
  `phone_type` varchar(100) DEFAULT '',
  `phone` text COMMENT '电话',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商联系人';

-- ----------------------------
-- Records of pss_supplier_linkman
-- ----------------------------
