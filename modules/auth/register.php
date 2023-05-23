<?php 

$data = [
    'pageTitle' => 'Đăng ký tài khoản'
];

layout('header-login', $data);

if(isPost()) {
    $formData = getBody();
    $errors = [];
    // Validate fullname
    if(empty(trim($formData['fullname']))) {
        $errors['fullname']['required'] = 'Họ tên bắt buộc phải nhập';
    } else {
        if(strlen(trim($formData['fullname'])) < 6) {
            $errors['fullname']['min'] = 'Họ tên phải lớn hơn hoặc bằng 6 ký tự';
        }
    }

    // Validate phone
    if(empty(trim($formData['phone']))) {
        $errors['phone']['required'] = 'Số điện thoại bắt buộc phải nhập';
    } else {
        if(!checkPhone($formData['phone'])) {
            $errors['phone']['validate'] = 'Số điện thoại không hợp lệ';
        }
    }

    // Validate email
    if(empty(trim($formData['email']))) {
        $errors['email']['required'] = 'Địa chỉ email bắt buộc phải nhập';
    } else {
        if(!isEmail(trim($formData['email']))) {
            $errors['email']['validate'] = 'Địa chỉ email không hợp lệ';
        } else {
            $email = trim($formData['email']);
            $sql = "SELECT id FROM users WHERE email='$email'";
            if(getRows($sql) > 0) {
                $errors['email']['unique'] = 'Địa chỉ email đã tồn tại';
            }
        }
    }

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
        $activeToken = sha1(uniqid().time());
        $dataInsert = [
            'email'=> $formData['email'],
            'phone' => $formData['phone'],
            'fullname' => $formData['fullname'],
            'password' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'activeToken' => $activeToken,
            'createdAt' => date('Y-m-d H:i:s')
        ];
        $insertStatus = insert('users', $dataInsert);
        if($insertStatus) {
            // Send mail
            $link = _WEB_HOST_ROOT.'?module=auth&action=active&token='.$activeToken;
            $subject = $formData['fullname'].' vui lòng kích hoạt tài khoản';
            $content = 'Chào bạn '.$formData['fullname'].'<br/>';
            $content.= 'Vui lòng click vào link dưới đây để kích hoạt tài khoản: <br/>'.$link.'<br/>';
            $content.= 'Trân trọng!';

            $sendStatus = sendMail($formData['email'], $subject, $content);
            if($sendStatus) {
                setFlashData('msg', 'Đăng ký tài khoản thành công. Vui lòng kiểm tra email để kích hoạt tài khoản');
                setFlashData('type', 'success');
            } else {
                setFlashData('msg', 'Hệ thống đang gặp sự cố. Thử lại sau');
                setFlashData('type', 'danger');
            }

        } else {
            setFlashData('msg', 'Hệ thống đang gặp sự cố. Thử lại sau');
            setFlashData('type', 'danger');
        }

        redirect('?module=auth&action=register');
    } else {
        setFlashData('msg', 'Vui lòng kiểm tra dữ liệu nhập vào');
        setFlashData('type', 'danger');
        setFlashData('errors', $errors);
        setFlashData('formDataPre', $formData);
        redirect('?module=auth&action=register');
    }
}
$msg = getFlashData('msg');
$type = getFlashData('type');
$errors = getFlashData('errors');
$formDataPre = getFlashData('formDataPre');

?>

<div class="row">
    <div class="col-6" style="margin: 100px auto">
        <h3 class="text-center text-uppercase">Đăng ký tài khoản</h3>
        <?php
            createNotifi($msg, $type);
        ?>
        <form action="" method="post">
            <div class="form-group mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" id="name" name="fullname" class="form-control" placeholder="Họ tên..." value="<?php displayInputValuePre('fullname', $formDataPre) ?>">
                <span class="error"><?php echo (!empty($errors['fullname'])) ? reset($errors['fullname']) : false; ?></span>
            </div>

            <div class="form-group mb-3">
                <label for="phone" class="form-label">Điện thoại</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Điện thoại..." value="<?php displayInputValuePre('phone', $formDataPre) ?>">
                <span class="error"><?php echo (!empty($errors['phone'])) ? reset($errors['phone']) : false; ?></span>
            </div>

            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Địa chỉ email..." value="<?php displayInputValuePre('email', $formDataPre) ?>">
                <span class="error"><?php echo (!empty($errors['email'])) ? reset($errors['email']) : false; ?></span>
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mật khẩu...">
                <span class="error"><?php echo (!empty($errors['password'])) ? reset($errors['password']) : false; ?></span>
            </div>

            <div class="form-group mb-3">
                <label for="password-confirm" class="form-label">Nhập lại mật khẩu</label>
                <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="Nhập lại mật khẩu...">
                <span class="error"><?php echo (!empty($errors['password-confirm'])) ? reset($errors['password-confirm']) : false; ?></span>
            </div>

            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Đăng ký</button></div>

            <hr>
            <p><a href="?module=auth&action=login">Đăng nhập hệ thống</a></p>
        </form>
    </div>
</div>

<?php
layout('footer-login');