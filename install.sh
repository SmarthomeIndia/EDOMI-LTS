#!/bin/sh

# -----------------------------------------------------------------------------------------------------------------------------------------
#
#
# -----------------------------------------------------------------------------------------------------------------------------------------
#
# 1. USB-Stick mounten:
# 		mkdir /mnt/usb
# 		mount -t vfat /dev/sdb1 /mnt/usb (Devicename sdb1 ggf. anpassen)
#
# 2. Installation starten:
# 		/mnt/usb/edomi/install.sh
#		Hinweis: u.U. ist das Script zunächst ausführbar zu machen mit: chmod 777 /mnt/usb/edomi/install.sh
#
# 3. nach Abschluss der Installation ggf. den USB-Stick auswerfen:
# 		umount /mnt/usb
#
# -----------------------------------------------------------------------------------------------------------------------------------------

cd "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SERVERIP="$(hostname -I | cut -d' ' -f1)"
MAIN_PATH="/usr/local/edomi"
CENTOSVERSION=$(rpm -qa \*-release | grep -Ei "oracle|redhat|centos" | cut -d"-" -f3)

install_onCentos65 () {
	echo -e "\033[32m"
	echo ">>> EDOMI und Systemdienste für CentOS 6.5 installieren... (Quelle: Lokal)"
	echo -e "\033[39m"
	sleep 3

	# -------------------------------
	echo -e "\033[32m>>> Packete installieren\033[39m"
	rpm -Uvh centos65/rpm/*.rpm

	# -------------------------------
	echo -e "\033[32m>>> SELinux deaktivieren\033[39m"
	cp centos65/config/config /etc/selinux/
	
	# -------------------------------
	echo -e "\033[32m>>> Apache konfigurieren\033[39m"
	cp centos65/config/welcome.conf /etc/httpd/conf.d/
	cp centos65/config/httpd.conf /etc/httpd/conf/
	sed -i -e "s#===INSTALL-HTTP-ROOT===#$MAIN_PATH/www#g" /etc/httpd/conf/httpd.conf
	sed -i -e "s#===INSTALL-SERVERIP===#$SERVERIP#g" /etc/httpd/conf/httpd.conf
	chkconfig --add httpd
	chkconfig --level 235 httpd on
	
	# -------------------------------
	echo -e "\033[32m>>> PHP konfigurieren\033[39m"
	cp centos65/config/php.conf /etc/httpd/conf.d/
	cp centos65/config/php.ini /etc/
	
	# -------------------------------
	echo -e "\033[32m>>> mySQL konfigurieren\033[39m"
	cp centos65/config/my.cnf /etc/
	chkconfig --add mysqld
	chkconfig --level 235 mysqld on
	service mysqld start

	# Passwort setzen (leer), diverses
	/usr/bin/mysqladmin -u root password ""
	mysql -e "DROP DATABASE test;"
	mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%'"
	mysql -e "FLUSH PRIVILEGES;"
	
	# Remote-Access aktivieren
	mysql -e "GRANT ALL ON *.* TO mysql@'%';"
	
	# -------------------------------
	echo -e "\033[32m>>> FTP konfigurieren\033[39m"
	cp centos65/config/vsftpd.conf /etc/vsftpd/
	rm -f /etc/vsftpd/ftpusers
	rm -f /etc/vsftpd/user_list
	chkconfig --add vsftpd
	chkconfig --level 235 vsftpd on
	service vsftpd start
	
	# -------------------------------
	echo -e "\033[32m>>> Dienste konfigurieren\033[39m"
	# NTP
	chkconfig --add ntpd	
	chkconfig --level 235 ntpd on
	
	# Diverses
	chkconfig --level 123456 crond off
	chkconfig crond --del
	chkconfig --level 123456 postfix off
	chkconfig postfix --del
	chkconfig --level 123456 blk-availability off
	chkconfig blk-availability --del
	chkconfig --level 123456 lvm2-monitor off
	chkconfig lvm2-monitor --del
	chkconfig --level 123456 mdmonitor off
	chkconfig mdmonitor --del
	chkconfig --level 123456 multipathd off
	chkconfig multipathd --del
	chkconfig --level 123456 netconsole off
	chkconfig netconsole --del
	chkconfig --level 123456 rdisc off
	chkconfig rdisc --del
	chkconfig --level 123456 restorecond off
	chkconfig restorecond --del
	chkconfig --level 123456 saslauthd off
	chkconfig saslauthd --del
	chkconfig --level 123456 auditd off
	chkconfig auditd --del
	
	# Firewall IP4 ausschalten
	service iptables save
	service iptables stop
	chkconfig --level 123456 iptables off
	chkconfig iptables --del
	
	# Firewall IP6 ausschalten
	service ip6tables save
	service ip6tables stop
	chkconfig --level 123456 ip6tables off
	chkconfig ip6tables --del
	
	# System-Logging ausschalten
	service rsyslog stop
	chkconfig rsyslog off
	chkconfig rsyslog --del
			
	# -------------------------------
	echo -e "\033[32m>>> Bootvorgang konfigurieren\033[39m"
	sed -i -e '/timeout=/ s/=.*/=1/' /boot/grub/grub.conf
	sed -i -e 's/quiet//g' /boot/grub/grub.conf
	sed -i -e 's/rhgb//g' /boot/grub/grub.conf
	
	# boot.log löschen
	chmod 777 /var/log/boot.log
	rm -f /var/log/boot.log
	# -------------------------------

	install_edomi

	# -------------------------------
	echo -e "\033[32m"
	echo "Autostart konfigurieren"
	echo -e "\033[39m"
	echo "/bin/sh $MAIN_PATH/main/start.sh" >> /etc/rc.d/rc.local

	# -------------------------------
	show_splash
	exit
}

