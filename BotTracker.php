<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id:  $
 */
	
/**
 * @package Piwik_BotTracker
 */
namespace Piwik\Plugins\BotTracker;

use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Piwik;
use Piwik\Tracker;
use Piwik\Plugins\BotTracker\API as BotTrackerAPI;

require_once PIWIK_INCLUDE_PATH . '/plugins/BotTracker/functions.php';

class BotTracker extends \Piwik\Plugin
{	
	protected $botDb = null;
	
	public function __destruct()
	{
		if(!is_null($this->botDb))
		{
			botDb_close($this->botDb);
		}
	}
	
	public function install()
	{
		$tableExists = false;
			
		// create new table "botDB"
		$query = "CREATE TABLE `".Common::prefixTable('bot_db')."` 
						 (`botId` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
						  `idsite` INTEGER(10) UNSIGNED NOT NULL,
						  `botName` VARCHAR(100) NOT NULL,
						  `botActive` BOOLEAN NOT NULL,
						  `botKeyword` VARCHAR(32) NOT NULL,
						  `botCount` INTEGER(10) UNSIGNED NOT NULL,
						  `botLastVisit` TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
						  `extra_stats` BOOLEAN NOT NULL DEFAULT FALSE,
						  PRIMARY KEY(`botId`)
						)  DEFAULT CHARSET=utf8";
		
		// if the table already exist do not throw error. Could be installed twice...
		try {
			Db::query($query);
		}
		catch(\Exception $e){
			$tableExists = true;
		}
		
		if (!$tableExists){		
			$sites = APISitesManager::getInstance()->getSitesWithAdminAccess();
			foreach ($sites as $site){
				BotTrackerAPI::insert_default_bots($site['idsite']);
			}
		}
		// Create extendet_stats_table
		$query4 =  'CREATE TABLE IF NOT EXISTS `'.Common::prefixTable('bot_db_stat').'`
						(
			 			`botId` INTEGER(10) UNSIGNED NOT NULL,
			 			`idsite` INTEGER(10) UNSIGNED NOT NULL,
			 			`page` VARCHAR(100) NOT NULL,
			 			`visit_timestamp` TIMESTAMP NOT NULL,
			 			`useragent` VARCHAR(100) NOT NULL,
			 
			 			PRIMARY KEY(`botId`,`visit_timestamp`)
						)  DEFAULT CHARSET=utf8';
		Db::query($query4);
	}
	
	public function uninstall()
	{
		$query = "DROP TABLE `".Common::prefixTable('bot_db')."` ";
		Db::query($query);
		$query2 = "DROP TABLE `".Common::prefixTable('bot_db_stat')."` ";
		Db::query($query2);
	}
	
	public function registerEvents()
	{
		return array(
			'Tracker.isExcludedVisit'  => 'checkBot',
		);
	}
	
	function checkBot(&$exclude, $request)
	{
		$ua = $request->getUserAgent();
		$idSite = $request->getIdSite();
		$currentTimestamp = gmdate("Y-m-d H:i:s");
		// max length of url can be 100 Bytes
		$currentUrl = substr($request->getParam('url'),0,100);
				
		Self::logToFile('SiteID:'.$idSite.' user Agent: '.$ua.' TS:'.$currentTimestamp.' page:'.$currentUrl);
		
		$db = Tracker::getDatabase();
		$result = $db->fetchRow("SELECT `botId`, `extra_stats` FROM ".Common::prefixTable('bot_db')."
		                        WHERE `botActive` = 1 
		                        AND   `idSite` = ?
		                        AND   LOCATE(`botKeyword`,?) >0
						            LIMIT 1", array($idSite, $ua));

		$botId = $result['botId'];
		if ($botId > 0 ){
			Self::logToFile('SiteID:'.$idSite.' found Bot: '.$botId);

			$db->query("UPDATE `".Common::prefixTable('bot_db')."` 
			               SET botCount = botCount + 1
			                 , botLastVisit = ?
			             WHERE botId = ?", array($currentTimestamp, $botId));

			$exclude = true;

			if ($result['extra_stats'] > 0){
				$query = "INSERT INTO `".Common::prefixTable('bot_db_stat')."` 
					(idsite, botid, page, visit_timestamp, useragent) VALUES (?,?,?,?,?)";
				$params = array($idSite,$botId,$currentUrl,$currentTimestamp,$ua);
				$db->query($query,$params);
			}
		}
	}


	public function logToFile($msg)
	{ 
		// to aktivate logging just change the value to "true".
		$logActive = false;
		
		if ($logActive){
			$pfad = "tmp/logs/";
			$filename = "log.txt";
			// open file
			$fd = fopen($pfad.$filename, "a");
			// append date/time to message
	    		if(is_array($msg))
	    		{
	  			$str = "[" . date("Y/m/d H:i:s", time()) . "] " . var_export($msg,true);
	    		} else {
				$str = "[" . date("Y/m/d H:i:s", time()) . "] " . $msg; 
			}
			// write string
			fwrite($fd, $str . "\n");
			// close file
			fclose($fd);
		}
	}
	
}
