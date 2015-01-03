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

/**
 * Countries Model
 */
class IpdataModelCountries extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.worldzone','worldzone',
				'a.name','name',
				'currencyname',
				'a.currency','currency',
				'a.codethree','codethree',
				'a.codetwo','codetwo',
				'a.published','published'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		
		$name = $app->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		$this->setState('filter.name', $name);
		
		$currency = $app->getUserStateFromRequest($this->context . '.filter.currency', 'filter_currency');
		$this->setState('filter.currency', $currency);
		
		$worldzone = $app->getUserStateFromRequest($this->context . '.filter.worldzone', 'filter_worldzone');
		$this->setState('filter.worldzone', $worldzone);
		
		$codetwo = $app->getUserStateFromRequest($this->context . '.filter.codetwo', 'filter_codetwo');
		$this->setState('filter.codetwo', $codetwo);
		
		$codethree = $app->getUserStateFromRequest($this->context . '.filter.codethree', 'filter_codethree');
		$this->setState('filter.codethree', $codethree);
				
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();
		// load parent items
		$items = parent::getItems();
		// return items
		return $items;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{		
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');
		$query->select('c.name AS currencyname');

		// From the ipdata_country table
		$query->from('#__ipdata_country AS a');
		
		$query->join('LEFT', '#__ipdata_currency AS c ON c.codethree = a.currency');
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}
		
		// Filter by published currancy
		$currency = $this->getState('filter.currency');
		if (!empty($currency))
		{
			$query->where('a.currency LIKE ' . $db->quote($currency));
		}
		
		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.codetwo LIKE ' . $search . ' OR a.codethree LIKE ' . $search . ' OR c.name LIKE ' . $search . ' OR a.currency LIKE ' . $search . ')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');		
		if ($orderCol != '') {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.worldzone');
		$id .= ':' . $this->getState('filter.name');
		$id .= ':' . $this->getState('filter.currency');
		$id .= ':' . $this->getState('filter.codetwo');
		$id .= ':' . $this->getState('filter.codethree');

		return parent::getStoreId($id);
	}
	
	/**
	 * Build an SQL query to checkin all items left chekced out longer then a day.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_ipdata')->get('check_in');
		
		if ($time){
			// Get Yesterdays date
			$date =& JFactory::getDate()->modify($time)->toSql();	
	
			// Get a db connection.
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
				$db->quoteName('checked_out') . '=0'
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('checked_out') . '!=0', 
				$db->quoteName('checked_out_time') . '<\''.$date.'\''
			);
			
			// Check table
			$query->update($db->quoteName('#__ipdata_country'))->set($fields)->where($conditions); 
				 
			$db->setQuery($query);
			 
			$result = $db->query();
		}
		
		return true;
	}
}
