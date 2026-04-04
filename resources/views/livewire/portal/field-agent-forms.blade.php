<div>
<style>
  :root {
    --green-dark: #1a4731;
    --green-mid: #2d6a4f;
    --green-light: #40916c;
    --green-accent: #52b788;
    --amber: #f59e0b;
    --amber-light: #fef3c7;
    --teal: #0d9488;
    --red: #dc2626;
    --bg: #f8faf9;
    --white: #ffffff;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --border: #d1fae5;
    --radius: 8px;
    --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.07), 0 2px 4px rgba(0,0,0,0.05);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--gray-800);
    font-size: 14px;
    line-height: 1.5;
  }

  /* NAV TABS */
  .tab-nav {
    background: var(--green-dark);
    display: flex;
    gap: 0;
    padding: 0 24px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }

  .tab-btn {
    background: none;
    border: none;
    color: rgba(255,255,255,0.6);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 16px 24px;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
  }

  .tab-btn:hover { color: rgba(255,255,255,0.9); }

  .tab-btn.active {
    color: #fff;
    border-bottom-color: var(--green-accent);
  }

  /* HERO HEADER */
  .hero {
    background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-mid) 60%, var(--green-light) 100%);
    color: white;
    padding: 48px 40px 40px;
    position: relative;
    overflow: hidden;
  }

  .hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.04) 0%, transparent 50%),
                      radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 50%);
  }

  .hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 16px;
  }

  .hero h1 {
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 10px;
  }

  .hero p {
    font-size: 14px;
    opacity: 0.8;
    max-width: 480px;
    margin-bottom: 28px;
  }

  .hero-meta {
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
  }

  .hero-meta-item strong {
    display: block;
    color: var(--green-accent);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 2px;
  }

  .hero-meta-item span {
    font-size: 13px;
    opacity: 0.85;
  }

  /* FORM PAGE */
  .form-page {
    display: none;
    max-width: 860px;
    margin: 0 auto;
    padding: 32px 24px 64px;
  }

  .form-page.active { display: block; }

  /* FORM HEADER */
  .form-header {
    background: var(--green-dark);
    border-radius: var(--radius) var(--radius) 0 0;
    padding: 20px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
  }

  .form-header-left { display: flex; align-items: center; gap: 14px; }

  .nj-logo {
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
    color: white;
    letter-spacing: -0.5px;
  }

  .form-header-title { color: white; }
  .form-header-title h2 { font-size: 18px; font-weight: 700; }
  .form-header-title p { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.7; }

  .form-badge {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 6px;
    padding: 6px 14px;
    text-align: right;
  }

  .form-badge strong { display: block; color: white; font-size: 13px; font-weight: 700; }
  .form-badge span { color: rgba(255,255,255,0.7); font-size: 11px; }

  /* FORM BODY */
  .form-body {
    background: white;
    border: 1px solid var(--gray-200);
    border-top: none;
    border-radius: 0 0 var(--radius) var(--radius);
    padding: 32px 28px;
    box-shadow: var(--shadow-md);
  }

  /* INSTRUCTIONS BOX */
  .instructions {
    background: #eff9f4;
    border: 1px solid #bbf7d0;
    border-left: 4px solid var(--green-accent);
    border-radius: var(--radius);
    padding: 16px 18px;
    margin-bottom: 28px;
  }

  .instructions h4 {
    color: var(--green-dark);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .instructions ul { list-style: none; padding: 0; }

  .instructions li {
    position: relative;
    padding-left: 16px;
    margin-bottom: 6px;
    font-size: 13px;
    color: var(--gray-700);
    line-height: 1.5;
  }

  .instructions li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: var(--green-accent);
    font-weight: 700;
  }

  .instructions li strong { color: var(--green-dark); }

  /* SECTION */
  .section {
    margin-bottom: 32px;
  }

  .section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--green-accent);
  }

  .section-num {
    width: 28px; height: 28px;
    background: var(--green-accent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
  }

  .section-title h3 {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--green-dark);
  }

  /* FORM FIELDS */
  .field-row {
    display: grid;
    gap: 16px;
    margin-bottom: 16px;
  }

  .field-row.cols-2 { grid-template-columns: 1fr 1fr; }
  .field-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
  .field-row.cols-1-1-2 { grid-template-columns: 1fr 1fr 2fr; }
  .field-row.cols-2-1 { grid-template-columns: 2fr 1fr; }

  .field { display: flex; flex-direction: column; gap: 5px; }

  .field label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--gray-600);
  }

  .field label .req {
    color: var(--red);
    margin-left: 2px;
  }

  .field input[type="text"],
  .field input[type="date"],
  .field input[type="time"],
  .field input[type="number"],
  .field textarea,
  .field select {
    border: 1.5px solid var(--gray-200);
    border-radius: 6px;
    padding: 9px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--gray-800);
    background: var(--gray-50);
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    width: 100%;
  }

  .field input:focus,
  .field textarea:focus,
  .field select:focus {
    border-color: var(--green-accent);
    box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
    background: white;
  }

  .field textarea { resize: vertical; min-height: 80px; }

  /* CHECKBOXES & RADIOS */
  .checkbox-group, .radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 20px;
    margin-top: 4px;
  }

  .checkbox-group label, .radio-group label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 400;
    letter-spacing: 0;
    text-transform: none;
    color: var(--gray-700);
    cursor: pointer;
  }

  .checkbox-group input[type="checkbox"],
  .radio-group input[type="radio"] {
    width: 16px; height: 16px;
    accent-color: var(--green-mid);
    cursor: pointer;
    flex-shrink: 0;
  }

  /* GPS BOX */
  .gps-note {
    background: var(--amber-light);
    border: 1px solid #fcd34d;
    border-left: 4px solid var(--amber);
    border-radius: var(--radius);
    padding: 12px 14px;
    margin-top: 12px;
    font-size: 12px;
    color: #92400e;
    line-height: 1.5;
  }

  .gps-note strong { color: #78350f; }

  /* TABLE */
  .data-table {
    width: 100%;
    border-collapse: collapse;
    border-radius: var(--radius);
    overflow: hidden;
    border: 1.5px solid var(--gray-200);
    margin-bottom: 4px;
  }

  .data-table thead tr {
    background: var(--green-dark);
    color: white;
  }

  .data-table thead th {
    padding: 11px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .data-table tbody tr:nth-child(even) { background: var(--gray-50); }
  .data-table tbody tr:last-child { background: #d1fae5; font-weight: 700; }

  .data-table tbody td {
    padding: 0;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
  }

  .data-table tbody td:first-child {
    padding: 10px 14px;
    font-size: 13px;
    color: var(--gray-700);
  }

  .data-table tbody td input[type="text"],
  .data-table tbody td input[type="number"] {
    width: 100%;
    border: none;
    background: transparent;
    padding: 10px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--gray-800);
    outline: none;
  }

  .data-table tbody td input:focus {
    background: #eff9f4;
  }

  /* ROAD CONDITION BAR */
  .condition-bar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 16px;
  }

  .condition-bar label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 8px;
    cursor: pointer;
    border-right: 1px solid var(--gray-200);
    transition: background 0.15s;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--gray-500);
  }

  .condition-bar label:last-child { border-right: none; }
  .condition-bar input[type="radio"] { display: none; }

  .condition-bar label:has(input:checked) { background: #d1fae5; color: var(--green-dark); }
  .condition-bar label:nth-child(4):has(input:checked) { background: #fef3c7; color: #92400e; }
  .condition-bar label:nth-child(5):has(input:checked) { background: #fee2e2; color: #991b1b; }

  /* OUTCOME OPTIONS */
  .outcome-options { display: flex; flex-direction: column; gap: 10px; }

  .outcome-option {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.15s;
  }

  .outcome-option:hover { border-color: var(--green-accent); background: #eff9f4; }
  .outcome-option input[type="radio"] { margin-top: 2px; accent-color: var(--green-mid); }

  .outcome-option strong { font-size: 13px; color: var(--green-dark); }
  .outcome-option p { font-size: 12px; color: var(--gray-600); margin-top: 2px; }

  /* SIGNATURE GRID */
  .sig-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
  }

  .sig-box {
    display: flex;
    flex-direction: column;
  }

  .sig-line {
    border-bottom: 1.5px solid var(--gray-400);
    height: 48px;
    margin-bottom: 6px;
  }

  .sig-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gray-500);
  }

  /* ADMIN BOX */
  .admin-box {
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-top: 20px;
  }

  .admin-box-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gray-600);
    margin-bottom: 14px;
  }

  .admin-box-title::before {
    content: '🔒';
    font-size: 14px;
  }

  /* PAGE BREAK */
  .page-break {
    border: none;
    border-top: 2px dashed var(--gray-200);
    margin: 36px 0;
  }

  .page-2-header {
    background: var(--green-dark);
    border-radius: var(--radius) var(--radius) 0 0;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
    color: white;
  }

  .page-2-header-left { display: flex; align-items: center; gap: 12px; }
  .page-2-header h3 { font-size: 16px; font-weight: 700; }
  .page-2-header p { font-size: 11px; opacity: 0.6; }

  .page-2-badge {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    color: white;
  }

  .agent-date-bar {
    background: white;
    border: 1px solid var(--gray-200);
    border-top: none;
    padding: 0 0 0 24px;
    display: flex;
    align-items: center;
    gap: 32px;
    height: 44px;
    font-size: 12px;
    color: var(--gray-500);
    font-family: 'DM Mono', monospace;
    border-bottom: none;
  }

  .agent-date-bar span { display: flex; gap: 8px; align-items: center; }
  .agent-date-bar strong { font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

  /* MEDIA LOG TABLE */
  .media-log {
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 12px;
  }

  .media-log table { width: 100%; border-collapse: collapse; }

  .media-log thead tr { background: var(--green-dark); }
  .media-log thead th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: white;
  }

  .media-log tbody tr { border-bottom: 1px solid var(--gray-200); }
  .media-log tbody tr:last-child { border-bottom: none; }

  .media-log tbody td {
    padding: 8px 12px;
    font-size: 12px;
    vertical-align: middle;
  }

  .media-log tbody td input[type="text"] {
    width: 100%;
    border: none;
    background: transparent;
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    outline: none;
  }

  .media-log .type-cell {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--gray-600);
  }

  .media-log .type-cell input { accent-color: var(--green-mid); }

  /* MEDIA NOTE */
  .media-note {
    background: var(--amber-light);
    border: 1px solid #fcd34d;
    border-left: 4px solid var(--amber);
    border-radius: var(--radius);
    padding: 10px 14px;
    font-size: 12px;
    color: #92400e;
    margin-top: 8px;
  }

  /* TOWNS GRID */
  .towns-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }

  .town-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.15s;
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
  }

  .town-card:hover { border-color: var(--green-accent); background: #eff9f4; }
  .town-card input[type="checkbox"] { accent-color: var(--green-mid); width: 15px; height: 15px; }

  .town-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  /* WAYPOINT TABLE */
  .waypoint-table { width: 100%; border-collapse: collapse; border: 1.5px solid var(--gray-200); border-radius: var(--radius); overflow: hidden; }
  .waypoint-table thead tr { background: var(--green-dark); }
  .waypoint-table thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: white; }
  .waypoint-table tbody tr { border-bottom: 1px solid var(--gray-200); }
  .waypoint-table tbody td:first-child { padding: 10px 14px; font-size: 12px; font-weight: 600; color: var(--green-dark); }
  .waypoint-table tbody td input { width: 100%; border: none; background: transparent; padding: 10px 12px; font-family: 'DM Sans', sans-serif; font-size: 13px; outline: none; }
  .waypoint-table tbody td input:focus { background: #eff9f4; }

  /* SKETCH */
  .sketch-area {
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius);
    background: var(--gray-50);
    min-height: 160px;
    padding: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 12px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    position: relative;
  }

  .north-indicator {
    position: absolute;
    bottom: 12px;
    right: 14px;
    font-size: 11px;
    color: var(--gray-400);
    font-style: italic;
  }

  /* SUBMIT BTN */
  .submit-bar {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-200);
  }

  .btn {
    padding: 11px 24px;
    border-radius: 7px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
  }

  .btn-secondary {
    background: var(--gray-100);
    color: var(--gray-600);
    border: 1.5px solid var(--gray-200);
  }

  .btn-secondary:hover { background: var(--gray-200); }

  .btn-primary {
    background: var(--green-mid);
    color: white;
  }

  .btn-primary:hover { background: var(--green-dark); }

  /* FOOTER */
  .form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding: 10px 0;
    font-size: 11px;
    color: var(--gray-400);
    font-family: 'DM Mono', monospace;
    border-top: 1px solid var(--gray-200);
  }

  /* PRINT */
  @media print {
    .tab-nav { display: none; }
    .form-page { display: block !important; }
    .submit-bar { display: none; }
  }

  @media (max-width: 640px) {
    .field-row.cols-2, .field-row.cols-3, .field-row.cols-1-1-2, .field-row.cols-2-1 { grid-template-columns: 1fr; }
    .sig-grid { grid-template-columns: 1fr 1fr; }
    .towns-grid { grid-template-columns: 1fr 1fr; }
    .condition-bar { grid-template-columns: 1fr 1fr; }
    .hero h1 { font-size: 24px; }
    .hero-meta { gap: 20px; }
  }
