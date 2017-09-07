# File Transfer

## Setup

```
$ git clone https://github.com/xinxinw1/transfer.git
$ cd transfer
$ mysql -u root -p < transfer.sql
$ mysqladmin -u transfer -p'transfer' password <new password>
$ vim authinfo.php
  - Update the password to your new password
$ vim /etc/php/php.ini
  - Set post_max_size and upload_max_filesize to 20M
$ touch transfer-pass
$ mkdir chunks files
$ chown www-data:www-data transfer-pass chunks/ files/
```
