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

jimport('joomla.database.databasequery');

function IpdataBuildRoute(&$query)
{
	$segments = array();
	
	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	
	return $segments;
}

function IpdataParseRoute($segments)
{
	$vars = array();
	switch($segments[0])
	{
		   case 'json':
				   $vars['view'] = 'json';
				   break;
	}
	return $vars;
}