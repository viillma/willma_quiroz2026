export function login() {
    return {
        url: '/login',
        onlyGuest: true,
    }
}

export function store() {
    return {
        url: '/login',
        method: 'POST',
    }
}

export function email() {
    return {
        url: '/forgot-password',
        method: 'POST',
    }
}

export function logout() {
    return {
        url: '/logout',
        method: 'POST',
    }
}

export function dashboard() {
    return {
        url: '/dashboard',
    }
}

export function home() {
    return {
        url: '/',
    }
}