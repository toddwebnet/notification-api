CREATE USER 'notification'@'%' IDENTIFIED BY 'SuperSecretDeveloper';
CREATE DATABASE IF NOT EXISTS `notification`;
GRANT ALL PRIVILEGES ON `notification`.* TO 'notification'@'%';GRANT ALL PRIVILEGES ON `notification\_%`.* TO 'notification'@'%';
flush privileges;
