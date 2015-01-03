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

// import Joomla controller library
jimport('joomla.application.component.controller');

class IpdataControllerUpdate extends JControllerLegacy
{
	protected $pluginParams = false;
	
	public function __construct($config)
	{
		parent::__construct($config);
		// load the tasks
		$this->registerTask('iptable', 'update');
		$this->registerTask('rates', 'update');
	}
	
	public function update()
	{
		$jinput		= JFactory::getApplication()->input;
		$task		= $this->getTask();
		
		// update the IP Table
		if ($task == 'iptable'){
			$key = $jinput->get('key', 0, 'ALNUM');
			if(JSession::getFormToken() == $key){
				$active = $jinput->get('active', 0, 'INT');
				if($active == 1){
					// get plugin params
					$plugin = JPluginHelper::getPlugin('system', 'ipdataupdatecron');
					if($plugin->params){
						$this->pluginParams = new JRegistry($plugin->params);
					}
					if($this->pluginParams){
						// get timer
						$time = $this->pluginParams->get('iptimer', '-1 day');
						if($this->setActiveState($time,'ipupdate') || $time == 0){
							if($this->unActive(1,'ipupdate')){
								// Import dependencies
								jimport('joomla.filesystem.file');
								// set cron keeper to stop multiple execution
								if($this->unActive(8,'ipupdate') && $this->unActive(2,'ipupdate')){
									if($this->active(2,'ipupdate')){
										if($this->upload('http://software77.net/geo-ip/?DL=2', 'IpToCountry.csv.zip', 'IpToCountry.csv')){
											// clear cron keeper
											$this->removeQ(2,'ipupdate');
										} else {
											// clear cron keeper
											$this->removeQ(2,'ipupdate');
											$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Upload Error, please try again later!'), 'error');
											return false;
										}
									} 
									$config   = JFactory::getConfig();
									$tmp_dest = $config->get('tmp_path');
									if(file_exists($tmp_dest.'/IpToCountry.csv')){
										// set cron keeper to stop multiple execution
										if($this->active(8,'ipupdate')){
											if($this->upDateIpTable($tmp_dest.'/IpToCountry.csv')){
												// clear cron keeper and set new timer
												if($this->removeQ(8,'ipupdate') && $this->active(1,'ipupdate')){
													$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('IP Table Was Successfully Updated!'));
													return true;
												}
												$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('There was an error, please try again later!'), 'error');
												return false;
											} else {
												// clear cron keeper
												$this->removeQ(8,'ipupdate');
												$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Could not read CSV file, please manually remove the file from temp folder and try again!'), 'error');
												return false;
											}
										}
									} else {
										// clear upload switch since file is not there.
										$this->removeQ(2,'ipupdate');
										$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Upload Error, please try again later!'), 'error');
										return false;
									}
								}
								$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Another update is still in progress!'), 'error');
								return false;
							}
						}
						$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('To soon! check the (IP Data Update Cron Timer) in System - IP Data Cron plugin.'), 'error');
						return false;
					} else {
						// Import dependencies
						jimport('joomla.filesystem.file');
						// upload ip Data
						$this->upload('http://software77.net/geo-ip/?DL=2', 'IpToCountry.csv.zip', 'IpToCountry.csv');
						
						$config   = JFactory::getConfig();
						$tmp_dest = $config->get('tmp_path');
						if(file_exists($tmp_dest.'/IpToCountry.csv')){
							// do update
							if($this->upDateIpTable($tmp_dest.'/IpToCountry.csv')){
								$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('IP Table Was Successfully Updated!'));
								return true;
							} else {
								$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Could not read CSV file, please manually remove the file from temp folder and try again!'), 'error');
								return false;
							}
						}
					}
				} else {
					// Import dependencies
					jimport('joomla.filesystem.file');
					// upload ip Data
					$this->upload('http://software77.net/geo-ip/?DL=2', 'IpToCountry.csv.zip', 'IpToCountry.csv');
					
					$config   = JFactory::getConfig();
					$tmp_dest = $config->get('tmp_path');
					if(file_exists($tmp_dest.'/IpToCountry.csv')){
						// do update
						if($this->upDateIpTable($tmp_dest.'/IpToCountry.csv')){
							$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('IP Table Was Successfully Updated!'));
							return true;
						} else {
							$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('Could not read CSV file, please manually remove the file from temp folder and try again!'), 'error');
							return false;
						}
					}
				}
			}
			$this->setRedirect('index.php?option=com_ipdata&tab=2',JText::_('There was an error, please try again!'), 'error');
			return false;
		}
		
		// update the exchange rates
		if ($task == 'rates'){
			$key = $jinput->get('key', 0, 'ALNUM');
			if(JSession::getFormToken() == $key){
				$active = $jinput->get('active', 0, 'INT');
				if($active == 1){
					// get plugin params
					$plugin = JPluginHelper::getPlugin('system', 'ipdataupdatecron');
					if($plugin->params){
						$this->pluginParams = new JRegistry($plugin->params);
					}
					if($this->pluginParams){
						// get timer
						$time = $this->pluginParams->get('ratetimer', '-1 day');
						// set cron keeper to stop multiple execution
						if($this->setActiveState($time,'rateupdate') || $time == 0){
							if($this->unActive(1,'rateupdate')){
								if($this->unActive(8,'rateupdate')){
									if($this->active(8,'rateupdate')){
										if($this->upDateRates()){
											// clear cron keeper
											$this->removeQ(8,'rateupdate');
											// set active keeper
											$this->active(1,'rateupdate');
											// set message and redirect
											$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Exchange Rates Was Successfully Updated!'));
											return true;
										} else {
											// clear cron keeper
											$this->removeQ(8,'rateupdate');
											// set message and redirect
											$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Update failed, please check that there are currencies publised!'), 'error');
											return false;
										}
									}
									$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Could not start update!'), 'error');
									return false;
								}
								$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Another update is still in progress!'), 'error');
								return false;
							}
							$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('To soon! check the (Exchange Rate Update Cron Timer) in System - IP Data Cron plugin.'), 'error');
							return false;
						}
					} else {
						// do update
						if($this->upDateRates()){
							// set message and redirect
							$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Exchange Rates Was Successfully Updated!'));
							return true;
						} else {
							// set message and redirect
							$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Update failed, please check that there are currencies publised!'), 'error');
							return false;
						}
					}
				} else {
					// do update
					if($this->upDateRates()){
						// set message and redirect
						$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Exchange Rates Was Successfully Updated!'));
						return true;
					} else {
						// set message and redirect
						$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('Update failed, please check that there are currencies publised!'), 'error');
						return false;
					}
				}
			}
			$this->setRedirect('index.php?option=com_ipdata&tab=3',JText::_('There was an error, please try again!'), 'error');
			return false;
		}
		$this->setRedirect('index.php?option=com_ipdata',JText::_('There was an error, please try again!'), 'error');
		return false;
	}
	
	/**
	* Update the DB with new data
	*
	*
	* @retunr a bool
	*
	**/
	protected function upDateIpTable($filename)
	{		
		// set the data
		if(($handle = fopen($filename, 'r')) !== false )
		{
			// Get a db connection.
			$db = JFactory::getDbo();
			// clear table from old data
			$db->setQuery("TRUNCATE TABLE `#__ipdata`");
			$db->execute();
			$counter = 0;
			$values = array();
			// loop through the file line-by-line
			while(($data = fgetcsv($handle)) !== false)
			{	
				// remove comments and ...
				$check = implode(",", $data);	
				if (substr($check, 0, 1) == '#') continue;
				// now we have the correct data one more check
				if (count($data) < 7) continue;
				// Insert values.
				$values[] = array($db->quote($data[0]), $db->quote($data[1]), $db->quote($data[2]), $db->quote($data[3]), $db->quote($data[4]), $db->quote($data[5]));
				// set counter
				$counter++;
				// set to db
				if($counter == 400){
					// Create a new query object.
					$query = $db->getQuery(true);
					 
					// Insert columns.
					$columns = array('IP_FROM', 'IP_TO', 'REGISTRY', 'ASSIGNED', 'CTRY', 'CNTRY');
					 
					// Prepare the insert query.
					$query->insert($db->quoteName('#__ipdata'));
					$query->columns($db->quoteName($columns));
					foreach($values as $value){
						$query->values(implode(',', $value));
					}
					// clear the values array
					unset($values);
					$values = array();
					// echo nl2br(str_replace('#__','api_',$query)); die;
					// Set the query using our newly populated query object and execute it.
					$db->setQuery($query);
					$db->execute();
					// rest counter
					$counter = 0;
				}
				// clear values
				unset($data);
			}
			fclose($handle);
			// load the last values that are less than 400
			if($counter > 0){
				// Create a new query object.
				$query = $db->getQuery(true);
				 
				// Insert columns.
				$columns = array('IP_FROM', 'IP_TO', 'REGISTRY', 'ASSIGNED', 'CTRY', 'CNTRY');
				 
				// Prepare the insert query.
				$query->insert($db->quoteName('#__ipdata'));
				$query->columns($db->quoteName($columns));
				foreach($values as $value){
					$query->values(implode(',', $value));
				}
				// clear the values array
				unset($values);
				// echo nl2br(str_replace('#__','api_',$query)); die;
				// Set the query using our newly populated query object and execute it.
				$db->setQuery($query);
				$db->execute();
				// rest counter
				$counter = 0;
			}
			// clear the uploaded files
			JFile::delete($filename);
			return true;
		}
		return false;
	}
	
	 /**
	 * upload the latest IpToCountry.csv.
	 *
	 * @return  a bool
	 *
	 */
	protected function upload($url,$filename,$csv)
	{
		// Did you give us a URL?
		if (!$url){
			return false;
		}
		
		$config   = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path');
		
		if (file_exists($tmp_dest.'/'.$csv)) {
			// already uploaded
			return false;
		}
		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url,$filename);
		// Was the package downloaded?
		if (!$p_file){
			return false;
		}

		// Unpack the downloaded package file
		$zip = new ZipArchive;
		$res = $zip->open($tmp_dest.'/'.$p_file);
		if ($res === TRUE) {
			$zip->extractTo($tmp_dest);
			$zip->close();
			// remove zip file
			return JFile::delete($tmp_dest.'/'.$p_file);
		}
		JFile::delete($tmp_dest.'/'.$p_file);
		return false;
	}	
	
	protected function upDateRates()
	{	
		$currencies = $this->getCurrencies();
		// get creation date
		$date_created = JFactory::getDate()->toSql();
		// set the data
		if(is_array($currencies)){
			// Get a db connection.
			$db = JFactory::getDbo();
			// Insert columns.
			$columns = array('name', 'nametoname', 'from', 'to', 'rate', 'date_rate', 'ask', 'bid', 'published', 'date_created');
			// set buket value arrays
			$values 	= array();
			$buket 		= array();
			$oldValues 	= array();
			foreach($currencies as $currency){
				$buket[] = $currency;
				if(count($buket) == 2){
					// get the rates from yahoo
					$rates = $this->getRates($buket,$currencies);
					unset($buket);
					$buket = array();
					if(is_array($rates)){
						foreach($rates as $rate){
							if(strpos($rate->Name,'=X') === false){
								// to get this date format 0000-00-00 00:00:00
								$date_rate	= date("Y-m-d", strtotime($rate->Date));
								$date_rate	.= ' '.date("H:i:s", strtotime($rate->Time));
								// get the from and to value
								list($from,$to) = explode(' to ',$rate->Name);
								// now set the values
								$values[] = array(
												$db->quote($rate->id),
												$db->quote($rate->Name),
												$db->quote(trim($from)),
												$db->quote(trim($to)),
												$db->quote($rate->Rate),
												$db->quote($date_rate),
												$db->quote($rate->Ask),
												$db->quote($rate->Bid),
												$db->quote(1),
												$db->quote($date_created));
								$oldValues[] = $rate->id;
							}
						}
						// clear rates
						unset($rates);
						if(count($values) > 0){
							// set the old values
							$this->setOldRates($oldValues);
							unset($oldValues);
							$oldValues = array();
							// Create a new query object.
							$query = $db->getQuery(true);
							 
							// Prepare the insert query.
							$query->insert($db->quoteName('#__ipdata_exchangerate'));
							$query->columns($db->quoteName($columns));
							foreach($values as $value){
								$query->values(implode(',', $value));
							}
							// clear the values array
							unset($values);
							$values = array();
							// echo nl2br(str_replace('#__','api_',$query)); die;
							// Set the query using our newly populated query object and execute it.
							$db->setQuery($query);
							$db->execute();
						}
					} else {
						// clear rates
						unset($rates);
					}
				}
			}
			// check if last set was processed
			if(count($buket) > 0){
				// get the rates from yahoo
				$rates = $this->getRates($buket,$currencies);
				unset($buket);
				if(is_array($rates)){
					foreach($rates as $rate){
						if(strpos($rate->Name,'=X') === false){
							// to get this date format 0000-00-00 00:00:00
							$date_rate	= date("Y-m-d", strtotime($rate->Date));
							$date_rate	.= ' '.date("H:i:s", strtotime($rate->Time));
							// get the from and to value
							list($from,$to) = explode(' to ',$rate->Name);
							// now set the values
							$values[] = array(
											$db->quote($rate->id),
											$db->quote($rate->Name),
											$db->quote(trim($from)),
											$db->quote(trim($to)),
											$db->quote($rate->Rate),
											$db->quote($date_rate),
											$db->quote($rate->Ask),
											$db->quote($rate->Bid),
											$db->quote(1),
											$db->quote($date_created));
							$oldValues[] = $rate->id;
						}
					} 
					// clear rates
					unset($rates);
					if(count($values) > 0){
						// set the old values
						$this->setOldRates($oldValues);
						unset($oldValues);
						$oldValues = array();
						// Create a new query object.
						$query = $db->getQuery(true);
						 
						// Prepare the insert query.
						$query->insert($db->quoteName('#__ipdata_exchangerate'));
						$query->columns($db->quoteName($columns));
						foreach($values as $value){
							$query->values(implode(',', $value));
						}
						// clear the values array
						unset($values);
						$values = array();
						// echo nl2br(str_replace('#__','api_',$query)); die;
						// Set the query using our newly populated query object and execute it.
						$db->setQuery($query);
						$db->execute();
					}
				} else {
					// clear rates
					unset($rates);
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	* get the rates from yahoo
	*
	* @retunr a bool
	*
	**/
	protected function getRates($bases,$currencies)
	{	
		// main currancies
		$main = array(	JComponentHelper::getParams('com_ipdata')->get('base_currency'),
						'USD','EUR','JPY','GBP','AUD','CHF','CAD','MXN','CNY','NZD','SEK','RUB','HKD','SGD','TRY','INR','BRL','NOK','DKK','ILS','KRW','ZAR');
		$main = array_keys(array_flip($main));
		// build query string
		$query				= array();
		$main_currencies	= array();
		foreach($bases as $base){
			foreach($currencies as $currancy){
				if($currancy != $base){
					if(in_array($base,$main)){
						$main_currencies[$base][] = $base.$currancy;
					} else {
						$query[] = $base.$currancy;
					}
				}
			}
		}
		// take more detailed calles to main currencies
		if(count($main_currencies) > 0){
			$buket		= array();
			$result		= array();
			foreach($main_currencies as $main_base => $relations){
				foreach($relations as $relation){
					$buket[] = $relation;
					if(count($buket) > 5){
						// build query API url
						$app_uri = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22".implode('%22,%22', $buket)."%22%29&format=json&env=store://datatables.org/alltableswithkeys&callback=";
						unset($buket);
						$buket = array();
						// get the data
						$data = @file_get_contents($app_uri);
						if(strlen($data) > 0){
							// store the result object
							$result = array_merge($result,json_decode(trim($data))->query->results->rate);
						}
					}
				}
			}
			unset($main_currencies);
			if(count($buket) > 0){
				// build query API url
				$app_uri = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22".implode('%22,%22', $buket)."%22%29&format=json&env=store://datatables.org/alltableswithkeys&callback=";
				unset($buket);
				$buket = array();
				// get the data
				$data = @file_get_contents($app_uri);
				if(strlen($data) > 0){
					// store the result object
					$result = array_merge($result,json_decode(trim($data))->query->results->rate);
				}
			}
			if(count($query) > 0){
				// build query API url
				$app_uri = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22".implode('%22,%22', $query)."%22%29&format=json&env=store://datatables.org/alltableswithkeys&callback=";
				unset($query);
				$buket = array();
				// get the data
				$data = @file_get_contents($app_uri);
				if(strlen($data) > 0){
					// store the result object
					$result = array_merge($result,json_decode(trim($data))->query->results->rate);
				}
			}
			if(count($result) > 0){
				// return the result object
				return $result;
			}
		} else {
			// build query API url
			$app_uri    = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28%22".implode('%22,%22', $query)."%22%29&format=json&env=store://datatables.org/alltableswithkeys&callback=";
			// get the data
			$data = @file_get_contents($app_uri);
			unset($query);
			unset($detailed);
			if(strlen($data) > 0){
				// return the result object
				return json_decode(trim($data))->query->results->rate;
			}
		}
		return false;
	}
	
	 /**
	 * Turn active update off if older then ser time or remove.
	 *
	 * @return  a bool
	 *
	 */
	protected function setOldRates($names)
	{
		if (is_array($names)){
			if($this->pluginParams){
				$history = $this->pluginParams->get('ratehistory', 0);
				if($history == 0){
					// Get a db connection.
					$db = JFactory::getDbo();
					// now remove old rates
					$query = $db->getQuery(true);
					// Conditions for which records should be deleted.
					$conditionsDe = array( 
						$db->quoteName('name').' IN (\'' . implode("','", $names) . '\')'
					);					
					$query->delete($db->quoteName('#__ipdata_exchangerate'));
					$query->where($conditionsDe);
					$db->setQuery($query);
					return $db->execute();
				} elseif ($history == 1){
					// Get a db connection.
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					// Fields to update.
					$fields = array(
						$db->quoteName('published') . ' = 0'
					);
					// Conditions for which records should be updated.
					$conditionsUn = array(
						$db->quoteName('published') . ' != 0', 
						$db->quoteName('name').' IN (\'' . implode("','", $names) . '\')'
					);
					// Check table
					$query->update($db->quoteName('#__ipdata_exchangerate'))->set($fields)->where($conditionsUn); 
					$db->setQuery($query);
					return $db->execute();
				} else {
					if ($history){
						// Get a db connection.
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						// Fields to update.
						$fields = array(
							$db->quoteName('published') . ' = 0'
						);
						// Conditions for which records should be updated.
						$conditionsUn = array(
							$db->quoteName('published') . ' != 0', 
							$db->quoteName('name').' IN (\'' . implode("','", $names) . '\')'
						);
						// Check table
						$query->update($db->quoteName('#__ipdata_exchangerate'))->set($fields)->where($conditionsUn); 
						$db->setQuery($query);
						$db->execute();
						// now remove those that do not belong in history
						$query = $db->getQuery(true);
						// Get date in sql
						$date =& JFactory::getDate()->modify($history)->toSql();
						// Conditions for which records should be deleted.
						$conditionsDe = array(
							$db->quoteName('date_created') . ' < '.$db->quote($date), 
							$db->quoteName('name').' IN (\'' . implode("','", $names) . '\')'
						);					
						$query->delete($db->quoteName('#__ipdata_exchangerate'));
						$query->where($conditionsDe);
						$db->setQuery($query);
						return $db->execute();
					}
				}
			} else {
				// Get a db connection.
				$db = JFactory::getDbo();
				// now remove old rates
				$query = $db->getQuery(true);
				// Conditions for which records should be deleted.
				$conditionsDe = array( 
					$db->quoteName('name').' IN (\'' . implode("','", $names) . '\')'
				);					
				$query->delete($db->quoteName('#__ipdata_exchangerate'));
				$query->where($conditionsDe);
				$db->setQuery($query);
				return $db->execute();
			}
		}
		return false;
	}
	
	 /**
	 * get all currancies.
	 *
	 * @return  a array
	 *
	 */
	protected function getCurrencies()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('codethree'));
		$query->from('#__ipdata_currency');
		$query->where($db->quoteName('published')." = 1");
		$query->order('ordering ASC');
		$db->setQuery($query);
		return $db->loadColumn();
	}
	 
	 /**
	 * Turn active update off if older then ser time.
	 *
	 * @return  a bool
	 *
	 */
	protected function setActiveState($time,$table)
	{
		if ($time){
			// Get date in sql
			$date =& JFactory::getDate()->modify($time)->toSql();	
	
			// Get a db connection.
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			 
			// Fields to update.
			$fields = array(
				$db->quoteName('active') . ' = 0'
			);
			 
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('active') . ' != 0', 
				$db->quoteName('date') . ' < '.$db->quote($date)
			);
			
			// Check table
			$query->update($db->quoteName('#__ipdata_'.$table.'_cron'))->set($fields)->where($conditions); 
				 
			$db->setQuery($query);
			 
			return $db->query();
		}
		return false;
	}
	
	/**
	*	Set the cron to active
	*
	*	@returns void
	**/
	protected function active($state, $table)
	{
		// Create and populate an object.
		$cron 			= new stdClass();
		$cron->date		= JFactory::getDate()->toSql();
		$cron->active	= $state;
		
		// Insert the object into the activity cron table.
		return JFactory::getDbo()->insertObject('#__ipdata_'.$table.'_cron', $cron);
	}
	
	 /**
	 * Check if a cron is un Active.
	 *
	 * @return  a bool
	 *
	 */
	protected function unActive($state, $table)
	{
		if($state){
			// Get a db connection.
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__ipdata_'.$table.'_cron'));
			$query->where($db->quoteName('active')." = ".$state);
			$db->setQuery($query);
			$db->execute();
			if($db->getNumRows()){
				return false;
			}
		}
		return true;
	}
	
	 /**
	 * Remove all quied kaapers
	 *
	 * @return  a bool
	 *
	 */
	protected function removeQ($state, $table)
	{
		if ($state){
			$db = JFactory::getDbo();
 			$query = $db->getQuery(true);
			// delete all crons of a given state.
			$conditions = array(
				$db->quoteName('active').' = '.$state
			);
			$query->delete($db->quoteName('#__ipdata_'.$table.'_cron'));
			$query->where($conditions);
			$db->setQuery($query);
			return $db->execute();
		}
		return false;
	}
}
