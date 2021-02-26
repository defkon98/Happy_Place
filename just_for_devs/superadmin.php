<?php

    require_once('../inc/db_inc.php');
    require_once('../inc/connect.php');


    if(isset($_POST['submit']))
    {
        $email = htmlspecialchars($_POST['email']);
        $vorname = htmlspecialchars($_POST['vorname']);
        $nachname = htmlspecialchars($_POST['nachname']);
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);


        $hash_salt = '$6$rounds=5000$ijustwanttobed$';
        $password = crypt($password, $hash_salt); 

        $query = 'insert into tbladmin (email, vorname, nachname, username, password, superadmin) values (:email, :name, :nachname, :username, :password, 1);'; 

        $prepStat = $db -> prepare($query);

        $prepStat -> bindParam(':email', $email);
        $prepStat -> bindParam(':name', $vorname);
        $prepStat -> bindParam(':nachname', $nachname);
        $prepStat -> bindParam(':username', $username);
        $prepStat -> bindParam(':password', $password);


        $result = $prepStat -> execute(); 

        if(!$result)
        {
            die('Query failed' . $prepStat -> errorinfo()[2]);
        }
        else
        {
            echo 'Superadmin erfolgreich erstellt';
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <fieldset>
            <legend>neuen Superadmin erfassen</legend>
            <label>E-Mail
                <input type="text" name="email"><br><br>
            </label>
            <label>vorname
                <input type="text" name="vorname"><br><br>
            </label>
            <label>nachname
                <input type="text" name="nachname"><br><br>
            </label>
            <label>Username
                <input type="text" name="username"><br><br>
            </label>
            <label>Password
                <input type="password" name="password"><br><br>
            </label>
                <input type="submit" name="submit" value="create">
        </fieldset>
    </form>
    
</body>
</html>