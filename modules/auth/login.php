<?php 
 
$data = [
    'pageTitle' => 'Đăng nhập hệ thống'
];

layout('header-login', $data);

if(checkLogin()) {
    redirect('?module=users');
}

if(isPost()) {
    $formData = getBody();
    if(!empty($formData['email']) && !empty($formData['password'])) {
        $email = $formData['email'];
        $password = $formData['password'];

        $queryData = firstRaw("SELECT id, password FROM users WHERE email='$email'");
        if(!empty($queryData)) {
            $userId = $queryData['id'];
            $passwordHash = $queryData['password'];
            if(password_verify($password, $passwordHash)) {
                $tokenLogin = sha1(uniqid().time());
                $dataInsert = [
                    'userId' => $userId,
                    'token' => $tokenLogin,
                    'createdAt' => date('Y-m-d H:i:s')
                ];
                $insertStatus = insert('login_token', $dataInsert);
                if($insertStatus) {
                    setSession('tokenLogin', $tokenLogin);
                    redirect('?module=users');
                } else {
                    setFlashData('msg', 'Lỗi hệ thống, tạm thời không đăng nhập được');
                    setFlashData('type', 'danger');
                }
            } else {
                setFlashData('msg', 'Mật khẩu không chính xác');
                setFlashData('type', 'danger');
            }
        } else {
            setFlashData('msg', 'Tài khoản không tồn tại trong hệ thống');
            setFlashData('type', 'danger');
        }
    } else {
        setFlashData('msg', 'Vui lòng nhập đầy đủ thông tin');
        setFlashData('type', 'danger');
    }
    redirect('?module=auth&action=login');
}

$msg = getFlashData('msg');
$type = getFlashData('type');
?>

<div class="row">
    <div class="col-6" style="margin: 100px auto">
        <h3 class="text-center text-uppercase">Đăng nhập hệ thống</h3>
        <?php createNotifi($msg, $type) ?>
        <form action="" method="post">
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Địa chỉ email...">
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mật khẩu...">
            </div>

            <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Đăng nhập</button></div>

            <hr>
            <div class="d-flex justify-content-between">
                <p><a href="?module=auth&action=forgot">Quên mật khẩu</a></p>
                <p><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
            </div>
        </form>
    </div>
</div>

<?php
layout('footer-login');