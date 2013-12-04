#######################################
#! /bin/sh

#######################################
# Please edit:                          

NAME=murmur_start.sh
SCRIPTNAME=/home/mumble/$NAME
HOME=/home/mumble
USER=mumble
DESC="Murmur Server"

#######################################
# PLEASE DONT CHANGE ANYTHING!!


do_start()
{
    cd $HOME; rm -f murmur.log
    cd $HOME; touch murmur.log
    cd $HOME; chmod 750 murmur.log
    cd $HOME; touch murmur.pid
    cd $HOME; chmod 750 murmur.pid
    cd $HOME; chmod 750 murmur.x86
    su $USER -c "cd $HOME; ./murmur.x86"
}
do_stop()
{
    su $USER -c "killall -9 murmur.x86"
    cd $HOME; rm -f murmur.pid
}

case "$1" in
  start)
    if [ $USER = "root" ]; then
	echo WARNING ! For security reasons we advise: DO NOT RUN THE SERVER AS ROOT
	c=1
	while [ "$c" -le 10 ]; do
		echo -n "!"
		sleep 1
		c=$((++c))
		done
		echo "!"
	fi
    echo "Starting $DESC $NAME"
        do_start
        ;;
  stop)
        echo "Stopping $DESC $NAME"
        do_stop
        ;;
  restart)
        echo "Stopping $DESC $NAME"
        do_stop
        echo "Starting $DESC $NAME"
        do_start
        ;;
  *)
        echo "Usage: $SCRIPTNAME {start|stop|restart}"
esac