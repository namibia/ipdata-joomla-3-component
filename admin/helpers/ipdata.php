<?php
/**
*
*	phpIp2Country class
* 
*	@author Mariusz Górski
*	@copyright 2008 Mariusz Górski
*	@name phpIp2Country
*	@version 1.0
*	@link http://code.google.com/p/php-ip-2-country/
*
* 
* 	@version 	1.0.0  December 11, 2014
* 	@package 	Ip Data API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@adapted	class phpIp2Country to class Ipdata helper class for Joomla 3
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
*
**/

class IpdataHelper {

	/**
	 * IP address
	 *
	 * @var string
	 */
	public $ip = '';
	
	/**
	 * Base currency
	 *
	 * @var string
	 */
	public $cbase = false;
	
	/**
	 * Numerical representation of IP address
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 * @var integer
	 */
	private $ipValue = NULL;
	
	
	/**
	 * IP address in form of array of integer values
	 *
	 * @var string
	 */
	private $ipArr = array();
	
	/**
	 * IP address information array
	 *
	 * @var string
	 */
	private $ipInfoArr = false;
	/**
	 * @param string $ip
	 * @param ip $method
	 */
	 
	function __construct($ip,$cBase){
		if(!$this->checkIpAddr($ip)){
			return false;
			// die('Bad IP address! Should be in xxx.xxx.xxx.xxx format!');
		} else {
			$this->ip = $ip;
			if(strlen($cBase) > 0){
				$this->cbase = $cBase;
			}
		}
		$this->ipArr = $this->getIpArr();
		$this->ipValue = $this->getIpValue();
			
		$this->ipInfoArr = $this->getIpdata();
		
		if(!$this->ipInfoArr){
			return false;
		}else{
			$this->ipInfoArr['IP_STR'] = $this->ip;
			$this->ipInfoArr['IP_VALUE'] = (string) $this->ipValue;
			$this->ipInfoArr['IP_FROM_STR'] = $this->getIpFromValue($this->ipInfoArr['IP_FROM']);
			$this->ipInfoArr['IP_TO_STR'] = $this->getIpFromValue($this->ipInfoArr['IP_TO']);
			$exchangeRate = $this->getExchangeRate($cBase,$this->ipInfoArr['CURRENCY_CODE_THREE']);
			if(is_array($exchangeRate)){
				$this->ipInfoArr = array_merge((array)$this->ipInfoArr, (array)$exchangeRate);
			}
		}
	}
	
	
	/**
	*
	*	returns information about IP adrress
	*
	*	@param integer $mode
	*	@return mixed
	*
	**/
	public function getInfo($mode = 0, $string = 0){
		if(!in_array($mode,range(1, 26))){
			// return all
			return $this->ipInfoArr;
		} else {
			// the return values of mode
			$get = array(	1 => 'IP_STR',
							2 => 'IP_VALUE',
							3 => 'IP_RANGE_NUMERICAL',
							4 => 'IP_RANGE',
							5 => 'IP_REGISTRY',
							6 => 'IP_ASSIGNED_UNIXTIME',
							7 => 'COUNTRY_ALL',
							8 => 'COUNTRY_NAME',
							9 => 'COUNTRY_CODE_TWO',
							10 => 'COUNTRY_CODE_THREE',
							11 => 'CURRENCY_ALL',
							12 => 'CURRENCY_NAME',
							13 => 'CURRENCY_CODE_THREE',
							14 => 'CURRENCY_CODE_NUMERIC',
							15 => 'CURRENCY_SYMBOL',
							16 => 'CURRENCY_DECIMAL_PLACE',
							17 => 'CURRENCY_DECIMAL_SYMBOL',
							18 => 'CURRENCY_POSITIVE_STYLE',
							19 => 'CURRENCY_NEGATIVE_STYLE',
							20 => 'EXCHANGE_RATE_ALL',
							21 => 'EXCHANGE_RATE_ID',
							22 => 'EXCHANGE_RATE_NAME',
							23 => 'EXCHANGE_RATE',
							24 => 'EXCHANGE_RATE_ASK',
							25 => 'EXCHANGE_RATE_BID',
							26 => 'EXCHANGE_RATE_DATE');			
			switch($mode){
				case 3:
					return array(
						'FROM' => $this->ipInfoArr['IP_FROM'],
						'TO' => $this->ipInfoArr['IP_TO']
					);
					break;
				case 4:
					return array(
						'FROM' => $this->ipInfoArr['IP_FROM_STR'],
						'TO' => $this->ipInfoArr['IP_TO_STR']
					);
					break;
				case 7:
					return array(
						'COUNTRY_NAME' => $this->ipInfoArr['COUNTRY_NAME'],
						'COUNTRY_CODE_TWO' => $this->ipInfoArr['COUNTRY_CODE_TWO'],
						'COUNTRY_CODE_THREE' => $this->ipInfoArr['COUNTRY_CODE_THREE']
					);
					break;
				case 11:
					if(strlen($this->ipInfoArr['CURRENCY_NAME']) > 0){
						return array(
							'CURRENCY_NAME' => $this->ipInfoArr['CURRENCY_NAME'],
							'CURRENCY_CODE_NUMERIC' => $this->ipInfoArr['CURRENCY_CODE_NUMERIC'],
							'CURRENCY_CODE_THREE' => $this->ipInfoArr['CURRENCY_CODE_THREE'],
							'CURRENCY_SYMBOL' => $this->ipInfoArr['CURRENCY_SYMBOL'],
							'CURRENCY_DECIMAL_PLACE' => $this->ipInfoArr['CURRENCY_DECIMAL_PLACE'],
							'CURRENCY_DECIMAL_SYMBOL' => $this->ipInfoArr['CURRENCY_DECIMAL_SYMBOL'],
							'CURRENCY_POSITIVE_STYLE' => $this->ipInfoArr['CURRENCY_POSITIVE_STYLE'],
							'CURRENCY_NEGATIVE_STYLE' => $this->ipInfoArr['CURRENCY_NEGATIVE_STYLE']
						);
					}
					return false;
					break;
				case 20:
					if(strlen($this->ipInfoArr['EXCHANGE_RATE_ID']) > 0){
						return array(
							'EXCHANGE_RATE_ID' => $this->ipInfoArr['EXCHANGE_RATE_ID'],
							'EXCHANGE_RATE_NAME' => $this->ipInfoArr['EXCHANGE_RATE_NAME'],
							'EXCHANGE_RATE' => $this->ipInfoArr['EXCHANGE_RATE'],
							'EXCHANGE_RATE_ASK' => $this->ipInfoArr['EXCHANGE_RATE_ASK'],
							'EXCHANGE_RATE_BID' => $this->ipInfoArr['EXCHANGE_RATE_BID'],
							'EXCHANGE_RATE_DATE' => $this->ipInfoArr['EXCHANGE_RATE_DATE']
						);
					}
					return false;
					break;
				default:
					if($string){
						return $this->ipInfoArr[$get[$mode]];						
					} else {
						return array( $get[$mode] => $this->ipInfoArr[$get[$mode]] );
					}
					break;
			}
		}
	}
	
