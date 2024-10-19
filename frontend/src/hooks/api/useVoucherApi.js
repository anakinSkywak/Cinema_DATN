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
    return usePost(apiRoutes.admin.voucher, updater);
};

export const useUpdateVoucher = (updater) => {
    return usePut(apiRoutes.admin.voucher, updater);
};

export const useGetVouchers = () => {
    return useFetch({ url: apiRoutes.admin.voucher, key: 'getListVoucher' });
};

export const useGetVoucher = (id) => {
    return useFetch({ url: apiRoutes.admin.voucher + '/' + id, key: 'getVoucherById' });
}

export const useDeleteVoucher = (updater) => {
    return useDelete(apiRoutes.admin.voucher, updater);
}