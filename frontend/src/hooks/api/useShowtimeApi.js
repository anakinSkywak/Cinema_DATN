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


export const useGetShowtimes = () => {
    return useFetch({ url: apiRoutes.web.schedulMovies, key: 'getShowtimes' });
};

export const useShowtimes = () => {
    return useFetch({ url: apiRoutes.admin.showtimes, key: 'getShowtimes' });
};

export const useAddShowtime = () => {
    return useFetch({ url: apiRoutes.admin.addShowtime, key: 'addShowtime' });
};

export const usestoreShowtime = (updater) => {
    return usePost(apiRoutes.admin.storeShowtime, updater);
};

export const useshowShowtime = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showShowtime}/${id}`, key: 'showShowtime' });
};

export const useUpdateShowtime = (updater) => {
    return usePut(apiRoutes.admin.updateShowtime, updater);
};

export const useDeleteShowtime = (updater) => {
    return useDelete(apiRoutes.admin.deleteShowtime, updater);
};

export const useeditShowtime = (id) => {
    return useFetch({ url: `${apiRoutes.admin.editShowtime}/${id}`, key: 'editShowtime' });
};