Laravel.VoltDB
==============
VoltDB providers for Laravel  
[![License](http://img.shields.io/packagist/l/ytake/laravel-voltdb.svg?style=flat)](https://packagist.org/packages/ytake/laravel-voltdb)
[![Latest Version](http://img.shields.io/packagist/v/ytake/laravel-voltdb.svg?style=flat)](https://packagist.org/packages/ytake/laravel-voltdb)
[![Total Downloads](http://img.shields.io/packagist/dt/ytake/laravel-voltdb.svg?style=flat)](https://packagist.org/packages/ytake/laravel-voltdb)
[![Dependency Status](https://www.versioneye.com/user/projects/53ef586c13bb06509e0002d4/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53ef586c13bb06509e0002d4)

[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/Laravel.VoltDB.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/Laravel.VoltDB/?branch=master)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/ytake/Laravel.VoltDB/master.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/Laravel.VoltDB/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ytake/Laravel.VoltDB/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ytake/Laravel.VoltDB/build-status/master)

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

#Cache
include voltdb cache driver  
in `app/config/cache.php`:
```php
 'driver' => 'voltdb',
```

#Session
include voltdb session driver  
in `app/config/session.php`:
```php
 'driver' => 'voltdb',
```

**Auth, Cache, Session use Stored Procedure(see schema/ddl.sql)**

#publish for auth, cache, session ddl.sql  
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
CREATE TABLE caches (
  key VARCHAR(255) UNIQUE NOT NULL,
  value VARCHAR(262144),
  expiration INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT PK_cache PRIMARY KEY (key)
);
CREATE INDEX IX_cache_expires ON cache (expiration);
CREATE TABLE sessions (
  id VARCHAR(255) UNIQUE NOT NULL,
  payload VARCHAR(65535),
  last_activity INTEGER DEFAULT 0 NOT NULL
);
CREATE INDEX IX_session_id ON sessions (id);
CREATE INDEX IX_activity ON sessions (last_activity);
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
