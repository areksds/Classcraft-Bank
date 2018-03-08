# Classcraft Bank
A fully functioning gold point bank for [Classcraft](https://classcraft.com) built off of therecluse26's [PHP Login Script](https://github.com/therecluse26/PHP-Login/tree/v2.0).

## Installation
The Classcraft Bank requires the following to operate:
* MySQL database
* SMTP host (for site-wide emails)
* Access to your server's cron jobs (setting up the loan cron)

As the bank has a built-in installer script, simply download the latest release or clone the repository and upload to your server. The script consists of two parts:
* Database configuration writer and installer
* General configuration writer

Simply navigate through the menus. Once you're done, you'll be led back to the homepage.

Beyond the initial installer, you will need to set up the cron job of your loan. Add the following line to your cron:
```bash
* * * * * /usr/bin/php /PATH/TO/SITE/login/includes/loandates.php >/dev/null 2>&1
```

Finally, be sure to set yourself as an administrator to gain access to the admin panel:
```sql
UPDATE `members` SET `isAdmin`= 1 WHERE username="YOURUSERNAME"
```

That's basically it for installation. On to the features.

