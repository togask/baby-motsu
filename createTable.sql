-- コードマスタ
CREATE TABLE CODE_MASTER (
  code_id INT PRIMARY KEY AUTO_INCREMENT,
  type VARCHAR(255) NOT NULL,
  value INT NOT NULL,
  description TEXT NOT NULL
);

-- ユーザー
CREATE TABLE USER (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255) NOT NULL,
  nickname VARCHAR(100) NOT NULL,
  profile_image_path VARCHAR(500) DEFAULT 'default_profile.png',
  introduce TEXT,
  active_shipping_address_id INT FOREIGN KEY REFERENCES SHIPPING_ADDRESS(shipping_address_id),
  name VARCHAR(100),
  name_kana VARCHAR(100),
  birthday DATE,
  address TEXT,
  point INT DEFAULT 0
);

-- 配送先
CREATE TABLE SHIPPING_ADDRESS (
  shipping_address_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  sei VARCHAR(50) NOT NULL,
  mei VARCHAR(50) NOT NULL,
  sei_kana VARCHAR(50) NOT NULL,
  mei_kana VARCHAR(50) NOT NULL,
  post_code VARCHAR(7) NOT NULL,
  prefectures VARCHAR(4) NOT NULL,
  municipalities VARCHAR(100) NOT NULL,
  street VARCHAR(100) NOT NULL,
  building_name VARCHAR(150),
  tel VARCHAR(15) NOT NULL
);

-- 大カテゴリ
CREATE TABLE MAJOR_CATEGORY (
  major_category_id INT PRIMARY KEY AUTO_INCREMENT,
  major_category VARCHAR(255) NOT NULL
);

-- 小カテゴリ
CREATE TABLE MINOR_CATEGORY (
  minor_category_id INT PRIMARY KEY AUTO_INCREMENT,
  major_category_id INT NOT NULL FOREIGN KEY REFERENCES MAJOR_CATEGORY(major_category_id),
  minor_category VARCHAR(255) NOT NULL
);

-- 商品
CREATE TABLE PRODUCT (
  product_id INT PRIMARY KEY AUTO_INCREMENT,
  seller_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id_id),
  age_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  weight_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  height_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  major_category_id INT NOT NULL FOREIGN KEY REFERENCES MAJOR_CATEGORY(major_category_id),
  minor_category_id INT NOT NULL FOREIGN KEY REFERENCES MINOR_CATEGORY(minor_category_id),
  bland_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  color_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  product_condition_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  shipping_fee_responsibility_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  shipping_method_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  shipping_origin_region_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  day_to_ship_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  status_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  product_name VARCHAR(40) NOT NULL,
  product_description TEXT NOT NULL,
  price INT NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商品画像
CREATE TABLE PRODUCT_IMAGE (
  product_image_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL FOREIGN KEY REFERENCES PRODUCT(product_id),
  path VARCHAR(500) NOT NULL,
  order INT NOT NULL
);

-- お気に入り
CREATE TABLE FAVORITE (
  product_id INT NOT NULL FOREIGN KEY REFERENCES PRODUCT(product_id),
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (product_id, user_id)
);

-- 閲覧
CREATE TABLE PRODUCTVIEWS (
  product_id INT NOT NULL FOREIGN KEY REFERENCES PRODUCT(product_id),
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (product_id, user_id)
);

-- 検索履歴
CREATE TABLE SEARCH_HISTORY (
  search_history_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  search_query VARCHAR(255),
  search_params TEXT,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 取引
CREATE TABLE TRANSACTION (
  transaction_id INT PRIMARY KEY AUTO_INCREMENT,
  buyer_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  seller_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  product_id INT NOT NULL FOREIGN KEY REFERENCES PRODUCT(product_id),
  transaction_status_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  amount INT NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 評価
CREATE TABLE EVALUATION (
  evalution_id INT PRIMARY KEY AUTO_INCREMENT,
  transaction_id INT NOT NULL FOREIGN KEY REFERENCES TRANSACTION(transaction_id),
  evaluator_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  evaluatee_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  is_seller BOOLEAN NOT NULL,
  comment TEXT,
  score INT NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ポイント
CREATE TABLE POINT (
  point_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  transaction_id INT NOT NULL FOREIGN KEY REFERENCES TRANSACTION(transaction_id),
  variable_point INT NOT NULL,
  is_plus BOOLEAN NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- レビュー
CREATE TABLE REVIEW (
  review_id INT PRIMARY KEY AUTO_INCREMENT,
  product_id INT NOT NULL FOREIGN KEY REFERENCES PRODUCT(product_id),
  buyer_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  usage_duration_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  comment TEXT NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- チャージ
CREATE TABLE CHARGE (
  charge_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  point INT NOT NULL,
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- お知らせ
CREATE TABLE NOTICE (
  notice_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL FOREIGN KEY REFERENCES USER(user_id),
  transaction_id INT NOT NULL FOREIGN KEY REFERENCES TRANSACTION(transaction_id),
  type_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  notice_status_id INT NOT NULL FOREIGN KEY REFERENCES CODE_MASTER(code_id),
  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);