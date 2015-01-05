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

$edit = "index.php?option=com_ipdata&view=currencies&task=currency.edit";
JHtml::_('behavior.tooltip');

?>
<?php foreach($this->items as $i => $item){
	$canCheckin	= $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
	$userChkOut	= JFactory::getUser($item->checked_out);
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<?php if($this->canEdit): ?>
            <td class="nowrap center hidden-phone">
                <?php if ($item->checked_out) : ?>
                    <?php if ($canCheckin) : ?>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    <?php else: ?>
                        &#35;
                    <?php endif; ?>
                <?php else: ?>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <?php endif; ?>
            </td>
		<?php endif; ?>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php if ($this->canEdit) : ?>
                <?php echo GetHelper::htmlEscape($item->name); ?> - (<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?>"><?php echo 'Edit'; ?></a>)
                <?php if ($item->checked_out){ ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'currencies.', $canCheckin); ?>
                <?php } ?>
            <?php else: ?>
                <?php echo GetHelper::htmlEscape($item->name); ?>
            <?php endif; ?>
		</td>
		<td class="nowrap hidden-phone">
            <?php
				$exchangeRate	= false;	
				$exchange		= GetHelper::getExchangeRate($item->codethree,1,2);
				if(is_array($exchange['TO'])){
					$to = current($exchange['TO']);
					if($to['EXCHANGE_RATE']){
					$exchangeRate = $to['EXCHANGE_RATE'];
					}
				}
				if($exchangeRate){
					echo '<span style="cursor: pointer; color: #08c;">'.JHtml::tooltip($exchangeRate, 'Unrounded Rate', '', GetHelper::htmlEscape(GetHelper::makeMoney($exchangeRate))).'</span> = '.GetHelper::htmlEscape(GetHelper::makeMoney(1,$item->codethree));
				}
			?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->codethree; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->numericcode; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->symbol; ?>
		</td>
		<?php if ($this->canState) : ?>
            <td class="center">
				<?php if ($item->checked_out) : ?>
                    <?php if ($canCheckin) : ?>
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'currencies.', true, 'cb'); ?>
                    <?php else: ?>
                        &#35;
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'currencies.', true, 'cb'); ?>
                <?php endif; ?>
            </tr>
       <?php endif; ?>
	</tr>
<?php } ?>