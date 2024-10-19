const apiRoutes = {
    common: {
        auth: {
            login: '/login',
            register: '/registers',
            changePassword: '/auth/change-password',
            confirmed: '/auth/confirmed',
        },
        user: {
            me: '/user/me'
        },
    },
    admin: {
        gerne: '/moviegenres',
        voucher: '/vouchers'
    },
    web: {
        user: '/auth/upload-profile',
        post: '/post',
        user_message: '/user/message',
        user_message_chat: '/user/message-chat',
        transaction: '/transaction',
        movie:'/movies',
    },
};

export default apiRoutes;
