Laravel.VoltDB
==============
VoltDB providers for Laravel  

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
#auth
in app/config/auth.php:
```php
 'driver' => 'voltdb',
```
use stored procedure(default)  
Auth_findUser  
Auth_rememberToken  
Auth_updateToken  

publish for auth ddl.sql  
```bash
$php artisan ytake:voltdb-auth-publish
```
**Options:**  
 --publish (-p)        Publish to a specific directory  
default app/storage/schema/ddl.sql

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
**ytake:voltdb-auth-publish**     publish DDL for auth driver  
**ytake:voltdb-info**             information about ytake/laravel-voltdb  
**ytake:voltdb-system-catalog**   renderer system catalog  
##voltdb-system-catalog
**Options:**  
 --component (-c)      returns information about the schema of the VoltDB database, depending upon the component keyword you specify.
![alt text](http://ytake.github.io/images/voltdb-system-catalog.png)
(all)
