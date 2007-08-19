<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_menu_pi1 = < plugin.tx_menu_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'res/suckerfish/class.tx_menu_pi1_suckerfish.php','_pi1_suckerfish','list_type',1);
?>