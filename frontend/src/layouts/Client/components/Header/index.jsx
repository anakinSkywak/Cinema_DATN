import Logo from "../../../../assets/image/logo.webp";
import LoginModal from "../../../../pages/Client/Login";
import RegisterModal from "../../../../pages/Client/Register";
import { Link, NavLink } from 'react-router-dom';
import config from '../../../../config';
import { useEffect, useState } from 'react';
import { Dropdown, Menu } from 'antd';
import { useGetMe } from '../../../../hooks/api/useUserApi';
import { clearToken, isTokenStoraged, getRoles } from '../../../../utils/storage';
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

function Header() {
    const [showRegisterModal, setShowRegisterModal] = useState(false);
    const [showLoginModal, setShowLoginModal] = useState(false);
    const { data, isLoading, refetch } = useGetMe();
    const [user, setUser] = useState(null);
    let roles = getRoles();
    console.log("ROLE", roles)
    useEffect(() => {
        if (isLoading || !data) return;
        localStorage.setItem('userId', data?.id);
        setUser(data);
    }, [isLoading, data]);

    const onLogout = () => {
        clearToken();
        setUser(null);
    };

    if (isTokenStoraged()) {
        refetch();
    }

    const openRegisterModal = () => {
        setShowRegisterModal(true);
        setShowLoginModal(false);
    };

    const closeRegisterModal = () => {
        setShowRegisterModal(false);
    };

    const openLoginModal = () => {
        console.log("Login Modal")
        setShowLoginModal(true);
        setShowRegisterModal(false)
    };

    const closeLoginModal = () => {
        setShowLoginModal(false);
    };

    const menu = (
        <Menu className="">
            <Menu.Item key="1">
                <a href="profile.html">Thông tin cá nhân</a>
            </Menu.Item>
            <Menu.Item key="2">
                <a href="#" onClick={onLogout}>Đăng xuất</a>
            </Menu.Item>
        </Menu>
    );

    return (
        <div>
            <header className="header bg-primary/30 flex justify-between items-center p-4 transition duration-500">
                <div className="flex items-center" style={{ marginLeft: 100 }}>
                    <img alt="NCC logo" className="mr-2" height="50" src={Logo} width="50" />
                    <nav className="flex space-x-4">
                        <nav className="flex space-x-4">
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.home}
                            >
                                Trang chủ
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.lichChieu}
                            >
                                Lịch Chiếu
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.tinTuc}
                            >
                                Tin Tức
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.khuyenMai}
                            >
                                Khuyến Mãi
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.giaVe}
                            >
                                Giá Vé
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.lienHoanPhim}
                            >
                                Liên Hoan Phim
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link' : 'pr-5 hover-text inactive')}
                                to={config.routes.web.gioiThieu}
                            >
                                Giới Thiệu
                            </NavLink>
                        </nav>
                    </nav>
                </div>
                {isTokenStoraged() ? (
                    <>
                        <div className="relative flex items-center space-x-2" style={{ marginRight: 100 }}>
                            {roles.includes('admin') ? (<>
                            <NavLink className="mr-4 bg-red-600 px-2 py-2 rounded-full hover-zoom" to={config.routes.admin.dashboard}>Quản trị viên</NavLink>
                            </>) : (<></>)}
                            <FontAwesomeIcon icon="fa-solid fa-user" />
                            <Dropdown overlay={menu} trigger={['click']}>
                                <span id="user-name" className="user-name cursor-pointer">
                                    Bằng Đỗ Trọng ▼
                                </span>
                            </Dropdown>
                        </div> 
                    </>
                )
                    :
                    (<>
                        <div className="flex items-center" style={{ marginRight: 100 }}>
                            <button
                                className="border border-white text-white py-2 px-4 pr-6 pl-6 rounded-full mr-6 hover-zoom"
                                onClick={openRegisterModal}
                            >
                                Đăng Kí
                            </button>
                            <button className="bg-red-500 text-white py-2 px-4 rounded-full hover-zoom"
                                onClick={openLoginModal}
                            >
                                Đăng nhập
                            </button>
                        </div>
                    </>)}

            </header>


            {showLoginModal && (
                <LoginModal
                    closeModal={closeLoginModal}
                    openRegisterModal={() => { setShowRegisterModal(true), setShowLoginModal(false) }}
                />
            )}
            {showRegisterModal && (
                <RegisterModal
                    closeModal={closeRegisterModal}
                    openLoginModal={() => { setShowLoginModal(true); setShowRegisterModal(false) }}
                />
            )}
        </div>
    );
}

export default Header;