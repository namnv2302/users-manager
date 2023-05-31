<?php

if(!defined('_INCODE')) die('Access Deined...');

$data = [
    'pageTitle' => 'Quản lý người dùng'
];

layout('header', $data);

// Xử lý lọc
$filter = '';
if(isGet()) {
    $body = getBody();
    if(!empty($body['status'])) {
        $status = $body['status'];
        if($status == 2) {
            $statusSql = 0;
        } else {
            $statusSql = $status;
        }
        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        } else {
            $operator = 'WHERE';
        }

        $filter .= " $operator status=$statusSql";
    }

    if(!empty($body['key'])) {
        $key = $body['key'];
        if(!empty($filter) && strpos($filter, 'WHERE') >= 0) {
            $operator = 'AND';
        } else {
            $operator = 'WHERE';
        }
        $filter .= " $operator fullname LIKE '%$key%'";
    }
}


// Phân trang
// Tổng số lượng bản ghi
$userCount = getRows("SELECT id FROM users $filter");
// Số lương bản ghi trên 1 trang
$perPage = 7;
$maxPage = ceil($userCount / $perPage);

if(!empty(getBody()['page'])) {
    $page = getBody()['page'];
    if($page < 1 || $page > $maxPage) {
        $page = 1;
    }
} else {
    $page = 1;
}

// Tính offset (vị trí bắt đầu)
$offset = ($page -1 ) * $perPage;

//
$queryString = null;
if(!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=users', '', $queryString);
    $queryString = str_replace('&page='.$page, '', $queryString);
    $queryString = trim($queryString, '&');
    if(strlen(trim($queryString)) != 0) {
        $queryString = '&'.$queryString;
    }
}

//
$listUsers = getRaw("SELECT * FROM users $filter ORDER BY createdAt DESC LIMIT $offset, $perPage");

$msg = getFlashData('msg');
$type = getFlashData('type');
?>

<div class="container">
    <hr/>
    <h4 class="mt-5"><?php echo $data['pageTitle']; ?></h4>
    <p>
        <a href="?module=users&action=add" class="btn btn-sm btn-success">Thêm người dùng</a>
    </p>

    <form method="get">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="0">Chọn trạng thái</option>
                        <option value="1" <?php echo (!empty($status) && $status == 1) ? 'selected' : false; ?>>Kích hoạt</option>
                        <option value="2" <?php echo (!empty($status) && $status == 2) ? 'selected' : false; ?>>Chưa kích hoạt</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <input type="search" name="key" class="form-control" placeholder="Tìm kiếm người dùng..." value="<?php echo (!empty($key)) ? $key : false; ?>" />
            </div>
            <div class="col-2">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-block">Tìm kiếm</button>
                </div>
            </div>
        </div>
        <input type="hidden" name="module" value="users" />
    </form>

    <?php
        createNotifi($msg, $type);
    ?>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <td width="5%">STT</td>
                <td>Họ tên</td>
                <td>Email</td>
                <td>Điện thoại</td>
                <td>Trạng thái</td>
                <td width="5%">Sửa</td>
                <td width="5%">Xóa</td>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($listUsers)) :
                    $index = ($perPage * ($page - 1));
                    foreach($listUsers as $item) :
                        $index++;
            ?>
            <tr>
                <td><?php echo $index; ?></td>
                <td><?php echo $item['fullname']; ?></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['phone']; ?></td>
                <td><?php echo $item['status'] == 1 ? '<button type"button" class="btn btn-sm btn-success">Kích hoạt</button>' : '<button type"button" class="btn btn-sm btn-danger">Chưa kích hoạt</button>'; ?></td>
                <td>
                    <a href="<?php echo '?module=users&action=edit&id='.$item['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="ti-marker-alt"></i>
                    </a>
                </td>
                <td><a href="#" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger"><i class="ti-trash"></i></a></td>
            </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-danger text-center">Danh sách trống</div>
                    </td>
                </tr>  
            <?php endif; ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php  
                if($page > 1) {
                    $prevPage = $page - 1; 
                    echo '<li class="page-item">
                            <a class="page-link" href="'._WEB_HOST_ROOT.'?module=users'.$queryString.'&page='.$prevPage.'" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
                }
            ?>
            <?php 
                $begin = $page - 2;
                if($begin < 1) {
                    $begin = 1;
                }
                $end = $page + 2;
                if($end > $maxPage) {
                    $end = $maxPage;
                }
                for($index = $begin; $index <= $end; $index++) { ?>
                <li class="page-item <?php echo $index == $page ? 'active' : false; ?>"><a class="page-link" href="<?php echo _WEB_HOST_ROOT.'?module=users'.$queryString.'&page='.$index; ?>"><?php echo $index; ?></a></li>
            <?php } ?>
            <?php 
                if($page < $maxPage) {
                    $nextPage = $page + 1;
                    echo '
                    <li class="page-item">
                        <a class="page-link" href="'._WEB_HOST_ROOT.'?module=users'.$queryString.'&page='.$nextPage.'" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    ';
                }
            ?>
        </ul>
    </nav>
    <hr/>
</div>

<?php
layout('footer');


