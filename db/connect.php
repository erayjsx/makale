<?php

$servername = "localhost";
$veritabani_adi = "makale";
$kullanici_adi = "root";
$sifre = "";

$conn = new mysqli($servername, $kullanici_adi, $sifre, $veritabani_adi);


if ($conn->connect_error) {
    die ("Bağlantı başarısız :" .$conn->connect_error);
}

?>