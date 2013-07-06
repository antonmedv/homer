Homer
=====
This is a simple internet search engine. It is composed from two parts:
* Front End - Silex application with interface based on Bootstrap.
* Demon - internet spider built on React PHP.

For databases and search using SQLite full text (fts3).

![screen shot](http://f.cl.ly/items/031E2j2j2T1P2C0R041h/screen.png)

Install
-------
Download or clone this repository. Open dir in terminal. And run Composer.
```
php composer.phar install
```
Copy config.php.dist to config.php. Open and edit configuration.

SQLite database by default stores in open/homer.db.
Be sure what php have write access to it and run next command:
```
php install.php
```
When delete this file.

Demon
-----
To start demon run next command:
```
php demon.php
```

Statistic
---------
Homer have built in PHP memory usage statistic.
![PHP Memory Usage](http://f.cl.ly/items/1v262P2C2A02393F3x3r/%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA%20%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%D0%B0%202013-07-06%20%D0%B2%2014.38.26.png)

Configuration
-------------
* HOMER_DNS - DNS string for SQLite database.
* HOMER_RESOLVER_ADDRESS - IP address for dns resolver (default: 8.8.8.8);
* HOMER_TIMER - Demon tick timer in seconds.
* HOMER_DEEP - How deep demon will be following by hyperlinks.
* HOMER_KEEP_HOST - Keep deep search only if host equals with parent page.
* HOMER_LIMITER_TIME - How much time do not reindex already indexed pages.
* HOMER_STAT - Turn on or off statistic.
* HOMER_HTTP_HOST - Host of http server there statistic can be loaded (default: localhost)
* HOMER_HTTP_PORT - Post of http server 1333.

License
-------
Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php