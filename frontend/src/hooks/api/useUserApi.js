import apiRoutes from '../../config/apiRoutes';
import {
    useDelete,
    useDeleteList,
    useFetch,
    usePost,
    usePut,
    usePostQuery,
    usePutForm,
    usePutFormWithoutId,
    usePutWithoutId,
    usePostForm,
} from '../../utils/reactQuery';

export const useGetMe = () => {
    return useFetch({ url: apiRoutes.common.user.me, key: 'getMe' });
};

export const useUpdateUser = (updater) => {
    return usePostForm(apiRoutes.web.user, updater);
}

export const useChangePassword = (updater) => {
    return usePost(apiRoutes.common.auth.changePassword, updater);
}