	/**
	 * validate IP address
	 *
	 * @param string $ip
	 * @return boolean
	 */
	private function checkIpAddr($ip=''){
		return preg_match('/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/i',$ip);
	}
	
	/**
	 * returns IP address in array of integer values
	 *
	 * @return array
	 */
	private function getIpArr(){
		$vars = explode('.',$this->ip);
		return array(
			intval($vars[0]),
			intval($vars[1]),
			intval($vars[2]),
			intval($vars[3])
		);
	}
	
	/**
	 * returns numerical representation of IP address.
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 *
	 * @return integer
	 */
	private function getIpValue(){
		return $this->ipArr[3] + ( $this->ipArr[2] * 256 ) + ( $this->ipArr[1] * 256 * 256 ) + ( $this->ipArr[0] * 256 * 256 * 256 );
	}
	
	/**
	 * returns IP numer from numerical representation.
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 *
	 * @param integer $value
	 * @param boolean $returnAsStr
	 * @return mixed
	 */
	private function getIpFromValue($value=0,$returnAsStr=true){
		$ip[0] = floor( intval($value) / (256*256*256) );
		$ip[1] = floor( ( intval($value) - $ip[0]*256*256*256 ) / (256*256) );
		$ip[2] = floor( ( intval($value) -$ip[0]*256*256*256 -$ip[1]*256*256 ) / 256 );
		$ip[3] = intval($value) - $ip[0]*256*256*256 - $ip[1]*256*256 - $ip[2]*256;
		if($returnAsStr){
			return $ip[0].'.'.$ip[1].'.'.$ip[2].'.'.$ip[3];
		}else{
			return $ip;
		}
	}
	
