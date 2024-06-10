FILENAME="$1"
while : ; do
	clear
	echo "--------------------------------------------------------------------------------"
	echo "EDOMI - Backupwiederherstellung (Restore)"
	echo "--------------------------------------------------------------------------------"
	echo ""
	echo "Backupdatei: $FILENAME"
	echo ""
	echo -e "\033[41m\033[39mACHTUNG: Bei einer Wiederherstellung gehen alle Daten und Einstellungen verloren!\033[49m\033[39m"
	echo ""
	echo "Kurzanleitung:"
	echo "Dieses Script erwartet als Parameter den vollständigen Dateinamen (mit Pfad) der wiederherzustellenden Backup-Datei, z.B.:"
	echo "sh /usr/local/edomi/main/restore.sh \"/tmp/Backup.edomibackup\" (die Anführungszeichen sind wichtig, wenn der Dateiname z.B. Leerzeichen enthält)"
	echo "Achtung: Die Backupdatei wird nicht überprüft, sondern unmittelbar wiederhergestellt!"
	echo "--------------------------------------------------------------------------------"
	echo " r (ENTER) = Backupdatei wiederherstellen"
	echo " q (ENTER) = Beenden"
	echo "--------------------------------------------------------------------------------"

	read answer 

	if [ "$answer" == "r" ]; then 
		if [ -f "$FILENAME" ]; then
			echo "Restore wird in 3 Sekunden gestartet..."
			sleep 3s

			echo "EDOMI beenden... (dies kann bis zu 30 Sekunden benötigen)"
			php /usr/local/edomi/main/control.php quit

			echo "Dienste beenden..."
			service mysqld stop
			service httpd stop
			sleep 1s
			
			echo "Restore vorbereiten..."
			rm -rf /usr/local/edomi
			rm -f /var/lib/mysql/mysql.sock
			rm -rf /var/lib/mysql/edomiAdmin
			rm -rf /var/lib/mysql/edomiProject
			rm -rf /var/lib/mysql/edomiLive
			sleep 1s
			
			echo "Restore durchführen..."
			tar -xf "$FILENAME" -C /
			chmod 777 -R /usr/local/edomi
			rm -rf /usr/local/edomi/clientid.edomi
	
			echo "Dienste starten..."
			service mysqld start
			service httpd start
	
			echo "Restore abgeschlossen. Reboot in 5 Sekunden..."
			sleep 5s
			reboot
			exit
		fi
		echo "Fehler: Die Backupdatei $FILENAME wurde nicht gefunden."
		exit
	fi 

	if [ "$answer" == "q" ]; then 
		exit
	fi 
done
