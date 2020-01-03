CREATE DATABASE IF NOT EXISTS phalcon CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
create table test
(
    id int auto_increment,
    name varchar(255) null,
    constraint test_pk
        primary key (id)
);
