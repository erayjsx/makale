<?php
session_start();
include 'db_baglanti.php';

if ($_SESSION['rol'] != 'hakem') {
    header('Location: giris.php');
    exit();
}

// Hakeme atanan bildirilerin listesi
$hakem_id = $_SESSION['kullanici_id'];
$sorgu = "SELECT bildiriler.* FROM bildiriler 
          JOIN degerlendirmeler ON bildiriler.id = degerlendirmeler.bildiri_id 
          WHERE degerlendirmeler.hakem_id = '$hakem_id'";
$sonuc = mysqli_query($conn, $sorgu);
?>

<h1>Değerlendirilecek Bildiriler</h1>
<table>
    <tr>
        <th>Başlık</th>
        <th>Dosya</th>
        <th>Değerlendir</th>
    </tr>
    <?php while($bildiri = mysqli_fetch_assoc($sonuc)): ?>
    <tr>
        <td><?= $bildiri['baslik']; ?></td>
        <td><a href="<?= $bildiri['dosya_yolu']; ?>">İndir</a></td>
        <td><a href="bildiri_degerlendir.php?bildiri_id=<?= $bildiri['id']; ?>">Değerlendir</a></td>
    </tr>
    <?php endwhile; ?>
</table>