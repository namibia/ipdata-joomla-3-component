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
 * Currencies View
 */
class IpdataViewCurrencies extends JViewLegacy
{
	/**
	 * Currencies view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			ContentHelper::addSubmenu('currencies');
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
		$this->canEdit 		= $this->user->authorise('currencies.edit', 'com_ipdata');
		$this->canState 	= $this->user->authorise('currencies.edit.state', 'com_ipdata');

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
		$canDo = GetHelper::getActions(0,'currency');
		JToolBarHelper::title(JText::_('Ip Data - Currencies'), 'ipdata');
		JHtmlSidebar::setAction('index.php?option=com_ipdata&view=currencies');
		
		if ($canDo->get('currencies.create')) {
			JToolBarHelper::addNew('currency.add');
		}
		if ($canDo->get('currencies.edit')) {
			JToolBarHelper::editList('currency.edit');
		}

		if ($canDo->get('currencies.edit.state')) {
			JToolBarHelper::divider();

			JToolBarHelper::publishList('currencies.publish');
			JToolBarHelper::unpublishList('currencies.unpublish');
	
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('currencies.archive');
				
			if ($canDo->get('core.admin')) {
				
				JToolBarHelper::checkin('currencies.checkin');
			}
		}

		if ($canDo->get('currencies.delete')) {
			JToolBarHelper::deleteList('', 'currencies.delete');
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
	}

	/**
	 * Method to set up the document properties
	 *
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('Ip Data - Currencies'));
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.published' 		=> JText::_('JSTATUS'),
			'a.name' 			=> JText::_('Name'),
			'a.codethree' 		=> JText::_('Code 3'),
			'a.numericcode' 	=> JText::_('Numeric Code')
		);
	}
}
