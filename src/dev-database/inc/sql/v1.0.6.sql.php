<?php
$hroseVersion = '1.0.6';

$sql[] = 'CREATE TABLE fish(`fish_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`fish_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE lake(`lake_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`lake_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE student(`student_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`student_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE teacher(`teacher_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`teacher_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE class(`class_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`class_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE parent(`parent_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`parent_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE missing(`missing_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`missing_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE antrag(`antrag_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`antrag_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE preise(`preise_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`preise_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE wiese(`wiese_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`wiese_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE klasse(`klasse_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`klasse_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE schrank(`schrank_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`schrank_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE pdf(`pdf_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`pdf_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE fischtopf(`fischtopf_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`fischtopf_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE company(`company_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`company_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE contact(`contact_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`contact_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE inbox(`inbox_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`inbox_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE function(`function_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`function_id`)) ENGINE = InnoDB';
$sql[] = 'CREATE TABLE arztrechnung(`arztrechnung_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , PRIMARY KEY (`arztrechnung_id`)) ENGINE = InnoDB';
