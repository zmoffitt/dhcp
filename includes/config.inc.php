<?

	// database info
        $db_hostname = 'localhost';

        // all dhcp partners should use the same auth host for single sign-on.
	$db_hostname_auth = 'localhost';
	$db_hostname_cbs = 'claven.gsb.columbia.edu';

        $db_username = 'dhcp';
        $db_password = 'dhcp2cool';
	$db_login_username = 'login';
	$db_login_password = 'login2cool';
	$db_cbs_username = 'cbs';
	$db_cbs_password = 'cbs2cool';

        $db_name = 'dhcp';
	$db_login_name = 'login';

        $db_tablename_global = 'global';
        $db_tablename_declaration = 'declaration';
        $db_tablename_ip = 'ip';
        $db_tablename_staff = 'staff';
        $db_tablename_logs = 'logs';
        $db_tablename_dynamic = 'dynamic';
        $db_tablename_login = 'login';
        $db_tablename_state = 'state';
	$db_table_groups = 'groups';
	$db_table_sessions = 'sessions';

        $auth_host = '128.59.172.3'; // authentication host
	$prefix = "128.59";
	$identifier = "Uris";
        $default_subnet = "172"; // default subnet to display
	$default_ip_type = "dynamic"; // default ip type

	// for compatibility of newer versions of DHCP server
	// for versions before 3.0b2pl11, use the value ""
	// for versions after 3.0b2pl11, use the value "ad-hoc"
	$ddns_update_style = "ad-hoc";

	// default refresh rate in sec for main page (main.php)
	$default_refresh_rate = 300; 

	// whether to do name lookup in the active lease page
	$name_lookup = 1;
	$name_lookup_url = "http://www.columbia.edu/cgi-bin/lookup.pl";

	$footer = "includes/bottom.inc.php";
	$dhcpd_conf_file = "/etc/dhcpd.conf";
	$dhcpd_log = "/var/log/messages";

	// maximum number of rows that can be returned by queries
	$max_result = 1000;

	// whether there are DHCP "partners", which share data such as
	// MAC registrations and blacklists
	$dhcp_replicate = 1;

	// Usage: $dhcp_partners["<DHCP Server Identifier>"] = "<IP address>";
	// Should list all the OTHER DHCP server(s) that need to share data,
	// INCLUDING the server itself.
	$dhcp_partners["Culbs"] = "128.59.219.2";
	$dhcp_partners["Uris"] = "localhost";

	// colors for different types of IPs
        $color_dynamic = "ffcc99";
        $color_dynamic_free = "99ffcc";
        $color_free = "00cc99";
        $color_reserved = "cccccc";
        $color_static = "66ccff";
        $color_unknown = "ff0000";
        $color_dynamic_active = "ffff33";
        $color_non_registered = "ff3333";
        $color_registered = "66ccff";
        $color_throttled = "cc99ff";
        $color_excluded = "99cccc";

	$color_dhcpd_logs_1 = "dddddd";
	$color_dhcpd_logs_2 = "33ccff";

	// pop-up window size
	$popup_width = 600;
	$popup_height = 655;

	// used to determine access level
	$ADMIN = 0;
	$READ = 1;

?>
