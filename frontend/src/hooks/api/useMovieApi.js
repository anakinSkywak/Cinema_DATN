import apiRoutes from '../../config/apiRoutes';
import {
    useDelete,
    useFetch,
    usePost,
    usePut,
} from '../../utils/reactQuery';

// Tạo một phim mới
export const useCreateMovie = (updater) => {
    return usePost(apiRoutes.admin.storeMovie, updater);
};

// Lấy danh sách tất cả phim
export const useGetMovies = () => {
    console.log("GET MOVIES");
    return useFetch({ url: apiRoutes.admin.movie, key: 'getListMovies' });
};

export const useGetMovieById = (id) => {
    console.log("GET MOVIES ID")
    return useFetch({ url: apiRoutes.web.showMovie + '/' + id, key: 'getMovieById' });
}

export const useGetMovieFilterById = (id) => {
    return useFetch({ url: apiRoutes.web.movieFilter + '/' + id, key: 'getMovieFilterById' });
}

export const useGetMovieFilterByKeyword = (searchParam) => {
    return useFetch({ url: apiRoutes.web.movieFilterKeyword , key: 'getMovieFilterKeyword' , searchParam});
}

export const useGetMovieDetailById = (id) => {
    return useFetch({ url: apiRoutes.web.movieDetail + '/' + id, key: 'getMovieDetailById' });
}


export const useGetShowtimeById = (movieId, date) => {
    return useFetch({ url: apiRoutes.web.movieDetail + '/' + movieId + '/showtime-date/' + date, key: 'getShowtimeById' });
}


export const useGetShowSeatById = (movieId, selectedDate, selectedTime) => {
    return useFetch({ url: apiRoutes.web.movieDetail + '/' + movieId + '/showtime-date/' + selectedDate + '/' + selectedTime, key: 'getShowSeatById' });
}

// Lấy thông tin một phim theo ID
export const useGetMovie = (id) => {
    return useFetch({ url: `${apiRoutes.admin.showMovie}/${id}`, key: 'showMovieById' });
};

// Cập nhật thông tin một phim theo ID
export const useUpdateMovie = (id) => {
    return usePut(`${apiRoutes.admin.updateMovie}/${id}`);
};

// Xóa một phim
export const useDeleteMovie = (updater) => {
    return useDelete(apiRoutes.admin.movie, updater);
};
// Lấy thông tin chi tiết một phim theo ID
export const useGetMovieDetail = (id) => {
    return useFetch({ url: `${apiRoutes.admin.movieDetail}/${id}`, key: 'getMovieDetail' });
};

