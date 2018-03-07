<?php

//Class Autoloader
spl_autoload_register(function ($className) {

    $className = strtolower($className);
    $path = "includes/{$className}.php";

    if (file_exists($path)) {

        require_once($path);

    } else {

        die("The file {$className}.php could not be found.");

    }
});

function checkAttempts($username)
{

    try {

        $db = new DbConn;
        $conf = new GlobalConf;
        $tbl_attempts = $db->tbl_attempts;
        $ip_address = $conf->ip_address;
        $err = '';

        $sql = "SELECT Attempts as attempts, lastlogin FROM ".$tbl_attempts." WHERE IP = :ip and Username = :username";

        $stmt = $db->conn->prepare($sql);
        $stmt->bindParam(':ip', $ip_address);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;

        $oldTime = strtotime($result['lastlogin']);
        $newTime = strtotime($datetimeNow);
        $timeDiff = $newTime - $oldTime;

    } catch (PDOException $e) {

        $err = "Error: " . $e->getMessage();

    }

    //Determines returned value ('true' or error code)
    $resp = ($err == '') ? 'true' : $err;

    return $resp;

};

function userInfo($usr)
    {
        try {
            $db = new DbConn;
            $tbl_members = $db->tbl_members;

            $stmt = $db->conn->prepare("SELECT * FROM ".$tbl_members." WHERE username = :myusr");
            $stmt->bindParam(':myusr', $usr);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {

            $result = "Error: " . $e->getMessage();

        }


    }

function userInfoEmail($email)
    {
        try {
            $db = new DbConn;
            $tbl_members = $db->tbl_members;

            $stmt = $db->conn->prepare("SELECT * FROM ".$tbl_members." WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {

            $result = "Error: " . $e->getMessage();

        }


    }

function checkBalance($usr)
    {
        try {
            $db = new DbConn;
            $tbl_balance = $db->tbl_balance;

            $stmt = $db->conn->prepare("SELECT * FROM ".$tbl_balance." WHERE username = :myusr");
            $stmt->bindParam(':myusr', $usr);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {

            $result = "Error: " . $e->getMessage();

        }

    }

function logAction($action, $username, $notes) {

    $date = date('Y/m/d H:i:s');

    $bdb = new DbConn;
    $btmt = $bdb->conn->prepare("INSERT INTO userLog (action, username, date, notes)
    VALUES (:action, :username, :date, :notes)");
    $btmt->bindParam(':action', $action);
    $btmt->bindParam(':username', $username);
    $btmt->bindParam(':date', $date);
    $btmt->bindParam(':notes', $notes);
    $btmt->execute();

}


function sendMoney($username, $email, $amount)
    {

        try {
            $balance = checkBalance($username);
            $usr = userInfoEmail($email);
            $type = gettype($amount);
            $db = new DbConn;
            $tbl_balance = $db->tbl_balance;
            $tbl_members = $db->tbl_members;

            $stmt = $db->conn->prepare("SELECT * FROM ".$tbl_balance." WHERE username = :myusr");
            $stmt->bindParam(':myusr', $usr['username']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $vdb = new DbConn;
            $vstmt = $vdb->conn->prepare("SELECT * FROM ".$tbl_members." WHERE email = :email");
            $vstmt->bindParam(':email', $email);
            $vstmt->execute();
            $row_count = $stmt->rowCount();
            
            if ($balance['userBalance'] - $balance['pendingOut'] < $amount){
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You don't have enough in your balance to make that transaction!</div>";
            } elseif ($amount <= 0) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please enter an amount greater than 0.</div>";
            } elseif ($row_count == 0){
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>The specified user could not be found.</div>";
            } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)){
                $finalamount = $result['userBalance'] + $amount;
                $useramount = $balance['userBalance'] - $amount;

                $bdb = new DbConn;
                $bstmt = $bdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
                $bstmt->bindParam(':balance', $finalamount);
                $bstmt->bindParam(':myusr', $usr['username']);
                $bstmt->execute();

                $rdb = new DbConn;
                $rstmt = $rdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
                $rstmt->bindParam(':balance', $useramount);
                $rstmt->bindParam(':myusr', $username);
                $rstmt->execute();

                $edb = new DbConn;
                $estmt = $edb->conn->prepare("SELECT * FROM ".$tbl_members." WHERE username = :username");
                $estmt->bindParam(':username', $username);
                $estmt->execute();
                $eresult = $estmt->fetch(PDO::FETCH_ASSOC);

                $r = new MailSender;
                $r->sendMail($email, $usr['username'], $amount, 'Recieved');

                $success = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Transaction success!</div>";

                logAction("SEND", $username, $username." just sent ".$usr['username']." ".$amount." GP.");

            } else {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please enter a valid email address.</div>";
            } 

            return $success;
            


        } catch (PDOException $e) {

            $success = "Error: " . $e->getMessage();

            return $success;

        }


    }   

function withdrawrequest($username, $amount){
        try {
			
			require 'config.php';
            $balance = checkBalance($username);
            $type = gettype($amount);

            if ($amount < 1) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please enter an amount of at least 1.</div>";
            } elseif ($amount > $balance['userBalance']) {
                 $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You do not have enough in your account for that transaction.</div>";
            } elseif ($balance['pendingOut'] > 0) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You already have a pending withdrawal.</div>"; 
            } else {   
                $bdb = new DbConn;
                $bstmt = $bdb->conn->prepare("UPDATE balance SET pendingOut = :pending WHERE username = :myusr");
                $bstmt->bindParam(':pending', $amount);
                $bstmt->bindParam(':myusr', $username);
                $bstmt->execute();

                $r = new MailSender;
                $r->sendMail($teacher_email, $username, $amount, 'Withdrawal');

                logAction("WITHDRAW ATTEMPT", $username, $username." is attempting to withdraw ".$amount." from their bank account.");

                $success = "<div class=\"alert alert-info alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Withdrawal request sent to administrator.</div>";

            }

            return $success;
            


        } catch (PDOException $e) {

            $success = "Error: " . $e->getMessage();

            return $success;

        }
}     

