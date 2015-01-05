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

class IpdataModelIpdata extends JModelList
{
	protected $app_params;
	protected $tab_id;
	
	public function __construct() 
	{		
		parent::__construct();
		
		// get params
		$this->app_params	= JComponentHelper::getParams('com_ipdata');
		// get tab ID
		$jinput				= JFactory::getApplication()->input;
		$this->tab_id 		= $jinput->get('tab', 0, 'INT');
	}
	
	public function getTabs()
	{	
		$APIkey = $this->getAPIkey();
		$canDo = JHelperContent::getActions('com_ipdata', 'ipdata');
		if ($canDo->get('core.admin')) {
			// setu the return url
			$uri = (string) JUri::getInstance();
			$return = urlencode(base64_encode($uri));
			$globalSettings = '<a href="index.php?option=com_config&amp;view=component&amp;component=com_ipdata&amp;path=&amp;return='.$return.'">'.JText::_('COM_IPDATA_CLICK_HERE').'</a>';
		} else {
			$globalSettings = JText::_('COM_IPDATA_CONTACT_YOUR_SYSTEM_ADMIN');
		}
			
		$div_cPanel = '<div class="span9">
						<h2 class="nav-header">'.JText::_('COM_IPDATA_CPANEL_HEADER').'</h2>
                        <div class="well well-small">
							'. $this->setIcons() .'
							<div class="clearfix"></div>
                    	</div>
                    </div>
                    <div class="span3">
                        <div>
							<h2 class="nav-header">'.JText::_('COM_IPDATA_EXTENSION_DETAILS').'</h2>
                            <a target="_blank" style="float: right;"href="https://www.vdm.io/joomla" title="Vast Development Method"><img src="/administrator/components/com_ipdata/assets/images/vdm.jpg" height="300"/></a>
							<ul class="list-group">
  								<li class="list-group-item">IP DATA</li>
  								<li class="list-group-item">Copyright &#169; <a href="http://vdm.io" target="_blank">Vast Development Method</a>.<br />All rights reserved.</li>
								<li class="list-group-item">Distributed under the GNU GPL <br />Version 2 or later</li>
								<li class="list-group-item">See <a href="https://www.vdm.io/gnu-gpl" target="_blank">License details</a></li>';
		$workers = $this->getWorkers();
		if(count($workers)){
			foreach($workers as $worker){						
				$div_cPanel .= '<li class="list-group-item">'.$worker.'</li>';
			}
		}
		$div_cPanel .= '</ul></div></div>';
					
		$div_APIJson = '<div class="span12">
						<h2 class="nav-header">'.JText::_('COM_IPDATA_API_HEADER').'</h2>
                        <div class="well well-small">
							<h2 class="nav-header">'.JText::_('COM_IPDATA_API_HOW_HEADER').'</h2>
							<p>'.JText::_('COM_IPDATA_JSON_API_CONTENT_1').'</p>
							<p>'.JText::_('COM_IPDATA_JSON_API_CONTENT_2').' <a href="'.JURI::root().'index.php?option=com_ipdata&amp;task=api.data&amp;format=json&amp;ip=105.232.113.65&amp;key='.$APIkey.'&amp;raw=1&amp;s=0&amp;m=0&amp;base=EUR" target="_blank">'.JURI::root().'index.php?option=com_ipdata&task=api.data&format=json&ip=105.232.113.65&key='.$APIkey.'&raw=1&s=0&m=0&base=EUR</a></p>
                        </div>
						<div class="well well-small">
							<p>'.JText::_('COM_IPDATA_JSON_API_CONTENT_3').'</p>
							<ol>
								<li>'.JText::_('Your Domain').' => [ '.JURI::root().' ]<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_4').'</li>
								<li>'.JText::_('Applcation Defaults').' => [ index.php?option=com_ipdata&task=api.data&format=json ]<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_4').'</li>
								<li>'.JText::_('IP Address').' => [ &ip=105.232.113.65 ]<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_5').'</li>
								<li>'.JText::_('API Key').' => [ &key='.$APIkey.' ]	<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_6').'
																					<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_6_1').'
																					'.JText::_('COM_IPDATA_JSON_API_CONTENT_6_2').'
																					<br />'.JText::sprintf('COM_IPDATA_JSON_API_CONTENT_6_3', $globalSettings).'</li>
								<li>'.JText::_('Return Wrapper').' => [ &raw=1 ] ('.JText::_('optional').')<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_7').'</li>
								<li>'.JText::_('Return Mode').' => [ &m=0 ] ('.JText::_('optional').')<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_8').'
																										<br /><ul>
																												  <li>0 => ALL</li>
																												  <li>1 => IP_STR</li>
																												  <li>2 => IP_VALUE</li>
																												  <li>3 => IP_RANGE_NUMERICAL</li>
																												  <li>4 => IP_RANGE</li>
																												  <li>5 => IP_REGISTRY</li>
																												  <li>6 => IP_ASSIGNED_UNIXTIME</li>
																												  <li>7 => COUNTRY_ALL</li>
																												  <li>8 => COUNTRY_NAME</li>
																												  <li>9 => COUNTRY_CODE_TWO</li>
																												  <li>10 => COUNTRY_CODE_THREE</li>
																												  <li>11 => CURRENCY_ALL</li>
																												  <li>12 => CURRENCY_NAME</li>
																												  <li>13 => CURRENCY_CODE_THREE</li>
																												  <li>14 => CURRENCY_CODE_NUMERIC</li>
																												  <li>15 => CURRENCY_SYMBOL</li>
																												  <li>16 => CURRENCY_DECIMAL_PLACE</li>
																												  <li>17 => CURRENCY_DECIMAL_SYMBOL</li>
																												  <li>18 => CURRENCY_POSITIVE_STYLE</li>
																												  <li>19 => CURRENCY_NEGATIVE_STYLE</li>
																												  <li>20 => EXCHANGE_RATE_ALL</li>
																												  <li>21 => EXCHANGE_RATE_ID</li>
																												  <li>22 => EXCHANGE_RATE_NAME</li>
																												  <li>23 => EXCHANGE_RATE</li>
																												  <li>24 => EXCHANGE_RATE_ASK</li>
																												  <li>25 => EXCHANGE_RATE_BID</li>
																												  <li>26 => EXCHANGE_RATE_DATE</li>
																											</ul></li>
								<li>'.JText::_('String Only').' => [ &s=0 ] ('.JText::_('optional').')<br />'.JText::_('COM_IPDATA_JSON_API_CONTENT_9').'</li>
								<li>'.JText::_('Base Currency').' => [ &base=EUR ] ('.JText::_('optional').')<br />'.JText::sprintf('COM_IPDATA_JSON_API_CONTENT_10', $globalSettings).'</li>
							</ol>
                        </div>
                    </div>';
		
		$tabs 				= array();
		// cPanel setup
		$tab_cPanel 		= new stdClass();	
		$tab_cPanel->alias 	= 'cpanel';
		$tab_cPanel->name 	= 'COM_IPDATA_CPANEL';
		$tab_cPanel->div 	= $div_cPanel;
		$tabs[0]			= $tab_cPanel;
		// API setup
		$tab_APIJson		= new stdClass();
		$tab_APIJson->alias = 'json_api';
		$tab_APIJson->name 	= 'COM_IPDATA_API_DOC';
		$tab_APIJson->div 	= $div_APIJson;
		$tabs[1]			= $tab_APIJson;
		
		// set the ip update tab
		$div_ipUpdate = '<div class="span12">';
		$div_ipUpdate .= '<a class="btn btn-small" href="'.JURI::base().'index.php?option=com_ipdata&amp;task=update.iptable&amp;key='.JSession::getFormToken().'" onclick="pleaseWait()">Manual Update IP Tables Now</a> <br /><small><em>This may take long! Do not close the browser window</em></small>';
		$div_ipUpdate .= '<h2 class="nav-header">Support the Software77 Team</h2><div class="well well-small">';
		$div_ipUpdate .= '<p>The database used to updated IP tables is provided by <a href="http://software77.net/geo-ip/" target="_blank">software77.net</a> as "<a href="http://software77.net/geo-ip/?license" target="_blank">donationware</a>" <br />
They need your support! Please make a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3HKM8N5XXUHV6" target="_blank">donation via paypal</a> now!</p>';
		$div_ipUpdate .= '</div>';
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ipdata'.DS.'helpers'.DS.'ipUpdateCron.php')) {
			$div_ipUpdate .= '<h2 class="nav-header">Activate Auto IP Updater!</h2><div class="well well-small">';
			// plugin was installed bit not active
			$div_ipUpdate .= '<p>You need to <a href="'.JURI::base().'index.php?option=com_plugins&view=plugins&filter_search=System - IP Data Cron" >activate</a> the <b>System - IP Data Cron</b> for the auto ip updater to work.</p>';
		} else {
			$div_ipUpdate .= '<h2 class="nav-header">Get Auto IP Updater!</h2><div class="well well-small">';
			$div_ipUpdate .= '<h2>There is an <b>Auto IP Updater</b> available <a href="https://www.vdm.io/joomla/item/ip-data-update-cron" target="_blank">HERE!</a></h2>';
			$div_ipUpdate .= '<p>Instead of manualy updating your IP table, get the <b>auto IP updater</b> <a href="https://www.vdm.io/joomla/item/ip-data-update-cron" target="_blank">TODAY!</a></h3>';
		}
		$div_ipUpdate .= '</div></div>';
		
