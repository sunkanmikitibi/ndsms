<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-qrcode" style="color:var(--accent);margin-right:10px;"></i>QR Scanner</h2>
            <p>Scan any NDSMS QR code to instantly look up address information</p>
        </div>
    </div>

    @if ($scannedAddress)
        <!-- Result Display -->
        <div style="max-width:680px;margin:0 auto;">
            <div class="card"
                style="background:linear-gradient(135deg, var(--accent-light) 0%, rgba(255,255,255,0.5) 100%);border:2px solid var(--accent);">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                    <div style="font-size:24px;color:var(--accent);"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div
                            style="font-size:12px;text-transform:uppercase;letter-spacing:0.5px;font-weight:700;color:var(--accent);">
                            Address Found</div>
                        <div style="font-size:14px;color:var(--text-primary);">
                            {{ $scannedAddress->street?->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:16px;margin-bottom:20px;">
                    <div>
                        <div
                            style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:4px;">
                            House Number</div>
                        <div style="font-size:16px;font-weight:700;color:var(--text-primary);">
                            {{ $scannedAddress->house_number }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:4px;">
                            Town/Ward</div>
                        <div style="font-size:16px;font-weight:700;color:var(--text-primary);">
                            {{ $scannedAddress->town }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:4px;">
                            Owner</div>
                        <div style="font-size:14px;color:var(--text-primary);">{{ $scannedAddress->owner_name }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:4px;">
                            Status</div>
                        <div style="font-size:14px;color:var(--accent);font-weight:700;">
                            {{ ucfirst($scannedAddress->status) }}</div>
                    </div>
                </div>

                @if ($scannedAddress->last_verified_at)
                    <div
                        style="padding:12px;background:rgba(255,255,255,0.6);border-radius:var(--radius-sm);margin-bottom:20px;">
                        <div style="font-size:11px;color:var(--text-secondary);">
                            Last verified {{ $scannedAddress->last_verified_at->diffForHumans() }}
                        </div>
                    </div>
                @endif

                <div style="display:flex;gap:10px;">
                    <button wire:click="verifyAddress" class="btn btn-primary" style="flex:1;">
                        <i class="fas fa-check"></i> Verify Address
                    </button>
                    <button wire:click="clearResult" class="btn btn-outline" style="flex:1;">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Scanner or Manual Entry -->
        <div style="max-width:680px;margin:0 auto;">
            @if ($isScanning)
                <!-- Live Scanner -->
                <div class="card" style="padding:0;overflow:hidden;">
                    <div style="position:relative;background:#000;aspect-ratio:1/1;">
                        <video id="qr-video" style="width:100%;height:100%;display:block;object-fit:cover;"
                            playsinline></video>
                        <div
                            style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:280px;height:280px;border:3px solid var(--accent);border-radius:12px;box-shadow:0 0 0 99999px rgba(0,0,0,0.5);">
                        </div>
                    </div>
                    <div style="padding:20px;text-align:center;">
                        <div style="font-size:14px;color:var(--text-secondary);margin-bottom:12px;">Point camera at QR
                            code</div>
                        <button wire:click="stopScanning" class="btn btn-outline" style="width:100%;">
                            <i class="fas fa-stop"></i> Stop Scanning
                        </button>
                    </div>
                </div>
            @else
                <!-- Start Scanning Button -->
                <div class="card">
                    <div style="text-align:center;padding:40px 20px;">
                        <div style="font-size:48px;color:var(--accent);margin-bottom:16px;"><i
                                class="fas fa-camera"></i></div>
                        <div style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">Ready
                            to Scan</div>
                        <p style="color:var(--text-secondary);margin-bottom:24px;font-size:14px;">Position an NDSMS QR
                            code within the frame</p>
                        <button wire:click="startScanning" class="btn btn-primary"
                            style="display:inline-flex;align-items:center;gap:8px;">
                            <i class="fas fa-qrcode"></i> Start Camera
                        </button>
                    </div>
                </div>
            @endif

            @if ($scanError)
                <div
                    style="margin-top:16px;padding:12px 16px;background:var(--danger-light);border:1.5px solid var(--danger);border-radius:var(--radius-sm);color:var(--danger);">
                    <i class="fas fa-exclamation-circle"></i> {{ $scanError }}
                </div>
            @endif

            <!-- Manual Entry -->
            <div class="card" style="margin-top:24px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                    <i class="fas fa-keyboard" style="color:var(--accent);font-size:20px;"></i>
                    <div style="font-size:16px;font-weight:700;color:var(--text-primary);">Manual Lookup</div>
                </div>
                <form wire:submit="searchManualCode" style="display:flex;gap:10px;">
                    <input type="text" wire:model="manualCode" placeholder="e.g. NJK-STR-0042 or house number"
                        style="flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-size:14px;" />
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Look Up
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

@script
    <script module>
        import jsQR from 'jsqr';

        let video, canvasElement, canvas;
        let isRunning = false;
        let animationFrameId = null;
        let lastScannedCode = null;
        let lastScanTime = 0;

        function initCamera() {
            video = document.getElementById('qr-video');
            canvasElement = document.createElement('canvas');
            canvas = canvasElement.getContext('2d');

            if (!video) return;

            // Request camera access
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    },
                    audio: false
                })
                .then((stream) => {
                    video.srcObject = stream;
                    video.play().catch(err => console.error('Play error:', err));
                    isRunning = true;
                    scanQRCode();
                })
                .catch((err) => {
                    console.error('Camera access denied:', err);
                    $wire.dispatch('notify', {
                        type: 'error',
                        message: 'Camera access denied. Please allow camera permissions.'
                    });
                    stopCamera();
                });
        }

        function scanQRCode() {
            if (!isRunning) return;

            if (video?.readyState === video?.HAVE_ENOUGH_DATA) {
                canvasElement.width = video.videoWidth;
                canvasElement.height = video.videoHeight;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);

                const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert',
                });

                if (code && code.data) {
                    if (code.data !== lastScannedCode || Date.now() - lastScanTime > 2000) {
                        lastScannedCode = code.data;
                        lastScanTime = Date.now();
                        $wire.dispatch('qr-code-detected', {
                            qrData: code.data
                        });
                    }
                }
            }

            animationFrameId = requestAnimationFrame(scanQRCode);
        }

        window.addEventListener('beforeunload', () => {
            stopCamera();
        });

        function stopCamera() {
            isRunning = false;
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            if (video?.srcObject instanceof MediaStream) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        }

        Livewire.on('start-camera', () => {
            setTimeout(() => initCamera(), 100);
        });

        Livewire.on('stop-camera', () => {
            stopCamera();
        });
    </script>
@endscript
