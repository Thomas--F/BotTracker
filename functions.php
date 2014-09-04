<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package BotTracker
 */

namespace Piwik\Plugins\BotTracker;

use Piwik\Piwik;


function getActiveIcon($botActive)
{
	if ($botActive == 1){
		$pathWithCode = 'plugins/BotTracker/images/ok.png';
	} else {
		$pathWithCode = 'plugins/BotTracker/images/delete.png';
	}

	return $pathWithCode;
}

function getRequest($key) {   
    return (isset($_REQUEST[$key]) && $_REQUEST[$key])?$_REQUEST[$key]:'';
}
function getServer($key) {   
    return (isset($_SERVER[$key]) && $_SERVER[$key])?$_SERVER[$key]:'';
}

