import { Navigate, useNavigate } from 'react-router-dom';
import { isTokenStoraged } from '../../utils/storage';
import config from '../../config';
import { useGetMe } from '../../hooks/api/useUserApi';


function PrivateRoute({ children, roles }) {
    // const { data, isLoading } = useGetMe();
    console.log("Private Route", roles)
    if (!isTokenStoraged()) {
        return <Navigate to={config.routes.web.login} replace />;
    }

    // if (isLoading) return <div>Loading...</div>;

    // if (!data) {
    //     return <Navigate to={config.routes.web.login} replace />;
    // }
    // if (roles.includes('admin') && !roles.includes('user')) {
    //     const res = data?.roles?.some((role) => role?.name?.includes('admin'));
    //     if (!res) {
    //         return <Navigate to={config.routes.admin.forbidden} replace />;
    //     }
    // }

    return children;
}

export default PrivateRoute;
