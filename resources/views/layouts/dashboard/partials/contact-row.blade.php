<a class="admin-contact-row" @if ($contact)
    id="admin-contact-{{ $contact->id }}"
    href="{{ route('dashboard.contacts.index', ['contact_id' => $contact->id], false) }}"
@endif>
    <div @class(['media', 'bg-blue-grey bg-lighten-5' => ! $contact || ! $contact->is_read])>
        <div class="media-left align-self-center">
            <i class="ft-mail icon-bg-circle bg-cyan" aria-hidden="true"></i>
        </div>
        <div class="media-body overflow-hidden">
            <h6 class="media-heading mb-0 text-bold-600 text-truncate contact-name">{{ $contact?->name }}</h6>
            <p class="notification-text font-small-3 text-muted text-truncate mb-0 contact-email">{{ $contact?->email }}</p>
            <p class="notification-text font-small-3 text-truncate mb-0 contact-message">{{ $contact ? \Illuminate\Support\Str::words($contact->message, 5) : '' }}</p>
            <small class="media-meta text-muted contact-time">{{ $contact?->created_at?->diffForHumans() }}</small>
        </div>
    </div>
</a>
