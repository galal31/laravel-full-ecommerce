<?php

use Livewire\Component;

new class extends Component {
    public function showMessagesOf($type)
    {
        $this->dispatch('showMessagesOf', type: $type);
    }
};
?>

<div class="sidebar-left">
    <div class="sidebar">
        <div class="sidebar-content email-app-sidebar d-flex">
            <div class="email-app-menu col-md-5 card d-none d-lg-block">
                <div class="form-group form-group-compose text-center">
                    <button type="button" class="btn btn-danger btn-block my-1"><i class="ft-mail"></i>
                        Compose</button>
                </div>
                <h6 class="text-muted text-bold-500 mb-1">Messages</h6>
                <div class="list-group list-group-messages">
                    <button wire:click="showMessagesOf('inbox')" type="button" class="list-group-item active border-0">
                        <i class="ft-inbox mr-1"></i> Inbox
                        <span class="badge badge-secondary badge-pill float-right">8</span>
                    </button>
                    <button wire:click="showMessagesOf('read')" type="button"
                        class="list-group-item list-group-item-action border-0"><i class="la la-paper-plane-o mr-1"></i>
                        Read</button>
                    <button wire:click="showMessagesOf('unread')" type="button"
                        class="list-group-item list-group-item-action border-0"><i class="la la-paper-plane-o mr-1"></i>
                        Unread</button>
                </div>
            </div>
            @livewire('dashboard.contacts.contact-inbox')
        </div>
    </div>
</div>
