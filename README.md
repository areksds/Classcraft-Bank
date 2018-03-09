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
UPDATE members SET isAdmin= 1 WHERE username="YOURUSERNAME";
```

That's basically it for installation. On to the features.

## Features
* Teacher transaction verification and authorization system.
* Withdrawals and deposits (up to 1000 GP for deposits).
* Loans (up to 500 GP and 2-weeks to pay back).
* Sending GP to friends.
* Administrator panel for users with `isAdmin` set to `1`.
* User balance manipulation and banning available through admin panel, as well as total bank balance and active loans.
* Emails sent for all actions, with reasons for transaction rejections (managed through [PHPMailer](https://github.com/PHPMailer/PHPMailer)).
* User log for all transactions.
* Interest distribution system.
  * All interest accumulated can be distributed through the admin panel dropdown. The interest per user is calculated by creating a fraction representing the user's balance as compared to the overall bank balance.

Any and all questions can be directed to me(at)buildblox.net or through an issue on the repository.
