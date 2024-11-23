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


export const useGetFoods = () => {
    return useFetch({ url: apiRoutes.web.foods, key: 'getFoods' });
};

// Hàm để tạo một món ăn mới
export const useCreateFood = (updater) => {
    return usePost(apiRoutes.admin.storeFood, updater);
};

// Hàm để lấy một món ăn theo id
export const useShowFood = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showFood}/${id}`, key: 'showFood' });
};

// Hàm để cập nhật một món ăn hiện có
export const useUpdateFood = (updater) => {
    return usePut(apiRoutes.admin.updateFood, updater);
};

// Hàm để xóa một món ăn sử dụng đường dẫn admin
export const useDeleteFood = (updater) => {
    return useDelete(apiRoutes.admin.deleteFood, updater);
};
