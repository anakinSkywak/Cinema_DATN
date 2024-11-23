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

export const useCreateVoucher = (updater) => {
    return usePost(apiRoutes.admin.storeVoucher, updater); // updated to match new route
};

export const useUpdateVoucher = (updater) => {
    return usePut(apiRoutes.admin.updateVoucher, updater);
};

export const useGetVouchers = () => {
    return useFetch({ url: apiRoutes.admin.voucher, key: 'getListVoucher' }); // unchanged
};

export const useShowVoucher = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showVoucher}/${id}`, key: 'showFood' });
};

// export const useShowVoucher = (id) => {
//     return useFetch({ url: `${apiRoutes.ú.showVoucher}/${id}`, key: 'showFood' });
// };

export const useDeleteVoucher = (id) => {
    return useDelete(apiRoutes.admin.voucher + '/' + id); // updated to match new route
};