<?php

namespace App\Events;

use App\Models\dashboard\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class FaqSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Contact $contact)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('faqs_sent')
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->contact->id,
            'name' => $this->contact->name,
            'email' => $this->contact->email,
            'message' => Str::words($this->contact->message, 5),
            'created_at' => $this->contact->created_at->diffForHumans()
        ];
    }
}
