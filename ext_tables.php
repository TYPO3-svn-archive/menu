<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
/*
 * Suckerfish plug in configuration
 */
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1_suckerfish']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1_suckerfish'] = 'pi_flexform';
t3lib_extMgm::addPlugin(
	array('LLL:EXT:menu/locallang.xml:pi1_title_suckerfish', $_EXTKEY.'_pi1_suckerfish'),
	'list_type');	
t3lib_extMgm::addPiFlexFormValue(
	$_EXTKEY . '_pi1_suckerfish',
	'FILE:EXT:menu/res/suckerfish/flexform_ds.xml'
);


if (TYPO3_MODE=="BE") $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_menu_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_menu_pi1_wizicon.php';

$tempColumns = Array (
	"tx_menu_backgroundimage" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:menu/locallang_db.xml:pages.tx_menu_backgroundImage",
		"config" => Array (
			"type" => "group",
			"internal_type" => "file",
			"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],
			"max_size" => 300,
			"uploadfolder" => "uploads/tx_menu",
			"show_thumbs" => 1,
			"size" => 1,
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_menu_backgroundimageover" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:menu/locallang_db.xml:pages.tx_menu_backgroundImageOver",
		"config" => Array (
			"type" => "group",
			"internal_type" => "file",
			"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],
			"max_size" => 300,
			"uploadfolder" => "uploads/tx_menu",
			"show_thumbs" => 1,
			"size" => 1,
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
	"tx_menu_backgroundimageactive" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:menu/locallang_db.xml:pages.tx_menu_backgroundImageActive",
		"config" => Array (
			"type" => "group",
			"internal_type" => "file",
			"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],
			"max_size" => 300,
			"uploadfolder" => "uploads/tx_menu",
			"show_thumbs" => 1,
			"size" => 1,
			"minitems" => 0,
			"maxitems" => 1,
		)
	),
);


t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("pages","tx_menu_backgroundimage,tx_menu_backgroundimageover,tx_menu_backgroundimageactive;;;;1-1-1");
?>