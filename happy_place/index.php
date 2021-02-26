<?php
    require_once('../inc/db_inc.php');
    require_once('../inc/connect.php');
    session_start();

    $login = 0;


    if(isset($_POST['logout']))
    {
        //setcookie('superadmin', -1, time() - 3600);
        session_destroy();
    }

    /*-------------------------------------------- New Student -------------------------------------------------------------------- */
    if(isset($_POST['submit']))
    {
        $query = 'INSERT INTO tbldude (plz_id, nachname, vorname) VALUES (:PLZ, :nachname, :vorname)';

        $prepStat = $db -> prepare($query);

        $prepStat -> bindParam(':PLZ', $_POST['PLZ']);
        $prepStat -> bindParam(':nachname', $_POST['nachname']);
        $prepStat -> bindParam(':vorname', $_POST['vorname']);

        $prepStat -> execute();
        $prepStat -> errorInfo()[2];
    }

    /*----------------------------------------- Login for the Page is done -------------------------------------------------------- */
    if(isset($_POST['login']))
    {
        $login = 1;

        if(!empty($_POST['username']) && !empty($_POST['password']))
        {

            $pw = crypt($_POST['password'], $hash_salt);
       
            $query = "SELECT * FROM tbladmin WHERE username = :username AND password = :password";

            $prepStat = $db -> prepare($query);

            $prepStat -> bindParam(':username', $_POST['username']);
            $prepStat -> bindParam(':password', $pw);

            if($prepStat -> execute())
            {
                $result = $prepStat -> fetch();

                $vorname = $result['vorname'];
                $nachname = $result['nachname'];
                $_SESSION['superadmin'] = $result['superadmin'];

                echo 'welcome ' . $vorname . ' ' . $nachname;
            }
            else
            {
                echo 'Logindaten sind Falsch';
            }
        }
        else
        {
            echo 'Bitte Passwort und Benutzername eingeben';
        }
    }

    /*-------------------------------------------------------------- Neuer Admin erstellen --------------------------------------------------------------*/
    
    if (isset($_SESSION['superadmin']))
    {


        if(isset($_POST['newAdmin']))
        {

            if (!empty($_POST['username']) && !empty($_POST['password'])) {

                $pw = crypt($_POST['password'], $hash_salt);
                
                $query = 'INSERT INTO tbladmin (email, vorname, nachname, username, password) VALUES (:email, :vorname, :nachname, :username, :password);';

                $prepStat = $db -> prepare($query);

                $prepStat -> bindParam(':email', $_POST['email']);
                $prepStat -> bindParam(':vorname', $_POST['vorname']);
                $prepStat -> bindParam(':nachname', $_POST['nachname']);
                $prepStat -> bindParam(':username', $_POST['username']);
                $prepStat -> bindParam(':password', $pw);

                if($prepStat -> execute())
                {
                    echo 'Admin erfolgreich erfasst';
                }
                else
                {
                    echo 'Smt went wrong';
                }
            }
        }
    }

    echo $_SESSION['superadmin'];
?>

<!--****************************************** HTML-Start **********************************************************************************************-->
<!DOCTYPE html>
<html lang="en">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="zli_logo.png" type="image/png"/>
    <link rel="stylesheet" href="stylesheet.css">
    <title>Happy Place</title>

    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script type="text/javascript" src="map.js"></script>

</head>
<body>

    <div class="all">

  <!-- ************************************* Logout Button ********************************************************************* -->       
        <?php 
            if(isset($_SESSION['superadmin']))
            {
                ?>
              
                <div class="formulaLogout">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input class="logout" type="submit" name="logout" value="logout">
                    </form>
                </div>
                <?php  
            }
        
            /*---------------------------------------------- Information for Formular für new Schüler ----------------------------------------------*/
            if(isset($_SESSION['superadmin']))
            {

                $query = 'SELECT * from tblplz order by ort;';

                $result = $db -> query($query);
                $resultAll = $result -> fetchAll();

                ?>



                <!--************************************* Neuer Schüler erfassen **************************************************************-->
                <div class="formula">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

                        <fieldset>
                            <legend>Erfassen</legend>

                            <label>Vorname des Schülers
                                <input type="text" name="vorname"> 
                            </label>

                            <br><br>

                            <label>Nachname des Schülers
                                <input type="text" name="nachname"> 
                            </label>

                            <br><br>

                            <label>PLZ des Wohnortes des Schülers
                                <select name="PLZ">
                                    <option value=""></option>
                                    <?php 

                                        foreach ($resultAll as $row) {
                                            echo '<option value="' . $row['plz_id'] . '">' . $row['ort'] . ', ' . $row['plz'] . '</option>';
                                        }

                                    ?>
                                </select>
                            </label>

                            <br><br>

                            <input type="submit" name="submit" value="Erfassen">

                        </fieldset>

                    </form>
                </div>
                <?php
            }

            /*------------------------------------ bei logout ----------------------------------------------------------------------------------------------- */

            if(isset($_POST['logout']))
            {
                echo 'Auf Wiedersehen';
            }

            /*--------------------------------------------------- Neuen Admin erstellen (nur für superadmins) ------------------------------------------------------------------------------*/
            if(isset($_SESSION['superadmin']) && $_SESSION['superadmin'])
            {
                ?>

                <div class="formula">
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">

                        <fieldset>

                            <legend>neuen admin erfassen</legend>

                            <label>E-Mail
                                <input type="text" name="email">
                            </label>

                            <br><br>

                            <label>vorname
                                <input type="text" name="vorname">
                            </label>

                            <br><br>

                            <label>nachname
                                <input type="text" name="nachname">
                            </label>

                            <br><br>

                            <label>Username
                                <input type="text" name="username">
                            </label>

                            <br><br>

                            <label>Password
                                <input type="password" name="password">
                            </label>

                            <br><br>

                            <input type="submit" name="newAdmin" value="create">

                        </fieldset>
                    </form>
                </div>

                <?php
            }

        ?>

        <!-- ******************************************* JS-Map ************************************************************************** -->
        <div class="map" id="map">
            <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD28_yO36kBFTkloCHe6Mtdth7EzZEXjiU&callback=initMap&libraries=&v=weekly"
            async
            ></script>
        </div>

    </div>
    
</body>
</html>