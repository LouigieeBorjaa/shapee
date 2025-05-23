<?php
require_once 'helpers/functions.php';

clearUserSession();
clearCart();
clearWishlist();

header('Location: login.php');
exit;