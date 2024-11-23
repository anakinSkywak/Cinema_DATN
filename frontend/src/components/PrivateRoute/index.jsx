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

    return children;
}

export default PrivateRoute;
