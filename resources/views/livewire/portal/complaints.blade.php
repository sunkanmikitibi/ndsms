<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-comment-dots" style="color:var(--accent);margin-right:10px;"></i>Complaints &amp; Feedback</h2>
            <p>Report issues or provide feedback to the Njikoka LGA Registry Office</p>
        </div>
    </div>

    @if($submitted)
    <div class="card" style="max-width:500px;margin:0 auto;text-align:center;padding:40px;">
        <div style="width:72px;height:72px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent);">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 style="font-size:18px;font-weight:700;margin-bottom:10px;">Complaint Submitted</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">We have received your complaint and will respond within <strong>3 working days</strong>. A reference number will be sent to your email.</p>
        <button wire:click="newComplaint" class="btn btn-primary"><i class="fas fa-plus"></i> Submit Another</button>
    </div>
    @else
    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;">
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;"><i class="fas fa-tag" style="color:var(--accent);margin-right:8px;"></i>Select Category</h3>

            <div class="complaint-type-grid">
                @foreach([
                    ['type' => 'street-naming', 'icon' => 'fa-road', 'label' => 'Street Naming'],
                    ['type' => 'wrong-address', 'icon' => 'fa-map-marker-alt', 'label' => 'Wrong Address'],
                    ['type' => 'damaged-plate', 'icon' => 'fa-qrcode', 'label' => 'Damaged Plate'],
                    ['type' => 'service-delay', 'icon' => 'fa-clock', 'label' => 'Service Delay'],
                    ['type' => 'staff-conduct', 'icon' => 'fa-user-shield', 'label' => 'Staff Conduct'],
                    ['type' => 'suggestion', 'icon' => 'fa-lightbulb', 'label' => 'Suggestion'],
                ] as $cat)
                <div class="complaint-type-btn {{ $type === $cat['type'] ? 'selected' : '' }}"
                     wire:click="selectType('{{ $cat['type'] }}')">
                    <i class="fas {{ $cat['icon'] }}"></i>
                    <span>{{ $cat['label'] }}</span>
                </div>
                @endforeach
            </div>
            @error('type')<p style="color:var(--danger);font-size:12px;margin-bottom:12px;">{{ $message }}</p>@enderror

            <form wire:submit="submit">
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Subject *</label>
                    <input wire:model="subject" type="text" placeholder="Brief description of your issue">
                    @error('subject')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Message *</label>
                    <textarea wire:model="message" rows="5" placeholder="Describe your complaint or feedback in detail…" style="resize:vertical;"></textarea>
                    @error('message')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-paper-plane"></i> Submit Complaint</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Submitting…</span>
                </button>
            </form>
        </div>

        <div>
            <div class="card" style="margin-bottom:16px;">
                <h4 style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--accent);">Contact Us Directly</h4>
                <div style="font-size:13px;color:var(--text-secondary);line-height:1.8;">
                    <div><i class="fas fa-phone" style="width:18px;color:var(--accent);"></i> +234 800 000 0000</div>
                    <div><i class="fas fa-envelope" style="width:18px;color:var(--accent);"></i> registry@njikoka.gov.ng</div>
                    <div><i class="fas fa-map-marker-alt" style="width:18px;color:var(--accent);"></i> Njikoka LGA Secretariat</div>
                    <div><i class="fas fa-clock" style="width:18px;color:var(--accent);"></i> Mon–Fri, 8am–4pm</div>
                </div>
            </div>
            <div class="card" style="background:var(--accent-light);border-color:rgba(27,122,68,0.2);">
                <h4 style="font-size:13px;font-weight:700;color:var(--accent);margin-bottom:6px;"><i class="fas fa-info-circle" style="margin-right:6px;"></i>Response Time</h4>
                <p style="font-size:12px;color:var(--text-secondary);">Complaints are resolved within <strong>3–5 working days</strong>. Urgent matters are prioritized within <strong>24 hours</strong>.</p>
            </div>
        </div>
    </div>
    @endif
</div>
