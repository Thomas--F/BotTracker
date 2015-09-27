<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: API.php 4448 2011-04-14 08:20:49Z matt $
 * 
 * @category Piwik_Plugins
 * @package Piwik_BotTracker
 */
namespace Piwik\Plugins\BotTracker;

use Piwik\Plugins\BotTracker\API as BotTrackerAPI;
use Piwik\Db;
use Piwik\Common;
use Piwik\DataTable;
use Piwik\Site;
use Piwik\Date;
use Piwik\Piwik;



/**
 * @see plugins/BotTracker/functions.php
 */
require_once PIWIK_INCLUDE_PATH . '/plugins/BotTracker/functions.php';

/**
 * @package Piwik_BotTracker
 */
class API extends \Piwik\Plugin\API
{
	static private $instance = null;
	static public function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	static function getAllBotData($idSite)
	{
		$rows = Db::get()->fetchAll("SELECT * FROM ".Common::prefixTable('bot_db')." WHERE idSite= ? ORDER BY `botId`", array($idSite));
		$rows = self::convertBotLastVisitToLocalTime($rows, $idSite);
		// convert this array to a DataTable object
		return DataTable::makeFromIndexedArray($rows);
	}

	static function getAllBotDataForConfig($idsite)
	{
		$rows = Db::get()->fetchAll("SELECT `idsite`, `botId`, `botName`, `botActive`, `botKeyword`, `extra_stats` FROM ".Common::prefixTable('bot_db')." WHERE `idsite` = ? ORDER BY `botId`", array($idsite));
		
		return $rows;
	}


	static function getActiveBotData($idSite)
	{
		$rows = Db::get()->fetchAll("SELECT * FROM ".Common::prefixTable('bot_db')." WHERE `botActive` = 1 AND idSite= ? ORDER BY `botId`", array($idSite));
		$rows = self::convertBotLastVisitToLocalTime($rows, $idSite);
		// convert this array to a DataTable object
		return DataTable::makeFromIndexedArray($rows);
	}

	function getAllBotDataWithIcon($idSite)
	{
		$dataTable = $this->getAllBotData($idSite);
		$dataTable->renameColumn('botActive', 'label');
		
		$dataTable->filter('ColumnCallbackAddMetadata', array('label', 'logo', __NAMESPACE__ . '\getActiveIcon'));
      		$dataTable->filter('ColumnCallbackReplace', array('label', create_function('$label', "return ' ';")));
      		$dataTable->queueFilter('ColumnCallbackAddMetadata', array(array(), 'logoWidth', function () { return 16; }));
      		$dataTable->queueFilter('ColumnCallbackAddMetadata', array(array(), 'logoHeight', function () { return 16; }));
		
		return $dataTable;
	}


	static function getAllBotDataPie($idSite)
	{
		$rows = Db::get()->fetchAll("SELECT `botName`, `botCount` FROM ".Common::prefixTable('bot_db')." WHERE `idSite`= ? ORDER BY `botCount` DESC LIMIT 10", array($idSite));

		$i = 0;
		$keys[0] = "";
		$values[0] = "";
		foreach($rows as $row)
		{
			$keys[$i] = $row['botName'];
			$values[$i] = $row['botCount'];
			$i++;
		}
		$pieArray = array_combine($keys, $values);
		
		// convert this array to a DataTable object
		return DataTable::makeFromIndexedArray($pieArray);
	}
	
