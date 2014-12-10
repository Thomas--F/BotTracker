<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Controller.php 4336 2011-04-06 01:52:11Z matt $
 *
 * @category Piwik_Plugins
 * @package Piwik_BotTracker
 */

namespace Piwik\Plugins\BotTracker;

use Piwik\Common;
use Piwik\Nonce;
use Piwik\Notification;
use Piwik\Notification\Manager as NotificationManager;
use Piwik\Piwik;
use Piwik\Site;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\View;
use Piwik\ViewDataTable\Factory as ViewDataTableFactory;
use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Plugins\BotTracker\API as APIBotTracker;
use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuTop;
use Piwik\Menu\MenuUser;
use Piwik\Menu\MenuMain;

class Controller extends \Piwik\Plugin\Controller
{	
	/**
	 * See the result on piwik/?module=BotTracker&action=displayWidget
	 * or in the dashboard > Add a new widget 
	 *
	 */
	function displayWidget($fetch = false)
	{
      		$controllerAction = $this->pluginName . '.' . __FUNCTION__;
      		$apiAction = 'BotTracker.getActiveBotData';	

		$view = $this->getStandardDataTable('table', $apiAction, $controllerAction);
		$view->config->columns_to_display  = array('botName','botCount','botLastVisit');
		$view->requestConfig->filter_sort_column = 'botCount';
		$view->requestConfig->filter_sort_order = 'desc';
		$view->requestConfig->filter_limit = 10;
		$view->config->disable_row_evolution  = true;
		
		return $this->renderView($view, $fetch);
	}
	
	function getBotTrackerView($fetch = false)
	{
		$controllerAction = $this->pluginName . '.' . __FUNCTION__;
      		$apiAction = 'BotTracker.getAllBotDataWithIcon';	
      
		$view = $this->getStandardDataTable('table', $apiAction, $controllerAction);
		$view->config->columns_to_display  = array('label','botName','botKeyword','botCount','botLastVisit');
		$view->config->translations['label'] = Piwik::translate('BotTracker_BotActive');
		$view->config->disable_row_evolution  = true;

		return $view->render();
	}

	function getBotTrackerPie($fetch = false)
	{
		$view = ViewDataTableFactory::build('graphPie', 'BotTracker.getAllBotDataPie', $controllerAction = 'BotTracker.getBotTrackerPie');

		//$view->config->columns_to_display = array('value');
      		$view->config->translations['value'] = "hits by Bot";
      		$view->config->show_footer_icons = true;
      		$view->config->selectable_columns = array("value");
      		$view->config->max_graph_elements = 10;
		$view->config->disable_row_evolution  = true;

      		return $view->render();
	}

	function getBotTrackerPage()
	{
		$view = new View('@BotTracker/viewTracker');

		$view->dataTableTrackerBot1 = $this->getBotTrackerView(true);
		$view->dataTableTrackerBot2 = $this->getBotTrackerPie(true);

		echo $view->render();		
	}

	public function config($siteID=0, $errorList = array()) {
		Piwik::checkUserHasSuperUserAccess();

		// $this->logToFile('config: siteID='.$siteID);

		if ($siteID==0){
			$siteID=Common::getRequestVar("idSite");
		}
		
  		$sitesList = APISitesManager::getInstance()->getSitesWithAdminAccess();
		$botList = APIBotTracker::getAllBotDataForConfig($siteID);

		$view = new View('@BotTracker/config');
		$view->language = LanguagesManager::getLanguageCodeForCurrentUser();
		
		$this->setBasicVariablesView($view);
		$view->currentAdminMenuName = MenuAdmin::getInstance()->getCurrentAdminMenuName();
		$view->defaultReportSiteName = Site::getNameFor($siteID);
		$view->assign('sitesList', $sitesList);
		$view->assign('botList', $botList);
		$view->assign('idSite', $siteID);
		$view->assign('errorList', $errorList);
		
		$view->nonce = Nonce::getNonce('BotTracker.saveConfig');
		$view->adminMenu = MenuAdmin::getInstance()->getMenu();
		$view->topMenu = MenuTop::getInstance()->getMenu();
		$view->userMenu = MenuUser::getInstance()->getMenu();
		$view->notifications = NotificationManager::getAllNotificationsToDisplay();
		$view->phpVersion = phpversion();
		$view->phpIsNewEnough = version_compare($view->phpVersion, '5.3.0', '>=');		

		
		echo $view->render();
	}

	public function config_reload() {
		Piwik::checkUserHasSuperUserAccess();

		$siteID=Common::getRequestVar('siteID',0);
		if ($siteID==0){
			$siteID=Common::getRequestVar("idSite");
		}
		// $this->logToFile('config_reload: siteID='.$siteID);
		
	   	$this->config($siteID);
	}

