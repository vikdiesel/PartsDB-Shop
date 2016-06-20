--
-- Table structure for table `brands_lc`
--

CREATE TABLE `brands_lc` (
  `brand_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(3) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crosses`
--

CREATE TABLE `crosses` (
  `id` int(11) NOT NULL,
  `art_numbers` text COLLATE utf8_unicode_ci NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `import_group_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crosses_search`
--

CREATE TABLE `crosses_search` (
  `art_number_clear` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `line_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `import_group_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_methods`
--

CREATE TABLE `delivery_methods` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descr` mediumtext COLLATE utf8_unicode_ci,
  `price` int(11) NOT NULL,
  `is_available` int(1) NOT NULL DEFAULT '1',
  `order` int(4) NOT NULL DEFAULT '0',
  `is_default` int(1) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `option_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `option_value` longtext COLLATE utf8_unicode_ci NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_human_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `vericode` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `order_comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount_paid` double(12,2) NOT NULL DEFAULT '0.00',
  `date` int(11) NOT NULL,
  `order_status` int(11) DEFAULT NULL,
  `order_status_changed` int(11) DEFAULT NULL,
  `opened_by` int(11) DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `is_sync_inprogress` tinyint(1) NOT NULL DEFAULT '0',
  `is_synched` tinyint(1) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `cartid` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `line_hash` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prices_line_id` int(11) DEFAULT NULL,
  `art_number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sup_brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` double(9,2) NOT NULL DEFAULT '0.00',
  `qty` double(7,2) NOT NULL DEFAULT '0.00',
  `qty_limit` int(5) NOT NULL DEFAULT '0',
  `qty_lot_size` int(5) NOT NULL DEFAULT '1',
  `discount` int(3) NOT NULL DEFAULT '0',
  `vendor_id` int(11) DEFAULT NULL,
  `vendor_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `delivery_days` int(2) DEFAULT NULL,
  `delivery_method` int(11) DEFAULT NULL,
  `is_updated_by_user` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '1',
  `status_change_date` int(11) DEFAULT NULL,
  `type` enum('item','service') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'item'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permalink` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `text` longtext COLLATE utf8_unicode_ci,
  `meta` text COLLATE utf8_unicode_ci,
  `meta2` text COLLATE utf8_unicode_ci,
  `last_update` int(11) NOT NULL,
  `allow_delete` tinyint(1) NOT NULL DEFAULT '1',
  `is_in_menu` tinyint(1) NOT NULL DEFAULT '1',
  `menu_order` int(4) NOT NULL DEFAULT '0',
  `term_id` int(11) DEFAULT NULL,
  `post_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'page'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_terms`
--

CREATE TABLE `post_terms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permalink` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `term_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'category'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` bigint(24) NOT NULL,
  `art_number` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `art_number_clear` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sup_brand` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qty` int(5) NOT NULL,
  `price` double(12,2) NOT NULL,
  `vendor_id` int(11) NOT NULL DEFAULT '1',
  `import_group_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_unicode_ci NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `adminpass` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `adminemail` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stats_search`
--

CREATE TABLE `stats_search` (
  `id` bigint(20) NOT NULL,
  `q` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vericode` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `discount` int(3) NOT NULL DEFAULT '0',
  `userdata` text COLLATE utf8_unicode_ci NOT NULL,
  `is_sample` tinyint(1) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `vendor_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vendor_type` enum('default','crosses') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `delivery_days` int(2) NOT NULL DEFAULT '0',
  `price_correction` double(9,2) NOT NULL DEFAULT '1.00',
  `structure_id` smallint(6) NOT NULL DEFAULT '1',
  `struct_art_number` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `struct_sup_brand` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `struct_description` tinyint(3) UNSIGNED NOT NULL DEFAULT '3',
  `struct_qty` tinyint(3) UNSIGNED NOT NULL DEFAULT '4',
  `struct_price` tinyint(3) UNSIGNED NOT NULL DEFAULT '5',
  `last_update` int(11) NOT NULL DEFAULT '0',
  `rows_cache` int(11) NOT NULL DEFAULT '0',
  `qtys_cache` int(11) NOT NULL DEFAULT '0',
  `import_group_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `allow_delete` tinyint(1) NOT NULL DEFAULT '1',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `api_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_key1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_key2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordername` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orderemail` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_apipull_cache`
--

CREATE TABLE `vendor_apipull_cache` (
  `hash` varchar(32) NOT NULL,
  `data` text NOT NULL,
  `time` int(11) NOT NULL,
  `siteid` varchar(100) NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crosses`
--
ALTER TABLE `crosses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crosses_search`
--
ALTER TABLE `crosses_search`
  ADD KEY `art_number_clear` (`art_number_clear`);

--
-- Indexes for table `delivery_methods`
--
ALTER TABLE `delivery_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderid` (`orderid`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_terms`
--
ALTER TABLE `post_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `art_number_clear` (`art_number_clear`),
  ADD KEY `sup_brand` (`sup_brand`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `stats_search`
--
ALTER TABLE `stats_search`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_apipull_cache`
--
ALTER TABLE `vendor_apipull_cache`
  ADD UNIQUE KEY `hash` (`hash`);

--
-- AUTO_INCREMENT for table `crosses`
--
ALTER TABLE `crosses`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `delivery_methods`
--
ALTER TABLE `delivery_methods`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `post_terms`
--
ALTER TABLE `post_terms`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `prices`
--
ALTER TABLE `prices`
MODIFY `id` bigint(24) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `stats_search`
--
ALTER TABLE `stats_search`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;