ALTER TABLE pim_prestashop_attribute_mapping RENAME TO pim_prestashop_attribute_mapping_old;
ALTER TABLE pim_prestashop_category_mapping RENAME TO pim_prestashop_category_mapping_old;
ALTER TABLE pim_prestashop_family_mapping RENAME TO pim_prestashop_family_mapping_old;

ALTER TABLE pim_prestashop_category_mapping_old DROP FOREIGN KEY FK_4FE1A41412469DE2;
ALTER TABLE pim_prestashop_attribute_mapping_old DROP FOREIGN KEY FK_62B148FEB6E62EFA;
ALTER TABLE pim_prestashop_family_mapping_old DROP FOREIGN KEY FK_FAA9D644C35E566A;
