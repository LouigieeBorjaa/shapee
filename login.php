<?php 
require_once 'helpers/functions.php';
template('header.php');

use Aries\MiniFrameworkStore\Models\User;

$user = new User();

if(isset($_POST['submit'])) {
    $user_info = $user->login([
        'email' => $_POST['email'],
    ]);

    if($user_info && password_verify($_POST['password'], $user_info['password'])) {
        setUserSession($user_info);
        header('Location: my-account.php');
        exit;
    } else {
        $message = 'Invalid email or password';
    }
}

if(isLoggedIn()) {
    header('Location: my-account.php');
    exit;
}

// Display timeout message if session expired
$timeout_message = '';
if(isset($_GET['msg']) && $_GET['msg'] === 'timeout') {
    $timeout_message = 'Your session has expired. Please login again.';
}
?>

<div class="container">
    <div class="row align-items-center">
        <div class="col mt-5 mb-5">
            <h1 class="text-center">Login</h1>
            <?php if($timeout_message): ?>
                <div class="alert alert-warning text-center"><?php echo $timeout_message; ?></div>
            <?php endif; ?>
            <h3 class="text-center text-danger"><?php echo isset($message) ? $message : ''; ?></h3>
            <form style="width: 400px; margin: auto;" action="login.php" method="POST">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>