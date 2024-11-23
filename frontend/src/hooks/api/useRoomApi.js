import apiRoutes from '../../config/apiRoutes';
import {
    useDelete,
    useFetch,
    usePost,
    usePut,
} from '../../utils/reactQuery'; // Adjust this path if necessary

// Tạo mới phòng chiếu
export const useCreateRoom = (updater) => {
    return usePost(apiRoutes.admin.storeRoom, updater);
};

// Lấy tất cả phòng chiếu
export const useGetRooms = () => {
    return useFetch({ url: apiRoutes.admin.room, key: 'getListRooms' });
};

// Lấy tất cả phòng chiếu và ghế
export const useSeatAllRoom = (id) => {
    const { data } = useFetch({ url: `${apiRoutes.admin.seatAllRoom}/${id}`, key: 'seatAllRoom' });
    
    // Log the total number of seats when data is fetched
    let totalSeats = 0; // Khởi tạo biến đếm ghế
    if (data && Array.isArray(data.data)) {
        totalSeats = data.data.length; // Đếm số ghế
    }

    return { data, totalSeats }; // Trả về dữ liệu và số ghế
};

// Mới: Bật bảo trì ghế
export const useEnableMaintenanceSeat = () => {
    return useMutation((id) => {
        return usePut(`${apiRoutes.admin.baoTriSeat}/${id}`); // Returns a promise
    });
};

// Mới: Tắt bảo trì ghế
export const useDisableMaintenanceSeat = () => {
    return useMutation((id) => {
        return usePut(`${apiRoutes.admin.tatbaoTriSeat}/${id}`); // Returns a promise
    });
};

// Lấy dữ liệu phòng chiếu theo ID
export const useGetRoom = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showRoom}/${id}`, key: 'getRoomById' });
};

// Cập nhật phòng chiếu theo ID
export const useUpdateRoom = (updater) => {
    return usePut(apiRoutes.admin.updateRoom, updater);
};

// Xóa phòng chiếu theo ID
export const useDeleteRoom = (updater) => {
    return useDelete(apiRoutes.admin.deleteRoom, updater);
};