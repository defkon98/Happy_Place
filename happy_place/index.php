<?php
    require_once('../inc/db_inc.php');
    require_once('../inc/connect.php');
    session_start();

    if(isset($_POST['logout']))
    {
        session_destroy();
        header("Refresh:0");
        die();
    }

    if(isset($_SESSION['auth']) && $_SESSION['auth'])
    {
        $login = 1;
    }
    else
    {
        $login = 0;
        if(!isset($_POST['login'])) $db = null;
    }

    /*-------------------------------------------- New Student -------------------------------------------------------------------- */
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
            $query = 'UPDATE tbldude set plz_id = :plz_id, nachname = :nachname, vorname = :vorname where id = :id;';

            $prepStat = $db -> prepare($query);

            $prepStat -> bindParam(':plz_id', $_POST['changeplz']);
            $prepStat -> bindParam(':nachname', $_POST['editnachname']);
            $prepStat -> bindParam(':vorname', $_POST['editvorname']);
            $prepStat -> bindParam(':id', $_POST['dudeid']);

            $prepStat -> execute();
            $prepStat -> errorInfo()[2];
        }
    }

    /*------------------------------------------------ Delete Student ----------------------------------------------------------------*/

    if($login && $_SESSION['auth'])
    {
        if(isset($_POST['delete']))
        {
            $query = 'DELETE FROM tbldude WHERE id = :id;';

            $prepStat = $db -> prepare($query);

            $prepStat -> bindParam(':id', $_POST['dudeid']);

            $prepStat -> execute();
            $prepStat -> errorInfo()[2];

        }
    }


    /*----------------------------------------- Login for the Page is done -------------------------------------------------------- */
    if(isset($_POST['login']))
    {
        $login = 1;

        if(!empty($_POST['username']) && !empty($_POST['password']))
        {

            //$pw = password_hash($_POST['password'], PASSWORD_BCRYPT);
       
            $query = "SELECT * FROM tbladmin WHERE username = :username";

            $prepStat = $db -> prepare($query);

            $prepStat -> bindParam(':username', $_POST['username']);

            if($prepStat -> execute())
            {
                $result = $prepStat -> fetch();

                $vorname = $result['vorname'];
                $nachname = $result['nachname'];
                $_SESSION['auth'] = password_verify($_POST['password'], $result['password']);

                if($_SESSION['auth'])
                {
                    $_SESSION['superadmin'] = $result['superadmin'];

                    echo 'welcome ' . $vorname . ' ' . $nachname;
                }
                else
                {
                    echo 'Logindaten sind falsch';
                }

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

                $query = 'SELECT * from tblplz order by ort;';

                $result = $db -> query($query);
                $resultAllOrte = $result -> fetchAll();

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

                                        foreach ($resultAllOrte as $row) {
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
        if($login && $_SESSION['auth'])
        {
            ?>
                <div class="edit_students">

                    <fieldset>
                        <legend>Schüler bearbeiten oder löschen</legend>
                        
                        <?php


                            // $resultAllOrte is needed here for the dropdown

                            $query = 'SELECT dude.id as id, dude.vorname as vorname, dude.nachname as nachname, dude.plz_id as dudeplz, ort.ort as ort, ort.plz as plz from tbldude as dude join tblplz as ort where dude.plz_id = ort.plz_id order by nachname';
                            
                            $result = $db -> query($query);
                            $resultAll = $result -> fetchAll();

                            $db = null;

                            foreach($resultAll as $row)
                            {    
                                echo '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';

                                if(isset($_POST['edit']))
                                {
                                    if($_POST['id'] == $row['id'])
                                    {
                                        echo '<input type="text" name="editnachname" value="' . $row['nachname'] . '"><input type="text" name="editvorname" value="' . $row['vorname'] . '"> ';
                                        echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                        echo '<select name="changeplz">';

                                        foreach ($resultAllOrte as $rowOrt) {

                                            if($row['dudeplz'] == $rowOrt['plz_id'])
                                            {
                                                echo '<option value="' . $rowOrt['plz_id'] . '" selected="selected">' . $rowOrt['ort'] . ', ' . $rowOrt['plz'] . '</option>';
                                            }
                                            else
                                            {
                                                echo '<option value="' . $rowOrt['plz_id'] . '">' . $rowOrt['ort'] . ', ' . $rowOrt['plz'] . '</option>';
                                            }
                                           
                                        }
                                        
                                        echo '</select>';
                                        echo '<input type="submit" name="submitedit" value="speichern">';
                                        echo '</form>';
                                    }
                                    else
                                    {
                                        echo  $row['nachname'] . ' ' . $row['vorname'] . ' ' . $row['plz'] . ' - ' . $row['ort'];
                                        echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                        echo '<input type="submit" name="edit" value="edit">  <input type="submit" name="delete" value="delete">';
                                            
                                        echo '</form>';
                                    }
                                }
                                else
                                {
                                    echo $row['nachname'] . ' ' . $row['vorname'] . ' ' . $row['plz'] . ' - ' . $row['ort'];
                                    echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                    echo '<input type="submit" name="edit" value="edit">  <input type="submit" name="delete" value="delete">';
                                        
                                    echo '</form>';
                                }
                            }
                    
                    echo '</fieldset>';
                echo '</div>';
        }
    ?>
</body>
</html>