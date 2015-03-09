<?php
/**
 * Diazwatson_CronProfiles
 *
 * @category    CronProfiles
 * @package     Diazwatson_CronProfiles
 * @Date        01/2015
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

class Diazwatson_Cronprofiles_Model_Cron extends Mage_Core_Model_Abstract{

	const CRON_STRING_PATH = 'crontab/jobs/cronprofiles/cron_expr';



	public function runDataFlowProfile()
	{
		if (Mage::getStoreConfig('cronprofiles/general/enabled')) {

			/**
			 *  Get profile Ids from configuration
			 */

			$profiles = explode(",", Mage::getStoreConfig('cronprofiles/general/profile_ids'));

			if(!empty($profiles)){


				/**
				 *  Check whether or not to disable automatic indexing before execute the profiles
				 */

				if(Mage::getStoreConfig('cronprofiles/general/disable_index') )
				{
					$this->disableAutomaticIndexing();
				}

				/**
				 *  Execute previously loaded profiles
				 */

				foreach ($profiles as $id) {
					Mage::getModel('dataflow/profile')
					    ->load($id)
					    ->run();
				}


				/**
				 *  Enable automatic indexing
				 */

				$this->enableAutomaticIndexing();


				/**
				 *  Reindex all available indexes
				 *  @var $indexCollection Mage_Index_Model_Resource_Process_Collection
				 */

				$indexCollection = Mage::getModel('index/process')->getCollection();
				foreach ($indexCollection as $index) {
					/* @var $index Mage_Index_Model_Process */
					$index->reindexAll();
				}
			}
		}
	}

	/**
	 *  Disable Automatic Indexing
	 */
	public function disableAutomaticIndexing()
	{
		$Collection = Mage::getSingleton('index/indexer')->getProcessesCollection();
		foreach ($Collection as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
		}
	}

	/**
	 *  Enabled Automatic Indexing
	 */
	public function enableAutomaticIndexing()
	{
		$Collection = Mage::getSingleton('index/indexer')->getProcessesCollection();
		foreach ($Collection as $process) {
			$process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
		}
	}
}