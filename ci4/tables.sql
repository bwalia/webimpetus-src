-- Adminer 4.8.1 MySQL 11.2.2-MariaDB-1:11.2.2+maria~ubu2204 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `address_line_3` varchar(255) DEFAULT NULL,
  `address_line_4` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `post_code` varchar(255) DEFAULT NULL,
  `uuid_contact` varchar(255) DEFAULT NULL,
  `uuid_user` varchar(255) DEFAULT NULL,
  `uuid_customer` varchar(255) DEFAULT NULL,
  `address_type` varchar(255) DEFAULT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `blocks_list`;
CREATE TABLE `blocks_list` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid_linked_table` varchar(63) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `text` mediumtext DEFAULT NULL,
  `status` int(5) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `title` varchar(245) DEFAULT NULL,
  `webpages_id` int(11) DEFAULT NULL,
  `sort` int(245) DEFAULT NULL,
  `type` varchar(245) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `blog_comments`;
CREATE TABLE `blog_comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `blog_uuid` varchar(36) NOT NULL,
  `Name` varchar(36) NOT NULL,
  `email` varchar(50) NOT NULL,
  `comments` text NOT NULL,
  `comment_by` varchar(36) NOT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `Created` int(11) NOT NULL,
  `Modified` int(11) NOT NULL,
  `site_uuid` varchar(36) NOT NULL,
  `OrderNum` int(11) NOT NULL,
  `Server_Number` int(11) NOT NULL,
  `Status` smallint(6) NOT NULL DEFAULT 0,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `blog_images`;
CREATE TABLE `blog_images` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(5) unsigned NOT NULL,
  `image` varchar(100) NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `businesses`;
CREATE TABLE `businesses` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `uuid` varchar(100) DEFAULT NULL,
  `default_business` int(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `company_address` varchar(245) DEFAULT NULL,
  `company_number` varchar(245) DEFAULT NULL,
  `vat_number` varchar(245) DEFAULT NULL,
  `email` varchar(245) DEFAULT NULL,
  `web_site` varchar(245) DEFAULT NULL,
  `telephone_no` varchar(245) DEFAULT NULL,
  `payment_page_url` varchar(245) DEFAULT NULL,
  `country_code` varchar(245) DEFAULT NULL,
  `language_code` varchar(7) DEFAULT NULL,
  `directors` varchar(245) DEFAULT NULL,
  `no_of_shares` decimal(12,2) DEFAULT NULL,
  `trading_as` varchar(245) DEFAULT NULL,
  `business_contacts` varchar(245) DEFAULT NULL,
  `business_code` varchar(24) DEFAULT NULL,
  `frontend_domain` varchar(124) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `business_contacts`;
CREATE TABLE `business_contacts` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `saludation` varchar(150) DEFAULT NULL,
  `comments` varchar(150) DEFAULT NULL,
  `news_letter_status` varchar(150) DEFAULT NULL,
  `allow_web_access` int(1) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `direct_phone` varchar(100) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `direct_fax` varchar(255) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `user_uuid` varchar(36) NOT NULL,
  `name` varchar(124) NOT NULL,
  `notes` text NOT NULL,
  `image_logo` longblob NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `sort_order` int(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(45) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `saludation` varchar(150) DEFAULT NULL,
  `comments` varchar(150) DEFAULT NULL,
  `news_letter_status` varchar(150) DEFAULT NULL,
  `allow_web_access` int(1) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `direct_phone` varchar(100) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `direct_fax` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `contact_type` varchar(245) DEFAULT NULL,
  `uuid` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `content_blocks_list`;
CREATE TABLE `content_blocks_list` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `text` mediumtext DEFAULT NULL,
  `status` int(5) NOT NULL DEFAULT 1,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `title` varchar(245) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `sort` int(245) DEFAULT NULL,
  `type` varchar(245) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `content_category`;
CREATE TABLE `content_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` int(11) NOT NULL DEFAULT 0,
  `modified` int(11) NOT NULL DEFAULT 0,
  `groupid` int(11) NOT NULL DEFAULT 0,
  `categoryid` int(11) NOT NULL DEFAULT 0,
  `uuid` int(11) NOT NULL DEFAULT 0,
  `contentid` int(11) NOT NULL DEFAULT 0,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DocumentCategories_ID` (`id`),
  KEY `DocumentCategories_Created` (`created`),
  KEY `DocumentCategories_Modified` (`modified`),
  KEY `DocumentCategories_CategoryGroupID` (`groupid`),
  KEY `DocumentCategories_CategoryID` (`categoryid`),
  KEY `DocumentCategories_UserID` (`uuid`),
  KEY `DocumentCategories_DocumentID` (`contentid`),
  KEY `categoryid` (`categoryid`,`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `content_list`;
CREATE TABLE `content_list` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) DEFAULT NULL,
  `title` text NOT NULL,
  `sub_title` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `type` int(5) NOT NULL DEFAULT 1,
  `status` int(5) NOT NULL DEFAULT 1,
  `code` text NOT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `custom_fields` longtext DEFAULT NULL,
  `custom_assets` longtext DEFAULT NULL,
  `user_uuid` varchar(64) DEFAULT NULL,
  `publish_date` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `categories` varchar(245) DEFAULT NULL,
  `published_date` int(11) DEFAULT NULL,
  `language_code` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `content_list__custom_fields`;
CREATE TABLE `content_list__custom_fields` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `content_list_id` varchar(36) NOT NULL,
  `custom_field_id` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) DEFAULT NULL,
  `company_name` varchar(100) NOT NULL,
  `acc_no` varchar(100) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `contact_firstname` varchar(100) NOT NULL,
  `contact_lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `supplier` int(1) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `categories` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `customer_categories`;
CREATE TABLE `customer_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `customer__contact`;
CREATE TABLE `customer__contact` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `customer_uuid` varchar(36) NOT NULL,
  `contact_uuid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `custom_fields`;
CREATE TABLE `custom_fields` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `field_name` varchar(36) NOT NULL,
  `field_type` varchar(36) NOT NULL,
  `field_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `demo`;
