DROP SCHEMA IF EXISTS `db_final_project`;

CREATE SCHEMA `db_final_project`;

USE `db_final_project`;

DROP TABLE IF EXISTS `_member_of`;
DROP TABLE IF EXISTS `_involved_in`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `teams`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `users`
(
  `id`       VARCHAR(64)  NOT NULL PRIMARY KEY,
  `name`     VARCHAR(64)  NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  CONSTRAINT `unique_name`
    UNIQUE (`name`)
);

CREATE TABLE `teams`
(
  `id`          VARCHAR(64)  NOT NULL PRIMARY KEY,
  `name`        VARCHAR(64)  NOT NULL,
  `description` VARCHAR(255) NOT NULL
);

CREATE TABLE `_member_of_`
(
  `user_id` VARCHAR(64) NOT NULL,
  `team_id` VARCHAR(64) NOT NULL,

  CONSTRAINT `fk_user_member_of_`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
      ON DELETE CASCADE,

  CONSTRAINT `fk__member_of_team`
    FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
      ON DELETE CASCADE
);

CREATE TABLE `projects`
(
  `id`          VARCHAR(64)  NOT NULL PRIMARY KEY,
  `name`        VARCHAR(64)  NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `team_id`     VARCHAR(64)  NOT NULL,

  CONSTRAINT `fk_project_team`
    FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
      ON DELETE CASCADE
);

CREATE TABLE `tasks`
(
  `id`          VARCHAR(64)  NOT NULL PRIMARY KEY,
  `name`        VARCHAR(64)  NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `status`      VARCHAR(16)  NOT NULL,
  `project_id`  VARCHAR(64)  NOT NULL,

  CONSTRAINT `fk_task_project`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
      ON DELETE CASCADE
);

CREATE TABLE `_involved_in_`
(
  `user_id` VARCHAR(64) NOT NULL,
  `task_id` VARCHAR(64) NOT NULL,

  CONSTRAINT `fk_user_involved_in_`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
      ON DELETE CASCADE,

  CONSTRAINT `fk__involved_in_task`
    FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
      ON DELETE CASCADE
)