</style>

<!-- NAV -->
<div class="tab-nav">
  <button class="tab-btn {{ $activeForm === 'form-a' ? 'active' : '' }}" wire:click="swapForm('form-a')">Form A — Street Revalidation</button>
  <button class="tab-btn {{ $activeForm === 'form-b' ? 'active' : '' }}" wire:click="swapForm('form-b')">Form B — New Street Suggestion</button>
</div>

<!-- HERO -->
<div class="hero">
  <div class="hero-badge">Official Field Collection Forms</div>
  <h1>NDSMS Field Agent<br>Data Collection Forms</h1>
  <p>Standardised forms for street revalidation surveys and new street/close proposals across all six towns of Njikoka Local Government Area.</p>
  <div class="hero-meta">
    <div class="hero-meta-item"><strong>Version</strong><span>2.0 — March 2026</span></div>
    <div class="hero-meta-item"><strong>Form Types</strong><span>Revalidation + New Street</span></div>
    <div class="hero-meta-item"><strong>Coverage</strong><span>6 Towns — Full LGA</span></div>
    <div class="hero-meta-item"><strong>GPS</strong><span>Auto Lat/Lng Capture</span></div>
  </div>
</div>

<!-- ===== FORM A ===== -->
<div id="form-a" class="form-page" style="display: {{ $activeForm === 'form-a' ? 'block' : 'none' }}">

  <div style="margin-top: 28px;">
    <div class="form-header">
      <div class="form-header-left">
        <div class="nj-logo">NJ</div>
        <div class="form-header-title">
          <h2>Njikoka LGA — NDSMS</h2>
          <p>Digital Street Management System</p>
        </div>
      </div>
      <div class="form-badge">
        <strong>FORM A</strong>
        <span>Street Revalidation Field Report</span>
      </div>
    </div>

    <div class="form-body">
      <!-- TITLE -->
      <div style="text-align:center; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-200);">
        <p style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:var(--green-accent); font-weight:700; margin-bottom:6px;">📋 Street Revalidation & Field Verification Report</p>
      </div>

      <!-- INSTRUCTIONS -->
      <div class="instructions">
        <h4>Instructions for Field Agent</h4>
        <ul>
          <li>Complete <strong>all fields marked with *</strong> (mandatory). Use BLOCK CAPITALS for handwritten entries.</li>
          <li>GPS coordinates are <strong>auto-captured</strong> by the mobile app. For paper forms, use a GPS device or phone.</li>
          <li>Attach photos and videos using the media log below. Number each file to match the reference column.</li>
          <li>Submit completed forms to your Ward Supervisor within <strong>24 hours</strong> of field visit.</li>
        </ul>
      </div>

      <!-- SECTION 1 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num">1</div>
          <h3>Agent & Assignment Details</h3>
        </div>

        <div class="field-row cols-3">
          <div class="field">
            <label>Agent Full Name <span class="req">*</span></label>
            <input type="text" placeholder="Full name in BLOCK CAPITALS" wire:model="formA.agent_name">
          </div>
          <div class="field">
            <label>Agent ID <span class="req">*</span></label>
            <input type="text" placeholder="e.g. AGT-0012" wire:model="formA.agent_id">
          </div>
          <div class="field">
            <label>Phone Number <span class="req">*</span></label>
            <input type="text" placeholder="+234 xxx xxxx xxx" wire:model="formA.phone">
          </div>
        </div>

        <div class="field-row" style="grid-template-columns: auto 1fr 1fr">
          <div class="field">
            <label>Assigned Town <span class="req">*</span></label>
            <div class="towns-grid" style="grid-template-columns: 1fr 1fr; gap:6px; margin-top:4px;">
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Abagana"><span class="town-dot" style="background:#10b981"></span>Abagana</label>
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Enugwu-Ukwu"><span class="town-dot" style="background:#3b82f6"></span>Enugwu-Ukwu</label>
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Nimo"><span class="town-dot" style="background:#8b5cf6"></span>Nimo</label>
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Nawfia"><span class="town-dot" style="background:#f59e0b"></span>Nawfia</label>
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Enugwu-Agidi"><span class="town-dot" style="background:#ec4899"></span>Enugwu-Agidi</label>
              <label class="town-card"><input type="checkbox" wire:model="formA.towns" value="Abba"><span class="town-dot" style="background:#ef4444"></span>Abba</label>
            </div>
          </div>
          <div class="field">
            <label>Date of Field Visit <span class="req">*</span></label>
            <input type="date" wire:model="formA.visit_date">
          </div>
          <div class="field">
            <label>Time Started — Time Ended <span class="req">*</span></label>
            <div style="display:flex;gap:8px;">
              <input type="time" placeholder="Start" wire:model="formA.start_time">
              <input type="time" placeholder="End" wire:model="formA.end_time">
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num">2</div>
          <h3>Street Identification</h3>
        </div>

        <div class="field-row cols-2-1">
          <div class="field">
            <label>Official Street Name (as Registered) <span class="req">*</span></label>
            <input type="text" placeholder="As it appears in NDSMS records" wire:model="formA.street_name">
          </div>
          <div class="field">
            <label>NDSMS Street Code <span class="req">*</span></label>
            <input type="text" placeholder="e.g. ABG-RD-0045" wire:model="formA.street_code">
          </div>
        </div>

        <div class="field-row" style="grid-template-columns:1fr auto;">
          <div class="field-row cols-2" style="margin:0">
            <div class="field">
              <label>Local/Common Name (if different)</label>
              <input type="text" placeholder="Alternative name used locally">
            </div>
            <div class="field">
              <label>Landmark / Known Reference Point</label>
              <input type="text" placeholder="e.g. Near St. Peter's Church">
            </div>
          </div>
          <div class="field">
            <label>Street Type <span class="req">*</span></label>
            <div class="checkbox-group" style="display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;margin-top:4px;">
              <label><input type="checkbox"> Road</label>
              <label><input type="checkbox"> Avenue</label>
              <label><input type="checkbox"> Close</label>
              <label><input type="checkbox"> Lane</label>
              <label><input type="checkbox"> Crescent</label>
              <label><input type="checkbox"> Drive</label>
              <label><input type="checkbox"> Court</label>
              <label><input type="checkbox"> Way</label>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num">3</div>
          <h3>GPS Coordinates (Auto-Captured)</h3>
        </div>

        <div class="field-row" style="grid-template-columns:1fr 1fr 1fr 1fr;">
          <div class="field">
            <label>Start Point — Latitude <span class="req">*</span></label>
            <input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;">
          </div>
          <div class="field">
            <label>Start Point — Longitude <span class="req">*</span></label>
            <input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;">
          </div>
          <div class="field">
            <label>End Point — Latitude <span class="req">*</span></label>
            <input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;">
          </div>
          <div class="field">
            <label>End Point — Longitude <span class="req">*</span></label>
            <input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;">
          </div>
        </div>

        <div class="gps-note">
          <strong>GPS NOTE:</strong> Coordinates auto-fill from the NDSMS mobile app. For paper forms, record from phone GPS (Settings → About Phone → Location, or Google Maps long-press). Format: <strong>6.XXXXXX, 7.XXXXXX</strong>
        </div>
      </div>

      <!-- SECTION 4 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num">4</div>
          <h3>Physical Assessment & Condition</h3>
        </div>

        <div class="field-row cols-3" style="margin-bottom:16px;">
          <div class="field">
            <label>Estimated Length (Metres) <span class="req">*</span></label>
            <input type="number" placeholder="e.g. 250">
          </div>
          <div class="field">
            <label>Estimated Width (Metres) <span class="req">*</span></label>
            <input type="number" placeholder="e.g. 6">
          </div>
          <div class="field">
            <label>Surface Type <span class="req">*</span></label>
            <div class="checkbox-group" style="display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;margin-top:4px;">
              <label><input type="checkbox"> Tarred</label>
              <label><input type="checkbox"> Gravel</label>
              <label><input type="checkbox"> Laterite</label>
              <label><input type="checkbox"> Earth</label>
              <label><input type="checkbox"> Concrete</label>
            </div>
          </div>
        </div>

        <div class="field" style="margin-bottom:14px;">
          <label>Overall Road Condition <span class="req">*</span> — Tick One</label>
          <div class="condition-bar" style="margin-top:6px;">
            <label><input type="radio" name="condition"> Excellent</label>
            <label><input type="radio" name="condition"> Good</label>
            <label><input type="radio" name="condition"> Fair</label>
            <label><input type="radio" name="condition"> Poor</label>
            <label><input type="radio" name="condition"> Impassable</label>
          </div>
        </div>

        <div class="field">
          <label>Infrastructure Present — Tick All That Apply</label>
          <div class="checkbox-group" style="margin-top:6px;">
            <label><input type="checkbox"> Street lighting</label>
            <label><input type="checkbox"> Drainage/gutters</label>
            <label><input type="checkbox"> Pavement/walkway</label>
            <label><input type="checkbox"> Street sign/nameplate</label>
            <label><input type="checkbox"> Speed bumps</label>
            <label><input type="checkbox"> Address plates visible</label>
            <label><input type="checkbox"> Electricity poles</label>
            <label><input type="checkbox"> Water mains</label>
          </div>
        </div>
      </div>

      <!-- SECTION 5 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num">5</div>
          <h3>Properties & Buildings Count</h3>
        </div>

        <table class="data-table">
          <thead>
            <tr>
              <th>Property Type</th>
              <th>Count (Left Side)</th>
              <th>Count (Right Side)</th>
              <th>Total</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Residential</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Commercial / Shop</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Government / Public</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Religious (Church/Mosque)</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Educational (School)</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Undeveloped / Empty Plot</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Under Construction</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td><strong>Grand Total</strong></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
          </tbody>
        </table>
      </div>

      <div class="form-footer">
        <span>Njikoka LGA — NDSMS Field Agent Form A — Street Revalidation</span>
        <span>Page 1 of 2 &nbsp;|&nbsp; NDSMS-FA-v2.0</span>
      </div>
    </div>

    <!-- PAGE 2 -->
    <div style="margin-top: 28px;">
      <div class="page-2-header">
        <div class="page-2-header-left">
          <div class="nj-logo">NJ</div>
          <div>
            <h3>FORM A — Street Revalidation (continued)</h3>
            <p>AGENT ID: _________________ &nbsp;&nbsp; DATE: _________________</p>
          </div>
        </div>
        <div class="page-2-badge">PAGE 2/2</div>
      </div>

      <div class="form-body" style="border-top: 3px solid var(--green-accent);">

        <!-- SECTION 6 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num">6</div>
            <h3>Issues, Discrepancies & Observations</h3>
          </div>

          <div class="field" style="margin-bottom:14px;">
            <label>Does the Street Name on Signage Match NDSMS Records? <span class="req">*</span></label>
            <div class="radio-group" style="margin-top:6px;">
              <label><input type="radio" name="sign_match"> Yes, matches exactly</label>
              <label><input type="radio" name="sign_match"> Partially matches (spelling variation)</label>
              <label><input type="radio" name="sign_match"> No signage found</label>
              <label><input type="radio" name="sign_match"> Does not match</label>
            </div>
          </div>

          <div class="field" style="margin-bottom:16px;">
            <label>If Name Does Not Match, What is Shown on Signage?</label>
            <input type="text" placeholder="Write exactly what appears on the sign">
          </div>

          <div class="field" style="margin-bottom:16px;">
            <label>Issues Found — Tick All That Apply</label>
            <div class="checkbox-group" style="margin-top:6px;display:grid;grid-template-columns:1fr 1fr;gap:8px 24px;">
              <label><input type="checkbox"> Street name misspelled on sign</label>
              <label><input type="checkbox"> Sign damaged or unreadable</label>
              <label><input type="checkbox"> Wrong street type on records</label>
              <label><input type="checkbox"> Property count differs from records</label>
              <label><input type="checkbox"> New buildings since last survey</label>
              <label><input type="checkbox"> Demolished buildings since last survey</label>
              <label><input type="checkbox"> Street extends beyond recorded boundary</label>
              <label><input type="checkbox"> Blocked or inaccessible section</label>
              <label><input type="checkbox"> Flood/erosion damage</label>
              <label><input type="checkbox"> No issues found</label>
            </div>
          </div>

          <div class="field" style="margin-bottom:12px;">
            <label>Detailed Observation Notes <span class="req">*</span></label>
            <textarea placeholder="Describe all findings, discrepancies, and observations in detail..."></textarea>
          </div>

          <div class="field">
            <label>Recommended Actions for Administration</label>
            <textarea placeholder="Suggest corrective actions, updates needed, or further investigation required..."></textarea>
          </div>
        </div>

        <!-- SECTION 7 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num">7</div>
            <h3>Photo & Video Evidence Log</h3>
          </div>

          <div class="media-log">
            <table>
              <thead>
                <tr>
                  <th style="width:48px;">Ref #</th>
                  <th style="width:130px;">Type</th>
                  <th>Description / What it Shows</th>
                  <th style="width:160px;">GPS at Capture Point</th>
                  <th style="width:90px;">Time</th>
                  <th style="width:130px;">File Name</th>
                </tr>
              </thead>
              <tbody>
                ${[1,2,3,4,5,6].map(n => `
                <tr>
                  <td style="text-align:center;color:var(--gray-400);font-size:12px;">${n}</td>
                  <td>
                    <div class="type-cell">
                      <label style="display:flex;align-items:center;gap:4px;font-size:11px;"><input type="checkbox"> Photo</label>
                      <label style="display:flex;align-items:center;gap:4px;font-size:11px;"><input type="checkbox"> Video</label>
                    </div>
                  </td>
                  <td><input type="text" placeholder="Describe content..."></td>
                  <td><input type="text" placeholder="6.XXXXXX, 7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:11px;"></td>
                  <td><input type="time"></td>
                  <td><input type="text" placeholder="IMG_0001.jpg"></td>
                </tr>`).join('')}
              </tbody>
            </table>
          </div>

          <div class="media-note">
            <strong>Media Requirements:</strong> Minimum 3 photos per street (start, middle, end). Videos should be 30–60 seconds showing a walk/drive along the street. All files must be geotagged (keep phone location ON).
          </div>
        </div>

        <!-- SECTION 8 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num">8</div>
            <h3>Verification & Signature</h3>
          </div>

          <div class="field" style="margin-bottom:20px;">
            <label>Revalidation Outcome <span class="req">*</span></label>
            <div class="outcome-options" style="margin-top:8px;">
              <label class="outcome-option">
                <input type="radio" name="outcome_a">
                <div><strong>✅ Verified</strong><p>Street data is accurate, no changes needed</p></div>
              </label>
              <label class="outcome-option">
                <input type="radio" name="outcome_a">
                <div><strong>✏️ Update Required</strong><p>Minor corrections needed (see notes above)</p></div>
              </label>
              <label class="outcome-option">
                <input type="radio" name="outcome_a">
                <div><strong>🚩 Flagged</strong><p>Significant discrepancies found, admin review required</p></div>
              </label>
            </div>
          </div>

          <div class="sig-grid">
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Field Agent Signature</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Date Signed</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Ward Supervisor Signature</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Date Received</div>
            </div>
          </div>

          <div class="admin-box">
            <div class="admin-box-title">For Admin Use Only</div>
            <div class="field-row" style="grid-template-columns:1fr 1fr 1fr 1fr;">
              <div class="field">
                <label>Reviewed By</label>
                <input type="text" placeholder="">
              </div>
              <div class="field">
                <label>Date Reviewed</label>
                <input type="date">
              </div>
              <div class="field">
                <label>Decision</label>
                <div class="radio-group" style="margin-top:4px;flex-direction:column;gap:6px;">
                  <label><input type="radio" name="admin_dec_a"> Approved</label>
                  <label><input type="radio" name="admin_dec_a"> Returned</label>
                </div>
              </div>
              <div class="field">
                <label>NDSMS Entry ID</label>
                <input type="text" placeholder="">
              </div>
            </div>
          </div>
        </div>

        <div class="submit-bar">
          <button class="btn btn-secondary" onclick="window.print()">🖨️ Print Form</button>
          <button class="btn btn-primary" wire:click.prevent="submitFormA">Submit Form A</button>
        </div>

        <div class="form-footer">
          <span>Njikoka LGA — NDSMS Field Agent Form A — Street Revalidation</span>
          <span>Page 2 of 2 &nbsp;|&nbsp; NDSMS-FA-v2.0</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== FORM B ===== -->
