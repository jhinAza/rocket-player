# universal-player

## Requirements

+ A web server with support of PHP (It has been tested on WAMP and Apache2 on Ubuntu 16.04)
+ PHP Interpreter (The project has been tested on PHP 5.5 and PHP 7)
+ MySQL server (The project has been tested on MySQL 5.6 or higher)
+ [Bootstrap 3](http://getbootstrap.com/getting-started/#download)
+ [jQuery 3.x](http://jquery.com/download/)
+ [jQuery forms](http://malsup.github.io/jquery.form.js)
+ SCSS to CSS converter (Only if you need to change the themes)
+ JS and CSS minifier (Only if you need to change the files)

# Installation
```
sudo apt-get install apache2 php5 libapache2-mod-php5 php5-mysql
```
## Important notes
To create the player I've created a JS Library that creates a common interface for controlling the HTML5 media element across browsers. This interface also allows the same controls for the messy fullscreen api, it takes into account that different browsers have different methods for requesting fullscreen or exiting fullscreen, and has different styles when on fullscreen the library sets some events that when are triggered changes the style of the HTML element that was on fullscreen.

As of writing this README file the use of this player on other browsers than Firefox or Google Chrome is discouraged as it is untested. The use of the player on MS EDGE is unsupported as its methods and styles are heterogeneous.

It's recommended to update this lines on the php.ini file in your system. By default they will be 8M and 2M respectively, but you can change them to anything you like. Take into account that the client-side validation stops the client to upload any file bigger than 300MB
```
upload_max_filesize = 2G
post_max_size = 2G
```

It's also recommended to change the .conf file of your site on `/etc/apache2/sites-available` and add the following statement 
```
DirectoryIndex home.php
```

# A word from the creator
Please before using this project take into account that this is a school project and can be improved in various ways, if you are interested in using it please before installing and using it in prod check that everything works great in your system and check the file at `universal-player/www/serverSettings/server_settings.xml` and add the info of the mysql server and the credentials as the credentials in this file are absolutely fake and you should never use them even if someone is pointing at you with a loaded gun. :fearful:

A last thing before you can go ahead and lost yourself in my little project, please, check the security settings of the server, the ´DatabaseController´ is using MD5 to cypher the passwords (Nah, not really, I've used the crypto functions of PHP, but even so, please check the code to add any improvement to the security that you would like to have in your production server)
