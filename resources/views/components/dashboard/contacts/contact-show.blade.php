<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Dashboard\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;
new class extends Component {
    public $replySubject = '';
    public $replyMessage = '';
    public $contact = null;

    public function mount()
    {
        $this->contact = Contact::with('user')->latest()->first();
    }

    // listeners
    #[On('contactSelected')]
    public function loadContact($contactId)
    {
        Contact::where('id', $contactId)->update([
            'is_read' => 1,
        ]);

        $this->contact = Contact::with('user')->find($contactId);
    }

    // events
    public function openReplyModal()
    {
        if (!$this->contact) {
            return;
        }

        $this->replySubject = 'Re: ' . $this->contact->subject;
        $this->replyMessage = '';

        $this->dispatch('openReplyModal');
    }

    public function sendReply()
    {
        $this->validate([
            'replySubject' => 'required|string|max:255',
            'replyMessage' => 'required|string|min:3',
        ]);

        if (!$this->contact) {
            return;
        }

        Mail::to($this->contact->email)->send(new ContactReplyMail($this->contact, $this->replySubject, $this->replyMessage));

        $this->dispatch('closeReplyModal');

        session()->flash('success', 'Reply sent successfully.');
    }
};

?>

<div class="content-right">
    <div class="content-wrapper">
        <div class="content-header row"></div>

        <div class="content-body">
            <div class="card email-app-details d-block">
                <div class="card-content">

                    @if ($contact)
                        <div class="email-app-options card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            wire:click="openReplyModal">
                                            <i class="la la-reply"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-secondary">
                                            <i class="ft-trash-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12 text-right">
                                    <span class="badge {{ $contact->is_read ? 'badge-success' : 'badge-danger' }}">
                                        {{ $contact->is_read ? 'Read' : 'Unread' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="email-app-title card-body">
                            <h3 class="list-group-item-heading">
                                {{ $contact->subject ?? 'No Subject' }}
                            </h3>

                            <p class="list-group-item-text mb-0">
                                <span class="primary">
                                    From Contact Message
                                </span>
                            </p>
                        </div>

                        <div class="media-list">
                            <div class="email-app-sender media border-0 bg-blue-grey bg-lighten-5 p-1">
                                <div class="media-left pr-1">
                                    <span class="avatar avatar-md">
                                        <img class="media-object rounded-circle"
                                            src="{{ asset('dashboard-assets/app-assets/images/portrait/small/avatar-s-7.png') }}"
                                            alt="User image">
                                    </span>
                                </div>

                                <div class="media-body w-100">
                                    <h6 class="list-group-item-heading mb-0">
                                        {{ $contact->name ?? 'Unknown User' }}
                                    </h6>

                                    <p class="list-group-item-text mb-0">
                                        {{ $contact->email }}

                                        @if ($contact->phone)
                                            <span class="mx-1">|</span>
                                            {{ $contact->phone }}
                                        @endif

                                        <span class="float-right text-muted">
                                            {{ $contact->created_at ? $contact->created_at->diffForHumans() : '' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="email-app-text card-body">
                                <div class="email-app-message">
                                    <p style="white-space: pre-line;">
                                        {{ $contact->message }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card-body text-center text-muted">
                            اختار رسالة من القائمة لعرض محتواها.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @if ($contact)
        <div class="modal fade" id="replyContactModal" tabindex="-1" role="dialog"
            aria-labelledby="replyContactModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title white" id="replyContactModalLabel">
                            <i class="la la-reply mr-1"></i>
                            Reply To {{ $contact->name }}
                        </h5>

                        <button type="button" class="close white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form wire:submit.prevent="sendReply">
                        <div class="modal-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    Please check the form fields.
                                </div>
                            @endif

                            <div class="form-group">
                                <label>To</label>
                                <input type="email" class="form-control" value="{{ $contact->email }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" class="form-control" wire:model="replySubject">

                                @error('replySubject')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" rows="7" wire:model="replyMessage" placeholder="Write your reply here..."></textarea>

                                @error('replyMessage')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="la la-paper-plane mr-1"></i>
                                Send Reply
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
