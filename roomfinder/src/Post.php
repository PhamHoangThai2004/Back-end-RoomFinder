<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php';

class Post {
    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function postFilter($categoryName, $area, $minPrice, $maxPrice, $minAcreage, $maxAcreage) {
        $sql = "SELECT PostID, Title, Acreage, Price, Address FROM post
        JOIN Category ON Post.CategoryID = Category.CategoryID
        WHERE ExpireAt > NOW()";

        $params = [];

        if ($categoryName != 'Tất cả') {
            $sql .= " AND category.CategoryName = ?";
            $params[] = $categoryName;
        }

        if ($area != 'Toàn quốc') {
            $sql .= " AND Address = ?";
            $params[] = $area;
        }

        // Trường hợp user ko lựa chon "Tất cả giá"
        if ($minPrice != -1 || $maxPrice != -1) {
            if ($minPrice != -1) {
                $sql .= " AND Price >= ?";
                $params[] = $minPrice;
            }

            if ($maxPrice != -1) {
                $sql .= " AND Price <= ?";
                $params[] = $maxPrice;
            }
        }

        // Trường hợp user ko lựa chọn "Tất cả diện tích"
        if ($minAcreage != -1 || $maxAcreage != -1) {
            if ($minAcreage != -1) {
                $sql .= " AND Acreage >= ?";
                $params[] = $minAcreage;
            }

            if  ($maxAcreage != -1) {
                $sql .= " AND Acreage <= ?";
                $params[] = $maxAcreage;
            }
        }

        $query = $this->connect->prepare($sql);
        $query->execute($params);
        $rawData = $query->fetchAll();

        $data = [];
        foreach ($rawData as $item) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$item['PostID']]);
            $images = $query_Image->fetch();

            $list = [
                'postID' => $item['PostID'],
                'title' => $item['Title'],
                'price' => $item['Price'],
                'acreage' => $item['Acreage'],
                'address' => $item['Address'],
                'images' => $images ? [
                    ['imagePath' => $images['ImagePath']]
                ] : []
            ];
            $data[] = $list;
        }
        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách lọc thành công',
            'data' => $data
        ]);
    }

    public function listGroup() {
        $data = [];

        $data[] = [
            'titleList' => 'Mới nhất',
            'listPost' => $this->getNewPost()
        ];

        $data[] = [
            'titleList' => 'Khu vực Hà Nội',
            'listPost' => $this->getAddressHNPost()
        ];

        $data[] = [
            'titleList' => 'Khu vực Đà Nẵng',
            'listPost' => $this->getAddressDNPost()
        ];

        $data[] = [
            'titleList' => 'Khu vực TP Hồ Chí Minh',
            'listPost' => $this->getAddressHCMPost()
        ];

        $data[] = [
            'titleList' => 'Được yêu thích nhất',
            'listPost' => $this->getFavoritePost()
        ];

        return json_encode([
            'status' => true,
            'message' => "Lấy danh sách thành công",
            'data' => $data
        ]);
    }

    public function listSearch($keyword, $numeric) {
        $sql = "SELECT PostID, Title, Price, Acreage, Address FROM post WHERE ExpireAt > NOW()";
        if(isset($numeric)) {
            $maxPrice = $numeric + 0.5;
            $minPrice = $numeric - 0.5;
            $maxAcreage = $numeric + 5;
            $minAcreage = $numeric - 5;

            $sql .= " AND ((Price BETWEEN $minPrice AND $maxPrice) OR (Acreage BETWEEN $minAcreage AND $maxAcreage))";
        }

        else {
            $sql .= " AND (Address LIKE '%$keyword%' OR Title LIKE '%$keyword%' OR Description LIKE '%$keyword%')";
        }

        $query = $this->connect->prepare($sql);
        $query->execute();
        $list = $query->fetchAll();

        $data = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $new = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'acreage' => $post['Acreage'],
                'address' => $post['Address'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $data[] = $new;
        }

        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách tìm kiếm thành công',
            'data' => $data
        ]);
    }

    private function getNewPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Address, Tym FROM post ORDER BY CreatedAt DESC LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $new = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'address' => $post['Address'],
                'tym' => $post["Tym"],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $list_Have_Image[] = $new;
        }

        return $list_Have_Image;
    }

    private function getAddressHNPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Address, Tym FROM post WHERE ExpireAt > NOW() AND Address = 'Hà Nội' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $address_HN = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'address' => $post['Address'],
                'tym' => $post["Tym"],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $list_Have_Image[] = $address_HN;
        }

        return $list_Have_Image;
    }

    private function getAddressDNPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Address, Tym FROM post WHERE ExpireAt > NOW() AND Address = 'Đà Nẵng' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $address_DN = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'address' => $post['Address'],
                'tym' => $post["Tym"],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $list_Have_Image[] = $address_DN;
        }

        return $list_Have_Image;
    }

    private function getAddressHCMPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Address, Tym FROM post WHERE ExpireAt > NOW() AND Address = 'TP Hồ Chí Minh' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $address_HCM = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'address' => $post['Address'],
                'tym' => $post['Tym'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $list_Have_Image[] = $address_HCM;
        }

        return $list_Have_Image;
    }

    private function getFavoritePost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Address, Tym FROM post WHERE ExpireAt > NOW() ORDER BY Tym DESC LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $most = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'address' => $post['Address'],
                'tym' => $post['Tym'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : []
            ];
            $list_Have_Image[] = $most;
        }

        return $list_Have_Image;
    }

}

?>