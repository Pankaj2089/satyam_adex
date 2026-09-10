<?php
session_start();
unset ($_SESSION['UserId']);
unset ($_SESSION['Name']);
unset ($_SESSION['Type']);
unset ($_SESSION['username']);
unset ($_SESSION['password']);
unset ($_SESSION['AccountPermissions']);
echo '<script>window.location="'.LINK_PATH.'"</script>';
?>