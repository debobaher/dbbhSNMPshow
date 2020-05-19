<?php
/*
 * dbbhSNMPshow script by Migachev Vladimir debobaher@gmail.com
 * This script connect to the database, get SNMP parameters of the devices to get
 * parameters and store them to the database.
 * Please use schedular application to run it time to time. Each run will collect one portion of the data.
 * More runs - more data. You can use cron for example.
*/
require 'config.php';

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_database);

if ($mysqli->connect_error)
  die('Can\'t connect to the database server. Here is the error: ' . $mysqli->connect_error . '(' . $mysqli->connect_errno . ')');

$mysqli->set_charset("utf8");

// Selecting the monitoring parameters from the database
// The parameters are selecting with such big list to get possibility to analize them (not just log)
// If you delete some of them make sure that you correct the row[] variables in the fetch area
if ($result = $mysqli->query('SELECT
	mon_param_type.snmp_read,
	mon_param_type.start_pos,
	device.address,
	device.community,
	mon_param_type.alert_min,
	mon_param_type.alert_max,
	mon_param_type.critical_min,
	mon_param_type.critical_max,
  mon_param.id_mon_param,
	mon_param_type.name,
	device.name,
	device.id_device
from
	' . $db_prefix . 'mon_param_type as mon_param_type, ' . $db_prefix . 'device as device,
  ' . $db_prefix . 'mon_param as mon_param, ' . $db_prefix . 'dev_type as dev_type
where
	mon_param.id_dev_type=dev_type.id_dev_type
	and mon_param.id_mon_param_type=mon_param_type.id_mon_param_type
	and device.id_dev_type=dev_type.id_dev_type')) {
    while ($row = $result->fetch_row()) { // Fetching the result row by row
			$tmp = (rtrim(substr(snmp2_get($row[2], $row[3], $row[0], 2000000, 1), $row[1]), ' "')); // Get the SNMP paameter as described in the fetched row
      // Storing the result to the database
      $mysqli->query('insert into ' . $db_prefix . 'mon_param_value (id_mon_param, value, id_device) values (' . $row[8] . ', "' . $tmp . '", ' . $row[11] . ')');
		}
    // Cleaning the memory
    $result->close();
    // Clear old data
    $mysqli->query('delete from ' . $db_prefix . 'mon_param_value where param_timestamp < (NOW() - INTERVAL 30 DAY)');
}

?>
