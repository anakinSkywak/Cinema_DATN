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
    return usePost(apiRoutes.admin.storeMoviegenre, updater);
};

export const useUpdateGerne = (updater) => {
    return usePut(apiRoutes.admin.updateMoviegenre, updater);
};

export const useGetGernes = () => {
    return useFetch({ url: apiRoutes.admin.gerne, key: 'getListGerne' });
};

export const useGetGerne = (id) => {
    return useFetch({ url: apiRoutes.admin.showMoviegenre + '/' + id, key: 'getGerneById' });
}

export const useDeleteGerne = (updater) => {
    return useDelete(apiRoutes.admin.deleteMoviegerne, updater);
}