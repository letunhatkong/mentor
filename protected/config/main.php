<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'timeZone' => "UTC",
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    //'controllerMap' => array('myController' => 'myExternalFramework.controllers.admin'),
    //'defaultController' => 'frontend/Index',
    'name' => 'Mentor',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.vendor.jwt.*'
    ),
    'aliases' => array(
        'RestfullYii' => realpath(__DIR__ . '/../extensions/starship/RestfullYii'),
    ),

    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '12345',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('192.168.2.135', '::1'),
        ),
        'admin',
        'auth'
    ),

    // application components
    'components' => array(
        'session' => array(
            'sessionName' => 'YiiSession',
            'class' => 'CDbHttpSession',
            'autoCreateSessionTable' => true,
            'connectionID' => 'db',
            'sessionTableName' => 'YiiSession',
            'autoStart' => 'true',
            'cookieMode' => 'only',
            'timeout' => 31536000,
        ),

        'user' => array(
            // enable cookie-based authentication
            //'allowAutoLogin'=>true,
            'class' => 'WebUser',
        ),

        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'urlFormat' => 'path',
            /*'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                        ),*/
            'rules' => require(
                dirname(__FILE__) . '/../extensions/starship/RestfullYii/config/routes.php'
            ),
        ),


        // database settings are configured in database.php
        'db' => require(dirname(__FILE__) . '/database.php'),
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
            ),
        ),

    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'keyJWT' => 'm3nt0r:secretKey',
        'avatarDefault' => '/images/defaultUser.png',

        'avatarFolderPath' => '/upload/avatars',
        'archiveFolderPath' => '/upload/archives',
        'fileVideoTempFolderPath' => '/upload/videosTemp',
        'fileVideoFolderPath' => '/upload/videos',
        'fileImagesFolderPath' => '/upload/images',
        'RestfullYii' => [
            'req.auth.user' => function () {
                return true;
            },
            'req.auth.ajax.user' => function () {
                return true;
            },
        ]
    ),
);