function depositrequest($username, $amount){
        try {
			
			require 'config.php';
            $balance = checkBalance($username);
            $type = gettype($amount);

            if ($amount < 1) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please enter an amount of at least 1.</div>";
            } elseif ($amount > 1000) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Please enter an amount under 1000.</div>";
            } elseif ($balance['pendingIn'] > 0) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You already have a pending deposit.</div>"; 
            } else {
                $bdb = new DbConn;
                $bstmt = $bdb->conn->prepare("UPDATE balance SET pendingIn = :pending WHERE username = :myusr");
                $bstmt->bindParam(':pending', $amount);
                $bstmt->bindParam(':myusr', $username);
                $bstmt->execute();

                $r = new MailSender;
                $r->sendMail($teacher_email, $username, $amount, 'Deposit');

                logAction("DEPOSIT ATTEMPT", $username, $username." is attempting to deposit ".$amount." from their Classcraft account.");

                $success = "<div class=\"alert alert-info alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Deposit request sent to administrator.</div>";

            }

            return $success;
            


        } catch (PDOException $e) {

            $success = "Error: " . $e->getMessage();

            return $success;

        }
}

function loanRequest($username, $amount, $reason){

    try {
	
	require 'config.php';
     $balance = checkBalance($username);
     $bank = checkBalance("bank");
            if ($amount < 1) {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>Please enter an amount greater than or equal to 1.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";
            } elseif ($amount > 500) {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>Please enter an amount less than or equal to 500.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";
            } elseif (($bank['userBalance'] - $amount) < 1) {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>The bank cannot process your loan at this time due to insufficient funds. Please try again later.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";
            } elseif ($balance['userLoans'] > 0) {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>You already have a pending loan request.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\" onclick=\"location.reload();\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>"; 
            } elseif ($reason == "") {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>Please enter a reason.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>"; 
        } else {

            $db = new DbConn;
            $stmt = $db->conn->prepare("UPDATE balance SET userLoans = :pending WHERE username = :myusr");
            $stmt->bindParam(':pending', $amount);
            $stmt->bindParam(':myusr', $username);
            $stmt->execute();

            logAction("LOAN REQUEST", $username, $username." just requested a loan of ".$amount." GP for the reason: ".$reason);

            $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
              <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Request sent</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>Your loan request for ".$amount." GP has been submitted. The administrator will send you an email once it has been approved/rejected.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\" onclick=\"location.reload();\">Return to loan center</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";

            $r = new MailSender;
            $r->sendMail($teacher_email, $username, $amount, 'LoanRequest');

            }

            return $success;

                } catch (PDOException $e) {

                    $success = "Error: " . $e->getMessage();

                    return $success;

        }

}     

