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


export const useGetMovies = () => {
    console.log("GET MOVIES")
    return useFetch({ url: apiRoutes.web.movie, key: 'getListMovies' });
};