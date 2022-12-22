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

        /*if (user.type != 'EM' && user.type != 'EC' && user.type != 'ED' && user.type != 'C') {
            socket.join('anonymous')
        }*/
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
        socket.broadcast.emit('newOrder', order)
    })

    socket.on('readyOrder', (order) => {
        socket.in(order.customer.user_id).emit('readyOrder', order)
    })

    socket.on('deliveredOrder', (order) => {
        socket.broadcast.emit('deliveredOrder', order)
    })

    socket.on('cancelledOrder', (order, manager) => {
        socket.broadcast.emit('cancelledOrder', order, manager)
    })

    /* ORDER ITEMS */

    socket.on('newHotDishes', (number) => {
        socket.broadcast.emit('newHotDishes', number)
        //socket.in('chef').emit('newHotDishes', data)
    })

    socket.on('preparingOrderItem', (order_item) => {
        socket.in('chef').except(order_item.preparation_by).emit('preparingOrderItem', order_item)
        socket.in(order_item.preparation_by).emit('preparingOrderItem', order_item)
    })

    socket.on('readyOrderItem', (order_item) => {
        socket.in('delivery').emit('readyOrderItem', order_item)
    })
})