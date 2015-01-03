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

class com_ipdataInstallerScript {
	
	/**
	* Method to install the component
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function install($parent)
	{
		 echo JText::_('Installed successfully');
	}
	
	/**
	* Method to update the component
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function update($parent)
	{
		echo JText::_('Updated successfully');
	}
	
	/**
	* method to run before an install/update/uninstall method
	*
	* @param mixed $parent The class calling this method
	* @return void
	*/
	public function preflight($type, $parent)
	{
	
	}
	 
	public function postflight($type, $parent)
	{
		//set the default confic setings
		if ($type == 'install') {
				// Set Global Settings
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->update('#__extensions');
				$defaults = '{"base_currency":"EUR","vdm_logo":"1","vdm_text":"1","vdm_link":"1","nameGlobal":"Vast Development Method","emailGlobal":"info@vdm.io","titleWorker1":"Application Engineer","nameWorker1":"Llewellyn van der Merwe","emailWorker1":"llewellyn@vdm.io","linkWorker1":"http:\/\/vdm.io","useWorker1":"2","showWorker1":"3","titleWorker2":"","nameWorker2":"","emailWorker2":"","linkWorker2":"","useWorker2":"0","showWorker2":"0","titleWorker3":"","nameWorker3":"","emailWorker3":"","linkWorker3":"","useWorker3":"0","showWorker3":"0","titleWorker4":"","nameWorker4":"","emailWorker4":"","linkWorker4":"","useWorker4":"0","showWorker4":"0","vdm_name":"Vast Development Method","vdm_url":"https:\/\/www.vdm.io\/","vdm_owner":"Llewellyn van der Merwe","api_access":"0","api_privatekey":""}';
				$query->set("params =  '{$defaults}'");
				$query->where("element = 'com_ipdata'"); 
				$db->setQuery($query);
				$db->query();
		
		echo '	<p>'.JText::_('Congratulations! Now you can start using Ip Data!').'</p>
				<a target="_blank" href="https://www.vdm.io/" title="Ip Data">
				<img src="/administrator/components/com_ipdata/assets/images/vdm.jpg"/>
				</a>';
		} 
		if ($type == 'update') {
				
		echo '	<p>'.JText::_('Congratulations! Now you can start using Ip Data!').'</p>
				<a target="_blank" href="https://www.vdm.io/" title="Ip Data">
				<img src="/administrator/components/com_ipdata/assets/images/vdm.jpg"/>
				</a>';
		}
	}
}