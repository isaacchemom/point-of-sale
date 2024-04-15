<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StkPushSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $transaction;
    public $user_id;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $transaction, $data)
    {
        //
        $this->transaction = $transaction;
        $this->user_id = $user_id;
        $this->data = $data;

    }
    public function broadcastAs()
    {
        return 'stkpush.sent';
    }
    public function broadcastWith()
    {
        return [
            'transaction' => $this->transaction,
            'user_id' => $this->user_id,
            'data'=>$this->data
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user = User::find($this->user_id);
     
        // Log::info('user  found'. json_encode(['user' => $user]));

        if($user){
            return new Channel('cashier.'.$user->id);

        }
        
    }
}
