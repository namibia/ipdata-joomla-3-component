<?php
/*------------------------------------------------------------------------
# ordering.php - Builder Box Component
# ------------------------------------------------------------------------
# author    Llewellyn van de Merwe
# copyright Copyright (C) 2014. All Rights Reserved
# license   GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
# website   www.vdm.io
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla formrule library
jimport('joomla.form.formrule');
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleordering extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 * @since	2.5
	 */
	protected $regex = '^[^_]+$';
}
?>