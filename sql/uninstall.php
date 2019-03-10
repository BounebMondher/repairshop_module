<?php
/**
 * Module repairshop
 *
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @category Prestashop
 * @category Module
 */

$sql = array();
$sql[] = 'SET foreign_key_checks = 0;';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'repair`;';
$sql[] = 'SET foreign_key_checks = 1;';
