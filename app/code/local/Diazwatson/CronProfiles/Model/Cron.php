<?php

/**
 * CronProfiles
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to
 * newer versions in the future.
 *
 * @category   Diazwatson
 * @package    Diazwatson_CronProfiles
 * @author     Raul E Watson (@diazwatson)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 **/

class Diazwatson_CronProfiles_Model_Cron extends Mage_Core_Model_Abstract
{

    const CRON_STRING_PATH = 'crontab/jobs/i4_cronprofiles/cron_expr';

    public function runDataFlowProfile()
    {
        if (Mage::getStoreConfig('i4cronprofiles/general/enabled')) {
            $profiles = explode(",",Mage::getStoreConfig('i4cronprofiles/general/profile_ids'));

            if(!empty($profiles)){

                if(Mage::getStoreConfig('i4cronprofiles/general/disable_index') )
                {
                    $this->disableAutomaticIndexing();
                }

                foreach ($profiles as $id) {
                    Mage::getModel('dataflow/profile')
                        ->load($id)
                        ->run();
                }

                $this->enableAutomaticIndexing();

                /* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
                $indexCollection = Mage::getModel('index/process')->getCollection();
                foreach ($indexCollection as $index) {
                    /* @var $index Mage_Index_Model_Process */
                    $index->reindexAll();
                }
            }
        }
    }

    public function disableAutomaticIndexing()
    {
        $Collection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($Collection as $process) {
            $process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
        }
    }

    public function enableAutomaticIndexing()
    {
        $Collection = Mage::getSingleton('index/indexer')->getProcessesCollection();
        foreach ($Collection as $process) {
            $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
        }
    }
}