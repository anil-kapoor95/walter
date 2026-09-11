-- =====================================================================
-- Meeting Room Booking System - add "Morning + Afternoon" and
-- "Afternoon + Evening" combo booking durations
-- Run once, against the live schema (table prefix added automatically).
-- Safe to run inside a transaction; take a database backup first.
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. Bookings: allow book_by = 'morningafternoon' / 'afternoonevening'
-- ---------------------------------------------------------------------
ALTER TABLE `bookings`
  MODIFY COLUMN `book_by` enum('multiday','morning','afternoon','evening','hour','morningafternoon','afternoonevening')
  DEFAULT NULL;

-- ---------------------------------------------------------------------
-- 2. Translatable labels: customer-facing duration dropdown
--    (Admin -> Opties -> Vertaling, editable like the other "front_*" labels)
-- ---------------------------------------------------------------------
INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('front_morningafternoon', 'frontend', 'Label / Morning + Afternoon', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Ochtend + Middag', 'script');

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('front_afternoonevening', 'frontend', 'Label / Afternoon + Evening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Middag + Avond', 'script');

-- ---------------------------------------------------------------------
-- 3. Translatable labels: admin duration dropdown (Boekingen -> Nieuw/Bewerken)
-- ---------------------------------------------------------------------
INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('time_morningafternoon', 'backend', 'Label / Morning + Afternoon', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Ochtend + Middag', 'script');

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('time_afternoonevening', 'backend', 'Label / Afternoon + Evening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Middag + Avond', 'script');

-- ---------------------------------------------------------------------
-- 4. Bust the cached field index so the new labels appear immediately
--    (otherwise they stay hidden until the admin session is recycled)
-- ---------------------------------------------------------------------
UPDATE `options`
   SET `value` = MD5(CONCAT(`value`, NOW()))
 WHERE `key` = 'o_fields_index';

COMMIT;
