<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class ApiController extends CController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            array('RestfullYii.filters.ERestFilter + REST.GET, REST.PUT, REST.POST, REST.DELETE'),
        );
    }

    public function actions()
    {
        return array(
            'REST.' => 'RestfullYii.actions.ERestActionProvider',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('REST.GET', 'REST.PUT', 'REST.POST', 'REST.DELETE'),
                'users' => array('*'),
            ),
            /* 
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
            */
        );
    }

    public function restEvents()
    {
        $this->onRest('model.instance', function() {
            return new Users();
        });
        
        /*
        $this->onRest('req.auth.username', function(){
            return 'admin@restuser';
        });

        $this->onRest('req.auth.password', function(){
            return 'admin@Access';
        });
         */
    }
    
    /**
     * Function change for array_column function in php 5.4 if current php version < 5.4
     * @param array $input
     * @param string $columnKey
     * @return array $array
     */
    public function arrayColumn(array $input, $columnKey) {
        $array = array();
        foreach ($input as $value) {
            if (isset($value[$columnKey]) && !is_null($value[$columnKey])) {
                $array[] = $value[$columnKey];
            }
        }
        return $array;
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

}