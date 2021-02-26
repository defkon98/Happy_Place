LOAD DATA INFILE 'plzverzmitkoor5.csv' 
INTO TABLE tblplz
CHARACTER SET 'latin1'
FIELDS TERMINATED BY ';' 
LINES TERMINATED BY '\n'
(plz, ort, longitude, latitude)
;