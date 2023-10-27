const http = require('http').createServer();
const io = require('socket.io')(http, {
    cors: {
        origin: "http://89.108.78.71:8080",
        methods: ["GET", "POST"]
    }
});

io.on('connection', (socket) => {
    console.log('a user connected');
    // тут можно обрабатывать различные события, которые будут приходить от клиентов
    // например, socket.on('message', function(data){ ... })

    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

http.listen(3000, () => {
    console.log('listening on *:3000');
});