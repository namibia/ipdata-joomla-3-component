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

/**
 * Ip Data API component helper.
 */
abstract class GetHelper
{
	
	public static $currentVersion 	= false;
	public static $localVersion 	= false;
	
	/**
	 *	Load the Component xml manifests. 
	 */
	 protected static function setXML()
	 {
		// check if data is in session
		$session 		= JFactory::getSession();
		// $session->clear('get_xml_settings'); // to debug the session
		$currentVersion 	= $session->get('currentVersion', false);
		if($currentVersion !== false){
			$currentVersion 	= json_decode(base64_decode($currentVersion));
			// Parse the XML
			$local 					= @simplexml_load_file(JPATH_ADMINISTRATOR."/components/com_ipdata/ipdata.xml");
			self::$localVersion 	= $local->version;
			self::$currentVersion 	= $currentVersion;
		} else {
			// Parse the XML
			$local 			= @simplexml_load_file(JPATH_ADMINISTRATOR."/components/com_ipdata/ipdata.xml");
			$feed 			= @file_get_contents('https://www.vdm.io/updates/joomla_three.xml');
			$updates 		= @simplexml_load_string($feed);
			// load local version
			self::$localVersion 	= (string)$local->version;
			// set current version
			if(self::$localVersion !== false){
				list($localMain,$localDesign,$localTail) = explode('.', self::$localVersion);
				if(count($updates) > 0 && ($updates !== false)){
					foreach ($updates as $update){
						list($currentMain,$currentDesign,$currentTail) = explode('.', $update->version);
						if (($currentTail >= $localTail) || ($currentDesign > $localDesign) || ($currentMain > $localMain)){
							self::$currentVersion = (string)$update->version;
						}
					}
				} else {
					self::$currentVersion = false;
				}
			} else {
				self::$localVersion = false;
				self::$currentVersion = false;
			}
			// if both are set, then continue
			if(self::$currentVersion !== false && self::$localVersion !== false){
				// add to session to speedup the page load.
				$session->set('currentVersion', base64_encode(json_encode(self::$currentVersion)));
			}
		}
	}
	
	public static function update(){
		// set xml info
		self::setXML();
		// check if we must update
		if(self::$currentVersion  !== false && self::$localVersion !== false ){
			$local 		= (int)str_replace('.', '', self::$localVersion);
			$current 	= (int)str_replace('.', '', self::$currentVersion);
			if($local !== $current){
				if($local < $current){
					$notice = "You are still on version(" . self::$localVersion ."). Get the latest ip Data version(" . self::$currentVersion .") <a class=\"btn btn-mini\"  href=\"". JRoute::_( 'index.php?option=com_installer&view=update', false )."\">Upgrade Now!</a> it only gets better.";
					JFactory::getApplication()->enqueueMessage($notice, 'notice');
					return true;
				}
			}
		}
		return false;
	}
	
