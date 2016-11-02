<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\BotTracker\Widgets;

use Piwik\Widget\Widget;
use Piwik\Widget\WidgetConfig;
use Piwik\Piwik;
use Piwik\View;
use Piwik\ViewDataTable\Factory as ViewDataTableFactory;
//use Piwik\Plugins\BotTracker\Controller as BTController;
/**
 * This class allows you to add your own widget to the Piwik platform. In case you want to remove widgets from another
 * plugin please have a look at the "configureWidgetsList()" method.
 * To configure a widget simply call the corresponding methods as described in the API-Reference:
 * http://developer.piwik.org/api-reference/Piwik/Plugin\Widget
 */
class GetBotTrackerDisplay extends Widget
{
    public static function configure(WidgetConfig $config)
    {
        /**
         * Set the category the widget belongs to. You can reuse any existing widget category or define
         * your own category.
         */
        $config->setCategoryId('BotTracker_BotTracker');

        /**
         * Set the subcategory the widget belongs to. If a subcategory is set, the widget will be shown in the UI.
         */
        // $config->setSubcategoryId('General_Overview');

        /**
         * Set the name of the widget belongs to.
         */
        $config->setName('BotTracker_DisplayWidget');

        /**
         * Set the order of the widget. The lower the number, the earlier the widget will be listed within a category.
         */
        $config->setOrder(99);

        /**
         * Optionally set URL parameters that will be used when this widget is requested.
         * $config->setParameters(array('myparam' => 'myvalue'));
         */

        /**
         * Define whether a widget is enabled or not. For instance some widgets might not be available to every user or
         * might depend on a setting (such as Ecommerce) of a site. In such a case you can perform any checks and then
         * set `true` or `false`. If your widget is only available to users having super user access you can do the
         * following:
         *
         * $config->setIsEnabled(\Piwik\Piwik::hasUserSuperUserAccess());
         * or
         * if (!\Piwik\Piwik::hasUserSuperUserAccess())
         *     $config->disable();
         */
    }

    /**
     * This method renders the widget. It's on you how to generate the content of the widget.
     * As long as you return a string everything is fine. You can use for instance a "Piwik\View" to render a
     * twig template. In such a case don't forget to create a twig template (eg. myViewTemplate.twig) in the
     * "templates" directory of your plugin.
     *
     * @return string
     */
    public function render()
    {
    		$apiAction = 'BotTracker.getActiveBotData';	
    		
		$view = ViewDataTableFactory::build('table', $apiAction);
		
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

		$view->config->columns_to_display  = array('botName','botCount','botLastVisit');
		$view->requestConfig->filter_sort_column = 'botCount';
		$view->requestConfig->filter_sort_order = 'desc';
		$view->requestConfig->filter_limit = 10;
		$view->config->disable_row_evolution  = true;
		
		return $view->render();
    }

}