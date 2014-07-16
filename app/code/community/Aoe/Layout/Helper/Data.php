<?php
/**
 * Layout Data Helper
 *
 * @category   Aoe
 * @package    Aoe_Layout
 * @author     Manish Jain
 */
class Aoe_Layout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CRON_JOB_ENABLED = 'aoelayout/general/cron_job_enabled';
    /**
     * Get config value for cron job enabled
     *
     * @return boolean
     */
    public function getCronJobEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_CRON_JOB_ENABLED);
    }

}
