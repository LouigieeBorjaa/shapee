<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    
    // Set session lifetime to 24 hours
    ini_set('session.gc_maxlifetime', 86400);
    session_set_cookie_params(86400);
    
    session_start();
}

function assets($path) {
    return 'assets/' . $path;
}

function template($path) {
    $template_path = __DIR__ . '/../templates/' . $path;
    if (!file_exists($template_path)) {
        die("Template file not found: " . $template_path);
    }
    include $template_path;
}

// Session timeout check
function checkSessionTimeout() {
    $timeout = 86400; // 24 hours in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Session has expired
        session_unset();
        session_destroy();
        header('Location: login.php?msg=timeout');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

// User session management
function setUserSession($user) {
    $_SESSION['user'] = $user;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['last_activity'] = time();
}

function clearUserSession() {
    unset($_SESSION['user']);
    unset($_SESSION['user_id']);
    unset($_SESSION['last_activity']);
}

function isLoggedIn() {
    if(isset($_SESSION['user'])) {
        return true;
    }
    return false;
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Wishlist session management
function initializeWishlist() {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
}

function clearWishlist() {
    unset($_SESSION['wishlist']);
}

/**
 * Format currency in PHP format
 * @param float $amount The amount to format
 * @param string $currency The currency code (default: PHP)
 * @return string Formatted currency string
 */
function formatCurrency($amount) {
    return '₱ ' . number_format($amount, 2, '.', ',');
}

// Check session timeout on every request
checkSessionTimeout();