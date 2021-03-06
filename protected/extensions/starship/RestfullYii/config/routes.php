<?php
return [
    array('serviceVideo/upload', 'pattern' => 'api/serviceVideo/upload', 'verb' => 'POST'),
    array('servicePicture/upload', 'pattern' => 'api/servicePicture/upload', 'verb' => 'POST'),
    array('serviceArchive/upload', 'pattern' => 'api/serviceArchive/upload', 'verb' => 'POST'),
    ['<controller>/REST.PUT', 'pattern' => 'api/<controller:\w+>/<id:\w*>', 'verb' => 'PUT'],
    ['<controller>/REST.PUT', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'PUT'],
    ['<controller>/REST.PUT', 'pattern' => 'api/<controller:\w*>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'PUT'],

    ['<controller>/REST.DELETE', 'pattern' => 'api/<controller:\w+>/<id:\w*>', 'verb' => 'DELETE'],
    ['<controller>/REST.DELETE', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'DELETE'],
    ['<controller>/REST.DELETE', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'DELETE'],

    ['<controller>/REST.POST', 'pattern' => 'api/<controller:\w+>', 'verb' => 'POST'],
    ['<controller>/REST.POST', 'pattern' => 'api/<controller:\w+>/<id:\w+>', 'verb' => 'POST'],
    ['<controller>/REST.POST', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'POST'],
    ['<controller>/REST.POST', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'POST'],

    ['<controller>/REST.OPTIONS', 'pattern' => 'api/<controller:\w+>', 'verb' => 'OPTIONS'],
    ['<controller>/REST.OPTIONS', 'pattern' => 'api/<controller:\w+>/<id:\w+>', 'verb' => 'OPTIONS'],
    ['<controller>/REST.OPTIONS', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'OPTIONS'],
    ['<controller>/REST.OPTIONS', 'pattern' => 'api/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'OPTIONS'],

    '<controller:\w+>/<id:\d+>' => '<controller>/view',
    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
];