function interest(){

    try {

    $db = new DbConn;
    $stmt = $db->conn->prepare("SELECT interest FROM activeLoans WHERE username = 'bank'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['interest'] < 100) {
                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>The total interest amount is less than 100 GP.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to admin panel</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";
            } else {
        
                $bank = checkBalance("bank");
                $finalbank = $bank['userBalance'] + $result['interest'];

                $amount = '';
                $bdb = new DbConn;
                $bstmt = $bdb->conn->prepare("SELECT * FROM balance WHERE username != 'bank'");
                $bstmt->setFetchMode(PDO::FETCH_OBJ);
                if($bstmt->execute() <> 0)
                  {

                    while($balances = $bstmt->fetch()) // loop and display data
                      {

                          $amount = $amount + $balances->userBalance;
                          
                      }

                  }

                $edb = new DbConn;
                $kdb = new DbConn;
                $kstmt = $kdb->conn->prepare("SELECT * FROM balance WHERE username != 'bank'");
                $kstmt->setFetchMode(PDO::FETCH_OBJ);
                if($kstmt->execute() <> 0)
                  {

                    while($balance = $kstmt->fetch()) // loop and display data
                      {


                              $final = $balance->userBalance / $amount;
                              $preinterest = $result['interest'] * $final;
                              $finalinterest = round($preinterest);
                              $finalamount = $balance->userBalance + $finalinterest;

                            try {

                                $estmt = $edb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = :myusr");
                                $estmt->bindParam(':balance', $finalamount);
                                $estmt->bindParam(':myusr', $balance->username);
                                $estmt->execute();

                            } catch (PDOException $e) {

                                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                                <div class=\"modal-dialog\" role=\"document\">
                                <div class=\"modal-content\">
                                  <div class=\"modal-header\">
                                    <h5 class=\"modal-title\">Error</h5>
                                  </div>
                                  <div class=\"modal-body\">
                                    <form>
                                      <div class=\"form-group\">
                                        
                                      </div>
                                        <h4>Error occured during interest distribution: ". $e->getMessage() ."</h4>
                                  </div>
                                  <div class=\"modal-footer\">
                                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to admin panel</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>";

                            return $success;

                            }

                              
                          
                      }

                  }

                $ldb = new DbConn;
                $lstmt = $ldb->conn->prepare("UPDATE activeLoans SET interest = 0 WHERE username = 'bank'");
                $lstmt->execute();

                $fdb = new DbConn;
                $fstmt = $fdb->conn->prepare("UPDATE balance SET userBalance = :balance WHERE username = 'bank'");
                $fstmt->bindParam(':balance', $finalbank);
                $fstmt->execute();


                $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
              <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Interest distributed</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>The full amount of interest distributed was ".$result['interest']." GP.</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\" onclick=\"location.reload();\">Return to admin panel</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";

            logAction("INTEREST", "SYSTEM", $result['interest']." GP of interest was distributed.");
            
            }


            return $success;

                } catch (PDOException $e) {

                    $success = "<div class=\"modal fade\" id=\"sent\" role=\"dialog\" aria-hidden=\"false\">
                <div class=\"modal-dialog\" role=\"document\">
                <div class=\"modal-content\">
                  <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Error</h5>
                  </div>
                  <div class=\"modal-body\">
                    <form>
                      <div class=\"form-group\">
                        
                      </div>
                        <h4>Error occured during interest distribution: ". $e->getMessage() ."</h4>
                  </div>
                  <div class=\"modal-footer\">
                    <button class=\"btn btn-default btn-lg btn-block\" data-dismiss=\"modal\">Return to admin panel</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>";

            return $success;

        }

}     

