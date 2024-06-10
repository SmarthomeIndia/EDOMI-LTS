#!/bin/sh

# -----------------------------------------------------------------------------------------------------------
#
# Dieses EDOMI-Startscript unterliegt den EDOMI-Lizenzbestimmungen (siehe /usr/local/edomi/Lizenzhinweis.txt)
#
# -----------------------------------------------------------------------------------------------------------

MAIN_PATH="/usr/local/edomi"
MYSQL_PATH="/var/lib/mysql"
RETURNCODE=0

wait_cancel () {
	read -t $1
	local userkey=$?
	if [ $userkey == "0" ]; then
		echo "0"
	else
		echo "1"
	fi
}

server_init () {
	echo -e "\033[32mSERVER: Initialisierung\033[39m"
	chmod 777 /dev/vcsa
	
	rm -f /tmp/*.edomi*
	
	service mysqld stop
	rm -f $MYSQL_PATH/mysql.sock
	service mysqld start
	
	service ntpd stop
	timeout 30s ntpd -q -g -x -n
	service ntpd start
}

edomi_config () {
	echo -e "\033[32mEDOMI: Basis-Konfiguration\033[39m"
	php $MAIN_PATH/main/setconfig.php $MAIN_PATH/edomi.ini $MAIN_PATH/www/shared/php/config.php
}

edomi_comand () {
	if [ $RETURNCODE == "10" ]; then
		echo -e "\033[32mEDOMI: Neustart > Pausieren\033[39m"
		return
	fi

	if [ $RETURNCODE == "12" ]; then
		echo -e "\033[32mEDOMI: Neustart > Starten\033[39m"
		return
	fi

	if [ $RETURNCODE == "14" ]; then
		echo -e "\033[32mEDOMI: Neustart > Projektaktivierung > Pausieren\033[39m"
		return
	fi

	if [ $RETURNCODE == "15" ]; then
		echo -e "\033[32mEDOMI: Neustart > Live-Projekt löschen > Projektaktivierung > Pausieren\033[39m"
		return
	fi

	if [ $RETURNCODE == "22" ]; then
		echo -e "\033[32mEDOMI: Beenden\033[39m"
		exit
	fi

	if [ $RETURNCODE == "13" ]; then
		echo -e "\033[32mSERVER: Reboot\033[39m"
		echo -e "\033[43m\033[30mServer wird in 5s neu gestartet...                         (Abbrechen mit ENTER)\033[49m\033[39m"
		if [ "$(wait_cancel 5)" == "1" ]; then
			reboot
		else
			echo "(abgebrochen)"
		fi
		exit
	fi
	
	if [ $RETURNCODE == "23" ]; then
		echo -e "\033[32mSERVER: Shutdown\033[39m"
		echo -e "\033[43m\033[30mServer wird in 5s ausgeschaltet...                         (Abbrechen mit ENTER)\033[49m\033[39m"
		if [ "$(wait_cancel 5)" == "1" ]; then
			shutdown -h now
		else
			echo "(abgebrochen)"
		fi
		exit
	fi

	if [ $RETURNCODE == "21" ]; then
		echo -e "\033[32mEDOMI: Backup wiederherstellen\033[39m"
		sleep 5s
		if [ -f /tmp/edomirestore.data ]; then
			sh /tmp/edomirestore.sh
		else
			echo "FEHLER: Kein Restore-Script vorhanden!"
			echo -e "\033[41m\033[30mServer wird in 30s neu gestartet...                        (Abbrechen mit ENTER)\033[49m\033[39m"
			if [ "$(wait_cancel 30)" == "1" ]; then
				reboot
			else
				echo "(abgebrochen)"
			fi
		fi
		exit
	fi
			
	if [ $RETURNCODE == "24" ]; then
		echo -e "\033[32mEDOMI: Update installieren\033[39m"
		sleep 5s
		if [ -f /tmp/edomiupdate.data ]; then
			sh /tmp/edomiupdate.sh
		else
			echo "FEHLER: Keine Update-Datei vorhanden!"
			echo -e "\033[41m\033[30mServer wird in 30s neu gestartet...                        (Abbrechen mit ENTER)\033[49m\033[39m"
			if [ "$(wait_cancel 30)" == "1" ]; then
				reboot
			else
				echo "(abgebrochen)"
			fi
		fi
		exit
	fi
	
	echo -e "\033[41m\033[30mUNBEKANNTER FEHLER > Server wird in 30s neu gestartet...   (Abbrechen mit ENTER)\033[49m\033[39m"
	if [ "$(wait_cancel 30)" == "1" ]; then
		reboot
	else
		echo "(abgebrochen)"
	fi
	exit
}



# Startup
echo -e "\033[42m\033[30m                                                                                \033[49m\033[39m"
echo -e "\033[42m\033[30m                       EDOMI_LTS - Long Term Evolution                        \033[49m\033[39m"
echo -e "\033[42m\033[30m                                                                                \033[49m\033[39m"

server_init
edomi_config

echo -e "\033[43m\033[30m           >>> EDOMI startet in 3 Sekunden (Abbrechen mit ENTER) <<<            \033[49m\033[39m"
if [ "$(wait_cancel 3)" == "0" ]; then
	echo "(abgebrochen)"
	exit
fi

# Mainloop
while : ; do
	pkill -9 php
	php $MAIN_PATH/main/proc/proc_main.php $RETURNCODE
	RETURNCODE=$?
	pkill -9 php
	edomi_comand
done
