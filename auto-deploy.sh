if [ $EUID -eq 0 ]; then
  # User is Root
  echo "Pulling from origin"
  git pull origin
  if [ -z $1 ]; then
    path="/var/www/html"
  else
    path=$1
  fi
  echo "Deleting content of" $path
  mv $path/res /tmp/res
  rm -rf /tmp/userSettings
  mv $path/userSettings /tmp/userSettings
  mv /tmp/userSettings $path/
  rm -rf $path/
  echo "Copying www to " $path
  cp -R www/ $path/
  mv /tmp/res $path/
  rm -rf /tmp/res
  echo "Changing the permission of" $path
  chmod -R a+w $path/*
  echo "Restarting Apache"
  service apache2 restart
else
  echo "User must be root to deploy"
  exit 1
fi
