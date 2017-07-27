<?php
class Magedev_Ordermanager_Model_System_Config_Backend_Ordermanager_Cron extends Mage_Core_Model_Config_Data
{
	 const CRON_STRING_PATH = 'crontab/jobs/ordermanager_archive/schedule/cron_expr';
   protected function _afterSave()
    {
	
		$isOrderManagerEnabled = Mage::getStoreConfig('orderview/general/enabled');
        if (!$isOrderManagerEnabled) return ;
		
        $time = Mage::getStoreConfig('orderview/order_arch_mng/time');//$this->getData('groups/general/fields/time/value');
		$frequncy = Mage::getStoreConfig('orderview/order_arch_mng/frequency');//$this->getData('groups/general/fields/frequency/value');
		
		$frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;
		
		$cronDayOfWeek = date('N');
		
        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}
