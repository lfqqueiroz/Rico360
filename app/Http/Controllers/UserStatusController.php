<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class UserStatusController extends Controller
{
    // Update user's current status (online/offline/in_call/away)
    // Used for real-time presence tracking
    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline,in_call,away'
        ]);

        DB::table('users')->where('id', auth()->id())->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
} 