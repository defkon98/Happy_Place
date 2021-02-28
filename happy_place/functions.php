<?php
/******************************** Check the login stat **********************************************************/
//Checks if user is logged in or not.

    function alreadyLoggedIn()
    {
        //When session is set and the authentification true is, user is an Admin an logged in
        if(isset($_SESSION['auth']) && $_SESSION['auth'])
        {
            $login = 1;
        }
        else
        {
            //Else user isnt logged in, and means hes not an admin
            $login = 0;
            if(!isset($_POST['login'])) $db = null;
        }

        return $login;
    }

/******************************** Check login params **********************************************************/
//Checks if the user who made a try to login, is really an admin

    function loginCheck($db)
    {

        //Check if user filled the form
        if(!empty($_POST['username']) && !empty($_POST['password']))
        {
    
            //Simple query, to search the user in the database
            $query = "SELECT * FROM tbladmin WHERE username = :username";

            $prepStat = $db -> prepare($query);

            $prepStat -> bindParam(':username', $_POST['username']);

            //If user is found lets go and check the password
            if($prepStat -> execute())
            {
                $result = $prepStat -> fetch();

                if ($result !== false)
                {
                    //Save the name of the user (for personal greeting), verify password and safe the verification in the session
                    $vorname = $result['vorname'];
                    $nachname = $result['nachname'];
                    $_SESSION['auth'] = password_verify($_POST['password'], $result['password']);

                    //If all went good, user is logged in and gets a greeting
                    if($_SESSION['auth'])
                    {
                        $_SESSION['superadmin'] = $result['superadmin'];

                        echo 'welcome ' . $vorname . ' ' . $nachname;
                    }
                    else
                    {
                        //When the password isnt correct, say it to the user
                        echo 'Logindaten sind falsch';
                    }
                }
                else
                {
                    //When the password isnt correct, say it to the user
                    echo 'Logindaten sind falsch';
                }
            }
            else
            {
                //When the username isnt correct, say it to the user
                echo 'Logindaten sind Falsch';
            }
        }
        else
        {
            //In case, if the user tries to login without a username or without a password
            echo 'Bitte Passwort und Benutzername eingeben';
        }
    }


/******************************** Dropdown for students livin-place **********************************************************/
//Just use in an <select> html stmt, to get the full dropdown

    function dropDownLivingPlace($db)
    {
        //A little query to select all living-places from the database (just switzerland) and fetch them into $resultAllOrte
        $query = 'SELECT * from tblplz order by ort;';

        $result = $db -> query($query);
        $resultAllOrte = $result -> fetchAll();

        //Make for every living-place an option
        foreach ($resultAllOrte as $row) {
            echo '<option value="' . $row['plz_id'] . '">' . $row['ort'] . ', ' . $row['plz'] . '</option>';
        }
    }


/******************************** Admin-footer **********************************************************/
//Special form included dropdown for the footer of admins to delete and edit the Students and have an overview

    function footerForAdmins($db)
    {
        
        ?>
            <div class="edit_students">
                
                <fieldset>
                    <legend>Schüler bearbeiten oder löschen</legend>
                    
                    <?php

                        //Query to select every information we need for the footer
                        $query = 'SELECT dude.id as id, dude.vorname as vorname, dude.nachname as nachname, dude.plz_id as dudeplz, ort.ort as ort, ort.plz as plz from tbldude as dude join tblplz as ort where dude.plz_id = ort.plz_id order by nachname';
                        
                        $result = $db -> query($query);
                        $resultAll = $result -> fetchAll();
                        //Lets make a form, for every student we have
                        foreach($resultAll as $row)
                        {    
                            echo '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';

                            //When the admin wants to edit a student, theres gona be a form with text and a select of living-places
                            if(isset($_POST['edit']))
                            {
                                if($_POST['dudeid'] == $row['id'])
                                {
                                    echo '<input type="text" name="editnachname" value="' . $row['nachname'] . '"><input type="text" name="editvorname" value="' . $row['vorname'] . '"> ';
                                    echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                    echo '<select name="changeplz">';

                                    //This just works, because its initialized in the function before
                                    // -NOPE- sadly, this does NOT work, because $resultAllOrte is scoped to the other function. We could execute the query outside of the function.
                                    // for now, I'm just redoing the query - unelegant, but safe
                                    $query = 'SELECT * from tblplz order by ort;';

                                    $result = $db -> query($query);
                                    $resultAllOrte = $result -> fetchAll();
                                    $db = null;

                                    foreach ($resultAllOrte as $rowOrt) {
                                        //We need here $resultAllOrte and not the return of the query in this function, because the query in this function wont give us every single place

                                        if($row['dudeplz'] == $rowOrt['plz_id'])
                                        {
                                            echo '<option value="' . $rowOrt['plz_id'] . '" selected="selected">' . $rowOrt['ort'] . ', ' . $rowOrt['plz'] . '</option>';
                                        }
                                        else
                                        {
                                            echo '<option value="' . $rowOrt['plz_id'] . '">' . $rowOrt['ort'] . ', ' . $rowOrt['plz'] . '</option>';
                                        }
                                        
                                    }
                                    
                                    //Select and form ending for the one student the admin wants to edit
                                    echo '</select>';
                                    echo '<input type="submit" name="submitedit" value="speichern">';
                                    echo '</form>';
                                }
                                else
                                {
                                    //Form for students not meant to be edited right now
                                    echo  $row['nachname'] . ' ' . $row['vorname'] . ' ' . $row['plz'] . ' - ' . $row['ort'];
                                    echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                    echo '<input type="submit" name="edit" value="edit">  <input type="submit" name="delete" value="delete">';
                                    echo '</form>';
                                }
                            }
                            else
                            {
                                //Form to show all students, when edit isn't clicked
                                echo $row['nachname'] . ' ' . $row['vorname'] . ' ' . $row['plz'] . ' - ' . $row['ort'];
                                echo '<input type="hidden" name="dudeid" value="' . $row['id'] . '">';
                                echo '<input type="submit" name="edit" value="edit">  <input type="submit" name="delete" value="delete">';
                                echo '</form>';
                            }
                        }
                            echo '</fieldset>';
                        echo '</div>';      
                    ?>
                </fieldset>
            </div>
        <?php
    }


?>