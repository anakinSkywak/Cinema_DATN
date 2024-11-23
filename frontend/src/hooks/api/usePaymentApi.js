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


export const usePaymentMethod = (showtimeId, paymentMethod) => {
    return usePost({ url: apiRoutes.web.payment + "/" + showtimeId + "/" + paymentMethod, key: 'getFoods' });
};