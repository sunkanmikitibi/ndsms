<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-qrcode" style="color:var(--accent);margin-right:10px;"></i>QR Scanner</h2>
            <p>Scan any NDSMS QR code to instantly look up address information</p>
        </div>
    </div>
    <div class="qr-container">
        <div class="qr-viewport" style="max-width:340px;margin:0 auto;">
            <div class="qr-corner tl"></div>
            <div class="qr-corner tr"></div>
            <div class="qr-corner bl"></div>
            <div class="qr-corner br"></div>
            <div style="text-align:center;color:rgba(255,255,255,0.5);padding:20px;">
                <i class="fas fa-camera" style="font-size:48px;margin-bottom:12px;display:block;opacity:.4;"></i>
                <p style="font-size:13px;">Camera access required</p>
            </div>
        </div>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">Point your camera at an NDSMS address plate QR code to scan it.</p>
        <button class="btn btn-primary" style="margin:0 auto;display:flex;" onclick="alert('Camera scanning requires HTTPS and device camera permissions.')">
            <i class="fas fa-qrcode"></i> Start Scanning
        </button>
        <div style="margin-top:24px;">
            <div style="font-size:13px;color:var(--text-secondary);margin-bottom:10px;font-weight:600;">Or enter code manually:</div>
            <div style="display:flex;gap:10px;">
                <input type="text" placeholder="Enter NDSMS code e.g. NJK-STR-0042" style="flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-size:15px;background:var(--bg-input);color:var(--text-primary);">
                <button class="btn btn-outline"><i class="fas fa-search"></i> Look Up</button>
            </div>
        </div>
    </div>
</div>
