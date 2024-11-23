import Logo from "../../../../assets/image/logo.webp";
import LoginModal from "../../../../pages/Client/Login";
import RegisterModal from "../../../../pages/Client/Register";
import { Link, NavLink } from 'react-router-dom';
import config from '../../../../config';
import { useEffect, useState } from 'react';
import { Dropdown, Menu, Spin } from 'antd';
import { clearToken, isTokenStoraged, getRoles, getInfoAuth } from '../../../../utils/storage';
import { useGetGernes } from "../../../../hooks/api/useGenreApi";

function Header() {
    const [showRegisterModal, setShowRegisterModal] = useState(false);
    const [showLoginModal, setShowLoginModal] = useState(false);
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const [user, setUser] = useState(null);
    const { data, isLoading } = useGetGernes();
    let roles = getRoles();
    let info = getInfoAuth();


    const onLogout = () => {
        clearToken();
        setUser(null);
        window.location.reload();
    };



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
                <NavLink to={config.routes.web.profile}>
                    Thông tin cá nhân
                </NavLink>
            </Menu.Item>
            <Menu.Item key="2">
                <a href="#" onClick={onLogout}>Đăng xuất</a>
            </Menu.Item>
        </Menu>
    );

    const menuNavLink = (
        <Menu>
            {isLoading ? (
                <Menu.Item>
                    <Spin />
                </Menu.Item>
            ) : (
                data && data?.data.map((genre) => (
                    <Menu.Item key={genre?.id}>
                        <NavLink to={`${config.routes.web.theLoai}/${genre?.id}`}>
                            {genre?.ten_loai_phim}
                        </NavLink>
                    </Menu.Item>
                ))
            )}
        </Menu>
    );

    return (
        <div>
            <header className="header bg-primary/30 flex justify-between items-center p-4 transition duration-500">
                <div className="flex items-center" style={{ marginLeft: 100 }}>
                    <img alt="NCC logo" className="mr-2" height="50" src={Logo} width="50" />
                    <nav className="flex space-x-4">
                        <nav className="flex space-x-4 relative">
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.home}
                            >
                                Trang chủ
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.lichChieu}
                            >
                                Lịch Chiếu
                            </NavLink>
                            <Dropdown  className="pr-4 text-gray-400" overlay={menuNavLink} trigger={['hover']}>
                                <NavLink
                                    className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                    to={config.routes.web.theLoai}
                                >
                                    Thể Loại
                                </NavLink>
                            </Dropdown>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.tinTuc}
                            >
                                Tin Tức
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.khuyenMai}
                            >
                                Săn voucher                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.giaVe}
                            >
                                Giá Vé
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
                                to={config.routes.web.lienHoanPhim}
                            >
                                Liên Hoan Phim
                            </NavLink>
                            <NavLink
                                className={({ isActive }) => (isActive ? 'pr-5 hover-text active-link text-white' : 'pr-5 hover-text text-gray-400')}
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
                            {roles === 'admin' ? (<>
                                {/* <NavLink className="mr-4 bg-red-600 px-2 py-2 rounded-full hover-zoom" to={config.routes.admin.dashboard}>Quản trị viên</NavLink> nút quản trị viên  */}
                            </>) : (<></>)}

                            <Dropdown overlay={menu} trigger={['click']}>
                                <span id="user-name" className="user-name cursor-pointer">
                                    {info.ho_ten} ▼
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
                                Đăng kí
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