<div id="form-b" class="form-page" style="display: {{ $activeForm === 'form-b' ? 'block' : 'none' }}">

  <div style="margin-top: 28px;">
    <div class="form-header">
      <div class="form-header-left">
        <div class="nj-logo">NJ</div>
        <div class="form-header-title">
          <h2>Njikoka LGA — NDSMS</h2>
          <p>Digital Street Management System</p>
        </div>
      </div>
      <div class="form-badge">
        <strong>FORM B</strong>
        <span>New Street / Close Suggestion</span>
      </div>
    </div>

    <div class="form-body">
      <!-- TITLE -->
      <div style="text-align:center; margin-bottom:28px; padding-bottom:20px; border-bottom:1px solid var(--gray-200);">
        <p style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:var(--teal); font-weight:700; margin-bottom:6px;">💡 New Street / Close / Road Suggestion Form</p>
      </div>

      <!-- INSTRUCTIONS -->
      <div class="instructions" style="border-left-color:var(--teal); background:#f0fdfa; border-color:#99f6e4;">
        <h4 style="color:#0f766e;">Instructions for Field Agent</h4>
        <ul>
          <li>Use this form to <strong>propose new streets, closes, lanes, or roads</strong> not currently in the NDSMS database.</li>
          <li>Walk the <strong>entire length</strong> of the proposed street to count properties and capture GPS waypoints.</li>
          <li>Take a <strong>minimum of 5 photos</strong> and <strong>1 video</strong> (walkthrough of the full street).</li>
          <li>If the street already has a commonly known name, record it. Otherwise, leave the proposed name blank for admin assignment.</li>
        </ul>
      </div>

      <!-- SECTION 1 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num" style="background:var(--teal);">1</div>
          <h3>Agent & Assignment Details</h3>
        </div>

        <div class="field-row cols-3">
          <div class="field">
            <label>Agent Full Name <span class="req">*</span></label>
            <input type="text" placeholder="Full name in BLOCK CAPITALS" wire:model="formB.agent_name">
          </div>
          <div class="field">
            <label>Agent ID <span class="req">*</span></label>
            <input type="text" placeholder="e.g. AGT-0012" wire:model="formB.agent_id">
          </div>
          <div class="field">
            <label>Date of Survey <span class="req">*</span></label>
            <input type="date" wire:model="formB.survey_date">
          </div>
        </div>

        <div class="field-row" style="grid-template-columns:auto 1fr 1fr;">
          <div class="field">
            <label>Town <span class="req">*</span></label>
            <div class="towns-grid" style="grid-template-columns:1fr 1fr;gap:6px;margin-top:4px;">
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#10b981"></span>Abagana</label>
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#3b82f6"></span>Enugwu-Ukwu</label>
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#8b5cf6"></span>Nimo</label>
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#f59e0b"></span>Nawfia</label>
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#ec4899"></span>Enugwu-Agidi</label>
              <label class="town-card"><input type="checkbox"><span class="town-dot" style="background:#ef4444"></span>Abba</label>
            </div>
          </div>
          <div class="field">
            <label>Area / Quarter / Village <span class="req">*</span></label>
            <input type="text" placeholder="e.g. Umuchukwu Quarter">
          </div>
          <div class="field">
            <label>Nearest Known Street (for Reference)</label>
            <input type="text" placeholder="e.g. Okafor Avenue">
          </div>
        </div>
      </div>

      <!-- SECTION 2 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num" style="background:var(--teal);">2</div>
          <h3>Proposed Street Details</h3>
        </div>

        <div class="field-row" style="grid-template-columns:2fr 1fr 1fr;">
          <div class="field">
            <label>Proposed / Commonly Known Name (if any)</label>
            <input type="text" placeholder="Leave blank if unknown — admin will assign">
          </div>
          <div class="field">
            <label>Proposed Type <span class="req">*</span></label>
            <div class="checkbox-group" style="display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;margin-top:4px;">
              <label><input type="checkbox"> Road</label>
              <label><input type="checkbox"> Avenue</label>
              <label><input type="checkbox"> Close</label>
              <label><input type="checkbox"> Lane</label>
              <label><input type="checkbox"> Crescent</label>
              <label><input type="checkbox"> Drive</label>
              <label><input type="checkbox"> Court</label>
              <label><input type="checkbox"> Way</label>
            </div>
          </div>
          <div class="field">
            <label>Is it a Dead End? <span class="req">*</span></label>
            <div class="radio-group" style="flex-direction:column;gap:8px;margin-top:6px;">
              <label><input type="radio" name="dead_end"> Yes (dead end / cul-de-sac)</label>
              <label><input type="radio" name="dead_end"> No (through road)</label>
            </div>
          </div>
        </div>

        <div class="field">
          <label>Description & Directions (How to find this street from a major landmark) <span class="req">*</span></label>
          <textarea rows="3" placeholder="e.g. From St. Mary's Church on Enugwu Road, turn left at the first junction and proceed 200m..."></textarea>
        </div>
      </div>

      <!-- SECTION 3 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num" style="background:var(--teal);">3</div>
          <h3>GPS Coordinates & Waypoints (Auto-Captured)</h3>
        </div>

        <table class="waypoint-table">
          <thead>
            <tr>
              <th style="width:130px;">Waypoint</th>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Description / Location Note</th>
            </tr>
          </thead>
          <tbody>
            <tr style="background:#eff9f4;">
              <td>🟢 Start Point</td>
              <td><input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="e.g. Junction with Okafor Ave"></td>
            </tr>
            <tr>
              <td>Midpoint 1</td>
              <td><input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text"></td>
            </tr>
            <tr>
              <td>Midpoint 2</td>
              <td><input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text"></td>
            </tr>
            <tr>
              <td>Midpoint 3</td>
              <td><input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text"></td>
            </tr>
            <tr style="background:#eff9f4;">
              <td>🔴 End Point</td>
              <td><input type="text" placeholder="6.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:12px;"></td>
              <td><input type="text" placeholder="e.g. Dead end / connects to main road"></td>
            </tr>
          </tbody>
        </table>

        <div class="gps-note" style="margin-top:10px;">
          <strong>Waypoint Guide:</strong> Record GPS at the start, end, and every major bend/intersection. The more waypoints, the better the street can be mapped. App users: tap "Drop Pin" button as you walk.
        </div>
      </div>

      <!-- SECTION 4 -->
      <div class="section">
        <div class="section-title">
          <div class="section-num" style="background:var(--teal);">4</div>
          <h3>Properties & Structures Along Proposed Street</h3>
        </div>

        <div class="field-row cols-3" style="margin-bottom:16px;">
          <div class="field">
            <label>Est. Length (Metres) <span class="req">*</span></label>
            <input type="number" placeholder="e.g. 180">
          </div>
          <div class="field">
            <label>Est. Width (Metres) <span class="req">*</span></label>
            <input type="number" placeholder="e.g. 4">
          </div>
          <div class="field">
            <label>Surface Type <span class="req">*</span></label>
            <div class="checkbox-group" style="display:grid;grid-template-columns:1fr 1fr;gap:6px 14px;margin-top:4px;">
              <label><input type="checkbox"> Tarred</label>
              <label><input type="checkbox"> Gravel</label>
              <label><input type="checkbox"> Laterite</label>
              <label><input type="checkbox"> Earth</label>
              <label><input type="checkbox"> Concrete</label>
            </div>
          </div>
        </div>

        <table class="data-table">
          <thead>
            <tr>
              <th>Property Type</th>
              <th>Count (Left)</th>
              <th>Count (Right)</th>
              <th>Total</th>
              <th>Notable Properties</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Residential (Completed)</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Residential (Under Construction)</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Commercial / Shop</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Government / Public</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Religious / Educational</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td>Empty / Undeveloped Plot</td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
            <tr><td><strong>Grand Total</strong></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="number" min="0"></td><td><input type="text"></td></tr>
          </tbody>
        </table>
      </div>

      <div class="form-footer">
        <span>Njikoka LGA — NDSMS Field Agent Form B — New Street Suggestion</span>
        <span>Page 1 of 2 &nbsp;|&nbsp; NDSMS-FB-v2.0</span>
      </div>
    </div>

    <!-- FORM B PAGE 2 -->
    <div style="margin-top:28px;">
      <div class="page-2-header" style="background: linear-gradient(90deg, var(--green-dark), var(--teal));">
        <div class="page-2-header-left">
          <div class="nj-logo">NJ</div>
          <div>
            <h3>FORM B — New Street Suggestion (continued)</h3>
            <p>AGENT ID: _________________ &nbsp;&nbsp; DATE: _________________</p>
          </div>
        </div>
        <div class="page-2-badge">PAGE 2/2</div>
      </div>

      <div class="form-body" style="border-top: 3px solid var(--teal);">

        <!-- SECTION 5 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num" style="background:var(--teal);">5</div>
            <h3>Justification & Community Impact</h3>
          </div>

          <div class="field" style="margin-bottom:16px;">
            <label>Why should this street be added to the NDSMS database? <span class="req">*</span><br><span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--gray-500);">(Explain significance, population served, access needs)</span></label>
            <textarea rows="5" placeholder="Describe why this street is significant, how many people rely on it, what services it provides access to, and why it should be formally recognised..."></textarea>
          </div>

          <div class="field-row cols-2">
            <div class="field">
              <label>Estimated Households <span class="req">*</span></label>
              <input type="number" placeholder="e.g. 24">
            </div>
            <div class="field">
              <label>Estimated Population <span class="req">*</span></label>
              <input type="number" placeholder="e.g. 120">
            </div>
          </div>

          <div class="field" style="margin-bottom:12px;">
            <label>Connecting Streets — List all streets this proposed street connects to</label>
            <textarea rows="2" placeholder="e.g. Connects to Okafor Avenue at start point and Nwosu Close at end point..."></textarea>
          </div>

          <div class="field">
            <label>Community Facilities Along Street (Hospitals, Markets, Schools, Churches, Water Points)</label>
            <textarea rows="3" placeholder="List any notable community facilities, landmarks, or services located along this street..."></textarea>
          </div>
        </div>

        <!-- SECTION 6 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num" style="background:var(--teal);">6</div>
            <h3>Photo & Video Evidence Log</h3>
          </div>

          <div class="media-log">
            <table>
              <thead>
                <tr>
                  <th style="width:40px;">#</th>
                  <th style="width:130px;">Type</th>
                  <th>Description / What it Shows</th>
                  <th style="width:160px;">GPS at Capture (Lat, Lng)</th>
                  <th style="width:90px;">Time</th>
                  <th style="width:130px;">File Name / Ref</th>
                </tr>
              </thead>
              <tbody>
                ${[1,2,3,4,5,6,7,8].map(n => `
                <tr>
                  <td style="text-align:center;color:var(--gray-400);font-size:12px;">${n}</td>
                  <td>
                    <div class="type-cell">
                      <label style="display:flex;align-items:center;gap:4px;font-size:11px;"><input type="checkbox"> Photo</label>
                      <label style="display:flex;align-items:center;gap:4px;font-size:11px;"><input type="checkbox"> Video</label>
                    </div>
                  </td>
                  <td><input type="text" placeholder="Describe content..."></td>
                  <td><input type="text" placeholder="6.XXXXXX, 7.XXXXXX" style="font-family:'DM Mono',monospace;font-size:11px;"></td>
                  <td><input type="time"></td>
                  <td><input type="text" placeholder="IMG_0001.jpg"></td>
                </tr>`).join('')}
              </tbody>
            </table>
          </div>

          <div class="media-note">
            <strong>Minimum Required:</strong> 5 photos (start, end, middle, any landmarks, street sign if exists) + 1 video walkthrough (30–60 seconds). All media must have GPS metadata enabled.
          </div>
        </div>

        <!-- SECTION 7 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num" style="background:var(--teal);">7</div>
            <h3>Sketch Map (Draw the Street Layout Below)</h3>
          </div>

          <div class="sketch-area">
            <span>Sketch Area — Draw Street Layout, Connecting Roads, Landmarks</span>
            <div class="north-indicator">N ↑ (indicate north direction)</div>
          </div>
        </div>

        <!-- SECTION 8 -->
        <div class="section">
          <div class="section-title">
            <div class="section-num" style="background:var(--teal);">8</div>
            <h3>Agent Declaration & Signature</h3>
          </div>

          <div style="background:#eff9f4; border:1px solid #bbf7d0; border-radius:var(--radius); padding:14px 16px; margin-bottom:20px; font-size:13px; color:var(--gray-700); line-height:1.6;">
            I hereby declare that the information provided in this form is accurate and based on my personal field observation. All photos and videos attached are genuine and unedited, captured during the survey date indicated. I recommend that this street be considered for inclusion in the NDSMS database.
          </div>

          <div class="sig-grid">
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Field Agent Signature</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Date Signed</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Community Leader / Witness Name</div>
            </div>
            <div class="sig-box">
              <div class="sig-line"></div>
              <div class="sig-label">Witness Signature</div>
            </div>
          </div>

          <div class="admin-box">
            <div class="admin-box-title">For Admin Use Only</div>
            <div class="field-row" style="grid-template-columns:1fr 1fr 1fr 1fr;">
              <div class="field">
                <label>Reviewed By</label>
                <input type="text" placeholder="">
              </div>
              <div class="field">
                <label>Date Reviewed</label>
                <input type="date">
              </div>
              <div class="field">
                <label>Decision</label>
                <div class="radio-group" style="flex-direction:column;gap:6px;margin-top:4px;">
                  <label><input type="radio" name="admin_dec_b"> Approved</label>
                </div>
              </div>
              <div class="field">
                <label>Assigned Street Code</label>
                <input type="text" placeholder="">
              </div>
            </div>
          </div>
        </div>

        <div class="submit-bar">
          <button class="btn btn-secondary" onclick="window.print()">🖨️ Print Form</button>
          <button class="btn btn-primary" style="background:var(--teal);" wire:click.prevent="submitFormB">Submit Form B</button>
        </div>

        <div class="form-footer">
          <span>Njikoka LGA — NDSMS Field Agent Form B — New Street Suggestion</span>
          <span>Page 2 of 2 &nbsp;|&nbsp; NDSMS-FB-v2.0</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function showTab(id) {
    document.querySelectorAll('.form-page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    event.target.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
</script>

</div>