CREATE TABLE `demo` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `city` varchar(150) NOT NULL,
  `age` varchar(150) NOT NULL,
  `salary` varchar(150) NOT NULL,
  `phonenumber` int(25) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(150) NOT NULL,
  `file` varchar(150) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `document_date` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `billing_status` varchar(150) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `uuid_linked_table` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `customer_uuid` varchar(36) NOT NULL,
  `sid` varchar(36) DEFAULT NULL,
  `name` varchar(124) NOT NULL,
  `notes` text NOT NULL,
  `image_logo` longblob DEFAULT NULL,
  `image_type` varchar(255) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `saludation` varchar(150) DEFAULT NULL,
  `comments` varchar(150) DEFAULT NULL,
  `news_letter_status` varchar(150) DEFAULT NULL,
  `allow_web_access` int(1) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `direct_phone` varchar(100) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `direct_fax` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `businesses` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `enquiries`;
CREATE TABLE `enquiries` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `type` int(5) NOT NULL DEFAULT 1,
  `attachment` text DEFAULT NULL,
  `att_type` longtext DEFAULT NULL,
  `contentid` int(100) NOT NULL DEFAULT 0,
  `ipaddress` varchar(255) DEFAULT NULL,
  `custom_fields` longtext DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `key_values`;
CREATE TABLE `key_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_product` varchar(100) DEFAULT NULL,
  `key_name` varchar(127) DEFAULT NULL,
  `key_value` varchar(24) DEFAULT NULL,
  `note` varchar(127) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `media_list`;
