<?php
/**
* Piwik - Open source web analytics
*
* @link http://piwik.org
* @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
*
* @category Piwik
* @package Updates
*/

namespace Piwik\Plugins\BotTracker;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updates;

class Updates_0_45 extends Updates
{
    static function getSql($schema = 'Myisam')
    {
        return array(
            'ALTER TABLE `'.Common::prefixTable('bot_db').'`
                DROP PRIMARY KEY,
                ADD PRIMARY KEY(
                 `botId`,
                 `visit_timestamp`)' => false
        );
    }

    static function isMajorUpdate()
    {
        return true;
    }

    static function update()
    {
        Updater::updateDatabase(__FILE__, self::getSql());
    }

}
