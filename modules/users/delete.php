<?php 

$id = getBody()['id'];
if(!empty($id)) {
    $sql = "SELECT fullname FROM users WHERE id='$id'";
    if(getRows($sql) > 0) {
        $deleteToken = delete('login_token', "userId=$id");
        if($deleteToken) {
            $condition = "id=$id";
            $deleteStatus = delete('users', $condition);
            if($deleteStatus) {
                setFlashData('msg', 'Xóa người dùng thành công');
                setFlashData('type', 'success');
            } else {
                setFlashData('msg', 'Lỗi hệ thống. Tạm thời không thể xóa');
                setFlashData('type', 'danger');
            }
        }

    } else {
        setFlashData('msg', 'Người dùng không tồn tại. Không thể xóa');
        setFlashData('type', 'danger');
    }
    redirect('?module=users');
} else {
    setFlashData('msg', 'Người dùng không tồn tại. Không thể xóa');
    setFlashData('type', 'danger');
    redirect('?module=users');
}