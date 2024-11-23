import apiRoutes from '../../config/apiRoutes';
import {
    useDelete,
    useFetch,
    usePost,
    usePut,
} from '../../utils/reactQuery';

// Lấy tất cả ghế
export const useGetSeats = () => {
    return useFetch({ url: apiRoutes.admin.seat, key: 'getListSeats' });
};

// Lấy ghế theo phòng
export const useGetSeatsByRoom = (roomId) => {
    return useFetch({ url: `${apiRoutes.admin.storeSeat}?roomId=${roomId}`, key: 'getSeatsByRoom' });
};

// Thêm ghế
export const useCreateSeat = (updater) => {
    return usePost(apiRoutes.admin.storeSeat, updater);
};

// Hiển thị ghế theo ID
export const useGetSeatById = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showSeat}/${id}`, key: 'getSeatById' });
};

// Cập nhật ghế theo ID
export const useUpdateSeat = (updater) => {
    return usePut(apiRoutes.admin.updateSeat, updater);
};

// Xóa ghế theo ID
export const useDeleteSeat = (updater) => {
    return useDelete(apiRoutes.admin.deleteSeat, updater);
};
export const useGetAddSeat = () => {
    return useFetch({ url: apiRoutes.admin.addSeat, key: 'getAddSeat' }); // xuat ghế theo phòng
};
