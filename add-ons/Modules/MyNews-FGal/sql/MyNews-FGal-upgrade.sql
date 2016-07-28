-- Rename the table from `photos` to `mnfgal_images`
ALTER TABLE `photos` RENAME `mnfgal_images` ;

-- Rename the `picid` column to `pid`.
ALTER TABLE `mnfgal_images` CHANGE `picid` `pid` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

-- Rename the `text` column to `title` and change it from a
-- "text" field to a varchar(40) field.  That should be plenty
-- for a "title"
ALTER TABLE `mnfgal_images` CHANGE `text` `title` VARCHAR( 40 ) NOT NULL ;

-- Rename the `extended` column to the `descr` column.  Short for
-- "description".
ALTER TABLE `mnfgal_images` CHANGE `extended` `descr` TEXT NOT NULL ;
