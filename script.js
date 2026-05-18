/**
 * script.js — Umuganda Smart Service Request Platform
 * Feature: JavaScript Live Preview (real-time, no button needed)
 */

document.addEventListener("DOMContentLoaded", function () {

  // Priority badge colours
  const priorityColors = {
    Low:    { bg: "#dcfce7", color: "#166534" },
    Medium: { bg: "#fef9c3", color: "#854d0e" },
    High:   { bg: "#fee2e2", color: "#991b1b" },
  };

  // Grab all form fields (match the name attributes in request_form.php)
  const fields = ["fullname", "email", "category", "priority", "description"];

  function updatePreview() {
    const fullname    = document.getElementById("fullname")?.value.trim()    || "";
    const email       = document.getElementById("email")?.value.trim()        || "";
    const category    = document.getElementById("category")?.value            || "";
    const priority    = document.getElementById("priority")?.value            || "";
    const description = document.getElementById("description")?.value.trim()  || "";

    const previewBox = document.getElementById("preview");
    if (!previewBox) return;

    // Hide preview until user starts typing
    const hasContent = fullname || email || category || priority || description;
    if (!hasContent) {
      previewBox.innerHTML = `<p style="color:#94a3b8;font-style:italic;text-align:center;padding:20px 0;">
        Start filling the form to see a live preview here.</p>`;
      return;
    }

    // Priority badge
    const pc = priorityColors[priority] || { bg: "#e5e7eb", color: "#374151" };
    const priorityBadge = priority
      ? `<span style="background:${pc.bg};color:${pc.color};padding:3px 12px;border-radius:999px;font-size:.8rem;font-weight:700;">${priority}</span>`
      : "<em style='color:#94a3b8'>—</em>";

    // Timestamp
    const now = new Date().toLocaleString("en-RW", { dateStyle: "medium", timeStyle: "short" });

    previewBox.innerHTML = `
      <div style="border-bottom:2px solid #86efac;padding-bottom:10px;margin-bottom:14px;">
        <h3 style="margin:0;color:#15803d;font-size:1rem;">📋 Request Preview</h3>
        <span style="font-size:.75rem;color:#6b7280;">Updates as you type</span>
      </div>

      <div class="prev-row">
        <span class="prev-label">Name</span>
        <span class="prev-val">${escHtml(fullname) || "<em style='color:#94a3b8'>—</em>"}</span>
      </div>
      <div class="prev-row">
        <span class="prev-label">Email</span>
        <span class="prev-val">${escHtml(email) || "<em style='color:#94a3b8'>—</em>"}</span>
      </div>
      <div class="prev-row">
        <span class="prev-label">Category</span>
        <span class="prev-val">${escHtml(category) || "<em style='color:#94a3b8'>—</em>"}</span>
      </div>
      <div class="prev-row">
        <span class="prev-label">Priority</span>
        <span class="prev-val">${priorityBadge}</span>
      </div>
      <div class="prev-row" style="align-items:flex-start;">
        <span class="prev-label">Description</span>
        <span class="prev-val" style="font-style:italic;white-space:pre-wrap;">${escHtml(description) || "<em style='color:#94a3b8'>—</em>"}</span>
      </div>
      <div class="prev-row">
        <span class="prev-label">Status</span>
        <span class="prev-val">
          <span style="background:#fef9c3;color:#854d0e;padding:3px 12px;border-radius:999px;font-size:.8rem;font-weight:700;">⏳ Pending</span>
        </span>
      </div>
      <div class="prev-row">
        <span class="prev-label">Timestamp</span>
        <span class="prev-val" style="font-size:.82rem;color:#6b7280;">${now}</span>
      </div>
    `;
  }

  // Escape HTML to prevent XSS in the preview
  function escHtml(str) {
    const d = document.createElement("div");
    d.textContent = str;
    return d.innerHTML;
  }

  // Attach live listeners to every field
  fields.forEach(function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    const evt = (el.tagName === "SELECT") ? "change" : "input";
    el.addEventListener(evt, updatePreview);
  });

  // Run once on load (handles browser autofill)
  updatePreview();
});
