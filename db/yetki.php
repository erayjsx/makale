<?php 

$yetki = null;

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  $sorgu = "SELECT yetki FROM users WHERE id = ?";
  $srg = $conn->prepare($sorgu);
  $srg->bind_param("i", $user_id);
  $srg->execute();
  $result = $srg->get_result();

  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      $yetki = $user['yetki'];
  }

  $srg->close();
}

?>
