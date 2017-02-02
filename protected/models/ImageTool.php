<?php

/**
 * Class ImageTool
 * @author UTC.HuyTD
 */
class ImageTool
{
    /**
     * Resize thumbnail Image
     * @param $folderPathOfThumbnail
     * @param $filename
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function resizeThumbnailImage($folderPathOfThumbnail, $filename, $width = 0, $height = 0)
    {
        try {
            if (file_exists($filename)) {
                $fileInfo = pathinfo($filename);
                $fileExtension = $fileInfo['extension'];

                $thumbnailFileName = $fileInfo['filename'] . "_thumbnail." . $fileExtension;

                // Create image with width & height
                $image = new Image($filename);
                $image->resize($width, $height);
                $image->save($folderPathOfThumbnail . "/" . $thumbnailFileName);

                return $folderPathOfThumbnail . "/" . $thumbnailFileName;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return "";
    }
}