CREATE TABLE `media_list` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid_linked_table` varchar(63) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` int(3) NOT NULL DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `icon` varchar(45) DEFAULT 'fa fa-globe',
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `language_code` varchar(10) NOT NULL DEFAULT 'en',
  `menu_fts` varchar(255) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `menu_fts` (`menu_fts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `menu_category`;
CREATE TABLE `menu_category` (
  `uuid` int(25) NOT NULL,
  `uuid_category` varchar(36) NOT NULL,
  `uuid_menu` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `meta_fields`;
CREATE TABLE `meta_fields` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) DEFAULT NULL,
  `name` varchar(127) DEFAULT NULL,
  `code` varchar(23) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sku` varchar(64) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `stock_available` int(11) DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_product` varchar(100) DEFAULT NULL,
  `uuid_category` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `budget` decimal(10,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `currency` varchar(20) NOT NULL,
  `deadline_date` int(11) DEFAULT NULL,
  `employees_id` int(11) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `purchase_invoices`;
CREATE TABLE `purchase_invoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` int(11) DEFAULT NULL,
  `custom_invoice_number` varchar(64) DEFAULT NULL,
  `terms` varchar(245) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `bill_to` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `order_by` text DEFAULT NULL,
  `project_code` varchar(245) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `due_date` int(11) DEFAULT NULL,
  `balance_due` decimal(12,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `total_paid` decimal(12,2) DEFAULT NULL,
  `paid_date` int(11) DEFAULT NULL,
  `analysis_ledger` varchar(45) DEFAULT NULL,
  `analysis_account` varchar(45) DEFAULT NULL,
  `payment_pin_or_passcode` varchar(245) DEFAULT NULL,
  `invoice_tax_rate` decimal(12,2) DEFAULT NULL,
  `inv_template` varchar(45) DEFAULT NULL,
  `print_template_code` int(11) DEFAULT NULL,
  `internal_notes` longtext DEFAULT NULL,
  `inv_customer_ref_po` varchar(245) DEFAULT NULL,
  `currency_code` varchar(45) DEFAULT NULL,
  `base_currency_code` varchar(45) DEFAULT NULL,
  `inv_exchange_rate` decimal(12,2) DEFAULT NULL,
  `inv_tax_code` varchar(45) DEFAULT NULL,
  `total_hours` decimal(12,2) DEFAULT NULL,
  `total_due` decimal(12,2) DEFAULT NULL,
  `total_tax` decimal(12,2) DEFAULT NULL,
  `total_due_with_tax` decimal(12,2) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `purchase_invoice_items`;
CREATE TABLE `purchase_invoice_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(1023) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `purchase_invoices_uuid` varchar(64) DEFAULT NULL,
  `hours` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `purchase_invoice_notes`;
CREATE TABLE `purchase_invoice_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_invoices_id` int(11) NOT NULL,
  `purchase_invoices_uuid` varchar(64) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` int(11) DEFAULT NULL,
  `custom_order_number` varchar(64) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `bill_to` longtext DEFAULT NULL,
  `comments` longtext DEFAULT NULL,
  `order_by` text DEFAULT NULL,
  `project_code` varchar(245) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `balance_due` decimal(12,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `total_paid` decimal(12,2) DEFAULT NULL,
  `paid_date` int(11) DEFAULT NULL,
  `payment_pin_or_passcode` varchar(245) DEFAULT NULL,
  `invoice_tax_rate` decimal(12,2) DEFAULT NULL,
  `template` varchar(45) DEFAULT NULL,
  `customer_ref_po` varchar(245) DEFAULT NULL,
  `tax_rate` decimal(12,2) DEFAULT NULL,
  `currency_code` varchar(45) DEFAULT NULL,
  `base_currency_code` varchar(45) DEFAULT NULL,
  `exchange_rate` decimal(12,2) DEFAULT NULL,
  `tax_code` varchar(45) DEFAULT NULL,
  `total_qty` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `total_due` decimal(12,2) DEFAULT NULL,
  `total_tax` decimal(12,2) DEFAULT NULL,
  `total_due_with_tax` decimal(12,2) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(1023) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `purchase_orders_uuid` varchar(64) DEFAULT NULL,
  `qty` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `uuid_business_id` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `roles__permissions`;
CREATE TABLE `roles__permissions` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `role_id` varchar(36) NOT NULL,
  `permission_id` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `sales_invoices`;
CREATE TABLE `sales_invoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` int(11) DEFAULT NULL,
  `custom_invoice_number` varchar(64) DEFAULT NULL,
  `terms` varchar(245) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `bill_to` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `order_by` text DEFAULT NULL,
  `project_code` varchar(245) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `due_date` int(11) DEFAULT NULL,
  `balance_due` decimal(12,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `total_paid` decimal(12,2) DEFAULT NULL,
  `paid_date` int(11) DEFAULT NULL,
  `analysis_ledger` varchar(45) DEFAULT NULL,
  `analysis_account` varchar(45) DEFAULT NULL,
  `payment_pin_or_passcode` varchar(245) DEFAULT NULL,
  `invoice_tax_rate` decimal(12,2) DEFAULT NULL,
  `inv_template` varchar(45) DEFAULT NULL,
  `print_template_code` int(11) DEFAULT NULL,
  `internal_notes` longtext DEFAULT NULL,
  `inv_customer_ref_po` varchar(245) DEFAULT NULL,
  `currency_code` varchar(45) DEFAULT NULL,
  `base_currency_code` varchar(45) DEFAULT NULL,
  `inv_exchange_rate` decimal(12,2) DEFAULT NULL,
  `inv_tax_code` varchar(45) DEFAULT NULL,
  `total_hours` decimal(12,2) DEFAULT NULL,
  `total_due` decimal(12,2) DEFAULT NULL,
  `total_tax` decimal(12,2) DEFAULT NULL,
  `total_due_with_tax` decimal(12,2) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `sales_invoice_items`;
CREATE TABLE `sales_invoice_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(1023) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `sales_invoices_uuid` varchar(64) DEFAULT NULL,
  `hours` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `sales_invoice_notes`;
CREATE TABLE `sales_invoice_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sales_invoices_uuid` varchar(64) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `secrets`;
CREATE TABLE `secrets` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) NOT NULL,
  `key_value` longtext DEFAULT NULL,
  `status` int(5) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  `secret_tags` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `secrets_services`;