install_onCentos7 () {
	echo -e "\033[32m"
	echo ">>> EDOMI und Systemdienste für CentOS 7 installieren... (Quelle: Lokal)"
	echo -e "\033[39m"
	sleep 3

	# -------------------------------
	echo -e "\033[32m>>> Firewall deaktivieren\033[39m"
	systemctl stop firewalld
	systemctl disable firewalld

	# -------------------------------
	echo -e "\033[32m>>> Packete installieren (Teil 1)\033[39m"
	rpm -Uvh centos7x/rpm1/*.rpm

	yum-config-manager --enable remi-php72

	# -------------------------------
	echo -e "\033[32m>>> Packete installieren (Teil 2)\033[39m"
	rpm -Uvh centos7x/rpm2/*.rpm

	# -------------------------------
	echo -e "\033[32m>>> Dienste konfigurieren\033[39m"
	systemctl enable ntpd
	systemctl enable vsftpd
	systemctl enable httpd
	systemctl enable mariadb
	systemctl disable postfix

	# -------------------------------
	echo -e "\033[32m>>> SELinux deaktivieren\033[39m"
	sed -i -e '/SELINUX=/ s/=.*/=disabled/' /etc/selinux/config

	# -------------------------------
	echo -e "\033[32m>>> FTP konfigurieren\033[39m"
	rm -f /etc/vsftpd/ftpusers
	rm -f /etc/vsftpd/user_list
	sed -i -e '/listen=/ s/=.*/=YES/' /etc/vsftpd/vsftpd.conf
	sed -i -e '/listen_ipv6=/ s/=.*/=NO/' /etc/vsftpd/vsftpd.conf
	sed -i -e '/userlist_enable=/ s/=.*/=NO/' /etc/vsftpd/vsftpd.conf

	# -------------------------------
	echo -e "\033[32m>>> Apache konfigurieren\033[39m"
	sed -i -e "s/#ServerName www\.example\.com/ServerName $SERVERIP/" /etc/httpd/conf/httpd.conf
	sed -i -e "s#DocumentRoot \"/var/www/html\"#DocumentRoot \"$MAIN_PATH/www\"#" /etc/httpd/conf/httpd.conf
	sed -i -e "s#<Directory \"/var/www\">#<Directory \"$MAIN_PATH/www\">#" /etc/httpd/conf/httpd.conf
	sed -i -e "s#<Directory \"/var/www/html\">#<Directory \"$MAIN_PATH/www\">#" /etc/httpd/conf/httpd.conf

	# -------------------------------
	echo -e "\033[32m>>> mySQL/MariaDB konfigurieren\033[39m"
	systemctl start mariadb
	/usr/bin/mysqladmin -u root password ""
	mysql -e "DROP DATABASE test;"
	mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%'"
	mysql -e "FLUSH PRIVILEGES;"
	mysql -e "GRANT ALL ON *.* TO mysql@'%';"

	echo "key_buffer_size=256M" 			> /tmp/tmp.txt
	echo "sort_buffer_size=8M" 				>> /tmp/tmp.txt
	echo "read_buffer_size=16M" 			>> /tmp/tmp.txt
	echo "read_rnd_buffer_size=4M" 			>> /tmp/tmp.txt
	echo "myisam_sort_buffer_size=4M" 		>> /tmp/tmp.txt
	echo "join_buffer_size=4M" 				>> /tmp/tmp.txt
	echo "query_cache_limit=8M" 			>> /tmp/tmp.txt
	echo "query_cache_size=8M" 				>> /tmp/tmp.txt
	echo "query_cache_type=1" 				>> /tmp/tmp.txt
	echo "wait_timeout=28800" 				>> /tmp/tmp.txt
	echo "interactive_timeout=28800" 		>> /tmp/tmp.txt
	sed -i '/\[mysqld\]/r /tmp/tmp.txt' /etc/my.cnf

	# mySQL-Symlink erstellen
	echo "Alias=mysqld.service" 			> /tmp/tmp.txt
	sed -i '/\[Install\]/r /tmp/tmp.txt' /usr/lib/systemd/system/mariadb.service
	ln -s '/usr/lib/systemd/system/mariadb.service' '/etc/systemd/system/mysqld.service'
	systemctl daemon-reload

	# -------------------------------
	echo -e "\033[32m>>> PHP konfigurieren\033[39m"
	sed -i -e '/short_open_tag =/ s/=.*/= On/' /etc/php.ini
	sed -i -e '/post_max_size =/ s/=.*/= 100M/' /etc/php.ini
	sed -i -e '/upload_max_filesize =/ s/=.*/= 100M/' /etc/php.ini
	sed -i -e '/max_file_uploads =/ s/=.*/= 1000/' /etc/php.ini

     # -------------------------------
	echo -e "\033[32m>>> Bootvorgang konfigurieren\033[39m"
	sed -i -e '/GRUB_TIMEOUT=/ s/=.*/=1/' /etc/default/grub
	sed -i -e 's/quiet//g' /etc/default/grub
	sed -i -e 's/rhgb//g' /etc/default/grub
	grub2-mkconfig -o /boot/grub2/grub.cfg
   	# -------------------------------

	install_edomi

	# -------------------------------
	echo -e "\033[32m>>> Autostart konfigurieren\033[39m"
	echo "[Unit]"						> /etc/systemd/system/edomi.service
	echo "Description=EDOMI"			>> /etc/systemd/system/edomi.service
	echo "Before=getty@tty1.service getty@tty2.service getty@tty3.service getty@tty4.service getty@tty5.service getty@tty6.service" >> /etc/systemd/system/edomi.service
	echo "After=httpd.service mysqld.service network.target" >> /etc/systemd/system/edomi.service
	echo "Conflicts=getty@tty1.service getty@tty2.service getty@tty3.service getty@tty4.service getty@tty5.service getty@tty6.service" >> /etc/systemd/system/edomi.service
	echo "[Service]"					>> /etc/systemd/system/edomi.service
	echo "Type=simple" 					>> /etc/systemd/system/edomi.service
	echo "ExecStart=/bin/sh /usr/local/edomi/main/start.sh" >> /etc/systemd/system/edomi.service
	echo "TimeoutStartSec=0"			>> /etc/systemd/system/edomi.service
	echo "StandardInput=tty-force"		>> /etc/systemd/system/edomi.service
	echo "StandardOutput=inherit" 		>> /etc/systemd/system/edomi.service
	echo "StandardError=inherit" 		>> /etc/systemd/system/edomi.service
	echo "[Install]" 					>> /etc/systemd/system/edomi.service
	echo "WantedBy=default.target"		>> /etc/systemd/system/edomi.service
	systemctl daemon-reload
	systemctl enable edomi

	# -------------------------------
	show_splash
	exit
}

