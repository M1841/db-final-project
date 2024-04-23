CREATE SCHEMA `db_final_project`;

USE `db_final_project`;

CREATE TABLE `accounts`
(
  `id`       VARCHAR(63)  NOT NULL
    PRIMARY KEY,
  `name`     VARCHAR(63)  NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  CONSTRAINT `unique_name`
    UNIQUE (`name`)
);

CREATE TABLE `teams`
(
  `id`   VARCHAR(64) NOT NULL
    PRIMARY KEY,
  `name` VARCHAR(64) NULL
);

CREATE TABLE `_member_of`
(
  `account_id` VARCHAR(64) NOT NULL,
  `team_id`    VARCHAR(64) NOT NULL,
  CONSTRAINT `fk_accounts_id`
    FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`)
      ON DELETE CASCADE,
  CONSTRAINT `fk_team_id`
    FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
      ON DELETE CASCADE
);