function loanpay($username){
        try {
            $balance = checkBalance($username);
            $bank = "bank";
            $email = userInfo($username);
            $bankbalance = checkBalance($bank);

            $db = new DbConn;
            $stmt = $db->conn->prepare("SELECT * FROM activeLoans WHERE username = :myusr");
            $stmt->bindParam(':myusr', $username);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $row_count = $stmt->rowCount();

            $sdb = new DbConn;
            $sstmt = $sdb->conn->prepare("SELECT interest FROM activeLoans WHERE username = :myusr");
            $sstmt->bindParam(':myusr', $bank);
            $sstmt->execute();
            $sresult = $sstmt->fetch(PDO::FETCH_ASSOC);

            $finalinterest = $result['interest'] + $sresult['interest'];
            $finalamount = $bankbalance['userBalance'] - $result['interest'];

            $final = $result['amount'] + $result['interest'];

            if ($row_count == 0) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Error: loan not found in database records. Please contact the administrator.</div>";
            } elseif ($balance['userBalance'] < $final) {
                 $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You do not have enough in your balance to pay back the loan.</div>";
            } elseif ($balance['pendingOut'] > 0) {
                $success = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>You have a pending withdrawal. You can't pay back your loan until the transaction has been processed.</div>"; 
            } else {   

                $now = time();
                $seconddate = strtotime($result['date']);
                $datediff2 = $now - $seconddate;
                $datediff1 = floor($datediff2 / (60 * 60 * 24));
                $datediff = 14 - $datediff1;

                $new = $balance['userBalance'] - $final;
                $bdb = new DbConn;
                $bstmt = $bdb->conn->prepare("UPDATE balance SET userBalance = :new WHERE username = :myusr");
                $bstmt->bindParam(':new', $new);
                $bstmt->bindParam(':myusr', $username);
                $bstmt->execute();

                $rdb = new DbConn;
                $rstmt = $rdb->conn->prepare("UPDATE balance SET userBalance = :new WHERE username = :myusr");
                $rstmt->bindParam(':new', $finalamount);
                $rstmt->bindParam(':myusr', $bank);
                $rstmt->execute();

                $edb = new DbConn;
                $estmt = $edb->conn->prepare("DELETE FROM activeLoans WHERE username = :myusr");
                $estmt->bindParam(':myusr', $username);
                $estmt->execute();

                $ldb = new DbConn;
                $lstmt = $ldb->conn->prepare("UPDATE activeLoans SET interest = :new WHERE username = :myusr");
                $lstmt->bindParam(':new', $finalinterest);
                $lstmt->bindParam(':myusr', $bank);
                $lstmt->execute();

                $r = new MailSender;
                $r->sendMail($email['email'], $username, $result['amount'], 'LoanPaid');

                if ($datediff <= 0) {
                    $m = new MailSender;
                    $m->sendMail($teacher_email, $username, $result['amount'], 'LoanPaidMention');
                }

                logAction("LOAN PAID", $username, $username." paid back their loan of ".$result['amount']." with an interest of ".$result['interest']." GP.");

                $success = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Your loan has been successfully paid. Please reload the page for the results to finalize.</div>";

            }

            return $success;
            


        } catch (PDOException $e) {

            $success = "Error: " . $e->getMessage();

            return $success;

        }
}     

function checkLoans($username) {
    $db = new DbConn;
    $stmt = $db->conn->prepare("SELECT * FROM activeLoans WHERE username = :myusr");
    $stmt->bindParam(':myusr', $username);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count == 0) {
        return false;
    } else {
        return true;
    }
}

function checkInterest() {
    $username = "bank";
    $db = new DbConn;
    $stmt = $db->conn->prepare("SELECT interest FROM activeLoans WHERE username = :myusr");
    $stmt->bindParam(':myusr', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}

function mySqlErrors($response)
{
    //Returns custom error messages instead of MySQL errors
    switch (substr($response, 0, 22)) {

        case 'Error: SQLSTATE[23000]':
            echo "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Username or email already exists</div>";
            break;

        default:
            echo "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>An error occurred... try again</div>";

    }
};
