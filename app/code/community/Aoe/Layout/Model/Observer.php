<?php
/**
 * Layout Observer
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Model_Observer
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED    = 0;

    /**
     * Cron job method for layout update
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledLayoutUpdate(Mage_Cron_Model_Schedule $schedule)
    {
        try {
            if (Mage::helper('aoe_layout')->getCronJobEnabled()) {
                $this->_runLayoutUpdate();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Updating the layout update data
     */
    protected function _runLayoutUpdate()
    {
        $layoutIds = Mage::getSingleton('aoe_layout/layout')->getResource()->getLayoutIds();
        if (count($layoutIds)>0) {
            try {
                foreach ($layoutIds as $layoutId) {
                    $layoutUpdateInstance = Mage::getSingleton('aoe_layout/layout')->load($layoutId);

                    $inRange = Mage::app()->getLocale()
                        ->isStoreDateInInterval(null, $layoutUpdateInstance->getLayoutActiveFrom(), $layoutUpdateInstance->getLayoutActiveTo());
                    if ($inRange) {
                        $layoutUpdateInstance->setIsActive(self::STATUS_ENABLED)
                            ->setIsMassupdate(true)
                            ->save();
                    } else {
                        $layoutUpdateInstance->setIsActive(self::STATUS_DISABLED)
                            ->setIsMassupdate(true)
                            ->save();
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e->getMessage());
                return;
            } catch (Exception $e) {
                 Mage::logException($e);
                return;
            }
        }
        return;
    }
}
