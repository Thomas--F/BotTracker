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

use Piwik\WidgetsList;
use Piwik\Menu\MenuMain;
use Piwik\Settings\SystemSetting;
use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;
use Piwik\Tracker;

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
						  `botLastVisit` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
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
				$params3 = array($site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']
				                ,$site['idsite']);
				$query3 = "INSERT INTO `".Common::prefixTable('bot_db')."` 
				(idsite,botName, botActive, botKeyword, botCount, botLastVisit)
				VALUES (?,'MSN Search'              ,1,'MSNBOT'              ,0,'0000-00-00 00:00:00')
				     , (?,'Bingbot'                 ,1,'bingbot'             ,0,'0000-00-00 00:00:00')
				     , (?,'GoogleBot'               ,1,'GoogleBot'           ,0,'0000-00-00 00:00:00')
				     , (?,'Google Instant'          ,1,'Google Web Preview'  ,0,'0000-00-00 00:00:00')
				     , (?,'Media Partners GoogleBot',1,'Mediapartners-Google',0,'0000-00-00 00:00:00')
				     , (?,'Baiduspider'             ,1,'BaiDuSpider'         ,0,'0000-00-00 00:00:00')
				     , (?,'Ezooms'                  ,1,'Ezooms'              ,0,'0000-00-00 00:00:00')
				     , (?,'YahooSeeker'             ,1,'YahooSeeker'         ,0,'0000-00-00 00:00:00')
				     , (?,'Yahoo! Slurp'            ,1,'Yahoo! Slurp'        ,0,'0000-00-00 00:00:00')
				     , (?,'Altavista 1'             ,1,'AltaVista'           ,0,'0000-00-00 00:00:00')
				     , (?,'Altavista 2'             ,1,'AVSearch'            ,0,'0000-00-00 00:00:00')
				     , (?,'Altavista 3'             ,1,'Mercator'            ,0,'0000-00-00 00:00:00')
				     , (?,'Altavista 4'             ,1,'Scooter'             ,0,'0000-00-00 00:00:00')
				     , (?,'Infoseek 1'              ,1,'InfoSeek'            ,0,'0000-00-00 00:00:00')
				     , (?,'Infoseek 2'              ,1,'Ultraseek'           ,0,'0000-00-00 00:00:00')
				     , (?,'Lycos'                   ,1,'Lycos'               ,0,'0000-00-00 00:00:00')
				     , (?,'Wget'                    ,1,'Wget'                ,0,'0000-00-00 00:00:00')
				     , (?,'Yandex'                  ,1,'YandexBot'           ,0,'0000-00-00 00:00:00')
				     , (?,'Yandex ?'                ,1,'Java/1.4.1_04'       ,0,'0000-00-00 00:00:00')
				     , (?,'SiteBot'                 ,1,'SiteBot'             ,0,'0000-00-00 00:00:00')
				     , (?,'Exabot'                  ,1,'Exabot'              ,0,'0000-00-00 00:00:00')";
			     
				Db::query($query3,$params3);
			}
		}
		// Create extendet_stats_table
		$query4 =  'CREATE TABLE IF NOT EXISTS `'.Common::prefixTable('bot_db_stat').'`
						(
			 			`botId` INTEGER(10) UNSIGNED NOT NULL,
			 			`idsite` INTEGER(10) UNSIGNED NOT NULL,
			 			`page` VARCHAR(100) NOT NULL,
			 			`visit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			 			`useragent` VARCHAR(100) NOT NULL,
			 
			 			PRIMARY KEY(`botId`)
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
	
	public function getListHooksRegistered()
	{
		return array(
			'Tracker.isExcludedVisit'  => 'checkBot',
			'WidgetsList.addWidgets'   => 'addWidget',
			'Menu.Reporting.addItems'  => 'addMenu',
			'Menu.Admin.addItems'      => 'addConfigMenu'
		);
	}
	function addWidget()
	{
		WidgetsList::add('BotTracker_Widgets', 'BotTracker_DisplayWidget', 'BotTracker', 'displayWidget');
	}
	
	function addMenu()
	{
		MenuMain::getInstance()->add('General_Visitors', 'BotTracker', array('module' => 'BotTracker', 'action' => 'getBotTrackerPage'), true, 30);
	}
	
	public function addConfigMenu() {
		
		MenuAdmin::addEntry(
				'BotTracker',
				array('module' => 'BotTracker', 'action' => 'config'),
				Piwik::isUserHasSomeAdminAccess(),
				$order = 9
		);
	}


	function checkBot(&$exclude)
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$idSite = $_REQUEST['idsite'];
		$currentTimestamp = date("Y-m-d H:i:s");
		$currentUrl = $_REQUEST['url'];
		
		BotTracker::logToFile('SiteID:'.$idSite.' user Agent: '.$ua.' TS:'.$currentTimestamp.' page:'.$currentUrl);
		
		$db = Tracker::getDatabase();
		$result = $db->fetchRow("SELECT `botId`, `extra_stats` FROM ".Common::prefixTable('bot_db')."
		                        WHERE `botActive` = 1 
		                        AND   `idSite` = ?
		                        AND   LOCATE(`botKeyword`,?) >0
						            LIMIT 1", array($idSite, $ua));

		$botId = $result['botId'];
		if ($botId > 0 ){
			BotTracker::logToFile('SiteID:'.$idSite.' found Bot: '.$botId);

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
		$logActive = true;
		
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