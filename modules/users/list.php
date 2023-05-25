<?php

if(!defined('_INCODE')) die('Access Deined...');

if(!checkLogin()) {
    redirect('?module=auth&action=login');
}