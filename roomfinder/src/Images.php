<?php

namespace Pht\Roomfinder;

use PDO;
use PDOException;

require_once '../vendor/autoload.php';

class Images {
    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function uploadImages($newImages, $postId) {
        $sql = "INSERT INTO images (PostID, ImagePath, PublicID) VALUES (?, ?, ?)";
        $query = $this->connect->prepare($sql);

        try {
            if ($newImages != null) {
                foreach ($newImages as $image) {
                    $query->execute([
                        $postId,
                        $image['imagePath'],
                        $image['publicId']
                    ]);
                }
            }
            return 1;
        } catch (PDOException $e) {
            return 0;
        }
    }

}

?>