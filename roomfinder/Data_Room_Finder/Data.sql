-- Chèn dữ liệu vào bảng Role
INSERT INTO Role (RoleName)
VALUES  ('User'),
        ('Admin');

-- Chèn dữ liệu vào bảng User
INSERT INTO User (RoleID, Email, Password, Name, PhoneNumber)
VALUES  (1, 'user1@example.com', 'password1', 'User One', '0973058625'),
        (1, 'user2@example.com', 'password2', 'User Two', '0901234562'),
        (1, 'user3@example.com', 'password3', 'User Three', '0901234563'),
        (1, 'user4@example.com', 'password4', 'User Four', '0901234564'),
        (1, 'user5@example.com', 'password5', 'User Five', '0901234565'),
        (1, 'user6@example.com', 'password6', 'User Six', '0901234566'),
        (1, 'user7@example.com', 'password7', 'User Seven', '0901234567'),
        (1, 'user8@example.com', 'password8', 'User Eight', '0901234568'),
        (1, 'user9@example.com', 'password9', 'User Nine', '0901234569'),
        (1, 'user10@example.com', 'password10', 'User Ten', '0901234570'),
        (1, 'landlord1@example.com', 'password1', 'Landlord One', '0911234561'),
        (1, 'landlord2@example.com', 'password2', 'Landlord Two', '0911234562'),
        (1, 'landlord3@example.com', 'password3', 'Landlord Three', '0911234563'),
        (1, 'landlord4@example.com', 'password4', 'Landlord Four', '0911234564'),
        (1, 'landlord5@example.com', 'password5', 'Landlord Five', '0911234565'),
        (2, 'admin1@example.com', 'password1', 'Admin One', '0921234561'),
        (2, 'admin2@example.com', 'password2', 'Admin Two', '0921234562');

-- Chèn dữ liệu cho bảng Category
INSERT INTO Category (CategoryName)
VALUES  ('Cho thuê phòng trọ'),
        ('Cho thuê căn hộ'),
        ('Cho thuê mặt bằng'),
        ('Cho thuê nhà nguyên căn'),
        ('Tìm người ở ghép');

-- Chèn dữ liệu vào bảng Location
INSERT INTO Location (Address, Longitude, Latitude)
VALUES  ('Gần Vincom Center, Quận Hai Bà Trưng, Hà Nội', 105.852, 21.015),
        ('Gần quán cà phê Highlands, Quận 1, TP.HCM', 106.702, 10.776),
        ('Gần khu chợ Bến Thành, Quận 1, TP.HCM', 106.698, 10.772),
        ('Gần quán cà phê The Coffee House, Quận 7, TP.HCM', 106.719, 10.729),
        ('Gần biển Mỹ Khê, Quận Ngũ Hành Sơn, Đà Nẵng', 108.249, 16.059),
        ('Gần trường Đại học Văn Hiến, Quận Tân Phú, TP.HCM', 106.631, 10.788),
        ('Gần Vincom Plaza, Quận Sơn Trà, Đà Nẵng', 108.239, 16.078),
        ('Gần sân bay Quốc tế Đà Nẵng, Quận Hải Châu, Đà Nẵng', 108.198, 16.043),
        ('Gần sân vận động Mỹ Đình, Quận Nam Từ Liêm, Hà Nội', 105.749, 21.028),
        ('Gần Vincom Mega Mall, Quận Long Biên, Hà Nội', 105.902, 21.033),
        ('Gần quán cà phê Highlands, Quận Thanh Khê, Đà Nẵng', 108.189, 16.069),
        ('Gần công viên Châu Á, Quận Hải Châu, Đà Nẵng', 108.216, 16.048),
        ('Gần hồ Hoàn Kiếm, Quận Hoàn Kiếm, Hà Nội', 105.852, 21.028),
        ('Gần quán cà phê AHA, Quận Ba Đình, Hà Nội', 105.820, 21.032),
        ('Gần công viên Thống Nhất, Quận Hai Bà Trưng, Hà Nội', 105.846, 21.017),
        ('Gần chợ Đồng Xuân, Quận Hoàn Kiếm, Hà Nội', 105.851, 21.036),
        ('Gần cầu Rồng, Quận Hải Châu, Đà Nẵng', 108.223, 16.067),
        ('Gần quán cà phê Phúc Long, Quận 3, TP.HCM', 106.687, 10.775),
        ('Gần công viên Hoàng Văn Thụ, Quận Tân Bình, TP.HCM', 106.661, 10.800),
        ('Gần cầu Sài Gòn, Quận Bình Thạnh, TP.HCM', 106.712, 10.806);


