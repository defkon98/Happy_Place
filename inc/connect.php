<?php
    /*
    Mit dem Datenbank-Server verbinden, Datenbank wählen und Zeichensatz definieren
    - mit Fehlerbehandlung

    17.01.2018, Pius Senn
    */

    // Verbindung zur Datenbank aufbauen
    try{
    $dsn = 'mysql:host=' . $host . ';dbname=' . $database;
    $db = new PDO($dsn, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }
    // Fehler-Behandlung
    catch(PDOException $e){
    // Fehlermeldung ohne Details, wird auch im produktiven Web gezeigt
    echo '<p>Verbindung fehlgeschlagen!';

    // Detaillierte Fehlermeldung, wird nur auf dem Testserver angezeigt (da, wo display_errors auf on gesetzt ist)
    if(ini_get('display_errors')){
    echo '<br>' . $e->getMessage();
    }

    // Ausführung des Scripts beenden
    exit;
    }
        //FÜR DIE LB VERWENDEN EINFACH UM SICHER ZU SEIN!!!
    //$db-> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //$db-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>