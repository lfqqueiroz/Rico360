let device;
let currentConnection = null;
let isMuted = false;

// Fetches the Twilio access token for the specified user identity
async function fetchAccessToken(identity) {
    try {
        const response = await fetch('/access-token?identity=' + identity);
        const data = await response.json();
        return data.token;
    } catch (error) {
        console.error('Failed to fetch token:', error);
    }
}

// Initializes and registers the Twilio Device client with event handlers
async function registerClient(userId) {
    const token = await fetchAccessToken(userId);
    if (!token) {
        alert('Failed to get token. Please try again.');
        return;
    }

    device = new Twilio.Device(token, { debug: true });

    device.on('ready', () => {
        document.getElementById('status').innerText = 'Ready to make and receive calls';
    });

    device.on('error', (error) => {
        document.getElementById('status').innerText = `Error: ${error.message}`;
    });

    device.on('incoming', (connection) => {
        currentConnection = connection;
        let callerName = 'Unknown';
        try {
            const customParameters = JSON.parse(connection.parameters.customParameters || '{}');
            callerName = customParameters.callerName || 'Unknown';
        } catch (e) {
            console.error('Error parsing custom parameters:', e);
        }
        
        console.log('Incoming call from:', callerName);
        updateCallStatus('incoming', callerName);
        document.getElementById('incomingCallControls').classList.remove('hidden');

        // Handler for missed call
        const missedCallTimeout = setTimeout(() => {
            if (currentConnection && currentConnection.status() !== 'open') {
                console.log('Call missed - no answer');
                logCall(connection.parameters.From.replace('client:', ''), 'incoming', 'missed', 0);
                updateCallStatus('missed');
                document.getElementById('incomingCallControls').classList.add('hidden');
                currentConnection = null;
            }
        }, 30000);

        // Handler for accepting call
        const acceptButton = document.getElementById('acceptCall');
        if (acceptButton) {
            acceptButton.onclick = () => {
                console.log('Call accepted by recipient');
                clearTimeout(missedCallTimeout);
                
                try {
                    connection.accept();
                    let startTime = Date.now();
                    
                    document.getElementById('incomingCallControls').classList.add('hidden');
                    document.getElementById('activeCallControls').classList.remove('hidden');
                    updateCallStatus('in_call');
                    updateUserStatus('in_call');

                    connection.on('disconnect', () => {
                        console.log('Call ended - after accept');
                        const duration = Math.floor((Date.now() - startTime) / 1000);
                        logCall(connection.parameters.From.replace('client:', ''), 'incoming', 'completed', duration);
                        
                        document.getElementById('activeCallControls').classList.add('hidden');
                        updateCallStatus('ended');
                        updateUserStatus('online');
                        currentConnection = null;
                    });
                } catch (error) {
                    console.error('Error accepting call:', error);
                    updateCallStatus('error');
                    document.getElementById('incomingCallControls').classList.add('hidden');
                    currentConnection = null;
                }
            };
        }

        // Handler for rejecting call
        const rejectButton = document.getElementById('rejectCall');
        if (rejectButton) {
            rejectButton.onclick = () => {
                console.log('Call rejected by recipient');
                clearTimeout(missedCallTimeout);
                
                try {
                    connection.reject();
                    logCall(connection.parameters.From.replace('client:', ''), 'incoming', 'rejected', 0);
                    
                    document.getElementById('incomingCallControls').classList.add('hidden');
                    updateCallStatus('rejected');
                    updateUserStatus('online');
                    currentConnection = null;
                } catch (error) {
                    console.error('Error rejecting call:', error);
                    updateCallStatus('error');
                    document.getElementById('incomingCallControls').classList.add('hidden');
                    currentConnection = null;
                }
            };
        }

        // Handler for when caller cancels
        connection.on('cancel', () => {
            console.log('Call cancelled by caller');
            clearTimeout(missedCallTimeout);
            logCall(connection.parameters.From.replace('client:', ''), 'incoming', 'missed', 0);
            
            document.getElementById('incomingCallControls').classList.add('hidden');
            updateCallStatus('missed');
            updateUserStatus('online');
            currentConnection = null;
        });
    });
}

