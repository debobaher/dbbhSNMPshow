CREATE TABLE `$PREFIX$device` (  `id_device` int(11) NOT NULL,  `name` varchar(255) COLLATE utf8_bin NOT NULL,  `address` varchar(128) COLLATE utf8_bin NOT NULL,  `community` varchar(255) COLLATE utf8_bin DEFAULT NULL,`id_dev_type` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `$PREFIX$dev_type` (  `id_dev_type` int(11) NOT NULL,  `name` varchar(255) COLLATE utf8_bin NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `$PREFIX$dev_type` (`id_dev_type`, `name`) VALUES(1, 'UPS Huawei 2000G'),(4, 'UPS Huawei 2000G (Old)'),(2, 'iMana');

CREATE TABLE `$PREFIX$mon_param` (  `id_mon_param` int(11) NOT NULL,  `id_dev_type` int(11) NOT NULL,  `id_mon_param_type` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `$PREFIX$mon_param` (`id_mon_param`, `id_dev_type`, `id_mon_param_type`) VALUES (15, 1, 1),(16, 1, 2),(20, 1, 4),(19, 2, 3),(21, 4, 1),(22, 4, 2);

CREATE TABLE `$PREFIX$mon_param_type` (  `id_mon_param_type` int(11) NOT NULL,  `name` varchar(255) COLLATE utf8_bin NOT NULL,  `snmp_read` varchar(512) COLLATE utf8_bin DEFAULT NULL,  `start_pos` int(11) NOT NULL DEFAULT '9',  `alert_min` int(11) DEFAULT NULL,  `alert_max` int(11) DEFAULT NULL,  `critical_min` int(11) DEFAULT NULL,  `critical_max` int(11) DEFAULT NULL,  `multiplier` float NOT NULL DEFAULT '1' COMMENT 'Multiplier for correct result appear') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `$PREFIX$mon_param_type` (`id_mon_param_type`, `name`, `snmp_read`, `start_pos`, `alert_min`, `alert_max`, `critical_min`, `critical_max`, `multiplier`) VALUES (1, 'Battery backup time (minutes)', 'iso.3.6.1.4.1.2011.6.174.1.6.100.1.4.1', 9, 900, NULL, 300, NULL, 0.0166667),(2, 'Inout voltage', 'iso.3.6.1.4.1.2011.6.174.1.5.100.1.1.1', 9, 2000, 2500, NULL, NULL, 0.1),(3, 'Outdoor Huawei server temperature', '1.3.6.1.4.1.2011.2.235.1.1.13.50.1.2.17.73.110.108.101.116.32.84.101.109.112.0.0.0.0.0.0.0', 9, NULL, 25, NULL, 31, 1),(4, 'Battery capacity (procent)', 'iso.3.6.1.2.1.33.1.2.4.0', 9, 30, NULL, 10, NULL, 1);

CREATE TABLE `$PREFIX$mon_param_value` ( `id_mon_param_value` int(11) NOT NULL,  `id_device` int(11) NOT NULL,  `param_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `id_mon_param` int(11) NOT NULL,  `value` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `$PREFIX$device`  ADD PRIMARY KEY (`id_device`),  ADD KEY `id_dev_type` (`id_dev_type`),  ADD KEY `device_name_index` (`name`);

ALTER TABLE `$PREFIX$dev_type`  ADD PRIMARY KEY (`id_dev_type`),  ADD KEY `device_type_name_index` (`name`);

ALTER TABLE `$PREFIX$mon_param`  ADD PRIMARY KEY (`id_mon_param`),  ADD KEY `id_device` (`id_dev_type`),  ADD KEY `id_mon_param_type` (`id_mon_param_type`),  ADD KEY `mon_param_id_dev_type_id_mon_param_type_index` (`id_dev_type`,`id_mon_param_type`) USING BTREE;

ALTER TABLE `$PREFIX$mon_param_type`  ADD PRIMARY KEY (`id_mon_param_type`),  ADD KEY `mon_param_type_name_index` (`name`);

ALTER TABLE `$PREFIX$mon_param_value`  ADD PRIMARY KEY (`id_mon_param_value`),  ADD KEY `id_device` (`id_device`),  ADD KEY `mon_param_value_param_timestamp_index` (`param_timestamp`),  ADD KEY `mon_param_value_complex_index` (`id_device`,`id_mon_param`,`param_timestamp`),  ADD KEY `id_mon_param_index` (`id_mon_param`) USING BTREE,  ADD KEY `mon_param_value_value_index` (`value`),  ADD KEY `mon_param_and_time_index` (`id_mon_param`,`param_timestamp`);

ALTER TABLE `$PREFIX$device`  MODIFY `id_device` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `$PREFIX$dev_type`  MODIFY `id_dev_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `$PREFIX$mon_param`  MODIFY `id_mon_param` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `$PREFIX$mon_param_type`  MODIFY `id_mon_param_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `$PREFIX$mon_param_value`  MODIFY `id_mon_param_value` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13202723;

ALTER TABLE `$PREFIX$device`  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`id_dev_type`) REFERENCES `$PREFIX$dev_type` (`id_dev_type`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `$PREFIX$mon_param`  ADD CONSTRAINT `mon_param_ibfk_1` FOREIGN KEY (`id_dev_type`) REFERENCES `$PREFIX$dev_type` (`id_dev_type`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `mon_param_ibfk_2` FOREIGN KEY (`id_mon_param_type`) REFERENCES `$PREFIX$mon_param_type` (`id_mon_param_type`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `$PREFIX$mon_param_value`  ADD CONSTRAINT `mon_param_value_ibfk_1` FOREIGN KEY (`id_mon_param`) REFERENCES `$PREFIX$mon_param` (`id_mon_param`) ON DELETE CASCADE ON UPDATE CASCADE,  ADD CONSTRAINT `mon_param_value_id_device_foreign_key` FOREIGN KEY (`id_device`) REFERENCES `$PREFIX$$PREFIX$device` (`id_device`) ON DELETE CASCADE ON UPDATE CASCADE;
