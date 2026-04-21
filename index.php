<?php
// ============================================================
//  index.php — DOCVAULT Front-end Entry Point
//
//  Session check: if already logged in, pass user data to JS
//  so the dashboard loads immediately without re-entering creds.
// ============================================================
session_start();

$sessionUser = null;
if (!empty($_SESSION['docvault_user'])) {
    // Sanitise before embedding into HTML/JS context
    $sessionUser = json_encode($_SESSION['docvault_user'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DOCVAULT — Document Storage</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --black: #0d0d0d;
  --white: #f5f4f0;
  --g1: #1a1a1a;
  --g2: #2e2e2e;
  --g3: #474747;
  --g4: #7a7a7a;
  --g5: #b8b8b8;
  --g6: #dcdcdc;
  --g7: #efefef;
  --red: #c0392b;
  --green: #1a7a4a;
  --fd: 'Bebas Neue', sans-serif;
  --fm: 'IBM Plex Mono', monospace;
  --fb: 'IBM Plex Sans', sans-serif;
  --t: 0.18s ease;
}
html.dark {
  --black: #e8e6e0;
  --white: #111113;
  --g1: #1c1c1f;
  --g2: #252528;
  --g3: #3a3a3e;
  --g4: #8a8a92;
  --g5: #5a5a62;
  --g6: #2e2e33;
  --g7: #1c1c1f;
  --red: #e05c4a;
  --green: #2ea865;
}
html, html.dark { color-scheme: light; }
html.dark { color-scheme: dark; }
html { scroll-behavior: smooth; }

/* ── DARK MODE TARGETED FIXES ── */
.login-left { background: #0d0d0d !important; }
.brand-name { color: #f5f4f0 !important; }
.login-headline h1 { color: #f5f4f0 !important; }
.login-headline h1 em { color: #7a7a7a !important; }
.login-tagline { color: #7a7a7a !important; }
.ll-bottom { color: #7a7a7a !important; border-top-color: #2e2e2e !important; }
.feat-tag { border-color: #2e2e2e !important; color: #7a7a7a !important; }
.ds-ext { color: #f5f4f0 !important; }
html.dark .login-right { background: var(--white); border-left-color: var(--g6); }
html.dark .login-right::before { border-color: var(--g6); }
.navbar-search input { background: rgba(255,255,255,0.07) !important; border-color: rgba(255,255,255,0.12) !important; color: #f5f4f0 !important; }
.navbar-search input::placeholder { color: rgba(255,255,255,0.3) !important; }
.search-icon-w svg { stroke: rgba(255,255,255,0.3) !important; }
html.dark .upload-zone { background: repeating-linear-gradient(-45deg,transparent,transparent 8px,rgba(255,255,255,0.02) 8px,rgba(255,255,255,0.02) 16px); }
html.dark .folder-icon svg { stroke: var(--g4); }
html.dark .modal { box-shadow: 8px 8px 0 rgba(0,0,0,0.7); }
html.dark .nf-modal { box-shadow: 6px 6px 0 rgba(0,0,0,0.5); }
html.dark ::-webkit-scrollbar { width: 6px; height: 6px; }
html.dark ::-webkit-scrollbar-track { background: var(--g1); }
html.dark ::-webkit-scrollbar-thumb { background: var(--g3); }
html.dark * { scrollbar-color: var(--g3) var(--g1); }
body { font-family: var(--fb); background: var(--white); color: var(--black); min-height: 100vh; overflow-x: hidden; line-height: 1.5; transition: background 0.22s ease, color 0.22s ease; }
.theme-toggle { height: 100%; padding: 0 16px; border-left: 1px solid var(--g2); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background var(--t); color: var(--g4); background: none; border-top: none; border-bottom: none; border-right: none; width: 48px; position: relative; }
.theme-toggle:hover { background: var(--g1); color: var(--white); }
.theme-toggle svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.5; position: absolute; transition: opacity 0.22s ease, transform 0.3s ease; }
.theme-toggle .icon-sun { opacity: 1; transform: rotate(0deg) scale(1); }
.theme-toggle .icon-moon { opacity: 0; transform: rotate(90deg) scale(0.6); }
html.dark .theme-toggle .icon-sun { opacity: 0; transform: rotate(-90deg) scale(0.6); }
html.dark .theme-toggle .icon-moon { opacity: 1; transform: rotate(0deg) scale(1); }
.folder-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(160px,1fr)); gap: 0; border: 1px solid var(--g6); margin-bottom: 2px; }
.folder-card { padding: 14px 16px; border-right: 1px solid var(--g6); border-bottom: 1px solid var(--g6); display: flex; align-items: center; gap: 11px; cursor: pointer; transition: background var(--t); position: relative; }
.folder-card:hover { background: var(--g7); }
.folder-icon { width: 32px; height: 32px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.folder-icon svg { width: 26px; height: 26px; stroke: var(--g4); fill: none; stroke-width: 1.5; transition: stroke var(--t); }
.folder-card:hover .folder-icon svg { stroke: var(--black); }
.folder-info { flex: 1; min-width: 0; }
.folder-name { font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--black); }
.folder-count { font-family: var(--fm); font-size: 9px; color: var(--g4); margin-top: 2px; }
.folder-actions { position: absolute; top: 6px; right: 6px; display: flex; gap: 3px; opacity: 0; transition: opacity var(--t); }
.folder-card:hover .folder-actions { opacity: 1; }
.folder-action-btn { width: 20px; height: 20px; background: var(--black); border: none; color: var(--white); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background var(--t); }
.folder-action-btn.del:hover { background: var(--red); }
.folder-action-btn:not(.del):hover { background: var(--g2); }
.folder-action-btn svg { width: 9px; height: 9px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.fc-swatch { width: 20px; height: 20px; border: 2px solid transparent; border-radius: 0; cursor: pointer; transition: all 0.15s ease; outline: none; flex-shrink: 0; }
.fc-swatch.active { border-color: var(--black); box-shadow: 0 0 0 1px var(--black); }
html.dark .fc-swatch.active { border-color: var(--white); box-shadow: 0 0 0 1px var(--white); }
.nf-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 400; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.18s ease; }
.nf-modal-overlay.show { opacity: 1; pointer-events: all; }
.nf-modal { background: var(--white); border: 1.5px solid var(--g6); width: 380px; padding: 28px 30px; position: relative; transform: translateY(8px); transition: transform 0.18s ease; }
.nf-modal-overlay.show .nf-modal { transform: translateY(0); }
.nf-modal h3 { font-family: var(--fd); font-size: 24px; letter-spacing: 1px; color: var(--black); margin-bottom: 6px; }
.nf-modal p { font-family: var(--fm); font-size: 10px; color: var(--g4); letter-spacing: 0.5px; margin-bottom: 18px; }
.nf-input { width: 100%; background: var(--white); border: 1.5px solid var(--g6); padding: 10px 13px; color: var(--black); font-family: var(--fm); font-size: 13px; outline: none; transition: border-color var(--t); border-radius: 0; margin-bottom: 18px; }
.nf-input:focus { border-color: var(--black); }
.nf-input::placeholder { color: var(--g5); }
.nf-footer { display: flex; gap: 8px; justify-content: flex-end; }
.nf-cancel { padding: 9px 18px; background: none; border: 1.5px solid var(--g5); color: var(--g4); font-family: var(--fm); font-size: 11px; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: all var(--t); }
.nf-cancel:hover { border-color: var(--black); color: var(--black); }
.nf-create { padding: 9px 18px; background: var(--black); border: 1.5px solid var(--black); color: var(--white); font-family: var(--fm); font-size: 11px; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: background var(--t); }
.nf-create:hover { background: var(--g1); }
.folder-section-hd { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.folder-section-label { font-family: var(--fm); font-size: 9px; font-weight: 600; color: var(--g4); letter-spacing: 2px; text-transform: uppercase; }
.breadcrumb { display: flex; align-items: center; gap: 6px; font-family: var(--fm); font-size: 11px; color: var(--g4); margin-bottom: 14px; }
.breadcrumb span { cursor: pointer; transition: color var(--t); }
.breadcrumb span:hover { color: var(--black); }
.breadcrumb .bc-sep { color: var(--g5); cursor: default; }
.breadcrumb .bc-current { color: var(--black); font-weight: 600; cursor: default; }
* { cursor: default; }
button, a, [onclick], label, .sidebar-item, .filter-chip, .doc-card, .doc-list-item, .demo-fill, .tab-btn { cursor: pointer !important; }
input, textarea { cursor: text !important; }
.page { display: none; min-height: 100vh; }
.page.active { display: flex; }

/* TOAST */
#toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.toast { background: var(--black); border: 1px solid var(--g3); padding: 10px 16px; font-size: 12px; font-family: var(--fm); color: var(--white); display: flex; align-items: center; gap: 10px; opacity: 0; transform: translateY(10px); transition: all 0.22s ease; pointer-events: all; max-width: 300px; letter-spacing: 0.3px; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.hide { opacity: 0; transform: translateY(10px); }
.toast-dot { width: 6px; height: 6px; flex-shrink: 0; }
.toast.success .toast-dot { background: var(--green); }
.toast.error .toast-dot { background: var(--red); }
.toast.info .toast-dot { background: var(--g5); }

/* ── DARK MODE OVERRIDES ── */
html.dark body { background: #111113; color: #e8e6e0; }
html.dark .login-right { background: #18181b; border-color: #2e2e33; }
html.dark .form-input { background: #18181b; border-color: #2e2e33; color: #e8e6e0; }
html.dark .form-input:focus { border-color: #e8e6e0; }
html.dark .tab-switch { border-color: #2e2e33; }
html.dark .tab-btn { border-color: #2e2e33; color: #5a5a62; }
html.dark .tab-btn.active { background: #e8e6e0; color: #111113; }
html.dark .tab-btn:not(.active):hover { background: #1c1c1f; color: #e8e6e0; }
html.dark .demo-accounts { border-color: #2e2e33; }
html.dark .demo-account { border-color: #252528; }
html.dark .demo-fill { border-color: #3a3a3e; color: #5a5a62; }
html.dark .demo-fill:hover { background: #e8e6e0; color: #111113; border-color: #e8e6e0; }
html.dark .login-right::before { border-color: #2e2e33; }
html.dark .sidebar { background: #111113; border-color: #2e2e33; }
html.dark .sidebar-item { color: #3a3a3e; }
html.dark .sidebar-item:hover { background: #1c1c1f; color: #e8e6e0; }
html.dark .sidebar-item.active { background: #e8e6e0; color: #111113; }
html.dark .sidebar-item.active .si-count { background: #c8c8c8; color: #222; }
html.dark .si-count { background: #1c1c1f; color: #3a3a3e; }
html.dark .sb-sep { background: #2e2e33; }
html.dark .sidebar-storage { border-color: #2e2e33; }
html.dark .storage-bar { background: #1c1c1f; }
html.dark .main-content { background: #111113; }
html.dark .content-header { border-color: #e8e6e0; }
html.dark .stats-grid { border-color: #2e2e33; }
html.dark .stat-card { border-color: #2e2e33; }
html.dark .sc-icon { border-color: #2e2e33; }
html.dark .sc-badge.neu { background: #1c1c1f; color: #5a5a62; }
html.dark .upload-zone { border-color: #3a3a3e; }
html.dark .upload-zone:hover, html.dark .upload-zone.drag-over { border-color: #e8e6e0; background: #1c1c1f; }
html.dark .uz-type { border-color: #2e2e33; color: #5a5a62; }
html.dark .queue-item { border-color: #2e2e33; background: #18181b; }
html.dark .qi-pb { background: #1c1c1f; }
html.dark .filter-bar { border-color: #2e2e33; }
html.dark .filter-chip { border-color: #2e2e33; color: #5a5a62; }
html.dark .filter-chip:hover { background: #1c1c1f; color: #e8e6e0; }
html.dark .filter-chip.active { background: #e8e6e0; color: #111113; }
html.dark .docs-grid { border-color: #2e2e33; }
html.dark .doc-card { border-color: #2e2e33; background: #18181b; }
html.dark .doc-card:hover { background: #1c1c1f; }
html.dark .dc-preview { border-color: #2e2e33; background: #111113; }
html.dark .doc-card:hover .dc-preview { border-color: #e8e6e0; }
html.dark .docs-list { border-color: #2e2e33; }
html.dark .doc-list-item { border-color: #2e2e33; }
html.dark .doc-list-item:hover { background: #1c1c1f; }
html.dark .dli-ext { border-color: #2e2e33; }
html.dark .dli-btn { border-color: #3a3a3e; color: #5a5a62; }
html.dark .dli-btn:hover { background: #e8e6e0; color: #111113; border-color: #e8e6e0; }
html.dark .empty-state { border-color: #2e2e33; }
html.dark .es-mark { border-color: #2e2e33; }
html.dark .view-toggle { border-color: #3a3a3e; }
html.dark .view-btn { border-color: #3a3a3e; color: #5a5a62; }
html.dark .view-btn.active { background: #e8e6e0; color: #111113; }
html.dark .view-btn:not(.active):hover { background: #1c1c1f; color: #e8e6e0; }
html.dark .btn-outline { border-color: #3a3a3e; color: #5a5a62; }
html.dark .btn-outline:hover { border-color: #e8e6e0; color: #e8e6e0; background: #1c1c1f; }
html.dark .btn-solid { background: #e8e6e0; color: #111113; border-color: #e8e6e0; }
html.dark .btn-solid:hover { background: #c8c6c0; }
html.dark .modal-overlay { background: rgba(0,0,0,0.72); }
html.dark .modal { background: #18181b; border-color: #2e2e33; }
html.dark .modal-header { border-color: #2e2e33; }
html.dark .modal-preview { background: #111113; border-color: #2e2e33; }
html.dark .modal-info .mi-row { border-color: #2e2e33; }
html.dark .modal-footer { border-color: #2e2e33; }
html.dark .mf-cancel { border-color: #3a3a3e; color: #5a5a62; }
html.dark .mf-cancel:hover { border-color: #e8e6e0; color: #e8e6e0; }
html.dark .mf-download { background: #e8e6e0; color: #111113; }
html.dark .mf-download:hover { background: #c8c6c0; }
html.dark .user-dropdown { background: #0a0a0c; border-color: #252528; }
html.dark .ud-info { border-color: #252528; }
html.dark .ud-item { border-color: #252528; color: #5a5a62; }
html.dark .ud-item:hover { background: #1c1c1f; color: #e8e6e0; }
html.dark .ud-item.danger { color: #e05c4a; }
html.dark .toast { background: #18181b; border-color: #2e2e33; }
html.dark .folder-grid { border-color: #2e2e33; }
html.dark .folder-card { border-color: #2e2e33; background: #18181b; }
html.dark .folder-card:hover { background: #1c1c1f; }
html.dark .nf-modal { background: #18181b; border-color: #2e2e33; }
html.dark .nf-input { background: #18181b; border-color: #2e2e33; color: #e8e6e0; }
html.dark .nf-input:focus { border-color: #e8e6e0; }
html.dark .nf-cancel { border-color: #3a3a3e; color: #5a5a62; }
html.dark .nf-cancel:hover { border-color: #e8e6e0; color: #e8e6e0; }
html.dark .nf-create { background: #e8e6e0; color: #111113; border-color: #e8e6e0; }
html.dark .nf-create:hover { background: #c8c6c0; }
html.dark #move-doc-list label { color: #e8e6e0; }
html.dark #move-doc-list label:hover { background: #1c1c1f; }
html.dark #move-doc-list { border-color: #2e2e33; }

/* ── LOGIN ── */
#login-page { flex-direction: row; align-items: stretch; min-height: 100vh; }
.login-left { flex: 1; background: var(--black); display: flex; flex-direction: column; justify-content: space-between; padding: 48px 56px; position: relative; overflow: hidden; }
.login-left::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px); background-size: 44px 44px; pointer-events: none; }
.brand-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 80px; position: relative; z-index: 1; }
.brand-mark { width: 40px; height: 40px; border: 1.5px solid var(--white); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.brand-mark svg { width: 20px; height: 20px; }
.brand-name { font-family: var(--fd); font-size: 24px; letter-spacing: 4px; color: var(--white); }
.login-headline { position: relative; z-index: 1; }
.login-headline h1 { font-family: var(--fd); font-size: clamp(56px, 7vw, 92px); line-height: 0.92; letter-spacing: 2px; color: var(--white); margin-bottom: 28px; }
.login-headline h1 em { font-style: normal; color: var(--g4); }
.login-tagline { color: var(--g5); font-size: 12px; font-family: var(--fm); letter-spacing: 0.5px; line-height: 1.8; max-width: 340px; margin-bottom: 40px; }
.feat-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.feat-tag { border: 1px solid var(--g3); padding: 5px 11px; font-family: var(--fm); font-size: 10px; color: var(--g5); letter-spacing: 0.5px; }
.doc-stack { position: absolute; right: 48px; bottom: 80px; display: flex; flex-direction: column; gap: 6px; opacity: 0.45; z-index: 1; }
.ds-item { display: flex; align-items: center; gap: 10px; padding: 7px 13px; border: 1px solid var(--g3); width: 190px; }
.ds-ext { font-family: var(--fm); font-size: 10px; font-weight: 600; color: var(--white); letter-spacing: 1px; min-width: 32px; }
.ds-bar { flex: 1; height: 2px; background: var(--g3); position: relative; overflow: hidden; }
.ds-bar-fill { position: absolute; left: 0; top: 0; bottom: 0; background: var(--white); }
.ds-status { font-family: var(--fm); font-size: 9px; color: var(--g4); letter-spacing: 0.5px; }
.ll-bottom { position: relative; z-index: 1; border-top: 1px solid var(--g3); padding-top: 20px; font-family: var(--fm); font-size: 11px; color: var(--g4); display: flex; justify-content: space-between; }
.login-right { width: 460px; flex-shrink: 0; background: var(--white); display: flex; flex-direction: column; justify-content: center; padding: 60px 50px; border-left: 1.5px solid var(--g6); position: relative; }
.login-right::before { content: ''; position: absolute; top: 0; right: 0; width: 56px; height: 56px; border-bottom: 1.5px solid var(--g6); border-left: 1.5px solid var(--g6); }
.form-header { margin-bottom: 32px; }
.fh-label { font-family: var(--fm); font-size: 10px; font-weight: 600; letter-spacing: 2px; color: var(--g4); text-transform: uppercase; margin-bottom: 6px; }
.form-header h2 { font-family: var(--fd); font-size: 44px; letter-spacing: 1px; color: var(--black); line-height: 1; }
.tab-switch { display: flex; border: 1.5px solid var(--g6); margin-bottom: 28px; }
.tab-btn { flex: 1; padding: 10px 0; border: none; background: transparent; color: var(--g4); font-family: var(--fm); font-size: 11px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; transition: all var(--t); border-right: 1px solid var(--g6); }
.tab-btn:last-child { border-right: none; }
.tab-btn.active { background: var(--black); color: var(--white); }
.tab-btn:not(.active):hover { background: var(--g7); color: var(--black); }
.form-group { margin-bottom: 15px; }
.form-label { display: block; font-family: var(--fm); font-size: 10px; font-weight: 600; color: var(--g4); letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 6px; }
.form-input { width: 100%; background: var(--white); border: 1.5px solid var(--g6); padding: 11px 14px; color: var(--black); font-family: var(--fm); font-size: 14px; outline: none; transition: border-color var(--t); border-radius: 0; -webkit-appearance: none; }
.form-input:focus { border-color: var(--black); }
.form-input::placeholder { color: var(--g5); }
.input-row { position: relative; }
.input-row .form-input { padding-right: 44px; }
.input-eye { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--g4); padding: 4px; transition: color var(--t); display: flex; align-items: center; }
.input-eye:hover { color: var(--black); }
.input-eye svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.form-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.form-check { display: flex; align-items: center; gap: 8px; font-family: var(--fm); font-size: 11px; color: var(--g4); user-select: none; }
.form-check input { width: 13px; height: 13px; accent-color: var(--black); }
.forgot-link { font-family: var(--fm); font-size: 11px; color: var(--g4); text-decoration: none; border-bottom: 1px solid var(--g5); padding-bottom: 1px; transition: color var(--t); }
.forgot-link:hover { color: var(--black); border-color: var(--black); }
.btn-primary { width: 100%; padding: 14px; background: var(--black); border: none; color: var(--white); font-family: var(--fd); font-size: 22px; letter-spacing: 4px; cursor: pointer; transition: background var(--t); }
.btn-primary:hover { background: var(--g1); }
.btn-primary:active { transform: translateY(1px); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.strength-bar { height: 2px; background: var(--g6); margin-top: 6px; }
.strength-fill { height: 100%; transition: all 0.3s ease; }
.strength-label { font-family: var(--fm); font-size: 10px; margin-top: 4px; letter-spacing: 0.5px; }
.divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; font-family: var(--fm); font-size: 10px; color: var(--g5); letter-spacing: 1px; text-transform: uppercase; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--g6); }
.demo-accounts { border: 1px solid var(--g6); padding: 14px 16px; }
.demo-accounts > p { font-family: var(--fm); font-size: 10px; color: var(--g4); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
.demo-account { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--g7); }
.demo-account:last-child { border-bottom: none; padding-bottom: 0; }
.demo-account-name { font-size: 13px; font-weight: 500; color: var(--black); }
.demo-account-email { font-family: var(--fm); font-size: 10px; color: var(--g4); margin-top: 2px; }
.demo-fill { background: none; border: 1px solid var(--g5); color: var(--g4); font-family: var(--fm); font-size: 10px; letter-spacing: 1px; padding: 5px 10px; text-transform: uppercase; transition: all var(--t); }
.demo-fill:hover { background: var(--black); color: var(--white); border-color: var(--black); }
.form-cols { display: flex; gap: 12px; }
.form-cols .form-group { flex: 1; }

/* ── DASHBOARD ── */
#dashboard-page { flex-direction: column; background: var(--white); min-height: 100vh; }
.navbar { height: 56px; background: var(--black); display: flex; align-items: center; position: sticky; top: 0; z-index: 100; flex-shrink: 0; }
.navbar-brand { font-family: var(--fd); font-size: 18px; letter-spacing: 3px; color: var(--white); text-decoration: none; display: flex; align-items: center; gap: 11px; padding: 0 24px; height: 100%; border-right: 1px solid var(--g2); flex-shrink: 0; }
.nb-mark { width: 24px; height: 24px; border: 1.5px solid var(--white); display: flex; align-items: center; justify-content: center; }
.nb-mark svg { width: 12px; height: 12px; }
.navbar-search { flex: 1; max-width: 340px; margin-left: 20px; position: relative; }
.navbar-search input { width: 100%; background: var(--g1); border: 1px solid var(--g2); padding: 7px 12px 7px 32px; color: var(--white); font-family: var(--fm); font-size: 12px; outline: none; transition: border-color var(--t); border-radius: 0; letter-spacing: 0.3px; }
.navbar-search input:focus { border-color: var(--g4); }
.navbar-search input::placeholder { color: var(--g3); }
.search-icon-w { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.search-icon-w svg { width: 13px; height: 13px; stroke: var(--g3); fill: none; stroke-width: 1.5; }
.navbar-right { margin-left: auto; display: flex; align-items: center; height: 100%; }
.nav-icon-btn { height: 100%; padding: 0 16px; border-left: 1px solid var(--g2); display: flex; align-items: center; cursor: pointer; transition: background var(--t); color: var(--g4); position: relative; }
.nav-icon-btn:hover { background: var(--g1); color: var(--white); }
.nav-icon-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.user-avatar { height: 100%; padding: 0 18px; border-left: 1px solid var(--g2); display: flex; align-items: center; gap: 10px; cursor: pointer; transition: background var(--t); position: relative; }
.user-avatar:hover { background: var(--g1); }
.ua-init { width: 28px; height: 28px; background: var(--white); color: var(--black); font-family: var(--fd); font-size: 14px; letter-spacing: 1px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ua-name { font-family: var(--fm); font-size: 11px; color: var(--g5); white-space: nowrap; }
.user-dropdown { position: absolute; top: calc(100% + 1px); right: 0; background: var(--black); border: 1px solid var(--g2); width: 210px; display: none; z-index: 200; box-shadow: 4px 4px 0 rgba(0,0,0,0.4); }
.user-dropdown.show { display: block; }
.ud-info { padding: 13px 15px; border-bottom: 1px solid var(--g2); }
.ud-name { font-weight: 600; font-size: 13px; color: var(--white); margin-bottom: 2px; }
.ud-email { font-family: var(--fm); font-size: 10px; color: var(--g4); }
.ud-item { display: flex; align-items: center; gap: 10px; padding: 9px 15px; font-family: var(--fm); font-size: 11px; color: var(--g4); letter-spacing: 0.3px; border-bottom: 1px solid var(--g2); transition: all var(--t); }
.ud-item:last-child { border-bottom: none; }
.ud-item:hover { background: var(--g1); color: var(--white); }
.ud-item svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 1.5; flex-shrink: 0; }
.ud-item.danger { color: #e55; }
.ud-item.danger:hover { background: rgba(200,50,50,0.12); color: #ff7070; }
.dashboard-body { display: flex; flex: 1; overflow: hidden; }

/* SIDEBAR */
.sidebar { width: 216px; flex-shrink: 0; background: var(--white); border-right: 1.5px solid var(--g6); display: flex; flex-direction: column; overflow-y: auto; }
.sb-label { font-family: var(--fm); font-size: 9px; font-weight: 600; color: var(--g4); letter-spacing: 2px; text-transform: uppercase; padding: 18px 18px 7px; }
.sidebar-item { display: flex; align-items: center; gap: 11px; padding: 9px 18px; font-family: var(--fm); font-size: 12px; color: var(--g3); letter-spacing: 0.3px; transition: all var(--t); border-bottom: 1px solid transparent; user-select: none; }
.sidebar-item svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.5; flex-shrink: 0; }
.sidebar-item:hover { background: var(--g7); color: var(--black); }
.sidebar-item.active { background: var(--black); color: var(--white); }
.si-label { flex: 1; }
.si-count { background: var(--g6); color: var(--g3); font-size: 10px; font-weight: 600; padding: 1px 7px; min-width: 22px; text-align: center; }
.sidebar-item.active .si-count { background: var(--g2); color: var(--g5); }
.sb-sep { height: 1px; background: var(--g6); margin: 6px 0; }
.sidebar-storage { margin-top: auto; border-top: 1.5px solid var(--g6); padding: 16px 18px; }
.storage-hd { display: flex; justify-content: space-between; font-family: var(--fm); font-size: 10px; color: var(--g4); margin-bottom: 8px; }
.storage-bar { height: 3px; background: var(--g6); position: relative; margin-bottom: 6px; }
.storage-fill { position: absolute; left: 0; top: 0; bottom: 0; background: var(--black); transition: width 0.4s ease; }
.storage-txt { font-family: var(--fm); font-size: 10px; color: var(--g5); }

/* MAIN */
.main-content { flex: 1; overflow-y: auto; padding: 32px 38px; display: flex; flex-direction: column; gap: 28px; }
.content-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; border-bottom: 1.5px solid var(--black); padding-bottom: 14px; }
.ch-eyebrow { font-family: var(--fm); font-size: 10px; font-weight: 600; color: var(--g4); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px; }
.content-header h2 { font-family: var(--fd); font-size: 34px; letter-spacing: 1px; color: var(--black); line-height: 1; }
.header-actions { display: flex; gap: 10px; align-items: center; }
.btn-outline { display: flex; align-items: center; gap: 8px; padding: 8px 15px; background: transparent; border: 1.5px solid var(--g5); color: var(--g3); font-family: var(--fm); font-size: 11px; letter-spacing: 1px; text-transform: uppercase; transition: all var(--t); }
.btn-outline:hover { border-color: var(--black); color: var(--black); background: var(--g7); }
.btn-outline svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.btn-solid { display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: var(--black); border: 1.5px solid var(--black); color: var(--white); font-family: var(--fm); font-size: 11px; letter-spacing: 1px; text-transform: uppercase; transition: background var(--t); }
.btn-solid:hover { background: var(--g1); }

/* STATS */
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 0; border: 1.5px solid var(--g6); }
.stat-card { padding: 20px 22px; border-right: 1px solid var(--g6); display: flex; flex-direction: column; gap: 7px; }
.stat-card:last-child { border-right: none; }
.sc-top { display: flex; align-items: center; justify-content: space-between; }
.sc-icon { width: 30px; height: 30px; border: 1px solid var(--g6); display: flex; align-items: center; justify-content: center; }
.sc-icon svg { width: 14px; height: 14px; stroke: var(--g3); fill: none; stroke-width: 1.5; }
.sc-badge { font-family: var(--fm); font-size: 10px; font-weight: 600; padding: 3px 7px; letter-spacing: 0.5px; }
.sc-badge.pos { background: rgba(26,122,74,0.1); color: var(--green); }
.sc-badge.neu { background: var(--g7); color: var(--g4); }
.stat-value { font-family: var(--fd); font-size: 34px; letter-spacing: 0.5px; color: var(--black); line-height: 1; }
.stat-label { font-family: var(--fm); font-size: 10px; color: var(--g4); letter-spacing: 0.5px; text-transform: uppercase; }

/* UPLOAD ZONE */
.upload-zone { border: 1.5px dashed var(--g5); padding: 52px 40px; display: flex; flex-direction: column; align-items: center; gap: 16px; cursor: pointer; transition: all var(--t); text-align: center; background: repeating-linear-gradient(-45deg,transparent,transparent 8px,rgba(0,0,0,0.012) 8px,rgba(0,0,0,0.012) 16px); }
.upload-zone:hover, .upload-zone.drag-over { border-color: var(--black); background: var(--g7); }
.uz-icon { width: 60px; height: 60px; border: 1.5px solid var(--g5); display: flex; align-items: center; justify-content: center; transition: all var(--t); }
.uz-icon svg { width: 26px; height: 26px; stroke: var(--g4); fill: none; stroke-width: 1.5; transition: stroke var(--t); }
.upload-zone:hover .uz-icon { border-color: var(--black); }
.upload-zone:hover .uz-icon svg { stroke: var(--black); }
.uz-title { font-family: var(--fd); font-size: 20px; letter-spacing: 2px; color: var(--black); }
.uz-sub { font-family: var(--fm); font-size: 12px; color: var(--g4); letter-spacing: 0.5px; }
.uz-types { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
.uz-type { border: 1px solid var(--g6); padding: 3px 9px; font-family: var(--fm); font-size: 10px; font-weight: 600; color: var(--g4); letter-spacing: 0.5px; }

/* QUEUE */
.queue-hd { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.queue-title { font-family: var(--fd); font-size: 17px; letter-spacing: 1px; display: flex; align-items: center; gap: 10px; }
.q-count { font-family: var(--fm); font-size: 11px; font-weight: 600; background: var(--black); color: var(--white); padding: 2px 8px; letter-spacing: 0.5px; }
.upload-queue { display: flex; flex-direction: column; }
.queue-item { display: flex; align-items: center; gap: 13px; padding: 11px 14px; border: 1px solid var(--g6); border-top: none; animation: sIn 0.2s ease; }
.queue-item:first-child { border-top: 1px solid var(--g6); }
@keyframes sIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
.qi-ext { font-family: var(--fm); font-size: 10px; font-weight: 600; letter-spacing: 1px; color: var(--g4); width: 34px; flex-shrink: 0; text-transform: uppercase; border: 1px solid var(--g6); padding: 3px 0; text-align: center; }
.qi-info { flex: 1; min-width: 0; }
.qi-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; color: var(--black); }
.qi-size { font-family: var(--fm); font-size: 10px; color: var(--g4); }
.qi-pw { max-width: 150px; flex: 1; }
.qi-pb { height: 2px; background: var(--g6); position: relative; margin-bottom: 3px; }
.qi-pf { position: absolute; left: 0; top: 0; bottom: 0; background: var(--black); transition: width 0.12s ease; }
.qi-pct { font-family: var(--fm); font-size: 10px; color: var(--g4); text-align: right; }
.qi-status { font-family: var(--fm); font-size: 10px; font-weight: 600; padding: 3px 9px; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; }
.qi-status.done { background: rgba(26,122,74,0.1); color: var(--green); }
.qi-status.processing { background: var(--g7); color: var(--g4); }
.qi-status.error { background: rgba(192,57,43,0.1); color: var(--red); }
.qi-large-warn { font-family: var(--fm); font-size: 9px; color: #c07a2b; background: rgba(192,122,43,0.08); border: 1px solid rgba(192,122,43,0.2); padding: 1px 6px; letter-spacing: 0.3px; white-space: nowrap; }
.qi-remove { background: none; border: none; color: var(--g4); font-size: 14px; padding: 4px; transition: color var(--t); display: flex; align-items: center; }
.qi-remove:hover { color: var(--red); }
.qi-remove svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 1.5; }

/* FILTER */
.filter-bar { display: flex; gap: 0; border: 1px solid var(--g6); width: fit-content; margin-bottom: 16px; }
.filter-chip { padding: 7px 15px; font-family: var(--fm); font-size: 11px; font-weight: 600; color: var(--g4); letter-spacing: 0.5px; text-transform: uppercase; border-right: 1px solid var(--g6); transition: all var(--t); user-select: none; }
.filter-chip:last-child { border-right: none; }
.filter-chip:hover { background: var(--g7); color: var(--black); }
.filter-chip.active { background: var(--black); color: var(--white); }
.view-toggle { display: flex; border: 1px solid var(--g5); overflow: hidden; }
.view-btn { background: none; border: none; width: 32px; height: 32px; cursor: pointer; color: var(--g4); transition: all var(--t); display: flex; align-items: center; justify-content: center; border-right: 1px solid var(--g5); }
.view-btn:last-child { border-right: none; }
.view-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.view-btn.active { background: var(--black); color: var(--white); }
.view-btn:not(.active):hover { background: var(--g7); color: var(--black); }

/* DOCS GRID */
.docs-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(178px,1fr)); gap: 0; border: 1px solid var(--g6); }
.doc-card { padding: 14px; border-right: 1px solid var(--g6); border-bottom: 1px solid var(--g6); display: flex; flex-direction: column; gap: 9px; cursor: pointer; transition: background var(--t); position: relative; }
.doc-card:hover { background: var(--g7); }
.dc-preview { width: 100%; aspect-ratio: 4/3; border: 1px solid var(--g6); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; background: var(--white); transition: border-color var(--t); }
.doc-card:hover .dc-preview { border-color: var(--black); }
.dc-type-mark { font-family: var(--fd); font-size: 14px; letter-spacing: 2px; color: var(--g4); }
.dc-actions { position: absolute; top: 5px; right: 5px; display: flex; gap: 4px; opacity: 0; transition: opacity var(--t); }
.doc-card:hover .dc-actions { opacity: 1; }
.dc-action-btn { width: 24px; height: 24px; background: var(--black); border: none; color: var(--white); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background var(--t); }
.dc-action-btn:hover { background: var(--g2); }
.dc-action-btn.del:hover { background: var(--red); }
.dc-action-btn svg { width: 11px; height: 11px; stroke: currentColor; fill: none; stroke-width: 1.5; }
.dc-name { font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--black); }
.dc-meta { font-family: var(--fm); font-size: 10px; color: var(--g4); display: flex; justify-content: space-between; }

/* DOCS LIST */
.docs-list { display: flex; flex-direction: column; border: 1px solid var(--g6); }
.doc-list-item { display: flex; align-items: center; gap: 13px; padding: 10px 15px; border-bottom: 1px solid var(--g6); cursor: pointer; transition: background var(--t); }
.doc-list-item:last-child { border-bottom: none; }
.doc-list-item:hover { background: var(--g7); }
.dli-ext { font-family: var(--fm); font-size: 10px; font-weight: 600; letter-spacing: 1px; color: var(--g4); width: 34px; flex-shrink: 0; text-transform: uppercase; border: 1px solid var(--g6); padding: 3px 0; text-align: center; }
.dli-name { flex: 1; font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--black); }
.dli-size { font-family: var(--fm); font-size: 11px; color: var(--g4); min-width: 70px; text-align: right; }
.dli-date { font-family: var(--fm); font-size: 11px; color: var(--g4); min-width: 88px; text-align: right; }
.dli-actions { display: flex; gap: 4px; opacity: 0; transition: opacity var(--t); }
.doc-list-item:hover .dli-actions { opacity: 1; }
.dli-btn { background: none; border: 1px solid var(--g5); width: 26px; height: 26px; cursor: pointer; color: var(--g4); display: flex; align-items: center; justify-content: center; transition: all var(--t); }
.dli-btn:hover { background: var(--black); color: var(--white); border-color: var(--black); }
.dli-btn.del:hover { background: var(--red); border-color: var(--red); }
.dli-btn svg { width: 11px; height: 11px; stroke: currentColor; fill: none; stroke-width: 1.5; }

/* EMPTY STATE */
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; gap: 14px; text-align: center; border: 1px solid var(--g6); }
.es-mark { width: 60px; height: 60px; border: 1.5px solid var(--g6); display: flex; align-items: center; justify-content: center; }
.es-mark svg { width: 26px; height: 26px; stroke: var(--g5); fill: none; stroke-width: 1.5; }
.empty-state h3 { font-family: var(--fd); font-size: 22px; letter-spacing: 1px; color: var(--g5); }
.empty-state p { font-family: var(--fm); font-size: 12px; color: var(--g5); }

/* MODAL */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 24px; }
.modal-overlay.show { display: flex; }
.modal { background: var(--white); border: 1.5px solid var(--black); width: 100%; max-width: 460px; animation: mIn 0.18s ease; box-shadow: 8px 8px 0 rgba(0,0,0,0.25); }
@keyframes mIn { from { opacity: 0; transform: scale(0.97) translateY(-6px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid var(--g6); }
.modal-header h3 { font-family: var(--fd); font-size: 20px; letter-spacing: 0.5px; color: var(--black); max-width: 330px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.modal-header p { font-family: var(--fm); font-size: 11px; color: var(--g4); margin-top: 3px; }
.modal-close { background: none; border: 1px solid var(--g5); width: 28px; height: 28px; color: var(--g4); cursor: pointer; font-size: 15px; display: flex; align-items: center; justify-content: center; transition: all var(--t); flex-shrink: 0; }
.modal-close:hover { background: var(--black); color: var(--white); border-color: var(--black); }
.modal-body { padding: 18px 20px; }
.modal-preview { border: 1px solid var(--g6); min-height: 150px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; background: var(--g7); }
.mp-type { font-family: var(--fd); font-size: 44px; letter-spacing: 4px; color: var(--g5); }
.modal-info { display: flex; flex-direction: column; }
.mi-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--g7); }
.mi-row:last-child { border-bottom: none; }
.mi-label { font-family: var(--fm); font-size: 10px; color: var(--g4); letter-spacing: 1px; text-transform: uppercase; }
.mi-value { font-family: var(--fm); font-size: 11px; color: var(--black); font-weight: 600; }
.mi-value.ok { color: var(--green); }
.modal-footer { display: flex; border-top: 1px solid var(--g6); }
.modal-footer button { flex: 1; padding: 14px; border: none; font-family: var(--fd); font-size: 16px; letter-spacing: 2px; cursor: pointer; transition: all var(--t); border-right: 1px solid var(--g6); }
.modal-footer button:last-child { border-right: none; }
.mf-cancel { background: var(--white); color: var(--g4); }
.mf-cancel:hover { background: var(--g7); color: var(--black); }
.mf-download { background: var(--black); color: var(--white); }
.mf-download:hover { background: var(--g1); }

/* Scrollbar */
::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--g6); }
::-webkit-scrollbar-thumb:hover { background: var(--g5); }
#file-input { display: none; }

@media (max-width: 900px) {
  #login-page { flex-direction: column; }
  .login-left { padding: 36px 30px; min-height: auto; }
  .doc-stack { display: none; }
  .login-right { width: 100%; padding: 36px 30px; }
  .sidebar { display: none; }
  .stats-grid { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 600px) {
  .stats-grid { grid-template-columns: repeat(2,1fr); }
  .main-content { padding: 20px 16px; }
  .docs-grid { grid-template-columns: repeat(2,1fr); }
}
</style>
</head>
<body>

<div id="toast-container"></div>
<input type="file" id="file-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.txt,.csv">

<!-- ═══════════════════════════════════════════════════════════
     LOGIN PAGE  (unchanged HTML structure)
════════════════════════════════════════════════════════════ -->
<div class="page active" id="login-page">
  <div class="login-left">
    <div>
      <div class="brand-logo">
        <div class="brand-mark">
          <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="1" width="9" height="12" stroke="#f5f4f0" stroke-width="1.5"/>
            <path d="M12 1l5 4v13H7v-3" stroke="#f5f4f0" stroke-width="1.5"/>
            <path d="M12 1v4h5" stroke="#f5f4f0" stroke-width="1.2"/>
            <path d="M9 11h5M9 14h3" stroke="#f5f4f0" stroke-width="1.2"/>
          </svg>
        </div>
        <div class="brand-name">DOCVAULT</div>
      </div>
      <div class="login-headline">
        <h1>STORE.<br>SCAN.<br><em>SECURE.</em></h1>
        <p class="login-tagline">Scan, upload and retrieve your documents from a single secured vault — up to 512 MB of encrypted storage per user.</p>
        <div class="feat-tags">
          <span class="feat-tag">// ENCRYPTED</span>
          <span class="feat-tag">// OCR SCAN</span>
          <span class="feat-tag">// 512 MB FREE</span>
          <span class="feat-tag">// FULL-TEXT SEARCH</span>
        </div>
      </div>
    </div>
    <div class="doc-stack">
      <div class="ds-item"><div class="ds-ext">PDF</div><div class="ds-bar"><div class="ds-bar-fill" style="width:78%"></div></div><div class="ds-status">SCANNED</div></div>
      <div class="ds-item" style="opacity:.75;margin-left:10px"><div class="ds-ext">DOCX</div><div class="ds-bar"><div class="ds-bar-fill" style="width:45%"></div></div><div class="ds-status">PROC...</div></div>
      <div class="ds-item" style="opacity:.5;margin-left:20px"><div class="ds-ext">XLSX</div><div class="ds-bar"><div class="ds-bar-fill" style="width:100%"></div></div><div class="ds-status">STORED</div></div>
    </div>
    <div class="ll-bottom">
      <span>v2.1-php // PRODUCTION BUILD</span>
      <span>© 2025 DOCVAULT</span>
    </div>
  </div>

  <div class="login-right">
    <div>
      <div class="form-header">
        <div class="fh-label">Account Access</div>
        <h2 id="form-title">SIGN IN</h2>
      </div>
      <div class="tab-switch">
        <button class="tab-btn active" id="tab-signin" onclick="switchTab('signin')">SIGN IN</button>
        <button class="tab-btn" id="tab-signup" onclick="switchTab('signup')">CREATE ACCOUNT</button>
      </div>

      <!-- SIGN IN -->
      <div id="signin-form">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-input" id="si-email" placeholder="you@example.com">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-row">
            <input type="password" class="form-input" id="si-password" placeholder="Enter password" onkeydown="if(event.key==='Enter')doLogin()">
            <button class="input-eye" onclick="togglePwd('si-password',this)" tabindex="-1">
              <svg id="eye-si" viewBox="0 0 20 20"><path d="M1 10s3-6 9-6 9 6 9 6-3 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="2.5"/></svg>
            </button>
          </div>
        </div>
        <div class="form-row">
          <label class="form-check"><input type="checkbox" id="si-remember"> Remember me</label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>
        <button class="btn-primary" id="btn-login" onclick="doLogin()">SIGN IN</button>
      </div>

      <!-- SIGN UP -->
      <div id="signup-form" style="display:none">
        <div class="form-cols">
          <div class="form-group"><label class="form-label">First Name</label><input type="text" class="form-input" id="su-first" placeholder="John"></div>
          <div class="form-group"><label class="form-label">Last Name</label><input type="text" class="form-input" id="su-last" placeholder="Doe"></div>
        </div>
        <div class="form-group"><label class="form-label">Email Address</label><input type="email" class="form-input" id="su-email" placeholder="you@example.com"></div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-row">
            <input type="password" class="form-input" id="su-password" placeholder="Min. 8 characters" oninput="checkStrength(this.value)">
            <button class="input-eye" onclick="togglePwd('su-password',this)" tabindex="-1">
              <svg viewBox="0 0 20 20"><path d="M1 10s3-6 9-6 9 6 9 6-3 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="2.5"/></svg>
            </button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
          <div class="strength-label" id="strength-label"></div>
        </div>
        <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" class="form-input" id="su-confirm" placeholder="Repeat password"></div>
        <button class="btn-primary" id="btn-signup" onclick="doSignup()" style="margin-top:4px">CREATE ACCOUNT</button>
      </div>

      <div class="divider">demo accounts</div>
      <div class="demo-accounts">
        <p>// Quick fill for testing</p>
        <div class="demo-account">
          <div><div class="demo-account-name">Alex Johnson</div><div class="demo-account-email">alex@docvault.io</div></div>
          <button class="demo-fill" onclick="fillDemo('alex@docvault.io','password123')">USE</button>
        </div>
        <div class="demo-account">
          <div><div class="demo-account-name">Priya Sharma</div><div class="demo-account-email">priya@docvault.io</div></div>
          <button class="demo-fill" onclick="fillDemo('priya@docvault.io','securepass')">USE</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     DASHBOARD  (unchanged HTML structure)
════════════════════════════════════════════════════════════ -->
<div class="page" id="dashboard-page">
  <nav class="navbar">
    <a href="#" class="navbar-brand" onclick="showSection('upload')">
      <div class="nb-mark">
        <svg viewBox="0 0 20 20" fill="none">
          <rect x="3" y="1" width="9" height="12" stroke="#f5f4f0" stroke-width="1.5"/>
          <path d="M12 1l5 4v13H7v-3" stroke="#f5f4f0" stroke-width="1.5"/>
          <path d="M9 11h5M9 14h3" stroke="#f5f4f0" stroke-width="1.2"/>
        </svg>
      </div>
      DOCVAULT
    </a>
    <div class="navbar-search">
      <div class="search-icon-w"><svg viewBox="0 0 16 16"><circle cx="6.5" cy="6.5" r="4"/><path d="M10 10l3 3" stroke-linecap="round"/></svg></div>
      <input type="text" placeholder="Search documents..." id="search-input" oninput="searchDocs(this.value)">
    </div>
    <div class="navbar-right">
      <div class="nav-icon-btn" title="Notifications">
        <svg viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3l-1.5 2.5h15L16 11V8a6 6 0 00-6-6z"/><path d="M8 16s.5 2 2 2 2-2 2-2" stroke-linecap="round"/></svg>
      </div>
      <button class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
        <svg class="icon-sun" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/><path d="M10 2v2M10 16v2M2 10h2M16 10h2M4.22 4.22l1.42 1.42M14.36 14.36l1.42 1.42M4.22 15.78l1.42-1.42M14.36 5.64l1.42-1.42" stroke-linecap="round"/></svg>
        <svg class="icon-moon" viewBox="0 0 20 20"><path d="M17 13.5A7.5 7.5 0 016.5 3a7.5 7.5 0 100 14 7.5 7.5 0 0010.5-3.5z" stroke-linecap="round"/></svg>
      </button>
      <div class="user-avatar" id="user-avatar-btn" onclick="toggleDropdown()">
        <div class="ua-init" id="user-initials">AJ</div>
        <div class="ua-name" id="ua-name-display">Alex Johnson</div>
        <div class="user-dropdown" id="user-dropdown">
          <div class="ud-info"><div class="ud-name" id="ud-fullname">Alex Johnson</div><div class="ud-email" id="ud-email-display">alex@docvault.io</div></div>
          <div class="ud-item" onclick="showSection('upload')"><svg viewBox="0 0 16 16"><path d="M8 2v8M4 6l4-4 4 4" stroke-linecap="round"/><rect x="2" y="11" width="12" height="3" stroke-width="1.2"/></svg>My Documents</div>
          <div class="ud-item"><svg viewBox="0 0 16 16"><circle cx="8" cy="5" r="3"/><path d="M2 14c0-3 2.7-5 6-5s6 2 6 5" stroke-linecap="round"/></svg>Settings</div>
          <div class="ud-item danger" onclick="doLogout()"><svg viewBox="0 0 16 16"><path d="M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3M10 11l3-3-3-3M6 8h7" stroke-linecap="round"/></svg>Sign Out</div>
        </div>
      </div>
    </div>
  </nav>

  <div class="dashboard-body">
    <aside class="sidebar">
      <div class="sb-label">Main</div>
      <div class="sidebar-item active" id="sb-upload" onclick="showSection('upload')"><svg viewBox="0 0 16 16"><path d="M8 2v8M4 6l4-4 4 4" stroke-linecap="round"/><rect x="2" y="11" width="12" height="3" stroke-width="1.2"/></svg><span class="si-label">Upload</span></div>
      <div class="sidebar-item" id="sb-docs" onclick="showSection('docs')"><svg viewBox="0 0 16 16"><rect x="3" y="2" width="7" height="9" stroke-width="1.5"/><path d="M10 2l3 3v9H6" stroke-width="1.5"/><path d="M10 2v3h3" stroke-width="1.2"/></svg><span class="si-label">All Documents</span><span class="si-count" id="sb-doc-count">0</span></div>
      <div class="sidebar-item" id="sb-recent" onclick="showSection('recent')"><svg viewBox="0 0 16 16"><circle cx="8" cy="8" r="5.5" stroke-width="1.5"/><path d="M8 5v3.5l2 1.5" stroke-linecap="round"/></svg><span class="si-label">Recent</span></div>
      <div class="sidebar-item" id="sb-starred" onclick="showSection('starred')"><svg viewBox="0 0 16 16"><path d="M8 2l1.8 3.6L14 6.3l-3 2.9.7 4.1L8 11.4l-3.7 1.9.7-4.1-3-2.9 4.2-.7L8 2z" stroke-linejoin="round"/></svg><span class="si-label">Starred</span></div>
      <div class="sidebar-item" id="sb-folders" onclick="showSection('folders')"><svg viewBox="0 0 16 16"><path d="M2 4h5l1.5 2H14a1 1 0 011 1v5a1 1 0 01-1 1H2a1 1 0 01-1-1V5a1 1 0 011-1z" stroke-linejoin="round"/></svg><span class="si-label">Folders</span><span class="si-count" id="sb-folder-count">0</span></div>
      <div class="sb-sep"></div>
      <div class="sb-label">By Type</div>
      <div class="sidebar-item" onclick="filterByType('pdf')"><svg viewBox="0 0 16 16"><rect x="3" y="1" width="7" height="9"/><path d="M10 1l3 3v9H6v-2"/><path d="M10 1v3h3" stroke-width="1.2"/></svg><span class="si-label">PDF</span><span class="si-count" id="count-pdf">0</span></div>
      <div class="sidebar-item" onclick="filterByType('doc')"><svg viewBox="0 0 16 16"><rect x="3" y="2" width="10" height="12"/><path d="M5 6h6M5 9h4" stroke-linecap="round"/></svg><span class="si-label">Documents</span><span class="si-count" id="count-doc">0</span></div>
      <div class="sidebar-item" onclick="filterByType('img')"><svg viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="10"/><circle cx="6" cy="7" r="1.5" stroke-width="1.2"/><path d="M2 11l3-3 3 3 2-2 4 4" stroke-linecap="round"/></svg><span class="si-label">Images</span><span class="si-count" id="count-img">0</span></div>
      <div class="sidebar-item" onclick="filterByType('xls')"><svg viewBox="0 0 16 16"><rect x="2" y="2" width="12" height="12"/><path d="M2 6h12M2 10h12M7 2v12"/></svg><span class="si-label">Spreadsheets</span><span class="si-count" id="count-xls">0</span></div>
      <div class="sidebar-storage">
        <div class="storage-hd"><span>Storage</span><span id="storage-used-label">0 / 512 MB</span></div>
        <div class="storage-bar"><div class="storage-fill" id="storage-fill" style="width:0%"></div></div>
        <div class="storage-txt" id="storage-pct">0% used</div>
      </div>
    </aside>

    <main class="main-content">

      <!-- UPLOAD -->
      <section id="section-upload">
        <div class="content-header">
          <div><div class="ch-eyebrow">// Document Management</div><h2>UPLOAD</h2></div>
          <div class="header-actions"><button class="btn-outline" onclick="showSection('docs')"><svg viewBox="0 0 14 14"><rect x="2" y="2" width="10" height="10"/><path d="M4 6h6M4 9h3" stroke-linecap="round"/></svg>View All</button></div>
        </div>
        <div class="stats-grid">
          <div class="stat-card"><div class="sc-top"><div class="sc-icon"><svg viewBox="0 0 16 16"><rect x="3" y="2" width="7" height="9"/><path d="M10 2l3 3v9H6"/></svg></div><span class="sc-badge neu">TOTAL</span></div><div class="stat-value" id="stat-total-docs">0</div><div class="stat-label">Documents</div></div>
          <div class="stat-card"><div class="sc-top"><div class="sc-icon"><svg viewBox="0 0 16 16"><rect x="2" y="2" width="12" height="12"/><path d="M5 8l2 2 4-4" stroke-linecap="round"/></svg></div><span class="sc-badge neu">512 MB</span></div><div class="stat-value" id="stat-storage">0</div><div class="stat-label">MB Used</div></div>
          <div class="stat-card"><div class="sc-top"><div class="sc-icon"><svg viewBox="0 0 16 16"><path d="M8 2l1.4 2.9L13 5.6l-2.5 2.4.6 3.4L8 9.7l-3.1 1.7.6-3.4L3 5.6l3.6-.7L8 2z"/></svg></div><span class="sc-badge pos">100%</span></div><div class="stat-value" id="stat-scanned">0</div><div class="stat-label">Scanned</div></div>
          <div class="stat-card"><div class="sc-top"><div class="sc-icon"><svg viewBox="0 0 16 16"><circle cx="8" cy="8" r="5.5"/><path d="M8 5v3.5l2 1.5" stroke-linecap="round"/></svg></div><span class="sc-badge neu">TODAY</span></div><div class="stat-value" id="stat-today">0</div><div class="stat-label">Added Today</div></div>
        </div>
        <div class="upload-zone" id="upload-zone" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)" onclick="document.getElementById('file-input').click()">
          <div class="uz-icon"><svg viewBox="0 0 28 28" fill="none"><path d="M14 6v12" stroke-linecap="round"/><path d="M8 12l6-6 6 6" stroke-linecap="round"/><rect x="4" y="20" width="20" height="4"/></svg></div>
          <div class="uz-title">DROP FILES OR CLICK TO BROWSE</div>
          <div class="uz-sub">Maximum 100 MB per file</div>
          <div class="uz-types"><span class="uz-type">PDF</span><span class="uz-type">DOC</span><span class="uz-type">DOCX</span><span class="uz-type">XLS</span><span class="uz-type">XLSX</span><span class="uz-type">PNG</span><span class="uz-type">JPG</span><span class="uz-type">TXT</span><span class="uz-type">CSV</span></div>
        </div>
        <div id="upload-queue-wrap" style="display:none">
          <div class="queue-hd"><div class="queue-title">QUEUE <span class="q-count" id="queue-count">0</span></div><button class="btn-outline" onclick="clearQueue()" style="font-size:10px;padding:6px 12px">Clear Done</button></div>
          <div class="upload-queue" id="upload-queue"></div>
        </div>
      </section>

      <!-- DOCS -->
      <section id="section-docs" style="display:none">
        <div class="content-header">
          <div><div class="ch-eyebrow">// Stored Files</div><h2>DOCUMENTS</h2></div>
          <div class="header-actions">
            <div class="view-toggle">
              <button class="view-btn active" id="view-grid-btn" onclick="setView('grid')"><svg viewBox="0 0 14 14"><rect x="1" y="1" width="5" height="5"/><rect x="8" y="1" width="5" height="5"/><rect x="1" y="8" width="5" height="5"/><rect x="8" y="8" width="5" height="5"/></svg></button>
              <button class="view-btn" id="view-list-btn" onclick="setView('list')"><svg viewBox="0 0 14 14"><path d="M1 3h12M1 7h12M1 11h12" stroke-linecap="round"/></svg></button>
            </div>
            <button class="btn-solid" onclick="showSection('upload')">+ UPLOAD</button>
          </div>
        </div>
        <p id="docs-count-label" style="font-family:var(--fm);font-size:11px;color:var(--g4);margin-top:-16px">// 0 documents stored</p>
        <div class="filter-bar"><span class="filter-chip active" onclick="filterChip(this,'all')">ALL</span><span class="filter-chip" onclick="filterChip(this,'pdf')">PDF</span><span class="filter-chip" onclick="filterChip(this,'doc')">DOCS</span><span class="filter-chip" onclick="filterChip(this,'img')">IMG</span><span class="filter-chip" onclick="filterChip(this,'xls')">XLS</span></div>
        <div id="docs-container">
          <div class="empty-state" id="docs-empty"><div class="es-mark"><svg viewBox="0 0 28 28"><rect x="6" y="4" width="16" height="20"/><path d="M10 10h8M10 14h5" stroke-linecap="round"/></svg></div><h3>NO DOCUMENTS</h3><p>// upload your first document to begin</p></div>
          <div class="docs-grid" id="docs-grid" style="display:none"></div>
          <div class="docs-list" id="docs-list" style="display:none"></div>
        </div>
      </section>

      <!-- RECENT -->
      <section id="section-recent" style="display:none">
        <div class="content-header"><div><div class="ch-eyebrow">// Last 7 Days</div><h2>RECENT</h2></div></div>
        <div class="docs-list" id="recent-list"><div class="empty-state"><div class="es-mark"><svg viewBox="0 0 28 28"><circle cx="14" cy="14" r="9"/><path d="M14 9v6l3.5 2" stroke-linecap="round"/></svg></div><h3>NO RECENT ACTIVITY</h3><p>// uploaded documents will appear here</p></div></div>
      </section>

      <!-- STARRED -->
      <section id="section-starred" style="display:none">
        <div class="content-header"><div><div class="ch-eyebrow">// Favourites</div><h2>STARRED</h2></div></div>
        <div class="empty-state"><div class="es-mark"><svg viewBox="0 0 28 28"><path d="M14 4l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L14 18.3l-5.6 2.9 1.1-6.2L5 10.6l6.2-.9L14 4z" stroke-linejoin="round"/></svg></div><h3>NO STARRED DOCUMENTS</h3><p>// star a document to pin it here</p></div>
      </section>

      <!-- FOLDERS -->
      <section id="section-folders" style="display:none">
        <div class="content-header">
          <div><div class="ch-eyebrow">// Organise Your Files</div><h2>FOLDERS</h2></div>
          <div class="header-actions">
            <button class="btn-solid" onclick="openNewFolderModal()">
              <svg viewBox="0 0 14 14" fill="none"><path d="M7 2v10M2 7h10" stroke-width="1.5" stroke-linecap="round"/></svg>
              NEW FOLDER
            </button>
          </div>
        </div>
        <div id="folders-empty" class="empty-state" style="display:none"><div class="es-mark"><svg viewBox="0 0 28 28" fill="none"><path d="M4 8h8l3 4h9v10H4z" stroke-width="1.5" stroke-linejoin="round"/></svg></div><h3>NO FOLDERS</h3><p>// create a folder to organise your documents</p></div>
        <div id="folder-browser">
          <div id="folder-breadcrumb" class="breadcrumb" style="display:none"></div>
          <div id="folder-grid-wrap">
            <div class="folder-section-hd"><span class="folder-section-label" id="folder-section-title">// Your Folders</span></div>
            <div class="folder-grid" id="folders-grid"></div>
          </div>
          <div id="folder-docs-wrap" style="display:none">
            <div class="folder-section-hd"><span class="folder-section-label">// Documents in Folder</span>
              <button class="btn-outline" style="font-size:10px;padding:5px 11px" onclick="openMoveToFolderModal()"><svg viewBox="0 0 14 14" fill="none"><path d="M7 2v10M2 7h10" stroke-width="1.5" stroke-linecap="round"/></svg>ADD DOCS</button>
            </div>
            <div id="folder-docs-empty" class="empty-state" style="display:none;border:1px solid var(--g6)"><div class="es-mark"><svg viewBox="0 0 28 28"><rect x="6" y="4" width="16" height="20"/><path d="M10 10h8M10 14h5" stroke-linecap="round"/></svg></div><h3>EMPTY FOLDER</h3><p>// add documents to this folder</p></div>
            <div class="docs-list" id="folder-docs-list"></div>
          </div>
        </div>
      </section>

    </main>
  </div>
</div>

<!-- DOCUMENT MODAL -->
<div class="modal-overlay" id="doc-modal">
  <div class="modal">
    <div class="modal-header">
      <div><h3 id="modal-doc-name">Document</h3><p id="modal-doc-meta">PDF · 2.4 MB</p></div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="modal-preview" id="modal-preview"><div class="mp-type" id="modal-type-mark">PDF</div></div>
      <div class="modal-info">
        <div class="mi-row"><span class="mi-label">OCR Status</span><span class="mi-value ok" id="modal-scan-status">SCANNED</span></div>
        <div class="mi-row"><span class="mi-label">File Size</span><span class="mi-value" id="modal-size">—</span></div>
        <div class="mi-row"><span class="mi-label">Uploaded</span><span class="mi-value" id="modal-date">—</span></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="mf-cancel" onclick="closeModal()">CLOSE</button>
      <button class="mf-download" onclick="downloadDoc()">DOWNLOAD</button>
    </div>
  </div>
</div>

<!-- NEW FOLDER MODAL -->
<div class="nf-modal-overlay" id="new-folder-modal">
  <div class="nf-modal">
    <h3>NEW FOLDER</h3>
    <p>// give your folder a name and a colour</p>
    <input class="nf-input" id="folder-name-input" type="text" placeholder="Folder name..." maxlength="48" onkeydown="if(event.key==='Enter')createFolder()">
    <div style="display:flex;gap:8px;margin-bottom:18px;align-items:center;">
      <span style="font-family:var(--fm);font-size:9px;color:var(--g4);letter-spacing:1.5px;text-transform:uppercase;white-space:nowrap">Colour</span>
      <div id="folder-color-picker" style="display:flex;gap:6px;flex-wrap:wrap;">
        <button class="fc-swatch active" data-color="#7a7a7a" onclick="selectFolderColor(this)" style="background:#7a7a7a"></button>
        <button class="fc-swatch" data-color="#3b82f6" onclick="selectFolderColor(this)" style="background:#3b82f6"></button>
        <button class="fc-swatch" data-color="#10b981" onclick="selectFolderColor(this)" style="background:#10b981"></button>
        <button class="fc-swatch" data-color="#f59e0b" onclick="selectFolderColor(this)" style="background:#f59e0b"></button>
        <button class="fc-swatch" data-color="#ef4444" onclick="selectFolderColor(this)" style="background:#ef4444"></button>
        <button class="fc-swatch" data-color="#8b5cf6" onclick="selectFolderColor(this)" style="background:#8b5cf6"></button>
        <button class="fc-swatch" data-color="#ec4899" onclick="selectFolderColor(this)" style="background:#ec4899"></button>
        <button class="fc-swatch" data-color="#0d0d0d" onclick="selectFolderColor(this)" style="background:#0d0d0d"></button>
      </div>
    </div>
    <div class="nf-footer">
      <button class="nf-cancel" onclick="closeNewFolderModal()">CANCEL</button>
      <button class="nf-create" onclick="createFolder()">CREATE</button>
    </div>
  </div>
</div>

<!-- MOVE TO FOLDER MODAL -->
<div class="nf-modal-overlay" id="move-folder-modal">
  <div class="nf-modal">
    <h3>ADD DOCUMENTS</h3>
    <p>// select documents to add to this folder</p>
    <div id="move-doc-list" style="max-height:240px;overflow-y:auto;border:1px solid var(--g6);margin-bottom:16px"></div>
    <div class="nf-footer">
      <button class="nf-cancel" onclick="closeMoveToFolderModal()">CANCEL</button>
      <button class="nf-create" onclick="addDocsToFolder()">ADD SELECTED</button>
    </div>
  </div>
</div>

<!-- RENAME FOLDER MODAL -->
<div class="nf-modal-overlay" id="rename-folder-modal">
  <div class="nf-modal">
    <h3>RENAME FOLDER</h3>
    <p>// enter a new name for this folder</p>
    <input class="nf-input" id="rename-folder-input" type="text" placeholder="New folder name..." maxlength="48" onkeydown="if(event.key==='Enter')confirmRenameFolder()">
    <div class="nf-footer">
      <button class="nf-cancel" onclick="closeRenameFolderModal()">CANCEL</button>
      <button class="nf-create" onclick="confirmRenameFolder()">RENAME</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT — doLogin() and doSignup() now use fetch()
     All other logic is preserved exactly as original.
════════════════════════════════════════════════════════════ -->
<script>
// ── PHP session injection ─────────────────────────────────────
// If the PHP session already has a user, we inject them here
// so the dashboard loads immediately on page refresh.
const PHP_SESSION_USER = <?php echo $sessionUser ? $sessionUser : 'null'; ?>;

const STORAGE_KEY = 'docvault_bw1';
const MAX_USER_STORAGE = 512 * 1024 * 1024;
const MAX_FILE_SIZE = 100 * 1024 * 1024;
let currentUser = null, currentDocs = [], currentView = 'grid', currentFilter = 'all', selectedDoc = null, uploadQueue = [];

// ── Helpers (unchanged) ───────────────────────────────────────
function fmtBytes(b){if(b<1024)return b+' B';if(b<1048576)return(b/1024).toFixed(1)+' KB';if(b<1073741824)return(b/1048576).toFixed(2)+' MB';return(b/1073741824).toFixed(2)+' GB';}
function uuid(){return'xxxx-xxxx-4xxx-yxxx'.replace(/[xy]/g,c=>{const r=Math.random()*16|0;return(c==='x'?r:(r&0x3|0x8)).toString(16);});}
function fmtDate(iso){return new Date(iso).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});}
function getCat(ext){if(['pdf'].includes(ext))return'pdf';if(['doc','docx','txt'].includes(ext))return'doc';if(['png','jpg','jpeg','gif','webp'].includes(ext))return'img';if(['xls','xlsx','csv'].includes(ext))return'xls';return'other';}

function showToast(msg,type='info'){
  const c=document.getElementById('toast-container');
  const t=document.createElement('div');t.className=`toast ${type}`;
  t.innerHTML=`<span class="toast-dot"></span>${msg}`;c.appendChild(t);
  requestAnimationFrame(()=>t.classList.add('show'));
  setTimeout(()=>{t.classList.remove('show');t.classList.add('hide');setTimeout(()=>t.remove(),280);},3000);
}

// ── Tab / UI helpers (unchanged) ──────────────────────────────
function switchTab(tab){
  document.getElementById('tab-signin').classList.toggle('active',tab==='signin');
  document.getElementById('tab-signup').classList.toggle('active',tab==='signup');
  document.getElementById('signin-form').style.display=tab==='signin'?'block':'none';
  document.getElementById('signup-form').style.display=tab==='signup'?'block':'none';
  document.getElementById('form-title').textContent=tab==='signin'?'SIGN IN':'REGISTER';
}

function fillDemo(email,pass){
  document.getElementById('si-email').value=email;
  document.getElementById('si-password').value=pass;
  switchTab('signin');showToast('// credentials loaded','info');
}

function togglePwd(id,btn){
  const inp=document.getElementById(id);const hide=inp.type==='password';
  inp.type=hide?'text':'password';
  btn.querySelector('svg').innerHTML=hide
    ?'<path d="M1 10s3-6 9-6 9 6 9 6-3 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="2.5"/><line x1="3" y1="3" x2="17" y2="17" stroke-width="1.5"/>'
    :'<path d="M1 10s3-6 9-6 9 6 9 6-3 6-9 6-9-6-9-6z"/><circle cx="10" cy="10" r="2.5"/>';
}

function checkStrength(val){
  const fill=document.getElementById('strength-fill');const label=document.getElementById('strength-label');
  let s=0;if(val.length>=8)s++;if(/[A-Z]/.test(val))s++;if(/[0-9]/.test(val))s++;if(/[^A-Za-z0-9]/.test(val))s++;
  const cfg=[{w:'0%',c:'transparent',t:''},{w:'25%',c:'#c0392b',t:'// WEAK'},{w:'50%',c:'#e67e22',t:'// FAIR'},{w:'75%',c:'#1a7a4a',t:'// GOOD'},{w:'100%',c:'#0d0d0d',t:'// STRONG'}][s];
  fill.style.width=cfg.w;fill.style.background=cfg.c;label.style.color=cfg.c;label.textContent=cfg.t;
}

// ── ★ UPDATED doLogin() — uses fetch() ───────────────────────
async function doLogin(){
  const email    = document.getElementById('si-email').value.trim();
  const password = document.getElementById('si-password').value;

  if(!email || !password){ showToast('// fill all fields','error'); return; }

  const btn = document.getElementById('btn-login');
  btn.disabled = true; btn.textContent = 'SIGNING IN…';

  try {
    const res  = await fetch('auth.php', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body   : JSON.stringify({ action: 'login', email, password })
    });
    const data = await res.json();

    if(!data.ok){
      showToast(`// ${data.error}`,'error');
      const inp = document.getElementById('si-password');
      inp.style.borderColor='#c0392b';
      setTimeout(()=>inp.style.borderColor='',2000);
      return;
    }

    // Merge server user with local localStorage docs (keep file data client-side)
    bootstrapUser(data.user);
    showToast(`// welcome back, ${currentUser.firstName}`,'success');

  } catch(err){
    showToast('// network error — check server','error');
    console.error(err);
  } finally {
    btn.disabled = false; btn.textContent = 'SIGN IN';
  }
}

// ── ★ UPDATED doSignup() — uses fetch() ──────────────────────
async function doSignup(){
  const firstName = document.getElementById('su-first').value.trim();
  const lastName  = document.getElementById('su-last').value.trim();
  const email     = document.getElementById('su-email').value.trim();
  const password  = document.getElementById('su-password').value;
  const confirm   = document.getElementById('su-confirm').value;

  if(!firstName||!lastName||!email||!password||!confirm){ showToast('// fill all fields','error'); return; }
  if(password !== confirm){ showToast('// passwords do not match','error'); return; }
  if(password.length < 8){ showToast('// password min. 8 chars','error'); return; }

  const btn = document.getElementById('btn-signup');
  btn.disabled = true; btn.textContent = 'CREATING…';

  try {
    const res  = await fetch('auth.php', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body   : JSON.stringify({ action:'signup', firstName, lastName, email, password, confirm })
    });
    const data = await res.json();

    if(!data.ok){ showToast(`// ${data.error}`,'error'); return; }

    bootstrapUser(data.user);
    showToast(`// account created — welcome ${firstName}`,'success');

  } catch(err){
    showToast('// network error — check server','error');
    console.error(err);
  } finally {
    btn.disabled = false; btn.textContent = 'CREATE ACCOUNT';
  }
}

// ── ★ UPDATED doLogout() — also destroys PHP session ─────────
async function doLogout(){
  try { await fetch('auth.php?action=logout'); } catch(_){}
  currentUser = null; currentDocs = []; uploadQueue = [];
  showPage('login-page');
  showToast('// signed out','info');
  document.getElementById('user-dropdown').classList.remove('show');
}

// ── bootstrapUser — called after successful auth ──────────────
// Merges server user with any locally-stored document data for this user.
function bootstrapUser(serverUser){
  // Pull locally-cached doc data (file blobs are only in localStorage)
  const localKey = 'docvault_docs_' + serverUser.id;
  const localDocs = JSON.parse(localStorage.getItem(localKey) || '[]');

  currentUser = Object.assign({}, serverUser, { documents: localDocs });
  currentDocs = localDocs;

  initDashboard();
  showPage('dashboard-page');
}

// ── Page / section helpers (unchanged) ───────────────────────
function showPage(id){document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));document.getElementById(id).classList.add('active');}

function showSection(name){
  document.querySelectorAll('[id^="section-"]').forEach(s=>s.style.display='none');
  document.getElementById('section-'+name).style.display='block';
  document.querySelectorAll('.sidebar-item').forEach(i=>i.classList.remove('active'));
  const sb=document.getElementById('sb-'+name);if(sb)sb.classList.add('active');
  document.getElementById('user-dropdown').classList.remove('show');
  const si=document.getElementById('search-input');if(si)si.value='';
  if(name==='docs')renderDocs();if(name==='recent')renderRecent();
  if(name==='folders'){renderFolders();if(openFolderId)openFolder(openFolderId);}
}

function toggleDropdown(){document.getElementById('user-dropdown').classList.toggle('show');}
document.addEventListener('click',e=>{const btn=document.getElementById('user-avatar-btn');if(btn&&!btn.contains(e.target))document.getElementById('user-dropdown').classList.remove('show');});

function initDashboard(){
  const ini=(currentUser.firstName[0]+(currentUser.lastName[0]||'')).toUpperCase();
  document.getElementById('user-initials').textContent=ini;
  document.getElementById('ua-name-display').textContent=currentUser.firstName+' '+currentUser.lastName;
  document.getElementById('ud-fullname').textContent=currentUser.firstName+' '+currentUser.lastName;
  document.getElementById('ud-email-display').textContent=currentUser.email;
  updateStorageUI();updateStats();renderDocs();renderFolders();showSection('upload');
}

function updateStorageUI(){
  const used = currentDocs.reduce((s,d)=>s+(d.fileSize||0),0);
  currentUser.storageUsed = used;
  const limit = MAX_USER_STORAGE;
  const pct = Math.min(100,(used/limit)*100);
  document.getElementById('storage-fill').style.width=pct+'%';
  document.getElementById('storage-used-label').textContent=`${fmtBytes(used)} / 512 MB`;
  document.getElementById('storage-pct').textContent=`${pct.toFixed(1)}% used · ${fmtBytes(limit-used)} free`;
  document.getElementById('stat-storage').textContent=(used/1048576).toFixed(2);
  document.getElementById('storage-fill').style.background=pct>85?'#c0392b':pct>65?'#c07a2b':'var(--black)';
}

function updateStats(){
  const today=new Date().toDateString();
  const tc=currentDocs.filter(d=>new Date(d.uploadedAt).toDateString()===today).length;
  document.getElementById('stat-total-docs').textContent=currentDocs.length;
  document.getElementById('stat-scanned').textContent=currentDocs.filter(d=>d.scanStatus==='done').length;
  document.getElementById('stat-today').textContent=tc;
  document.getElementById('sb-doc-count').textContent=currentDocs.length;
  document.getElementById('docs-count-label').textContent=`// ${currentDocs.length} document${currentDocs.length!==1?'s':''} stored`;
  const counts={pdf:0,doc:0,img:0,xls:0};
  currentDocs.forEach(d=>{if(counts[d.category]!==undefined)counts[d.category]++;});
  ['pdf','doc','img','xls'].forEach(k=>document.getElementById('count-'+k).textContent=counts[k]);
}

// ── File upload (unchanged) ───────────────────────────────────
document.getElementById('file-input').addEventListener('change',function(){handleFiles(Array.from(this.files));this.value='';});
function handleDragOver(e){e.preventDefault();document.getElementById('upload-zone').classList.add('drag-over');}
function handleDragLeave(){document.getElementById('upload-zone').classList.remove('drag-over');}
function handleDrop(e){e.preventDefault();document.getElementById('upload-zone').classList.remove('drag-over');handleFiles(Array.from(e.dataTransfer.files));}

function handleFiles(files){
  if(!files.length)return;
  files.forEach(file=>{
    if(file.size>MAX_FILE_SIZE){showToast(`// ${file.name} exceeds 100 MB`,'error');return;}
    const used=currentDocs.reduce((s,d)=>s+(d.fileSize||0),0);
    if(file.size>MAX_USER_STORAGE-used){showToast(`// insufficient storage`,'error');return;}
    const ext=file.name.split('.').pop().toLowerCase();
    const doc={id:'doc_'+uuid(),originalName:file.name,storedName:uuid()+'.'+ext,fileType:file.type,fileExt:ext,fileSize:file.size,category:getCat(ext),scanStatus:'processing',uploadedAt:new Date().toISOString(),isStarred:false,tags:[]};
    const queueId='q_'+uuid();uploadQueue.push({doc,queueId});simulateUpload(doc,queueId,file);
  });
  renderQueue();
}

const LARGE_FILE_THRESHOLD = 10 * 1024 * 1024;

function simulateUpload(doc,queueId,file){
  if(file.size>LARGE_FILE_THRESHOLD){
    let p=0;updateQueueItem(queueId,0,'processing');
    const iv=setInterval(()=>{
      p+=Math.random()*8+2;p=Math.min(p,95);updateQueueItem(queueId,p,'processing');
      if(p>=95){clearInterval(iv);setTimeout(()=>{doc.scanStatus='done';doc.dataUrl=null;doc.largeFile=true;updateQueueItem(queueId,100,'done');commitDocument(doc);},800+Math.random()*400);}
    },120);
  } else {
    const reader=new FileReader();
    reader.onload=e=>{
      let p=0;
      const iv=setInterval(()=>{
        p+=Math.random()*15+4;p=Math.min(p,95);updateQueueItem(queueId,p,'processing');
        if(p>=95){clearInterval(iv);setTimeout(()=>{doc.scanStatus='done';doc.dataUrl=e.target.result;updateQueueItem(queueId,100,'done');commitDocument(doc);},600+Math.random()*400);}
      },100);
    };
    reader.onerror=()=>{updateQueueItem(queueId,0,'error');showToast(`// failed to read ${doc.originalName}`,'error');};
    reader.readAsDataURL(file);
  }
}

function commitDocument(doc){
  // Store docs keyed by user ID in localStorage (keeps file blobs client-side)
  const localKey = 'docvault_docs_' + currentUser.id;
  const existing = JSON.parse(localStorage.getItem(localKey) || '[]');
  const docToSave = {...doc};
  if(docToSave.dataUrl && docToSave.fileSize > LARGE_FILE_THRESHOLD){ docToSave.dataUrl=null; docToSave.largeFile=true; }
  existing.push(docToSave);
  try {
    localStorage.setItem(localKey, JSON.stringify(existing));
    currentDocs = existing;
  } catch(e){
    if(doc.dataUrl){ doc.dataUrl=null; doc.largeFile=true; docToSave.dataUrl=null; }
    existing[existing.length-1] = docToSave;
    try{ localStorage.setItem(localKey, JSON.stringify(existing)); currentDocs=existing; }
    catch(e2){ showToast('// storage quota full — clear some files','error'); return; }
  }
  updateStorageUI(); updateStats();
  showToast(`// ${doc.originalName} stored · ${fmtBytes(doc.fileSize)}`,'success');
  if(doc.largeFile) showToast(`// preview unavailable for files over 10 MB`,'info');
}

function renderQueue(){
  const wrap=document.getElementById('upload-queue-wrap');
  const qEl=document.getElementById('upload-queue');
  wrap.style.display=uploadQueue.length?'block':'none';
  document.getElementById('queue-count').textContent=uploadQueue.length;
  qEl.innerHTML='';
  uploadQueue.forEach(({doc,queueId})=>{
    const item=document.createElement('div');item.className='queue-item';item.id=queueId;
    const largeWarn=doc.fileSize>LARGE_FILE_THRESHOLD?`<span class="qi-large-warn">⚠ LARGE FILE — preview only</span>`:'';
    item.innerHTML=`<div class="qi-ext">${doc.fileExt.toUpperCase()}</div><div class="qi-info"><div class="qi-name">${doc.originalName}</div><div class="qi-size">${fmtBytes(doc.fileSize)} ${largeWarn}</div></div><div class="qi-pw"><div class="qi-pb"><div class="qi-pf" style="width:0%" id="pf_${queueId}"></div></div><div class="qi-pct" id="pp_${queueId}">0%</div></div><span class="qi-status processing" id="qs_${queueId}">PROCESSING</span><button class="qi-remove" onclick="removeQueueItem('${queueId}')"><svg viewBox="0 0 14 14" fill="none"><path d="M2 2l10 10M12 2L2 12" stroke-width="1.5" stroke-linecap="round"/></svg></button>`;
    qEl.appendChild(item);
  });
}

function updateQueueItem(queueId,pct,status){
  const pf=document.getElementById('pf_'+queueId);
  const pp=document.getElementById('pp_'+queueId);
  const qs=document.getElementById('qs_'+queueId);
  if(!pf)return;
  pf.style.width=pct+'%';pp.textContent=Math.round(pct)+'%';
  if(status==='done'){qs.className='qi-status done';qs.textContent='DONE';}
}

function removeQueueItem(queueId){uploadQueue=uploadQueue.filter(q=>q.queueId!==queueId);renderQueue();}
function clearQueue(){uploadQueue=uploadQueue.filter(q=>q.doc.scanStatus!=='done');renderQueue();}

function setView(v){
  currentView=v;
  document.getElementById('view-grid-btn').classList.toggle('active',v==='grid');
  document.getElementById('view-list-btn').classList.toggle('active',v==='list');
  renderDocs();
}

function filterChip(el,type){
  document.querySelectorAll('.filter-chip').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');currentFilter=type;renderDocs();
}

function filterByType(type){
  currentFilter=type;showSection('docs');
  document.querySelectorAll('.filter-chip').forEach(c=>{
    const m=(type==='all'&&c.textContent==='ALL')||(type==='doc'&&c.textContent==='DOCS')||(type==='img'&&c.textContent==='IMG')||c.textContent===type.toUpperCase();
    c.classList.toggle('active',m);
  });
}

function searchDocs(q){
  if(!q){renderDocs();return;}
  const docsSection=document.getElementById('section-docs');
  if(docsSection&&docsSection.style.display==='none'){showSection('docs');}
  const lower=q.toLowerCase();
  const results=currentDocs.filter(d=>d.originalName.toLowerCase().includes(lower)||(d.fileExt||'').toLowerCase().includes(lower)||(d.category||'').toLowerCase().includes(lower));
  renderDocsData(results);
  const label=document.getElementById('docs-count-label');
  if(label)label.textContent=`// ${results.length} result${results.length!==1?'s':''} for "${q}"`;
}

function renderDocs(){
  const label=document.getElementById('docs-count-label');
  if(label)label.textContent=`// ${currentDocs.length} document${currentDocs.length!==1?'s':''} stored`;
  renderDocsData(currentFilter==='all'?currentDocs:currentDocs.filter(d=>d.category===currentFilter));
}

function renderDocsData(docs){
  const empty=document.getElementById('docs-empty');
  const grid=document.getElementById('docs-grid');
  const list=document.getElementById('docs-list');
  if(!docs.length){empty.style.display='flex';grid.style.display='none';list.style.display='none';return;}
  empty.style.display='none';
  if(currentView==='grid'){
    grid.style.display='grid';list.style.display='none';
    grid.innerHTML=docs.map(doc=>`<div class="doc-card" onclick="openDoc('${doc.id}')"><div class="dc-preview"><div class="dc-type-mark">${doc.fileExt.toUpperCase()}</div><div class="dc-actions"><button class="dc-action-btn" onclick="event.stopPropagation();downloadDocById('${doc.id}')" title="Download"><svg viewBox="0 0 12 12" fill="none"><path d="M6 1v7M3 6l3 3 3-3" stroke-width="1.3" stroke-linecap="round"/><path d="M1 10h10" stroke-width="1.3"/></svg></button><button class="dc-action-btn del" onclick="event.stopPropagation();deleteDoc('${doc.id}')" title="Delete"><svg viewBox="0 0 12 12" fill="none"><path d="M2 3h8M5 3V2h2v1M4 3v6h4V3H4z" stroke-width="1.2" stroke-linecap="round"/></svg></button></div></div><div class="dc-name" title="${doc.originalName}">${doc.originalName}</div><div class="dc-meta"><span>${fmtBytes(doc.fileSize)}</span><span>${fmtDate(doc.uploadedAt)}</span></div></div>`).join('');
  } else {
    grid.style.display='none';list.style.display='flex';
    list.innerHTML=docs.map(doc=>`<div class="doc-list-item" onclick="openDoc('${doc.id}')"><div class="dli-ext">${doc.fileExt.toUpperCase()}</div><span class="dli-name">${doc.originalName}</span><span class="dli-size">${fmtBytes(doc.fileSize)}</span><span class="dli-date">${fmtDate(doc.uploadedAt)}</span><div class="dli-actions"><button class="dli-btn" onclick="event.stopPropagation();downloadDocById('${doc.id}')" title="Download"><svg viewBox="0 0 12 12" fill="none"><path d="M6 1v7M3 6l3 3 3-3" stroke-width="1.3" stroke-linecap="round"/><path d="M1 10h10" stroke-width="1.3"/></svg></button><button class="dli-btn del" onclick="event.stopPropagation();deleteDoc('${doc.id}')" title="Delete"><svg viewBox="0 0 12 12" fill="none"><path d="M2 3h8M5 3V2h2v1M4 3v6h4V3H4z" stroke-width="1.2" stroke-linecap="round"/></svg></button></div></div>`).join('');
  }
}

function renderRecent(){
  const listEl=document.getElementById('recent-list');
  const cutoff=new Date(Date.now()-7*86400000);
  const recent=currentDocs.filter(d=>new Date(d.uploadedAt)>cutoff).sort((a,b)=>new Date(b.uploadedAt)-new Date(a.uploadedAt));
  if(!recent.length){listEl.innerHTML=`<div class="empty-state"><div class="es-mark"><svg viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="9" stroke-width="1.8"/><path d="M14 9v6l3.5 2" stroke-width="1.8" stroke-linecap="round"/></svg></div><h3>NO RECENT ACTIVITY</h3><p>// uploaded documents will appear here</p></div>`;return;}
  listEl.innerHTML=recent.map(doc=>`<div class="doc-list-item" onclick="openDoc('${doc.id}')"><div class="dli-ext">${doc.fileExt.toUpperCase()}</div><span class="dli-name">${doc.originalName}</span><span class="dli-size">${fmtBytes(doc.fileSize)}</span><span class="dli-date">${fmtDate(doc.uploadedAt)}</span></div>`).join('');
}

function openDoc(id){
  selectedDoc=currentDocs.find(d=>d.id===id);if(!selectedDoc)return;
  document.getElementById('modal-doc-name').textContent=selectedDoc.originalName;
  document.getElementById('modal-doc-meta').textContent=`${selectedDoc.fileExt.toUpperCase()} · ${fmtBytes(selectedDoc.fileSize)}`;
  document.getElementById('modal-size').textContent=fmtBytes(selectedDoc.fileSize);
  document.getElementById('modal-date').textContent=fmtDate(selectedDoc.uploadedAt);
  document.getElementById('modal-scan-status').textContent=selectedDoc.scanStatus==='done'?'SCANNED':'PROCESSING';
  document.getElementById('modal-scan-status').className='mi-value'+(selectedDoc.scanStatus==='done'?' ok':'');
  const prev=document.getElementById('modal-preview');
  if(['png','jpg','jpeg','gif','webp'].includes(selectedDoc.fileExt)&&selectedDoc.dataUrl){
    prev.innerHTML=`<img src="${selectedDoc.dataUrl}" style="max-width:100%;max-height:160px;object-fit:contain;">`;
  } else {
    prev.innerHTML=`<div class="mp-type">${selectedDoc.fileExt.toUpperCase()}</div>`;
  }
  document.getElementById('doc-modal').classList.add('show');
}

function closeModal(){document.getElementById('doc-modal').classList.remove('show');selectedDoc=null;}

function downloadDoc(){
  if(!selectedDoc)return;
  if(selectedDoc.dataUrl){const a=document.createElement('a');a.href=selectedDoc.dataUrl;a.download=selectedDoc.originalName;a.click();showToast(`// downloading ${selectedDoc.originalName}`,'info');}
  else showToast('// file data not available (>10 MB)','info');
  closeModal();
}

function downloadDocById(id){
  const doc=currentDocs.find(d=>d.id===id);if(!doc)return;
  if(doc.dataUrl){const a=document.createElement('a');a.href=doc.dataUrl;a.download=doc.originalName;a.click();showToast(`// downloading ${doc.originalName}`,'info');}
  else showToast('// file data not available (>10 MB)','info');
}

function deleteDoc(id){
  const doc=currentDocs.find(d=>d.id===id);if(!doc)return;
  const localKey='docvault_docs_'+currentUser.id;
  const updated=currentDocs.filter(d=>d.id!==id);
  localStorage.setItem(localKey,JSON.stringify(updated));
  currentDocs=updated;
  updateStorageUI();updateStats();renderDocs();
  showToast(`// ${doc.originalName} deleted`,'info');
}

// ── Dark theme (unchanged) ────────────────────────────────────
function toggleTheme(){
  const isDark=document.documentElement.classList.toggle('dark');
  localStorage.setItem('docvault_theme',isDark?'dark':'light');
}
function initTheme(){
  const saved=localStorage.getItem('docvault_theme');
  const prefersDark=window.matchMedia('(prefers-color-scheme: dark)').matches;
  if(saved==='dark'||(saved===null&&prefersDark)) document.documentElement.classList.add('dark');
}
initTheme();

// ── Folders (unchanged) ───────────────────────────────────────
let currentFolders=[],openFolderId=null;

function getFolders(){
  if(!currentUser)return[];
  return JSON.parse(localStorage.getItem('docvault_folders_'+currentUser.id)||'[]');
}
function saveFolders(f){localStorage.setItem('docvault_folders_'+currentUser.id,JSON.stringify(f));}

function openNewFolderModal(){
  document.getElementById('folder-name-input').value='';
  document.querySelectorAll('.fc-swatch').forEach(s=>s.classList.remove('active'));
  const first=document.querySelector('.fc-swatch');if(first)first.classList.add('active');
  document.getElementById('new-folder-modal').classList.add('show');
  setTimeout(()=>document.getElementById('folder-name-input').focus(),120);
}
function closeNewFolderModal(){document.getElementById('new-folder-modal').classList.remove('show');}

function selectFolderColor(el){
  document.querySelectorAll('.fc-swatch').forEach(s=>s.classList.remove('active'));
  el.classList.add('active');
}

function createFolder(){
  const name=document.getElementById('folder-name-input').value.trim();
  if(!name){showToast('// enter a folder name','error');return;}
  const folders=getFolders();
  if(folders.find(f=>f.name.toLowerCase()===name.toLowerCase())){showToast('// folder name already exists','error');return;}
  const activeColor=document.querySelector('.fc-swatch.active');
  const color=activeColor?activeColor.dataset.color:'#7a7a7a';
  folders.push({id:'fld_'+uuid(),name,color,createdAt:new Date().toISOString(),docIds:[]});
  saveFolders(folders);closeNewFolderModal();renderFolders();
  showToast(`// folder "${name}" created`,'success');
}

function deleteFolder(id){
  const folders=getFolders().filter(f=>f.id!==id);
  saveFolders(folders);renderFolders();
  if(openFolderId===id){openFolderId=null;showFolderRoot();}
  showToast('// folder deleted','info');
}

function openFolder(id){
  openFolderId=id;
  const folder=getFolders().find(f=>f.id===id);if(!folder)return;
  const bc=document.getElementById('folder-breadcrumb');
  bc.style.display='flex';
  bc.innerHTML=`<span onclick="showFolderRoot()">FOLDERS</span><span class="bc-sep">/</span><span class="bc-current">${folder.name}</span>`;
  document.getElementById('folder-grid-wrap').style.display='none';
  document.getElementById('folder-docs-wrap').style.display='block';
  renderFolderDocs(folder);
}

function showFolderRoot(){
  openFolderId=null;
  document.getElementById('folder-breadcrumb').style.display='none';
  document.getElementById('folder-grid-wrap').style.display='block';
  document.getElementById('folder-docs-wrap').style.display='none';
  renderFolders();
}

function renderFolders(){
  currentFolders=getFolders();
  const grid=document.getElementById('folders-grid');
  const empty=document.getElementById('folders-empty');
  const browser=document.getElementById('folder-browser');
  document.getElementById('sb-folder-count').textContent=currentFolders.length;
  if(!currentFolders.length){empty.style.display='flex';browser.style.display='none';return;}
  empty.style.display='none';browser.style.display='block';
  grid.innerHTML=currentFolders.map(f=>{
    const count=f.docIds?f.docIds.length:0;
    return `<div class="folder-card" onclick="openFolder('${f.id}')"><div class="folder-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M3 7h7l2 3h9v9H3z" stroke-width="1.5" stroke-linejoin="round"/></svg></div><div class="folder-info"><div class="folder-name" title="${f.name}">${f.name}</div><div class="folder-count">${count} file${count!==1?'s':''}</div></div><div class="folder-actions"><button class="folder-action-btn" onclick="event.stopPropagation();openRenameFolderModal('${f.id}','${f.name.replace(/'/g,"\\'")}' )" title="Rename"><svg viewBox="0 0 12 12" fill="none"><path d="M2 9L8 3l2 2-6 6H2V9z" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button class="folder-action-btn del" onclick="event.stopPropagation();deleteFolder('${f.id}')" title="Delete"><svg viewBox="0 0 12 12" fill="none"><path d="M2 3h8M5 3V2h2v1M4 3v6h4V3H4z" stroke-width="1.2" stroke-linecap="round"/></svg></button></div></div>`;
  }).join('');
}

function renderFolderDocs(folder){
  const docs=currentDocs.filter(d=>folder.docIds&&folder.docIds.includes(d.id));
  const listEl=document.getElementById('folder-docs-list');
  const empty=document.getElementById('folder-docs-empty');
  if(!docs.length){empty.style.display='flex';listEl.style.display='none';return;}
  empty.style.display='none';listEl.style.display='flex';
  listEl.innerHTML=docs.map(doc=>`<div class="doc-list-item" onclick="openDoc('${doc.id}')"><div class="dli-ext">${doc.fileExt.toUpperCase()}</div><span class="dli-name">${doc.originalName}</span><span class="dli-size">${fmtBytes(doc.fileSize)}</span><span class="dli-date">${fmtDate(doc.uploadedAt)}</span><div class="dli-actions"><button class="dli-btn del" onclick="event.stopPropagation();removeDocFromFolder('${doc.id}')" title="Remove from folder"><svg viewBox="0 0 12 12" fill="none"><path d="M2 3h8M5 3V2h2v1M4 3v6h4V3H4z" stroke-width="1.2" stroke-linecap="round"/></svg></button></div></div>`).join('');
}

function removeDocFromFolder(docId){
  const folders=getFolders();
  const folder=folders.find(f=>f.id===openFolderId);if(!folder)return;
  folder.docIds=folder.docIds.filter(id=>id!==docId);
  saveFolders(folders);renderFolderDocs(folder);showToast('// document removed from folder','info');
}

function openMoveToFolderModal(){
  const modal=document.getElementById('move-folder-modal');
  const listEl=document.getElementById('move-doc-list');
  const folder=getFolders().find(f=>f.id===openFolderId);
  const existing=folder?folder.docIds||[]:[];
  if(!currentDocs.length){showToast('// no documents to add','info');return;}
  listEl.innerHTML=currentDocs.map(doc=>{
    const checked=existing.includes(doc.id);
    return `<label style="display:flex;align-items:center;gap:10px;padding:9px 13px;border-bottom:1px solid var(--g6);cursor:pointer;font-family:var(--fm);font-size:12px;color:var(--black);"><input type="checkbox" value="${doc.id}" ${checked?'checked':''} style="accent-color:var(--black);width:13px;height:13px;"><span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${doc.originalName}</span><span style="color:var(--g4);font-size:10px">${fmtBytes(doc.fileSize)}</span></label>`;
  }).join('');
  modal.classList.add('show');
}
function closeMoveToFolderModal(){document.getElementById('move-folder-modal').classList.remove('show');}

function addDocsToFolder(){
  const checked=Array.from(document.querySelectorAll('#move-doc-list input[type=checkbox]:checked')).map(c=>c.value);
  const folders=getFolders();
  const folder=folders.find(f=>f.id===openFolderId);if(!folder)return;
  folder.docIds=checked;
  saveFolders(folders);closeMoveToFolderModal();renderFolderDocs(folder);
  showToast(`// ${checked.length} document${checked.length!==1?'s':''} updated in folder`,'success');
}

document.getElementById('new-folder-modal').addEventListener('click',function(e){if(e.target===this)closeNewFolderModal();});
document.getElementById('move-folder-modal').addEventListener('click',function(e){if(e.target===this)closeMoveToFolderModal();});
document.getElementById('rename-folder-modal').addEventListener('click',function(e){if(e.target===this)closeRenameFolderModal();});

let renameFolderId=null;
function openRenameFolderModal(id,currentName){
  renameFolderId=id;document.getElementById('rename-folder-input').value=currentName;
  document.getElementById('rename-folder-modal').classList.add('show');
  setTimeout(()=>{const el=document.getElementById('rename-folder-input');el.focus();el.select();},120);
}
function closeRenameFolderModal(){document.getElementById('rename-folder-modal').classList.remove('show');renameFolderId=null;}
function confirmRenameFolder(){
  const name=document.getElementById('rename-folder-input').value.trim();
  if(!name){showToast('// enter a folder name','error');return;}
  const folders=getFolders();
  const folder=folders.find(f=>f.id===renameFolderId);if(!folder){closeRenameFolderModal();return;}
  if(folders.find(f=>f.id!==renameFolderId&&f.name.toLowerCase()===name.toLowerCase())){showToast('// folder name already exists','error');return;}
  folder.name=name;saveFolders(folders);closeRenameFolderModal();renderFolders();
  showToast(`// folder renamed to "${name}"`,'success');
}

// ── ★ Session persistence: auto-login if PHP session is active ─
if(PHP_SESSION_USER){
  // PHP already validated the session; hydrate the dashboard directly
  bootstrapUser(PHP_SESSION_USER);
}
</script>
</body>
</html>
