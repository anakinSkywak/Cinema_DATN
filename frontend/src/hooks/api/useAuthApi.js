import apiRoutes from '../../config/apiRoutes';
import { usePost, usePut, usePutWithoutId } from '../../utils/reactQuery';

export const useLogin = (updater) => {
    return usePost(apiRoutes.common.auth.login, updater);
}
export const useRegister = (updater) => {
    return usePost(apiRoutes.common.auth.register, updater);
}

export const useConfirmEmail = (updater) => {
    return usePost(apiRoutes.common.auth.confirmed, updater);
}
