# Piwik BotTracker Plugin 

## Description

BotTracker ist a plugin to exclude and separately track the visits of bots, spiders and webcrawlers, that hit your page. Because Piwik doesn't store the user agent, BotTracker will only be able to track new bots from the moment you add them to its list forward (retroactive tracking isn't possible).

This plugin is still in BETA-status, but I have tested it for a while. It should be stable.

Before you install this plugin, here's something you should be aware of:
Many webcrawlers, spiders and bots don't load the images in a page and most of them don't execute JavaScript. So you cannot track them with Piwik if you don't use the PHP-API. The BotTracker can only track those that were caught by Piwik itself.

Additional info:
I wrote BotTracker for my own needs but people ask me to make it public - so I put it online.
It's free to use and I will support it as long as I can spend the time. But I will *not* activate a donation button. If someone is paying money, I feel like I have to support him. 
I want to work on this plugin because I want to and not because I have to. I hope you can unterstand that.

## How it works

The plugin scans the user agent of any incoming visit for specific keywords. If the keyword is found, the visit is excluded from the normal log and the corresponding counter in the bot-table (BOT_DB) is increased.
If you enable the "extra stats" for a bot entry, the visit will also be written into a second bot-table (BOT_DB_STAT). This second table logs the timestamp, the visited page and the user agent. The second table is currently not displayed in Piwik, but the more experienced users can select the data from the database. Some more detailed reports may come in the future.

You can add/delete/modify the keywords in the administration-menu. There are webpages that list the user-agents of known spiders and webcrawlers (e.g. http://www.useragentstring.com/pages/Crawlerlist/). The most common bots are already in the default list of the plugin.

## Installation / Update

See http://piwik.org/faq/plugins/#faq_21

For additional information go to http://dev.piwik.org/trac/ticket/2391

If you update form Piwik 1.x to Piwik 2.x sometimes the update-script (BotTracker\Updates\0.43.php) is not executed. In this case you get an error about a not existing column "extra_stats" when you open the config-page. Then you have to execute the following 2 statemnts on your Piwik-Database (e.g. with MyPHPAdmin):

```sql
ALTER TABLE `piwik_bot_db`
ADD `extra_stats` BOOLEAN NOT NULL DEFAULT FALSE
```
```sql
CREATE TABLE IF NOT EXISTS `piwik_bot_db_stat`
(
 `botId` INTEGER(10) UNSIGNED NOT NULL,
 `idsite` INTEGER(10) UNSIGNED NOT NULL,
 `page` VARCHAR(100) NOT NULL,
 `visit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `useragent` VARCHAR(100) NOT NULL,
		 
 PRIMARY KEY(`botId`)
)  DEFAULT CHARSET=utf8
```


The "extra Stats"-Feature is in "early developing". You can collect the data about the who, when and where, but there is currently no widget to display the data.


## Changelog
__0.56__
* bugfix: botLastVisit-Date is not shown (pull request #35)
* bugfix: Some characters are not quoted properly (issue #32)
* a lot more languages. Thanks a lot to all transiflex-supporter

__0.55__
* some minor bugfixes and typos
* add some more languages

__0.54__
* bugfix for Piwik 2.11

__0.53__
* bugfix for cloud-view on "Top 10"
* deactivating insights for "Top 10"
* add more default bots (just use the "add default bots" button, only the new ones will be added)

__0.52__
* bugfix for issue #10 (NOTICE in error-log for undeclared variables)

__0.51__
* emergency-fix for v0.50

__0.50__
* bugfix for issue #9 (wrong time zone for last visit)

__0.49__
* fixed crash with a new and empty webpage

__0.48__
* change requirements because 0.47 doesn't work with Piwik 2.3

__0.47__
* bugfix: changes menu-creation for Piwik v2.4

__0.46__
* bugfix: remove depricated method for Piwik v2.2

__0.45__
* add column to primary key in extra-stats-table

__0.44__
* more description for the marketplace

__0.43__
* Compatible with Piwik 2.0

## License

GPL v3 / fair use

## Support

Please direct any feedback to: 

* [https://github.com/Thomas--F/BotTracker/issues](https://github.com/Thomas--F/BotTracker/issues)
* [http://dev.piwik.org/trac/ticket/2391](http://dev.piwik.org/trac/ticket/2391)

