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
// Use backend helper.
JLoader::register('IpdataHelper', JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'ipdata.php');

class IpdataModelControl extends JModelList
{
	protected $app_params;
	
	public function __construct() 
	{
		parent::__construct();
		
		// get params
		$this->app_params = JComponentHelper::getParams('com_ipdata');
		
	}
	
	public function getData($ip,$keyToken = 0,$base ,$mode = 0, $string = 0)
	{
		$access = false;
		if($this->app_params->get('api_access') > 0){
			$privateKey = $this->app_params->get('api_privatekey');
			if(strlen($privateKey) > 0){
				switch($this->app_params->get('api_access')){
					case 1:
					// only private key
					if(md5($privateKey) == $keyToken){
						$access = true;
					}
					break;
					case 2:
					// all users
					$users = $this->getUserNames();
					if(is_array($users)){
						foreach($users as $user){
							if(md5($user.'_'.$privateKey) == $keyToken){
								$access = true;
								break;
							}
						}
					}
					break;
					case 3:
					// only users in certain groups
					if(is_array($this->app_params->get('api_accessgroup'))){
						$ids = $this->getUserIdsInGroups($this->app_params->get('api_accessgroup'));
						if(is_array($ids)){
							$users = $this->getUserNames($ids);
							if(is_array($users)){
								foreach($users as $user){
									if(md5($user.'_'.$privateKey) == $keyToken){
										$access = true;
										break;
									}
								}
							}
						}
					}
					break;
					case 4:
					// only users with certain access level
					if(is_array($this->app_params->get('api_accesslevel'))){
						$groups = $this->getUserGroupsWithAccess($this->app_params->get('api_accesslevel'));
						if(is_array($groups)){
							$ids = $this->getUserIdsInGroups($groups);
							if(is_array($ids)){
								$users = $this->getUserNames($ids);
								if(is_array($users)){
									foreach($users as $user){
										if(md5($user.'_'.$privateKey) == $keyToken){
											$access = true;
											break;
										}
									}
								}
							}
						}
					}
					break;
					case 5:
					// only selected users
					if(is_array($this->app_params->get('api_accessuser'))){
						foreach($this->app_params->get('api_accessuser') as $user){
							if(md5($user.'_'.$privateKey) == $keyToken){
								$access = true;
								break;
							}
						}
					}						
					break;
				}
			}
		} else {
			$access = true;
		}
		if ($access && $this->isReady()){
			$result = (string) preg_replace('/[^0-9\.]/i', '', $ip);
			if(strlen($base) > 0){
				$base = strtoupper($base);
				if(!$this->isCurrency($base)){
					$base = $this->app_params->get('base_currency', 'EUR');
				}
			} else {
				$base = $this->app_params->get('base_currency', 'EUR');
			}
			$ipdata = new IpdataHelper(trim($result),$base);
			if($ipdata){
				return $ipdata->getInfo($mode, $string);
			}
		}
		return false;
	}
	
	protected function isReady()
	{
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_ipdata/helpers/updateCron.php')){
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('#__ipdata_update_cron.date');
			$query->from('#__ipdata_update_cron');
			$query->where('#__ipdata_update_cron.active = 8');
			$db->setQuery((string)$query);
			$db->execute();
			if($db->getNumRows()){
				return false;
			}
		}
		return true;
	}
	
	protected function isCurrency($currency)
	{
		if ($currency != 'USD'){
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('#__ipdata_currency.id');
			$query->from('#__ipdata_currency');
			$query->where('#__ipdata_currency.codethree = '. $db->quote($currency));
			$db->setQuery((string)$query);
			$db->execute();
			if($db->getNumRows()){
				return true;
			}
			return false;
		}
		return true;
	}
	
	protected function getUserNames($ids)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__users.username');
		$query->from('#__users');
		if(is_array($ids)){
			$query->where('#__users.id IN ('.implode(',', $ids).')');
		}
		$db->setQuery((string)$query);
		return $db->loadColumn();
	}
	
	protected function getUserIdsInGroups($groups)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__user_usergroup_map.user_id');
		$query->from('#__user_usergroup_map');
		$query->where('#__user_usergroup_map.group_id IN ('.implode(',', $groups).')');
		$db->setQuery((string)$query);
		return $db->loadColumn();
	}
	
	protected function getUserGroupsWithAccess($levels)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__viewlevels.rules');
		$query->from('#__viewlevels');
		$query->where('#__viewlevels.id IN ('.implode(',', $levels).')');
		$db->setQuery((string)$query);
		$group_levels =  $db->loadColumn();
		if(is_array($group_levels)){
			$groups = array();
			foreach($group_levels as $level){
				$group_ids = json_decode($level, true);
				if(is_array($group_ids)){
					$groups = $groups + $group_ids;
				}
			}
			return array_unique($groups);
		}
		return false;
	}
}
