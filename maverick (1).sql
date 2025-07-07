-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 11, 2025 lúc 09:29 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `maverick`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `CategoryID` int(11) NOT NULL,
  `Category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`CategoryID`, `Category`) VALUES
(1, 'Shirts'),
(2, 'Skirts'),
(3, 'Frocks'),
(4, 'P.T. T-shirts'),
(5, 'P.T. shorts'),
(6, 'P.T. track pants'),
(7, 'Belts'),
(8, 'Ties'),
(9, 'Logos'),
(10, 'Socks');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Message` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ProductID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `feedback`
--

INSERT INTO `feedback` (`FeedbackID`, `UserID`, `Message`, `Date`, `ProductID`) VALUES
(4, 1, 'This is a great product!', '2025-01-11 07:03:09', 1),
(5, 2, 'Highly recommend this to everyone!', '2025-01-11 07:03:09', 1),
(6, 1, 'Decent quality for the price.', '2025-01-11 07:03:09', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `onlineusers`
--

CREATE TABLE `onlineusers` (
  `OnlineUsersID` int(11) NOT NULL,
  `OnlineUsers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orderdetail`
--

CREATE TABLE `orderdetail` (
  `OrderDetailID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orderdetail`
--

INSERT INTO `orderdetail` (`OrderDetailID`, `ProductID`, `Quantity`) VALUES
(26, 7, 1),
(27, 1, 1),
(28, 82, 1),
(29, 87, 1),
(30, 1, 0),
(31, 1, -1),
(32, 1, 2),
(33, 80, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OrderDetailID` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `OrderDetailID`, `Status`, `Address`, `Total`) VALUES
(28, 2, 26, 'Pending', 'admin123', 22),
(29, 12, 28, 'Pending', 'Thành phố Hải Phòng, Việt Nam', 116),
(30, 2, 30, 'Pending', 'admin123', 20),
(31, 2, 31, 'Pending', 'admin123', 20),
(32, 2, 32, 'Pending', 'admin123', 56);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Stock` int(11) NOT NULL,
  `ImageURLs` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `CategoryID`, `Price`, `Stock`, `ImageURLs`, `Description`) VALUES
(1, 'Shirts', 1, 10.00, 1, 'https://contents.mediadecathlon.com/p2606947/k$1c9e0ffdefc3e67bdeabc82be7893e93/dry-men-s-running-breathable-t-shirt-red-decathlon-8771124.jpg?f=1920x0&format=auto', ''),
(7, 'Skirts', 2, 12.00, 1, 'https://images-cdn.ubuy.co.in/66005eac435539354f17cf1a-shooying-girls-women-39-s-pleated-skirt.jpg', ''),
(70, 'Frocks', 3, 10.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lvlgkqw76aohc8@resize_w450_nl.webp', ''),
(72, 'P.T. shorts', 5, 30.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-luu4k4ra4g0i26@resize_w450_nl.webp', ''),
(73, 'P.T. track pants', 6, 15.00, 1, 'https://down-vn.img.susercontent.com/file/87946b76166a6045636a35ad0f13828c@resize_w450_nl.webp', ''),
(74, 'Belts', 7, 20.00, 10, 'https://down-vn.img.susercontent.com/file/39db839db2c8db6775e8efd79a8f6bfa@resize_w450_nl.webp', ''),
(75, 'Ties', 8, 10.00, 1, 'https://down-vn.img.susercontent.com/file/a257d06a77d5f31b14ebd09e171a8548@resize_w450_nl.webp', ''),
(77, ' Socks', 10, 20.00, 1, 'https://down-vn.img.susercontent.com/file/edf17442687dc1cf29be4f38157061f5@resize_w450_nl.webp', ''),
(78, 'P.T. T-shirts', 4, 10.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lf6dywlzs5efc4@resize_w450_nl.webp', ''),
(79, 'Logos', 9, 10.00, 1, 'https://inmiligo.com/wp-content/uploads/2024/05/logo-shop-quan-ao-dep-5.jpg', ''),
(80, 'demo1', 1, 12.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lm7411fr1jvj21@resize_w450_nl.webp', ''),
(81, 'Demo2', 1, 50.00, 1, 'https://down-vn.img.susercontent.com/file/a81be37d09c3893add4286c9bdffd723@resize_w450_nl.webp', ''),
(82, 'Demo3', 1, 60.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134201-7qukw-lewf9pptw7yv3a@resize_w450_nl.webp', ''),
(83, 'Demo4', 1, 17.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-ljzhwa8wfn3ocf@resize_w450_nl.webp', ''),
(84, 'Demo4', 1, 26.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lvmmpaw6yxb129@resize_w450_nl.webp', ''),
(85, 'Demo5', 1, 36.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m3kfapneloywc8@resize_w450_nl.webp', ''),
(86, 'Demo7', 1, 46.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lf96yzqqr4ay63@resize_w450_nl.webp', ''),
(87, 'Demo8', 1, 56.00, 1, 'https://down-vn.img.susercontent.com/file/vn-11134201-7qukw-lewf9pptw7yv3a@resize_w450_nl.webp', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role`
--

CREATE TABLE `role` (
  `RoleID` int(11) NOT NULL,
  `Role` enum('admin','user','manager') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `role`
--

INSERT INTO `role` (`RoleID`, `Role`) VALUES
(1, 'admin'),
(2, 'manager'),
(3, 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `RoleID` int(11) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `Email`, `RoleID`, `Phone`, `Address`) VALUES
(1, 'a', '$2y$10$UYTYB.B.Ou0PDq4HdUA4UuYGk3wlfnWq5d11iZ.kzIq0UHiduzggi', 'd@ggg', 2, '123456', 'Thành phố Hải Phòng, Việt Nam'),
(2, 'admin', '$2y$10$jnBs6a./UWzM8mnBfO6Y7egdLI9vDBl6QalP0h3aF.V5N4J2IZqV.', 'admin@gmail.com', 1, '01245678', 'admin123'),
(12, 'demo', '$2y$10$kpE9MDWmoNl8KOee2NCuxej9CvcKLHmG5AlF87NbNJ2AW.TXWdJvK', 'demo@gmail.com', 3, '123456789', 'Thành phố Hải Phòng, Việt Nam'),
(13, 'loc', '$2y$10$vvbKcFJvRvvLghcECzdFKeSd3oiyUOBVTTctE.1dKjlE0yqbLlvZe', 'loc2809', 3, '01245678', 'Thành phố Hải Phòng, Việt Nam');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Chỉ mục cho bảng `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `UserID` (`UserID`);

--
-- Chỉ mục cho bảng `onlineusers`
--
ALTER TABLE `onlineusers`
  ADD PRIMARY KEY (`OnlineUsersID`);

--
-- Chỉ mục cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`OrderDetailID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD UNIQUE KEY `OrderDetailID` (`OrderDetailID`),
  ADD KEY `UserID` (`UserID`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`RoleID`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `RoleID` (`RoleID`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `onlineusers`
--
ALTER TABLE `onlineusers`
  MODIFY `OnlineUsersID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `OrderDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT cho bảng `role`
--
ALTER TABLE `role`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Các ràng buộc cho bảng `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `orderdetail_ibfk_1` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`OrderDetailID`) REFERENCES `orderdetail` (`OrderDetailID`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `category` (`CategoryID`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `role` (`RoleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
