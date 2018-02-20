<?php
class NewUserForm extends DbConn
{
    public function createUser($usr, $uid, $email, $pw)
    {
        try {

            $balance = 0;
            $loan = 0;
            $db = new DbConn;
            $tbl_members = $db->tbl_members;
            $tbl_balance = $db->tbl_balance;
            // prepare sql and bind parameters
            $stmt = $db->conn->prepare("INSERT INTO ".$tbl_members." (id, username, password, email)
            VALUES (:id, :username, :password, :email)");
            $stmt->bindParam(':id', $uid);
            $stmt->bindParam(':username', $usr);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $pw);
            $stmt->execute();

            $bdb = new DbConn;
            $btmt = $bdb->conn->prepare("INSERT INTO ".$tbl_balance." (username, userBalance, userLoans)
            VALUES (:username, :userBalance, :userLoans)");
            $btmt->bindParam(':username', $usr);
            $btmt->bindParam(':userBalance', $balance);
            $btmt->bindParam(':userLoans', $loan);
            $btmt->execute();
            $err = '';

        } catch (PDOException $e) {

            $err = "Error: " . $e->getMessage();

        }
        //Determines returned value ('true' or error code)
        if ($err == '') {

            $success = 'true';

        } else {

            $success = $err;

        };

        return $success;

    }
}
