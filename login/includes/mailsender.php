<?php
class MailSender
{
    public function sendMail($email, $user, $id, $type)
    {
        require_once 'scripts/PHPMailer/PHPMailerAutoload.php';
        include 'config.php';

        $finishedtext = $active_email;

        // ADD $_SERVER['SERVER_PORT'] TO $verifyurl STRING AFTER $_SERVER['SERVER_NAME'] FOR DEV URLS USING PORTS OTHER THAN 80
        // substr() trims "createuser.php" off of the current URL and replaces with verifyuser.php
        // Can pass 1 (verified) or 0 (unverified/blocked) into url for "v" parameter
        $verifyurl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "verifyuser.php?v=1&uid=" . $id;

        $withdrawapproveurl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/withdraw.php?code=OSKKMk1GgccTRC4DUjfR&status=1&amount=" . $id . "&username=" . $user;
        $withdrawrejecturl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/withdraw.php?code=OSKKMk1GgccTRC4DUjfR&status=0&amount=" . $id . "&username=" . $user;
        $depositapproveurl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/deposit.php?code=OSKKMk1GgccTRC4DUjfR&status=1&amount=" . $id . "&username=" . $user;
        $depositrejecturl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/deposit.php?code=OSKKMk1GgccTRC4DUjfR&status=0&amount=" . $id . "&username=" . $user;
        $loanapproveurl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/loans.php?code=OSKKMk1GgccTRC4DUjfR&status=1&amount=" . $id . "&username=" . $user;
        $loanrejecturl = substr($base_url . $_SERVER['PHP_SELF'], 0, -strlen(basename($_SERVER['PHP_SELF']))) . "includes/loans.php?code=OSKKMk1GgccTRC4DUjfR&status=0&amount=" . $id . "&username=" . $user;

        // Create a new PHPMailer object
        // ADD sendmail_path = "env -i /usr/sbin/sendmail -t -i" to php.ini on UNIX servers
        $mail = new PHPMailer;
        $mail->isHTML(true);
        $mail->CharSet = "text/html; charset=UTF-8;";
        $mail->WordWrap = 80;
        $mail->setFrom($from_email, $from_name);
        $mail->AddReplyTo($from_email, $from_name);
        /****
        * Set who the message is to be sent to
        * CAN BE SET TO addAddress(youremail@website.com, 'Your Name') FOR PRIVATE USER APPROVAL BY MODERATOR
        * SET TO addAddress($email, $user) FOR USER SELF-VERIFICATION
        *****/
        $mail->addAddress($email, $user);

        //Sets message body content based on type (verification or confirmation)
        if ($type == 'Verify') {

            //Set the subject line
            $mail->Subject = 'Account Verification';

            //Set the body of the message
            $mail->Body = $verifymsg . '<br><a href="'.$verifyurl.'">'.$verifyurl.'</a>';

            $mail->AltBody  =  $verifymsg . $verifyurl;

        } elseif ($type == 'Active') {

            //Set the subject line
            $mail->Subject = $site_name . ' Account Created!';

            //Set the body of the message
            $mail->Body = $active_email . '<br><a href="'.$signin_url.'">'.$signin_url.'</a>';
            $mail->AltBody  =  $active_email . $signin_url;

        } elseif ($type == 'Sent') {
            //Set the subject line
            $mail->Subject = 'You sent some gold!';

            //Set the body of the message
            $mail->Body = '<p>You just sent <b>'.$id.'</b> gold pieces. Log in to check your balance: <br>'.$signin_url.'</p>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'Recieved') {
            //Set the subject line
            $mail->Subject = 'You recieved some gold!';

            //Set the body of the message
            $mail->Body = '<p>You just recieved <b>'.$id.'</b> gold pieces. Log in to check your balance: <br>'.$signin_url.'</p>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'Withdrawal') {
            //Set the subject line
            $mail->Subject = 'Withdrawal request from '.$user;

            //Set the body of the message
            $mail->Body = '<p>There is a request from <b>'.$user.'</b> to withdraw <b>'.$id.'</b> gold pieces from their bank account to their Classcraft account.<br>If you approve this request, please be sure to manually make the change in their Classcraft account.</p><hr><a href="'.$withdrawapproveurl.'">Approve Request</a> // <a href="'.$withdrawrejecturl.'">Reject Request</a>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'Deposit') {
            //Set the subject line
            $mail->Subject = 'Deposit request from '.$user;

            //Set the body of the message
            $mail->Body = '<p>There is a request from <b>'.$user.'</b> to draw <b>'.$id.'</b> gold pieces from their Classcraft account to their bank account.<br>If you approve this request, please be sure to manually make the change in their Classcraft account.</p><hr><a href="'.$depositapproveurl.'">Approve Request</a> // <a href="'.$depositrejecturl.'">Reject Request</a>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'Overdue') {
            //Set the subject line
            $mail->Subject = 'Your loan payback is overdue!';

            //Set the body of the message
            $mail->Body = '<p>It has been two weeks since you took a loan of '.$id.' gold pieces from our bank. Since you were unable to pay the loan back during that period, your character will be killed.<br>In order to revive your character, please pay back the loan at <a href="">/'.$signin_url.'</a> as soon as possible.</p>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'OverdueMention') {
            //Set the subject line
            $mail->Subject = $user.' failed to pay their loan back on time!';

            //Set the body of the message
            $mail->Body = '<p>It has been two weeks since <b>'.$user.'</b> took a loan of '.$id.' GP gold pieces from the Classcraft bank. As such, they must now die. You will recieve an email once the loan has been paid back.';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'LoanPaid') {
            //Set the subject line
            $mail->Subject = 'Your reciept';

            //Set the body of the message
            $mail->Body = '<p>This is digital proof that you paid back your loan of <b>'.$id.'</b> GP.<br>We thank you for using the Classcraft Bank!';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'LoanPaidMention') {
            //Set the subject line
            $mail->Subject = $user.' paid back their loan';

            //Set the body of the message
            $mail->Body = '<b>'.$user.'</b> paid back their loan of <b>'.$id.'</b> GP.<br>They may now be revived.';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'LoanRequest') {
            //Set the subject line
            $mail->Subject = 'Loan request from '.$user;

            //Set the body of the message
            $mail->Body = '<p>There is a loan request from <b>'.$user.'</b> for <b>'.$id.'</b> gold pieces to be deposited to their Classcraft account.<br>If you approve this request, please be sure to manually add the gold to their account.</p><hr><a href="'.$loanapproveurl.'">Approve Request</a> // <a href="'.$loanrejecturl.'">Reject Request</a>';
            $mail->AltBody  =  $signin_url;

        }

        //SMTP Settings
        if ($mailServerType == 'smtp') {

            $mail->IsSMTP(); //Enable SMTP
            $mail->SMTPAuth = true; //SMTP Authentication
            $mail->Host = $smtp_server; //SMTP Host
            //Defaults: Non-Encrypted = 25, SSL = 465, TLS = 587
            $mail->SMTPSecure = $smtp_security; // Sets the prefix to the server
            $mail->Port = $smtp_port; //SMTP Port
            //SMTP user auth
            $mail->Username = $smtp_user; //SMTP Username
            $mail->Password = $smtp_pw; //SMTP Password
            //********************
            $mail->SMTPDebug = 0; //Set to 0 to disable debugging (for production)

        }

        try {

            $mail->Send();

        } catch (phpmailerException $e) {

            echo $e->errorMessage();// Error messages from PHPMailer

        } catch (Exception $e) {

            echo $e->getMessage();// Something else

        }
    }