// Initiates an outgoing call to the specified receiver
function makeCall(receiverId) {
    const button = document.querySelector(`button[data-user-id="${receiverId}"]`);
    
    if (!button) {
        console.error('Button element not found');
        return;
    }

    if (button.disabled) {
        alert('This user is currently in another call.');
        return;
    }

    if (!device) {
        alert('Device not ready. Please wait...');
        return;
    }

    const params = { To: receiverId };
    currentConnection = device.connect(params);
    let startTime = Date.now();
    let callStatus = 'initiated';
    
    document.getElementById('activeCallControls').classList.remove('hidden');
    updateCallStatus('calling');
    updateUserStatus('in_call');

    if (currentConnection) {
        console.log('Outgoing call initiated');

        // Handler for rejecting call
        currentConnection.on('reject', () => {
            console.log('Call rejected by recipient');
            logCall(receiverId, 'outgoing', 'rejected', 0);
            
            document.getElementById('activeCallControls').classList.add('hidden');
            updateCallStatus('rejected_by_remote');
            updateUserStatus('online');
            currentConnection = null;

            // Reset status after 3 seconds
            setTimeout(() => {
                if (!currentConnection) {
                    updateCallStatus('ready');
                }
            }, 3000);
        });

        // Handler for disconnecting
        currentConnection.on('disconnect', () => {
            console.log('Call disconnected', callStatus);
            const duration = Math.floor((Date.now() - startTime) / 1000);
            const finalStatus = callStatus === 'accepted' ? 'completed' : 'missed';
            
            logCall(receiverId, 'outgoing', finalStatus, duration);
            document.getElementById('activeCallControls').classList.add('hidden');
            updateCallStatus('ended');
            updateUserStatus('online');
            currentConnection = null;

            setTimeout(() => {
                if (!currentConnection) {
                    updateCallStatus('ready');
                }
            }, 3000);
        });

        currentConnection.on('accept', () => {
            console.log('Call accepted by recipient');
            callStatus = 'accepted';
            updateCallStatus('in_call');
            startTime = Date.now(); // Reset start time when call is actually accepted
        });

        // Add handler for call error
        currentConnection.on('error', (error) => {
            console.error('Call error:', error);
            document.getElementById('activeCallControls').classList.add('hidden');
            updateCallStatus('error');
            updateUserStatus('online');
            currentConnection = null;

            // Reset status after 3 seconds
            setTimeout(() => {
                if (!currentConnection) {
                    updateCallStatus('ready');
                }
            }, 3000);
        });
    }
}

// Updates the user's current status (online, offline, in_call) in the backend
async function updateUserStatus(status) {
    try {
        await fetch('/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status })
        });
    } catch (error) {
        console.error('Failed to update status:', error);
    }
}

// Sets up WebSocket listeners for real-time user status updates
function setupWebSockets() {
    if (typeof window.Echo === 'undefined') {
        console.error('Laravel Echo is not initialized');
        return;
    }

    window.Echo.private('users')
        .listen('UserStatusUpdated', (e) => {
            const userElement = document.querySelector(`button[data-user-id="${e.userId}"]`);
            if (userElement) {
                const statusIndicator = userElement.querySelector('.status-indicator');
                const statusText = userElement.querySelector('.status-text');
                
                statusIndicator.className = `w-3 h-3 rounded-full mr-2 status-indicator ${getStatusColor(e.status)}`;
                statusText.textContent = getStatusText(e.status);

                if (e.status === 'in_call') {
                    userElement.disabled = true;
                    userElement.classList.remove('bg-blue-500', 'hover:bg-blue-700');
                    userElement.classList.add('bg-gray-400', 'cursor-not-allowed');
                } else {
                    userElement.disabled = false;
                    userElement.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    userElement.classList.add('bg-blue-500', 'hover:bg-blue-700');
                }
            }
        });
}

// Returns the appropriate CSS class for the status indicator color
function getStatusColor(status) {
    switch (status) {
        case 'online': return 'bg-green-500';
        case 'in_call': return 'bg-red-500';
        case 'away': return 'bg-yellow-500';
        default: return 'bg-gray-500';
    }
}

// Returns the human-readable text for each status
function getStatusText(status) {
    switch (status) {
        case 'online': return 'Online';
        case 'in_call': return 'In Call';
        case 'away': return 'Away';
        default: return 'Offline';
    }
}

