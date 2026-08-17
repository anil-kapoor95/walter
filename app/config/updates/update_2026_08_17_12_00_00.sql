-- =====================================================================
-- Meeting Room Booking System - add "evening" as a third bookable period
-- Run once, against the live schema (table prefix: )
-- Safe to run inside a transaction; take a database backup first.
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. Weekly working hours: evening window per weekday
-- ---------------------------------------------------------------------
ALTER TABLE `working_times`
  ADD COLUMN `monday_evening_from` time DEFAULT NULL AFTER `monday_afternoon_to`,
  ADD COLUMN `monday_evening_to`   time DEFAULT NULL AFTER `monday_evening_from`,
  ADD COLUMN `tuesday_evening_from` time DEFAULT NULL AFTER `tuesday_afternoon_to`,
  ADD COLUMN `tuesday_evening_to`   time DEFAULT NULL AFTER `tuesday_evening_from`,
  ADD COLUMN `wednesday_evening_from` time DEFAULT NULL AFTER `wednesday_afternoon_to`,
  ADD COLUMN `wednesday_evening_to`   time DEFAULT NULL AFTER `wednesday_evening_from`,
  ADD COLUMN `thursday_evening_from` time DEFAULT NULL AFTER `thursday_afternoon_to`,
  ADD COLUMN `thursday_evening_to`   time DEFAULT NULL AFTER `thursday_evening_from`,
  ADD COLUMN `friday_evening_from` time DEFAULT NULL AFTER `friday_afternoon_to`,
  ADD COLUMN `friday_evening_to`   time DEFAULT NULL AFTER `friday_evening_from`,
  ADD COLUMN `saturday_evening_from` time DEFAULT NULL AFTER `saturday_afternoon_to`,
  ADD COLUMN `saturday_evening_to`   time DEFAULT NULL AFTER `saturday_evening_from`,
  ADD COLUMN `sunday_evening_from` time DEFAULT NULL AFTER `sunday_afternoon_to`,
  ADD COLUMN `sunday_evening_to`   time DEFAULT NULL AFTER `sunday_evening_from`;

-- Seed a sensible default so existing rows are not NULL.
-- Adjust in Admin -> Options -> Working hours afterwards.
UPDATE `working_times` SET
    `monday_evening_from` = COALESCE(`monday_evening_from`, '19:00:00'),
    `monday_evening_to`   = COALESCE(`monday_evening_to`,   '23:00:00'),
    `tuesday_evening_from` = COALESCE(`tuesday_evening_from`, '19:00:00'),
    `tuesday_evening_to`   = COALESCE(`tuesday_evening_to`,   '23:00:00'),
    `wednesday_evening_from` = COALESCE(`wednesday_evening_from`, '19:00:00'),
    `wednesday_evening_to`   = COALESCE(`wednesday_evening_to`,   '23:00:00'),
    `thursday_evening_from` = COALESCE(`thursday_evening_from`, '19:00:00'),
    `thursday_evening_to`   = COALESCE(`thursday_evening_to`,   '23:00:00'),
    `friday_evening_from` = COALESCE(`friday_evening_from`, '19:00:00'),
    `friday_evening_to`   = COALESCE(`friday_evening_to`,   '23:00:00'),
    `saturday_evening_from` = COALESCE(`saturday_evening_from`, '19:00:00'),
    `saturday_evening_to`   = COALESCE(`saturday_evening_to`,   '23:00:00'),
    `sunday_evening_from` = COALESCE(`sunday_evening_from`, '19:00:00'),
    `sunday_evening_to`   = COALESCE(`sunday_evening_to`,   '23:00:00');

-- ---------------------------------------------------------------------
-- 2. Per-date overrides (the "Amended" tab): evening window
-- ---------------------------------------------------------------------
ALTER TABLE `dates`
  ADD COLUMN `start_evening` time DEFAULT NULL AFTER `end_afternoon`,
  ADD COLUMN `end_evening`   time DEFAULT NULL AFTER `start_evening`;

UPDATE `dates`
   SET `start_evening` = COALESCE(`start_evening`, '19:00:00'),
       `end_evening`   = COALESCE(`end_evening`,   '23:00:00');

-- ---------------------------------------------------------------------
-- 3. Bookings: allow book_by = 'evening'
-- ---------------------------------------------------------------------
ALTER TABLE `bookings`
  MODIFY COLUMN `book_by` enum('multiday','morning','afternoon','evening','hour')
  CHARACTER SET utf8 DEFAULT NULL;

-- ---------------------------------------------------------------------
-- 4. Translatable labels (one row per configured locale)
-- ---------------------------------------------------------------------
INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('time_evening', 'backend', 'Label / Evening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT @id, 'pjField', l.`id`, 'title', 'Evening', 'script' FROM `plugin_locale` l;

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('time_evening_from', 'backend', 'Label / Evening from', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT @id, 'pjField', l.`id`, 'title', 'Evening from', 'script' FROM `plugin_locale` l;

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('time_evening_to', 'backend', 'Label / Evening to', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT @id, 'pjField', l.`id`, 'title', 'Evening until', 'script' FROM `plugin_locale` l;

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('front_evening', 'frontend', 'Label / Evening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
SELECT @id, 'pjField', l.`id`, 'title', 'Evening', 'script' FROM `plugin_locale` l;

-- ---------------------------------------------------------------------
-- 5. Bust the cached field index so the new labels appear immediately
--    (otherwise they stay hidden until the admin session is recycled)
-- ---------------------------------------------------------------------
UPDATE `options`
   SET `value` = MD5(CONCAT(`value`, NOW()))
 WHERE `key` = 'o_fields_index';

COMMIT;