    public function sendReasonMail($email, $user, $id, $reason, $type)
    {
        require '../scripts/PHPMailer/PHPMailerAutoload.php';
        include '../config.php';

        $finishedtext = $active_email;

        // Create a new PHPMailer object
        // ADD sendmail_path = "env -i /usr/sbin/sendmail -t -i" to php.ini on UNIX servers
        $mail = new PHPMailer;
        $mail->isHTML(true);
        $mail->CharSet = "text/html; charset=UTF-8;";
        $mail->WordWrap = 80;
        $mail->setFrom($from_email, $from_name);
        $mail->AddReplyTo($from_email, $from_name);
        /****
        * Set who the message is to be sent to
        * CAN BE SET TO addAddress(youremail@website.com, 'Your Name') FOR PRIVATE USER APPROVAL BY MODERATOR
        * SET TO addAddress($email, $user) FOR USER SELF-VERIFICATION
        *****/
        $mail->addAddress($email, $user);

        //Sets message body content based on type (verification or confirmation)
        if ($type == 'WithdrawReject') {

            //Set the subject line
            $mail->Subject = 'Your withdrawal request has been rejected';

            //Set the body of the message
            $mail->Body = '<p>Your withdrawal request has been rejected by the administrator for the following reason: <i>'.$reason.'</i><br>Please talk to the administrator if you think this was done in error.</p>';
            $mail->AltBody  =  $signin_url;



        } elseif ($type == 'DepositReject') {

            //Set the subject line
            $mail->Subject = 'Your deposit request has been rejected';

            //Set the body of the message
            $mail->Body = '<p>Your deposit request has been rejected by the administrator for the following reason: <i>'.$reason.'</i><br>Please talk to the administrator if you think this was done in error.</p>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'LoanReject') {

            //Set the subject line
            $mail->Subject = 'Your loan request has been rejected';

            //Set the body of the message
            $mail->Body = '<p>Your loan request has been rejected by the administrator for the following reason: <i>'.$reason.'</i><br>Please talk to the administrator if you think this was done in error.</p>';
            $mail->AltBody  =  $signin_url;

        }

        //SMTP Settings
        if ($mailServerType == 'smtp') {

            $mail->IsSMTP(); //Enable SMTP
            $mail->SMTPAuth = true; //SMTP Authentication
            $mail->Host = $smtp_server; //SMTP Host
            //Defaults: Non-Encrypted = 25, SSL = 465, TLS = 587
            $mail->SMTPSecure = $smtp_security; // Sets the prefix to the server
            $mail->Port = $smtp_port; //SMTP Port
            //SMTP user auth
            $mail->Username = $smtp_user; //SMTP Username
            $mail->Password = $smtp_pw; //SMTP Password
            //********************
            $mail->SMTPDebug = 0; //Set to 0 to disable debugging (for production)

        }

        try {

            $mail->Send();

        } catch (phpmailerException $e) {

            echo $e->errorMessage();// Error messages from PHPMailer

        } catch (Exception $e) {

            echo $e->getMessage();// Something else

        }
    }

public function sendAcceptMail($email, $user, $id, $type)
    {
        require '../scripts/PHPMailer/PHPMailerAutoload.php';
        include '../config.php';

        $finishedtext = $active_email;

        // Create a new PHPMailer object
        // ADD sendmail_path = "env -i /usr/sbin/sendmail -t -i" to php.ini on UNIX servers
        $mail = new PHPMailer;
        $mail->isHTML(true);
        $mail->CharSet = "text/html; charset=UTF-8;";
        $mail->WordWrap = 80;
        $mail->setFrom($from_email, $from_name);
        $mail->AddReplyTo($from_email, $from_name);
        /****
        * Set who the message is to be sent to
        * CAN BE SET TO addAddress(youremail@website.com, 'Your Name') FOR PRIVATE USER APPROVAL BY MODERATOR
        * SET TO addAddress($email, $user) FOR USER SELF-VERIFICATION
        *****/
        $mail->addAddress($email, $user);

        //Sets message body content based on type (verification or confirmation)
        if ($type == 'DepositAccept') {

            //Set the subject line
            $mail->Subject = 'Deposit request accepted!';

            //Set the body of the message
            $mail->Body = '<p>Your request for <b>'.$id.'</b> gold pieces to be drawn from your Classcraft account into your bank account has been accepted.<br>Please note that, while the amount has been added to your bank account, it can take up to an hour for the gold to be deducted from your Classcraft account. Contact the administrator if the gold is not deducted within 24 hours.</p>';
            $mail->AltBody  =  $signin_url;


        } elseif ($type == 'WithdrawalAccept') {
            //Set the subject line
            $mail->Subject = 'Withdrawal request accepted!';

            //Set the body of the message
            $mail->Body = '<p>Your withdrawal request for <b>'.$id.'</b> gold pieces from your bank account has been accepted.<br>Please note that, while the amount has been deducted from your bank account, it can take up to an hour for the gold to appear in your Classcraft account. Contact the administrator if you do not recieve the gold in 24 hours.</p>';
            $mail->AltBody  =  $signin_url;

        } elseif ($type == 'LoanAccept') {
            $interest = $id / 5;
            //Set the subject line
            $mail->Subject = 'Loan request accepted!';

            //Set the body of the message
            $mail->Body = '<p>Your loan request for <b>'.$id.'</b> gold pieces has been accepted. The gold will be deposited into your account.<br>Please note that it can take up to an hour for the gold to appear in your Classcraft account.<br>You must repay the loan (and the interest of '.$interest.' gold pieces) back in <b>2 weeks after this message</b>, or risk the death of your character in-game.</p>';
            $mail->AltBody  =  $signin_url;

        } 

        //SMTP Settings
        if ($mailServerType == 'smtp') {

            $mail->IsSMTP(); //Enable SMTP
            $mail->SMTPAuth = true; //SMTP Authentication
            $mail->Host = $smtp_server; //SMTP Host
            //Defaults: Non-Encrypted = 25, SSL = 465, TLS = 587
            $mail->SMTPSecure = $smtp_security; // Sets the prefix to the server
            $mail->Port = $smtp_port; //SMTP Port
            //SMTP user auth
            $mail->Username = $smtp_user; //SMTP Username
            $mail->Password = $smtp_pw; //SMTP Password
            //********************
            $mail->SMTPDebug = 0; //Set to 0 to disable debugging (for production)

        }

        try {

            $mail->Send();

        } catch (phpmailerException $e) {

            echo $e->errorMessage();// Error messages from PHPMailer

        } catch (Exception $e) {

            echo $e->getMessage();// Something else

        }
    }
}
