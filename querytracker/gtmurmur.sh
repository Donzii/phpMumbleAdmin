#!/bin/bash
###########################################################
#                                                         #
#       =========================================         #
#       |       DNW Despe Networks              |         #
#       =========================================         #
#       |>>>>>>>> GT Murmur Startscript v1   >>>|         #
#       |>>>>>>>> http://www.despe.de >>>>>>>>>>|         #
#       |>>>>>>>> DO NOT EDIT, only if u know >>|         #
#       |>>>>>>>> what are you doing! >>>>>>>>>>|         #
#       =========================================         #
#                                                         #
###########################################################

#	Name der für das Script ausgeben wird
NAME="GameTracker-Query"

#	Arbeitsverzeichnis für GameTracker-Query Murmur
BASEDIR="/home/mumble/querytracker"

#	Das Startcommando für  GameTracker-Query Murmur
COMMAND="gtmurmur-static"

#	Pfad zum Murmur Server
PATH="/home/mumble"

#	Die ini-Daten von murmur
INI=murmur.ini

#	Hier bewahrt das Script den GameTracker-Query-PID auf um später den Server wieder stoppen zu können
PIDFILE=gtmurmur-static.pid

#########################################################################
# DONT EDIT BELOW THIS LINE!!! Broken Server is the reason !!!          #
#########################################################################

case "$1" in
	start)
		cd $BASEDIR
		if test -f $PIDFILE; then
			PID="`cat $PIDFILE`"
   			echo "PID-File vorhanden. Prozess-ID '$PID' . Server abgestürzt?"
		elif test $PIDFILE; then
			echo "Prozess-ID und PID-File nicht vorhanden, starte den $NAME"
			touch $PIDFILE
			chmod 750 $COMMAND
			nice -n 19 ./$COMMAND -conf $PATH/$INI 2> /dev/null >&2 &
			ps -ef | grep $COMMAND | grep -v grep | awk ' { print $2 }' > $PIDFILE
			PID="`cat $PIDFILE`"
			echo "$NAME erfolgreich wieder hergestellt. Prozess-ID '$PID'"
	fi

;;

	stop)
		cd $BASEDIR
		if test -f $PIDFILE; then
			echo "Stoppe den $NAME"
			PID="`cat $PIDFILE`"
			kill $PID
			killall -9 $COMMAND
			echo "$NAME2 wurde beendet"
			rm -f $PIDFILE
			echo "$PIDFILE wurde entfernt"
			chmod 644 $COMMAND
		elif test $PIDFILE; then
			echo "Der $NAME läuft nicht."
	fi
;;

	restart)
		cd $BASEDIR
		PID="`cat $PIDFILE`"
		echo "$NAME '$PID' wird gestoppt ..."
		kill $PID
		killall -9 $COMMAND
		rm -f $PIDFILE
		sleep 5
		cd $BASEDIR
		nice -n 19 ./$COMMAND -conf $PATH/$INI 2> /dev/null >&2 &
		ps -ef | grep $COMMAND | grep -v grep | awk ' { print $2 }' > $PIDFILE
		PID="`cat $PIDFILE`"
		echo "$NAME erfolgreich wieder hergestellt. Prozess-ID '$PID'"

;;

*)
	echo "Usage: $0 Parameter eingeben {start|stop|restart}"
	exit 1
;;
esac