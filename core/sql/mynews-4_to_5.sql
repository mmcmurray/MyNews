-- $Id: mynews-4_to_5.sql 513 2005-09-27 23:52:52Z alien $

-- Need to extend the password field in the "authors" table to work with
-- md5 passwords.
    ALTER TABLE `authors` CHANGE `password` `password` VARCHAR( 32 ) DEFAULT NULL;
    UPDATE `authors` SET `password` = 'e99a18c428cb38d5f260853678922e03' WHERE `artnr` = '1' LIMIT 1;

-- section field in the sections table needs to be UNIQUE
    ALTER TABLE `sections` ADD UNIQUE ( `section`);

-- Need to rename all of the fields in the calendar table to reflect 
-- the new naming convention.
    ALTER TABLE `calendar` CHANGE `msg_id` `eid` INT( 11 ) NOT NULL AUTO_INCREMENT;
    ALTER TABLE `calendar` CHANGE `msg_month` `month` INT( 2 ) DEFAULT '0' NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_day` `day` INT( 2 ) DEFAULT '0' NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_year` `year` INT( 4 ) DEFAULT '0' NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_title` `type` VARCHAR( 12 ) NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_who` `title` VARCHAR( 20 ) NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_text` `descrip` TEXT NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_poster_id` `userid` VARCHAR( 16 ) DEFAULT '0' NOT NULL;
    ALTER TABLE `calendar` CHANGE `msg_recurring` `recurring` TINYTEXT DEFAULT NULL;
    ALTER TABLE `calendar` CHANGE `msg_active` `active` INT( 1 ) DEFAULT '0' NOT NULL;

-- Need to add the new ctstmp field to the calendar table.
    ALTER TABLE `calendar` ADD `ctstmp` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER `eid`;

-- Need to delete the fields in the calendar table that we no longer need.
    ALTER TABLE `calendar` DROP `msg_where`;
    ALTER TABLE `calendar` DROP `msg_city`;
    ALTER TABLE `calendar` DROP `msg_state`;

-- Need to delete the fields in the authors table that we no longer need.
    ALTER TABLE `authors` DROP `listening`;
    ALTER TABLE `authors` DROP `reading`;
    ALTER TABLE `authors` DROP `thinking`;

-- Since the value of Active is now true (1), we need to switch the inactive and active flags.
    UPDATE news set active = 3 where active = 0;
    UPDATE news set active = 0 where active = 1;
    UPDATE news set active = 1 where active = 3;

-- OPTIMIZE all of the tables for good measure.
    OPTIMIZE TABLE `authors`;
    OPTIMIZE TABLE `calendar`;
    OPTIMIZE TABLE `comments`;
    OPTIMIZE TABLE `news`;
    OPTIMIZE TABLE `sections`;
    OPTIMIZE TABLE `submissions`;
