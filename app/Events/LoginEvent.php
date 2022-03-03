<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class LoginEvent extends Event
{
    use SerializesModels;
    
    public $request;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $user)
    {
        $this->request = $request;
        $this->user = $user;
    }
}
