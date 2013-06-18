/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50515
Source Host           : localhost:3307
Source Database       : erp

Target Server Type    : MYSQL
Target Server Version : 50515
File Encoding         : 65001

Date: 2012-06-28 09:42:51
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `erp_back_buy`
-- ----------------------------
DROP TABLE IF EXISTS `erp_back_buy`;
CREATE TABLE `erp_back_buy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT '外键，关联erp_buy_order.id',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `back_id` int(11) NOT NULL COMMENT '退货人',
  `back_name` varchar(50) NOT NULL DEFAULT '' COMMENT '退货人',
  `buyer_id` int(11) NOT NULL COMMENT '采购员用户id',
  `buyer` varchar(50) NOT NULL COMMENT '采购员',
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
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购退货单';

-- ----------------------------
-- Records of erp_back_buy
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_back_buy_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_back_buy_item`;
CREATE TABLE `erp_back_buy_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `back_buy_id` int(11) NOT NULL COMMENT '外键，关联erp_back_buy.id',
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `storehouse_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`),
  KEY `back_buy_id` (`back_buy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购退货明细';

-- ----------------------------
-- Records of erp_back_buy_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_back_sales`
-- ----------------------------
DROP TABLE IF EXISTS `erp_back_sales`;
CREATE TABLE `erp_back_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '外键，关联erp_sales_order.id',
  `no` varchar(50) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `back_id` int(11) NOT NULL COMMENT '退货人',
  `back_name` varchar(50) NOT NULL DEFAULT '' COMMENT '退货人',
  `salesman_id` int(11) NOT NULL COMMENT '业务员用户id',
  `salesman` varchar(50) NOT NULL COMMENT '业务员',
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
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售退货单';

-- ----------------------------
-- Records of erp_back_sales
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_back_sales_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_back_sales_item`;
CREATE TABLE `erp_back_sales_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `back_sales_id` int(11) NOT NULL COMMENT '外键，关联erp_back_sales.id',
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售退货明细';

-- ----------------------------
-- Records of erp_back_sales_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_billing`
-- ----------------------------
DROP TABLE IF EXISTS `erp_billing`;
CREATE TABLE `erp_billing` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收支表';

-- ----------------------------
-- Records of erp_billing
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_billing_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_billing_item`;
CREATE TABLE `erp_billing_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '收支科目 0,1,2,3,4,5',
  `price` decimal(10,2) NOT NULL,
  `remark` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收支明细';

-- ----------------------------
-- Records of erp_billing_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_buy_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `erp_buy_assignment`;
CREATE TABLE `erp_buy_assignment` (
  `assign_id` int(11) NOT NULL COMMENT '用户id或角色id',
  `product_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=用户,1=角色',
  PRIMARY KEY (`assign_id`,`product_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购分配表';

-- ----------------------------
-- Records of erp_buy_assignment
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_buy_order`
-- ----------------------------
DROP TABLE IF EXISTS `erp_buy_order`;
CREATE TABLE `erp_buy_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(20) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '填单人id',
  `buyer_id` int(11) NOT NULL COMMENT '采购员用户id',
  `buyer` varchar(50) NOT NULL COMMENT '采购员',
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
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单';

-- ----------------------------
-- Records of erp_buy_order
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_buy_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_buy_order_item`;
CREATE TABLE `erp_buy_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '外键，关联erp_buy_order.id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单明细';

-- ----------------------------
-- Records of erp_buy_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_buy_urged`
-- ----------------------------
DROP TABLE IF EXISTS `erp_buy_urged`;
CREATE TABLE `erp_buy_urged` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) NOT NULL COMMENT '催办人',
  `to_uid` int(11) NOT NULL COMMENT '催办对象',
  `item_id` int(11) NOT NULL COMMENT '销售清单id',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of erp_buy_urged
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_buy_urged_reply`
-- ----------------------------
DROP TABLE IF EXISTS `erp_buy_urged_reply`;
CREATE TABLE `erp_buy_urged_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urged_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '回复人',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_buy_urged_reply
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_customer`
-- ----------------------------
DROP TABLE IF EXISTS `erp_customer`;
CREATE TABLE `erp_customer` (
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
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户表';

-- ----------------------------
-- Records of erp_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_customer_linkman`
-- ----------------------------
DROP TABLE IF EXISTS `erp_customer_linkman`;
CREATE TABLE `erp_customer_linkman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT '外键，关联erp_customer.id',
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户联系人';

-- ----------------------------
-- Records of erp_customer_linkman
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_number`
-- ----------------------------
DROP TABLE IF EXISTS `erp_number`;
CREATE TABLE `erp_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_number
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_pay_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_pay_item`;
CREATE TABLE `erp_pay_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT '销售单id',
  `operator` varchar(50) NOT NULL COMMENT '记账员',
  `operator_id` int(11) NOT NULL COMMENT '记账员id',
  `price` decimal(10,2) NOT NULL COMMENT '实收金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_pay_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_product`
