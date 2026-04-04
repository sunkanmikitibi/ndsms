<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-robot" style="color:var(--accent);margin-right:10px;"></i>AI Address Lookup</h2>
            <p>Describe an address in natural language and our AI will find it</p>
        </div>
    </div>
    <div class="card" style="max-width:680px;">
        <div class="ai-input-area">
            <input type="text" placeholder="e.g. "The green house near Abagana market on the main road"" id="ai-input">
            <button class="btn btn-primary" onclick="doAiLookup()"><i class="fas fa-robot"></i> Search</button>
        </div>
        <div id="ai-thinking" style="display:none;" class="ai-thinking">
            <div class="ai-thinking-dot"></div>
            <div class="ai-thinking-dot"></div>
            <div class="ai-thinking-dot"></div>
            <span>AI is thinking…</span>
        </div>
        <div id="ai-result-area" style="display:none;" class="ai-result">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;font-weight:700;color:var(--accent);margin-bottom:10px;"><i class="fas fa-robot"></i> AI Result</div>
            <p id="ai-result-text" style="font-size:14px;line-height:1.7;"></p>
        </div>
        <p style="font-size:12px;color:var(--text-secondary);margin-top:12px;">
            <i class="fas fa-info-circle"></i> AI lookup uses natural language to search our address database. Results may not be 100% accurate — always verify.
        </p>
    </div>
    <script>
    function doAiLookup() {
        const input = document.getElementById('ai-input').value;
        if (!input.trim()) return;
        document.getElementById('ai-thinking').style.display = 'flex';
        document.getElementById('ai-result-area').style.display = 'none';
        setTimeout(() => {
            document.getElementById('ai-thinking').style.display = 'none';
            document.getElementById('ai-result-area').style.display = 'block';
            document.getElementById('ai-result-text').innerHTML =
                `Based on your description, the closest match in our database is: <strong>14A Nnewi Road, Abagana Ward</strong> — Owner: John Doe, Status: Active.<br><br>
                <small style="color:var(--text-secondary);">Confidence: 78% — Please verify using the Verification tool.</small>`;
        }, 2000);
    }
    document.getElementById('ai-input').addEventListener('keydown', e => { if (e.key === 'Enter') doAiLookup(); });
    </script>
</div>
