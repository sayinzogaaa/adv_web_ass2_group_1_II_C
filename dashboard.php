<?php
/**
 * app/view/dashboard.php
 * Admin Dashboard — summary stats, search & filter, status update in action
 */
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include "../../config/db.php";

/* ── Flash message ───────────────────────────────────────────────── */
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

/* ── Search & Filter inputs ──────────────────────────────────────── */
$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$status   = trim($_GET['status']   ?? '');

/* ── Build WHERE clause safely ───────────────────────────────────── */
$where  = [];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = "(fullname LIKE ? OR email LIKE ? OR description LIKE ?)";
    $like     = "%$search%";
    $params   = array_merge($params, [$like, $like, $like]);
    $types   .= 'sss';
}
if ($category !== '') {
    $where[]  = "category = ?";
    $params[] = $category;
    $types   .= 's';
}
if ($status !== '') {
    $where[]  = "status = ?";
    $params[] = $status;
    $types   .= 's';
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ── Fetch filtered requests ─────────────────────────────────────── */
$sql  = "SELECT * FROM requests $whereSQL ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ── Summary counts (always unfiltered) ──────────────────────────── */
$counts = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(status='Pending')     AS pending,
        SUM(status='In Progress') AS in_progress,
        SUM(status='Resolved')    AS resolved
    FROM requests
")->fetch_assoc();

/* ── Distinct categories for filter dropdown ─────────────────────── */
$cats = $conn->query("SELECT DISTINCT category FROM requests ORDER BY category")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Umuganda Platform</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    body { background: #f1f5f9; font-family: Arial, sans-serif; margin: 0; }

    /* ── Top bar ─────────────────────────────────────── */
    .topbar {
      background: #15803d;
      color: #fff;
      padding: .9rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .topbar h2 { margin: 0; font-size: 1.2rem; }
    .topbar a  { color: #d1fae5; text-decoration: none; }
    .topbar a:hover { text-decoration: underline; }

    /* ── Wrapper ─────────────────────────────────────── */
    .wrap { max-width: 1200px; margin: 1.5rem auto; padding: 0 1.2rem; }

    /* ── Summary cards ───────────────────────────────── */
    .stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.8rem; }
    @media (max-width:640px){ .stats { grid-template-columns: repeat(2,1fr); } }

    .stat-card {
      background: #fff;
      border-radius: 10px;
      padding: 1.2rem 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,.07);
      border-left: 5px solid;
    }
    .stat-card.total    { border-color: #6366f1; }
    .stat-card.pending  { border-color: #f59e0b; }
    .stat-card.progress { border-color: #3b82f6; }
    .stat-card.resolved { border-color: #22c55e; }
    .stat-card .num   { font-size: 2rem; font-weight: 800; color: #1e293b; }
    .stat-card .label { font-size: .8rem; color: #6b7280; margin-top: .2rem; }

    /* ── Flash ───────────────────────────────────────── */
    .alert { padding: .8rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
    .alert-success { background: #dcfce7; color: #166534; }

    /* ── Filter bar ──────────────────────────────────── */
    .filter-bar {
      background: #fff;
      border-radius: 10px;
      padding: 1.2rem 1.5rem;
      box-shadow: 0 2px 8px rgba(0,0,0,.07);
      margin-bottom: 1.5rem;
    }
    .filter-bar form {
      display: flex; flex-wrap: wrap; gap: .8rem; align-items: flex-end;
    }
    .fg { display: flex; flex-direction: column; gap: .25rem; }
    .fg label { font-size: .75rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .04em; }
    .fg input, .fg select {
      padding: .55rem .9rem;
      border: 1.5px solid #d1d5db;
      border-radius: 7px;
      font-size: .9rem;
      min-width: 155px;
      font-family: Arial, sans-serif;
    }
    .fg input:focus, .fg select:focus { outline: none; border-color: #16a34a; }
    .btn-filter, .btn-reset {
      padding: .58rem 1.2rem;
      border: none;
      border-radius: 7px;
      font-weight: 700;
      cursor: pointer;
      font-size: .9rem;
      align-self: flex-end;
      text-decoration: none;
    }
    .btn-filter { background: #15803d; color: #fff; }
    .btn-filter:hover { background: #166534; }
    .btn-reset  { background: #e5e7eb; color: #374151; display: inline-block; line-height: 1.4; }
    .btn-reset:hover { background: #d1d5db; }

    /* ── Results info ────────────────────────────────── */
    .results-info { font-size: .85rem; color: #6b7280; margin-bottom: .7rem; }

    /* ── Table ───────────────────────────────────────── */
    .tbl-wrap {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,.07);
      overflow-x: auto;
    }
    table { width: 100%; border-collapse: collapse; font-size: .9rem; }
    thead th {
      background: #f8fafc;
      padding: .85rem 1rem;
      text-align: left;
      font-size: .75rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #6b7280;
      border-bottom: 1px solid #e2e8f0;
    }
    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr:hover { background: #f8fafc; }
    tbody td { padding: .75rem 1rem; vertical-align: middle; }

    /* ── Status badges ───────────────────────────────── */
    .badge {
      display: inline-block;
      padding: .2rem .75rem;
      border-radius: 999px;
      font-size: .78rem;
      font-weight: 700;
    }
    .b-pending   { background: #fef9c3; color: #854d0e; }
    .b-progress  { background: #dbeafe; color: #1e40af; }
    .b-resolved  { background: #dcfce7; color: #166534; }

    /* ── Priority badges ─────────────────────────────── */
    .pri { display: inline-block; padding: .15rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .p-low    { background: #dcfce7; color: #166534; }
    .p-medium { background: #fef9c3; color: #854d0e; }
    .p-high   { background: #fee2e2; color: #991b1b; }

    /* ── Status update mini-form ─────────────────────── */
    .upd-form { display: flex; gap: .45rem; align-items: center; }
    .upd-form select {
      padding: .3rem .5rem;
      border: 1.5px solid #d1d5db;
      border-radius: 6px;
      font-size: .82rem;
      font-family: Arial, sans-serif;
    }
    .btn-upd {
      padding: .3rem .85rem;
      background: #15803d;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: .82rem;
      font-weight: 700;
      cursor: pointer;
      white-space: nowrap;
    }
    .btn-upd:hover { background: #166534; }

    /* ── Empty state ─────────────────────────────────── */
    .empty { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
  </style>
</head>
<body>

  <!-- ── Top bar ─────────────────────────────────────── -->
  <div class="topbar">
    <h2>🌿 Umuganda — Admin Dashboard</h2>
    <div>
      Welcome, <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong>
      &nbsp;|&nbsp;
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="wrap">

    <!-- ── Flash ────────────────────────────────────── -->
    <?php if ($flash): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <!-- ── Summary stats ────────────────────────────── -->
    <div class="stats">
      <div class="stat-card total">
        <div class="num"><?= (int)$counts['total'] ?></div>
        <div class="label">Total Requests</div>
      </div>
      <div class="stat-card pending">
        <div class="num"><?= (int)$counts['pending'] ?></div>
        <div class="label">Pending</div>
      </div>
      <div class="stat-card progress">
        <div class="num"><?= (int)$counts['in_progress'] ?></div>
        <div class="label">In Progress</div>
      </div>
      <div class="stat-card resolved">
        <div class="num"><?= (int)$counts['resolved'] ?></div>
        <div class="label">Resolved</div>
      </div>
    </div>

    <!-- ── Search & Filter bar ──────────────────────── -->
    <div class="filter-bar">
      <form method="GET" action="">

        <div class="fg">
          <label for="search">🔍 Search</label>
          <input type="text" id="search" name="search"
                 placeholder="Name, email, description…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="fg">
          <label for="category">Category</label>
          <select id="category" name="category">
            <option value="">All Categories</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= htmlspecialchars($c['category']) ?>"
                <?= $category === $c['category'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['category']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="fg">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="">All Statuses</option>
            <option value="Pending"     <?= $status==='Pending'     ? 'selected':'' ?>>Pending</option>
            <option value="In Progress" <?= $status==='In Progress' ? 'selected':'' ?>>In Progress</option>
            <option value="Resolved"    <?= $status==='Resolved'    ? 'selected':'' ?>>Resolved</option>
          </select>
        </div>

        <button type="submit" class="btn-filter">Apply Filter</button>
        <a href="dashboard.php" class="btn-reset">Reset</a>
      </form>
    </div>

    <!-- ── Results count ────────────────────────────── -->
    <div class="results-info">
      Showing <strong><?= count($requests) ?></strong> request(s)
      <?php if ($search || $category || $status): ?>
        — filtered by:
        <?php if ($search):   echo " search \"<em>".htmlspecialchars($search)."</em>\""; endif; ?>
        <?php if ($category): echo " category \"<em>".htmlspecialchars($category)."</em>\""; endif; ?>
        <?php if ($status):   echo " status \"<em>".htmlspecialchars($status)."</em>\""; endif; ?>
      <?php endif; ?>
    </div>

    <!-- ── Requests table ───────────────────────────── -->
    <div class="tbl-wrap">
      <?php if (empty($requests)): ?>
        <div class="empty">
          <p style="font-size:2rem;">📭</p>
          <p>No requests match your filter criteria.</p>
        </div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name / Email</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Description</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Update Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($requests as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($row['fullname']) ?></strong><br>
              <small style="color:#6b7280"><?= htmlspecialchars($row['email']) ?></small>
            </td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>
              <?php
                $pc = ['Low'=>'p-low','Medium'=>'p-medium','High'=>'p-high'][$row['priority']] ?? '';
              ?>
              <span class="pri <?= $pc ?>"><?= htmlspecialchars($row['priority']) ?></span>
            </td>
            <td style="max-width:200px;word-break:break-word;">
              <?= htmlspecialchars(mb_strimwidth($row['description'], 0, 75, '…')) ?>
            </td>
            <td>
              <?php
                $bc = ['Pending'=>'b-pending','In Progress'=>'b-progress','Resolved'=>'b-resolved'][$row['status']] ?? '';
              ?>
              <span class="badge <?= $bc ?>"><?= htmlspecialchars($row['status']) ?></span>
            </td>
            <td style="white-space:nowrap;font-size:.82rem;color:#6b7280;">
              <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
            </td>
            <td>
              <!-- Status update mini-form -->
              <form class="upd-form" method="POST"
                    action="../controller/RequestController.php?action=update_status">
                <input type="hidden" name="request_id" value="<?= (int)$row['id'] ?>">
                <!-- Pass active filters so they survive the redirect -->
                <input type="hidden" name="filter_search"   value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="filter_category" value="<?= htmlspecialchars($category) ?>">
                <input type="hidden" name="filter_status"   value="<?= htmlspecialchars($status) ?>">
                <select name="new_status">
                  <option value="Pending"     <?= $row['status']==='Pending'     ? 'selected':'' ?>>Pending</option>
                  <option value="In Progress" <?= $row['status']==='In Progress' ? 'selected':'' ?>>In Progress</option>
                  <option value="Resolved"    <?= $row['status']==='Resolved'    ? 'selected':'' ?>>Resolved</option>
                </select>
                <button type="submit" class="btn-upd">Save</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  </div><!-- /.wrap -->
</body>
</html>
