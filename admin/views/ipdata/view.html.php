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

jimport('joomla.application.component.helper');

class IpdataViewIpdata extends JViewLegacy
{
	protected $params;
	protected $tabs;
	protected $tab_active;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// check for updates
		GetHelper::update();
		// Assign data to the view
		$this->tabs 		= $this->get('Tabs');
		$this->tab_active 	= $this->get('Tabactive');
		// Get app Params
		$this->params 	= JComponentHelper::getParams('com_ipdata');
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			/*ContentHelper::addSubmenu('ipdata');
			$this->sidebar = JHtmlSidebar::render();*/
			$this->addToolbar();
		}
		parent::display($tpl);
		
		// Set the document
		$this->setDocument();
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function addToolbar()
	{	
		$canDo = JHelperContent::getActions('com_ipdata', 'ipdata');
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		
		JToolBarHelper::title(JText::_('COM_IPDATA'), 'location ipdata');
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_ipdata');
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
		
		$document->addStyleSheet(JURI::root() . "administrator/components/com_ipdata/assets/css/dashboard.css");
		
		$document->addStyleSheet(JURI::root() . "administrator/components/com_ipdata/assets/css/footable.core.css?v=2-0-1");
		$document->addStyleSheet(JURI::root() . "administrator/components/com_ipdata/assets/css/footable.standalone.css");
		
		$document->addScript(JURI::root() . "administrator/components/com_ipdata/assets/js/footable.js?v=2-0-1");
		$document->addScript(JURI::root() . "administrator/components/com_ipdata/assets/js/footable.sort.js?v=2-0-1");
		$document->addScript(JURI::root() . "administrator/components/com_ipdata/assets/js/footable.filter.js?v=2-0-1");
		$document->addScript(JURI::root() . "administrator/components/com_ipdata/assets/js/footable.paginate.js?v=2-0-1");
		
		$document->setTitle(JText::_('COM_IPDATA'));
	}
}
