 -- Tạo bảng Role
CREATE TABLE Role(
    RoleID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    RoleName VARCHAR(20) NOT NULL
);

-- Tạo bảng User
CREATE TABLE User(
    UserID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    RoleID INT NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Name VARCHAR(40),
    PhoneNumber CHAR(10),
    CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    OTP CHAR(6),

    CONSTRAINT U_fk_R FOREIGN KEY (RoleID)
        REFERENCES Role(RoleID)
);

-- Tạo bảng Category
CREATE TABLE Category(
    CategoryID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    CategoryName VARCHAR(100) NOT NULL
);

-- Tạo bảng Location
CREATE TABLE Location(
    LocationID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    Description VARCHAR(255) NOT NULL,
    Longitude FLOAT NOT NULL,
    Latitude FLOAT NOT NULL
);

-- Tạo bảng Post
CREATE TABLE Post(
    PostID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    UserID INT NOT NULL,
    CategoryID INT NOT NULL,
    LocationID INT NOT NULL,
    Title VARCHAR(255) NOT NULL,
    Description TEXT,
    Price FLOAT NOT NULL,
    Acreage FLOAT NOT NULL,
    Address VARCHAR(100) NOT NULL,
    Tym INT NOT NULL  DEFAULT 0
    Bonus VARCHAR(255),
    CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ExpireAt TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 30 DAY),

    CONSTRAINT P_fk_U FOREIGN KEY (UserID)
        REFERENCES User(UserID),
    CONSTRAINT C_fk_U FOREIGN KEY (CategoryID)
        REFERENCES Category(CategoryID),
    CONSTRAINT L_fk_P FOREIGN KEY (LocationID)
        REFERENCES Location(LocationID)
);

-- Tạo bảng Favorite
CREATE TABLE Favorite(
    UserID INT NOT NULL,
    PostID INT NOT NULL,

    CONSTRAINT U_pk_P PRIMARY KEY (UserID, PostID),
    CONSTRAINT F_fk_U FOREIGN KEY (UserID)
        REFERENCES User(UserID) ON DELETE CASCADE,
    CONSTRAINT F_fk_P FOREIGN KEY (PostID)
        REFERENCES Post(PostID) ON DELETE CASCADE
);

-- Tạo bảng Images (Lưu trữ ảnh phòng trọ)
CREATE TABLE Images(
    ImageID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    PostID INT NOT NULL,
    ImagePath VARCHAR(255) NOT NULL,
    PublicID VARCHAR(100) NOT NULL,

    CONSTRAINT I_fk_P FOREIGN KEY (PostID)
        REFERENCES Post(PostID)
);

-- Câu lệnh update ExpireAt của table Post
UPDATE Post
SET ExpireAt = CURRENT_TIMESTAMP + INTERVAL 1 MONTH;
