-- =============================================================
-- ShopZone – Database Schema
-- Engine: InnoDB | Charset: utf8mb4
-- =============================================================

CREATE DATABASE IF NOT EXISTS shopzone
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE shopzone;

-- -------------------------------------------------------------
-- 1. User  (super-entity for Admin & Customer)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS User (
    User_ID      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Fname        VARCHAR(50)  NOT NULL,
    Mname        VARCHAR(50)  DEFAULT NULL,
    Lname        VARCHAR(50)  NOT NULL,
    Gender       ENUM('Male','Female','Other') NOT NULL,
    Date_of_Birth DATE         DEFAULT NULL,
    Profile_Img  VARCHAR(255) DEFAULT NULL,
    Email        VARCHAR(150) NOT NULL UNIQUE,
    Password     VARCHAR(255) NOT NULL,
    Created_At   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 2. User_Phones  (multi-valued attribute)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS User_Phones (
    User_ID  INT UNSIGNED NOT NULL,
    UPhone   VARCHAR(20)  NOT NULL,
    PRIMARY KEY (User_ID, UPhone),
    CONSTRAINT fk_uphones_user
        FOREIGN KEY (User_ID) REFERENCES User(User_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 3. Admin
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Admin (
    Admin_ID   INT UNSIGNED NOT NULL PRIMARY KEY,
    SSN        VARCHAR(20)  NOT NULL UNIQUE,
    Last_Login DATETIME     DEFAULT NULL,
    CONSTRAINT fk_admin_user
        FOREIGN KEY (Admin_ID) REFERENCES User(User_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 4. Customer
--    Ban_Until: تاريخ انتهاء الحظر (NULL = لا يوجد حظر أو حظر دائم)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Customer (
    Customer_ID    INT UNSIGNED NOT NULL PRIMARY KEY,
    Account_Status ENUM('Active','Suspended','Banned') NOT NULL DEFAULT 'Active',
    Ban_Until      DATETIME     DEFAULT NULL,
    City           VARCHAR(100) DEFAULT NULL,
    Street         VARCHAR(150) DEFAULT NULL,
    State          VARCHAR(100) DEFAULT NULL,
    Zip_Code       VARCHAR(20)  DEFAULT NULL,
    CONSTRAINT fk_customer_user
        FOREIGN KEY (Customer_ID) REFERENCES User(User_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 5. Category
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Category (
    Category_ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name        VARCHAR(100) NOT NULL UNIQUE,
    Image       VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 6. Product
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Product (
    Product_ID       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name             VARCHAR(200) NOT NULL,
    Price            DECIMAL(10,2) NOT NULL CHECK (Price >= 0),
    Rating_No        DECIMAL(3,2) DEFAULT 0.00 CHECK (Rating_No BETWEEN 0 AND 5),
    Description      TEXT         DEFAULT NULL,
    Product_Quantity INT UNSIGNED NOT NULL DEFAULT 0,
    Release_Date     DATE         DEFAULT NULL,
    Brand            VARCHAR(100) DEFAULT NULL,
    Image_URL        VARCHAR(255) DEFAULT NULL,
    Category_ID      INT UNSIGNED DEFAULT NULL,
    CONSTRAINT fk_product_category
        FOREIGN KEY (Category_ID) REFERENCES Category(Category_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 7. Specification  (Owens relationship)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Specification (
    Spec_ID    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Product_ID INT UNSIGNED NOT NULL,
    Spec_Key   VARCHAR(100) NOT NULL,
    Spec_Value VARCHAR(255) NOT NULL,
    CONSTRAINT fk_spec_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 8. Manages  (Admin manages Product)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Manages (
    Admin_ID   INT UNSIGNED NOT NULL,
    Product_ID INT UNSIGNED NOT NULL,
    PRIMARY KEY (Admin_ID, Product_ID),
    CONSTRAINT fk_manages_admin
        FOREIGN KEY (Admin_ID) REFERENCES Admin(Admin_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_manages_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 9. Coupon
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Coupon (
    Coupon_Code     VARCHAR(50)   NOT NULL PRIMARY KEY,
    Discount_Value  DECIMAL(5,2)  NOT NULL CHECK (Discount_Value > 0),
    Start_Date      DATE          NOT NULL,
    End_Date        DATE          NOT NULL,
    Status          ENUM('Active','Inactive','Expired') NOT NULL DEFAULT 'Active',
    Min_Order_Value DECIMAL(10,2) NOT NULL DEFAULT 0,
    Usage_Limit     INT UNSIGNED  NOT NULL DEFAULT 1,
    Used_Count      INT UNSIGNED  NOT NULL DEFAULT 0,
    Admin_ID        INT UNSIGNED  NOT NULL,
    CONSTRAINT fk_coupon_admin
        FOREIGN KEY (Admin_ID) REFERENCES Admin(Admin_ID)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 10. Order
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Order` (
    Order_ID         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Shipping_Fees    DECIMAL(10,2)  NOT NULL DEFAULT 0,
    Delivery_Address VARCHAR(300)   NOT NULL,
    Order_Date       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Order_Status     ENUM('Pending','Processing','Shipped','Delivered','Cancelled','Refunded')
                     NOT NULL DEFAULT 'Pending',
    Total_Price      DECIMAL(10,2)  NOT NULL CHECK (Total_Price >= 0),
    Coupon_Code      VARCHAR(50)    DEFAULT NULL,
    Customer_ID      INT UNSIGNED   NOT NULL,
    CONSTRAINT fk_order_customer
        FOREIGN KEY (Customer_ID) REFERENCES Customer(Customer_ID)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_order_coupon
        FOREIGN KEY (Coupon_Code) REFERENCES Coupon(Coupon_Code)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 11. Includes  (Order ↔ Product)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Includes (
    Order_ID         INT UNSIGNED  NOT NULL,
    Product_ID       INT UNSIGNED  NOT NULL,
    Price_at_Purchase DECIMAL(10,2) NOT NULL,
    Quantity         INT UNSIGNED  NOT NULL DEFAULT 1,
    PRIMARY KEY (Order_ID, Product_ID),
    CONSTRAINT fk_includes_order
        FOREIGN KEY (Order_ID) REFERENCES `Order`(Order_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_includes_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 12. Payment
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Payment (
    Payment_ID     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Payment_Method ENUM('CreditCard','DebitCard','PayPal','CashOnDelivery','BankTransfer')
                   NOT NULL,
    Payment_Date   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Is_Paid        TINYINT(1)  NOT NULL DEFAULT 0,
    Amount         DECIMAL(10,2) NOT NULL CHECK (Amount >= 0),
    Order_ID       INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_payment_order
        FOREIGN KEY (Order_ID) REFERENCES `Order`(Order_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 13. Shopping_Cart
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Shopping_Cart (
    Cart_ID     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Item_Count  INT UNSIGNED NOT NULL DEFAULT 0,
    Customer_ID INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_cart_customer
        FOREIGN KEY (Customer_ID) REFERENCES Customer(Customer_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 14. Contains  (Shopping_Cart ↔ Product)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Contains (
    Cart_ID    INT UNSIGNED NOT NULL,
    Product_ID INT UNSIGNED NOT NULL,
    Quantity   INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (Cart_ID, Product_ID),
    CONSTRAINT fk_contains_cart
        FOREIGN KEY (Cart_ID) REFERENCES Shopping_Cart(Cart_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_contains_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 15. Wish_List
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Wish_List (
    Wishlist_ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name        VARCHAR(100) NOT NULL DEFAULT 'My Wishlist',
    Customer_ID INT UNSIGNED NOT NULL,
    CONSTRAINT fk_wishlist_customer
        FOREIGN KEY (Customer_ID) REFERENCES Customer(Customer_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 16. Stores  (Wish_List ↔ Product)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Stores (
    Wishlist_ID INT UNSIGNED NOT NULL,
    Product_ID  INT UNSIGNED NOT NULL,
    PRIMARY KEY (Wishlist_ID, Product_ID),
    CONSTRAINT fk_stores_wishlist
        FOREIGN KEY (Wishlist_ID) REFERENCES Wish_List(Wishlist_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_stores_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 17. Review
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Review (
    Customer_ID  INT UNSIGNED NOT NULL,
    Product_ID   INT UNSIGNED NOT NULL,
    Review_No    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Comment      TEXT         DEFAULT NULL,
    Created_At   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    AI_Rating    DECIMAL(3,2) DEFAULT NULL,
    AI_Sentiment ENUM('Positive','Neutral','Negative') DEFAULT NULL,
    PRIMARY KEY (Customer_ID, Product_ID),
    UNIQUE KEY uk_review_no (Review_No),
    CONSTRAINT fk_review_customer
        FOREIGN KEY (Customer_ID) REFERENCES Customer(Customer_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_review_product
        FOREIGN KEY (Product_ID) REFERENCES Product(Product_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- 18. Redeems  (Customer redeems Coupon)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Redeems (
    Customer_ID INT UNSIGNED NOT NULL,
    Coupon_Code VARCHAR(50)  NOT NULL,
    PRIMARY KEY (Customer_ID, Coupon_Code),
    CONSTRAINT fk_redeems_customer
        FOREIGN KEY (Customer_ID) REFERENCES Customer(Customer_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_redeems_coupon
        FOREIGN KEY (Coupon_Code) REFERENCES Coupon(Coupon_Code)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
