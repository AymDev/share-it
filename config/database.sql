-- Ce fichier permet de recréer la base de données utilisée pour ce TP

DROP DATABASE IF EXISTS share_it;
CREATE DATABASE share_it CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE share_it;

CREATE TABLE fichier (
    id INT(3) NOT NULL AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    nom_original VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    telechargements INT(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=INNODB;