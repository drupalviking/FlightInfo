
##Get Composer

First of all we need [Composer](https://getcomposer.org/), it will handle all our dependencies.
The best thing is to install it globally, that way it's easier to run it from the command-line.

	$ curl -sS https://getcomposer.org/installer | php
	$ sudo mv composer.phar /usr/local/bin/composer

(if you can't run `composer` from the terminal you may need to do `sudo chmod a+x /usr/local/bin/composer` )

##Get ZF2

I have made an Skeleton project with the ZF2 Framework preconfigured (but not installed).  You can start by fetching it:
	$ mkdir FlightInfo
	$ cd FlightInfo
	$ https://github.com/drupalviking/ZF2-Skeleton.git

Remove the folder module/FlightInfo from the Skeleton:
	$ rm -rf module/FlightInfo

##Get FlightInfo

Now we are ready to get the actual FlightInfo module code.

	$ cd module
	$ https://github.com/drupalviking/FlightInfo.git

This will clone our FlightInfo module into the `module` directory. When we start to develop, this is what we
will change and commit back to GitHub.

##Installation
Go to the root folder and run php composer.phar install, to install the Zend Framework.  Then go to the module folder
and install the module:
	$ cd (workspace)/FlightInfo
	$ php composer.phar install

Now we need to config out system so that it can connect to the Databaseþ
_ZF2 MVC_ applications looks for files that follow this naming pattern `<workspace>/FlightInfo/config/autoload/*.local.php`
and load them in as config files. We want to make our own.

Create a new file `<workspace>/FlightInfo/config/autoload` and call it `flightinfo.local.php`, add this to it:

```php
<?php

return array(
	'db' => array(
		'dns' => 'mysql:dbname=flightinfo;host=127.0.0.1',
		'user' => 'root',
		'password' => ''
	),
);
```

Now we want to to tell our system about our module and that we want our 3rd party libraries to be
loaded from its `vendor` directory, not in the root.

Open `</workspace>/FlightInfo/config/application.config.php` and change accordingly.

```php
return array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
        'FlightInfo',
    ),

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './module/FlightInfo/vendor',
        ),
```

Change this file as well `init_autoloader.php`, so it says:

```php
if (file_exists('module/FlightInfo/vendor/autoload.php')) {
    $loader = include 'module/FlightInfo/vendor/autoload.php';
}
```

We have to create a config directory for our testing environment.

Copy/paste the whole `<workspace>/FlightInfo/config/autoload` directory and name it `test`, change `flightinfo.local.php`
in that directory to reflect testing environment.

Now go into the module and get all dependencies

	$ cd module/FlightInfo
	$ composer install

Since (Apache's) httpd folder is `<workspace>/FlightInfo/public` but all our js/css code
is located in `<workspace>/FlightInfo/module/FlightInfo/public`, we have to connect the resources folder to the
httpd folder.

	$ ln -s <full/path/to/workspace>/FlightInfo/module/FlightInfo/public/flight-info <full/path/to/workspace>/FlightInfo/public/flight-info

##Get resources

Make sure you have [Bower](http://bower.io/) set up, and the go to

    $ cd <workspace>/FlightInfo/module/FlightInfo/public/flight-info/
    $ bower install
    $ bower install bootstrap-sass-official

##Get database
Go to the running production server and do `mysqldump` on the old database. Copy it to your local
machine and install it. (make sure that there exists a database called `flightinfo`)

    $ mysql -u root flightinfo < /<path/to/dump.sql>

Then run the migration script on top of it

    $ mysql -u root flightinfo < <workspace>/FlightInfo/module/FlightInfo/assets/db/migrate.sql




##RabbitMQ
The Stjornvisi module is dependent on RabbitMQ to do it's long running tasks. Installing RabbitMQ is
easily done with brew.

###Install the Server
Before installing make sure you have the latest brews:

    $ brew update

Then, install RabbitMQ server with:

    $ brew install rabbitmq

####Run RabbitMQ Server

    $ rabbitmq-server

The RabbitMQ server scripts are installed into /usr/local/sbin. This is not automatically added to your path, so you may wish to add
PATH=$PATH:/usr/local/sbin to your .bash_profile or .profile. The server can then be started with rabbitmq-server.

All scripts run under your own user account. Sudo is not required.

##Run

Now go back to the root _public_ folder and run the builtin-server

    $ cd <workspace>/Stjornvisi/public
    $ php -S 0.0.0.0:8080


###PHPStorm
I find it better to run the builtin-server from PHPStorm. This is how my config looks like
![alt](https://cloud.githubusercontent.com/assets/386336/5754975/5ef64ad0-9cf3-11e4-8045-e3a81ecde12a.png)

###Other services
But FlightInfo is a complicated application and it need more processes that just the WebService one. Every time
a `notify` event is fired from a controller, a message is sent to a queue (RabbitMQ). A php process needs to
be started that pulls messages out of this queue. To start that process, create a _Run Configuration_ for
PHPStorm that looks like this
![alt](https://cloud.githubusercontent.com/assets/386336/5755091/99aa1872-9cf4-11e4-97f3-e23eff51ad29.png)
and then actually start it. You can and
should [read more about all the processes](https://github.com/fizk/Stjornvisi/wiki/Processes)

## UnitTests ##
It is really important to be able to unit-test this code. Do the following:

###Database
Make sure you have database called `flightinfo_test` and that it's exactly the same as the production
database except that's empty.

One way of doing this is to import the `flightinfo-empty.sql` located in `assets`

    $ mysql -u root flightinfo_test < <workspace>/FlightInfo/module/FlightInfo/assets/flightinfo-empty.sql

This may, or may not be the most up to date version of the schema. To make sure that the schemas are up to
date you have to import the migration script. This can be done my running the migration script.

    $ mysql -u root flightinfo_test < <workspace>/FlightInfo/module/FlightInfo/assets/migrate.sql

This can on the other hand produce errors. The only way to make sure that all migration commands have run is to
open `migrate.sql` in *MySQL Workbench* and run each statement one by one, just to make sure that all of them get
executed.

###Config
Next we have to make sure that the system is set up for unit-test environment. Under the skeleton root
there should be a folder called `<workspace>/FlightInfo/config/test`, it should mimic the `autoload` folder.

Make sure that `flightinfo.local.php` file is pointing to the test database

```php
<?php

return array(
	'db' => array(
		'dns' => 'mysql:dbname=flightinfo_test;host=127.0.0.1',
		'user' => 'root',
		'password' => ''
	),
```

###PHPStorm
Now it's time to config PHPStorm to run PHPUnit tests. Go to *Preferences* and point to _autoloader_ and
_phpunit config_ file

![alt](https://cloud.githubusercontent.com/assets/386336/5752537/ceb28f64-9ccb-11e4-810f-17bcc6957f10.png)
Now you can right-click on any test file and run it as a PHPUnit

![alt](https://cloud.githubusercontent.com/assets/386336/5754360/e4c2d474-9ceb-11e4-8ddb-108e64508086.png)

# Commandline #
This module comes with some command line actions.

To run command-line actions you only have to point your PHP runtime to the index file.

    $ php <workspace>/FlightInfo/public/index.php [arguments]

and then you can pass in some arguments.

You can read the [Process documentation](https://github.com/fizk/Stjornvisi/wiki/Processes) for
detailed examples.


(LOCK TABLES)(([\w\W]*?))(UNLOCK TABLES;)

AUTO_INCREMENT=[0-9]*


http://brewformulas.org/Wkhtmltopdf

http://sourceforge.net/projects/wkhtmltopdf/?source=typ_redirect

https://github.com/cangelis/php-pdf
https://github.com/wkhtmltopdf/wkhtmltopdf