install_onCentos7_download () {
	echo -e "\033[32m"
	echo ">>> EDOMI und Systemdienste für CentOS 7 installieren... (Quelle: Download)"
	echo -e "\033[39m"
	sleep 3

	# -------------------------------
	echo -e "\033[32m>>> Firewall deaktivieren\033[39m"
	systemctl stop firewalld
	systemctl disable firewalld

	# -------------------------------
	echo -e "\033[32m>>> Packete installieren\033[39m"
	# NTP, nano und vsFTP installieren
	yum -y install ntp
	yum -y install nano
	yum -y install vsftpd
	
	# Apache installieren
	yum -y install httpd

	# mySQL/MariaDB installieren
	yum -y install mariadb-server

	# PHP 7.2 installieren
	yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
	yum -y install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
	yum -y install yum-utils
	yum-config-manager --enable remi-php72
	yum -y install php
	yum -y install php-mysql
	yum -y install php-soap

	# expect installieren
	yum -y install expect

	# -------------------------------
	echo -e "\033[32m>>> Dienste konfigurieren\033[39m"
	systemctl enable ntpd
	systemctl enable vsftpd
	systemctl enable httpd
	systemctl enable mariadb
	systemctl disable postfix

	# -------------------------------
	echo -e "\033[32m>>> SELinux deaktivieren\033[39m"
	sed -i -e '/SELINUX=/ s/=.*/=disabled/' /etc/selinux/config

	# -------------------------------
	echo -e "\033[32m>>> FTP konfigurieren\033[39m"
	rm -f /etc/vsftpd/ftpusers
	rm -f /etc/vsftpd/user_list
	sed -i -e '/listen=/ s/=.*/=YES/' /etc/vsftpd/vsftpd.conf
	sed -i -e '/listen_ipv6=/ s/=.*/=NO/' /etc/vsftpd/vsftpd.conf
	sed -i -e '/userlist_enable=/ s/=.*/=NO/' /etc/vsftpd/vsftpd.conf

	# -------------------------------
	echo -e "\033[32m>>> Apache konfigurieren\033[39m"
	sed -i -e "s/#ServerName www\.example\.com/ServerName $SERVERIP/" /etc/httpd/conf/httpd.conf
	sed -i -e "s#DocumentRoot \"/var/www/html\"#DocumentRoot \"$MAIN_PATH/www\"#" /etc/httpd/conf/httpd.conf
	sed -i -e "s#<Directory \"/var/www\">#<Directory \"$MAIN_PATH/www\">#" /etc/httpd/conf/httpd.conf
	sed -i -e "s#<Directory \"/var/www/html\">#<Directory \"$MAIN_PATH/www\">#" /etc/httpd/conf/httpd.conf

	# -------------------------------
	echo -e "\033[32m>>> mySQL/MariaDB konfigurieren\033[39m"
	systemctl start mariadb
	/usr/bin/mysqladmin -u root password ""
	mysql -e "DROP DATABASE test;"
	mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%'"
	mysql -e "FLUSH PRIVILEGES;"
	mysql -e "GRANT ALL ON *.* TO mysql@'%';"

	echo "key_buffer_size=256M" 			> /tmp/tmp.txt
	echo "sort_buffer_size=8M" 				>> /tmp/tmp.txt
	echo "read_buffer_size=16M" 			>> /tmp/tmp.txt
	echo "read_rnd_buffer_size=4M" 			>> /tmp/tmp.txt
	echo "myisam_sort_buffer_size=4M" 		>> /tmp/tmp.txt
	echo "join_buffer_size=4M" 				>> /tmp/tmp.txt
	echo "query_cache_limit=8M" 			>> /tmp/tmp.txt
	echo "query_cache_size=8M" 				>> /tmp/tmp.txt
	echo "query_cache_type=1" 				>> /tmp/tmp.txt
	echo "wait_timeout=28800" 				>> /tmp/tmp.txt
	echo "interactive_timeout=28800" 		>> /tmp/tmp.txt
	sed -i '/\[mysqld\]/r /tmp/tmp.txt' /etc/my.cnf

	# mySQL-Symlink erstellen
	echo "Alias=mysqld.service" 			> /tmp/tmp.txt
	sed -i '/\[Install\]/r /tmp/tmp.txt' /usr/lib/systemd/system/mariadb.service
	ln -s '/usr/lib/systemd/system/mariadb.service' '/etc/systemd/system/mysqld.service'
	systemctl daemon-reload

	# -------------------------------
	echo -e "\033[32m>>> PHP konfigurieren\033[39m"
	sed -i -e '/short_open_tag =/ s/=.*/= On/' /etc/php.ini
	sed -i -e '/post_max_size =/ s/=.*/= 100M/' /etc/php.ini
	sed -i -e '/upload_max_filesize =/ s/=.*/= 100M/' /etc/php.ini
	sed -i -e '/max_file_uploads =/ s/=.*/= 1000/' /etc/php.ini

     # -------------------------------
	echo -e "\033[32m>>> Bootvorgang konfigurieren\033[39m"
	sed -i -e '/GRUB_TIMEOUT=/ s/=.*/=1/' /etc/default/grub
	sed -i -e 's/quiet//g' /etc/default/grub
	sed -i -e 's/rhgb//g' /etc/default/grub
	grub2-mkconfig -o /boot/grub2/grub.cfg
   	# -------------------------------

	install_edomi

	# -------------------------------
	echo -e "\033[32m>>> Autostart konfigurieren\033[39m"
	echo "[Unit]"						> /etc/systemd/system/edomi.service
	echo "Description=EDOMI"			>> /etc/systemd/system/edomi.service
	echo "Before=getty@tty1.service getty@tty2.service getty@tty3.service getty@tty4.service getty@tty5.service getty@tty6.service" >> /etc/systemd/system/edomi.service
	echo "After=httpd.service mysqld.service network.target" >> /etc/systemd/system/edomi.service
	echo "Conflicts=getty@tty1.service getty@tty2.service getty@tty3.service getty@tty4.service getty@tty5.service getty@tty6.service" >> /etc/systemd/system/edomi.service
	echo "[Service]"					>> /etc/systemd/system/edomi.service
	echo "Type=simple" 					>> /etc/systemd/system/edomi.service
	echo "ExecStart=/bin/sh /usr/local/edomi/main/start.sh" >> /etc/systemd/system/edomi.service
	echo "TimeoutStartSec=0"			>> /etc/systemd/system/edomi.service
	echo "StandardInput=tty-force"		>> /etc/systemd/system/edomi.service
	echo "StandardOutput=inherit" 		>> /etc/systemd/system/edomi.service
	echo "StandardError=inherit" 		>> /etc/systemd/system/edomi.service
	echo "[Install]" 					>> /etc/systemd/system/edomi.service
	echo "WantedBy=default.target"		>> /etc/systemd/system/edomi.service
	systemctl daemon-reload
	systemctl enable edomi

	# -------------------------------
	show_splash
	exit
}

