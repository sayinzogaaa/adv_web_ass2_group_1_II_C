<?php
/**
 * app/controller/RequestController.php
 * Handles:
 *   POST /RequestController.php              → submit new request
 *   POST /RequestController.php?action=update_status → update status
 */
session_start();

include "../../config/db.php";

$action = $_GET['action'] ?? 'submit';

/* ════════════════════════════════════════════════════════════
   ACTION 1: Submit a new service request (public form)
════════════════════════════════════════════════════════════ */
if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname    = trim($_POST['fullname']    ?? '');
    $email       = trim($_POST['email']       ?? '');
    $category    = trim($_POST['category']    ?? '');
    $priority    = trim($_POST['priority']    ?? '');
    $description = trim($_POST['description'] ?? '');

    // ── Validate ──────────────────────────────────────────
    if (empty($fullname) || empty($email) || empty($category) || empty($priority) || empty($description)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../../app/view/request_form.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: ../../app/view/request_form.php");
        exit();
    }
    if (!in_array($priority, ['Low','Medium','High'])) {
        $_SESSION['error'] = "Invalid priority selected.";
        header("Location: ../../app/view/request_form.php");
        exit();
    }

    // ── Insert ────────────────────────────────────────────
    $sql  = "INSERT INTO requests (fullname, email, category, priority, description, status, created_at)
             VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullname, $email, $category, $priority, $description);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Your request was submitted successfully!";
        header("Location: ../../public/index.php");
    } else {
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: ../../app/view/request_form.php");
    }
    $stmt->close();
    exit();
}

/* ════════════════════════════════════════════════════════════
   ACTION 2: Update request status (admin dashboard)
════════════════════════════════════════════════════════════ */
if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Must be logged-in admin
    if (!isset($_SESSION['admin'])) {
        header("Location: ../../app/view/login.php");
        exit();
    }

    $id        = intval($_POST['request_id'] ?? 0);
    $newStatus = trim($_POST['new_status']   ?? '');
    $allowed   = ['Pending', 'In Progress', 'Resolved'];

    if ($id <= 0 || !in_array($newStatus, $allowed)) {
        $_SESSION['flash'] = "❌ Invalid update request.";
        header("Location: ../../app/view/dashboard.php");
        exit();
    }

    $sql  = "UPDATE requests SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['flash'] = "✅ Request #$id updated to \"$newStatus\".";
    } else {
        $_SESSION['flash'] = "⚠️ No change made (status may already be set to \"$newStatus\").";
    }
    $stmt->close();

    // Preserve active filters on redirect back to dashboard
    $qs = http_build_query([
        'search'   => $_POST['filter_search']   ?? '',
        'category' => $_POST['filter_category'] ?? '',
        'status'   => $_POST['filter_status']   ?? '',
    ]);
    header("Location: ../../app/view/dashboard.php" . ($qs ? "?$qs" : ''));
    exit();
}

/* ── Fallback ─────────────────────────────────────────────── */
header("Location: ../../public/index.php");
exit();
