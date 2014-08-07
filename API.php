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

use Piwik\Db;
use Piwik\Common;
use Piwik\DataTable;
use Piwik\Site;
use Piwik\Date;



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
		             WHERE `botId` = ?", array($botName, $botKeyword, $botActive, $extraStats, $botId));

	}

	static function insertBot($siteID, $botName, $botActive, $botKeyword, $extraStats)
	{
	
		Db::get()->query("INSERT INTO `".Common::prefixTable('bot_db')."` 
               (`idsite`,`botName`, `botActive`, `botKeyword`, `botCount`, `botLastVisit`, `extra_stats`)
                VALUES (?,?,?,?,0,'0000-00-00 00:00:00',?)"
          , array($siteID, $botName, $botActive, $botKeyword, $extraStats));
	}

	static function deleteBot($botId)
	{
		Db::get()->query("DELETE FROM `".Common::prefixTable('bot_db')."` WHERE `botId` = ?",array($botId));
	}

	static function getBotByName($siteID,$botName)
	{
		$rows = Db::get()->fetchAll("SELECT * FROM ".Common::prefixTable('bot_db')." WHERE `botName` = ? AND `idSite`= ? ORDER BY `botId`", array($botName, $siteID));
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
	
}
