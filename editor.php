<?php
session_start();
include 'db/connect.php';

if ($_SESSION['rol'] != 'editor') {
    header('Location: giris.php');
    exit();
}

$sorgu = "SELECT * FROM bildiriler";
$sonuc = mysqli_query($conn, $sorgu);
?>

<h1>Bildiri Listesi</h1>
<table>
    <tr>
        <th>Başlık</th>
        <th>Durum</th>
        <th>Hakem Ataması</th>
    </tr>
    <?php while($bildiri = mysqli_fetch_assoc($sonuc)): ?>
    <tr>
        <td><?= $bildiri['baslik']; ?></td>
        <td><?= $bildiri['durum']; ?></td>
        <td>
            <form method="POST" action="hakem_atama.php">
                <input type="hidden" name="bildiri_id" value="<?= $bildiri['id']; ?>">
                <select name="hakem_id">
                    <?php
                    // Hakem listesini çek
                    $hakemler = mysqli_query($conn, "SELECT * FROM kullanicilar WHERE rol='hakem'");
                    while($hakem = mysqli_fetch_assoc($hakemler)): ?>
                        <option value="<?= $hakem['id']; ?>"><?= $hakem['kullanici_adi']; ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="submit" value="Ata">
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>888