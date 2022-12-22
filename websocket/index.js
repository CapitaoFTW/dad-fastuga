const httpServer = require('http').createServer()

const io = require("socket.io")(httpServer, {
    cors: {
        // The origin is the same as the Vue app domain. Change if necessary
        origin: "http://localhost:5173",
        methods: ["GET", "POST"],
        credentials: true
    }
})

httpServer.listen(8080, () => {
    console.log('listening on *:8080')
})

io.on('connection', (socket) => {
    console.log(`client ${socket.id} has connected`)

    socket.on('loggedIn', function (user) {
        socket.join(user.id)
        if (user.type == 'EM') {
            socket.join('manager')
        }

        if (user.type == 'EC') {
            socket.join('chef')
        }

        if (user.type == 'ED') {
            socket.join('delivery')
        }

        if (user.type == 'C') {
            socket.join('customer')
        }
    })

    socket.on('loggedOut', function (user) {
        socket.leave(user.id)
        if (user.type == 'EM') {
            socket.leave('manager')
        }

        if (user.type == 'EC') {
            socket.leave('chef')
        }

        if (user.type == 'ED') {
            socket.leave('delivery')
        }

        if (user.type == 'C') {
            socket.leave('customer')
        }
    })

    /* USERS */

    socket.on('newUser', function (data) {
        socket.in('manager').except(data.user.id).emit('newUser', data)
    })

    socket.on('updateUser', function (data) {
        socket.in('manager').except(data.user.id).emit('updateUser', data)
        socket.in(data.user.id).emit('updateUser', data)
    })

    socket.on('deleteUser', function (data) {
        socket.in('manager').except(data.user.id).emit('deleteUser', data)
        socket.in(data.user.id).emit('deleteUser', data)
    })

    socket.on('blockUser', function (data) {
        socket.in('manager').except(data.user.id).emit('blockUser', data)
        socket.in(data.user.id).emit('blockUser', data)
    })

    /* ORDERS */

    socket.on('newOrder', (order) => {
        socket.broadcast.emit('newOrder', order) // broadcast para atualizar as orders mas não aparecer a notificação no ecrã para alguns (verificações no client)
    })

    socket.on('readyOrder', (order) => {
        socket.broadcast.emit('readyOrder', order) // broadcast para atualizar as orders mas não aparecer a notificação no ecrã para alguns (verificações no client)
    })

    socket.on('deliveredOrder', (order) => {
        socket.broadcast.emit('deliveredOrder', order) // broadcast para atualizar as orders mas não aparecer a notificação no ecrã para alguns (verificações no client)
    })

    socket.on('cancelledOrder', (data) => {
        socket.broadcast.emit('cancelledOrder', data) // broadcast para atualizar as orders mas não aparecer a notificação no ecrã para alguns (verificações no client)
    })

    /* ORDER ITEMS */

    socket.on('newHotDishes', (data) => {
        socket.in('chef').emit('newHotDishes', data)
        socket.in('delivery').emit('newHotDishes', data) // delivery para atualizar as orders mas não aparecer a notificação no ecrã (verificações no client)
        socket.in('manager').emit('newHotDishes', data) // manager para atualizar as orders mas não aparecer a notificação no ecrã (verificações no client)
    })

    socket.on('preparingOrderItem', (data) => {
        socket.in('chef').except(data.order_item.preparation_by).emit('preparingOrderItem', data)
        socket.in('manager').emit('preparingOrderItem', data) // manager para atualizar as orders mas não aparecer a notificação no ecrã (verificações no client)
    })

    socket.on('readyOrderItem', (data) => {
        socket.broadcast.emit('readyOrderItem', data) // broadcast para atualizar as orders todas mas não aparecer a notificação no ecrã para alguns (verificações no client)
    })
})