CREATE TABLE `secrets_services` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `secret_id` varchar(36) NOT NULL DEFAULT '0',
  `service_id` varchar(36) NOT NULL DEFAULT '0',
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_uuid` varchar(36) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `cid` int(25) NOT NULL DEFAULT 0,
  `tid` int(25) NOT NULL DEFAULT 0,
  `name` varchar(124) NOT NULL,
  `code` varchar(6) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `nginx_config` mediumtext NOT NULL,
  `varnish_config` mediumtext NOT NULL,
  `notes` text NOT NULL,
  `image_logo` longblob DEFAULT NULL,
  `image_brand` longblob DEFAULT NULL,
  `system_type` varchar(36) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `env_tags` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `service__domains`;
CREATE TABLE `service__domains` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `service_uuid` varchar(36) NOT NULL,
  `domain_uuid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `sprints`;
CREATE TABLE `sprints` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sprint_name` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `reported_by` int(11) DEFAULT NULL,
  `projects_id` int(11) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `estimated_hour` int(11) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `category` varchar(127) DEFAULT NULL,
  `priority` varchar(127) DEFAULT NULL,
  `sprint_id` int(11) DEFAULT NULL,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `task_id` int(11) DEFAULT NULL,
  `customers_id` int(11) DEFAULT NULL,
  `contacts_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `taxes`;
