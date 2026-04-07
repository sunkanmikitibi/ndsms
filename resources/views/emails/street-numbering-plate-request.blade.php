@component('mail::message')
    # Street Numbering Plate Request Confirmation

    Hello {{ $numberingPlateRequest->user->name }},

    Your street numbering plate request has been successfully submitted and is awaiting review.

    ## Request Details

    | Item | Details |
    |------|---------|
    | **Reference Number** | `{{ $numberingPlateRequest->reference_number }}` |
    | **Street Name** | {{ $numberingPlateRequest->street_name }} |
    | **Ward** | {{ $numberingPlateRequest->ward }} |
    | **Number of Plates** | {{ $numberingPlateRequest->quantity_requested }} |
    | **Plate Type** | {{ $plateTypeLabel }} |
    | **Material** | {{ $materialLabel }} |
    | **Estimated Cost** | ₦{{ number_format($totalCost, 2) }} |
    | **Current Status** | {{ $statusLabel }} |

    @if ($numberingPlateRequest->installation_date_requested)
        | **Requested Installation Date** | {{ $numberingPlateRequest->installation_date_requested->format('F j, Y') }} |
    @endif

    | **Delivery Address** | {{ $numberingPlateRequest->delivery_address }} |

    ## Next Steps

    1. **Review**: Our team will review your request within 2-3 business days
    2. **Approval**: You'll receive an email notification once your request is approved
    3. **Production**: Once approved, your plates will enter production
    4. **Delivery**: We'll notify you when your plates are ready for delivery
    5. **Installation**: We can arrange professional installation if needed

    ## Important Information

    - All costs are subject to final review and approval
    - Installation dates are subject to availability
    - You can track the status of your request by visiting your portal dashboard

    @component('mail::button', ['url' => url('/portal/request-numbering-plates')])
        View My Request
    @endcomponent

    If you have any questions about your request, please contact our support team or reply to this email.

    Thank you for using our municipal services!
@endcomponent