-- ----------------------------
DROP TABLE IF EXISTS `erp_product`;
CREATE TABLE `erp_product` (
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
  `created` datetime NOT NULL COMMENT '创建时间',
  `updated` datetime NOT NULL COMMENT '修改时间',
  `deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标志',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品';

-- ----------------------------
-- Records of erp_product
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_product_brand`
-- ----------------------------
DROP TABLE IF EXISTS `erp_product_brand`;
CREATE TABLE `erp_product_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品品牌';

-- ----------------------------
-- Records of erp_product_brand
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_product_cate`
-- ----------------------------
DROP TABLE IF EXISTS `erp_product_cate`;
CREATE TABLE `erp_product_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品分类';

-- ----------------------------
-- Records of erp_product_cate
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_product_detail`
-- ----------------------------
DROP TABLE IF EXISTS `erp_product_detail`;
CREATE TABLE `erp_product_detail` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品明细';

-- ----------------------------
-- Records of erp_product_detail
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_product_unit`
-- ----------------------------
DROP TABLE IF EXISTS `erp_product_unit`;
CREATE TABLE `erp_product_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品单位';

-- ----------------------------
-- Records of erp_product_unit
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_receive_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_receive_item`;
CREATE TABLE `erp_receive_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL COMMENT 'erp_receive.id的外键',
  `order_id` int(11) NOT NULL COMMENT '销售单id',
  `operator` varchar(50) NOT NULL COMMENT '记账员',
  `operator_id` int(11) NOT NULL COMMENT '记账员id',
  `price` decimal(10,2) NOT NULL COMMENT '实收金额',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of erp_receive_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_sales_order`
-- ----------------------------
DROP TABLE IF EXISTS `erp_sales_order`;
CREATE TABLE `erp_sales_order` (
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
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售单';

-- ----------------------------
-- Records of erp_sales_order
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_sales_order_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_sales_order_item`;
CREATE TABLE `erp_sales_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '外键，关联erp_sales_order.id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售单明细';

-- ----------------------------
-- Records of erp_sales_order_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock`;
CREATE TABLE `erp_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `storehouse_id` int(11) NOT NULL DEFAULT '0' COMMENT '外键，关联erp_storehouse.id',
  `quantity` int(11) DEFAULT '0' COMMENT '商品数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `storehouse_product` (`storehouse_id`,`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='库存表';

-- ----------------------------
-- Records of erp_stock
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock_allocate`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock_allocate`;
CREATE TABLE `erp_stock_allocate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `allocate_id` int(11) NOT NULL COMMENT '调拨人id',
  `allocate_name` varchar(50) NOT NULL COMMENT '调拨人',
  `storehouse_id` int(11) NOT NULL,
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='调拨单';

-- ----------------------------
-- Records of erp_stock_allocate
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock_allocate_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock_allocate_item`;
CREATE TABLE `erp_stock_allocate_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `allocate_id` int(11) NOT NULL COMMENT '外键，erp_stock_in或erp_stock_out或erp_stock_allocate的主键',
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `from_storehouse_id` int(11) NOT NULL COMMENT '调拨源仓库 关联erp_storehouse.id',
  `to_storehouse_id` int(11) NOT NULL COMMENT '调拨目标仓库 关联erp_storehouse.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='调拨明细';

-- ----------------------------
-- Records of erp_stock_allocate_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock_in`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock_in`;
CREATE TABLE `erp_stock_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人id',
  `buy_order_id` int(11) DEFAULT NULL COMMENT '关联采购订单',
  `in_id` int(11) NOT NULL COMMENT '入库人id',
  `in_name` varchar(50) NOT NULL COMMENT '入库人',
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
  `approval_status` tinyint(4) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL COMMENT '审批状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入库单';

-- ----------------------------
-- Records of erp_stock_in
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock_item`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock_item`;
CREATE TABLE `erp_stock_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL COMMENT '外键，erp_stock_in.id或erp_stock_out.id',
  `type` tinyint(4) NOT NULL COMMENT '0=入库,1=出库',
  `stock_id` int(11) NOT NULL COMMENT '库存id',
  `product_id` int(11) NOT NULL COMMENT '外键，关联erp_product.id',
  `storehouse_id` int(11) NOT NULL COMMENT '外键，关联erp_storehouse.id',
  `product_name` varchar(50) NOT NULL,
  `product_no` varchar(50) DEFAULT '',
  `product_brand` varchar(50) DEFAULT '',
  `product_unit` varchar(20) DEFAULT '',
  `product_cate` varchar(50) DEFAULT '',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) NOT NULL COMMENT '单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='入库/出库 明细';

-- ----------------------------
-- Records of erp_stock_item
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_stock_out`
-- ----------------------------
DROP TABLE IF EXISTS `erp_stock_out`;
CREATE TABLE `erp_stock_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `sales_order_id` int(11) DEFAULT NULL,
  `out_id` int(11) NOT NULL COMMENT '出库人id',
  `out_name` varchar(50) NOT NULL COMMENT '出库人',
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
  `approval_status` tinyint(4) NOT NULL COMMENT '审批状态',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='出货单';

-- ----------------------------
-- Records of erp_stock_out
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_storehouse`
-- ----------------------------
DROP TABLE IF EXISTS `erp_storehouse`;
CREATE TABLE `erp_storehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `storeman_id` int(11) NOT NULL COMMENT '仓管人id',
  `user_id` int(11) NOT NULL COMMENT '录入人',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='仓库表';

-- ----------------------------
-- Records of erp_storehouse
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_supplier`
-- ----------------------------
DROP TABLE IF EXISTS `erp_supplier`;
CREATE TABLE `erp_supplier` (
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
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no` (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商表';

-- ----------------------------
-- Records of erp_supplier
-- ----------------------------

-- ----------------------------
-- Table structure for `erp_supplier_linkman`
-- ----------------------------
DROP TABLE IF EXISTS `erp_supplier_linkman`;
CREATE TABLE `erp_supplier_linkman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL COMMENT '外键，关联erp_supplier.id',
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='供应商联系人';

-- ----------------------------
-- Records of erp_supplier_linkman
-- ----------------------------
