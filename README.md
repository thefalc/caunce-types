# Caunce Types

Source code for the website [Caunce Types](http://www.cauncetypes.com/). I created this website as a searchable index of Survivor players combined wtih their Angie Caunce defined [character type](https://robhasawebsite.com/survivor-2014-casting-types-success-winner-statistics/). 

The site is no longer updated, but in the past I have used the data from this site for a number of experiments that you can read about [Crowdsource a behavioral model for Survivor](https://thefalc.com/2017/09/crowdsourcing-a-behavioral-model-for-survivor/) and [Predicting the "Dateability" of Survivor players](https://thefalc.com/2016/09/predicting-the-dateability-of-survivor-players/).

![Caunce Types](/assets/caunce-types.jpeg)

## Technical details

The project runs on the LAMP stack, the backend uses the [CakePHP](http://www.cakephp.org) framework. The frontend is a combination of vanilla Javascript and jQuery. 

The importer creates player records based on their Survivor Wikia entries by parsing the HTML of the page. The importer is unlikely to work at this point since the website's HTML structure has likely changed.

All raw player data, seasons, and character types is available in the [database file](https://github.com/thefalc/caunce-types/blob/main/caunce_types.sql)..

**Code structure**
* The backend code serves the player search and data entry. Take a look at the [app/Controller/](https://github.com/thefalc/caunce-types/blob/main/app/Controller/).
* The frontend pages for displaying the search and players can ve viewed in [app/View/Pages](https://github.com/thefalc/caunce-types/tree/main/app/View/Pages).
* The database structure is defined in [caunce_types.sql](https://github.com/thefalc/caunce-types/blob/main/caunce_types.sql).
