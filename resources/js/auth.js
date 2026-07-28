export function saveAuth(token, user) {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('auth_user', JSON.stringify(user));
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    delete window.axios.defaults.headers.common['Authorization'];
}

export function getUser() {
    const raw = localStorage.getItem('auth_user');
    return raw ? JSON.parse(raw) : null;
}