// Initializes the call dashboard with all necessary event listeners and configurations
function initializeCallDashboard(userId) {
    console.log('Initializing dashboard...');
    registerClient(userId);
    updateUserStatus('online');
    setupCallControls();
    
    // Only setup WebSockets if Echo is available
    if (typeof window.Echo !== 'undefined') {
        setupWebSockets();
    } else {
        console.warn('Laravel Echo not available, real-time updates disabled');
    }

    // Setup beforeunload event
    window.addEventListener('beforeunload', function() {
        updateUserStatus('offline');
    });
}

// Sets up call control buttons (hangup, mute) and their event listeners
function setupCallControls() {
    console.log('Setting up call controls');
    
    // Setup hangup button
    const hangupButton = document.getElementById('hangupCall');
    if (hangupButton) {
        hangupButton.addEventListener('click', () => {
            if (currentConnection) {
                console.log('Call ended by user');
                currentConnection.disconnect();
                document.getElementById('activeCallControls').classList.add('hidden');
                updateCallStatus('ended');
                updateUserStatus('online');
            }
        });
    }

    // Setup mute button
    const muteButton = document.getElementById('muteCall');
    if (muteButton) {
        console.log('Found mute button');
        muteButton.addEventListener('click', () => {
            if (currentConnection) {
                if (!isMuted) {
                    // Mute the local audio
                    const localStream = currentConnection.getLocalStream();
                    if (localStream) {
                        localStream.getTracks().forEach(track => {
                            if (track.kind === 'audio') {
                                track.enabled = false;
                            }
                        });
                    }
                    muteButton.textContent = 'Unmute';
                    muteButton.classList.remove('bg-gray-500');
                    muteButton.classList.add('bg-red-500');
                    isMuted = true;
                } else {
                    // Unmute the local audio
                    const localStream = currentConnection.getLocalStream();
                    if (localStream) {
                        localStream.getTracks().forEach(track => {
                            if (track.kind === 'audio') {
                                track.enabled = true;
                            }
                        });
                    }
                    muteButton.textContent = 'Mute';
                    muteButton.classList.remove('bg-red-500');
                    muteButton.classList.add('bg-gray-500');
                    isMuted = false;
                }
            }
        });
    } else {
        console.error('Mute button not found');
    }
}

// Logs call details to the backend (type, status, duration)
async function logCall(contactId, type, status, duration = null) {
    try {
        await fetch('/call-logs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                contact_id: contactId,
                type,
                status,
                duration
            })
        });
    } catch (error) {
        console.error('Failed to log call:', error);
    }
}

// Updates the UI to reflect the current call status
function updateCallStatus(status, callerName = '') {
    const statusElement = document.getElementById('status');
    console.log('Updating call status:', status);
    
    switch (status) {
        case 'incoming':
            statusElement.innerText = `Incoming call from ${callerName}`;
            statusElement.className = 'mt-4 text-yellow-600';
            break;
        case 'calling':
            statusElement.innerText = 'Calling...';
            statusElement.className = 'mt-4 text-blue-600';
            break;
        case 'in_call':
            statusElement.innerText = 'In call';
            statusElement.className = 'mt-4 text-green-600';
            break;
        case 'ended':
            statusElement.innerText = 'Call ended';
            statusElement.className = 'mt-4 text-red-600';
            break;
        case 'rejected':
            statusElement.innerText = 'Call rejected';
            statusElement.className = 'mt-4 text-red-600';
            break;
        case 'rejected_by_remote':
            statusElement.innerText = 'Call was rejected';
            statusElement.className = 'mt-4 text-red-600';
            break;
        case 'missed':
            statusElement.innerText = 'Missed call';
            statusElement.className = 'mt-4 text-yellow-600';
            break;
        case 'error':
            statusElement.innerText = 'Call error occurred';
            statusElement.className = 'mt-4 text-red-600';
            break;
        case 'ready':
            statusElement.innerText = 'Ready to make and receive calls';
            statusElement.className = 'mt-4 text-green-600';
            break;
        default:
            statusElement.innerText = 'Ready to make and receive calls';
            statusElement.className = 'mt-4 text-green-600';
    }
}

// Add listener for the beforeunload event
window.addEventListener('beforeunload', (event) => {
    if (currentConnection) {
        currentConnection.disconnect();
        updateUserStatus('offline');
    }
});
 