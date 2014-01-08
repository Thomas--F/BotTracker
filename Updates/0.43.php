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

class Updates_0_43 extends Updates
{
    static function getSql($schema = 'Myisam')
    {
        return array(
            'ALTER TABLE `'.Common::prefixTable('bot_db').'`
	          ADD `extra_stats` BOOLEAN NOT NULL DEFAULT FALSE' => false,
	       
	       'CREATE TABLE IF NOT EXISTS `'.Common::prefixTable('bot_db_stat').'`
			(
			 `botId` INTEGER(10) UNSIGNED NOT NULL,
			 `idsite` INTEGER(10) UNSIGNED NOT NULL,
			 `page` VARCHAR(100) NOT NULL,
			 `visit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			 `useragent` VARCHAR(100) NOT NULL,
			 
			 PRIMARY KEY(`botId`)
			)  DEFAULT CHARSET=utf8' => false,
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