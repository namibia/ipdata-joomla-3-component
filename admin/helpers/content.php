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


class ContentHelper extends JHelperContent
{
	public static $extension = 'com_ipdata';

	/**
	 * Configure the submenu.
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_HOME'),
			'index.php?option=com_ipdata&view=ipdata',
			$vName == 'ipdata'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_API_DOC'),
			'index.php?option=com_ipdata&view=ipdata&tab=1',
			$vName == 'ipdata'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_IP_UPDATER'),
			'index.php?option=com_ipdata&view=ipdata&tab=2',
			$vName == 'ipdata'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_EXCHANGERATE_UPDATER'),
			'index.php?option=com_ipdata&view=ipdata&tab=3',
			$vName == 'ipdata'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_COUNTRIES'),
			'index.php?option=com_ipdata&view=countries',
			$vName == 'countries'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_IPDATA_CURRENCIES'),
			'index.php?option=com_ipdata&view=currencies',
			$vName == 'currencies'
		);
	}

	/**
	 * Applies the content tag filters to arbitrary text as per settings for current user group
	 *
	 * @param   text  $text  The string to filter
	 *
	 * @return  string  The filtered string
	 *
	 * @deprecated  4.0  Use JComponentHelper::filterText() instead.
	*/
	public static function filterText($text)
	{
		JLog::add('ContentHelper::filterText() is deprecated. Use JComponentHelper::filterText() instead.', JLog::WARNING, 'deprecated');

		return JComponentHelper::filterText($text);
	}
}
