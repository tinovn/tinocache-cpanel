#/usr/bin/bash

# ASK FOR INSTALLATION
#Create by Tino Team


configFile='/usr/local/cpanel/base/frontend/jupiter/dynamicui/dynamicui_tinocachePlugin.conf';

if [ -f  "$configFile" ]
then
    installed=1;
fi;

if [ "$installed" = "1" ]
then
    echo -n "
Select Action:
    1 - Uninstall TinoCache Plugin?
:";
    read option;
    case "$option" in
        "1") action='uninstall';;
    esac;
else
    action='install';
fi;


# TAKE ACTION

case "$action" in
    "install")


        #WHM
        # Check for and create the directory for plugin and AppConfig files.
        if [ ! -d /var/cpanel/apps ]
            then
            mkdir /var/cpanel/apps
            chmod 775 /var/cpanel/apps
        fi

        # Check for and create the directory for plugin CGI files.
        if [ ! -d /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin ]
          then
            mkdir /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin
            chmod 775 /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin
        fi

        # Register the plugin with AppConfig.
        /usr/local/cpanel/bin/register_appconfig InstallFiles/tinocachePluginWHMPlugin.conf

        # Copy plugin files to their locations and update permissions.
        /bin/cp -R tinocachePluginWHM/* /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin
        chmod 775 -R /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/*
        /bin/cp InstallFiles/tinocachePlugin.png /usr/local/cpanel/whostmgr/docroot/addon_plugins
        echo -en "\nChào cưng. Nhập WHM API Token đi nào: ";
        read apikey;
        echo "apitoken=\"$apikey\"" > /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/api.ini


        #custom header

        /bin/cp -rf ./InstallFiles/customizations/global_header.html.tt /var/cpanel/customizations/includes

        # Install yum install memcached
        yum install memcached -y
        yum install libmemcached -y
        yum install epel-release -y
        yum install redis -y


        #CPanel
        $install

        /usr/local/cpanel/scripts/install_plugin InstallFiles/tinocachePlugin.zip

        #check if dir exists
        if [ ! -d /usr/local/cpanel/base/frontend/jupiter/tinocachePlugin ]
          then
            mkdir /usr/local/cpanel/base/frontend/jupiter/tinocachePlugin
            chmod 775 /usr/local/cpanel/base/frontend/jupiter/tinocachePlugin
        fi

        #copy files
        /bin/cp -R ./tinocachePluginCPanel/tinocachePlugin/* /usr/local/cpanel/base/frontend/jupiter/tinocachePlugin

        # CONFIG DATABASE
        chmod 777 -R /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Db
        chmod 777 -R /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Db/tinocachePlugin.sqlite
        php /usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/index.php install

        #kill all PID
        pkill memcached
        pkill redis-server

        #Check /bin/cre_redis
        /bin/cp -f ./InstallFiles/config/memcached.cfg /etc/cagefs/conf.d
        /bin/cp -f ./InstallFiles/config/redis_cre.cfg /etc/cagefs/conf.d
        /bin/cp -f ./InstallFiles/config/redis-server.cfg /etc/cagefs/conf.d
        /bin/cp -f ./InstallFiles/config/cre_redis /usr/bin
        chmod a+x /usr/bin/cre_redis
        /bin/cp -f ./InstallFiles/config/tinocache_reboot /scripts/
        chmod +x /scripts/tinocache_reboot


        #@reboot echo /scripts/tinocache_reboot | at now + 5 minutes
        sed -i -e "/tinocache_reboot/d" /var/spool/cron/root
        touch /var/spool/cron/root
        echo "@reboot echo /scripts/tinocache_reboot | at now + 2 minutes" >> /var/spool/cron/root
        crontab -u root -l
        echo "Đợi tao update cagefs tí... ";
        cagefsctl --force-update

        /scripts/tinocache_reboot

    ;;

    "uninstall")
        # Uninstall cPanel plugin
        rm -rf "/usr/local/cpanel/base/frontend/jupiter/tinocachePlugin"
        rm -f "$configFile"
        rm -f "/etc/cagefs/conf.d/memcached.cfg"
        rm -f "/etc/cagefs/conf.d/redis_cre.cfg"
        rm -f "/etc/cagefs/conf.d/redis-server.cfg"
        sed -i -e "/tinocache_reboot/d" /var/spool/cron/root;
        /usr/local/cpanel/bin/rebuild_sprites --quiet

        # Uninstall WHM plugin
        rm -rf "/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin"
        /usr/local/cpanel/bin/unregister_appconfig /var/cpanel/apps/tinocachePluginWHMPlugin.conf
    ;;
esac;
