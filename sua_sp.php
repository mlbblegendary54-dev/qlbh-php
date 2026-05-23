<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sp = $conn->query("SELECT * FROM sanpham WHERE id=$id")->fetch_assoc();
if (!$sp) { header("Location: index.php"); exit; }

$thongbao = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten     = trim($_POST['ten_sp']);
    $gia     = $_POST['gia'];
    $soluong = $_POST['so_luong'];
    $danhmuc = $_POST['danh_muc'];

    if (empty($ten) || empty($gia) || empty($soluong)) {
        $thongbao = ['loai'=>'error','noi_dung'=>'Vui lòng điền đầy đủ!'];
    } else {
        $stmt = $conn->prepare("UPDATE sanpham SET ten_sp=?,gia=?,so_luong=?,danh_muc=? WHERE id=?");
        $stmt->bind_param("sdisi", $ten, $gia, $soluong, $danhmuc, $id);
        if ($stmt->execute()) {
            $sp['ten_sp']=$ten; $sp['gia']=$gia;
            $sp['so_luong']=$soluong; $sp['danh_muc']=$danhmuc;
            $thongbao = ['loai'=>'success','noi_dung'=>'Cập nhật thành công!'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa sản phẩm · ShopManager</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Inter',sans-serif; background:#0f0f13; color:#e2e2e2; display:flex; min-height:100vh; }

.sidebar {
    width:240px; min-height:100vh; background:#17171f;
    border-right:1px solid #2a2a35;
    display:flex; flex-direction:column;
    position:fixed; top:0; left:0; bottom:0;
}
.sidebar-logo { padding:28px 24px 20px; border-bottom:1px solid #2a2a35; }
.sidebar-logo h1 { font-size:18px; font-weight:700; color:#fff; }
.sidebar-logo h1 span { color:#6c63ff; }
.sidebar-logo p { font-size:11px; color:#555; margin-top:3px; }
.sidebar-menu { padding:16px 12px; flex:1; }
.menu-label { font-size:10px; font-weight:600; color:#444; text-transform:uppercase; letter-spacing:1px; padding:0 12px; margin:16px 0 8px; }
.menu-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:8px; text-decoration:none;
    color:#888; font-size:14px; font-weight:500;
    transition:all .2s; margin-bottom:2px;
}
.menu-item:hover { background:#1f1f2e; color:#fff; }
.menu-item.active { background:#6c63ff22; color:#6c63ff; }
.menu-item .icon { font-size:16px; width:20px; text-align:center; }
.sidebar-footer { padding:16px; border-top:1px solid #2a2a35; font-size:12px; color:#444; text-align:center; }

.main { margin-left:240px; flex:1; padding:32px; }
.page-header { margin-bottom:28px; }
.page-header h2 { font-size:22px; font-weight:700; color:#fff; }
.page-header p  { font-size:13px; color:#555; margin-top:3px; }

.form-card {
    background:#17171f; border:1px solid #2a2a35;
    border-radius:16px; padding:32px; max-width:520px;
}
.form-card h3 {
    font-size:16px; font-weight:600; color:#fff;
    margin-bottom:24px; padding-bottom:16px;
    border-bottom:1px solid #2a2a35;
    display:flex; align-items:center; gap:8px;
}
.form-group { margin-bottom:20px; }
.form-group label {
    display:block; font-size:12px; font-weight:600; color:#666;
    text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;
}
.form-group input,
.form-group select {
    width:100%; padding:11px 14px;
    background:#0f0f13; border:1px solid #2a2a35;
    border-radius:9px; color:#e2e2e2; font-size:14px;
    outline:none; font-family:'Inter',sans-serif;
    transition:border-color .2s, box-shadow .2s;
}
.form-group input:focus,
.form-group select:focus { border-color:#6c63ff; box-shadow:0 0 0 3px #6c63ff22; }
.form-group select option { background:#1a1a24; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.form-hint { font-size:11px; color:#444; margin-top:5px; }

.form-actions { display:flex; gap:10px; margin-top:28px; padding-top:20px; border-top:1px solid #2a2a35; }
.btn-cancel {
    padding:10px 20px; background:transparent; border:1px solid #2a2a35;
    border-radius:9px; color:#666; font-size:13px; font-weight:500;
    text-decoration:none; display:inline-flex; align-items:center;
    transition:all .2s; cursor:pointer;
}
.btn-cancel:hover { border-color:#444; color:#aaa; }
.btn-submit {
    flex:1; padding:11px 20px; background:#6c63ff; border:none;
    border-radius:9px; color:#fff; font-size:14px; font-weight:600;
    cursor:pointer; font-family:'Inter',sans-serif; transition:all .2s;
}
.btn-submit:hover { background:#5b53ee; box-shadow:0 4px 20px #6c63ff44; }

.alert {
    padding:12px 16px; border-radius:9px; margin-bottom:20px;
    font-size:13px; font-weight:500; display:flex; align-items:center; gap:8px;
}
.alert-success { background:#4ade8022; color:#4ade80; border:1px solid #4ade8033; }
.alert-error   { background:#f8717122; color:#f87171; border:1px solid #f8717133; }

.id-badge {
    display:inline-block; padding:2px 10px; background:#6c63ff22;
    color:#a78bfa; border-radius:5px; font-size:12px; font-weight:600;
    margin-left:8px;
}
</style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <h1>Shop<span>Manager</span></h1>
        <p>Hệ thống quản lý bán hàng</p>
    </div>
    <nav class="sidebar-menu">
        <div class="menu-label">Quản lý</div>
        <a href="index.php" class="menu-item">
            <span class="icon">📦</span> Sản phẩm
        </a>
        <a href="them_sp.php" class="menu-item">
            <span class="icon">➕</span> Thêm sản phẩm
        </a>
    </nav>
    <div class="sidebar-footer">v1.0 · ShopManager</div>
</aside>

<main class="main">
    <div class="page-header">
        <h2>Chỉnh sửa sản phẩm <span class="id-badge">#<?= $id ?></span></h2>
        <p>Cập nhật thông tin sản phẩm</p>
    </div>

    <div class="form-card">
        <h3>✏️ <?= htmlspecialchars($sp['ten_sp']) ?></h3>

        <?php if ($thongbao): ?>
            <div class="alert alert-<?= $thongbao['loai'] ?>">
                <?= $thongbao['loai']=='success' ? '✅' : '❌' ?> <?= $thongbao['noi_dung'] ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Tên sản phẩm</label>
                <input type="text" name="ten_sp" value="<?= htmlspecialchars($sp['ten_sp']) ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Giá bán (VNĐ)</label>
                    <input type="number" name="gia" value="<?= $sp['gia'] ?>" min="0" required>
                    <div class="form-hint">Nhập số, không cần dấu chấm</div>
                </div>
                <div class="form-group">
                    <label>Số lượng tồn</label>
                    <input type="number" name="so_luong" value="<?= $sp['so_luong'] ?>" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Danh mục</label>
                <select name="danh_muc">
                    <?php foreach (['Quần áo'=>'👔','Giày dép'=>'👟','Phụ kiện'=>'👜'] as $dm=>$emoji): ?>
                        <option value="<?= $dm ?>" <?= $sp['danh_muc']==$dm?'selected':'' ?>>
                            <?= $emoji ?> <?= $dm ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <a href="index.php" class="btn-cancel">← Quay lại</a>
                <button type="submit" class="btn-submit">💾 Lưu thay đổi</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>