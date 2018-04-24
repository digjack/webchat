if [ -e logs/main.log ]
then 
    touch logs/main.log
fi
nohup bash start.sh id_list > logs/main.log 2>&1 &
