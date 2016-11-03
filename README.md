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

### How it works

The plugin scans the user agent of any incoming visit for specific keywords. If the keyword is found, the visit is excluded from the normal log and the corresponding counter in the bot-table (BOT_DB) is increased.
If you enable the "extra stats" for a bot entry, the visit will also be written into a second bot-table (BOT_DB_STAT). This second table logs the timestamp, the visited page and the user agent. The second table is currently not displayed in Piwik, but the more experienced users can select the data from the database. Some more detailed reports may come in the future.

You can add/delete/modify the keywords in the administration-menu. There are webpages that list the user-agents of known spiders and webcrawlers (e.g. http://www.useragentstring.com/pages/Crawlerlist/). The most common bots are already in the default list of the plugin.

### Installation / Update

See http://piwik.org/faq/plugins/#faq_21

If you update form Piwik 1.x to Piwik 2.x or from an old version of BotTracker (before 0.45) please reinstall the plugin.

# License

GPL v3 / fair use

# Support

Please direct any feedback to: 

* [https://github.com/Thomas--F/BotTracker/issues](https://github.com/Thomas--F/BotTracker/issues)


