<?php

/**
 * Class DeviceController
 * @author UTC.KongLTN
 * Last Update on Nov 25, 2015
 */
class DeviceController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->titleInHeaderBar = 'Error';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        } else {
            $error = 404;
            $this->render('error', array('error' => $error, 'message' => 'Page not found'));
        }
    }

    /**
     * Create a new userDevice or update data if exists
     */
    public function actionUpdate()
    {
        $id = Yii::app()->request->getParam("id");
        $deviceToken = Yii::app()->request->getParam("token");
        $status = Yii::app()->request->getParam("status");
        $type = Yii::app()->request->getParam("type");

        $obj = UserDevice::updateDeviceToken($id, $deviceToken, $status, $type);

        $json = json_encode(($obj) ? $obj->getAttributes() : null);
        echo $json;
    }

    public function actionTest()
    {
//        $id = Yii::app()->request->getParam('id');
//
//        $test = '{"avaPath":"/upload/avatars/e04929cca5b463192231ffe90e434300_thumbnail.jpg","content":"test android","invitedUser":["1","2"],"fullNameOfCreator":"Samuel Kong","notifyId":"425","ownerId":"2","gender":"his","time":1450428473,"redirect":"messages#message_index_226","type":"mess","idMessage":226}';
//        $test = json_decode($test);
//        $ff = (array) $test;
//        var_dump($ff);
//        $tokenAndroid = array();
//        $a = array("a","b");
//        $tokenAndroid = array_merge($tokenAndroid,$a);
//        $b = array("1","2");
//        $tokenAndroid = array_merge($tokenAndroid,$b);
//
//
//        foreach($ff['invitedUser'] as $key) {
//            if ($key != $ff['ownerId']) {
//                $deviceArray =  UserDevice::model()->getDeviceTokenByUserId($key);
//                $tokenAndroid = array_merge($tokenAndroid, $deviceArray['android']);
//                var_dump($deviceArray);
//            }
//        }
//
//        var_dump($tokenAndroid);
//        $key = 'APA91bFcEJ3oXdD9R-GHHJtebZgFGE7GpU4JVFwlWwkTKKeICj8qEJjpM77M5ui7zwUWmyEDM1-Zlz3-JKUepnQrwc_SAFdpu0lvXtcIcBcfDCJDkQOatdbpIo6nx7CsvjDa0nWA5Hz8';
//        $ar = array($key);
//        $k = array("a"=>123, "b"=>"string");
//        var_dump($ar);
//        var_dump($k);
        // $this->pushToAndroid($ar,$k );

//        $deviceArray =  UserDevice::model()->getDeviceTokenByUserId(1);
//        var_dump($deviceArray);
//        $data = '{"avaPath":"/upload/avatars/Tulips.jpg","content":"kong <br> <br>  giet","invitedUser":["1","2"],"fullNameOfCreator":"Samuel Kong","notifyId":"327","ownerId":"2","gender":"his","time":1449225966,"redirect":"messages#message_index_217","type":"mess","idMessage":217}';
//        $data = json_decode($data);
//        var_dump($data);
//        $this->pushToIOS($deviceArray['ios'], 22, $data);
    }

    /**
     * Push notifications to mobile devices
     */
    public function actionPushToDevice()
    {
        // Get data
        $data = Yii::app()->request->getParam('data');
        $decoded = json_decode($data);  //echo json_encode($decoded);

        // Change content of message
        $message = '';
        if (isset($decoded->content)) {
            if ($decoded->type == 'mess') {
                $message = $decoded->fullNameOfCreator . ' sent a message: ' . $decoded->content;
            } else {
                $message = $decoded->fullNameOfCreator . ' ' . strtolower($decoded->content);
            }
        }
        $message = str_replace('<br>', ' ', $message);
        $message = preg_replace('!\s+!', ' ', $message);
        $decoded->content = $this->shortString($message, 80);

        // Android data
        $androidData = (array)$decoded;

        if (isset($decoded->invitedUser)) {
            foreach ($decoded->invitedUser as $userId) {
                if ($userId !== $decoded->ownerId) {
                    $androidData['badge'] = 0;
                    $tokenAndroid = array();
                    $deviceArray = UserDevice::model()->getDeviceTokenByUserId($userId);
                    $tokenAndroid = array_merge($tokenAndroid, $deviceArray['android']);
                    $androidData['badge'] = (int)$deviceArray['countNotify'];
                    $this->pushToIOS($deviceArray['ios'], $deviceArray['countNotify'], $decoded);
                    $this->pushToAndroid($tokenAndroid, $androidData);
                }
            }
        }


    }

    /**
     * Push message to iOs device
     * @param $deviceArray
     * @param $count
     * @param $data
     */
    public function pushToIOS($deviceArray, $count, $data)
    {
        $message = $data->content;

        $badge = 1;
        if (isset($count)) {
            $badge = (int)$count;
        }

        if ($badge > 0) {
            $passPhrase = '';
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', 'MentorAppProduction.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passPhrase);

            $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err,
                $errStr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp) exit("Failed to connect: $err $errStr" . PHP_EOL);


            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'badge' => $badge,
                'sound' => 'default'
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            foreach ($deviceArray as $obj) {
                $token = (string)trim($obj->deviceToken);
                $msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
                fwrite($fp, $msg, strlen($msg));
            }

            // Close connection
            fclose($fp);
        }
    }

    /**
     * Push message to Android device
     * @param $deviceArray
     * @param $data
     */
    public function pushToAndroid($deviceArray, $data)
    {
        $googleApiKey = "AIzaSyDQCtDL2Q8hhmT6B75rn0ZcTpmTFH54gr0";
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $deviceArray,
            'data' => $data,
        );

        $headers = array(
            'Authorization: key=' . $googleApiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        //else echo "success <br>";
        //echo $result;

        // Close connection
        curl_close($ch);

    }
}