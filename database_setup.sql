CREATE SCHEMA `db_final_project`;

USE `db_final_project`;

CREATE TABLE `Accounts`
(
    `id`       VARCHAR(63)  NOT NULL PRIMARY KEY,
    `name`     VARCHAR(63)  NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    CONSTRAINT `unique_name`
        UNIQUE (`name`)
);

