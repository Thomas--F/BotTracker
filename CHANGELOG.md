## Changelog
1.06
-  correct default for "botLastVisit" (issue #63)

1.05

- removed default on visit_timestamp (issue #53)
- changed primary key and add aditional column for stats table (issue #53)
- changed default for last_visit (issue #61)
- corrected delimiter in botlist.txt (issue #62) 

1.04

- change license string (validator-fail)

1.03

- replace depricated functions

1.02

- change PHP-requirements for Piwik v3

1.01

- changes at description and changelog for Piwik v3

1.00

- upgrade to Piwik Version 3 (issue #50)
- some parts were new coded, others are only migrated

0.58

- new feature: BotTracker now works with the import_logs-script (issue #38)
- add: some new translation-strings (issue #46)
- bufgix: truncate the url to max 100 bytes (issue #49)

0.57

- bugfix: change of order and position in the BotTracker-Visitor-View
- deleting of the old update-scripts (from version 0.43 and 0.45)
- bugfix: change of the default-value for botLastVisit '0000-00-00' to '2000-01-01'
- new feature: file import for new bots (see online-help in the administration-dialog for more infos)

0.56

- bugfix: botLastVisit-Date is not shown (pull request #35)
- bugfix: Some characters are not quoted properly (issue #32)
- a lot more languages. Thanks a lot to all transiflex-supporter

0.55

- some minor bugfixes and typos
- add some more languages

0.54

- bugfix for Piwik 2.11

0.53

- bugfix for cloud-view on "Top 10"
- deactivating insights for "Top 10"
- add more default bots (just use the "add default bots" button, only the new ones will be added)

0.52

- bugfix for issue #10 (NOTICE in error-log for undeclared variables)

0.51

- emergency-fix for v0.50

0.50

- bugfix for issue #9 (wrong time zone for last visit)

0.49

- fixed crash with a new and empty webpage

0.48

- change requirements because 0.47 doesn't work with Piwik 2.3

0.47

- bugfix: changes menu-creation for Piwik v2.4

0.46

- bugfix: remove depricated method for Piwik v2.2

0.45

- add column to primary key in extra-stats-table

0.44

- more description for the marketplace

0.43

- Compatible with Piwik 2.0