	public function saveConfig() {
		try{
			// Only admin is allowed to do this!
			Piwik::checkUserHasSuperUserAccess();
			$siteID=Common::getRequestVar('siteID',0);
			if ($siteID==0){
				$siteID=Common::getRequestVar("idSite");
			}

	  		$botList = APIBotTracker::getAllBotDataForConfig($siteID);
			
			$errorList = array();
			
			foreach($botList as $bot)
			{
				$botName = trim(Common::getRequestVar($bot['botId'].'_botName',''));
				$botKeyword = trim(Common::getRequestVar($bot['botId'].'_botKeyword',''));
				$botActive = Common::getRequestVar($bot['botId'].'_botActive',0);
				$extraStats = Common::getRequestVar($bot['botId'].'_extraStats',0);
				
				if ($botName    != $bot['botName'] || 
				    $botKeyword != $bot['botKeyword'] ||
				    $botActive  != $bot['botActive'] ||
				    $extraStats != $bot['extra_stats']) {

				//$this->logToFile($bot['botId'].': Name alt >'.$bot['botName'].'< neu >'.$botName.'<');
				//$this->logToFile($bot['botId'].': Key alt >'.$bot['botKeyword'].'< neu >'.$botKeyword.'<');
				//$this->logToFile($bot['botId'].': Aktiv alt >'.$bot['botActive'].'< neu >'.$botActive.'<');

				    	
					if (empty($botName)){
						$errorList[]=Piwik::translate('BotTracker_BotName').' '.$bot['botId'].Piwik::translate('BotTracker_Error_empty');
					} else if (empty($botKeyword)){
						$errorList[]=Piwik::translate('BotTracker_BotKeyword').' '.$bot['botId'].Piwik::translate('BotTracker_Error_empty');
					} else {
						APIBotTracker::updateBot($botName, $botKeyword, $botActive, $bot['botId'], $extraStats);
					}
				}
				
			}
			$botName = trim(Common::getRequestVar('new_botName',''));
			$botKeyword = trim(Common::getRequestVar('new_botKeyword',''));
			$botActive = Common::getRequestVar('new_botActive',0);
			$extraStats = Common::getRequestVar('new_extraStats',0);
			
			//$this->logToFile('Name neu >'.$botName.'<  Key neu >'.$botKeyword.'<');
			
			if ($botName    != '' || 
			    $botKeyword != '') {
				if (empty($botName)){
						$errorList[]=Piwik::translate('BotTracker_BotName').Piwik::translate('BotTracker_Error_empty');
				} else if (empty($botKeyword)){
						$errorList[]=Piwik::translate('BotTracker_BotKeyword').Piwik::translate('BotTracker_Error_empty');
				} else {
					APIBotTracker::insertBot($siteID, $botName, $botActive, $botKeyword, $extraStats);
				}
			}

			$this->config($siteID, $errorList);

		} catch(Exception $e ) {
			echo $e;
		}
	}

	public function deleteBotEntry() {
		try{
			// Only admin is allowed to do this!
			Piwik::checkUserHasSuperUserAccess();
			$siteID=Common::getRequestVar('siteID',0);
			if ($siteID==0){
				$siteID=Common::getRequestVar("idSite");
			}
			$botId=Common::getRequestVar("botId");
			$errorList = array();

			APIBotTracker::deleteBot($botId);

			$errorList[]='Bot '.$botId.Piwik::translate('BotTracker_Message_deleted');

			$this->config($siteID, $errorList);
		} catch(Exception $e ) {
			echo $e;
		}
	}
	
	public function config_insert_db() {
		try{
			// Only admin is allowed to do this!
			Piwik::checkUserHasSuperUserAccess();
			$siteID=Common::getRequestVar('siteID',0);
			if ($siteID==0){
				$siteID=Common::getRequestVar("idSite");
			}

			$errorList = array();
			$botList = array();
			$botList[] = array('MSN Search'              ,'MSNBOT'              );
			$botList[] = array('MSN Bot Media'           ,'msnbot-media'        );
			$botList[] = array('Bingbot'                 ,'bingbot'             );
			$botList[] = array('GoogleBot'               ,'Googlebot'           );
			$botList[] = array('Google Instant'          ,'Google Web Preview'  );
			$botList[] = array('Media Partners GoogleBot','Mediapartners-Google');
			$botList[] = array('Baiduspider'             ,'BaiDuSpider'         );
			$botList[] = array('Ezooms'                  ,'Ezooms'              );
			$botList[] = array('YahooSeeker'             ,'YahooSeeker'         );
			$botList[] = array('Yahoo! Slurp'            ,'Yahoo! Slurp'        );
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
			
			$i = 0;
			foreach($botList as $bot)
			{
				$botX = APIBotTracker::getBotByName($siteID, $bot[0]);
				
				if (empty($botX)){
					APIBotTracker::insertBot($siteID, $bot[0],1,$bot[1],0);
					$i++;	
				}
			}
			$errorList[] = $i." ".Piwik::translate('BotTracker_Message_bot_inserted');
			
			$this->config($siteID, $errorList);

		} catch(Exception $e ) {
			echo $e;
		}
	}

	
	protected function getStandardDataTable( $name, $apiAction, $controllerAction)
	{
		$view = ViewDataTableFactory::build($name, $apiAction, $controllerAction);
		
		$view->config->translations['botId'] = Piwik::translate('BotTracker_BotId');
		$view->config->translations['botName'] = Piwik::translate('BotTracker_BotName');
		$view->config->translations['botKeyword'] = Piwik::translate('BotTracker_BotKeyword');
		$view->config->translations['botCount'] = Piwik::translate('BotTracker_BotCount');
		$view->config->translations['botLastVisit'] = Piwik::translate('BotTracker_BotLastVisit');
		$view->config->show_search = false;
		$view->config->show_footer_icons = false;
		$view->config->show_exclude_low_population = false;
		$view->requestConfig->filter_limit = 25;
		$view->requestConfig->filter_sort_column = 'botId';
		$view->requestConfig->filter_sort_order = 'asc';
		
		return $view;
	}
	
	public function logToFile($msg)
	{ 
//		$pfad = "tmp/logs/";
//		$filename = "log2.txt";
//		// open file
//		$fd = fopen($pfad.$filename, "a");
//		// append date/time to message
//    		if(is_array($msg))
//    		{
//  			$str = "[" . date("Y/m/d H:i:s", time()) . "] " . var_export($msg,true);
//    		} else {
//			$str = "[" . date("Y/m/d H:i:s", time()) . "] " . $msg; 
//		}
//		// write string
//		fwrite($fd, $str . "\n");
//		// close file
//		fclose($fd);
	}
	
}
