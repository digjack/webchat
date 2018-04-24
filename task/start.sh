#!/bin/bash

while true
do 
    if ! [ -s $1 ];then
        sleep 2;continue;
    fi

    tailCommand="tail -n 1 $1"
    echo $tailCommand
    id=`$tailCommand`

    touchCommand="touch logs/${id}.log"
    `$touchCommand`
    nohup python webchat.py ${id} > logs/${id}.log &
    cp /dev/null $1 
done   
