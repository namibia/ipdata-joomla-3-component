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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Countries View
 */
class IpdataViewCountries extends JViewLegacy
{
	/**
	 * Countries view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			ContentHelper::addSubmenu('countries');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		};

		// Assign data to the view
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->user 		= JFactory::getUser();
		$this->listOrder	= $this->escape($this->state->get('list.ordering'));
		$this->listDirn		= $this->escape($this->state->get('list.direction'));
		$this->canEdit 		= $this->user->authorise('countries.edit', 'com_ipdata');
		$this->canState 	= $this->user->authorise('countries.edit.state', 'com_ipdata');
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = GetHelper::getActions(0,'countries');
		JToolBarHelper::title(JText::_('Ip Data - Countries'), 'ipdata');
		JHtmlSidebar::setAction('index.php?option=com_ipdata&view=countries');
		
		if ($canDo->get('countries.create')) {
			JToolBarHelper::addNew('country.add');
		}
		if ($canDo->get('countries.edit')) {
			JToolBarHelper::editList('country.edit');
		}

		if ($canDo->get('countries.edit.state')) {
			JToolBarHelper::divider();

			JToolBarHelper::publishList('countries.publish');
			JToolBarHelper::unpublishList('countries.unpublish');
	
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('countries.archive');
				
			if ($canDo->get('core.admin')) {
				
				JToolBarHelper::checkin('countries.checkin');
			}
		}

		if ($canDo->get('countries.delete')) {
			JToolBarHelper::deleteList('', 'countries.delete');
		}
		
		JToolBarHelper::divider();
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_ipdata');
		}
		
		if($this->canState){
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
		}
		
		// set Currency selection
		$currencies = $this->getCurrencies();
		if($currencies){
			JHtmlSidebar::addFilter(
			JText::_('- Select Currency -'),
			'filter_currency',
			JHtml::_('select.options', $currencies, 'value', 'text', $this->state->get('filter.currency'))
			);
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('Ip Data - Countries'));
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.published' 	=> JText::_('JSTATUS'),
			'a.name' 		=> JText::_('Name'),
			'a.worldzone' 	=> JText::_('Worldzone'),
			'currencyname' 	=> JText::_('Currency'),
			'a.codethree' 	=> JText::_('Code 3'),
			'a.codetwo' 	=> JText::_('Code 2')
		);
	}
	
	protected function getCurrencies()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__ipdata_currency.codethree as value, #__ipdata_currency.name as name');
		$query->from('#__ipdata_currency');
		$query->where('#__ipdata_currency.published = 1');
		$query->order('#__ipdata_currency.ordering ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if($items){
			foreach($items as $item){
				$options[] = JHtml::_('select.option', $item->value, ucwords($item->name));
			};
			return $options;
		};
		return false;
	}
}