install_edomi () {
	echo -e "\033[32m>>> EDOMI installieren\033[39m"
	service mysqld stop
	mkdir -p $MAIN_PATH
	tar -xvf edomi.edomiinstall -C $MAIN_PATH
	chmod 777 -R $MAIN_PATH

	# edomi.ini anpassen
	sed -i -e "s#global_serverIP.*#global_serverIP='$SERVERIP'#" $MAIN_PATH/edomi.ini
}

delete_edomi () {
	echo -e "\033[32m>>> EDOMI-Installation löschen\033[39m"
	service mysqld stop
	
	rm -rf $MAIN_PATH
	rm -rf /var/lib/mysql/edomi*
}

show_title () {
	echo -e "\033[42m\033[30m                                                                                \033[49m\033[39m"
	echo -e "\033[42m\033[30m                       EDOMI - (c) Dr. Christian Gärtner                        \033[49m\033[39m"
	echo -e "\033[42m\033[30m                                                                                \033[49m\033[39m"
}

show_splash () {
	show_title
	echo -e "\033[32mDie EDOMI-Installation ist abgeschlossen.\033[39m"
	echo -e "\033[32mBeim nächsten Systemstart wird EDOMI automatisch gestartet.\033[39m"
	echo -e "\033[32mNeustart mit: reboot (ENTER)\033[39m"
	echo ""
}


