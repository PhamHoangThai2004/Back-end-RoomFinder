<?php

namespace Pht\Roomfinder;

require_once '../vendor/autoload.php';

class Post {
    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
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
        $sql = "SELECT PostID, Title, Description, Price, Address FROM post WHERE ExpireAt > NOW()";
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
                'description' => $post['Description'],
                'price' => $post['Price'],
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