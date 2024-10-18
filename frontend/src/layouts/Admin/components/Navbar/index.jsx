import './navbar.scss';
import config from '../../../../config';
import React, { useState } from 'react';
import {
    IconHome2,
    IconFingerprint,
    IconBrandCashapp,
    IconReportAnalytics,
    IconSettings,
    IconAutomaticGearbox,
    IconInfoCircle,
    Icon24Hours,
    IconCategory,
    IconConeOff,
    IconBrandBlogger,
    IconPhoneCall,
    IconMovie
} from '@tabler/icons-react';
import { Menu } from 'antd';
import { useLocation, useNavigate } from 'react-router-dom';

const items = [
    getItem('Trang chủ', config.routes.admin.dashboard, <IconHome2 />),
    getItem('Người dùng', config.routes.admin.account, <IconFingerprint />),
    getItem('Phim', config.routes.admin.movies, <IconMovie />),
    getItem('Thể Loại', config.routes.admin.genres, <IconCategory />),
    getItem('Khuyến Mãi', config.routes.admin.voucher, <IconConeOff />),
    getItem('Báo cáo', config.routes.admin.report, <IconReportAnalytics />),
    getItem('Tin tức', config.routes.admin.blogs, <IconBrandBlogger />),
    getItem('Liên hệ', config.routes.admin.trade, <IconPhoneCall />),
    getItem('Thông tin', config.routes.admin.info, <IconInfoCircle />),
    getItem('Cài đặt hệ thống', config.routes.admin.setting, <IconSettings />),
    getItem('Duyệt tự động', config.routes.admin.auto, <IconAutomaticGearbox />),
];

const rootSubmenuKeys = ['user', 'product', 'order'];

function getItem(label, key, icon, children, type) {
    return {
        key,
        icon,
        children,
        label,
        type,
    };
}

function Navbar() {
    const navigate = useNavigate();
    let routeKey = useLocation().pathname;

    const [openKeys, setOpenKeys] = useState(['home']);
    const onOpenChange = (keys) => {
        const latestOpenKey = keys.find((key) => openKeys.indexOf(key) === -1);
        if (latestOpenKey && rootSubmenuKeys.indexOf(latestOpenKey) === -1) {
            setOpenKeys(keys);
        } else {
            setOpenKeys(latestOpenKey ? [latestOpenKey] : []);
        }
    };
    const onClick = (e) => {
        navigate(e.key);
    };

    return (
        <div className="navbar-admin-container w-[250px]">
            <Menu
                className='bg-[#0D111AFF]'
                mode="inline"
                onClick={onClick}
                defaultSelectedKeys={[routeKey]}
                openKeys={openKeys}
                onOpenChange={onOpenChange}
                items={items}
            />
        </div>
    );
}

export default Navbar;
