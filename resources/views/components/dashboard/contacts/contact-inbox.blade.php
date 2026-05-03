<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dashboard\Contact;
use Livewire\Attributes\On;
new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selectedContactId = null;

    public $type = 'inbox';

    public function mount()
    {
        $this->selectedContactId = Contact::latest()->value('id');
    }
    // listeners
    #[On('showMessagesOf')]
    public function showMessagesOf($type)
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function with()
    {
        $messages = Contact::latest();
        if ($this->type == 'read') {
            $messages = $messages->where('is_read', 1);
        } elseif ($this->type == 'unread') {
            $messages = $messages->where('is_read', 0);
        }

        return [
            'contacts' => $messages->paginate(5),
        ];
    }

    public function selectContact($id)
    {
        $this->selectedContactId = $id;

        Contact::where('id', $id)->update([
            'is_read' => 1,
        ]);

        $this->dispatch('contactSelected', contactId: $id);
    }
};

?>

<div class="email-app-list-wraper contact-inbox-card col-md-7 card p-0">
    <div class="email-app-list">
        <div class="card-body chat-fixed-search">
            <fieldset class="form-group position-relative has-icon-left m-0 pb-1">
                <input type="text" class="form-control" id="iconLeft4" placeholder="Search email">
                <div class="form-control-position">
                    <i class="ft-search"></i>
                </div>
            </fieldset>
        </div>

        <div id="users-list" class="list-group">
            <div class="users-list-padding media-list">

                @forelse($contacts as $contact)
                    @php
                        $isSelected = $selectedContactId == $contact->id;
                        $isUnread = !$contact->is_read;
                    @endphp

                    <a href="#"
                       wire:key="contact-{{ $contact->id }}"
                       wire:click.prevent="selectContact({{ $contact->id }})"
                       class="media border-0
                       {{ $isSelected ? 'bg-blue-grey bg-lighten-4 border-left-primary border-left-3' : '' }}
                       {{ !$isSelected && $isUnread ? 'bg-blue-grey bg-lighten-5 border-right-primary border-right-2' : '' }}">

                        <div class="media-left pr-1">
                            <span class="avatar avatar-md">
                                <img class="media-object rounded-circle"
                                     src="{{ asset('dashboard-assets/app-assets/images/portrait/small/avatar-s-7.png') }}"
                                     alt="Generic placeholder image">
                            </span>
                        </div>

                        <div class="media-body w-100">
                            <h6 class="list-group-item-heading {{ $isUnread ? 'text-bold-700' : 'text-bold-400 text-muted' }}">
                                {{ $contact->name }}

                                <span class="float-right">
                                    <span class="font-small-2 {{ $isUnread ? 'primary text-bold-600' : 'text-muted' }}">
                                        {{ $contact->created_at ? $contact->created_at->diffForHumans() : '' }}
                                    </span>
                                </span>
                            </h6>

                            <p class="list-group-item-text text-truncate mb-0 {{ $isUnread ? 'text-bold-700' : 'text-muted' }}">
                                {{ $contact->subject }}
                            </p>

                            <p class="list-group-item-text text-truncate mb-0 {{ $isUnread ? 'text-bold-500' : 'text-muted' }}">
                                {{ $contact->message }}

                                <span class="float-right primary">
                                    @if ($isUnread)
                                        <span class="badge badge-success mr-1">New</span>
                                    @else
                                        <span class="badge badge-light mr-1">Read</span>
                                    @endif

                                    <i class="font-medium-1 ft-star {{ $isUnread ? 'warning' : 'blue-grey lighten-3' }}"></i>
                                </span>
                            </p>

                            <p class="list-group-item-text text-truncate mb-0 {{ $isUnread ? '' : 'text-muted' }}">
                                {{ $contact->email }}

                                @if ($contact->phone)
                                    - {{ $contact->phone }}
                                @endif
                            </p>
                        </div>
                    </a>
                @empty
                    <a href="#" class="media border-0">
                        <div class="media-left pr-1">
                            <span class="avatar avatar-md">
                                <img class="media-object rounded-circle"
                                     src="{{ asset('dashboard-assets/app-assets/images/portrait/small/avatar-s-7.png') }}"
                                     alt="Generic placeholder image">
                            </span>
                        </div>

                        <div class="media-body w-100">
                            <h6 class="list-group-item-heading">No contacts found</h6>
                            <p class="list-group-item-text text-truncate mb-0">There are no messages yet.</p>
                        </div>
                    </a>
                @endforelse

            </div>
        </div>

        <div class="card-footer">
            {{ $contacts->links() }}
        </div>
    </div>
</div>