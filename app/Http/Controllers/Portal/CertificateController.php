<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\StreetApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Generate PDF certificate for a street application
     */
    public function streetCertificate(int $id)
    {
        $application = StreetApplication::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.street-certificate', compact('application'));
        
        return $pdf->stream("certificate-street-{$id}.pdf");
    }

    /**
     * Generate PDF certificate for an address
     */
    public function addressCertificate(int $id)
    {
        $address = Address::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.address-certificate', compact('address'));
        
        return $pdf->stream("certificate-address-{$id}.pdf");
    }
}
