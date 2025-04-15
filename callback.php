<?php
session_start();

require_once 'vendor/autoload.php';

$google_client = new Google_Client();
$google_client->setClientId('24328922924-hdn0mkqmsdajveavibo61i6pf4oj24fs.apps.googleusercontent.com');
$google_client->setClientSecret('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');
$google_client->setRedirectUri('http://localhost/faculty/callback.php');
$google_client->addScope('email');
$google_client->addScope('profile');

if (isset($_GET['code'])) {
    // Authenticate and get the access token
    $google_client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $google_client->getAccessToken();

    // Set the access token to fetch user info
    $google_client->setAccessToken($_SESSION['access_token']);
    $google_service = new Google_Service_Oauth2($google_client);
    $google_account_info = $google_service->userinfo->get();

    // Store user information in session
    $_SESSION['user_email'] = $google_account_info->email;
    $_SESSION['user_name'] = $google_account_info->name;

    // Redirect to the home page
    header('Location: home.php');
    exit();
} else {
    // Redirect back to login if code is not set
    header('Location: science.php');
    exit();
}
?>