while : ; do

	clear
	show_title
	echo ""
	echo -e " IP-Adresse: \033[32m$SERVERIP\033[39m (wird zur Konfiguration verwendet)"
	echo ""
	echo "--------------------------------------------------------------------------------"
	echo " 1 = EDOMI und Systemdienste für CentOS 6.5 installieren (Quelle: Lokal)"
	echo " 2 = EDOMI und Systemdienste für CentOS 7.x installieren (Quelle: Lokal)"
	echo " 3 = EDOMI und Systemdienste für CentOS 7.x installieren (Quelle: Download)"
	echo ""
	echo " e = nur EDOMI installieren (eine vorhandene Installation wird überschrieben!)"
	echo " x = Beenden"
	echo "--------------------------------------------------------------------------------"

	read MENUOPTION
	if [ "$MENUOPTION" == "1" ]; then
		if [ "$CENTOSVERSION" == "6" ]; then
			install_onCentos65
		else
			echo "Das installierte CentOS scheint Version $CENTOSVERSION zu sein!"
			echo "Sollen dennoch die Systemdienste für CentOS 6.5 installiert werden (j/n)?"
			read CONFIRM
			if [ "$CONFIRM" == "j" ]; then 
				install_onCentos65
			fi	
		fi
	fi 
	if [ "$MENUOPTION" == "2" ]; then 
		if [ "$CENTOSVERSION" == "7" ]; then
			install_onCentos7
		else
			echo "Das installierte CentOS scheint Version $CENTOSVERSION zu sein!"
			echo "Sollen dennoch die Systemdienste für CentOS 7.x installiert werden (j/n)?"
			read CONFIRM
			if [ "$CONFIRM" == "j" ]; then 
				install_onCentos7
			fi	
		fi
	fi 
	if [ "$MENUOPTION" == "3" ]; then 
		if [ "$CENTOSVERSION" == "7" ]; then
			install_onCentos7_download
		else
			echo "Das installierte CentOS scheint Version $CENTOSVERSION zu sein!"
			echo "Sollen dennoch die Systemdienste für CentOS 7.x installiert werden (j/n)?"
			read CONFIRM
			if [ "$CONFIRM" == "j" ]; then 
				install_onCentos7_download
			fi	
		fi
	fi 
	if [ "$MENUOPTION" == "e" ]; then 
		echo -e "\033[32m"
		echo ">>> nur EDOMI installieren..."
		echo -e "\033[39m"
		sleep 3
		delete_edomi
		install_edomi
		show_splash
		exit
	fi 
	if [ "$MENUOPTION" == "x" ]; then 
		exit
	fi 

done
