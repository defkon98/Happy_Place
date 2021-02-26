<?php

    require_once('../inc/db_inc.php');
    require_once('../inc/connect.php');

    class Marker
    {
        public int $plz;
        public string $ortName;
        public float $lat;
        public float $lng;
        public int $anzahl;
        
        public function __construct(int $plz, string $ortName, float $lat, float $lng)
        {
            $this -> plz = $plz;
            $this -> ortName = $ortName;
            $this -> lat = $lat;
            $this -> lng = $lng;
            $this -> anzahl = 1;
        }

        public function Json()
        {
            $jsonKoordinaten = json_encode($this, JSON_PRETTY_PRINT);
        }
    }

    $query = 'SELECT plz_id from tbldude ORDER BY plz_id';

    $result = $db -> query($query);
    $resultAll = $result -> fetchAll();

    $json = array();
    $vergleich = array();

    foreach($resultAll as $row)
    {
        $query = 'SELECT plz, ort, latitude, longitude FROM tblplz WHERE plz_id = ' . $row[0] . ';';
        
        $result = $db -> query($query);
        $result = $result -> fetch();
        
        if(!empty($json))
        {
            if($result[0] == $temp -> plz)
            {
                $temp -> anzahl += 1;
            }
            else
            {
                $temp = new Marker($result[0], $result[1], $result[2], $result[3]);
                $json[] = $temp;
            }
        }
        else
        {
            $temp = new Marker($result[0], $result[1], $result[2], $result[3]);
            $json[] = $temp;
        }
    }
    
    $db = null;

    echo json_encode($json);

?>