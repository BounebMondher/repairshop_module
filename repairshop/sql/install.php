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
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'repair` (
		  `id_repair` int(10) NOT NULL AUTO_INCREMENT,
		  `id_cart` int(10) NOT NULL,
		  `id_customer` int(10) NOT NULL,
          `name` varchar(128),
          `message` TEXT,
          `device` varchar(128),
		  `date_add` DATETIME NOT NULL,
          `statut` int(2) DEFAULT 0,
		  `id_order` int(10) NULL,
		  `id_creator` int(10) NULL,
		  `id_updater` int(10) NULL,
		  `date_repaired` DATETIME NULL,
		  `date_returned` DATETIME NULL,
  		PRIMARY KEY (`id_repair`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'repair` AUTO_INCREMENT=100001';
