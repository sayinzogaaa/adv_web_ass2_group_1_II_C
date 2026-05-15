<?php
/**
 * app/view/request_form.php
 * Public form — includes JavaScript Live Preview panel
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Request — Umuganda Platform</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <style>
    /* ── Page layout ─────────────────────────────────── */
    .page-wrap {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1.2rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }
    @media (max-width: 768px) { .page-wrap { grid-template-columns: 1fr; } }

    /* ── Form card ───────────────────────────────────── */
    .form-card {
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0,0,0,.08);
    }
    .form-card h2 { margin-top: 0; color: #1e293b; }

    .form-group { margin-bottom: 1.1rem; }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: .88rem;
      color: #374151;
      margin-bottom: .35rem;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: .65rem .9rem;
      border: 1.5px solid #d1d5db;
      border-radius: 8px;
      font-size: .95rem;
      font-family: Arial, sans-serif;
      box-sizing: border-box;
      transition: border-color .2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #16a34a;
    }
    .form-group textarea { resize: vertical; min-height: 110px; }

    .btn-submit {
      width: 100%;
      padding: .8rem;
      background: #15803d;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: background .2s;
      margin-top: .5rem;
    }
    .btn-submit:hover { background: #166534; }

    /* ── Live Preview panel ──────────────────────────── */
    .preview-panel {
      background: #f0fdf4;
      border: 2px dashed #86efac;
      border-radius: 12px;
      padding: 1.8rem;
      position: sticky;
      top: 1.5rem;
      height: fit-content;
    }
    .preview-panel > h3 {
      margin: 0 0 .8rem;
      color: #15803d;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: .4rem;
    }
    #preview { min-height: 120px; }

    /* rows inside preview (injected by script.js) */
    .prev-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #bbf7d0;
      padding: .45rem 0;
      gap: 1rem;
    }
    .prev-row:last-child { border-bottom: none; }
    .prev-label {
      font-size: .75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #4b5563;
      min-width: 80px;
    }
    .prev-val {
      font-size: .9rem;
      color: #1e293b;
      text-align: right;
      word-break: break-word;
    }

    /* ── Flash alerts ────────────────────────────────── */
    .alert { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500; }
    .alert-error   { background: #fee2e2; color: #991b1b; }
    .alert-success { background: #dcfce7; color: #166534; }

    /* ── Back link ───────────────────────────────────── */
    .back-link { display:block; margin-top:1rem; text-align:center; color:#6b7280; font-size:.88rem; }
    .back-link a { color: #15803d; }
  </style>
</head>
<body>

  <header>
    <div style="max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;">
      <h1 style="margin:0;font-size:1.2rem;">🌿 Umuganda Platform</h1>
      <nav>
        <a href="../../public/index.php" style="color:#fff;margin-right:1rem;text-decoration:none;">Home</a>
        <a href="login.php" style="color:#fff;text-decoration:none;">Admin Login</a>
      </nav>
    </div>
  </header>

  <div class="page-wrap">

    <!-- ── FORM ──────────────────────────────────────── -->
    <div class="form-card">
      <h2>📋 Submit a Service Request</h2>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <form method="POST" action="../controller/RequestController.php" novalidate>

        <div class="form-group">
          <label for="fullname">Full Name *</label>
          <input type="text" id="fullname" name="fullname" placeholder="e.g. Mugisha Eric" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" placeholder="e.g. mugisha@ines.ac.rw" required>
        </div>

        <div class="form-group">
          <label for="category">Service Category *</label>
          <select id="category" name="category" required>
            <option value="">— Select Category —</option>
            <option value="Water Issue">Water Issue</option>
            <option value="Street Light">Street Light</option>
            <option value="Cleaning">Cleaning</option>
            <option value="ICT Support">ICT Support</option>
            <option value="Security">Security</option>
            <option value="Road & Paths">Road &amp; Paths</option>
          </select>
        </div>

        <div class="form-group">
          <label for="priority">Priority Level *</label>
          <select id="priority" name="priority" required>
            <option value="">— Select Priority —</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Description *</label>
          <textarea id="description" name="description" placeholder="Describe the issue in detail…" required></textarea>
        </div>

        <button type="submit" class="btn-submit">🚀 Submit Request</button>
      </form>

      <p class="back-link"><a href="../../public/index.php">← Back to Home</a></p>
    </div>

    <!-- ── LIVE PREVIEW ───────────────────────────────── -->
    <div class="preview-panel">
      <h3>👁 Live Preview</h3>
      <div id="preview">
        <p style="color:#94a3b8;font-style:italic;text-align:center;padding:20px 0;">
          Start filling the form to see a live preview here.
        </p>
      </div>
    </div>

  </div><!-- /.page-wrap -->

  <!-- Load the live preview script -->
  <script src="../../assets/js/script.js"></script>
</body>
</html>
