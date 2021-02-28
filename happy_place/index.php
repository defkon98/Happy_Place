<?php
    require_once('../inc/db_inc.php');
    require_once('../inc/connect.php');
    include('functions.php');
    session_start();

    if(isset($_POST['logout']))
    {
        session_destroy();
        header("Refresh:0");
        die();
    }

/************************************************** Login for the Page is done ***************************************************************/

    if(isset($_POST['login']))
    {
        loginCheck($db);
    }

/************************************************** Check if user is logged in ***************************************************************/
    $login = alreadyLoggedIn();

/************************************************** Create new Student if logged in ***************************************************************/
    if($login && $_SESSION['auth'])
    {
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
    }

    /*----------------------------------------------- Edit Student ------------------------------------------------------------------ */

    if($login && $_SESSION['auth'])
    {
        if(isset($_POST['submitedit']))
        {
            try
            {
                $query = 'UPDATE tbldude set plz_id = :plz_id, nachname = :nachname, vorname = :vorname where id = :id;';

                $prepStat = $db -> prepare($query);

                $prepStat -> bindParam(':plz_id', $_POST['changeplz']);
                $prepStat -> bindParam(':nachname', $_POST['editnachname']);
                $prepStat -> bindParam(':vorname', $_POST['editvorname']);
                $prepStat -> bindParam(':id', $_POST['dudeid']);

                $prepStat -> execute();
                $prepStat -> errorInfo()[2];
            }
            // Fehler-Behandlung
            catch(PDOException $e){
                // Fehlermeldung ohne Details, wird auch im produktiven Web gezeigt
                echo '<p>Edit fehlgeschlagen! Bitte beschweren Sie sich bei der Steuerbehörde.';
            
                // Detaillierte Fehlermeldung, wird nur auf dem Testserver angezeigt (da, wo display_errors auf on gesetzt ist)
                if(ini_get('display_errors')){
                echo '<br>' . $e->getMessage();
                }
            }
        }
    }

    /*------------------------------------------------ Delete Student ----------------------------------------------------------------*/

    if($login && $_SESSION['auth'])
    {
        if(isset($_POST['delete']))
        {
            try
            {
                $query = 'DELETE FROM tbldude WHERE id = :id;';

                $prepStat = $db -> prepare($query);

                $prepStat -> bindParam(':id', $_POST['dudeid']);

                $prepStat -> execute();
                $prepStat -> errorInfo()[2];
            }
            // Fehler-Behandlung
            catch(PDOException $e){
                // Fehlermeldung ohne Details, wird auch im produktiven Web gezeigt
                echo '<p>Löschen fehlgeschlagen! Bitte beschweren Sie sich bei der Steuerbehörde.';
            
                // Detaillierte Fehlermeldung, wird nur auf dem Testserver angezeigt (da, wo display_errors auf on gesetzt ist)
                if(ini_get('display_errors')){
                echo '<br>' . $e->getMessage();
                }
            }
        }
    }




    /*-------------------------------------------------------------- Neuer Admin erstellen --------------------------------------------------------------*/
    
    if ($login && $_SESSION['auth'])
    {

        if(isset($_POST['newAdmin']))
        {

            if (!empty($_POST['username']) && !empty($_POST['password'])) {

                $pw = password_hash($_POST['password'], PASSWORD_BCRYPT);
                
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

                $prepStat = null;

            }
        }
    }
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
            if($login && $_SESSION['auth'])
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
            if($login && $_SESSION['auth'])
            {
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

                                        dropDownLivingPlace($db);

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
            if($login && $_SESSION['auth'] && $_SESSION['superadmin'])
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

    <?php

    /*--------------------------------------------------------- Show all Students to admin -------------------------------------------------------*/
        if($login && $_SESSION['auth'])
        {
            footerForAdmins($db);
        }
    ?>
</body>
</html>