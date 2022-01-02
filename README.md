# Obtaining currency rates (day, period, charts) #
*** 
![project-main](https://user-images.githubusercontent.com/96653165/147747920-b37dcd00-e81f-471e-b0fb-3bed60c7824a.PNG)
***

## Opportunities ##

Get currency rates from the CBRF website for any day
Get the history of the exchange rate of any currency
Drawing a chart with days and currency values for that day
Caching the result in the MySQL database (with protection against re-entering the same information)

_you can look at the folder gifs and and see what is written above only with the help of animation_
***

## Info ##
The site where the exchange rates come from - https://www.cbr.ru/ - CBRF

Caching in a database based on MEMORY, a class (MemoryCache) that implements the interface between
the library and the database checks for the entered parameters, protecting against repeated
entering the same information

Chart - Google Charts - https://developers.google.com/chart/interactive/docs/gallery/linechart

***

## ___To work with___ ##

I used XAMPP, you can use whatever you like.

For everything to work, you need to change the ___config.ini___ file located in the root of the project
You must specify host, username, password, database name and table name (database will auto create after first request)

Default:

\$ serverName = "localhost";
   \$ userName = "root";
   \$ password = "";
   \$ dbName = "PHP_Project";
   \$ tableName = "Currency_Rates";

_also don't forget to enable apache server and database_
***

#### config.ini ####
```
; Настройки базы данных(database settings )

; serverName - default:localhost         (хост бд)                 string
; userName   - default:root              (имя пользователя бд)     string
; password   - default:                  (пароль от бд)            string
; dbName     - default:PHP_Project 	 (имя СОЗДАВАЕМОЙ бд)      string
; tableName  - default:Currency_Rates    (имя СОЗДАВАЕМОЙ таблицы) string

[settings]
serverName = "localhost";
userName   = "root";
password   = "";
dbName     = "PHP_Project";
tableName  = "Currency_Rates";
```
