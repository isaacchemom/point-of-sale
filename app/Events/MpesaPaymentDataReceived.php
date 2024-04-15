<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MpesaPaymentDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($cashier_id, $data)
    {
        //
        $this->user_id = $cashier_id;
        $this->data = $data;

    }
    public function broadcastAs()
    {
        return 'mpesa-paymentdata-received';
    }
    public function broadcastWith()
    {
        return [
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
