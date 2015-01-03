<?php
/**
* 
* 	@version 	1.0.0  December 11, 2014
* 	@package 	Ip Data API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Added for Joomla 3.0
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
};

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_ipdata')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
};

// require helper files
JLoader::register('GetHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'get.php');
JLoader::register('ContentHelper', dirname(__FILE__) . DS.'helpers'.DS.'content.php');

// No access check.
$controller	= JControllerAdmin::getInstance('Ipdata');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
