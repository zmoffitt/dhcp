#!/usr/bin/perl

$file = "/home/httpd/html/dhcp/queue/export";
$template = "/root/scripts/dhcpd.template";

if (-e $file){
    system("lynx --source http://127.0.0.1/dhcp/dump.php?action=go 2>&1 > /dev/null");
    system("mv /home/httpd/html/dhcp/queue/tmp.txt /root/scripts/template.dhcpd");
    system("/root/scripts/format.pl");
    system("rm -f $file");
}

exit 0;