	/**
	 * returns IP Data from #__vdm_ipdata database
	 *
	 * @return string
	 */
	private function getIpdata(){
		
		$get = array(	'IP_FROM' => 'a.IP_FROM', 
						'IP_TO' => 'a.IP_TO', 
						'IP_REGISTRY' => 'a.REGISTRY',
						'IP_ASSIGNED' => 'a.ASSIGNED',
						'COUNTRY_NAME' => 'b.name',
						'COUNTRY_CODE_TWO' => 'b.codetwo',
						'COUNTRY_CODE_THREE' => 'a.CNTRY',
						'CURRENCY_NAME' => 'c.name',
						'CURRENCY_CODE_THREE' => 'c.codethree',
						'CURRENCY_CODE_NUMERIC' => 'c.numericcode',
						'CURRENCY_SYMBOL' => 'c.symbol',
						'CURRENCY_DECIMAL_PLACE' => 'c.decimalplace',
						'CURRENCY_DECIMAL_SYMBOL' => 'c.decimalsymbol',
						'CURRENCY_POSITIVE_STYLE' => 'c.positivestyle',
						'CURRENCY_NEGATIVE_STYLE' => 'c.negativestyle');
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array_values($get),array_keys($get)))
			->from($db->quoteName('#__ipdata', 'a'))
			->join('INNER', $db->quoteName('#__ipdata_country', 'b') . ' ON (' . $db->quoteName('a.CNTRY') . ' = ' . $db->quoteName('b.codethree') . ')')
			->join('INNER', $db->quoteName('#__ipdata_currency', 'c') . ' ON (' . $db->quoteName('b.currency') . ' = ' . $db->quoteName('c.codethree') . ')')
			->where($db->quoteName('a.IP_FROM') . ' <= '. $db->quote($this->ipValue))
			->where($db->quoteName('a.IP_TO') . ' >= '. $db->quote($this->ipValue));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	/**
	 * returns ExchangeRate from #__vdm_ipdata_exchangerate database
	 *
	 * @return string
	 */
	private function getExchangeRate($from,$to)
	{
		if($from){
			$get = array(	'EXCHANGE_RATE_ID' => 'a.name', 
							'EXCHANGE_RATE_NAME' => 'a.nametoname', 
							'EXCHANGE_RATE' => 'a.rate',
							'EXCHANGE_RATE_ASK' => 'a.ask',
							'EXCHANGE_RATE_BID' => 'a.bid',
							'EXCHANGE_RATE_DATE' => 'a.date_rate');
			// Get a db connection.
			$db = JFactory::getDbo();
			// Create a new query object.
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName(array_values($get),array_keys($get)))
				->from($db->quoteName('#__ipdata_exchangerate', 'a'))
				->where($db->quoteName('a.from') . ' = '. $db->quote($from))
				->where($db->quoteName('a.to') . ' = '. $db->quote($to))
				->where($db->quoteName('a.published') . ' = 1');
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			return $db->loadAssoc();
		}
		return false;
	}
}
