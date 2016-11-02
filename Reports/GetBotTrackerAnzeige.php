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
class GetBotTrackerAnzeige extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = Piwik::translate('BotTracker_DisplayWidget');
        $this->subCategory   = 'BotTracker_BotTracker';
	   //$this->subcategoryId = 'BotTracker';
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
		$view->config->show_search = false;
		$view->config->show_footer_icons = false;
		$view->config->show_exclude_low_population = false;
		$view->config->columns_to_display  = array('botName','botCount','botLastVisit');
		$view->requestConfig->filter_sort_column = 'botCount';
		$view->requestConfig->filter_sort_order = 'desc';
		$view->requestConfig->filter_limit = 10;
		$view->config->disable_row_evolution  = true;
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
    		// we have to do it manually since it's only done automatically if a subcategoryId is specified,
    		// we do not set a subcategoryId since this report is not supposed to be shown in the UI
    		$widgetsList->addWidgetConfig($factory->createWidget());
	}
}
