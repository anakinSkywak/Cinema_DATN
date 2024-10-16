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

export const useCreateGerne = (updater) => {
    return usePost(apiRoutes.admin.gerne, updater);
};

export const useUpdateGerne = (updater) => {
    return usePut(apiRoutes.admin.gerne, updater);
};

export const useGetGernes = () => {
    return useFetch({ url: apiRoutes.admin.gerne, key: 'getListGerne' });
};

export const useGetGerne = (id) => {
    return useFetch({ url: apiRoutes.admin.gerne + '/' + id, key: 'getGerneById' });
}

export const useDeleteGerne = (updater) => {
    return useDelete(apiRoutes.admin.gerne, updater);
}