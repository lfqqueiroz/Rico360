<x-app-layout>
    <!-- Main layout component that provides the base structure for the dashboard -->

    <!-- Header section containing the application title -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rico Call maker') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col">

            <!-- Call Interface Section: Contains the user list and call controls -->
            <div class="flex mb-8">
                <!-- Sidebar: Displays list of online users with their current status -->
                <aside class="w-1/4 bg-gray-100 dark:bg-gray-900 shadow-md rounded-lg p-4">
                    <!-- Users are shown with status indicators (online, in call, away) -->
                    <!-- Each user has a call button that is disabled when they are in a call -->
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        {{ __('Online Users') }}
                    </h3>
                    <ul>

                        @forelse ($usersOnline as $user)
                            <li class="my-2">
                                <button 
                                    data-user-id="{{ $user->id }}"
                                    onclick="makeCall('{{ $user->id }}')" 
                                    @if($user->status === 'in_call') disabled @endif
                                    class="text-left w-full px-4 py-2 
                                        @if($user->status === 'in_call')
                                            bg-gray-400 cursor-not-allowed
                                        @else
                                            bg-blue-500 hover:bg-blue-700
                                        @endif
                                    text-white rounded flex justify-between items-center">
                                    <span>{{ $user->name }}</span>
                                    <span class="inline-flex items-center ml-4">
                                        @if($user->status === 'online')
                                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                            {{ __('Online') }}
                                        @elseif($user->status === 'in_call')
                                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                            {{ __('In Call') }}
                                        @elseif($user->status === 'away')
                                            <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                            {{ __('Away') }}
                                        @else
                                            <span class="w-3 h-3 bg-gray-500 rounded-full mr-2"></span>
                                            {{ __('Offline') }}
                                        @endif
                                    </span>
                                </button>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">
                                {{ __('No users are online right now.') }}
                            </li>
                        @endforelse
                    </ul>
                </aside>

                <!-- Main Content Area: Contains call status and controls -->
                <div class="flex-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg ml-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Status display shows current call state -->
                        {{ __("You're logged in!") }}
                        <div id="status" class="mt-4 text-green-600"></div>
                        
                        <!-- Incoming Call Controls: Hidden by default, shown when receiving a call -->
                        <div id="incomingCallControls" class="mt-4 hidden">
                            <!-- Accept/Reject buttons for incoming calls -->
                            <button id="acceptCall" class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded mr-2">
                                {{ __('Accept') }}
                            </button>
                            <button id="rejectCall" class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded">
                                {{ __('Reject') }}
                            </button>
                        </div>

                        <!-- Active Call Controls: Hidden by default, shown during an active call -->
                        <div id="activeCallControls" class="mt-4 hidden">
                            <!-- Hangup and Mute buttons for active calls -->
                            <button id="hangupCall" class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded mr-2">
                                {{ __('Hang Up') }}
                            </button>
                            <button id="muteCall" class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded">
                                {{ __('Mute') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call History Section: Displays a log of all calls -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Filter Controls: Allow filtering calls by type, status, and date -->
                    <div class="flex justify-between items-center mb-4">
                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Call History') }}
                        </h3>
                        
                        <!-- Filter Form: Contains dropdown menus and date picker -->
                        <form action="{{ route('dashboard') }}" method="GET" class="flex gap-4">
                            <!-- Filter options for call type, status, and date -->
                            <select name="type" class="rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Types') }}</option>
                                <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>{{ __('Incoming') }}</option>
                                <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>{{ __('Outgoing') }}</option>
                            </select>

                            <select name="status" class="rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="missed" {{ request('status') === 'missed' ? 'selected' : '' }}>{{ __('Missed') }}</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                            </select>

                            <input type="date" name="date" value="{{ request('date') }}" 
                                class="rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                {{ __('Filter') }}
                            </button>

                            @if(request()->hasAny(['type', 'status', 'date']))
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Call History Table: Shows detailed call records -->
                    <div class="overflow-x-auto">
                        <!-- Table with sortable columns for contact, type, status, duration, and date -->
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('dashboard', ['sort' => 'to_user_id', 'direction' => request('sort') === 'to_user_id' && request('direction') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}" class="flex items-center">
                                            {{ __('Contact') }}
                                            @if(request('sort') === 'to_user_id')
                                                @if(request('direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a class="flex items-center">
                                            {{ __('Type') }}
                                            
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('dashboard', ['sort' => 'status', 'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}" class="flex items-center">
                                            {{ __('Status') }}
                                            @if(request('sort') === 'status')
                                                @if(request('direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('dashboard', ['sort' => 'duration', 'direction' => request('sort') === 'duration' && request('direction') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}" class="flex items-center">
                                            {{ __('Duration') }}
                                            @if(request('sort') === 'duration')
                                                @if(request('direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('dashboard', ['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'] + request()->except(['sort', 'direction'])) }}" class="flex items-center">
                                            {{ __('Date') }}
                                            @if(request('sort') === 'created_at')
                                                @if(request('direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($callHistory as $call)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                @if(auth()->id() === $call->from_user_id)
                                                    {{ $call->toUser->name ?? 'Unknown' }}
                                                @else
                                                    {{ $call->fromUser->name ?? 'Unknown' }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ auth()->id() === $call->from_user_id ? 
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                                {{ auth()->id() === $call->from_user_id ? __('Outgoing') : __('Incoming') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $call->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                   ($call->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                                                {{ ucfirst($call->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $call->formatted_duration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $call->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('No call records found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination Controls: Shows when there are multiple pages of call history -->
                        @if($callHistory->hasPages())
                            <div class="mt-4">
                                {{ $callHistory->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <!-- Core application JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Twilio Client SDK for call functionality -->
    <script src="https://sdk.twilio.com/js/client/v1.14/twilio.js"></script>
    <!-- Custom dashboard JavaScript handling calls and UI updates -->
    <script src="{{ asset('js/call-dashboard.js') }}"></script>
    
    <!-- Initialize dashboard when page loads -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeCallDashboard('{{auth()->user()->id}}');
        });
    </script>

    <!-- Additional scripts for filter functionality -->
    @push('scripts')
    
    <!-- Auto-submit form when filters change -->
    <script>
        document.querySelectorAll('select[name="type"], select[name="status"], input[name="date"]').forEach(element => {
            element.addEventListener('change', () => {
                element.closest('form').submit();
            });
        });
    </script>
    @endpush
</x-app-layout>
