const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http);
const path = require('path');

// 1. Serve static files (client.js, css, etc.) from the current directory
app.use(express.static(__dirname));

// 2. Serve the consultation.html file on the root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'consultation.html'));
});

// 3. Socket.io Signaling Logic
io.on('connection', (socket) => {
    console.log('User connected:', socket.id);

    socket.on('join-room', (roomId) => {
        socket.join(roomId);
        // Notify others in the room
        socket.to(roomId).emit('user-connected', socket.id);

        // Handle WebRTC Signaling
        socket.on('offer', (payload) => {
            io.to(payload.target).emit('offer', payload);
        });

        socket.on('answer', (payload) => {
            io.to(payload.target).emit('answer', payload);
        });

        socket.on('ice-candidate', (incoming) => {
            io.to(incoming.target).emit('ice-candidate', incoming.candidate);
        });
    });
});

const PORT = process.env.PORT || 3000;
http.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));