-- Chèn dữ liệu cho bảng Post
INSERT INTO Post (UserID, CategoryID, LocationID, Title, Description, Price, Acreage, Area, Bonus)
VALUES  (11, 1, 1, 'Căn hộ tiện nghi tại Quận Hai Bà Trưng', 'Căn hộ gần Vincom Center, Quận Hai Bà Trưng, Hà Nội', 4.3, 80, 'Hà Nội', 'Giảm giá tháng đầu'),
        (12, 1, 2, 'Căn hộ cao cấp Quận 1', 'Căn hộ cao cấp gần quán cà phê Highlands, Quận 1, TP.HCM', 3, 85, 'TP. Hồ Chí Minh', 'Miễn phí dịch vụ 3 tháng'),
        (11, 1, 3, 'Nhà gần chợ Bến Thành', 'Nhà phố gần khu chợ Bến Thành, Quận 1, TP.HCM', 2.7, 95, 'TP. Hồ Chí Minh', 'Giảm giá 10% cho hợp đồng 1 năm'),
        (13, 2, 4, 'Nhà riêng Quận 7', 'Nhà riêng gần quán cà phê The Coffee House, Quận 7, TP.HCM', 12, 90, 'TP. Hồ Chí Minh', NULL),
        (13, 1, 5, 'Căn hộ ven biển Mỹ Khê', 'Căn hộ gần biển Mỹ Khê, Quận Ngũ Hành Sơn, Đà Nẵng', 6, 120, 'Đà Nẵng', 'Miễn phí dịch vụ'),
        (12, 2, 6, 'Nhà phố Tân Phú', 'Nhà phố gần trường Đại học Văn Hiến, Quận Tân Phú, TP.HCM', 5.2, 70, 'TP. Hồ Chí Minh', 'Tặng 2 tháng tiền nhà'),
        (11, 1, 7, 'Căn hộ Quận Sơn Trà', 'Căn hộ gần Vincom Plaza, Quận Sơn Trà, Đà Nẵng', 2.8, 75, 'Đà Nẵng', NULL),
        (14, 2, 8, 'Nhà riêng gần sân bay Đà Nẵng', 'Nhà riêng gần sân bay Quốc tế Đà Nẵng, Quận Hải Châu, Đà Nẵng', 3, 105, 'Đà Nẵng', 'Miễn phí 1 tháng thuê'),
        (14, 1, 9, 'Nhà gần sân vận động Mỹ Đình', 'Nhà gần sân vận động Mỹ Đình, Quận Nam Từ Liêm, Hà Nội', 3.3, 85, 'Hà Nội', 'Giảm giá cho hợp đồng dài hạn'),
        (15, 2, 10, 'Nhà phố Long Biên', 'Nhà phố gần Vincom Mega Mall, Quận Long Biên, Hà Nội', 11, 100, 'Hà Nội', 'Miễn phí 2 tháng đầu'),
        (15, 1, 11, 'Nhà Quận Thanh Khê', 'Nhà gần quán cà phê Highlands, Quận Thanh Khê, Đà Nẵng', 6.2, 95, 'Đà Nẵng', NULL),
        (15, 2, 12, 'Nhà phố Hải Châu', 'Nhà phố gần công viên Châu Á, Quận Hải Châu, Đà Nẵng', 2, 85, 'Đà Nẵng', 'Tặng 1 tháng tiền thuê'),
        (12, 1, 13, 'Căn hộ cao cấp Hoàn Kiếm', 'Căn hộ cao cấp gần hồ Hoàn Kiếm, Quận Hoàn Kiếm, Hà Nội', 3.3, 90, 'Hà Nội', 'Miễn phí dịch vụ 3 tháng'),
        (13, 1, 14, 'Nhà phố Ba Đình', 'Nhà phố gần quán cà phê AHA, Quận Ba Đình, Hà Nội', 4, 80, 'Hà Nội', 'Giảm giá 5% cho hợp đồng 2 năm'),
        (14, 2, 15, 'Nhà gần công viên Thống Nhất', 'Nhà riêng gần công viên Thống Nhất, Quận Hai Bà Trưng, Hà Nội', 8, 85, 'Hà Nội', NULL),
        (14, 1, 16, 'Nhà phố Đồng Xuân', 'Nhà phố gần chợ Đồng Xuân, Quận Hoàn Kiếm, Hà Nội', 3.7, 100, 'Hà Nội', 'Tặng 2 tháng tiền nhà'),
        (15, 2, 17, 'Nhà ven cầu Rồng', 'Nhà gần cầu Rồng, Quận Hải Châu, Đà Nẵng', 13, 110, 'Đà Nẵng', 'Miễn phí dịch vụ 6 tháng'),
        (11, 1, 18, 'Nhà phố Quận 3', 'Nhà phố gần quán cà phê Phúc Long, Quận 3, TP.HCM', 6.3, 70, 'TP. Hồ Chí Minh', 'Giảm 10% giá thuê'),
        (13, 2, 19, 'Nhà Tân Bình', 'Nhà gần công viên Hoàng Văn Thụ, Quận Tân Bình, TP.HCM', 3, 80, 'TP. Hồ Chí Minh', NULL),
        (12, 1, 20, 'Căn hộ Bình Thạnh', 'Căn hộ gần cầu Sài Gòn, Quận Bình Thạnh, TP.HCM', 3.5, 90, 'TP. Hồ Chí Minh', 'Tặng 1 năm phí quản lý');


