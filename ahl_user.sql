SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE customers (
  id int(11) NOT NULL,
  customer_name varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  password varchar(255) NOT NULL,
  address varchar(255) NOT NULL,
  contact_number varchar(15) NOT NULL,
  status enum('pending','approved','declined') DEFAULT 'pending',
  images varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE orders (
  order_id int(11) NOT NULL,
  customer_email varchar(255) NOT NULL,
  order_date datetime DEFAULT current_timestamp(),
  subtotal decimal(10,2) NOT NULL,
  total decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE order_items (
  order_item_id int(11) NOT NULL,
  order_id int(11) NOT NULL,
  product_name varchar(255) NOT NULL,
  price decimal(10,2) NOT NULL,
  product_image varchar(255) NOT NULL,
  original_price decimal(10,2) NOT NULL,
  quantity int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE products (
  product_id int(11) NOT NULL,
  product_name varchar(100) DEFAULT NULL,
  description text DEFAULT NULL,
  price decimal(10,2) DEFAULT NULL,
  quantity int(11) NOT NULL DEFAULT 0,
  image varchar(100) DEFAULT NULL,
  category varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



ALTER TABLE customers
  ADD PRIMARY KEY (id);

ALTER TABLE orders
  ADD PRIMARY KEY (order_id);

ALTER TABLE order_items
  ADD PRIMARY KEY (order_item_id),
  ADD KEY order_id (order_id);

ALTER TABLE products
  ADD PRIMARY KEY (product_id);


ALTER TABLE customers
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE orders
  MODIFY order_id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE order_items
  MODIFY order_item_id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE products
  MODIFY product_id int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE order_items
  ADD CONSTRAINT order_items_ibfk_1 FOREIGN KEY (order_id) REFERENCES `orders` (order_id) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
