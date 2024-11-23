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

export const getInfoAuth = () => {
    let authInfo = JSON.parse(localStorage.getItem('token'));
    if (!authInfo) return;

    let info = authInfo.auth;
    return info;
};

export const getTokenOfUser = () => {
    let authInfo = JSON.parse(localStorage.getItem('token'));
    if (!authInfo) return;

    let jwtDecodeObj = authInfo["access-token"];
    return jwtDecodeObj;
};

export const getRoles = () => {
    let token = JSON.parse(localStorage.getItem('token'));
    if (!token) return;
    let jwtDecodeObj = jwtDecode(token['access-token']);
    let role = Object.keys(jwtDecodeObj).find((val) => val.includes('vai_tro'));
    return jwtDecodeObj[role];
};