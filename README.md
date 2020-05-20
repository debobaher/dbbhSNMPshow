# dbbhSNMPshow
This script allow you to control SNMP parameters of your devices and fetch them time to time with some statistics.

Please use [snmp.php](snmp.php) script at the host that will fetch the SNMP parameters from the devices (SNMP v2 is used here). This script will connect to the database and read devices addresses, SNMP communities and SNMP parameters to read.

Once it is run, it fetch all it can from SNMP devices and store fetched data to the database. Then it exit. **You need to run it with schedular to get proper data**. I use cron for this. It run the script every minute and I got new data every minute.

The [snmp_show.php](snmp_show.php) file will show you a table with collected information. You will see minimal value, maximal value and average value of the parameters and its last value.

You can use different host for these two scripts. Both of them need `config.php` file to connect the database. The [snmp_show.php](snmp_show.php) also use [styles.css](styles.css) file for CSS markup.

To start using the script simply run [install.php](install.php) and complete the database parameters. After clicking the "install" button the script will write new `config.php` file with your database parameters and initialise the content of the database with tables structure and parameters to monitor some Huawei devices.

After completing the installation process please delete the `install.php`, `install.css`, `mb_str_split.php`, `config_install.php` and `db.sql` files. If you use two host for operation then copy [snmp.php](snmp.php) and `config.php` files to it. You will need to run [snmp.php](snmp.php). To the second host copy `config.php`, [snmp_show.php](snmp_show.php) and [styles.css](styles.css) files. Open [snmp_show.php](snmp_show.php) with browser to watch the result.

To add monitoring device please use `device` table.  `id_device` - autoincrement id you can skip it,  `name` - device name (you will see it in the result table),  `address` - network address of the device (IP),  `community` - SNMP community to read the data,`id_dev_type` - device type identifier: 1 - UPS 2000G SNMP card (new version), 4 - old version, 2 - iBMC (iMana) of the Huawei server (V2, V3 and V5)).

I use [dbbhInstaller](https://github.com/debobaher/dbbhInstaller) in this scripts.
