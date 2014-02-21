BotTracker
==========

BotTracker-Plugin for Piwik

for additional information go to http://dev.piwik.org/trac/ticket/2391

This plugin is still in BETA-status, but I have testet it for a while. It should be stable.

Before you install this plugin, some things you should be aware of:

- many webcrawler, spiders and bots don't load the images in a page and most of them don't execute JacaScript. So you cannot track them with Piwik if you don't use the PHP-API. The BotTracker can only track those, who were caught by Piwik itself.

- if you update form Piwik 1.x to Piwik 2.x sometimes the update-script (BotTracker\Updates\0.43.php) is not executed. In this case you get an error about a not existing column "extra_stats" when you open the config-page. Then you have to execute the following 2 statemnts on your Piwik-Database (e.g. with MyPHPAdmin):

ALTER TABLE `piwik_bot_db'`
ADD `extra_stats` BOOLEAN NOT NULL DEFAULT FALSE


CREATE TABLE IF NOT EXISTS `piwik_bot_db_stat`
(
 `botId` INTEGER(10) UNSIGNED NOT NULL,
 `idsite` INTEGER(10) UNSIGNED NOT NULL,
 `page` VARCHAR(100) NOT NULL,
 `visit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `useragent` VARCHAR(100) NOT NULL,
		 
 PRIMARY KEY(`botId`)
)  DEFAULT CHARSET=utf8


- the "extra Stats"-Feature is in "early developing". You can collect the data about the who, when and where, but there is currently no widget to display the data.
