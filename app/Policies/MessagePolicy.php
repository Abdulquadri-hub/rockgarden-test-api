<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    public function view(User $user, Message $message)
    {
        return $message->sender_id === $user->id || 
               $message->recipients()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Message $message)
    {
        return $message->sender_id === $user->id || 
               $message->recipients()->where('user_id', $user->id)->exists();
    }

    public function restore(User $user, Message $message)
    {
        return $message->sender_id === $user->id || 
               $message->recipients()->where('user_id', $user->id)->exists();
    }
}
