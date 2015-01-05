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

?>
<tr>
	<?php if($this->canEdit): ?>
        <th width="20" class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.checkall'); ?>
        </th>
    <?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
		<?php echo JHtml::_('grid.sort', 'Id', 'id', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'Name', 'name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" style="text-align: right;">
		<?php echo JText::_('Exchange Rate'); ?>
	</th>
	<th class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'Currency', 'currency', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="50" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', 'Code 3', 'codethree', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="50" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', 'Code 2', 'codetwo', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="50" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', 'Worldzone', 'worldzone', $this->listDirn, $this->listOrder); ?>
	</th>
	<?php if($this->canState): ?>
        <th width="10" class="nowrap center" >
            <?php echo JHtml::_('grid.sort', 'Published', 'published', $this->listDirn, $this->listOrder); ?>
        </th>
    <?php endif; ?>
</tr>