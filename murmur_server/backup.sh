#!/bin/bash
###########################################################
#                                                         #
#       =========================================         #
#       |       DNW Despe Networks              |         #
#       =========================================         #
#       |>>>>>>>> Murmur Backup >>>>>>>>>>>>>>>>|         #
#       |>>>>>>>> http://www.despe.de >>>>>>>>>>|         #
#       |>>>>>>>> DO NOT EDIT, only if u know >>|         #
#       |>>>>>>>> what are you doing! >>>>>>>>>>|         #
#       =========================================         #
#                                                         #
###########################################################
# 	http://wiki.ubuntuusers.de/Skripte/inkrementelles_Backup
#	Script fuer inkrementelles Backup mit 30 taegigem Vollbackup

### Einstellungen ##
BACKUPDIR="/home/mumble/backup"                # Pfad zum Backupverzeichnis
ROTATEDIR="/home/mumble/backup/rotate"     # Pfad wo die Backups nach 30 Tagen konserviert werden
TIMESTAMP="timestamp.dat"                 # Zeitstempel
SOURCE="home/mumble"                            # Verzeichnis(se) welche(s) gesichert werden soll(en)
DATUM="$(date +%d-%m-%Y)"             # Datumsformat einstellen
ZEIT="$(date +%H:%M)"                          # Zeitformat einstellen

### Verzeichnisse/Dateien welche nicht gesichert werden sollen ! Achtung keinen Zeilenumbruch ! ##
EXCLUDE="--exclude=home/mumble/backup/fullbackup*.tgz --exclude=home/mumble/backup --exclude=home/mumble/backup/rotate --exclude=home/mumble/public_html/mumblewebadmin/sessions"

### Wechsel in root damit die Pfade stimmen ##
cd /

### Backupverzeichnis anlegen ##
mkdir -p ${BACKUPDIR}

### Test ob Backupverzeichnis existiert und Mail an Admin bei fehlschlagen ##
if [ ! -d "${BACKUPDIR}" ]; then

echo "Backupverzeichnis nicht vorhanden!"

 . exit 1
fi

### Alle Variablen einlesen und letzte Backupdateinummer herausfinden ##
set -- ${BACKUPDIR}/fullbackup-???.tgz
lastname=${!#}
backupnr=${lastname##*backup-}
backupnr=${backupnr%%.*}
backupnr=${backupnr//\?/0}
backupnr=$[10#${backupnr}]

### Backupdateinummer automatisch um +1 bis maximal 30 erhoehen ##
if [ "$[backupnr++]" -ge 30 ]; then
mkdir -p ${ROTATEDIR}/${DATUM}-${ZEIT}

### Test ob Rotateverzeichnis existiert und Mail an Admin bei fehlschlagen ##
if [ ! -d "${ROTATEDIR}/${DATUM}-${ZEIT}" ]; then

echo "Rotateverzeichnis nicht vorhanden!"

 . exit 1
else
mv ${BACKUPDIR}/* ${ROTATEDIR}/${DATUM}-${ZEIT} 
fi

### Abfragen ob das Backupverschieben erfolgreich war ##
if [ $? -ne 0 ]; then

echo "Backupverschieben fehlerhaft!"

exit 1
else

echo "Backupverschieben erfolgreich"

### die Backupnummer wieder auf 1 stellen ##
backupnr=1 
fi 
fi

backupnr=000${backupnr}
backupnr=${backupnr: -3}
filename=fullbackup-${backupnr}.tgz

### Nun wird das eigentliche Backup ausgefuehrt ##
tar -cpzf ${BACKUPDIR}/${filename} -g ${BACKUPDIR}/${TIMESTAMP} ${SOURCE} ${EXCLUDE}

### Abfragen ob das Backup erfolgreich war ##
if [ $? -ne 0 ]; then

echo "Backup (${SOURCE}) war fehlerhaft!"

else

echo "Backup (${SOURCE}) war erfolgreich"

fi