		// set the exchange rate update tab
		$div_rateUpdate = '<div class="span12">';
		$div_rateUpdate .= '<a class="btn btn-small" href="'.JURI::base().'index.php?option=com_ipdata&amp;task=update.rates&amp;key='.JSession::getFormToken().'" onclick="pleaseWait()">Update Exchange Rates Now</a><br /><small><em>This will take long! Do not close the browser window. This can take up to <b>25 minutes</b>.</small>';
		$div_rateUpdate .= '<h2 class="nav-header">Yahoo finance</h2><div class="well well-small">';
		$div_rateUpdate .= '<p>Thanks to <a href="http://finance.yahoo.com/currency-converter/" target="_blank">Yahoo finance</a> we can update your DB with the latest exchange rates.</p>';
		$div_rateUpdate .= '</div>';
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ipdata'.DS.'helpers'.DS.'rateUpdateCron.php')) {
			$div_rateUpdate .= '<h2 class="nav-header">Activate Auto exchange rate Updater!</h2><div class="well well-small">';
			// plugin was installed bit not active
			$div_rateUpdate .= '<p>You need to <a href="'.JURI::base().'index.php?option=com_plugins&view=plugins&filter_search=System - IP Data Cron">activate</a> the <b>System - IP Data Cron</b> for the auto exchange rate updater to work.</p>';
		} else {
			$div_rateUpdate .= '<h2 class="nav-header">Get Auto exchange rate Updater!</h2><div class="well well-small">';
			$div_rateUpdate .= '<h2>There is an <b>Auto exchange rate Updater</b> available <a href="https://www.vdm.io/joomla/item/ip-data-update-cron" target="_blank">HERE!</a></h2>';
			$div_rateUpdate .= '<p>Instead of manualy updating your exchange rates, get the <b>auto exchange rate updater</b> <a href="https://www.vdm.io/joomla/item/ip-data-update-cron" target="_blank">TODAY!</a></h3>';
		}
		$div_rateUpdate .= '</div></div>';
		
		// ip updater setup
		$tab_ipUpdate 			= new stdClass();
		$tab_ipUpdate->alias 	= 'ip_update';
		$tab_ipUpdate->name 	= 'COM_IPDATA_IP_UPDATER';
		$tab_ipUpdate->div 		= $div_ipUpdate;
		$tabs[3]				= $tab_ipUpdate;
		
		// rate updater setup
		$tab_rateUpdate 		= new stdClass();
		$tab_rateUpdate->alias 	= 'rate_update';
		$tab_rateUpdate->name 	= 'COM_IPDATA_EXCHANGERATE_UPDATER';
		$tab_rateUpdate->div 	= $div_rateUpdate;
		$tabs[4]				= $tab_rateUpdate;
		
		$script = 'jQuery(document).ready(function($) {
						var outerDiv = $(\'.admin\');
				
						$(\'<div id="loading"></div>\')
							.css("background", "rgba(255, 255, 255, .8) url(\'../media/jui/img/ajax-loader.gif\') 50% 15% no-repeat")
							.css("top", outerDiv.position().top - $(window).scrollTop())
							.css("left", outerDiv.position().left - $(window).scrollLeft())
							.css("width", outerDiv.width())
							.css("height", outerDiv.height())
							.css("position", "fixed")
							.css("opacity", "0.90")
							.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
							.css("filter", "alpha(opacity = 90)")
							.css("display", "none")
							.appendTo(outerDiv);
					});
					function pleaseWait()
					{
						jQuery(\'#loading\').css(\'display\', \'block\');
					}';
		// get the document and add script to page
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration($script);
			
		$mainframe = JFactory::getApplication();
		//Trigger Event - ipdata_bk_onBefore_cPanel_display
		$mainframe->triggerEvent('ipdata_bk_onBefore_cPanel_display',array('tabs'=>&$tabs));
		
		return $tabs;
	}
	
	public function getTabactive()
	{
		switch($this->tab_id){
			case 0:
			return 'cpanel';
			break;
			case 1:
			return 'json_api';
			break;
			case 2:
			return 'ip_update';
			break;
			case 3:
			return 'rate_update';
			break;
			default:
			return 'cpanel';
			break;
		}
		
	}
	
	protected function setIcons()
	{
		// setup icons
		$icons 					= array();
		// API
		$api_doc 				= new stdClass();	
		$api_doc->other			= 'data-toggle="tab" onclick="changeTab(\'json_api\');"';
		$api_doc->url 			= '#json_api';
		$api_doc->name 			= 'COM_IPDATA_API_DOC';
		$api_doc->title 		= 'COM_IPDATA_API_DOC_DESC';
		$api_doc->image 		= 'administrator/components/com_ipdata/assets/images/icons/api_doc.png';
		$icons[0]				= $api_doc;
		// IP Updater
		$ip_updater 			= new stdClass();	
		$ip_updater->other		= 'data-toggle="tab" onclick="changeTab(\'ip_update\');"';
		$ip_updater->url 		= '#ip_update';
		$ip_updater->name 		= 'COM_IPDATA_IP_UPDATER';
		$ip_updater->title 		= 'COM_IPDATA_IP_UPDATER_DESC';
		$ip_updater->image 		= 'administrator/components/com_ipdata/assets/images/icons/ip_updater.png';
		$icons[1]				= $ip_updater;
		// Exchange Rate Updater
		$xrate_updater 			= new stdClass();
		$xrate_updater->other	= 'data-toggle="tab" onclick="changeTab(\'rate_update\');"';
		$xrate_updater->url 	= '#rate_update';
		$xrate_updater->name 	= 'COM_IPDATA_EXCHANGERATE_UPDATER';
		$xrate_updater->title 	= 'COM_IPDATA_EXCHANGERATE_UPDATER_DESC';
		$xrate_updater->image 	= 'administrator/components/com_ipdata/assets/images/icons/rate_updater.png';
		$icons[2]				= $xrate_updater;
		// Coutry
		$icon_countries 		= new stdClass();	
		$icon_countries->other	= '';
		$icon_countries->url 	= 'index.php?option=com_ipdata&view=countries';
		$icon_countries->name 	= 'COM_IPDATA_COUNTRIES';
		$icon_countries->title 	= 'COM_IPDATA_COUNTRIES_DESC';
		$icon_countries->image 	= 'administrator/components/com_ipdata/assets/images/icons/countries.png';
		$icons[3]				= $icon_countries;
		// Currency
		$icon_currencies 		= new stdClass();	
		$icon_currencies->other	= '';	
		$icon_currencies->url 	= 'index.php?option=com_ipdata&view=currencies';
		$icon_currencies->name 	= 'COM_IPDATA_CURRENCIES';
		$icon_currencies->title = 'COM_IPDATA_CURRENCIES_DESC';
		$icon_currencies->image = 'administrator/components/com_ipdata/assets/images/icons/currencies.png';
		$icons[4]				= $icon_currencies;
		// First check user access
		$canDo = JHelperContent::getActions('com_ipdata', 'ipdata');
		if ($canDo->get('core.admin')) {
			// setu the return url
			$uri = (string) JUri::getInstance();
			$return = urlencode(base64_encode($uri));
			// Global Settings
			$global_settings 			= new stdClass();	
			$global_settings->other		= '';	
			$global_settings->url 		= 'index.php?option=com_config&amp;view=component&amp;component=com_ipdata&amp;path=&amp;return=' . $return;
			$global_settings->name		= 'COM_IPDATA_OPTIONS';
			$global_settings->title		= 'COM_IPDATA_OPTIONS_DESC';
			$global_settings->image		= 'administrator/components/com_ipdata/assets/images/icons/options.png';
			$icons[111]					= $global_settings;
		}
		
		$mainframe = JFactory::getApplication();
		// Trigger Event - ipdata_bk_onBefore_icon_display
		$mainframe->triggerEvent('ipdata_bk_onBefore_icon_display',array('icons'=>&$icons));
		
		// setup template
		$temp = '';
		foreach($icons as $icon){
			$temp .= '<div class="dashboard-wraper"><div class="dashboard-content"><a class="icon hasTip" '.$icon->other.' href="'.$icon->url.'" title="';
			$temp .= JText::_($icon->title);
			$temp .= '">';
            $temp .= JHTML::_('image', $icon->image, JText::_($icon->name));
            $temp .= '<span class="dashboard-title">';
			$temp .= JText::_($icon->name);
			$temp .= '</span></a></div></div>';
        }
		return $temp;
	}
	
	protected function getAPIkey()
	{
		if($this->app_params->get('api_access') > 0){
			$privateKey = $this->app_params->get('api_privatekey');
			if(strlen($privateKey) > 0){
				switch($this->app_params->get('api_access')){
					case 1:
					// only private key
					return md5($privateKey);
					break;
					case 2:
					// all users					
					return md5(JFactory::getUser()->username.'_'.$privateKey);
					break;
					case 3:
					// only users in certain groups
					if(is_array($this->app_params->get('api_accessgroup'))){
						$ids = $this->getUserIdsInGroups($this->app_params->get('api_accessgroup'));
						if(is_array($ids)){
							$users = $this->getUserNames($ids);
							if(is_array($users)){
								foreach($users as $user){
									return md5($user.'_'.$privateKey);
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
										return md5($user.'_'.$privateKey);
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
							return md5($user.'_'.$privateKey);
						}
					}						
					break;
				}
			}
		}

		return 0;
	}
	
	protected function getWorkers()
	{
		$workForce = array();
		// get all workers
		$workers = range(1,4);
		foreach($workers as $nr){
			if($this->app_params->get("showWorker".$nr) == 1 || $this->app_params->get("showWorker".$nr) == 3){
				if($this->app_params->get("useWorker".$nr) == 1){
					$link_front = '<a href="mailto:'.$this->app_params->get("emailWorker".$nr).'" target="_blank">';
					$link_back = '</a>';
				} elseif($this->app_params->get("useWorker".$nr) == 2) {
					$link_front = '<a href="'.$this->app_params->get("linkWorker".$nr).'" target="_blank">';
					$link_back = '</a>';
				} else {
					$link_front = '';
					$link_back = '';
				}
				$workForce[] = $this->app_params->get("titleWorker".$nr).' '.$link_front.$this->app_params->get("nameWorker".$nr).$link_back;
			}
		}
		return $workForce;
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
