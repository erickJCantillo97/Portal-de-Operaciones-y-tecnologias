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

    public $title = 'Carga de archivos';

    public $type;

    public $processId;

    public function __construct($processId, $message = '')
    {
        // $this->title = $title;
        $this->message = $message;
        $this->processId = $processId;

        $this->message = 'La carga de archivos '.$this->processId."\n".' ha terminado de cargar exitosamente.';
    }

    // public function __construct(Contract $contract, $type = 'created')
    // {

    //     $this->type = $type;

    //     if ($type === 'created') {
    //         $this->message = 'Se ha creado el contrato:'."\n".$contract->contract_id;
    //     } elseif ($type === 'updated') {
    //         $this->message = 'Se ha actualizado el contrato:'."\n".$contract->name;
    //     } elseif ($type === 'deleted') {
    //         $this->message = 'Se ha eliminado el contrato:'."\n".$contract->name;
    //     }
    // }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('bulk-data-loading.'.$this->processId),
        ];
    }

    public function broadcastAs()
    {
        return 'bulk.data.loaded';
    }
}
