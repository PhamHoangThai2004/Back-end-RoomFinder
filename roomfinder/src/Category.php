<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php';

class Category {
    private $connect;

    public function __construct($_connect) {
        $this->connect = $_connect;
    }

    public function listCategory() {
        $query = $this->connect->prepare("SELECT CategoryName FROM category");
        $query->execute();
        $list = $query->fetchAll();


        if ($list) {
            $listCategory = [];
            foreach ($list as $l) {
                $listCategory[] = [
                    'categoryName' => $l['CategoryName']
                ];
            }
            return json_encode([
                'status' => true,
                'message' => "Lấy danh sách danh mục thành công",
                'data' => $listCategory
            ]);
        }
        else {
            return json_encode([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ]);
        }
    }
}
?>