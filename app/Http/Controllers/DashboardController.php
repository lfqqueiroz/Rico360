<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Call;

class DashboardController extends Controller
{
    // Display dashboard with online users and call history
    // Handles filtering and sorting of call records
    public function index(Request $request)
    {
        // Get all online users except the current user
        $usersOnline = User::where('status', '!=', 'offline')
            ->where('id', '!=', auth()->id())
            ->get();

        // Build query for call history related to current user
        $query = Call::where(function($q) {
            $q->where('from_user_id', auth()->id())
                ->orWhere('to_user_id', auth()->id());
        })->with(['fromUser', 'toUser']);

        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply call type filter (incoming/outgoing)
        if ($request->filled('type')) {
            if ($request->type === 'outgoing') {
                $query->where('from_user_id', auth()->id());
            } else if ($request->type === 'incoming') {
                $query->where('to_user_id', auth()->id());
            }
        }

        // Apply date filter if provided
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Apply sorting to the results
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results and maintain query parameters
        $callHistory = $query->paginate(10)->withQueryString();

        return view('dashboard', compact('usersOnline', 'callHistory'));
    }
}
