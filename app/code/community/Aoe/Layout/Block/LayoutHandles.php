<?php
/**
 * Class Aoe_Layout_Block_LayoutHandles
 *
 * @author Fabrizio Branca
 * @since 2014-08-12
 */
class Aoe_Layout_Block_LayoutHandles extends Mage_Core_Block_Abstract {

    protected function _toHtml()
    {
        $handles = $this->getLayout()->getUpdate()->getHandles();
        if ($this->getIsVisible()) {
            return '<ul><li>' . implode('</li><li>', $handles) . '</li></ul>';
        } else {
            return "<!-- Layout Handles: \n- " . implode("\n- ", $handles) . "\n -->\n";
        }
    }

} 