CREATE TABLE `taxes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tax_code` varchar(24) DEFAULT NULL,
  `tax_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `template_content` longtext DEFAULT NULL,
  `comment` longtext DEFAULT NULL,
  `module_name` varchar(127) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `templates__services`;
CREATE TABLE `templates__services` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `secret_template_id` varchar(36) NOT NULL,
  `values_template_id` varchar(36) NOT NULL,
  `service_id` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `tenants`;
CREATE TABLE `tenants` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `name` varchar(124) NOT NULL,
  `address` text NOT NULL,
  `contact_name` varchar(124) NOT NULL,
  `contact_email` varchar(124) NOT NULL,
  `notes` text NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `user_uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `tenants_services`;
CREATE TABLE `tenants_services` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `sid` int(25) NOT NULL DEFAULT 0,
  `tid` int(25) NOT NULL DEFAULT 0,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `timeslips`;
CREATE TABLE `timeslips` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(150) NOT NULL,
  `task_name` varchar(150) DEFAULT NULL,
  `week_no` int(11) DEFAULT NULL,
  `employee_name` varchar(150) DEFAULT NULL,
  `slip_start_date` int(11) DEFAULT NULL,
  `slip_timer_started` varchar(50) DEFAULT NULL,
  `slip_end_date` int(11) DEFAULT NULL,
  `slip_timer_end` varchar(50) DEFAULT NULL,
  `break_time` tinyint(1) DEFAULT NULL,
  `break_time_start` varchar(50) DEFAULT NULL,
  `break_time_end` varchar(50) DEFAULT NULL,
  `slip_hours` decimal(10,2) DEFAULT NULL,
  `slip_description` text DEFAULT NULL,
  `slip_rate` decimal(10,2) DEFAULT NULL,
  `slip_timer_accumulated_seconds` int(11) DEFAULT NULL,
  `billing_status` varchar(150) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `name` varchar(124) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(36) NOT NULL,
  `address` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `role` varchar(36) DEFAULT NULL,
  `notes` text NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `permissions` text DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `language_code` varchar(10) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `profile_img` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `user_business`;
CREATE TABLE `user_business` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_business_id` varchar(2047) DEFAULT NULL,
  `primary_business_uuid` varchar(127) DEFAULT NULL,
  `user_uuid` varchar(36) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DROP TABLE IF EXISTS `webpage_categories`;
CREATE TABLE `webpage_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `webpage_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `webpage_images`;
CREATE TABLE `webpage_images` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `webpage_id` int(5) unsigned NOT NULL,
  `image` varchar(100) NOT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `work_orders`;
CREATE TABLE `work_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` int(11) DEFAULT NULL,
  `custom_order_number` varchar(64) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `bill_to` longtext DEFAULT NULL,
  `comments` longtext DEFAULT NULL,
  `order_by` text DEFAULT NULL,
  `project_code` varchar(245) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `balance_due` decimal(12,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `total_paid` decimal(12,2) DEFAULT NULL,
  `paid_date` int(11) DEFAULT NULL,
  `payment_pin_or_passcode` varchar(245) DEFAULT NULL,
  `invoice_tax_rate` decimal(12,2) DEFAULT NULL,
  `template` varchar(45) DEFAULT NULL,
  `customer_ref_po` varchar(245) DEFAULT NULL,
  `tax_rate` decimal(12,2) DEFAULT NULL,
  `currency_code` varchar(45) DEFAULT NULL,
  `base_currency_code` varchar(45) DEFAULT NULL,
  `exchange_rate` decimal(12,2) DEFAULT NULL,
  `tax_code` varchar(45) DEFAULT NULL,
  `total_qty` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `total_due` decimal(12,2) DEFAULT NULL,
  `total_tax` decimal(12,2) DEFAULT NULL,
  `total_due_with_tax` decimal(12,2) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `uuid` varchar(64) DEFAULT NULL,
  `uuid_business_id` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `work_order_items`;
CREATE TABLE `work_order_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(1023) DEFAULT NULL,
  `rate` decimal(12,2) DEFAULT NULL,
  `work_orders_uuid` varchar(64) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `uuid` varchar(150) NOT NULL,
  `uuid_business_id` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `modified_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


-- 2024-04-01 22:41:46