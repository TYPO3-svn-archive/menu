<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Philip Almeida <philip.almeida@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_pagetree.php');

class tx_menu_pi1_suckerfish extends tslib_pibase{ 
	
	var $prefixId      = 'tx_menu';		// Same as class name
	var $scriptRelPath = 'res/suckerfish/class.tx_menu_pi1_suckerfish.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'menu';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	 
	function main($content,$conf)	{

		global $LANG;	
		$this->conf=$conf;
		$this->pi_initPIflexForm();	// Init FlexForm configuration for plugin
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=0;	// Configuring so caching is expected. 
	
		/* 
		 * GLOBAL variables
		*/
		$GLOBALS['txMenuPiFlexForm']  = $this->cObj->data['pi_flexform'];

		$menuInstance = t3lib_div::makeinstance('tx_menu_pi1_suckerfish');

		return $this->pi_wrapInBaseClass($menuInstance->buildMenu());
	}


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	
	function buildMenu(){
		$this->cObj = t3lib_div::makeInstance("tslib_cObj");
		$this->pageSelect = t3lib_div::makeInstance("t3lib_pageSelect");	
		$this->pageId = $GLOBALS['TSFE']->id;
		$this->record =  '_'.substr($GLOBALS['TSFE']->currentRecord,strpos($GLOBALS['TSFE']->currentRecord,':')+1,strlen($GLOBALS['TSFE']->currentRecord)).'_';
		
 		/*
		* Get Global variables
		*/
		$piFlexForm = $GLOBALS['txMenuPiFlexForm'];
		foreach ($piFlexForm['data'] as $sheet => $data )
			foreach ( $data as $lang => $value )
   				foreach ( $value as $key => $val )
    				$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);

