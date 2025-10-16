<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use App\Services\CacheService;
use App\Events\FriendRequestSent;
use App\Events\FriendRequestResponded;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $q = $request->q;
        $userId = (string) auth()->user()->_id;

        $results = User::where('_id', '!=', $userId)
            ->where('username', 'like', "%$q%")
            ->select(['_id', 'username', 'name', 'profile_image'])
            ->limit(20)
            ->get();

        return response()->json($results);
    }

    public function send(Request $request)
    {
        $request->validate(['to_user_id' => 'required|string']);
        $from = (string) auth()->user()->_id;
        $to = (string) $request->to_user_id;
        if ($from === $to) return response()->json(['message' => 'Không thể tự kết bạn'], 422);

        // Prevent duplicates
        $existing = FriendRequest::where(function($q) use ($from, $to) {
                $q->where('from_user_id', $from)->where('to_user_id', $to);
            })->orWhere(function($q) use ($from, $to) {
                $q->where('from_user_id', $to)->where('to_user_id', $from);
            })->whereIn('status', ['pending','accepted'])
            ->first();
        if ($existing) return response()->json(['message' => 'Yêu cầu đã tồn tại'], 422);

        $fr = FriendRequest::create([
            'from_user_id' => $from,
            'to_user_id' => $to,
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);
        
        // Clear caches for both users
        CacheService::clearFriendCachesForUsers([$from, $to]);
        
        // Get from user data for WebSocket event
        $fromUser = User::find($from);
        
        // Broadcast WebSocket event to the recipient
        event(new FriendRequestSent($fr, $fromUser, $to));
        
        return response()->json($fr, 201);
    }

    public function incoming()
    {
        $userId = (string) auth()->user()->_id;
        
        // Try to get from cache first
        $cached = CacheService::getCachedIncomingRequests($userId);
        if ($cached !== null) {
            return response()->json($cached);
        }
        
        // If not in cache, fetch from database
        $list = FriendRequest::where('to_user_id', $userId)
            ->where('status', 'pending')
            ->with('fromUser:_id,username,name,profile_image')
            ->get();
            
        // Cache the result
        CacheService::cacheIncomingRequests($userId, $list);
        
        return response()->json($list);
    }

    public function outgoing()
    {
        $userId = (string) auth()->user()->_id;
        
        // Try to get from cache first
        $cached = CacheService::getCachedOutgoingRequests($userId);
        if ($cached !== null) {
            return response()->json($cached);
        }
        
        // If not in cache, fetch from database
        $list = FriendRequest::where('from_user_id', $userId)
            ->where('status', 'pending')
            ->with('toUser:_id,username,name,profile_image')
            ->get();
            
        // Cache the result
        CacheService::cacheOutgoingRequests($userId, $list);
        
        return response()->json($list);
    }

    public function respond(Request $request)
    {
        $request->validate([
            'request_id' => 'required|string',
            'action' => 'required|in:accept,decline'
        ]);
        $userId = (string) auth()->user()->_id;
        $fr = FriendRequest::where('_id', $request->request_id)
            ->where('to_user_id', $userId)
            ->where('status', 'pending')
            ->first();
        if (!$fr) return response()->json(['message' => 'Yêu cầu không hợp lệ'], 404);

        $fr->status = $request->action === 'accept' ? 'accepted' : 'declined';
        $fr->updated_at = now()->toDateTimeString();
        $fr->save();
        
        // Clear caches for both users
        CacheService::clearFriendCachesForUsers([$fr->from_user_id, $fr->to_user_id]);
        
        // Get to user data for WebSocket event
        $toUser = User::find($fr->to_user_id);
        
        // Broadcast WebSocket event to the sender
        event(new FriendRequestResponded($fr, $fr->from_user_id, $toUser, $request->action));
        
        return response()->json($fr);
    }

    public function friends()
    {
        $userId = (string) auth()->user()->_id;
        
        // Try to get from cache first
        $cached = CacheService::getCachedFriends($userId);
        if ($cached !== null) {
            return response()->json($cached);
        }
        
        // If not in cache, fetch from database
        $accepted = FriendRequest::where(function($q) use ($userId) {
                $q->where('from_user_id', $userId)->orWhere('to_user_id', $userId);
            })->where('status', 'accepted')->get();

        $friendIds = [];
        foreach ($accepted as $row) {
            $friendIds[] = $row->from_user_id === $userId ? $row->to_user_id : $row->from_user_id;
        }
        $friends = User::whereIn('_id', $friendIds)->select(['_id','username','name','email','profile_image'])->get();
        
        // Cache the result
        CacheService::cacheFriends($userId, $friends);
        
        return response()->json($friends);
    }

    public function unfriend(Request $request)
    {
        $request->validate(['friend_user_id' => 'required|string']);
        $userId = (string) auth()->user()->_id;
        $friendId = (string) $request->friend_user_id;

        // Find accepted relation both directions
        $relation = FriendRequest::where(function($q) use ($userId, $friendId) {
                $q->where('from_user_id', $userId)->where('to_user_id', $friendId);
            })->orWhere(function($q) use ($userId, $friendId) {
                $q->where('from_user_id', $friendId)->where('to_user_id', $userId);
            })->where('status', 'accepted')->first();

        if (!$relation) {
            return response()->json(['message' => 'Không tìm thấy quan hệ bạn bè'], 404);
        }

        // Option 1: delete relation; Option 2: set declined. We'll delete for simplicity.
        $relation->delete();
        
        // Clear caches for both users
        CacheService::clearFriendCachesForUsers([$userId, $friendId]);

        return response()->json(['message' => 'Đã hủy kết bạn']);
    }
}


