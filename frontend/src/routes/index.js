import config from "../config";
import ClientLayout from "../layouts/Client/ClientLayout";
import AdminLayout from '../layouts/Admin/AdminLayout'
import DashboardPage from "../pages/Admin/Dashboard";
import SettingPage from "../pages/Admin/Setting";
import HomePage from "../pages/Client/Home";
import AutoApprovePage from "../pages/Admin/AutoApprove";
import GenreFormPage from "../pages/Admin/Genres/GenreForm";
import GenrePage from "../pages/Admin/Genres";
import VoucherPage from "../pages/Admin/Voucher";
import VoucherFormPage from "../pages/Admin/Voucher/VoucherForm";
import BlogsPage from "../pages/Admin/Blogs";

const publicRoutes = [
    {
        path: config.routes.web.home,
        component: HomePage,
        layout: ClientLayout,
        roles: ['user'],
        private: false,
    }
]

const privateRoutes = [
    {
        path: config.routes.admin.dashboard,
        component: DashboardPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.setting,
        component: SettingPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.auto,
        component: AutoApprovePage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },{
        path: config.routes.admin.genres,
        component: GenrePage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.genres + '/create',
        component: GenreFormPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },    
    {
        path: config.routes.admin.genres + '/update/:id',
        component: GenreFormPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },{
        path: config.routes.admin.voucher,
        component: VoucherPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.voucher + '/create',
        component: VoucherFormPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.voucher + '/update/:id',
        component: VoucherFormPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },
    {
        path: config.routes.admin.blogs,
        component: BlogsPage,
        layout: AdminLayout,
        roles: ['admin'],
        private: true,
    },

]

const routes = [...publicRoutes, ...privateRoutes];
export default routes;