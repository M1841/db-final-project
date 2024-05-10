INSERT INTO `users` (`id`, `name`, `password`)
VALUES (UUID(), 'Mihai', '12345678'),
       (UUID(), 'User1', '12345678'),
       (UUID(), 'User2', '12345678'),
       (UUID(), 'User3', '12345678'),
       (UUID(), 'User4', '12345678'),
       (UUID(), 'User5', '12345678'),
       (UUID(), 'User6', '12345678'),
       (UUID(), 'User7', '12345678'),
       (UUID(), 'User8', '12345678'),
       (UUID(), 'User9', '12345678');

SELECT `id`, `name`
FROM `users`;

INSERT INTO `teams` (`id`, `name`, `description`)
VALUES (UUID(), 'Team1', 'Description'),
       (UUID(), 'Team2', 'Description'),
       (UUID(), 'Team3', 'Description');


SELECT `id`, `name`
FROM `teams`;

INSERT INTO `_member_of_` (`user_id`, `team_id`)
VALUES ('15b8f283-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b8f283-0e97-11ef-a773-00155d1c0d1c',
        '6d667a6f-0e97-11ef-a773-00155d1c0d1c'),
       ('15b8f283-0e97-11ef-a773-00155d1c0d1c',
        '6d667bdc-0e97-11ef-a773-00155d1c0d1c'),

       ('15b90298-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b9041c-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b904db-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b90597-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b90674-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b907b8-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),
       ('15b90878-0e97-11ef-a773-00155d1c0d1c',
        '6d6672e6-0e97-11ef-a773-00155d1c0d1c'),

       ('15b90939-0e97-11ef-a773-00155d1c0d1c',
        '6d667a6f-0e97-11ef-a773-00155d1c0d1c'),
       ('15b909e9-0e97-11ef-a773-00155d1c0d1c',
        '6d667a6f-0e97-11ef-a773-00155d1c0d1c');

SELECT `teams`.`id`, `teams`.`name`
FROM `teams`
       JOIN `_member_of_`
            ON `_member_of_`.`team_id` = `teams`.`id`
WHERE `_member_of_`.`user_id` = '15b8f283-0e97-11ef-a773-00155d1c0d1c';