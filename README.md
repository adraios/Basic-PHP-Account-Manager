# Basic-PHP-Account-Manager
An example of a typical register, login, logout features with admin account management

# Troubleshooting
For a perfect working process, ensure to follow this instructions:

- Get installed Mbstring extension:
```
sudo apt update
sudo apt install php-mbstring
```

- Give owner to `www-data` in root folder with (or where you have the project folder):
```
chown www-data:www-data /var/www/html/
```

- Change Apache Override permisions for the directory `/var/www/`:
```
sudo nano /etc/apache2/apache2.conf
```

Change the block code with:
```
<Directory /var/www/>
    AllowOverride All
</Directory>
```

- Enable Mod Override:
```
sudo a2enmod rewrite
```

- For any change reload Apache service:
```
sudo systemctl restart apache2
```