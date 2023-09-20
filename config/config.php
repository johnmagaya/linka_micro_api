<?php

$config = [
    'id' => 'link-platform-micro-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Africa/Dar_es_Salaam',
    'components' => [
        'webService' => [
            'class' => 'mongosoft\soapclient\Client',
            'url' => 'http://localhost:88/LinkaService/WebService1.asmx?WSDL', //local
            //'url' => 'http://89.117.62.178/sava-web-services?wsdl',  //from the server
            'options' => [
                'cache_wsdl' => WSDL_CACHE_NONE,
                'verifypeer' => false,
                'verifyhost' => false,
                'soap_version' => SOAP_1_2,
                'trace' => true

            ],
        ],
        'mailer' => [                
            'class' => 'yii\swiftmailer\Mailer',               
            'transport' => [                   
                'class' => 'Swift_SmtpTransport',                   
                'host' => 'mail.tbridge.co.tz',                  
                'username' => 'john.magaya@tbridge.co.tz',                  
                'password' => 'Bongoflava01',                   
                'port' => '465',                  
                'encryption' => 'ssl'               
            ],
            'useFileTransport' => false,
        ],
        'helper' => [
            'class' => 'app\components\Helper',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
         ],
        'request' => [
            'class'=>'yii\web\Request',
            'enableCsrfValidation'=>false,
            'enableCookieValidation'=>false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:Server=192.168.88.14;Database=partner_db',
            'username' => 'sys_user',
            'password' => 'sys@2019',
            'charset' => 'utf8',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 50,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/app.log',
                    'except' => [
                        'yii\web\Session:*', // Excludes all session messages
                            // or
                        'yii\web\Session::init', // Exclude only session init
                    ],
                ],
            ],
        ],

        'response' => [
            'class'=>'yii\web\Response',
            'format' =>  \yii\web\Response::FORMAT_JSON,
            'formatters' => [
               \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
               ],
            ]
        ],

        'errorHandler' => [
            'class' =>  'app\components\ErrorHandler'
        ],
    ],
    'params' => [
        'version' => '0.0.1',
        'sms_url' => 'https://external.tbridgetech.com/sms/beem', //server
        //'sms_url' => 'http://192.168.88.14:8088/sms/beem' //local
    ],  
];



return $config;