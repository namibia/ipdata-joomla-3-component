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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$params = $this->form->getFieldsets('params');
$componentParams = JComponentHelper::getParams('com_ipdata');

?>
<style type="text/css">
	.full, .thumb { border: 1px solid #CCC; float: left; margin: 0 10px 0 0; padding: 10px; }
	.full h2, .thumb h2 { margin: 0; padding: 0; }
</style>
<ul class="nav nav-tabs hidden" >
	<li class="active"><a data-toggle="tab" href="#home">tab</a></li>
</ul>
<form action="<?php echo JRoute::_('index.php?option=com_ipdata&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Details' ); ?></legend>
				<div class="adminformlist">
					<?php foreach($this->form->getFieldset('details') as $field){ ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php }; ?>
				</div>
			</fieldset>
		</div>
	</div>
	<div>
		<input type="hidden" name="task" value="country.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>