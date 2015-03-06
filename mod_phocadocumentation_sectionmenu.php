<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @module Phoca - Phoca Module
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
defined('_JEXEC') or die('Restricted access');// no direct access

$user 		=& JFactory::getUser();
$aid 		= $user->get('aid', 0);	
$db 		=& JFactory::getDBO();
$menu 		=& JSite::getMenu();
$document	=& JFactory::getDocument();

$document->addStyleSheet(JURI::base(true).'/components/com_phocadocumentation/assets/phocadocumentation.css');
		
//$paramsC = JComponentHelper::getParams('com_phocadocumentation') ;


// PARAMS 
$display_sections 	= $params->get( 'display_sections', '' );
$hide_sections 		= $params->get( 'hide_sections', '' );

// ITEMID
$itemSection	= $menu->getItems('link', 'index.php?option=com_phocadocumentation&view=sections');
	
if(isset($itemSection[0])) {
	$itemId = $itemSection[0]->id;
} else {
	$itemId = JRequest::getVar('Itemid', 1, 'get', 'int');
}

// SQL, QUERY
if ( $display_sections != '' ) {
	$section_ids_where = " AND s.id IN (".$display_sections.")";
} else {
	$section_ids_where = '';
}

if ( $hide_sections != '' ) {
	$section_ids_not_where = " AND s.id NOT IN (".$hide_sections.")";
} else {
	$section_ids_not_where = '';
}

$wheres[] = " s.published = 1";
$wheres[] = " cc.published = 1";
$wheres[] = " s.id = cc.section";
	

if ($aid !== null) {
	$wheres[] = "s.access <= " . (int) $aid;
}

$query =  " SELECT s.id, s.title, s.alias, COUNT(cc.id) AS numcat, '' AS categories"
		. " FROM #__sections AS s, #__categories AS cc"
		. " WHERE " . implode( " AND ", $wheres )
		. $section_ids_where
		. $section_ids_not_where
		. " GROUP BY s.id";
$db->setQuery( $query );
$sections = $db->loadObjectList();

// DISPLAY
$output = '<div id="phoca-doc-sections-box-module">';
if (!empty($sections)) {
	foreach ($sections as $value) {
		$output .= '<p class="sections">';
		$output .= '<a href="'. JRoute::_('index.php?option=com_phocadocumentation&view=section&id='.$value->id.':'.$value->alias.'&Itemid='.(int)$itemId ).'">'. $value->title.'</a>';
		$output .= ' <small>('.$value->numcat.')</small></p>';
	}	
}
$output .= '</div>';

require(JModuleHelper::getLayoutPath('mod_phocadocumentation_sectionmenu'));
?>