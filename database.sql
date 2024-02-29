DROP SCHEMA IF EXISTS BlogPHP;
CREATE DATABASE `BlogPHP` DEFAULT CHARACTER SET utf8;
USE BlogPHP;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `post`;
DROP TABLE IF EXISTS `comment`;

CREATE TABLE `user`(  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `token` VARCHAR(255) UNIQUE NULL,
    role ENUM('ROLE_USER', 'ROLE_ADMIN') NOT NULL
);

CREATE TABLE `post`(  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` SMALLINT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `chapo` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
	`creation_date` DATETIME NOT NULL,
	`update_date` DATETIME DEFAULT NULL,
	`image` VARCHAR(255) DEFAULT NULL,
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE `comment`(  
    `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` SMALLINT NOT NULL,
	`post_id` SMALLINT NOT NULL,
    `content` TEXT NOT NULL,
	`comment_date` DATETIME NOT NULL,
	`status` BOOLEAN DEFAULT false NOT NULL,
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
	FOREIGN KEY (post_id) REFERENCES post(id) ON DELETE CASCADE
);

INSERT INTO `user` (`username`, `password`, `email`, `token`, `role`) 
VALUES ('Admin', MD5('5f66a2f90e8b79adfc10d94e3923d7ab'), 'admin@nicolascastagna.com', NULL, 'ROLE_ADMIN'),
('User', MD5('5f66a2f90e8b79adfc10d94e3923d7ab'), 'user@nicolascastagna.com', NULL, 'ROLE_USER'),
('JohnDoe', MD5('51ab6c0f84d431e28f5a2f94d0b5f2d8'), 'john.doe@hotmail.com', NULL, 'ROLE_USER'),
('JeanDupont', MD5('3fbfa916d5c6c6140b87d33c8930dfae'), 'jeandupon@hotmail.com', NULL, 'ROLE_USER')
;

INSERT INTO `post` (`user_id`, `title`, `chapo`, `content`, `creation_date`, `update_date`, `image`) 
VALUES ('1', 'Mon premier faux article', 'Extrait de l\'article', 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Odit magnam sunt corrupti iusto nemo officiis aperiam tempora animi quibusdam temporibus voluptates quas reiciendis, autem accusamus itaque neque ipsa? Maiores laborum, inventore unde nulla est blanditiis culpa repellendus beatae, tempore dolorem quaerat quae, ducimus deleniti odio ab accusamus architecto nihil doloribus enim itaque magnam veniam cum nesciunt! Voluptates, fugit. Facere sequi eius error ipsam soluta perferendis eligendi corrupti nesciunt tempore, ea autem nam culpa doloremque illum placeat quidem qui quod a iure adipisci. Saepe modi voluptates possimus aliquid blanditiis incidunt, praesentium velit? Ut laboriosam beatae numquam? Earum deleniti tempore odit impedit.', '2024-01-14 9:10:22', NULL, NULL),
('1', 'Les outils indispensables du développeur', 'Extrait de l\'article', 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Odit magnam sunt corrupti iusto nemo officiis aperiam tempora animi quibusdam temporibus voluptates quas reiciendis, autem accusamus itaque neque ipsa? Maiores laborum, inventore unde nulla est blanditiis culpa repellendus beatae, tempore dolorem quaerat quae, ducimus deleniti odio ab accusamus architecto nihil doloribus enim itaque magnam veniam cum nesciunt! Voluptates, fugit. Facere sequi eius error ipsam soluta perferendis eligendi corrupti nesciunt tempore, ea autem nam culpa doloremque illum placeat quidem qui quod a iure adipisci. Saepe modi voluptates possimus aliquid blanditiis incidunt, praesentium velit? Ut laboriosam beatae numquam? Earum deleniti tempore odit impedit.', '2024-01-16 13:41:00', NULL, NULL),
('1', '10 méthodes de design UX', 'Explorez les fondements de l\'expérience utilisateur avec notre article sur les 10 Méthodes de Design UX', 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Odit magnam sunt corrupti iusto nemo officiis aperiam tempora animi quibusdam temporibus voluptates quas reiciendis, autem accusamus itaque neque ipsa? Maiores laborum, inventore unde nulla est blanditiis culpa repellendus beatae, tempore dolorem quaerat quae, ducimus deleniti odio ab accusamus architecto nihil doloribus enim itaque magnam veniam cum nesciunt! Voluptates, fugit. Facere sequi eius error ipsam soluta perferendis eligendi corrupti nesciunt tempore, ea autem nam culpa doloremque illum placeat quidem qui quod a iure adipisci. Saepe modi voluptates possimus aliquid blanditiis incidunt, praesentium velit? Ut laboriosam beatae numquam? Earum deleniti tempore odit impedit.', '2024-01-19 15:19:22', NULL, NULL)
;

INSERT INTO `comment` (`user_id`, `post_id`, `content`, `comment_date`, `status`) 
VALUES ('3', '3', 'Excellent article! Les conseils sont très utiles pour améliorer l\'expérience utilisateur.', '2024-01-19 17:34:19', false),
('1', '2', 'Très intéressant !', '2024-01-19 19:34:10', true),
('2', '2', 'Pas mal !', '2024-01-19 21:14:00', false)
;