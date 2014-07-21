<?php
class Aoe_Layout_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    public function saveCache($specificTime = null)
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        $tags = $this->getHandles();

        $tags[] = self::LAYOUT_GENERAL_CACHE_TAG;
        return Mage::app()->saveCache($str, $this->getCacheId(), $tags, $specificTime);
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @return Mage_Core_Model_Layout_Update
     */
    public function load($handles=array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
        } elseif (!is_array($handles)) {
            throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid layout update handle'));
        }

        foreach ($handles as $handle) {
            $this->addHandle($handle);
        }

        if ($this->loadCache()) {
            return $this;
        }

        $specificTime = null;
        $storeTimeStamp = Mage::app()->getLocale()->storeTimeStamp();

        foreach ($this->getHandles() as $handle) {
            $this->merge($handle);
            /* Set cache lifetime based on layout active to date*/
            if (Mage::app()->useCache('layout')) {
                if ($layoutActiveTo = Mage::getResourceModel('core/layout')->fetchLayoutActiveToByHandle($handle)) {
                    $endDate  = Mage::app()->getLocale()->date()
                        ->setDate($layoutActiveTo, 'Y-M-d')
                        ->setTime('23:59:59')
                        ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                    $timeDiff = strtotime($endDate) - $storeTimeStamp;
                    if ($timeDiff>0) {
                        $specificTime = is_null($specificTime) ? $timeDiff : ($specificTime > $timeDiff ? $timeDiff : $specificTime);
                    }
                }
            }
        }

        $this->saveCache($specificTime);
        return $this;
    }
}
