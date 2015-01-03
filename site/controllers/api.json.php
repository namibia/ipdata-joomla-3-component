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

class IpdataControllerApi extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		// make sure all json stuff are set
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="api.json"');
		JResponse::setHeader("Access-Control-Allow-Origin", "*");
		// load the tasks
		$this->registerTask('data', 'api');
	}
	
	public function api()
	{
		$jinput 	= JFactory::getApplication()->input;
		$task 		= $this->getTask();
		if ($task == 'data'){
			try
			{				
				$result = $this->getModel('control')->getData(	$jinput->get('ip', 0, 'CMD'),
																$jinput->get('key', 0, 'ALNUM'),
																$jinput->get('base', 0, 'WORD'),
																$jinput->get('m', 0, 'INT'),
																$jinput->get('s', 0, 'INT'));
				$raw = $jinput->get('raw', 0, 'INT');
				if($raw){
					echo $_GET['callback'].json_encode($result);
				} else {
					echo $_GET['callback']."(".json_encode($result).");";
				}
			}
				catch(Exception $e)
			{
				if($raw){
			  		echo $_GET['callback'].json_encode($e);
				} else {
			  		echo $_GET['callback']."(".json_encode($e).");";
				}
			}
		} 
	}
}
