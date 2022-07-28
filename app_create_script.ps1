function app-create {
  $Appname = Read-Host -Prompt 'What is the appname'
  $app = $Appname.ToLower()
  $app = $app -replace '\s','_'
  $db_prefix = $app.Substring(0, 3)
  $User = 'wp'
  $Password = 'bib'
  $email = 'birkblauner@gmail.com'

  Write-Host "Getting the boilerplate"
  mkdir $app
  git clone https://github.com/bbcbirk/boilerplate.git .\$app\
  cd .\$app\
  Remove-Item -Recurse -Force .git

  Write-Host "Starting search-replacing"
  $configFiles = Get-ChildItem .\* -include ('docker-compose.yml','wp-config.php', 'default.conf', 'composer.json', '.gitignore' ) -recurse
  foreach ($file in $configFiles)
  {
      (Get-Content $file.PSPath) |
      Foreach-Object { $_ -replace "__APP_PREFIX", $db_prefix } |
      Foreach-Object { $_ -replace "__APPNAME", $app } |
      Foreach-Object { $_ -replace "__USER", $User } |
      Foreach-Object { $_ -replace "__PASS", $Password } |
      Set-Content $file.PSPath
  }

  Write-Host "Install Composer"
  & 'C:\Program Files\Git\bin\sh.exe' -lic 'composer install'

  Write-Host "Init Repo"
  git init -b main
  git add .
  git commit -m "Init App"
  gh repo create $app -s . --private --push

  Write-Host "Update Host"
  Add-Content -Path $env:windir\System32\drivers\etc\hosts -Value "`n127.0.0.1`t$app.test" -Force

  Write-Host "Add Certificates"
  cd .\nginx\certs\
  mkcert "$app.test"
  cd ..\..

  Write-Host "Start Docker"
  docker-compose up -d --build site
  
  Write-Host "Install WP"
  Start-Sleep -Seconds 15
  docker-compose run --rm wp core install --url="$app.test/wp" --title=$Appname --admin_user=admin --admin_password=$Password --admin_email=$email
  docker-compose run --rm wp option update siteurl "http://$app.test/wp"
  docker-compose run --rm wp cache flush
  docker-compose run --rm wp option update home "http://$app.test"
  docker-compose run --rm wp option update timezone_string "Europe/Copenhagen"
  docker-compose run --rm wp user delete admin --yes
  docker-compose run --rm wp user create $User $email --role=administrator --user_pass=$Password --first_name=WP --last_name=Admin

  Write-Host "Done..."
  Write-Host "Url: http://$app.test"
  Write-Host "Admin Url: http://$app.test/wp/wp-login.php"
}