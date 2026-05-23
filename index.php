<?php
require_once 'config.php';

$tong_sp   = $conn->query("SELECT COUNT(*) AS tong FROM sanpham")->fetch_assoc()['tong'];
$tong_tien = $conn->query("SELECT SUM(gia * so_luong) AS tong FROM sanpham")->fetch_assoc()['tong'];
$sp_con_it = $conn->query("SELECT COUNT(*) AS tong FROM sanpham WHERE so_luong < 20")->fetch_assoc()['tong'];
$result    = $conn->query("SELECT * FROM sanpham ORDER BY ngay_tao DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>ShopManager</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Inter',sans-serif; background:#0f0f13; color:#e2e2e2; display:flex; min-height:100vh; }

/* ===== SIDEBAR ===== */
.sidebar {
    width:240px; min-height:100vh;
    background:#17171f;
    border-right:1px solid #2a2a35;
    display:flex; flex-direction:column;
    position:fixed; top:0; left:0; bottom:0;
    z-index:100;
}
.sidebar-logo {
    padding:28px 24px 20px;
    border-bottom:1px solid #2a2a35;
}
.sidebar-logo h1 {
    font-size:18px; font-weight:700;
    color:#fff; letter-spacing:-.3px;
}
.sidebar-logo h1 span { color:#6c63ff; }
.sidebar-logo p { font-size:11px; color:#555; margin-top:3px; }

.sidebar-menu { padding:16px 12px; flex:1; }
.menu-label {
    font-size:10px; font-weight:600; color:#444;
    text-transform:uppercase; letter-spacing:1px;
    padding:0 12px; margin:16px 0 8px;
}
.menu-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:8px;
    text-decoration:none; color:#888;
    font-size:14px; font-weight:500;
    transition:all .2s; margin-bottom:2px;
}
.menu-item:hover { background:#1f1f2e; color:#fff; }
.menu-item.active { background:#6c63ff22; color:#6c63ff; }
.menu-item .icon { font-size:16px; width:20px; text-align:center; }

.sidebar-footer {
    padding:16px; border-top:1px solid #2a2a35;
    font-size:12px; color:#444; text-align:center;
}

/* ===== MAIN ===== */
.main { margin-left:240px; flex:1; padding:32px; }

.page-header {
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:28px;
}
.page-header h2 { font-size:22px; font-weight:700; color:#fff; }
.page-header p  { font-size:13px; color:#555; margin-top:3px; }

/* ===== STAT CARDS ===== */
.stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; }

.stat-card {
    background:#17171f; border:1px solid #2a2a35;
    border-radius:14px; padding:22px;
    position:relative; overflow:hidden;
    transition:transform .2s, border-color .2s;
}
.stat-card:hover { transform:translateY(-2px); border-color:#6c63ff44; }
.stat-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:2px;
    background:var(--accent);
}
.stat-card:nth-child(1) { --accent: linear-gradient(90deg,#6c63ff,#a78bfa); }
.stat-card:nth-child(2) { --accent: linear-gradient(90deg,#06b6d4,#67e8f9); }
.stat-card:nth-child(3) { --accent: linear-gradient(90deg,#f59e0b,#fcd34d); }

.stat-icon {
    width:40px; height:40px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; margin-bottom:14px;
}
.stat-card:nth-child(1) .stat-icon { background:#6c63ff22; }
.stat-card:nth-child(2) .stat-icon { background:#06b6d422; }
.stat-card:nth-child(3) .stat-icon { background:#f59e0b22; }

.stat-card .label { font-size:12px; color:#555; font-weight:500; text-transform:uppercase; letter-spacing:.5px; }
.stat-card .value { font-size:28px; font-weight:700; color:#fff; margin-top:4px; line-height:1; }
.stat-card .sub   { font-size:12px; color:#444; margin-top:6px; }

/* ===== TABLE CARD ===== */
.table-card {
    background:#17171f; border:1px solid #2a2a35;
    border-radius:14px; overflow:hidden;
}
.table-header {
    padding:20px 24px; border-bottom:1px solid #2a2a35;
    display:flex; justify-content:space-between; align-items:center;
}
.table-header h3 { font-size:15px; font-weight:600; color:#fff; }
.table-header span { font-size:12px; color:#555; }

.search-bar {
    display:flex; align-items:center; gap:12px;
    padding:12px 24px; border-bottom:1px solid #1e1e28;
    background:#13131a;
}
.search-bar input {
    flex:1; background:#1f1f2e; border:1px solid #2a2a35;
    border-radius:8px; padding:8px 14px; color:#e2e2e2;
    font-size:13px; outline:none; font-family:'Inter',sans-serif;
    transition:border-color .2s;
}
.search-bar input:focus { border-color:#6c63ff; }
.search-bar input::placeholder { color:#444; }

table { width:100%; border-collapse:collapse; }
thead tr { background:#13131a; }
th {
    padding:12px 20px; text-align:left;
    font-size:11px; font-weight:600; color:#444;
    text-transform:uppercase; letter-spacing:.8px;
    border-bottom:1px solid #2a2a35;
}
td {
    padding:15px 20px; font-size:14px;
    border-bottom:1px solid #1e1e28;
    vertical-align:middle;
    transition:background .15s;
}
tbody tr:hover td { background:#1a1a24; }
tbody tr:last-child td { border-bottom:none; }

.product-name { font-weight:500; color:#e2e2e2; }
.product-id   { font-size:12px; color:#444; font-weight:400; }

.badge {
    padding:4px 10px; border-radius:6px;
    font-size:11px; font-weight:600; letter-spacing:.3px;
}
.badge-ao   { background:#6c63ff22; color:#a78bfa; border:1px solid #6c63ff33; }
.badge-giay { background:#06b6d422; color:#67e8f9; border:1px solid #06b6d433; }
.badge-pk   { background:#f59e0b22; color:#fcd34d; border:1px solid #f59e0b33; }

.price { font-weight:600; color:#6c63ff; font-size:14px; }

.stock { font-weight:600; font-size:13px; }
.stock-ok  { color:#4ade80; }
.stock-low { color:#f87171; }

.stock-bar {
    width:60px; height:4px; background:#2a2a35;
    border-radius:2px; margin-top:4px;
}
.stock-bar-fill { height:100%; border-radius:2px; transition:width .3s; }

.actions { display:flex; gap:6px; }
.btn {
    padding:6px 14px; border-radius:7px; font-size:12px;
    font-weight:500; cursor:pointer; text-decoration:none;
    border:1px solid transparent; transition:all .2s;
    font-family:'Inter',sans-serif;
}
.btn-edit   { background:#1f1f2e; color:#a78bfa; border-color:#6c63ff33; }
.btn-edit:hover { background:#6c63ff22; border-color:#6c63ff66; }
.btn-delete { background:#1f1f2e; color:#f87171; border-color:#f8717133; }
.btn-delete:hover { background:#f8717122; border-color:#f8717166; }

.btn-add {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 18px; background:#6c63ff; color:#fff;
    border-radius:9px; text-decoration:none; font-size:13px;
    font-weight:600; border:none; cursor:pointer;
    transition:all .2s; font-family:'Inter',sans-serif;
}
.btn-add:hover { background:#5b53ee; transform:translateY(-1px); box-shadow:0 4px 20px #6c63ff44; }

.empty-state {
    text-align:center; padding:60px 20px; color:#444;
}
.empty-state .emoji { font-size:48px; display:block; margin-bottom:12px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <h1>Shop<span>Manager</span></h1>
        <p>Hệ thống quản lý bán hàng</p>
    </div>
    <nav class="sidebar-menu">
        <div class="menu-label">Quản lý</div>
        <a href="index.php" class="menu-item active">
            <span class="icon">📦</span> Sản phẩm
        </a>
        <a href="them_sp.php" class="menu-item">
            <span class="icon">➕</span> Thêm sản phẩm
        </a>
    </nav>
    <div class="sidebar-footer">v1.0 · ShopManager</div>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="page-header">
        <div>
            <h2>Sản phẩm</h2>
            <p>Quản lý toàn bộ sản phẩm trong kho</p>
        </div>
        <a href="them_sp.php" class="btn-add">＋ Thêm sản phẩm</a>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="label">Tổng sản phẩm</div>
            <div class="value"><?= $tong_sp ?></div>
            <div class="sub">Đang kinh doanh</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="label">Giá trị kho</div>
            <div class="value"><?= number_format($tong_tien/1000000, 1) ?>M</div>
            <div class="sub"><?= number_format($tong_tien,0,',','.') ?>đ</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚠️</div>
            <div class="label">Sắp hết hàng</div>
            <div class="value"><?= $sp_con_it ?></div>
            <div class="sub">Tồn kho dưới 20</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <div class="table-header">
            <h3>Danh sách sản phẩm</h3>
            <span><?= $tong_sp ?> sản phẩm</span>
        </div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="🔍  Tìm kiếm sản phẩm...">
        </div>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
            $max_sl = $conn->query("SELECT MAX(so_luong) AS m FROM sanpham")->fetch_assoc()['m'];
            $result = $conn->query("SELECT * FROM sanpham ORDER BY ngay_tao DESC");
            while ($sp = $result->fetch_assoc()):
                $badge_class = 'badge-pk';
                if ($sp['danh_muc'] == 'Quần áo') $badge_class = 'badge-ao';
                if ($sp['danh_muc'] == 'Giày dép') $badge_class = 'badge-giay';
                $stock_class = $sp['so_luong'] < 20 ? 'stock-low' : 'stock-ok';
                $bar_color   = $sp['so_luong'] < 20 ? '#f87171' : '#4ade80';
                $bar_pct     = $max_sl > 0 ? round($sp['so_luong'] / $max_sl * 100) : 0;
            ?>
                <tr>
                    <td>
                        <div class="product-name"><?= htmlspecialchars($sp['ten_sp']) ?></div>
                        <div class="product-id">#<?= $sp['id'] ?></div>
                    </td>
                    <td><span class="badge <?= $badge_class ?>"><?= $sp['danh_muc'] ?></span></td>
                    <td class="price"><?= number_format($sp['gia'],0,',','.') ?>đ</td>
                    <td>
                        <div class="stock <?= $stock_class ?>"><?= $sp['so_luong'] ?></div>
                        <div class="stock-bar">
                            <div class="stock-bar-fill" style="width:<?= $bar_pct ?>%;background:<?= $bar_color ?>"></div>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="sua_sp.php?id=<?= $sp['id'] ?>" class="btn btn-edit">✏️ Sửa</a>
                            <a href="xoa_sp.php?id=<?= $sp['id'] ?>" class="btn btn-delete"
                               onclick="return confirm('Xóa sản phẩm này?')">🗑 Xóa</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>

<script>
// Tìm kiếm realtime
document.getElementById('searchInput').addEventListener('keyup', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(row => {
        const name = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
        row.style.display = name.includes(keyword) ? '' : 'none';
    });
});
</script>
</body>
</html>