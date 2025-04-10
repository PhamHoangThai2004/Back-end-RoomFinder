<?php

namespace Pht\Roomfinder;

use PDOException;

require_once '../vendor/autoload.php';
require_once '../notification/notification.php';

class Post {
    private $connect;

    public function __construct($_connect)
    {
        $this->connect = $_connect;
    }

    public function updatePost($data, $userId) {
        $category = new Category($this->connect);
        $categoryId = $category->getCategoryById($data['category']['categoryName']);

        if ($data['user']['userId'] == $userId) {
            $sql = "UPDATE post 
                    SET CategoryID = ?, Title = ?, Description = ?, Price = ?, Acreage = ?, Bonus = ?";
            if ($data['expireAt'] == 3) {
                $sql .= ", ExpireAt = CURRENT_TIMESTAMP + INTERVAL 3 DAY";
            }
            else if ($data['expireAt'] == 5) {
                $sql .= ", ExpireAt = CURRENT_TIMESTAMP + INTERVAL 5 DAY";
            }
            else if ($data['expireAt'] == 7) {
                $sql .= ", ExpireAt = CURRENT_TIMESTAMP + INTERVAL 7 DAY";
            }
            $sql .= " WHERE PostID = ?";
            $params = [$categoryId, $data['title'], $data['description'], $data['price'], $data['acreage'], $data['bonus'], $data['postID']];

            $query = $this->connect->prepare($sql);
            if ($query->execute($params)) {
                return json_encode([
                    'status' => true,
                    'message' => 'Cập nhật bài đăng thành công'
                ]);
            }
            else {
                return json_encode([
                    'status' => false,
                    'message' => 'Sửa bài đăng thất bại'
                ]);
            }
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Không có quyền sửa bài đăng'
            ]);
        }
    }

    public function deletePost($postId, $userId) {
        $sql = "SELECT UserID, LocationID FROM post WHERE PostID = ?";
        $query = $this->connect->prepare($sql);
        $query->execute([$postId]);
        $post = $query->fetch();

        $location = new Location($this->connect);
        if ($post['UserID'] == $userId) {
            $this->connect->beginTransaction();
            $result = $location->deleteLocation($post['LocationID']);

            if ($result == 1) {
                $this->connect->commit();
                return json_encode([
                    'status' => true,
                    'message' => 'Xóa bài đăng thành công'
                ]);
            }
            else {
                $this->connect->rollBack();
                return json_encode([
                    'status' => false,
                    'message' => 'Xóa bài đăng thất bại'
                ]);
            }
        } else {
            return json_encode([
                'status' => false,
                'message' => 'Không có quyền xóa bài đăng'
            ]);
        }
    }

    public function newPost($post, $userId) {
        $category = new Category($this->connect);
        $categoryId = $category->getCategoryById($post['category']['categoryName']);

        $this->connect->beginTransaction();

        $newLocation = $post['location'];
        $location = new Location($this->connect);
        $locationId = $location->newLocation($newLocation);

        if ($locationId > 0 && $categoryId > 0) {
            $bonus = "";
            if ($post['bonus'] == "") {
                $bonus = null;
            } else {
                $bonus = $post['bonus'];
            }
            $sql = "INSERT INTO Post (UserID, CategoryID, LocationID, Title, Description, Price, Acreage, Area, Bonus)
                    VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?);";
            $query = $this->connect->prepare($sql);
            try {
                $result = $query->execute([$userId, $categoryId, $locationId, $post['title'], $post['description'], $post['price'], $post['acreage'], $post['area'], $bonus]);
                if ($result) {
                    $postId = $this->connect->lastInsertId();
                    $newImages = $post['images'];
                    $images = new Images($this->connect);
                    $resultImages = $images->uploadImages($newImages, $postId);

                    if ($resultImages == 1) {
                        $this->connect->commit();
                        $area = $post['area'];
                        $title = "Bài đăng mới trên Room Finder!";
                        $mess = "Có một phòng mới vừa được đăng ở khu vực $area, hãy kiểm tra ngay!";

                        sendNotification($title, $mess, $postId);
                        return json_encode([
                            'status' => true,
                            'message' => "Tạo bài đăng mới thành công"
                        ]);
                    } else {
                        $this->connect->rollBack();
                        return json_encode([
                            'status' => false,
                            'message' => 'Upload ảnh thất bại'
                        ]);
                    }

                } else {
                    $this->connect->rollBack();
                    return json_encode([
                        'status' => false,
                        'message' => 'Tạo bài đăng thất bại'
                    ]);
                }
            } catch (PDOException $e) {
                $this->connect->rollBack();
                return json_encode([
                    'status' => false,
                    'message' => 'Có lỗi SQL: ' . $e->getMessage()
                ]);
            }
        }

    }

    public function listPosts($userId, $isExpired) {
        $sql = "SELECT PostID, Title, Acreage, Price, Area, CreatedAt FROM post
                WHERE UserID = ? AND ExpireAt > NOW()
                ORDER By CreatedAt DESC";
        if ($isExpired) {
            $sql = "SELECT PostID, Title, Acreage, Price, Area, CreatedAt FROM post
                WHERE UserID = ? AND ExpireAt < NOW()
                ORDER By CreatedAt DESC";
        }
        $query = $this->connect->prepare($sql);

        try {
            $query->execute([$userId]);
            $rawData = $query->fetchAll();

            $data = $this->getImage($rawData);

            return json_encode([
                'status' => true,
                'message' => 'Lấy danh sách bài đăng của user thành công',
                'data' => $data
            ]);
        } catch (PDOException $e) {
            return json_encode([
                'status' => false,
                'message' => 'Lỗi SQL'
            ]);
        }
    }

    public function favoritePost($userId) {
        $sql = "SELECT post.PostID, Title, Acreage, Price, Area, CreatedAt FROM post
                JOIN favorite ON post.PostID = favorite.PostID
                WHERE favorite.UserID = ?
                ORDER By CreatedAt DESC";
        $query = $this->connect->prepare($sql);

        try {
            $query->execute([$userId]);
            $rawData = $query->fetchAll();

            $data = $this->getImage($rawData);

            return json_encode([
                'status' => true,
                'message' => 'Lấy danh sách bài đăng yêu thích thành công',
                'data' => $data
            ]);
        } catch (PDOException $e) {
            return json_encode([
                'status' => false,
                'message' => 'Lỗi SQL'
            ]);
        }
    }

    public function likePost($userId, $postId, $isLiked) {
        $sql = "";
        if ($isLiked == true) {
            $sql = "DELETE FROM favorite WHERE UserID = ? AND PostID = ?";
        }
        else if ($isLiked == false) {
            $sql = "INSERT INTO favorite (UserID, PostID) VALUES (?, ?)";
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Lỗi do tham số'
            ]);
        }

        $query = $this->connect->prepare($sql);
        try {
            if ($query->execute([$userId, $postId])) {
                if ($query->rowCount() > 0) {
                    return json_encode([
                        'status' => true,
                        'message' => 'Thao tác thành công'
                    ]);
                }
                else {
                    return json_encode([
                        'status' => false,
                        'message' => 'Không thay đổi'
                    ]);
                }
            }
            else {
                return json_encode([
                    'status' => false,
                    'message' => 'Lỗi do CSDL'
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'status' => false,
                'message' => 'Lỗi UserID và PostID đã tồn tại'
            ]);
        }
    }

    public function postFilter($categoryName, $area, $minPrice, $maxPrice, $minAcreage, $maxAcreage) {
        $sql = "SELECT PostID, Title, Acreage, Price, Area, CreatedAt FROM post
        JOIN category ON post.CategoryID = category.CategoryID
        WHERE ExpireAt > NOW()";

        $params = [];

        if ($categoryName != 'Tất cả') {
            $sql .= " AND category.CategoryName = ?";
            $params[] = $categoryName;
        }

        if ($area != 'Toàn quốc') {
            $sql .= " AND Area = ?";
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

        $sql .= " ORDER BY CreatedAt DESC";

        $query = $this->connect->prepare($sql);
        $query->execute($params);
        $rawData = $query->fetchAll();

        $data = $this->getImage($rawData);

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
            'listPost' => $this->getAreaHNPost()
        ];

        $data[] = [
            'titleList' => 'Khu vực Đà Nẵng',
            'listPost' => $this->getAreaDNPost()
        ];

        $data[] = [
            'titleList' => 'Khu vực TP Hồ Chí Minh',
            'listPost' => $this->getAreaHCMPost()
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

    public function listRandom() {
        $sql = "SELECT p.PostID, p.Title, p.Price, p.Acreage, p.Area, p.CreatedAt 
        FROM post p 
        JOIN (SELECT PostID FROM post ORDER BY RAND() LIMIT 10) AS random_posts 
        ON p.PostID = random_posts.PostID";

        $query = $this->connect->prepare($sql);
        $query->execute();
        $rawData = $query->fetchAll();

        $data = $this->getImage($rawData);

        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách bài đăng ngẫu nhiên thành công',
            'data' => $data
        ]);
        
    }

    public function listBonus($area) {
        $params = [];
        $sql = "SELECT PostID, Title, Price, Acreage, Area, CreatedAt
                FROM post
                WHERE Bonus IS NOT NULL";

        if ($area != 'Toàn quốc') {
            $sql .= " AND Area = ?";
            $params[] = $area;
        }
        
        $query = $this->connect->prepare($sql);
        $query->execute($params);
        $rawData = $query->fetchAll();

        $data = $this->getImage($rawData);

        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách bài đăng có sale thành công',
            'data' => $data
        ]);
    }

    public function listRoommate($area) {
        $params = [];
        $sql = "SELECT PostID, Title, Price, Acreage, Area, CreatedAt
                FROM post
                WHERE CategoryID = 5";

        if ($area != 'Toàn quốc') {
            $sql .= " AND Area = ?";
            $params[] = $area;
        }
        
        $query = $this->connect->prepare($sql);
        $query->execute($params);
        $rawData = $query->fetchAll();

        $data = $this->getImage($rawData);

        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách bài đăng tìm người ở ghép thành công',
            'data' => $data
        ]);
    }

    public function listSearch($keyword, $numeric) {
        $sql = "SELECT PostID, Title, Price, Acreage, Area, CreatedAt FROM post WHERE ExpireAt > NOW()";
        if(isset($numeric)) {
            $maxPrice = $numeric + 0.5;
            $minPrice = $numeric - 0.5;
            $maxAcreage = $numeric + 5;
            $minAcreage = $numeric - 5;

            $sql .= " AND ((Price BETWEEN $minPrice AND $maxPrice) OR (Acreage BETWEEN $minAcreage AND $maxAcreage))";
        }

        else {
            $sql .= " AND (Area LIKE '%$keyword%' OR Title LIKE '%$keyword%' OR Description LIKE '%$keyword%')";
        }

        $sql .= " ORDER By CreatedAt DESC";

        $query = $this->connect->prepare($sql);
        $query->execute();
        $rawData = $query->fetchAll();

        $data = $this->getImage($rawData);

        return json_encode([
            'status' => true,
            'message' => 'Lấy danh sách tìm kiếm thành công',
            'data' => $data
        ]);
    }

    public function postDetail($userId, $postId) {
        $sql = "SELECT post.*, CategoryName, location.Address, Longitude, Latitude
	            FROM post
                    JOIN user ON post.UserID = user.UserID
                    JOIN category ON post.CategoryID = category.CategoryID
                    JOIN location ON post.LocationID = location.LocationID
			            WHERE PostID = ?";
        $query = $this->connect->prepare($sql);
        $query->execute([$postId]);
        $rawData = $query->fetch();

        if ($rawData) {
            $queryImage = $this->connect->prepare("SELECT ImagePath AS imagePath, PublicID AS publicId FROM images WHERE PostID = ?");
            $queryImage->execute([$postId]);
            $images = $queryImage->fetchAll();

            $queryTym = $this->connect->prepare("SELECT COUNT(*) AS Tym FROM favorite WHERE PostID = ?");
            $queryTym->execute([$postId]);
            $tym = $queryTym->fetch();

            $queryLike = $this->connect->prepare("SELECT COUNT(*) AS IsLiked FROM favorite
            WHERE UserID = ? AND PostID = ?");
            $queryLike->execute([$userId, $postId]);
            $isLike = $queryLike->fetch();

            $data = [
                'postID' => $rawData['PostID'],
                'user' => [
                    'userId' => $rawData['UserID']
                ],
                'category' => [
                    'categoryName' => $rawData['CategoryName']
                ],
                'location' => [
                    'address' => $rawData['Address'],
                    'longitude' => $rawData['Longitude'],
                    'latitude' => $rawData['Latitude']
                ],
                'title' => $rawData['Title'],
                'description' => $rawData['Description'],
                'price' => $rawData['Price'],
                'acreage' => $rawData['Acreage'],
                'area' => $rawData['Area'],
                'bonus' => $rawData['Bonus'],
                'createdAt' => $rawData['CreatedAt'],
                'expireAt' => $rawData['ExpireAt'],
                'images' => $images,
                'tym' => $tym['Tym'],
                'isLiked' => ($isLike['IsLiked'] > 0) ? true : false
            ];

            return json_encode([
                'status' => true,
                'message' => 'Lấy chi tiết bài đăng thành công',
                'data' => $data
            ]);

        }
        else {
            return json_encode([
                'status' => false,
                'message' => "Không tìm thấy bài đăng"
            ]);
        }
    }

    public function userDetail($userId) {
        $sql = "SELECT Name, Avatar, PhoneNumber, Address, CreatedAt,
                    (SELECT COUNT(*) FROM post WHERE UserID = ?) AS TotalPost
                        FROM user WHERE UserID = ?";
        $query = $this->connect->prepare($sql);
        $query->execute([$userId, $userId]);
        $rawData = $query->fetch();

        if ($rawData) {
            $sqlPost = "SELECT PostID, Title, Acreage, Price, Area, CreatedAt FROM `post` WHERE UserID = ?";
            $queryList = $this->connect->prepare($sqlPost);
            $queryList->execute([$userId]);
            $rawList = $queryList->fetchAll();

            $list = $this->getImage($rawList);

            return json_encode([
                'status' => true,
                'message' => 'Lấy thông tin user thành công',
                'data' => [
                    'user' => [
                        'name' => $rawData['Name'],
                        'avatar' => $rawData['Avatar'],
                        'phoneNumber' => $rawData['PhoneNumber'],
                        'address' => $rawData['Address'],
                        'createdAt' => $rawData['CreatedAt']
                    ],
                    'totalPosts' => $rawData['TotalPost'],
                    'listPost' => $list
                ]
            ]);
        }
        else {
            return json_encode([
                'status' => false,
                'message' => 'Không có thông tin user'
            ]);
        }
    }

    private function getImage($rawData) {
        $list = [];
        foreach ($rawData as $item) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$item['PostID']]);
            $images = $query_Image->fetch();

            $data = [
                'postID' => $item['PostID'],
                'title' => $item['Title'],
                'price' => $item['Price'],
                'acreage' => $item['Acreage'],
                'area' => $item['Area'],
                'createdAt' => $item['CreatedAt'],
                'images' => $images ? [
                    ['imagePath' => $images['ImagePath']]
                ] : []
            ];
            $list[] = $data;
        }
        return $list;
    }

    private function getNewPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Area FROM post ORDER BY CreatedAt DESC LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $queryTym = $this->connect->prepare("SELECT COUNT(*) AS Tym FROM favorite WHERE PostID = ?");
            $queryTym->execute([$post['PostID']]);
            $tym = $queryTym->fetch();

            $new = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'area' => $post['Area'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : [],
                'tym' => $tym['Tym']
            ];
            $list_Have_Image[] = $new;
        }

        return $list_Have_Image;
    }

    private function getAreaHNPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Area FROM post WHERE ExpireAt > NOW() AND Area = 'Hà Nội' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $queryTym = $this->connect->prepare("SELECT COUNT(*) AS Tym FROM favorite WHERE PostID = ?");
            $queryTym->execute([$post['PostID']]);
            $tym = $queryTym->fetch();

            $address_HN = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'area' => $post['Area'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : [],
                'tym' => $tym['Tym']
            ];
            $list_Have_Image[] = $address_HN;
        }

        return $list_Have_Image;
    }

    private function getAreaDNPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Area FROM post WHERE ExpireAt > NOW() AND Area = 'Đà Nẵng' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $queryTym = $this->connect->prepare("SELECT COUNT(*) AS Tym FROM favorite WHERE PostID = ?");
            $queryTym->execute([$post['PostID']]);
            $tym = $queryTym->fetch();

            $address_DN = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'area' => $post['Area'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : [],
                'tym' => $tym['Tym']
            ];
            $list_Have_Image[] = $address_DN;
        }

        return $list_Have_Image;
    }

    private function getAreaHCMPost() {
        $query = $this->connect->prepare("SELECT PostID, Title, Price, Area FROM post WHERE ExpireAt > NOW() AND Area = 'TP. Hồ Chí Minh' LIMIT 10");
        $query->execute();
        $list = $query->fetchAll();

        $list_Have_Image = [];

        foreach ($list as $post) {
            $query_Image = $this->connect->prepare("SELECT * FROM images WHERE PostID = ?");
            $query_Image->execute([$post['PostID']]);
            $image = $query_Image->fetch();

            $queryTym = $this->connect->prepare("SELECT COUNT(*) AS Tym FROM favorite WHERE PostID = ?");
            $queryTym->execute([$post['PostID']]);
            $tym = $queryTym->fetch();

            $address_HCM = [
                'postID' => $post['PostID'],
                'title' => $post['Title'],
                'price' => $post['Price'],
                'area' => $post['Area'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : [],
                'tym' => $tym['Tym']
            ];
            $list_Have_Image[] = $address_HCM;
        }

        return $list_Have_Image;
    }

    private function getFavoritePost() {
        $query = $this->connect->prepare("SELECT post.PostID, Title, Price, Area, COUNT(favorite.PostID) AS Tym
                                            FROM post LEFT JOIN favorite ON post.PostID = favorite.PostID
                                            WHERE ExpireAt > Now()
                                            GROUP BY post.PostID
                                            ORDER BY Tym DESC LIMIT 10");
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
                'area' => $post['Area'],
                'images' => $image ? [
                    ['imagePath' => $image['ImagePath']]
                ] : [],
                'tym' => $post['Tym']
            ];
            $list_Have_Image[] = $most;
        }

        return $list_Have_Image;
    }

}

?>