		// initialize funtions
		$outPut .= $this->setVars();	
		$outPut .= $this->loadStylesheet();
		$outPut .= $this->loadJavascript();
		$outPutStart= $this->startDisplay();
		$outPutList = $this->buildList();
		if($this->preload)
			$outPutPreload = '<script type="text/javascript">'."\n".$this->preload."\n".'</script>'."\n";
		$outPutEnd = $this->endDisplay();
		return $outPut.$outPutPreload.$outPutStart.$outPutList.$outPutEnd;
	}
 	/**
	 * The setVars method of the PlugIn
	 *
	 * @return	void
	 */ 
	function setVars(){
		/*
		 *  SET DEFAULT CONFIGURATION VARS
		 * 
		 */
		/* Javascript only included in first rendered menu */
		$this->jsCounter = $GLOBALS['jsSuckerFishCounter']['counter']+=1;	
		/* set path and pathLib*/
		$this->pathLib = 'typo3conf/ext/menu/res/common/lib/';
		$this->path = substr(t3lib_extMgm::extRelPath('menu'),3).'res/suckerfish/';
		$this->uploadFolder = 'uploads/tx_menu/';
		
		/* 
		 * FIRST LEVEL CONFIGURATION VARS
		 * 
		 * 
		 */
		 /* Image marker 1º level type  */
		if($this->lConf['imageMarkerSelection1']=='') $this->lConf['imageMarkerSelection1'] = 1;
		if($this->lConf['paddingTopImageMarker1']=='') $this->lConf['paddingTopImageMarker1'] = 10;
		if($this->lConf['paddingRightImageMarker1']=='') $this->lConf['paddingRightImageMarker1'] = 5;
		if($this->lConf['rightPointer1Width']=='') $this->lConf['rightPointer1Width'] = 15;
		if($this->lConf['rightPointer1Height']=='') $this->lConf['rightPointer1Height'] = 15;
		if($this->lConf['downPointer1Width']=='') $this->lConf['downPointer1Width'] = 15;
		if($this->lConf['downPointer1Height']=='') $this->lConf['downPointer1Height'] = 15;
		switch ($this->lConf['imageMarkerSelection1']){
			case '1': /* default image assign*/
				$this->pointerRightMain = 'typo3conf/ext/menu/res/suckerfish/images/arrow-right_white.png';
				$this->pointerDownMain = 'typo3conf/ext/menu/res/suckerfish/images/arrow-down_white.png';
			break;
			case '2': /* custom image assign */
				$this->pointerRightMain = $this->uploadFolder.$this->lConf['rightPointer1'];
				$this->pointerDownMain = $this->uploadFolder.$this->lConf['downPointer1'];
			break;
		}
		/* Set main render type to text*/
		if($this->lConf['renderTypeMain']=='') $this->lConf['renderTypeMain'] = 1;
		if($this->lConf['paddingMainImage']=='') $this->lConf['paddingMainImage'] = 3;
		if($this->lConf['angleMainImage']=='') $this->lConf['angleMainImage'] = 0;
		/* Set default Main level dimensions to width 160px height 30px*/	
		if($this->lConf['menuWidthMain']=='') $this->lConf['menuWidthMain'] = 160;
		if($this->lConf['menuHeightMain']=='') $this->lConf['menuHeightMain'] = 30;
		/* Main level background colors */
		if($this->lConf['menuColorMain']=='') $this->lConf['menuColorMain'] = '#666666';
		if($this->lConf['menuColorMainOver']=='') $this->lConf['menuColorMainOver'] = '#999999';
		if($this->lConf['menuColorMainActive']=='') $this->lConf['menuColorMainActive'] = '#FFFFFF';
		/* Image background definitions*/
		if($this->lConf['backgroundRenderTypeMain']=='') $this->lConf['backgroundRenderTypeMain'] = 1;
		if($this->lConf['backgroundRenderTypeMain']==2){
			if($this->lConf['backgroundImageMain']=='') 
				$this->lConf['backgroundImageMain'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImageMain'] = $this->uploadFolder.$this->lConf['backgroundImageMain'];
			if($this->lConf['backgroundImageMainOver']=='') 
				$this->lConf['backgroundImageMainOver'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImageMainOver'] = $this->uploadFolder.$this->lConf['backgroundImageMainOver'];
			if($this->lConf['backgroundImageMainActive']=='') 
				$this->lConf['backgroundImageMainActive'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImageMainActive'] = $this->uploadFolder.$this->lConf['backgroundImageMainActive'];
		}
		/* Main level font colors and font definition */
		if($this->lConf['fontColorMain']=='') $this->lConf['fontColorMain']		='#FFFFFF';
		if($this->lConf['fontColorMainOver']=='') $this->lConf['fontColorMainOver']	='#F0F0F0';
		if($this->lConf['fontColorMainActive']=='') $this->lConf['fontColorMainActive']	='#333333';
		if($this->lConf['fontSizeMain']=='') $this->lConf['fontSizeMain']		=14;
		/* Set default margin only applies to main level items */
		if($this->lConf['marginLeft']=='') $this->lConf['marginLeft'] = 0;
		if($this->lConf['marginRight']=='') $this->lConf['marginRight'] = 0;
		if($this->lConf['marginBottom']=='') $this->lConf['marginBottom'] = 0;
		if($this->lConf['marginTop']=='') $this->lConf['marginTop'] = 0; 
		/* Set default padding for main level item */
		if($this->lConf['paddingLeft']=='') $this->lConf['paddingLeft'] = 10;
		if($this->lConf['paddingRight']=='') $this->lConf['paddingRight'] = 0;
		if($this->lConf['paddingBottom']=='') $this->lConf['paddingBottom'] = 0;
		if($this->lConf['paddingTop']=='') $this->lConf['paddingTop'] = 10; 
		
		/* 
		 * ALL LEVEL CONFIGURATION VARS
		 * 
		 * 
		 */
		/* Image marker normal levels */
		if($this->lConf['imageMarkerSelection']=='') $this->lConf['imageMarkerSelection'] = 1;
		if($this->lConf['paddingTopImageMarker']=='') $this->lConf['paddingTopImageMarker'] = 5;
		if($this->lConf['paddingRightImageMarker']=='') $this->lConf['paddingRightImageMarker'] = 5;
		if($this->lConf['rightPointerWidth']=='') $this->lConf['rightPointerWidth'] = 15;
		if($this->lConf['rightPointerHeight']=='') $this->lConf['rightPointerHeight'] = 15;
		switch ($this->lConf['imageMarkerSelection']){
			case '1': /* default image assign*/
				$this->pointerRight = 'typo3conf/ext/menu/res/suckerfish/images/arrow-right.gif';
			break;
			case '2': /* custom image assign */
				$this->pointerRight = $this->uploadFolder.$this->lConf['rightPointer'];
			break;
		}
		/* Set default render type to text*/
		if($this->lConf['renderType']=='') $this->lConf['renderType'] = 1;
		if($this->lConf['paddingImage']=='') $this->lConf['paddingImage'] = 3;
		if($this->lConf['angleImage']=='') $this->lConf['angleImage'] = 0;
		/* Set default All level dimensions to width 160px height 20px*/	
		if($this->lConf['menuWidth']=='') $this->lConf['menuWidth'] = 160;
		if($this->lConf['menuHeight']=='') $this->lConf['menuHeight'] = 20;
		/* All level background colors */
		if($this->lConf['menuColor']=='') $this->lConf['menuColor'] = '#F0F0F0';
		if($this->lConf['menuColorOver']=='') $this->lConf['menuColorOver'] = '#CCCCCC';
		if($this->lConf['menuColorActive']=='') $this->lConf['menuColorActive'] = '#666666';
		/* Image background definitions*/
		if($this->lConf['backgroundRenderType']=='') $this->lConf['backgroundRenderType'] = 1;
		if($this->lConf['backgroundRenderType']=='2'){
			if($this->lConf['backgroundImage']=='') 
				$this->lConf['backgroundImage'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImage'] = $this->uploadFolder.$this->lConf['backgroundImage'];
			if($this->lConf['backgroundImageOver']=='') 
				$this->lConf['backgroundImageOver'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImageOver'] = $this->uploadFolder.$this->lConf['backgroundImageOver'];
			if($this->lConf['backgroundImageActive']=='') 
				$this->lConf['backgroundImageActive'] = 'typo3conf/ext/menu/res/common/img/transparent.png';
			else
				$this->lConf['backgroundImageActive'] = $this->uploadFolder.$this->lConf['backgroundImageActive'];
		}
		/* Main level font colors and font definition*/
		if($this->lConf['fontSize']=='')			$this->lConf['fontSize']			=10;
		if($this->lConf['fontColor']=='')			$this->lConf['fontColor']			='#000000';
		if($this->lConf['fontColorOver']=='')		$this->lConf['fontColorOver']		='#333333';
		if($this->lConf['fontColorActive']=='')		$this->lConf['fontColorActive']		='#FFFFFF';
		/* Set default margin only applies to main level items */
		if($this->lConf['marginLeftNormal']=='') $this->lConf['marginLeftNormal'] = 0;
		if($this->lConf['marginBottomNormal']=='') $this->lConf['marginBottomNormal'] = 0;
		if($this->lConf['marginTopNormal']=='') $this->lConf['marginTopNormal'] = 0;
		if($this->lConf['marginTopNormal']=='') $this->lConf['marginTopNormal'] = 0; 
		// Set default normal padding
		if($this->lConf['paddingLeftNormal']=='') $this->lConf['paddingLeftNormal'] = 10;
		if($this->lConf['paddingRightNormal']=='') $this->lConf['paddingRightNormal'] = 0;
		if($this->lConf['paddingBottomNormal']=='') $this->lConf['paddingBottomNormal'] = 0;
		if($this->lConf['paddingTopNormal']=='') $this->lConf['paddingTopNormal'] = 5;
		
		/*
		 * BORDER CONFIGURATION VARS
		 * 
		 */
		if($this->lConf['borderWidth']=='') $this->lConf['borderWidth'] = 1;
		if($this->lConf['borderType']=='')			$this->lConf['borderType']			='dashed';
		if($this->lConf['borderColor']=='')			$this->lConf['borderColor']			='#CCCCCC';

		// Set default font
		if($this->lConf['fontFileMain']=='') 
			$this->lConf['fontFileMain'] = PATH_typo3conf."ext/menu/res/common/fonts/Verdana_Bold.ttf";

		else
			$this->lConf['fontFileMain'] = PATH_typo3."../uploads/tx_menu/".$this->lConf['fontFileMain'];
		// Set default font
		if($this->lConf['fontFile']=='') 
			$this->lConf['fontFile'] = PATH_typo3conf."ext/menu/res/common/fonts/Verdana_Bold.ttf";

		else
			$this->lConf['fontFile'] = PATH_typo3."../uploads/tx_menu/".$this->lConf['fontFile'];
		/*
		 *  SET MAIN CONFIGURATION VARS
		 * 
		 */
		/* Set to horizontal if null */
		if($this->lConf['menuDirection']=='' || $this->lConf['menuDirection']==1){
			$this->lConf['menuDirection'] = 1; 
			$this->cssDirection = 'horizontal';		
		}elseif($this->lConf['menuDirection']==2){
			$this->cssDirection = 'vertical';	
		}

		/*
		 *  SET CSS CONFIGURATION VARS
		 * 
		 */
		/* Set defaultClassName */
		if($this->lConf['cssClassName']=='') $this->lConf['cssClassName'] = 'defaultMenuClassName_';
		/* Check if is first menu based on default stylesheet for direction */
		$this->cssCounter = $GLOBALS['cssRepeatSuckerfish'][($this->lConf['menuDirection'])]+=1;

		
		return;
		
	}		

	/**
	 * The loadStylesheet method of the PlugIn
	 *
	 * @return	loadStylesheet
	 */ 
	function loadStylesheet(){
			// include user stylesheet for menutype
		if ($this->lConf['cssFile']){
			$cssPath .= "\n".'<link href="uploads/tx_menu/'.$this->lConf['cssFile'].'" rel=STYLESHEET type="text/css">'."\n";
		}else{
			if($this->cssCounter==1){
					$cssPath .= "\n".'<link href="'.$this->path.'css/suckerfish'.$this->cssDirection.'.css" rel=STYLESHEET type="text/css">'."\n";
			}
									
		}

		return $cssPath;
	}	

	/**
	 * The loadJavascript method of the PlugIn
	 *
	 * @return	loadJavascript
	 */ 
	function loadJavascript(){
		if ($this->jsCounter==1){
			$jsPath .= '<script type="text/javascript" src="'.$this->path.'js/suckerfish.js"></script>'."\n";
		}
		return $jsPath;
	}
		
	/**
	 * The startDisplay method of the PlugIn
	 *
	 * @return	StartDisplay
	 */ 
	function startDisplay(){
		// Preload image markers first level
		switch($this->lConf['imageMarkerSelection1']){
			case '3':
			break;
			default: /* Default image loading*/
				$outPut .='<script type="text/javascript">'."\n";
				$outPut .='<!-- hide from none JavaScript Browsers'."\n";
				$outPut .=$this->record.'_pointerRightMain= new Image('.$this->lConf['rightPointer1Width'].','.$this->lConf['rightPointer1Height'].')'."\n";
				$outPut .=$this->record.'_pointerRightMain.src = "'.$this->pointerRightMain.'"'."\n";
				$outPut .=$this->record.'_pointerDownMain= new Image('.$this->lConf['downPointer1Width'].','.$this->lConf['downPointer1Height'].')'."\n";
				$outPut .=$this->record.'_pointerDownMain.src = "'.$this->pointerDownMain.'"'."\n";
				$outPut .='// End Hiding -->'."\n";
				$outPut .='</script>'."\n";
			break;
		}
		// Preload image markers all levels
		switch($this->lConf['imageMarkerSelection']){
			case '3':
			break;
			default: /* Default image loading*/
				$outPut .='<script type="text/javascript">'."\n";
				$outPut .='<!-- hide from none JavaScript Browsers'."\n";
				$outPut .=$this->record.'_pointerRight= new Image('.$this->lConf['rightPointerWidth'].','.$this->lConf['rightPointerHeight'].')'."\n";
				$outPut .=$this->record.'_pointerRight.src = "'.$this->pointerRight.'"'."\n";
				$outPut .='// End Hiding -->'."\n";
				$outPut .='</script>'."\n";
			break;
		}

		$outPut.="\n".'<script type="text/javascript">';
		/* MOUSE OVER ME MAIN*/
		$outPut.="\n".'function '.$this->record.'mouseOverMeMain(obj){';
		/* text */
		if($this->lConf['renderTypeMain']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorMainOver'].'"';
		}
		// Display menu as image then discard format for text menu
		if($this->lConf['backgroundRenderTypeMain']==1){
			$outPut.="\n".'obj.style.backgroundColor = "'.$this->lConf['menuColorMainOver'].'"';
		}else{
			$outPut .="\n".'obj.style.backgroundImage="url('.$this->lConf['backgroundImageMainOver'].')"';
		} 
		$outPut.="\n".'}';

		/* MOUSE OUT ME MAIN*/
		$outPut.="\n".'function '.$this->record.'mouseOutMeMain(obj){';
		/* text */
		if($this->lConf['renderTypeMain']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorMain'].'"';
		}
		/* text & color backgr*/
		if($this->lConf['backgroundRenderTypeMain']==1){
			$outPut.="\n".'obj.style.backgroundColor = "'.$this->lConf['menuColorMain'].'"';
		}else{
			$outPut .="\n".'obj.style.backgroundImage="url('.$this->lConf['backgroundImageMain'].')"';
		} 
		$outPut.="\n".'}';

		/* MOUSE OVER ME*/
		$outPut.="\n".'function '.$this->record.'mouseOverMe(obj){';
		/* text */
		if($this->lConf['renderType']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorOver'].'"';
		}
		/* text & color backgr*/
		if($this->lConf['backgroundRenderType']==1){
			$outPut.="\n".'obj.style.backgroundColor = "'.$this->lConf['menuColorOver'].'"';
		}else{
			$outPut .="\n".'obj.style.backgroundImage="url('.$this->lConf['backgroundImageOver'].')"';
		}
		$outPut.="\n".'}';

		/* MOUSE OUT ME*/
		$outPut.="\n".'function '.$this->record.'mouseOutMe(obj){';
		/* text */
		if($this->lConf['renderType']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColor'].'"';
		}
		/* text & color backgr*/
		if($this->lConf['backgroundRenderType']==1){
			$outPut.="\n".'obj.style.backgroundColor = "'.$this->lConf['menuColor'].'"';
		}
		/* text & img backgr*/
		if($this->lConf['backgroundRenderType']==2){
			$outPut .="\n".'obj.style.backgroundImage="url('.$this->lConf['backgroundImage'].')"';
		}
		$outPut.="\n".'}';

		/* MOUSE OUT ME SPECIAL MAIN*/
		$outPut.="\n".'function special_'.$this->record.'mouseOutMeMain(obj,url){';
		/* text */
		if($this->lConf['renderTypeMain']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorMain'].'"';
		}
		/* text & img backgr*/
		if($this->lConf['backgroundRenderTypeMain']==2){
			$outPut .="\n".'obj.style.backgroundImage="url("+url+")"';
		}
		$outPut.="\n".'}';
		
		/* MOUSE OVER SPECIAL MAIN*/
		$outPut.="\n".'function special_'.$this->record.'mouseOverMeMain(obj,url){';
		/* text */
		if($this->lConf['renderTypeMain']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorMainOver'].'"';
		}
		/* text & img backgr*/
		if($this->lConf['backgroundRenderTypeMain']==2){
			$outPut .="\n".'obj.style.backgroundImage="url("+url+")"';
		}
		$outPut.="\n".'}';

		/* MOUSE OUT ME SPECIAL NORMAL*/
		$outPut.="\n".'function special_'.$this->record.'mouseOutMe(obj,url){';
		/* text */
		if($this->lConf['renderType']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColor'].'"';
		}
		/* text & img backgr*/
		if($this->lConf['backgroundRenderType']==2){
			$outPut .="\n".'obj.style.backgroundImage="url("+url+")"';
		}
		$outPut.="\n".'}';
		
		/* MOUSE OVER SPECIAL NORMAL*/
		$outPut.="\n".'function special_'.$this->record.'mouseOverMe(obj,url){';
		/* text */
		if($this->lConf['renderType']==1){
			$outPut.="\n".'obj.style.color = "'.$this->lConf['fontColorOver'].'"';
		}
		/* text & img backgr*/
		if($this->lConf['backgroundRenderType']==2){
			$outPut .="\n".'obj.style.backgroundImage="url("+url+")"';
		}
		$outPut.="\n".'}';

		$outPut.="\n".'</script>'."\n";

		$outPut .= '<div class="container_'.$this->lConf['cssClassName'].$this->cssDirection.'">'."\n";			
		$outPut .= '<ul class="'.$this->lConf['cssClassName'].$this->cssDirection.'" id="'.$this->record.'">'."\n";

		return $outPut;
	}	
		
	/**
	 * The buildList method of the PlugIn
	 *
	 * @param	int		$startingPoint: Tree start level
	 * @param	int		$level: Level for code render beauty
	 * @return	Final HTML ordered list block
	 */ 
	function buildList($startingPoint=0,$level=0,$counter=0,$accCounter=0){
		if($this->lConf['level']==1 && $level==1) return;
		// First entry?
		($startingPoint>0?$startingPoint:$startingPoint=$this->lConf['startingNode']);
		// Build tree
		$depth = 1;		
		$where = $this->cObj->enableFields('pages');
		$tree = t3lib_div::makeinstance('t3lib_pageTree');
		$tree->init('AND doktype IN (1,2,3,4,199)'.$where);
		$tree->getTree($startingPoint, $depth, '');

		$carriageRepeat = false;
		$accCounter='1';
		if($level>0){
			$accCounter=$accCounter.'+1';
			$carriageRepeat = str_repeat("\t",$level);
		}

		// Build Tree
		foreach($tree->tree as $data){

			// Accessible Menus
			$accCounterRender = '<dfn style="visibility:hidden;position:absolute;">'.str_replace("+",".",$accCounter).': </dfn>';
			$accFineTuning = '<span class="hidden" style="visibility:hidden;position:absolute;">. </span>';
			// Link item
			$url = $this->pi_linkTP_keepPIvars_url($overrulePIvars=array(),$cache=1,$clearAnyway=0,$data['row']['uid']);
			$uid = $data['row']['uid'];
			
			// Language page overlay
			$language = t3lib_div::_GP('L');
			$newdata = $this->pageSelect->getPageOverlay($data['row'],$language);

			$title =$newdata['title'];
			$titleText = $title;


					
			/* Setting active state for main level and others and mouse over and out functionality*/			
			$identifier = 'id="'.$this->record.'_'.$uid.'"';
			$onMouseOver = 'onmouseover="'.$this->record.'mouseOverMe(this);"';
			$onMouseOut = 'onmouseout="'.$this->record.'mouseOutMe(this);"';
			if($level==0){ 
				$onMouseOver = 'onmouseover="'.$this->record.'mouseOverMeMain(this);"';
				$onMouseOut = 'onmouseout="'.$this->record.'mouseOutMeMain(this);"';
				$identifier = 'id="main_'.$this->record.'_'.$uid.'"';
			}
			

			/* Verify if there is a image configuration on a item basis*/
			if($this->lConf['backgroundRenderTypeMain']==2 || $this->lConf['backgroundRenderType']==2){
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_menu_backgroundimage,tx_menu_backgroundimageover,tx_menu_backgroundimageactive','pages','uid = '.intval($uid));
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if($row['tx_menu_backgroundimage']!=''){
						if($level==0 && $this->lConf['backgroundRenderTypeMain']==2){
							$this->_backgroundImageMain[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimage'];
							$onMouseOut = 'onmouseout="special_'.$this->record.'mouseOutMeMain(this,\''.$this->uploadFolder.$row['tx_menu_backgroundimage'].'\');"';
						}elseif($level!=0 && $this->lConf['backgroundRenderType']==2){
							$this->_backgroundImage[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimage'];
							$onMouseOut = 'onmouseout="special_'.$this->record.'mouseOutMe(this,\''.$this->uploadFolder.$row['tx_menu_backgroundimage'].'\');"';
						}
					}
					if($row['tx_menu_backgroundimageover']!=''){
						if($level==0 && $this->lConf['backgroundRenderTypeMain']==2){
							$this->_backgroundImageMainOver[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimageover'];
							$onMouseOver = 'onmouseover="special_'.$this->record.'mouseOverMeMain(this,\''.$this->uploadFolder.$row['tx_menu_backgroundimageover'].'\');"';
						}elseif($level!=0 && $this->lConf['backgroundRenderType']==2){
							$this->_backgroundImageOver[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimageover'];
							$onMouseOver = 'onmouseover="special_'.$this->record.'mouseOverMe(this,\''.$this->uploadFolder.$row['tx_menu_backgroundimageover'].'\');"';
						}
					}
					if($row['tx_menu_backgroundimageactive']!=''){
						if($level==0 && $this->lConf['backgroundRenderTypeMain']==2){
							$this->_backgroundImageMainActive[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimageactive'];
						}elseif($level!=0 && $this->lConf['backgroundRenderType']==2){
							$this->_backgroundImageActive[$uid] = $this->uploadFolder.$row['tx_menu_backgroundimageactive'];
						}
					}
				}
			}
			// Active link state
			$active = 'active="0"';
			foreach($GLOBALS['TSFE']->rootLine as $value){
				if($value[uid]==$uid){
					$active = 'active="1"';
					unset($onMouseOver);
					unset($onMouseOut);
				}
			}
			if($level==0){ 
				// Display menu as image?
				if($this->lConf['renderTypeMain']==2){
					$src=$this->buildImg($newdata['title'],$level,'',$uid,true);
					$this->buildImg($newdata['title'],$level,'Over',$uid,true);
					$title='<img id="'.$uid.'" src="'.$src.'"  onmouseover="this.src=_'.$this->record.$uid.'_Over.src" onmouseout="this.src=_'.$this->record.$uid.'_.src" alt="'.$newdata['title'].'">';
					// Active state
					foreach($GLOBALS['TSFE']->rootLine as $value){
						if($value[uid]==$uid){
							$src=$this->buildImg($newdata['title'],$level,'Active',$value,false);
							$title='<img id="'.$value.'" src="'.$src.'" alt="'.$newdata['title'].'">';
						}
					}
				}
			}else{
				// Display menu as image?
				if($this->lConf['renderType']==2){
					$src=$this->buildImg($newdata['title'],$level,'',$uid,true);
					$this->buildImg($newdata['title'],$level,'Over',$uid,true);
					$title='<img id="'.$uid.'" src="'.$src.'" onmouseover="this.src=_'.$this->record.$uid.'_Over.src" onmouseout="this.src=_'.$this->record.$uid.'_.src" alt="'.$newdata['title'].'">';
					// Active state
					foreach($GLOBALS['TSFE']->rootLine as $value){
						if($value[uid]==$uid){
							$src = $this->buildImg($newdata['title'],$level,'Active',$value,false);
							$title='<img id="'.$value.'" src="'.$src.'" alt="'.$newdata['title'].'">';
						}
					}
				}

			}
			
			if ($tree->getTree($uid, $depth, '')>0){
				/* Define image marker array*/
				if($level==0){
					$this->mainImageMarker[]=$uid;
					// Setting image marker
					$markerPaddingTop = $this->lConf['paddingTopImageMarker1'];
					$markerPaddingRight = $this->lConf['paddingRightImageMarker1'];
				}else{
					$this->normalImageMarker[]=$uid;
					// Setting image marker
					$markerPaddingTop = $this->lConf['paddingTopImageMarker'];
					$markerPaddingRight = $this->lConf['paddingRightImageMarker'];
				}
				$imageMarker = '<div id="'.$this->record.'_imageMarkerContainer_'.$uid.'" style="display:none;top:'.$markerPaddingTop.'px;right:'.$markerPaddingRight.'px;float:right;position:absolute;"><img border="0" src="typo3conf/ext/menu/res/common/img/transparent.png" name="'.$this->record.'_imageMarker_'.$uid.'"></div>';

				$outPut.= $carriageRepeat.'<li level="'.$level.$this->record.'" class="parent'.$this->record.'"><a '.$active.' level="'.$level.'" '.$onMouseOver.' '.$onMouseOut.' href="'.$url.'" title="'.$titleText.'" '.$identifier.'>'.$accCounterRender.$title.$imageMarker.'</a>'.$accFineTuning."\n";	
				$outPut.= $carriageRepeat.'<ul id="ul_'.$this->record.$uid.'">'."\n";
				$outPut.= $carriageRepeat.$this->buildList($uid,++$level,$counter,$accCounter);
				$outPut.= $carriageRepeat.'</ul>'."\n";
				--$level;
			}else{
				$outPut.= $carriageRepeat.'<li level="'.$level.$this->record.'" ><a '.$active.' level="'.$level.'" '.$onMouseOver.' '.$onMouseOut.' href="'.$url.'" title="'.$titleText.'" '.$identifier.'>'.$accCounterRender.$title.'</a>'.$accFineTuning.'</li>'."\n";	
			}
		++$counter; 
		++$accCounter;				
		}
		

		return $outPut;
	}

	/**
	 * The buildImg method of the PlugIn
	 *
	 * @return	endDisplay
	 */ 
	function buildImg($text,$level,$state,$uid,$isPreload){

		$fontSize = $this->lConf['fontSize'];
		$fontColor = substr($this->lConf['fontColor'.$state],1,strlen($this->lConf['fontColor'.$state]));
		$height = $this->lConf['menuHeight'];
		$width = $this->lConf['menuWidth'];
		$font = $this->lConf['fontFile'];
		$padding = $this->lConf['paddingImage'];
		$angle = $this->lConf['angleImage'];
		if($level==0){
			$fontColor = substr($this->lConf['fontColorMain'.$state],1,strlen($this->lConf['fontColorMain'.$state]));
			$font = $this->lConf['fontFileMain'];
			$fontSize = $this->lConf['fontSizeMain'];
			$height = $this->lConf['menuHeightMain'];
			$width = $this->lConf['menuWidthMain'];
			$padding = $this->lConf['paddingMainImage'];
			$angle = $this->lConf['angleMainImage'];
		}
		$imgSrc = 'new=FFFFFF|0';
		$out = $this->pathLib.'phpThumb/phpThumb.php?'
		.$imgSrc
		.'&f=png'
		.'&w='.$width
		.'&h='.$height
		.'&fltr[]=wmt|'.$text.'|'.$fontSize.'|L|'.$fontColor.'|'.$font.'|100|'.$padding.'|'.$angle;
		if($isPreload){
			$this->preload.='_'.$this->record.$uid.'_'.$state.'= new Image('.$width.','.$height.');'."\n";
			$this->preload.='_'.$this->record.$uid.'_'.$state.'.src = "'.$out.'";'."\n";
		}
		return	$out;
	}	

	
	/**
	 * The endDisplay method of the PlugIn
	 *
	 * @return	endDisplay
	 */ 
	function endDisplay(){
		$outPut .= '</ul>'."\n";
		$outPut .= '</div>'."\n";	
		$boderDefinition = '"'.$this->lConf['borderWidth'].'px '.$this->lConf['borderType'].' '.$this->lConf['borderColor'].'"';
		// Preload image markers first level
		switch($this->lConf['imageMarkerSelection1']){
			case '3':
			break;
			default: /* Default image loading*/
				$outPut .='<script type="text/javascript">'."\n";
				foreach($this->mainImageMarker as $key){
					// Horizontal menu
					if($this->lConf['menuDirection']==1)
						$outPut .='document.'.$this->record.'_imageMarker_'.$key.'.src='.$this->record.'_pointerDownMain.src'."\n";
					else
						$outPut .='document.'.$this->record.'_imageMarker_'.$key.'.src='.$this->record.'_pointerRightMain.src'."\n";
					$outPut .='document.getElementById(\''.$this->record.'_imageMarkerContainer_'.$key.'\').style.display="block"'."\n";
				}
				$outPut .='</script>'."\n";
			break;
		}
		// Preload image markers all levels
		switch($this->lConf['imageMarkerSelection']){
			case '3':
			break;
			default: /* Default image loading*/
				$outPut .='<script type="text/javascript">'."\n";
				foreach($this->normalImageMarker as $key){
					$outPut .='document.'.$this->record.'_imageMarker_'.$key.'.src='.$this->record.'_pointerRight.src'."\n";
					$outPut .='document.getElementById(\''.$this->record.'_imageMarkerContainer_'.$key.'\').style.display="block"'."\n";
				}
				$outPut .='</script>'."\n";
			break;
		}

/* 
 * 
 * 
 * 
 * HORIZONTAL MENU 
 * 
 * 
 * 
 * */
		if($this->lConf['menuDirection']==1){
			$outPut .="\n".'<script type="text/javascript">
//SuckerTree Horizontal Menu (Sept 14th, 06)
//By Dynamic Drive: http://www.dynamicdrive.com/style/
var menuids=["'.$this->record.'"] //Enter id(s) of SuckerTree UL menus, separated by commas
windowWidth = document.body.clientWidth;
windowPosition = 0;
function '.$this->record.'(){
var levelCounter = 1
var counterGroundLevel = 1
for (var i=0; i<menuids.length; i++){
	var litags=document.getElementById(menuids[i]).getElementsByTagName("li")
	for (var t=0; t<litags.length; t++){
		litags[t].getElementsByTagName("a")[0].style.borderTop='.$boderDefinition.' // dynamically border
		litags[t].getElementsByTagName("a")[0].style.height="'.$this->lConf['menuHeight'].'px" // dynamically height link
		litags[t].getElementsByTagName("a")[0].style.width="'.($this->lConf['menuWidth']+$this->lConf['paddingLeft']+$this->lConf['paddingRight']).'px" // dynamically width link
		litags[t].getElementsByTagName("a")[0].style.left="0"';

			if($this->lConf['renderType']==1){
				$outPut .="\n".'		litags[t].getElementsByTagName("a")[0].style.marginLeft="'.$this->lConf['marginLeftNormal'].'px"';
				$outPut .="\n".'		litags[t].getElementsByTagName("a")[0].style.marginRight="'.$this->lConf['marginRightNormal'].'px"';
				$outPut .="\n".'		litags[t].getElementsByTagName("a")[0].style.marginBottom="'.$this->lConf['marginBottomNormal'].'px"';
				$outPut .="\n".'		litags[t].getElementsByTagName("a")[0].style.marginTop="'.$this->lConf['marginTopNormal'].'px"';
			}
			$outPut .="\n".'		if(litags[t].getAttribute(\'level\')!="0'.$this->record.'"){';

			/* Define padding for text render items, image render menus will be define in buildmenu()*/
			if($this->lConf['backgroundRenderType']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.backgroundColor="'.$this->lConf['menuColor'].'"';
			}

			/* Define background for all items menu*/
			if($this->lConf['backgroundRenderType']==2){ /* Render type image*/
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.backgroundImage="url('.$this->lConf['backgroundImage'].')"';
			}

			/* Define padding for text render items, image render menus will be define in buildmenu()*/
			if($this->lConf['renderType']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.color="'.$this->lConf['fontColor'].'"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.fontSize="'.$this->lConf['fontSize'].'px"';
			}

			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingLeft="'.$this->lConf['paddingLeftNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingRight="'.$this->lConf['paddingRightNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingTop="'.$this->lConf['paddingTopNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingBottom="'.$this->lConf['paddingBottomNormal'].'px"';

			$outPut .="\n".'		}else{
			litags[t].getElementsByTagName("a")[0].style.borderBottom='.$boderDefinition.' // dynamically border
			litags[t].getElementsByTagName("a")[0].style.borderBottom='.$boderDefinition.' // dynamically border
			litags[t].getElementsByTagName("a")[0].style.borderLeft='.$boderDefinition.' // dynamically border
			litags[t].getElementsByTagName("a")[0].style.height="'.$this->lConf['menuHeightMain'].'px" // dynamically height link
			litags[t].getElementsByTagName("a")[0].style.width="'.$this->lConf['menuWidthMain'].'px" // dynamically width link
			counterGroundLevel++';
	
			/* Define padding for main level for text render items, image render menus will be define in buildmenu()*/
			if($this->lConf['renderTypeMain']==1){
				$outPut .="\n".'		litags[t].getElementsByTagName("a")[0].style.width="'.($this->lConf['menuWidth']+$this->lConf['paddingLeftNormal']+$this->lConf['paddingRightNormal']).'px" // dynamically width link';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.color="'.$this->lConf['fontColorMain'].'"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.fontSize="'.$this->lConf['fontSizeMain'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginRight="'.$this->lConf['marginRight'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginTop="'.$this->lConf['marginTop'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginBottom="'.$this->lConf['marginBottom'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginLeft="'.$this->lConf['marginLeft'].'px"';
			}
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingLeft="'.$this->lConf['paddingLeft'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingRight="'.$this->lConf['paddingRight'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingBottom="'.$this->lConf['paddingBottom'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingTop="'.$this->lConf['paddingTop'].'px"';
			/* Define background for main menu*/
			if($this->lConf['backgroundRenderTypeMain']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.backgroundColor="'.$this->lConf['menuColorMain'].'"';
			}
			/* Define background for main menu*/
			if($this->lConf['backgroundRenderTypeMain']==2){
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.backgroundImage="url('.$this->lConf['backgroundImageMain'].')"';
			}
			$outPut .="\n".'				levelCounter = t
		}';

		$outPut .="\n".'

	}
	litags[levelCounter].getElementsByTagName("a")[0].style.borderRight='.$boderDefinition.' // dynamically border
	var ultags=document.getElementById(menuids[i]).getElementsByTagName("ul")
	for (var t=0; t<ultags.length; t++){
		ultags[t].style.borderLeft='.$boderDefinition.' // dynamically border	
		ultags[t].style.borderRight='.$boderDefinition.' // dynamically border
		ultags[t].style.borderBottom='.$boderDefinition.' // dynamically border
		if (ultags[t].parentNode.parentNode.id==menuids[i]){ //if this is a first level submenu
			ultags[t].style.top=ultags[t].parentNode.offsetHeight-'.$this->lConf['borderWidth'].'+"px" //dynamically position first level submenus to be height of main menu item
			//ultags[t].style.left=findPosX(ultags[t].parentNode.offsetWidth-'.$this->lConf['marginLeft'].')+'.$this->lConf['marginLeft'].'+"px" //dynamically position first level submenus to be height of main menu item';
		$outPut .="\n".'		}
		else{ //else if this is a sub level menu (ul)
			theString = ultags[t].getElementsByTagName("a")[0].offsetWidth+"px";	
			theLength = theString.length;	
			theWidth =  theString.substring(0,theLength-2);
			windowPosition = findPosX(ultags[t].getElementsByTagName("a")[0]) + theWidth *2
			if(windowPosition>windowWidth){
				ultags[t].style.left="-"+(theWidth*1+2)+"px" //position menu to the right of menu item that activated it						
				ultags[t].style.zIndex=ultags[t].getElementsByTagName("a")[0].getAttribute(\'level\');
			}else{
				ultags[t].style.left=ultags[t-1].getElementsByTagName("a")[0].offsetWidth+"px" //position menu to the right of menu item that activated it
				ultags[t].style.zIndex=ultags[t].getElementsByTagName("a")[0].getAttribute(\'level\');
			}';
			$outPut .="\n".'		}
	}
}
// Define menu main width preventind fallback
counterGroundLevel = counterGroundLevel -1
additionalWidthCounter = 12*counterGroundLevel + counterGroundLevel*'.$this->lConf['marginLeft'].' + counterGroundLevel*'.$this->lConf['marginRight'].'+ counterGroundLevel*'.$this->lConf['paddingRightNormal'].'+ counterGroundLevel*'.$this->lConf['paddingLeftNormal'].'
menuTotalW = counterGroundLevel*'.$this->lConf['menuWidthMain'].'+additionalWidthCounter+"px"
document.getElementById(\''.$this->record.'\').style.width = menuTotalW
}
'.$this->record.'();

</script>'."\n";
/* 
 * 
 * 
 * 
 * VERTICAL MENU 
 * 
 * 
 * 
 * */
		}else{	
			$outPut .='
<script type="text/javascript">
//SuckerTree Vertical Menu 1.1 (Nov 8th, 06)
//By Dynamic Drive: http://www.dynamicdrive.com/style/

var menuids=["'.$this->record.'"] //Enter id(s) of SuckerTree UL menus, separated by commas
function '.$this->record.'(){
	for (var i=0; i<menuids.length; i++){
		var litags=document.getElementById(menuids[i]).getElementsByTagName("li")
		for (var t=0; t<litags.length; t++){
			litags[t].getElementsByTagName("a")[0].style.borderLeft='.$boderDefinition.'
			litags[t].getElementsByTagName("a")[0].style.borderTop='.$boderDefinition.'
			litags[t].getElementsByTagName("a")[0].style.height="'.$this->lConf['menuHeight'].'px"
			litags[t].getElementsByTagName("a")[0].style.width="'.$this->lConf['menuWidth'].'px"';

			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginLeft="'.$this->lConf['marginLeftNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginRight="'.$this->lConf['marginRightNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginBottom="'.$this->lConf['marginBottomNormal'].'px"';
			$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.marginTop="'.$this->lConf['marginTopNormal'].'px"';

			$outPut .="\n".'			if(litags[t].getAttribute(\'level\')!="0'.$this->record.'"){';
			
			/* Define background for text type*/
			if($this->lConf['backgroundRenderType']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.backgroundColor="'.$this->lConf['menuColor'].'"';
			}
			/* Define background for image type*/
			if($this->lConf['backgroundRenderType']==2){
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.backgroundImage="url('.$this->lConf['backgroundImage'].')"';
			}
			/* Define padding for text render items, image render menus will be define in buildmenu()*/
			if($this->lConf['renderType']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.color="'.$this->lConf['fontColor'].'"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.fontSize="'.$this->lConf['fontSize'].'px"';
			}
			if($this->lConf['renderType']==1){
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingLeft="'.$this->lConf['paddingLeftNormal'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingRight="'.$this->lConf['paddingRightNormal'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingTop="'.$this->lConf['paddingTopNormal'].'px"';
				$outPut .="\n".'			litags[t].getElementsByTagName("a")[0].style.paddingBottom="'.$this->lConf['paddingBottomNormal'].'px"';
			}
			$outPut .="\n".'			}else{
				litags[t].getElementsByTagName("a")[0].style.width="'.$this->lConf['menuWidthMain'].'px"
				litags[t].getElementsByTagName("a")[0].style.height="'.$this->lConf['menuHeightMain'].'px"
				litags[t].getElementsByTagName("a")[0].style.borderRight='.$boderDefinition.'
				litags[t].getElementsByTagName("a")[0].style.borderLeft='.$boderDefinition;

			/* Define padding for text render items, image render menus will be define in buildmenu()*/
			if($this->lConf['renderTypeMain']==1){
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.color="'.$this->lConf['fontColorMain'].'"';
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.fontSize="'.$this->lConf['fontSizeMain'].'px"';
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.paddingLeft="'.$this->lConf['paddingLeft'].'px"';
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.paddingRight="'.$this->lConf['paddingRight'].'px"';
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.paddingBottom="'.$this->lConf['paddingBottom'].'px"';
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.paddingTop="'.$this->lConf['paddingTop'].'px"';
			}
			$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.marginRight="'.$this->lConf['marginRight'].'px"';
			$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.marginTop="'.$this->lConf['marginTop'].'px"';
			$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.marginBottom="'.$this->lConf['marginBottom'].'px"';
			$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.marginLeft="'.$this->lConf['marginLeft'].'px"';

			/* Define background color for main menu*/
			if($this->lConf['backgroundRenderTypeMain']==1){
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.backgroundColor="'.$this->lConf['menuColorMain'].'"';
			}
			/* Define background image for main menu*/
			if($this->lConf['backgroundRenderTypeMain']==2){
				$outPut .="\n".'				litags[t].getElementsByTagName("a")[0].style.backgroundImage="url('.$this->lConf['backgroundImageMain'].')"';
			}
			$outPut .="\n".'				levelCounter = t
 			}
 		}';

		$outPut .="\n".'
		litags[levelCounter].getElementsByTagName("a")[0].style.borderBottom='.$boderDefinition.' // dynamically border
		var ultags=document.getElementById(menuids[i]).getElementsByTagName("ul")
		for (var t=0; t<ultags.length; t++){
			ultags[t].style.borderRight='.$boderDefinition.' // dynamically border
			ultags[t].style.borderBottom='.$boderDefinition.' // dynamically border
			if (ultags[t].parentNode.parentNode.id==menuids[i]){ //if this is a first level submenu
				ultags[t].style.left="'.($this->lConf['menuWidthMain']+$this->lConf['marginRight']+$this->lConf['marginLeft']+$this->lConf['paddingLeft']+$this->lConf['paddingRight']+$this->lConf['borderWidth']).'px" //dynamically position first level submenus to be width of main menu item
			}else{
			 	//else if this is a sub level submenu (ul)
				ultags[t].style.left=ultags[t-1].getElementsByTagName("a")[0].offsetWidth+"px" //position menu to the right of menu item that activated it
			}
		}
		for (var t=ultags.length-1; t>-1; t--){ //loop through all sub menus again, and use "display:none" to hide menus (to prevent possible page scrollbars
			ultags[t].style.visibility="visible"
			ultags[t].style.display="none"
		}
	}
}

'.$this->record.'();'."\n";
			$outPut .='document.getElementById(\''.$this->record.'\').style.width = "'.$this->lConf['menuWidthMain'].'px";'."\n";
			$outPut .='</script>'."\n";	
			}

$outPut .='<script type="text/javascript">
function IEHoverPseudo'.$this->record.'() {
	var navItems = document.getElementById("'.$this->record.'").getElementsByTagName("li");
	for (var i=0; i<navItems.length; i++) {
		if(navItems[i].className == "parent'.$this->record.'") {
			navItems[i].onmouseover=function() {
				if(this.getElementsByTagName("a")[0].getAttribute("active")==0){ // Only for none active items
					if(this.getElementsByTagName("a")[0].getAttribute("level")==0){
						this.getElementsByTagName("a")[0].style.color= "'.$this->lConf['fontColorMainOver'].'";';

			if($this->lConf['backgroundRenderTypeMain']==1){
				$outPut.="\n".'						this.getElementsByTagName("a")[0].style.backgroundColor= "'.$this->lConf['menuColorMainOver'].'"';
			}
			if($this->lConf['backgroundRenderTypeMain']==2){
				$outPut.="\n".'						this.getElementsByTagName("a")[0].style.backgroundImage= "url('.$this->lConf['backgroundImageMainOver'].')"';
				foreach($this->_backgroundImageMainOver as $itemId=>$value){
					$outPut .="\n\t\t\t\t\t\t".'if(this.getElementsByTagName("a")[0].getAttribute("id")=="main_'.$this->record.'_'.$itemId.'"){';
					$outPut .="\n\t\t\t\t\t\t\t".'this.getElementsByTagName("a")[0].style.backgroundImage="url('.$value.')"';
					$outPut .="\n\t\t\t\t\t\t".'}'."\n";
				}
			}
			if($this->lConf['renderTypeMain']==2){
				$outPut.="\n".'						overId = eval(\'_'.$this->record.'\'+this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].getAttribute("id")+\'_Over\')';
				$outPut.="\n".'						this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].src= overId.src';
			}

			$outPut.="\n".'					}else{
						this.getElementsByTagName("a")[0].style.color= "'.$this->lConf['fontColorOver'].'";';

			if($this->lConf['backgroundRenderType']==1){
				$outPut.='					this.getElementsByTagName("a")[0].style.backgroundColor= "'.$this->lConf['menuColorOver'].'"';
			}
			if($this->lConf['backgroundRenderType']==2){
				$outPut.="\n".'						this.getElementsByTagName("a")[0].style.backgroundImage= "url('.$this->lConf['backgroundImageOver'].')"';
				foreach($this->_backgroundImageOver as $itemId=>$value){
					$outPut .="\n\t\t\t\t\t\t".'if(this.getElementsByTagName("a")[0].getAttribute("id")=="'.$this->record.'_'.$itemId.'"){';
					$outPut .="\n\t\t\t\t\t\t\t".'this.getElementsByTagName("a")[0].style.backgroundImage="url('.$value.')"';
					$outPut .="\n\t\t\t\t\t\t".'}'."\n";
				}
			}
			
			if($this->lConf['renderType']==2){
				$outPut.="\n".'						overId = eval(\'_'.$this->record.'\'+this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].getAttribute("id")+\'_Over\')';
				$outPut.="\n".'						this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].src= overId.src';
			}
			
			$outPut.="\n".'					}
				}		
				this.getElementsByTagName("ul")[0].style.visibility="visible";
				this.getElementsByTagName("ul")[0].style.display="block";
			}
			// ON MOUSE OUT
			navItems[i].onmouseout=function() { 
				if(this.getElementsByTagName("a")[0].getAttribute("active")==0){ // Only for none active items
					if(this.getElementsByTagName("a")[0].getAttribute("level")==0){ 
						this.getElementsByTagName("a")[0].style.color= "'.$this->lConf['fontColorMain'].'";';

			if($this->lConf['backgroundRenderTypeMain']==1){
				$outPut.='					this.getElementsByTagName("a")[0].style.backgroundColor= "'.$this->lConf['menuColorMain'].'"';
			}
			if($this->lConf['backgroundRenderTypeMain']==2){
				$outPut.='						this.getElementsByTagName("a")[0].style.backgroundImage= "url('.$this->lConf['backgroundImageMain'].')"';
				foreach($this->_backgroundImageMain as $itemId=>$value){
					$outPut .="\n\t\t\t\t\t\t".'if(this.getElementsByTagName("a")[0].getAttribute("id")=="main_'.$this->record.'_'.$itemId.'"){';
					$outPut .="\n\t\t\t\t\t\t\t".'this.getElementsByTagName("a")[0].style.backgroundImage="url('.$value.')"';
					$outPut .="\n\t\t\t\t\t\t".'}'."\n";
				}
			}

			if($this->lConf['renderTypeMain']==2){
				$outPut.="\n".'						overId = eval(\'_'.$this->record.'\'+this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].getAttribute("id")+\'_\')';
				$outPut.="\n".'						this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].src= overId.src';
			}
			
			$outPut.='					}else{
						this.getElementsByTagName("a")[0].style.color= "'.$this->lConf['fontColor'].'";';

			if($this->lConf['backgroundRenderType']==1){
				$outPut.='					this.getElementsByTagName("a")[0].style.backgroundColor= "'.$this->lConf['menuColor'].'"';
			}
			if($this->lConf['backgroundRenderType']==2){
				$outPut.='						this.getElementsByTagName("a")[0].style.backgroundImage= "url('.$this->lConf['backgroundImage'].')"';
				foreach($this->_backgroundImage as $itemId=>$value){
					$outPut .="\n\t\t\t\t\t\t".'if(this.getElementsByTagName("a")[0].getAttribute("id")=="'.$this->record.'_'.$itemId.'"){';
					$outPut .="\n\t\t\t\t\t\t\t".'this.getElementsByTagName("a")[0].style.backgroundImage="url('.$value.')"';
					$outPut .="\n\t\t\t\t\t\t".'}'."\n";
				}
			}

			if($this->lConf['renderType']==2){
				$outPut.="\n".'						overId = eval(\'_'.$this->record.'\'+this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].getAttribute("id")+\'_\')';
				$outPut.="\n".'						this.getElementsByTagName("a")[0].getElementsByTagName("img")[0].src= overId.src';
			}
			
			$outPut.='					}
				}
				this.getElementsByTagName("ul")[0].style.visibility="hidden";
				this.getElementsByTagName("ul")[0].style.display="block";
			}
		}
	}

}
// Onload handle function	
womAdd(\'IEHoverPseudo'.$this->record.'()\');
womOn();
</script>'."\n";

			/* SET ACTIVE MENU STATE MAIN LEVEL*/
			#if($this->lConf['renderTypeMain']==1){
			$outPut .='<script type="text/javascript">'."\n";
			foreach($GLOBALS['TSFE']->rootLine as $value){
				
				$outPut .='if(document.getElementById("main_'.$this->record.'_'.$value['uid'].'")){'."\n";
				/* text */
				$outPut .="\t".'document.getElementById("main_'.$this->record.'_'.$value['uid'].'").style.color="'.$this->lConf['fontColorMainActive'].'"'."\n";
				/* text & color backgr*/
				if($this->lConf['backgroundRenderTypeMain']==1){
					$outPut .="\t".'document.getElementById("main_'.$this->record.'_'.$value['uid'].'").style.backgroundColor="'.$this->lConf['menuColorMainActive'].'"'."\n";
				}
				/* text & img backgr*/
				if($this->lConf['backgroundRenderTypeMain']==2){
					$outPut .="\t".'document.getElementById("main_'.$this->record.'_'.$value['uid'].'").style.backgroundImage="url('.$this->lConf['backgroundImageMainActive'].')"'."\n";
				}
				$outPut .='}'."\n";

				/* SET ACTIVE MENU STATE NORMAL LEVEL*/

				$outPut .='if(document.getElementById("'.$this->record.'_'.$value['uid'].'")){'."\n";
				/* text */
				$outPut .="\t".'document.getElementById("'.$this->record.'_'.$value['uid'].'").style.color="'.$this->lConf['fontColorActive'].'"'."\n";
				/* text & color backgr*/
				if($this->lConf['backgroundRenderType']==1){
					$outPut .="\t".'document.getElementById("'.$this->record.'_'.$value['uid'].'").style.backgroundColor="'.$this->lConf['menuColorActive'].'"'."\n";
				}
				/* text & img backgr*/
				if($this->lConf['backgroundRenderType']==2){
					$outPut .="\t".'document.getElementById("'.$this->record.'_'.$value['uid'].'").style.backgroundImage="url('.$this->lConf['backgroundImageActive'].')"'."\n";
				}
				$outPut .='}'."\n";
			}	
			/* Define background for special menu items main and active state */
			if($this->lConf['backgroundRenderTypeMain']==2){
				foreach($this->_backgroundImageMain as $itemId=>$value){
					/* Define normal state*/
					$outPut .="\n".'document.getElementById("main_'.$this->record.'_'.$itemId.'").style.backgroundImage="url('.$value.')"'."\n";
				}
				foreach($this->_backgroundImageMainActive as $itemId=>$url){
					foreach($GLOBALS['TSFE']->rootLine as $value){
						$outPut .='if(document.getElementById("main_'.$this->record.'_'.$value['uid'].'")){'."\n";
						$outPut .="\t".'document.getElementById("main_'.$this->record.'_'.$value['uid'].'").style.backgroundImage="url('.$url.')"'."\n";
						$outPut .='}'."\n";
					}
				}
			}
			
			/* Define background for special menu items normal and active state*/
			if($this->lConf['backgroundRenderType']==2){
				foreach($this->_backgroundImage as $itemId=>$value){
					/* Define normal state*/
					$outPut .="\n".'document.getElementById("'.$this->record.'_'.$itemId.'").style.backgroundImage="url('.$value.')"'."\n";
				}
				foreach($this->_backgroundImageActive as $itemId=>$url){
					/* Define active state*/
					foreach($GLOBALS['TSFE']->rootLine as $value){
						$outPut .='if(document.getElementById("'.$this->record.'_'.$value['uid'].'")){'."\n";
						$outPut .="\t".'document.getElementById("'.$this->record.'_'.$value['uid'].'").style.backgroundImage="url('.$url.')"'."\n";
						$outPut .='}'."\n";
					}
				}
			}
			$outPut .='</script>'."\n";

			#}
			return $outPut;	
	}	
	

}	

 if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/menu/res/suckerFish/class.tx_menu_pi1_suckerfish.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/menu/res/suckerFish/class.tx_menu_pi1_suckerfish.php']);
}

?>
