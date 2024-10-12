<?php
include_once 'db/connect.php';
include_once 'db/yetki.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: ./login.php");
    exit();
}

$giris_yapildi = isset($_SESSION['user_id']);

$currentPath = $_SERVER['REQUEST_URI'];

function isActive($path) {
    global $currentPath;
    return strpos($currentPath, $path) !== false ? 'text-black font-bold' : 'text-zinc-400';
}

$user_photo = '';
$user_gender = '';
$user_email = '';

if ($giris_yapildi) {
    $user_id = $_SESSION['user_id'];
    $giris = $conn->prepare("SELECT photo, gender, email FROM users WHERE id = ?");
    $giris->bind_param("i", $user_id);
    $giris->execute();
    $giris->bind_result($user_photo, $user_gender, $user_email);
    $giris->fetch();
    $giris->close();

}

 ?> 
 
<header class="flex top-0 sticky bg-white z-20 items-center justify-between border-b p-2 px-4">
  <div class="flex w-full items-center justify-between mx-auto max-w-screen-lg">
    <a href="./" class=" w-96 flex text-2xl"><b>makale</b>beta</a>

    <nav class="flex justify-center max-lg:hidden max-lg:*:w-10 *:w-32 *:justify-center *:flex *:flex-col *:items-center items-center *:rounded-lg *:h-full *:px-0 *:py-3 *:transition hover:*:bg-zinc-200">
      <a href="./" class="<?php echo isActive(''); ?>">
          <i class="ri-home-6-fill text-2xl"></i>
          <p class="text-sm font-semibold ">Ana Sayfa</p>
      </a>
      <a href="./search.php" class="<?php echo isActive('/search'); ?>"> 
          <i class="ri-search-fill text-2xl"></i>
          <p class="text-sm font-semibold">Ara</p>
      </a>
      <?php if ($yetki === 0): ?>
          <a href="./makale-ekle.php" class="<?php echo isActive('/makale-ekle.php'); ?>">
              <i class="ri-edit-fill text-2xl"></i>
              <p class="text-sm font-semibold">Makale Ekle</p>
          </a>

      <?php endif; ?>
      <?php if ($giris_yapildi): ?>
          <a href="./notifications.php" class="<?php echo isActive('/notifications.php'); ?>">
            <i class="ri-notification-3-fill text-2xl"></i>
            <p class="text-sm font-semibold">Bildirimler</p>
        </a>
      <?php endif; ?>
    </nav>

    <div class="flex gap-2 *:px-3 flex-4 w-96 *:py-1.5 *:rounded-lg justify-end items-center">
        <?php if ($giris_yapildi): ?>
            <div
                x-data="{ isOpen: false, openedWithKeyboard: false }"
                class="relative w-36 justify-end text-end"
                @keydown.esc.window="isOpen = false, openedWithKeyboard = false"
            >
                <button
                    type="button"
                    @click="isOpen = ! isOpen"
                    class="inline-flex cursor-pointer items-center gap-2 ml-auto whitespace-nowrap rounded-md text-sm font-medium tracking-wide transition"
                    aria-haspopup="true"
                    @keydown.space.prevent="openedWithKeyboard = true"
                    @keydown.enter.prevent="openedWithKeyboard = true"
                    @keydown.down.prevent="openedWithKeyboard = true"
                    :class="isOpen || openedWithKeyboard ? 'text-neutral-900 ' : 'text-neutral-600 '"
                    :aria-expanded="isOpen || openedWithKeyboard"
                    >
                    <div class="text-right max-lg:hidden">
                        <b><?php echo $_SESSION['username']; ?></b>
                        <p class="text-xs opacity-60"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                    <img
                        class="w-8 h-8 rounded-xl border object-cover"
                        src="<?php echo !empty($user_photo) ? './assets/photos/' . htmlspecialchars($user_photo) : 'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg'; ?>"
                        alt="Profil Fotoğrafı"
                    />
                </button>

                <div
                    x-cloak
                    x-show="isOpen || openedWithKeyboard"
                    x-transition
                    x-trap="openedWithKeyboard"
                    @click.outside="isOpen = false, openedWithKeyboard = false"
                    @keydown.down.prevent="$focus.wrap().next()"
                    @keydown.up.prevent="$focus.wrap().previous()"
                    class="absolute top-14 right-0 flex w-full shadow-md border flex-col overflow-hidden rounded-lg"
                    role="menu"
                    >
                    <a
                        href="./profile.php?id=<?php echo $_SESSION['user_id'] ?>"
                        class="bg-white w-full w-36 text-left px-4 py-2 text-sm text-neutral-600 hover:bg-zinc-200"
                        role="menuitem"
                    >
                        Profilim
                    </a>
                        <form method="POST" action="" class="flex w-full gap-2 items-center w-36">
                            <button type="submit" name="logout" class="bg-white text-left w-full w-36 px-4 py-2 text-sm text-red-600 hover:bg-red-200">Çıkış Yap</button>
                        </form>
                </div>
            </div>
        <?php else: ?>
            <a href="./register.php" class="bg-zinc-200">Kayıt Ol</a>
            <a href="./login.php" class="bg-black text-white">Giriş Yap</a>
        <?php endif; ?>
    </div>

  </div>
</header>


<div class="fixed bottom-0 left-0 lg:hidden right-0 flex *:flex-1 bg-white h-20 border-t shadow-xl *:justify-center *:flex *:flex-col *:items-center z-10">
        <a href="./" class="<?php echo isActive(''); ?>">
          <i class="ri-home-6-fill text-2xl"></i>
          <p class="text-sm font-semibold ">Ana Sayfa</p>
        </a>
        <a href="./search.php" class="<?php echo isActive('/search'); ?>"> 
            <i class="ri-search-fill text-2xl"></i>
            <p class="text-sm font-semibold">Ara</p>
        </a>
        <?php if ($yetki === 0): ?>
            <a href="./makale-ekle.php" class="<?php echo isActive('/makale-ekle.php'); ?>">
                <i class="ri-edit-fill text-2xl"></i>
                <p class="text-sm font-semibold ">Makale Ekle</p>
            </a>

        <?php endif; ?>
        <?php if ($giris_yapildi): ?>
            <a href="./notifications.php" class="<?php echo isActive('/notifications.php'); ?>">
                <i class="ri-notification-3-fill text-2xl"></i>
                <p class="text-sm font-semibold ">Bildirimler</p>
            </a>
        <?php endif; ?>
</div>