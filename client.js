const socket = io();

// --- DOM Elements ---
const localVideo = document.getElementById('local-video');
const remoteVideo = document.getElementById('remote-video');
const lobbyScreen = document.getElementById('lobby-screen');
const roomInput = document.getElementById('room-input');
const joinBtn = document.getElementById('join-btn');
const roomDisplay = document.getElementById('room-display');

// Buttons
const micBtn = document.getElementById('mic-btn');
const cameraBtn = document.getElementById('camera-btn');

// --- WebRTC Config ---
const rtcConfig = {
    iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
};

let localStream;
let peerConnection;
let roomId;

// 1. Lobby Logic
const urlParams = new URLSearchParams(window.location.search);
const roomFromUrl = urlParams.get('room');

if (roomFromUrl) {
    roomId = roomFromUrl;
    lobbyScreen.classList.add('hidden');
    roomDisplay.innerText = `Room: ${roomId}`;
    startCall();
} else {
    joinBtn.addEventListener('click', () => {
        const input = roomInput.value;
        if (input === "") return alert("Please enter a room name");
        window.location.href = `/?room=${input}`;
    });
}

// 2. Start Call
async function startCall() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
        socket.emit('join-room', roomId);
    } catch (err) {
        console.error('Error accessing media:', err);
    }
}

// 3. Socket Events
socket.on('user-connected', async (userId) => {
    createPeerConnection(userId);
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);
    socket.emit('offer', { target: userId, sdp: offer, caller: socket.id });
});

socket.on('offer', async (payload) => {
    createPeerConnection(payload.caller);
    await peerConnection.setRemoteDescription(new RTCSessionDescription(payload.sdp));
    const answer = await peerConnection.createAnswer();
    await peerConnection.setLocalDescription(answer);
    socket.emit('answer', { target: payload.caller, sdp: answer });
});

socket.on('answer', (payload) => {
    peerConnection.setRemoteDescription(new RTCSessionDescription(payload.sdp));
});

socket.on('ice-candidate', (candidate) => {
    if (peerConnection) {
        peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    }
});

function createPeerConnection(targetSocketId) {
    peerConnection = new RTCPeerConnection(rtcConfig);
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
    
    peerConnection.ontrack = (event) => {
        remoteVideo.srcObject = event.streams[0];
    };

    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            socket.emit('ice-candidate', { target: targetSocketId, candidate: event.candidate });
        }
    };
}

// ==========================================
// 4. BUTTON LOGIC (MUTE & CAMERA TOGGLE)
// ==========================================

// Toggle Microphone
micBtn.addEventListener('click', () => {
    const audioTrack = localStream.getAudioTracks()[0];
    
    if (audioTrack.enabled) {
        // Disable (Mute)
        audioTrack.enabled = false;
        micBtn.innerText = "Unmute Mic";
        micBtn.classList.add('off'); // Turn button red
    } else {
        // Enable (Unmute)
        audioTrack.enabled = true;
        micBtn.innerText = "Mute Mic";
        micBtn.classList.remove('off'); // Turn button grey
    }
});

// Toggle Camera
cameraBtn.addEventListener('click', () => {
    const videoTrack = localStream.getVideoTracks()[0];
    
    if (videoTrack.enabled) {
        // Disable (Turn Off Video)
        videoTrack.enabled = false;
        cameraBtn.innerText = "Start Camera";
        cameraBtn.classList.add('off'); // Turn button red
    } else {
        // Enable (Turn On Video)
        videoTrack.enabled = true;
        cameraBtn.innerText = "Stop Camera";
        cameraBtn.classList.remove('off'); // Turn button grey
    }
});