<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\BotTracker\Reports;

use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;
use Piwik\ViewDataTable\Factory as ViewDataTableFactory;
use Piwik\View;
use Piwik\Widget\WidgetsList;
use Piwik\Report\ReportWidgetFactory;

/**
 * This class defines a new report.
 *
 * See {@link http://developer.piwik.org/api-reference/Piwik/Plugin/Report} for more information.
 */
class GetBotTracker extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = Piwik::translate('BotTracker_BotTracker');
	$this->subcategoryId = 'BotTracker';
        // This defines in which order your report appears in the mobile app, in the menu and in the list of widgets
        $this->order = 99;

    }

    /**
     * Here you can configure how your report should be displayed. For instance whether your report supports a search
     * etc. You can also change the default request config. For instance change how many rows are displayed by default.
     *
     * @param ViewDataTable $view
     */
    public function configureView(ViewDataTable $view)
    {
		$view->config->translations['botId'] = Piwik::translate('BotTracker_BotId');
		$view->config->translations['botName'] = Piwik::translate('BotTracker_BotName');
		$view->config->translations['botKeyword'] = Piwik::translate('BotTracker_BotKeyword');
		$view->config->translations['botCount'] = Piwik::translate('BotTracker_BotCount');
		$view->config->translations['botLastVisit'] = Piwik::translate('BotTracker_BotLastVisit');
		$view->config->translations['label'] = Piwik::translate('BotTracker_BotActive');
		$view->config->columns_to_display  = array('botName','botKeyword','botCount','botLastVisit','label');
		$view->config->disable_row_evolution  = true;
		$view->config->show_search = false;
		$view->config->show_footer_icons = false;
		$view->config->show_exclude_low_population = false;
		$view->config->show_table_all_columns = false;
		$view->config->show_insights = false;
		$view->config->show_related_reports  = false;
		$view->config->show_pivot_by_subtable = false;
		$view->config->show_table_performance = false;
		$view->config->show_all_views_icons = false;
		$view->config->show_export = false;
		$view->requestConfig->filter_limit = 25;
		$view->requestConfig->filter_sort_column = 'botCount';
		$view->requestConfig->filter_sort_order = 'desc';
    }

    /**
     * Here you can define related reports that will be shown below the reports. Just return an array of related
     * report instances if there are any.
     *
     * @return \Piwik\Plugin\Report[]
     */
    public function getRelatedReports()
    {
        return array(); // eg return array(new XyzReport());
    }
    
	public function configureWidgets(WidgetsList $widgetsList, ReportWidgetFactory $factory)
	{
		
		$widgetsList->addWidgetConfig(
        		$factory->createWidget()->setIsNotWidgetizable()
    			);
	}
    
}
