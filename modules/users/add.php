<?php

if(!defined('_INCODE')) die('Access Deined...');

$data = [
    'pageTitle' => 'Thêm người dùng'
];

layout('header', $data);

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
    if(empty(trim($formData['confirm-password']))) {
        $errors['confirm-password']['required'] = 'Xác nhận mật khẩu không được để trống';
    } else {
        if(trim($formData['password']) != trim($formData['confirm-password'])) {
            $errors['confirm-password']['match'] = 'Mật khẩu không khớp nhau';
        }
    }

    if(empty($errors)) {
        $activeToken = sha1(uniqid().time());
        $dataInsert = [
            'email'=> $formData['email'],
            'phone' => $formData['phone'],
            'fullname' => $formData['fullname'],
            'password' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'status' => $formData['status'],
            'createdAt' => date('Y-m-d H:i:s')
        ];
        $insertStatus = insert('users', $dataInsert);
        if($insertStatus) {
                setFlashData('msg', 'Thêm người dùng thành công');
                setFlashData('type', 'success');
                redirect('?module=users');
        } else {
            setFlashData('msg', 'Hệ thống đang gặp sự cố. Thử lại sau');
            setFlashData('type', 'danger');
            redirect('?module=users&action=add');
        }
    } else {
        setFlashData('msg', 'Vui lòng kiểm tra dữ liệu nhập vào');
        setFlashData('type', 'danger');
        setFlashData('errors', $errors);
        setFlashData('formDataPre', $formData);
        redirect('?module=users&action=add');
    }
}
$msg = getFlashData('msg');
$type = getFlashData('type');
$errors = getFlashData('errors');
$formDataPre = getFlashData('formDataPre');
?>

<div class="container">
    <hr/>
    <h4 class="mt-5"><?php echo $data['pageTitle']; ?></h4>
    <?php
            createNotifi($msg, $type);
        ?>
    
    <form action="" method="post">
        <div class="row">
            <div class="col">
                <div class="form-group mb-3">
                    <label class="form-label" for="fullname">Họ tên</label>
                    <input id="fullname" type="text" name="fullname" class="form-control" value="<?php echo displayInputValuePre('fullname', $formDataPre) ?>" />
                    <span class="error d-inline-block" style="height: 16px;"><?php echo (!empty($errors['fullname'])) ? reset($errors['fullname']) : false; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" for="phone">Số điện thoại</label>
                    <input id="phone" type="text" name="phone" class="form-control" value="<?php echo displayInputValuePre('phone', $formDataPre) ?>" />
                    <span class="error d-inline-block" style="height: 16px;"><?php echo (!empty($errors['phone'])) ? reset($errors['phone']) : false; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="<?php echo displayInputValuePre('email', $formDataPre) ?>" />
                    <span class="error d-inline-block" style="height: 16px;"><?php echo (!empty($errors['email'])) ? reset($errors['email']) : false; ?></span>
                </div>
            </div>
            <div class="col">
                <div class="form-group mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input id="password" type="password" name="password" class="form-control" />
                    <span class="error d-inline-block" style="height: 16px;"><?php echo (!empty($errors['password'])) ? reset($errors['password']) : false; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" for="confirm-password">Nhập lại mật khẩu</label>
                    <input id="confirm-password" type="password" name="confirm-password" class="form-control" />
                    <span class="error d-inline-block" style="height: 16px;"><?php echo (!empty($errors['confirm-password'])) ? reset($errors['confirm-password']) : false; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="0" <?php echo displayInputValuePre('status', $formDataPre) == 0 ? 'selected' : false; ?>>Chưa kích hoạt</option>
                        <option value="1" <?php echo displayInputValuePre('status', $formDataPre) == 1 ? 'selected' : false; ?>>Kích hoạt</option>
                    </select>
                </div>
            </div>
        </div>
        <hr/>
        <button type="sub" class="btn btn-primary btn-sm">Thêm người dùng</button>
        <a href="?module=users" class="btn btn-success btn-sm">Quay lại</a>
    </form>
</div>

<?php
layout('footer');