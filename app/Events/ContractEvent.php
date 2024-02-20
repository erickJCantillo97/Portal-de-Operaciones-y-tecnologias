<?php

namespace App\Events;

use App\Models\Projects\Contract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;

    public $type;

    public function __construct(Contract $contract, $type = 'created')
    {

        $this->type = $type;

        if ($type === 'created') {
            $this->message = 'Se ha creado el contrato:' . "\n" . $contract->contract_id;
        } elseif ($type === 'updated') {
            $this->message = 'Se ha actualizado el contrato:' . "\n" . $contract->name;
        } elseif ($type === 'deleted') {
            $this->message = 'Se ha eliminado el contrato:' . "\n" . $contract->name;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('contracts'),
            // new PrivateChannel('myPrivateChannel.user.id'),
        ];
    }

    public function broadcastAs()
    {
        return 'contracts-event';
        //return 'private_msg';
    }
}
