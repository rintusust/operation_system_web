Crons: Check Queues COmmand '/var/www/html/ansarerp/local$ php artisan queue:listen '

Check Current Crob tab: nano /etc/crontab OR crontab -e

Remove unverified Queue: php artisan remove:unverified

Check Cron Supervisor: sudo supervisorctl status

Supervisor URL: /etc/supervisor/conf.d/ansarerp_queue.conf

# Commands

* sudo service supervisor restart
* sudo unlink /run/supervisor.sock
* sudo supervisorctl status

* service mysql stop
* service mysql start
* service mysql restart

* Set Global MAX_USER_CONNECTIONS=10000;
* Set Global MAX_CONNECTIONS=10000;

# PM2 Applications Url with Command

* Add job into pm2 :/var/www/html/ansar_recruitment/server pm2 start app.js

# Short code SMS url
* https://www.bdansarerp.gov.bd/HRM/receive_sms