	static function updateBot($botName, $botKeyword, $botActive, $botId, $extraStats)
	{
		Db::get()->query("UPDATE `".Common::prefixTable('bot_db')."` 
		             SET `botName` = ?
		               , `botKeyword` = ?
		               , `botActive` = ?
		               , `extra_stats` = ?
		             WHERE `botId` = ?", array(self::htmlentities2utf8($botName), self::htmlentities2utf8($botKeyword), $botActive, $extraStats, $botId));

	}

	static function insertBot($idSite, $botName, $botActive, $botKeyword, $extraStats)
	{
	
		Db::get()->query("INSERT INTO `".Common::prefixTable('bot_db')."` 
               (`idsite`,`botName`, `botActive`, `botKeyword`, `botCount`, `extra_stats`)
                VALUES (?,?,?,?,0,?)"
          , array($idSite, self::htmlentities2utf8($botName), $botActive, self::htmlentities2utf8($botKeyword), $extraStats));
	}

	static function insert_default_bots($idsite = 0) {
		$i = 0;
		
		if ($idsite <> 0){
			// Only admin is allowed to do this!
			Piwik::checkUserHasSuperUserAccess();

			$botList = array();
			$botList[] = array('MSN Search'              ,'MSNBOT'              );
			$botList[] = array('MSN Bot Media'           ,'msnbot-media'        );
			$botList[] = array('Bingbot'                 ,'bingbot'             );
			$botList[] = array('GoogleBot'               ,'Googlebot'           );
			$botList[] = array('Google Instant'          ,'Google Web Preview'  );
			$botList[] = array('Media Partners GoogleBot','Mediapartners-Google');
			$botList[] = array('Baiduspider'             ,'Baiduspider'         );
			$botList[] = array('Ezooms'                  ,'Ezooms'              );
			$botList[] = array('YahooSeeker'             ,'YahooSeeker'         );
			$botList[] = array('Yahoo! Slurp'            ,'Slurp'               );
			$botList[] = array('Altavista 1'             ,'AltaVista'           );
			$botList[] = array('Altavista 2'             ,'AVSearch'            );
			$botList[] = array('Altavista 3'             ,'Mercator'            );
			$botList[] = array('Altavista 4'             ,'Scooter'             );
			$botList[] = array('Infoseek 1'              ,'InfoSeek'            );
			$botList[] = array('Infoseek 2'              ,'Ultraseek'           );
			$botList[] = array('Lycos'                   ,'Lycos'               );
			$botList[] = array('Wget'                    ,'Wget'                );
			$botList[] = array('Yandex'                  ,'YandexBot'           );
			$botList[] = array('Yandex ?'                ,'Java/1.4.1_04'       );
			$botList[] = array('SiteBot'                 ,'SiteBot'             );
			$botList[] = array('Exabot'                  ,'Exabot'              );
			$botList[] = array('AhrefsBot'               ,'AhrefsBot'           );
			$botList[] = array('MJ12Bot'                 ,'MJ12bot'             );
			$botList[] = array('NetSeer Crawler'         ,'NetSeer crawler'     );
			$botList[] = array('TurnitinBot'             ,'TurnitinBot'         );
			$botList[] = array('Magpie Crawler'          ,'magpie-crawler'      );
			$botList[] = array('Nutch Crawler'           ,'Nutch Crawler'       );
			$botList[] = array('CMS Crawler'             ,'CMS Crawler'         );
			$botList[] = array('RogerBot'                ,'rogerbot'            );
			$botList[] = array('MJ12bot'                 ,'MJ12bot'             );
			$botList[] = array('Domnutch-Bot'            ,'Domnutch'            );
			$botList[] = array('Ssearch Crawler'         ,'ssearch_bot'         );
			$botList[] = array('XoviBot'                 ,'XoviBot'             );
			$botList[] = array('NetSeer Crawler'         ,'netseer'             );
			$botList[] = array('Digincore'               ,'digincore'           );
			$botList[] = array('Fr-Crawler'              ,'fr-crawler'          );

			foreach($botList as $bot)
			{
				$botX = BotTrackerAPI::getBotByName($idsite, $bot[0]);
				
				if (empty($botX)){
					BotTrackerAPI::insertBot($idsite, $bot[0],1,$bot[1],0);
					$i++;	
				}
			}
		}	
	
		return $i;
	}

	static function deleteBot($botId)
	{
		Db::get()->query("DELETE FROM `".Common::prefixTable('bot_db')."` WHERE `botId` = ?",array($botId));
	}

	static function getBotByName($idSite, $botName)
	{
		$rows = Db::get()->fetchAll("SELECT * FROM ".Common::prefixTable('bot_db')." WHERE `botName` = ? AND `idSite`= ? ORDER BY `botId`", array($botName, $idSite));
		$rows = self::convertBotLastVisitToLocalTime($rows, $idSite);
		return $rows;
	}

	static function convertBotLastVisitToLocalTime($rows, $idSite)
	{
		// convert lastVisit to localtime
		$timezone = Site::getTimezoneFor($idSite);
		
		foreach($rows as &$row)
		{
			if ($row['botLastVisit'] == '0000-00-00 00:00:00'){
				$row['botLastVisit'] = " - ";
			} else {
				$botLastVisit = Date::adjustForTimezone(strtotime($row['botLastVisit']), $timezone);
	        		$row['botLastVisit'] = date('Y-m-d H:i:s', $botLastVisit);
        		}
		}
		return $rows;
	}
	static function htmlentities2utf8 ($string)
	{
		$output = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $string); 
    		return html_entity_decode($output);
  	} 
}
