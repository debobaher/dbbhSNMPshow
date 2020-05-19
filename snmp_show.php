<?php
/*
 * dbbhSNMPshow script by Migachev Vladimir debobaher@gmail.com
 * This script connect to the database and show the statistic data
*/
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>
<html>

<head>
	<link href="styles.css" rel="stylesheet"/>
	<meta http-equiv="Refresh" content="30" />
</head>

<body>
<table class="showTable">
	<tr class="header">
		<td colspan=2><pre><?php printf(date('G:i:s   d.m.y')); ?></pre></td>
		<td colspan=3 >Last 30 minutes</td>
	<tr class="header">
		<td>Параметр</td><td>Actual value</td>
		<td width=15%>Avg</td><td width=15%>Min</td><td width=15%>Max</td>
<?php
// Connecting to the database
require 'config.php';
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_database);

if ($mysqli->connect_error)
  die('Can\'t connect to the database server. Here is the error: ' . $mysqli->connect_error . '(' . $mysqli->connect_errno . ')');

$mysqli->set_charset("utf8");

// Selecting the device list
if ($result = $mysqli->query('SELECT
		device.name,
		id_device
	from
		' . $db_prefix . 'device as device
	order by
		device.name')) {
    while ($row = $result->fetch_row()) { // We read one device name and ID. Now we will compose is table row
			printf('<tr><td colspan=5 class="deviceHeader">' . $row[0] . '</td>');

			if ($result2 = $mysqli->query('SELECT distinct
					mon_param_value.id_mon_param,
					mon_param_type.name,
					mon_param_type.id_mon_param_type
				from
					' . $db_prefix . 'mon_param_type as mon_param_type, ' . $db_prefix . 'mon_param as mon_param, ' . $db_prefix . 'dev_type as dev_type, ' . $db_prefix . 'mon_param_value as mon_param_value, ' . $db_prefix . 'device as device
				where
					mon_param_value.id_device=' . $row[1] . '
					and mon_param.id_mon_param_type=mon_param_type.id_mon_param_type
					and mon_param_value.id_mon_param=mon_param.id_mon_param
					and mon_param.id_dev_type=device.id_dev_type
					and device.id_device=mon_param_value.id_device
					and mon_param_value.param_timestamp > (NOW() - INTERVAL "30:0" MINUTE_SECOND)
							order by
									mon_param_type.name')) {
				while ($row2 = $result2->fetch_row()) { // And now we will calculate the values to display for the last 30 minutes
					if ($result3 = $mysqli->query('SELECT
							max(mon_param_value.param_timestamp),
							ROUND(AVG(mon_param_value.value) * mon_param_type.multiplier, 1),
							ROUND(MIN(mon_param_value.value * mon_param_type.multiplier)),
							ROUND(MAX(mon_param_value.value * mon_param_type.multiplier))
						from
							' . $db_prefix . 'mon_param_type as mon_param_type, ' . $db_prefix . 'mon_param as mon_param, ' . $db_prefix . 'mon_param_value as mon_param_value, ' . $db_prefix . 'device as device, ' . $db_prefix . 'dev_type as dev_type
						where
							mon_param_value.id_device=' . $row[1] . '
							and mon_param_type.id_mon_param_type=' . $row2[2] . '
							and mon_param.id_mon_param_type=mon_param_type.id_mon_param_type
							and mon_param_value.id_mon_param=' . $row2[0] . '
							and mon_param.id_dev_type=device.id_dev_type
							and device.id_device=mon_param_value.id_device
							and mon_param_value.param_timestamp > (NOW() - INTERVAL "30:0" MINUTE_SECOND)')) {
						$row3 = $result3->fetch_row();
						// Now we will collect last data and alerts
						$result4 = $mysqli->query('SELECT
							ROUND(mon_param_value.value * mon_param_type.multiplier, 1),
							(mon_param_value.value < mon_param_type.alert_min), mon_param_type.alert_min,
							(mon_param_value.value > mon_param_type.alert_max), mon_param_type.alert_max,
							(mon_param_value.value < mon_param_type.critical_min), mon_param_type.critical_min,
							(mon_param_value.value > mon_param_type.critical_max), mon_param_type.critical_max
						from
							' . $db_prefix . 'mon_param_type as mon_param_type, ' . $db_prefix . 'mon_param as mon_param, ' . $db_prefix . 'mon_param_value as mon_param_value, ' . $db_prefix . 'device as device, ' . $db_prefix . 'dev_type as dev_type
						where
							mon_param_value.id_device=' . $row[1] . '
							and mon_param_type.id_mon_param_type=' . $row2[2] . '
							and mon_param.id_mon_param_type=mon_param_type.id_mon_param_type
							and mon_param_value.id_mon_param=' . $row2[0] . '
							and mon_param.id_dev_type=device.id_dev_type
							and device.id_device=mon_param_value.id_device
							and mon_param_value.id_mon_param_value = (SELECT MAX(id_mon_param_value) from mon_param_value WHERE
								mon_param_value.id_device=' . $row[1] . '
								and mon_param_value.id_mon_param=' . $row2[0] . ')
						limit 1');
						$row4 = $result4->fetch_row();
						printf('<tr class="row"><td');
						if( (($row4[6] != NULL)&&($row4[5] > 0)) ||
							(($row4[8] != NULL)&&($row4[7] > 0)) ) {
							printf(' CLASS="critical"');
							}
						elseif( (($row4[2] != NULL)&&($row4[1] > 0)) ||
							(($row4[4] != NULL)&&($row4[3] > 0)) ) {
							printf(' CLASS="alert"');
							}
						printf('>' . $row2[1] . '</td><td title="' . $row3[0] . '" class="data">' . $row4[0] . '</td><td title="Avg" class="data">' . $row3[1] . '</td><td title="Min" class="data">' . $row3[2] . '</td><td title="Max" class="data">' . $row3[3] . '</td>');
						$result4->close();
						$result3->close();
					}
				}

			$result2->close();
			}
		}
    $result->close();
		// Clear the old data
    $mysqli->query('delete from mon_param_value where param_timestamp < (NOW() - INTERVAL 30 DAY)');
}
?>
</table>
</body>
</html>
