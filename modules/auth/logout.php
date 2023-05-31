<?php 

if(checkLogin()) {
    $token = getSession('tokenLogin');
    delete('login_token', "token='$token'");
    removeSession('tokenLogin');
    redirect('?module=auth&action=login');
}