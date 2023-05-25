<?php

$data = [
    'pageTitle' => 'Quên mật khẩu'
];

layout('header-login', $data);

if(checkLogin()) {
    redirect('?module=users');
}

if(isPost()) {
    $formData = getBody();
    if(!empty($formData['email'])) {
        $email = $formData['email'];
        
        $queryData = firstRaw("SELECT id FROM users WHERE email='$email'");
        if(!empty($queryData)) {
            $userId = $queryData['id'];
            $forgotToken = sha1(uniqid().time());

            $dateUpdate = [
                "forgotToken" => $forgotToken
            ];
            $updateStatus = update('users', $dateUpdate, "id=$userId");
            if($updateStatus) {
                $link = _WEB_HOST_ROOT.'?module=auth&action=reset&token='.$forgotToken;

                $subject = 'Yêu cầu khôi phục mật khẩu';
                $content = 'Chào bạn '.$email.'<br/>';
                $content .= 'Vui lòng click vào link sau để tiến hành đặt lại mật khẩu: '.$link.'<br/>';
                $content .= 'Trân trọng';

                $sendStatus = sendMail($email, $subject, $content);
                if($sendStatus) {
                    setFlashData('msg', 'Vui lòng kiểm tra email để đặt lại mật khẩu');
                    setFlashData('type', 'success');
                } else {
                    setFlashData('msg', 'Lỗi hệ thống, tạm thời không thể sử dụng chức năng này');
                    setFlashData('type', 'danger');
                }
            } else {
                setFlashData('msg', 'Lỗi hệ thống, tạm thời không thể sử dụng chức năng này');
                setFlashData('type', 'danger');
            }
        } else {
            setFlashData('msg', 'Email không tồn tại trong hệ thống');
            setFlashData('type', 'danger');
        }
    } else {
        setFlashData('msg', 'Vui lòng nhập email xác nhận');
        setFlashData('type', 'danger');
    }
    redirect('?module=auth&action=forgot');
}

$msg = getFlashData('msg');
$type = getFlashData('type');
?>

<div class="row">
    <div class="col-6" style="margin: 100px auto">
        <h3 class="text-center text-uppercase">Quên mật khẩu</h3>
        <?php createNotifi($msg, $type) ?>
        <form action="" method="post">
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Địa chỉ email...">
            </div>

            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Xác nhận</button></div>

            <hr>
            <div class="d-flex justify-content-between">
                <p><a href="?module=auth&action=login">Đăng nhập hệ thống</a></p>
                <p><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
            </div>
        </form>
    </div>
</div>

<?php
layout('footer-login');