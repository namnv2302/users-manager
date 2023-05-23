<?php 

layout('header-login');

echo '<div class="container text-center"><br/>';
$token = getBody()['token'];
if(!empty($token)) {
    // So sánh token trong db
    $tokenQuery = firstRaw("SELECT id, fullname, email FROM users WHERE activeToken='$token'");
    if(!empty($tokenQuery)) {
        $userId = $tokenQuery['id'];
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null
        ];
        $updateStatus = update('users', $dataUpdate, "id=$userId");
        if($updateStatus) {
            setFlashData('msg', 'Kích hoạt tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ');
            setFlashData('type', 'success');

            // Send mail 
            $loginLink = _WEB_HOST_ROOT.'?module=auth&action=login';
            $subject = 'Kích hoạt tài khoản thành công';
            $content = 'Chúc mừng '.$tokenQuery['fullname'].' đã kích hoạt thành công<br/>';
            $content .= 'Bạn có thể đăng nhập bằng link sau: '.$loginLink.'<br/>';

            sendMail($tokenQuery['email'], $subject, $content);

        } else {
            setFlashData('msg', 'Kích hoạt tài khoản không thành công! Liên hệ quản trị viên');
            setFlashData('type', 'danger');
        }
        redirect('?module=auth&action=login');
    } else {
        createNotifi('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
    }
} else {
    createNotifi('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
}
echo '</div>';

layout('footer-login');