<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\BotTracker;

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuReporting;
use Piwik\Piwik;

/**
 * This class allows you to add, remove or rename menu items.
 * To configure a menu (such as Admin Menu, Reporting Menu, User Menu...) simply call the corresponding methods as
 * described in the API-Reference http://developer.piwik.org/api-reference/Piwik/Menu/MenuAbstract
 */
class Menu extends \Piwik\Plugin\Menu
{
    public function configureReportingMenu(MenuReporting $menu)
    {
        $menu->add('General_Visitors', 'BotTracker', array('module' => 'BotTracker', 'action' => 'getBotTrackerPage'),true, $orderId = 30);
    }

    public function configureAdminMenu(MenuAdmin $menu)
    {
				$menu->add('General_Settings', 'BotTracker', array('module' => 'BotTracker', 'action' => 'config'), Piwik::isUserHasSomeAdminAccess(), $orderId = 9);
    }
}
