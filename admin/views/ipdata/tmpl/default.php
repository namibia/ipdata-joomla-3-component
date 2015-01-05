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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// keep loged-in
JHtml::_('behavior.keepalive');

?>
<div id="j-main-container" class="span12">
    <div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'cpanel_tab', array('active' => $this->tab_active)); ?>
			<?php foreach($this->tabs as $tab): ?>
				<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', $tab->alias, JText::_($tab->name, true)); ?>
                    <div class="row-fluid">
                        <?php echo $tab->div; ?>
                    </div>
                <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php endforeach; ?>
                      
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
</div>
<script type="text/javascript">
function changeTab(url)
{
	jQuery('#cpanel_tabTabs a[href="#'+url+'"]').tab('show');
	
}
</script>