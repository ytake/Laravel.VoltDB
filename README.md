Laravel.VoltDB
==============
VoltDB providers for Laravel  
[![Latest Stable Version](https://poser.pugx.org/ytake/laravel-voltdb/v/stable.svg)](https://packagist.org/packages/ytake/laravel-voltdb) [![Total Downloads](https://poser.pugx.org/ytake/laravel-voltdb/downloads.svg)](https://packagist.org/packages/ytake/laravel-voltdb) [![Latest Unstable Version](https://poser.pugx.org/ytake/laravel-voltdb/v/unstable.svg)](https://packagist.org/packages/ytake/laravel-voltdb) [![License](https://poser.pugx.org/ytake/laravel-voltdb/license.svg)](https://packagist.org/packages/ytake/laravel-voltdb)  

##future plan
.schema builder (stored procedure, .java class)

#Install
**required [ext-voltdb](https://github.com/VoltDB/voltdb-client-php), ext-curl**  
Add the package to your composer.json and run composer update.
```json
"require": {
    "php": ">=5.4.0",
    "ytake/laravel-voltdb": "0.*"
},
```

Add the service provider in app/config/app.php:
```php
'providers' => [
    'Ytake\LaravelVoltDB\VoltDBServiceProvider',
];
```
Add the aliases in app/config/app.php:
```php
'aliases' => [
    'VoltDBApi' => 'Ytake\LaravelVoltDB\VoltDBFacade',
];
```
The service provider will register a voltdb database extension  
**not supported Eloquent, QueryBuilder  
(VoltDB supports PHP client application development using VoltDB PHP client library.)**  

#Configure
Add database connection
```php
'voltdb' => [
    'driver' => 'voltdb',
    'host' => 'localhost',
    'username' => '',
    'password' => '',
    'port' => 21212
],
```
config publish
```bash
$ php artisan config:publish ytake/laravel-voltdb
```
#Database Extension
##@AdHoc query
```php
$sql = "INSERT INTO users (user_id, username, password, remember_token, created_at)"
    ." VALUES (" . rand() . ", 'voltdb', '" . $pass . "', null, '" . date("Y-m-d H:i:s") . "')";
\DB::connection('voltdb')->exec($sql);
$sql = "SELECT * FROM users";
\DB::connection('voltdb')->select($sql);
```
**not supported prepared statement**  
Recommended stored procedure
##stored procedure
```php
\DB::connection('voltdb')->procedure('Auth_findUser', [1]);
```
#Auth
include voltdb auth driver  
in `app/config/auth.php`:
```php
 'driver' => 'voltdb',
```
use stored procedure(default)  
###Auth_findUser  
```sql
CREATE PROCEDURE Auth_findUser AS
  SELECT * FROM users WHERE user_id = ?;
PARTITION PROCEDURE Auth_findUser ON TABLE users COLUMN user_id;
```
###Auth_rememberToken  
```sql
CREATE PROCEDURE Auth_rememberToken AS
  SELECT * FROM users WHERE user_id = ? AND remember_token = ?;
PARTITION PROCEDURE Auth_rememberToken ON TABLE users COLUMN user_id;
```
###Auth_updateToken  
```sql
CREATE PROCEDURE Auth_updateToken AS
  UPDATE users SET remember_token = ? WHERE user_id = ?;
PARTITION PROCEDURE Auth_updateToken ON TABLE users COLUMN user_id PARAMETER 1;
```
#Cache
include voltdb cache driver  
in `app/config/cache.php`:
```php
 'driver' => 'voltdb',
```
use stored procedure(default)  
###Cache_flushAll  
```sql
CREATE PROCEDURE Cache_flushAll AS DELETE FROM cache;
```
###Cache_forget
```sql
CREATE PROCEDURE Cache_forget AS
  DELETE FROM cache WHERE key = ?;
PARTITION PROCEDURE Cache_forget ON TABLE cache COLUMN key;
```
###Cache_find
```sql
CREATE PROCEDURE Cache_find AS
  SELECT * FROM cache WHERE key = ?;
PARTITION PROCEDURE Cache_find ON TABLE cache COLUMN key;
```
###Cache_add
```sql
CREATE PROCEDURE Cache_add AS
  INSERT INTO cache (key, value, expiration) VALUES (?, ?, ?);
```
###Cache_update
```sql
CREATE PROCEDURE Cache_update AS
  UPDATE cache SET value = ?, expiration = ? WHERE key = ?;
PARTITION PROCEDURE Cache_update ON TABLE cache COLUMN key PARAMETER 2;
```

#publish for auth, cache ddl.sql  
```bash
$ php artisan ytake:voltdb-schema-publish
```
**Options:**  
 --publish (-p)        Publish to a specific directory  
default app/storage/schema/ddl.sql

###DDL
default ddl
```sql
CREATE TABLE users (
   user_id INTEGER UNIQUE NOT NULL,
   username VARCHAR(40) NOT NULL,
   password VARCHAR(64) NOT NULL,
   remember_token VARCHAR(128) DEFAULT NULL,
   created_at TIMESTAMP NOT NULL,
   PRIMARY KEY(user_id)
);
CREATE INDEX UsersIndex ON users (username, password, remember_token);
CREATE TABLE cache (
  key VARCHAR(255) UNIQUE NOT NULL,
  value VARCHAR(262144),
  expiration INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT PK_cache PRIMARY KEY (key)
);
CREATE INDEX IX_cache_expires ON cache (expiration);
```


#Facades
supported for json interface API  

```php
// call stored procedure @SystemInformation
\VoltDBApi::request()->info()->getResult();

// basic
\VoltDBApi::request()->post([
    'Procedure' => 'addUser',
    'Parameters' => [1, "voltdb"]
])->getResult();
```
###Async Stored Procedure
see [VoltDB.PHPClientWrapper](https://github.com/ytake/VoltDB.PHPClientWrapper)

#Console
**ytake:voltdb-schema-publish**   publish DDL for auth, cache driver  
**ytake:voltdb-info**             information about ytake/laravel-voltdb  
**ytake:voltdb-system-catalog**   renderer system catalog  
##voltdb-system-catalog
**Options:**  
 --component (-c)      returns information about the schema of the VoltDB database, depending upon the component keyword you specify.
![alt text](http://ytake.github.io/images/voltdb-system-catalog.png)

__example__  
Such as MySQL SHOW COLUMNS
```bash
$ php artisan ytake:voltdb-system-catalog -c COLUMNS
```
![alt text](http://ytake.github.io/images/voltdb-system-catalog-column.png)
