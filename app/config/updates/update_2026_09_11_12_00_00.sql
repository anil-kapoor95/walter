-- =====================================================================
-- Meeting Room Booking System - dedicated admin-configurable prices for
-- the "Morning + Afternoon" and "Afternoon + Evening" combo durations.
--
-- Previously the combo price was computed on the fly as
-- price_half_day * 2. This adds two real per-room price columns
-- (price_morning_afternoon / price_afternoon_evening) so the admin can
-- set them independently on the Room create/edit screen, exactly like
-- Price per day / Price half-day / Price per hour.
--
-- Run once, against the live schema (table prefix added automatically).
-- Safe to run inside a transaction; take a database backup first.
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. Rooms: add the two new price columns
-- ---------------------------------------------------------------------
ALTER TABLE `rooms`
  ADD COLUMN `price_morning_afternoon` decimal(9,2) DEFAULT NULL AFTER `price_per_hour`,
  ADD COLUMN `price_afternoon_evening` decimal(9,2) DEFAULT NULL AFTER `price_morning_afternoon`;

-- ---------------------------------------------------------------------
-- 2. Seed the new columns for existing rooms so pricing keeps working
--    exactly as before (price_half_day * 2) until an admin adjusts it.
--    Only touched for rooms that actually offer half-day booking.
-- ---------------------------------------------------------------------
UPDATE `rooms`
   SET `price_morning_afternoon` = `price_half_day` * 2,
       `price_afternoon_evening` = `price_half_day` * 2
 WHERE `book_by_halfday` = 'T'
   AND `price_half_day` IS NOT NULL;

-- ---------------------------------------------------------------------
-- 3. Translatable labels: Room admin create/edit price field labels
--    (Admin -> Kamers -> Nieuw/Bewerken, "Price per ..." field group)
-- ---------------------------------------------------------------------
INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('price_per_ARRAY_morningafternoon', 'arrays', 'price_per_ARRAY_morningafternoon', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Price morning + afternoon', 'script');

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('price_per_ARRAY_afternoonevening', 'arrays', 'price_per_ARRAY_afternoonevening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Price afternoon + evening', 'script');

-- ---------------------------------------------------------------------
-- 4. Bust the cached field index so the new labels appear immediately
--    (otherwise they stay hidden until the admin session is recycled)
-- ---------------------------------------------------------------------
UPDATE `options`
   SET `value` = MD5(CONCAT(`value`, NOW()))
 WHERE `key` = 'o_fields_index';

COMMIT;
