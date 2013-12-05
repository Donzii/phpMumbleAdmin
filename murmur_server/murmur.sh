#!/bin/bash
###########################################################
#                                                         #
#       =========================================         #
#       |       DNW Despe Networks              |         #
#       =========================================         #
#       |>>>>>>>> Murmur Startscript v1      >>>|         #
#       |>>>>>>>> http://www.despe.de >>>>>>>>>>|         #
#       |>>>>>>>> DO NOT EDIT, only if u know >>|         #
#       |>>>>>>>> what are you doing! >>>>>>>>>>|         #
#       =========================================         #
#                                                         #
###########################################################

#	Name der für das Script ausgeben wird
NAME="Mumble Server"

#	Das Startcommando
COMMAND="murmur.x86"

#	Die ini-Daten von murmur
INI=murmur.ini

#	Hier bewahrt das Script den PID auf um später den Server wieder stoppen zu können
PIDFILE=murmur.pid

#########################################################################
# DONT EDIT BELOW THIS LINE!!! Broken Server is the reason !!!          #
#########################################################################

case "$1" in
	start)

		if test -f $PIDFILE; then
			PID="`cat $PIDFILE`"
   			echo "PID-File vorhanden. Prozess-ID '$PID' . Server abgestürzt?"
		elif test $PIDFILE; then
			echo "Prozess-ID und PID-File nicht vorhanden, starte den $NAME"
			
			rm -f murmur.log
			touch murmur.log
			touch $PIDFILE
			chmod 750 $COMMAND
			./$COMMAND -ini $INI
			PID="`cat $PIDFILE`"
			echo "$NAME erfolgreich wieder hergestellt. Prozess-ID '$PID'"
	fi

;;

	stop)
		if test -f $PIDFILE; then
			echo "Stoppe den $NAME"
			PID="`cat $PIDFILE`"
			kill $PID
			killall -9 $COMMAND
			echo "$NAME wurde beendet"
			rm -f $PIDFILE
			echo "$PIDFILE wurde entfernt"
			chmod 644 $COMMAND
		elif test $PIDFILE; then
			echo "Der $NAME läuft nicht."
	fi
;;

	restart)
		PID="`cat $PIDFILE`"
		echo "$NAME '$PID' wird gestoppt ..."
		kill $PID
		killall -9 $COMMAND
   		rm -f $PIDFILE
		touch $PIDFILE
		chmod 750 $COMMAND
		sleep 5
		./$COMMAND -ini $INI
		PID="`cat $PIDFILE`"
		echo "$NAME erfolgreich wieder hergestellt. Prozess-ID '$PID'"

;;


*)
	echo "Usage: $0 Parameter eingeben {start|stop|restart}"
	exit 1
;;
esac