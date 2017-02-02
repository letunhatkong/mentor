<?php

/**
 * Class Controller
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 *
 * @author UTC.HuyTD
 * @author UTC.KongLtn
 * @author UTC.BaoDTQ
 * Last Update on Nov 20, 2015
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/layoutFrontEnd';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    /**
     * @var null
     */
    public $titleInHeaderBar = null;

    public $showBackButtonInHeaderBar = false;

    /**
     * Function change for array_column function in php 5.4 if current php version < 5.4
     * @param array $input
     * @param string $columnKey
     * @return array $array
     */
    public function arrayColumn(array $input, $columnKey)
    {
        $array = array();
        foreach ($input as $value) {
            if (isset($value[$columnKey]) && !is_null($value[$columnKey])) {
                $array[] = $value[$columnKey];
            }
        }
        return $array;
    }

    /**
     * @var array search data
     */
    public $searchData = array();

    /**
     * Check user isGuest and get user Id
     * @return integer - user Id
     */
    public function checkIsGuest()
    {
        return (Yii::app()->user->isGuest) ? 0 : Yii::app()->user->idUser;
    }

    /**
     * Get search Data
     * @return array $this->searchData
     */
    public function getSearchData()
    {
        $this->searchData["users"] = Users::model()->findAll();
        $this->searchData["topics"] = Topics::model()->findAll();
        return $this->searchData;
    }

    /**
     * Search function in menu bar
     */
    public function actionSearch()
    {
        $this->titleInHeaderBar = 'Search Result';
        // Redirect to login page if isGuest
        if (Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->getBaseUrl() . "/auth/user/login");
        }
        $submit = Yii::app()->request->getParam("submitSearchForm");
        if (!isset($submit)) {
            $this->redirect(Yii::app()->getBaseUrl() . "/");
        }

        $uId = Yii::app()->user->idUser;
        $iStr = Yii::app()->request->getParam("inputStringSearch");
        $iStr = preg_replace('!\s+!', ' ', $iStr);
        $iStr = trim($iStr);
        $uSelect = Yii::app()->request->getParam("selectedUserSearch");
        $tSelect = Yii::app()->request->getParam("selectedTopicSearch");
        $fromDate = Yii::app()->request->getParam("fromDate");
        $toDate = Yii::app()->request->getParam("toDate");

        // Search multi string
        $searchArr = array();
        array_push($searchArr, $iStr);
        foreach (explode(" ", $iStr) as $item) array_push($searchArr, $item);
        $data = Sessions::model()->searchAdvanced($uId, $searchArr, $uSelect, $tSelect, $fromDate, $toDate);

        if (isset($data["dataPlanned"]) && !is_null($data["dataPlanned"])) {
            foreach ($data["dataPlanned"] as $key => $row) {
                $data["dataPlanned"][$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
                $data["dataPlanned"][$key]['description'] = $this->limitShow($row['description']);
                $dataNumCommentInPlannedTab = Comments::model()->getNumComment((int)$row['idSession']);
                $data["dataPlanned"][$key]['numComment'] = $dataNumCommentInPlannedTab;
            }
        }

        if (isset($data["dataPast"]) && !is_null($data["dataPast"])) {
            foreach ($data["dataPast"] as $key => $row) {
                $data["dataPast"][$key]['timeElapse'] = $this->getTiming($row['dateCreate']);
                $data["dataPast"][$key]['description'] = $this->limitShow($row['description']);
                $dataNumCommentInPastTab = Comments::model()->getNumComment((int)$row['idSession']);
                $data["dataPast"][$key]['numComment'] = $dataNumCommentInPastTab;
            }
        }

        $this->render('../site/searchResults', array(
            'dataPlanned' => $data["dataPlanned"],
            'dataPast' => $data["dataPast"]
        ));

        // Render
        //$this->render("../site/searchResults", array("dataSearch" => $data));
    }

    /**
     * Get Timing
     * @param $date
     * @return string
     */
    function getTiming($date)
    {
        $time = strtotime($date);
        $timeMinus = time() - $time;
        $timeMinus = ($timeMinus < 1) ? 1 : $timeMinus;
        if ($timeMinus <= 24 * 3600) {
            return $this->getTimingElapse($time) . ' ago';
        } else {
            return date("d.m.Y", strtotime($date));
        }
    }

    /**
     * Get Timing elapse
     * @param $time
     * @return string
     */
    function getTimingElapse($time)
    {
        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
        return "undefined";
    }

    /**
     * Get Timing today Type
     * @param string $date
     * @return string
     */
    function getTimingTodayType($date)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        if (date("Y-m-d", strtotime($date)) == $today) {
            return 'Today ' . date("H:i", strtotime($date));
        } elseif (date("Y-m-d", strtotime($date)) == $yesterday) {
            return 'Yesterday ' . date("H:i", strtotime($date));
        } else {
            return date("d.m.Y", strtotime($date));
        }
    }

    /**
     * Get timing compared to the current time
     * @param $time
     * @return string
     */
    function toTimeNotify($time)
    {
        $timeMinus = time() - $time;
        $timeMinus = ($timeMinus < 2) ? 2 : $timeMinus;

        if ($timeMinus <= 24 * 3600) {
            $interval = floor($timeMinus / 3600);
            if ($interval == 1) return $interval . " hour ago";
            if ($interval > 1) return $interval . " hours ago";
            $interval = floor($timeMinus / 60);
            if ($interval == 1) return $interval . " minute ago";
            if ($interval > 1) return $interval . " minutes ago";
            return floor($timeMinus) . " seconds ago";
        } else {
            return date('d.m.y H:i', $time);
        }
    }


    /**
     * Limit show text
     * @param string $string
     * @return string is limited characters
     */
    function limitShow($string)
    {
        $string = strip_tags($string);
        if (strlen($string) > 120) {
            $stringCut = substr($string, 0, 120);
            $string = substr($stringCut, 0, strrpos($stringCut, ' '));
        }
        return $string;
    }

    /**
     * Get All notification by user Id
     * @param $id
     * @return array
     */
    function getAllNotify($id)
    {
        return Notify::model()->getAllNotifyById($id);
    }

    /**
     * Count all notification (not read) by user id
     * @param $id
     * @return string
     */
    function countNotifications($id)
    {
        return NotifyUser::model()->countByAttributes(array(
            'userId' => $id,
            'isRead' => 0
        ));
    }

    /**
     * Get setting data
     * @return array|mixed|null|static
     */
    function getSettingData(){
        return Setting::model()->find();
    }

    /**
     * Create thumbnail image
     * @param $folderPathOfThumbnail
     * @param string $directImage
     * @param int $defaultWidth
     * @return string
     */
    public static function createThumbnailImage($folderPathOfThumbnail, $directImage = 'images/no_image.jpg', $defaultWidth = 150)
    {
        $thumbnailPathCreated = "";

        if ($defaultWidth == "") {
            return $directImage;
        }

        if (file_exists($directImage)) {
            $size = getimagesize($directImage);

            // Get image width & height
            $imgWidth = $size[0];
            $imgHeight = $size[1];

            if ($imgHeight > $imgWidth) {
                if ($imgHeight != $defaultWidth) {
                    $imgHeightNew = $defaultWidth;
                    $scaleHeight = round($imgHeightNew / $imgHeight, 2);
                    $imgWidthNew = $imgWidth * $scaleHeight;
                    // Create thumbnail image
                    $thumbnailPathCreated = ImageTool::resizeThumbnailImage($folderPathOfThumbnail, $directImage, $imgWidthNew, $imgHeightNew);
                }
            } else {
                if ($imgWidth != $defaultWidth) {
                    $scale = round($defaultWidth / $imgWidth, 2);
                    // Create thumbnail image
                    $thumbnailPathCreated = ImageTool::resizeThumbnailImage($folderPathOfThumbnail, $directImage, $imgWidth*$scale, $imgHeight*$scale);
                }
            }
        }

        if ($thumbnailPathCreated != "") {
            if (substr($thumbnailPathCreated, 0, 1) == "/")
                $thumbnailPathCreated = substr($thumbnailPathCreated, 1);
        }
        return $thumbnailPathCreated;
    }

    /**
     * Short string
     * @param $string
     * @param $number
     * @return string
     */
    public function shortString($string, $number) {
        if (strlen($string) > $number) {
            return substr($string,0,$number) . '...';
        } else return $string;
    }
}