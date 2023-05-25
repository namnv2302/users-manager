<?php 

layout('header-login');

echo '<div class="container text-center"><br/>';
$token = getBody()['token'];

if(!empty($token)) {
    $queryData = firstRaw("SELECT id, fullname, email FROM users WHERE forgotToken='$token'");
    if(!empty($queryData)) {
        $email = $queryData['email'];
        $userId = $queryData['id'];
        $fullname = $queryData['fullname'];

        if(isPost()) {
            // redirect('?module=auth&action=reset&token='.$token);
            $formData = getBody();
            $errors = [];
            
            // Validate password
            if(empty(trim($formData['password']))) {
                $errors['password']['required'] = 'Mật khẩu bắt buộc phải nhập';
            } else {
                if(strlen(trim($formData['password'])) < 8) {
                    $errors['password']['min'] = 'Mật khẩu không được nhỏ hơn 8 ký tự';
                }
            }

            // Validate password confirm
            if(empty(trim($formData['password-confirm']))) {
                $errors['password-confirm']['required'] = 'Xác nhận mật khẩu không được để trống';
            } else {
                if(trim($formData['password']) != trim($formData['password-confirm'])) {
                    $errors['password-confirm']['match'] = 'Mật khẩu không khớp nhau';
                }
            }

            if(empty($errors)) {
                $passwordHash = password_hash($formData['password'], PASSWORD_DEFAULT);
                $dataUpdate = [
                    'password' => $passwordHash,
                    'forgotToken' => null,
                    'updatedAt' => date('Y-m-d H:i:s')
                ];
                $updateStatus = update('users', $dataUpdate, "id=$userId");
                if($updateStatus) {
                    setFlashData('msg', 'Thay đổi mật khẩu thành công');
                    setFlashData('type', 'success');

                    $subject = 'Đổi mật khẩu thành công';
                    $content = 'Chào '.$fullname.'<br/>';
                    $content .= 'Bạn đã thay đổi mật khẩu thành công.';
                    sendMail($email, $subject, $content);

                    redirect('?module=auth&action=login');
                } else {
                    setFlashData('msg', 'Có lỗi! Tạm thời chưa thể đổi mật khẩu');
                    setFlashData('type', 'danger');
                    redirect('?module=auth&action=reset&token='.$token);
                }
            } else {
                setFlashData('msg', 'Vui lòng kiểm tra dữ liệu nhập vào');
                setFlashData('type', 'danger');
                setFlashData('errors', $errors);
                redirect('?module=auth&action=reset&token='.$token);
            }
        }
        $msg = getFlashData('msg');
        $type = getFlashData('type');
        $errors = getFlashData('errors');
?>

<div class="row text-start">
    <div class="col-6" style="margin: 100px auto">
        <h3 class="text-center text-uppercase">Đặt lại mật khẩu</h3>
        <?php
            createNotifi($msg, $type);
        ?>
        <form action="" method="post">
            <div class="form-group mb-3">
                <label for="password" class="form-label">Nhập mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control">
                <span class="error"><?php echo (!empty($errors['password'])) ? reset($errors['password']) : false; ?></span>
            </div>

            <div class="form-group mb-3">
                <label for="password-confirm" class="form-label">Nhập lại mật khẩu</label>
                <input type="password" id="password-confirm" name="password-confirm" class="form-control">
                <span class="error"><?php echo (!empty($errors['password-confirm'])) ? reset($errors['password-confirm']) : false; ?></span>
            </div>

            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Xác nhận</button></div>

            <hr>
            <div class="d-flex justify-content-between">
                <p><a href="?module=auth&action=login">Đăng nhập hệ thống</a></p>
                <p><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
            </div>
            <input type="hidden" name="token" value="<?php echo $token; ?>" />
        </form>
    </div>
</div>

<?php

    } else {
        createNotifi('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
    }
} else {
    createNotifi('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
}

echo '</div>';

layout('footer-login');