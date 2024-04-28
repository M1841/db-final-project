CREATE SCHEMA `db_final_project`;

USE `db_final_project`;

DROP TABLE IF EXISTS `_member_of`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `teams`;

CREATE TABLE `users`
(
  `id`       VARCHAR(64)  NOT NULL
    PRIMARY KEY,
  `name`     VARCHAR(64)  NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  CONSTRAINT `unique_name`
    UNIQUE (`name`)
);

CREATE TABLE `teams`
(
  `id`   VARCHAR(64) NOT NULL
    PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL
);

CREATE TABLE `_member_of`
(
  `user_id` VARCHAR(64) NOT NULL,
  `team_id` VARCHAR(64) NOT NULL,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
      ON DELETE CASCADE,
  CONSTRAINT `fk_team_id`
    FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
      ON DELETE CASCADE
);

SELECT UUID() AS `id`