-- Dữ liệu test
{
    "category": {
        "categoryName": "Tìm người ở ghép"
    },
    "location": {
        "address": "Nguyên Xá - Minh Khai - Hà Nội",
        "longitude": "105.749",
        "latitude": "21.028"
    },
    "title": "Cho thuê phòng trọ khép kín giá rẻ ở Hà Nội",
    "description": "Nhà gần sân vận động Mỹ Đình', 'Nhà gần sân vận động Mỹ Đình, Quận Nam Từ Liêm, Hà Nội",
    "price": "3.3",
    "acreage": "40",
    "area": "Hà Nội",
    "bonus": "Không có bonus",
    "images": [
        {
            "imagePath": "sdhshjd/wjedd.com",
            "publicId": "1234"
        },
        {
            "imagePath": "images_5/wjedd.com",
            "publicId": "5678"
        }
    ]
}
-- Dữ liệu test
{
    "postID": 36,
    "user": {
      "userId": 1
    },
    "category": {
      "categoryName": "Tìm người ở ghép"
    },
    "title": "Tôi cũng chúc bạn và gia đình sang năm mới may mắn, mạnh khoẻ, vạn sự như ý, công việc hanh",
    "description": "Nhà gần sân vận động Mỹ Đình', 'Nhà gần sân vận động Mỹ Đình, Quận Nam Từ Liêm, Hà Nội",
    "price": 12,
    "acreage": 850,
    "bonus": "Giảm 1 nửa tiền thuê tháng đầu",
    "expireAt": "7" -- 0, 3, 5, 7
}
