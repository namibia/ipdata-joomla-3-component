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
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * name Form Field class for the Builderbox component
 */
class JFormFieldCurrency extends JFormFieldList
{
	/**
	 * The name field type.
	 *
	 * @var		string
	 */
	protected $type = 'currency';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__ipdata_currency.codethree as value, #__ipdata_currency.name as name');
		$query->from('#__ipdata_currency');
		$query->where('#__ipdata_currency.published = 1');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if($items){
			foreach($items as $item){
				$options[] = JHtml::_('select.option', $item->value, ucwords($item->name));
			};
		};
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}