	public static function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}

	/**
	 *	Get the actions
	 */
	public static function getActions($Id = 0, $type = 'component')
	{
		jimport('joomla.access.access');

		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($Id)){
			$assetName = 'com_ipdata';
		} else {
			$assetName = 'com_ipdata.'.(string) $type.'.'.(int) $Id;
		};

		$actions = JAccess::getActions('com_ipdata', $type);

		foreach ($actions as $action){
			$result->set($action->name, $user->authorise($action->name, $assetName));
		};

		return $result;
	}
	
	/**
	 *	Get the Exchange Rate
	 *
	 * @param   string  $currency   The currency to target.
	 * @param   integer $mode		The mode of return result.
	 * @param   integer	$direction	The relationship direction.
	 *
	 * MODE 1 = Base
	 * MODE 2 = Main Currecies
	 * MODE 3 = All Currecies
	 *
	 * DIRECTION 1 = $currency	<- 	MODE FROM
	 * DIRECTION 2 = $currency	-> 	MODE TO
	 * DIRECTION 3 = $currency	<->	MODE FROM & TO
	 *
	 * TYPE 1 = EXCHANGE_RATE
	 * TYPE 2 = EXCHANGE_RATE, EXCHANGE_RATE_ASK, EXCHANGE_RATE_BID
	 * TYPE 3 = EXCHANGE_RATE, EXCHANGE_RATE_ASK, EXCHANGE_RATE_BID, EXCHANGE_RATE_DATE
	 *
	 */
	public static function getExchangeRate($currency, $mode = 1, $direction = 1, $type = 1)
	{
		switch($mode){
			case 1:
			// Get Base Currency
			$currencies = array(JComponentHelper::getParams('com_ipdata')->get('base_currency'));
			break;
			case 2:
			// Get Main Currencies
			$currencies = array(JComponentHelper::getParams('com_ipdata')->get('base_currency'),
								'USD','EUR','JPY','GBP','AUD','CHF','CAD','MXN','CNY','NZD','SEK','RUB','HKD','SGD','TRY','INR','BRL','NOK','DKK','ILS','KRW','ZAR');
			$currencies = array_keys(array_flip($currencies));
			break;
			case 3:
			// Get All Published Currecies
			$currencies = self::getCurrencies();
			break;
		}
		if(is_array($currencies)){
			foreach($currencies as $mode){
				switch($direction){
					case 1:
					// $currency <- MODE
					$exchangeRate['FROM'][$mode]	= self::setExchangeRate( $mode, $currency, $type);
					break;
					case 2:
					// $currency -> MODE
					$exchangeRate['TO'][$mode]		= self::setExchangeRate( $currency, $mode, $type);
					break;
					case 3:
					// $currency <-> MODE
					$exchangeRate['FROM'][$mode]	= self::setExchangeRate( $mode, $currency, $type);
					$exchangeRate['TO'][$mode]		= self::setExchangeRate( $currency, $mode, $type);
					break;
				}
			}
			return $exchangeRate;
		}
		return false;
	}
	
	protected static function getCurrencies()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__ipdata_currency.codethree');
		$query->from('#__ipdata_currency');
		$query->where('#__ipdata_currency.published = 1');
		$query->order('#__ipdata_currency.ordering ASC');
		$db->setQuery((string)$query);
		return  $db->loadColumn();
	}
	
	/**
	 * returns ExchangeRate from #__vdm_ipdata_exchangerate database
	 *
	 * @param   string  $from	The currency from.
	 * @param   string  $to		The currency to.
	 * @param   integer	$type	The return options
	 *
	 * TYPE 1 = EXCHANGE_RATE
	 * TYPE 2 = EXCHANGE_RATE, EXCHANGE_RATE_ASK, EXCHANGE_RATE_BID
	 * TYPE 3 = EXCHANGE_RATE, EXCHANGE_RATE_ASK, EXCHANGE_RATE_BID, EXCHANGE_RATE_DATE
	 */
	protected static function setExchangeRate( $from, $to, $type = 1)
	{
		switch($type){
			case 1:
			// return only rate
			$get = array(	'EXCHANGE_RATE' => 'a.rate');
			break;
			case 2:
			// return all rates
			$get = array(	'EXCHANGE_RATE' => 'a.rate',
							'EXCHANGE_RATE_ASK' => 'a.ask',
							'EXCHANGE_RATE_BID' => 'a.bid');
			break;
			case 3:
			// return all rates and date
			$get = array(	'EXCHANGE_RATE' => 'a.rate',
							'EXCHANGE_RATE_ASK' => 'a.ask',
							'EXCHANGE_RATE_BID' => 'a.bid',
							'EXCHANGE_RATE_DATE' => 'a.date_rate');
			break;
		}
		if(is_array($get) && $from && $to){
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
	
	
	
	protected static function getCurrency($currency)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('t.*');
		$query->from('#__ipdata_currency AS t');
		$query->where('t.codethree = \''.$currency.'\'');

		$db->setQuery($query);

		return $db->loadAssoc();
	}
	
	public static function makeMoney($number,$currency = false)
	{
		if (is_numeric($number)){
			$negativeFinderObj = new NegativeFinder(new Expression("$number"));
			$negative = $negativeFinderObj->isItNegative() ? TRUE : FALSE;
		} else {
			throw new Exception('ERROR! ('.$number.') is not a number!');
		}
		if(!$currency){
			$currency = JComponentHelper::getParams('com_ipdata')->get('base_currency');
		}
		
		$currency = self::getCurrency($currency);
		
		if (!$negative){
			$format = $currency['positivestyle'];
			$sign = '+';
		} else {
			$format = $currency['negativestyle'];
			$sign = '-';
			$number = abs($number);
		}
		$setupNumber = number_format((float)$number, (int)$currency['decimalplace'], $currency['decimalsymbol'], ' '); //$currency['thousands']);
		$search = array('{sign}', '{number}', '{symbol}');
		$replace = array($sign, $setupNumber, $currency['symbol']);
		$moneyMade = str_replace ($search,$replace,$format);
		
		return $moneyMade;
	}
	
}

// Detecting negative numbers
class Expression {
    protected $expression;
    protected $result;

    public function __construct($expression) {
        $this->expression = $expression;
    }

    public function evaluate() {
        $this->result = eval("return ".$this->expression.";");
        return $this;
    }

    public function getResult() {
        return $this->result;
    }
}

class NegativeFinder {
    protected $expressionObj;

    public function __construct(Expression $expressionObj) {
        $this->expressionObj = $expressionObj;
    }


    public function isItNegative() {
        $result = $this->expressionObj->evaluate()->getResult();

        if($this->hasMinusSign($result)) {
            return true;
        } else {
            return false;
        }
    }

    protected function hasMinusSign($value) {
        return (substr(strval($value), 0, 1) == "-");
    }
}

