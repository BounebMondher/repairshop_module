<?php
/**
 *
 * Created by PhpStorm.
 * User: Black Joker
 * Date: 11/11/2018
 * Time: 9:15 AM
 *
 */
$sql = array();
$sql[] = 'SET foreign_key_checks = 0;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'repair`;';
$sql[] = 'SET foreign_key_checks = 1;';
