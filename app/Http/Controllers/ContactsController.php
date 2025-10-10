<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function get()
    {
        $contacts = User::where('_id', '!=', auth()->user()->_id)->get();

        //count unread messages
        $unreadIds = Message::select(DB::raw('`from` as sender_id, count(`from`) as messages_count'))
            ->where('to', auth()->user()->_id)
            ->where('read', false)
            ->groupBy('from')
            ->get();

        $contacts = $contacts->map(function ($contact) use ($unreadIds) {
            $contactUnread = $unreadIds->where('sender_id', $contact->_id)->first();
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
        });

        return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
        $currentUserId = auth()->user()->_id;
        
        //mark all messages with the selected contact as read
        Message::where('from', $id)->where('to', $currentUserId)->update(['read' => true]);
        $messages = Message::where(function ($q) use ($id, $currentUserId) {
            $q->where('from', $currentUserId);
            $q->where('to', $id);
        })->orWhere(function ($q) use ($id, $currentUserId) {
            $q->where('from', $id);
            $q->where('to', $currentUserId);
        })->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }

    public function send(Request $request)
    {
        // Validate request
        $request->validate([
            'contact_id' => 'required|string',
            'text' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'from' => auth()->user()->_id,
            'to' => $request->contact_id,
            'text' => $request->text,
            'read' => false
        ]);

        // Load the relationship before broadcasting
        $message->load('fromContact');

        broadcast(new NewMessage($message));

        return response()->json($message);
    }
}
