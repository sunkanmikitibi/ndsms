<?php

namespace App\Livewire\Portal;

use App\Mail\ComplaintSubmitted;
use App\Models\Complaint;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Complaints & Feedback')]
class Complaints extends Component
{
    public string $type = '';
    public string $subject = '';
    public string $message = '';
    public bool $submitted = false;
    public ?string $submitError = null;

    protected $rules = [
        'type'    => 'required|string|in:bug,feature,complaint,feedback,other',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:20|max:2000',
    ];

    public function selectType(string $type): void
    {
        $this->type = $type;
    }

    public function submit(): void
    {
        $this->validate();
        $this->submitError = null;

        try {
            $user = auth()->user();

            // Create complaint record in database
            $complaint = Complaint::create([
                'user_id' => $user->id,
                'type' => $this->type,
                'subject' => $this->subject,
                'message' => $this->message,
                'status' => 'new',
            ]);

            // Send email to support team
            Mail::send(new ComplaintSubmitted(
                userName: $user->name,
                userEmail: $user->email,
                complaintType: ucfirst($this->type),
                subject: $this->subject,
                message: $this->message,
                submittedAt: $complaint->created_at->format('Y-m-d H:i'),
            ));

            $this->submitted = true;
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Your complaint has been submitted successfully. We will respond within 3 working days.',
            ]);
        } catch (\Exception $e) {
            $this->submitError = 'Error submitting complaint: ' . $e->getMessage();
            logger()->error('Complaint submission error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to submit complaint. Please try again later.',
            ]);
        }
    }

    public function newComplaint(): void
    {
        $this->reset();
        $this->submitted = false;
        $this->submitError = null;
    }

    public function render()
    {
        return view('livewire.portal.complaints', [
            'complaintTypes' => [
                'bug' => 'Report a Bug',
                'feature' => 'Feature Request',
                'complaint' => 'File a Complaint',
                'feedback' => 'Send Feedback',
                'other' => 'Other',
            ],
        ]);
    }
}
