import { jwtDecode } from "jwt-decode";


export const clearToken = () => {
    localStorage.removeItem('token');
};

export const isTokenStoraged = () => {
    return !!localStorage.getItem('token');
};

export const saveToken = (token) => {
    localStorage.setItem('token', JSON.stringify(token));
};

export const getRoles = () => {
    let authInfo = JSON.parse(localStorage.getItem('token'));
    if (!authInfo) return;

    let jwtDecodeObj = jwtDecode(authInfo.token);
    let role = Object.keys(jwtDecodeObj).find((val) => val.includes('role'));
    return jwtDecodeObj[role];
};
