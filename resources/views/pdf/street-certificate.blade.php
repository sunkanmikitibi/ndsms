<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Street Registration Certificate - {{ $application->street_name }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #334155;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            background-color: #fff;
            position: relative;
        }
        .border-outer {
            border: 15px solid #064e3b;
            height: 94%;
            padding: 10px;
            box-sizing: border-box;
        }
        .border-inner {
            border: 2px solid #064e3b;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            text-align: center;
        }
        .header {
            margin-bottom: 40px;
        }
        .seal {
            width: 100px;
            height: 100px;
            background-color: #064e3b;
            border-radius: 50%;
            margin: 0 auto 20px;
            line-height: 100px;
            color: #fcd34d;
            font-size: 40px;
            font-weight: bold;
        }
        .govt-name {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #064e3b;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .dept-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 30px;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            color: #064e3b;
            margin-bottom: 40px;
            text-transform: uppercase;
        }
        .content {
            font-size: 18px;
            margin-bottom: 50px;
            text-align: left;
            padding: 0 40px;
        }
        .content p {
            margin-bottom: 20px;
        }
        .highlights {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
            text-align: left;
        }
        .highlight-item {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .highlight-label {
            color: #64748b;
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .highlight-value {
            color: #1e293b;
            font-weight: bold;
        }
        .footer {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #334155;
            width: 200px;
            margin: 0 auto 10px;
            padding-top: 5px;
        }
        .sig-name {
            font-weight: bold;
            font-size: 14px;
        }
        .sig-title {
            font-size: 12px;
            color: #64748b;
        }
        .qr-code {
            position: absolute;
            bottom: 60px;
            right: 60px;
            width: 80px;
            height: 80px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            text-align: center;
            line-height: 80px;
            font-size: 10px;
            color: #94a3b8;
        }
        .cert-number {
            position: absolute;
            bottom: 60px;
            left: 60px;
            font-size: 12px;
            color: #64748b;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="border-outer">
            <div class="border-inner">
                <div class="header">
                    <div class="seal">N</div>
                    <div class="govt-name">Njikoka Digital Street Management System</div>
                    <div class="dept-name">Ministry of Lands & Physical Planning</div>
                </div>

                <div class="title">Certificate of Street Registration</div>

                <div class="content">
                    <p>This is to certify that the street detailed below has been officially registered and indexed within the Njikoka Digital Street Management System (NDSMS).</p>
                    
                    <div class="highlights">
                        <div class="highlight-item">
                            <span class="highlight-label">Street Name:</span>
                            <span class="highlight-value">{{ $application->street_name }}</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-label">Town/LGA:</span>
                            <span class="highlight-value">{{ $application->town }}</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-label">Street Type:</span>
                            <span class="highlight-value">{{ ucfirst($application->type) }}</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-label">Approved By:</span>
                            <span class="highlight-value">NDSMS Registry Office</span>
                        </div>
                        <div class="highlight-item">
                            <span class="highlight-label">Date Approved:</span>
                            <span class="highlight-value">{{ $application->reviewed_at?->format('F d, Y') ?? date('F d, Y') }}</span>
                        </div>
                    </div>

                    <p>This certificate serves as official recognition of the street's inclusion in the municipal infrastructure database.</p>
                </div>

                <div class="footer">
                    <div class="signature">
                        <div class="sig-line"></div>
                        <div class="sig-name">Hon. Commissioner</div>
                        <div class="sig-title">Ministry of Lands</div>
                    </div>
                    <div class="signature">
                        <div class="sig-line"></div>
                        <div class="sig-name">Registry Director</div>
                        <div class="sig-title">NDSMS Headquarters</div>
                    </div>
                </div>

                <div class="cert-number">
                    CERT-ST-{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
                </div>

                <div class="qr-code">
                    Verification QR
                </div>
            </div>
        </div>
    </div>
</body>
</html>
