START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. Rooms: add the two new combo-capability flags
-- ---------------------------------------------------------------------
ALTER TABLE `rooms`
  ADD COLUMN `book_by_morningafternoon` enum('T','F') DEFAULT NULL AFTER `book_by_hour`,
  ADD COLUMN `book_by_afternoonevening` enum('T','F') DEFAULT NULL AFTER `book_by_morningafternoon`;

-- ---------------------------------------------------------------------
-- 2. Seed the new flags so existing rooms keep working exactly as
--    before: a room only had combo prices set (by the previous
--    migration) when it already had "Per day part" enabled, so those
--    are the rooms that should keep offering each combo.
-- ---------------------------------------------------------------------
UPDATE `rooms`
   SET `book_by_morningafternoon` = IF(`book_by_halfday` = 'T' AND `price_morning_afternoon` IS NOT NULL, 'T', 'F');
UPDATE `rooms`
   SET `book_by_afternoonevening` = IF(`book_by_halfday` = 'T' AND `price_afternoon_evening` IS NOT NULL, 'T', 'F');

-- ---------------------------------------------------------------------
-- 3. Translatable labels: Room admin "Application for" checkboxes
--    (Admin -> Kamers -> Nieuw/Bewerken)
-- ---------------------------------------------------------------------
INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('book_by_ARRAY_combo', 'arrays', 'book_by_ARRAY_combo', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Per 2 consecutive time slots', 'script');

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('book_by_ARRAY_morningafternoon', 'arrays', 'book_by_ARRAY_morningafternoon', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Morning + Afternoon', 'script');

INSERT INTO `fields` (`key`, `type`, `label`, `source`, `modified`)
VALUES ('book_by_ARRAY_afternoonevening', 'arrays', 'book_by_ARRAY_afternoonevening', 'script', NOW());
SET @id := LAST_INSERT_ID();
INSERT INTO `multi_lang` (`foreign_id`, `model`, `locale`, `field`, `content`, `source`)
VALUES (@id, 'pjField', ::LOCALE::, 'title', 'Afternoon + Evening', 'script');

-- ---------------------------------------------------------------------
-- 4. Bust the cached field index so the new labels appear immediately
--    (otherwise they stay hidden until the admin session is recycled)
-- ---------------------------------------------------------------------
UPDATE `options`
   SET `value` = MD5(CONCAT(`value`, NOW()))
 WHERE `key` = 'o_fields_index';

COMMIT;
