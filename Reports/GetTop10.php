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

use Piwik\View;

/**
 * This class defines a new report.
 *
 * See {@link http://developer.piwik.org/api-reference/Piwik/Plugin/Report} for more information.
 */
class GetTop10 extends Base
{
    protected function init()
    {
        parent::init();

        $this->name          = Piwik::translate('BotTracker_Top_10_Bots');
    	   $this->subcategoryId = 'BotTracker';
        $this->subCategory   = 'BotTracker_BotTracker';

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
      	$view->config->translations['value'] = Piwik::translate('BotTracker_hits_by_Bot');
      	$view->config->show_footer_icons = true;
      	$view->config->show_insights = false;
      	$view->config->selectable_columns = array("value");
      	$view->config->max_graph_elements = 10;
		$view->config->disable_row_evolution  = true;
		$view->config->show_related_reports  = true;
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

     public function getDefaultTypeViewDataTable()
     {
     	return 'graphPie';
     }
     public function alwaysUseDefaultViewDataTable()
     {
     	return true;
     }

}
