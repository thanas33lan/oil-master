-- Thana 14-Sep-2020
INSERT INTO `resources` (`resource_id`, `display_name`) VALUES ('Admin\\Controller\\QtyDetails', 'Quantity Details');
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Admin\\Controller\\QtyDetails', 'index', 'Access'), ('Admin\\Controller\\QtyDetails', 'add', 'Add'), ('Admin\\Controller\\QtyDetails', 'edit', 'Edit');
ALTER TABLE `qty_details` ADD `created_on` DATETIME NULL DEFAULT NULL AFTER `qty_status`, ADD `created_by` INT(11) NULL DEFAULT NULL AFTER `created_on`, ADD `modified_on` DATETIME NULL DEFAULT NULL AFTER `created_by`, ADD `modified_by` INT(11) NULL DEFAULT NULL AFTER `modified_on`;

-- Thana 09-Oct-2020
INSERT INTO `resources` (`resource_id`, `display_name`) VALUES ('Admin\\Controller\\Supplier', 'Manage Suppliers');
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Admin\\Controller\\Supplier', 'index', 'Access'), ('Admin\\Controller\\Supplier', 'add', 'Add'), ('Admin\\Controller\\Supplier', 'edit', 'Edit'), ('Admin\\Controller\\Supplier', 'change-status', 'Change Status');
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Admin\\Controller\\QtyDetails', 'change-status', 'Change Status');