import { NavLink, useParams } from "react-router-dom";
import { useGetMovieFilterById, useGetMovies } from "../../../hooks/api/useMovieApi";
import Voucher from "../Home/Voucher";
import config from "../../../config";

const MovieGernes = () => {
    const { id } = useParams();
    const { data, isLoading } = id ? useGetMovieFilterById(id) : useGetMovies();
    const groupMovies = (movies, groupSize) => {
        const groups = [];
        for (let i = 0; i < movies.length; i += groupSize) {
            groups.push(movies.slice(i, i + groupSize));
        }
        return groups;
    };

    const movieGroups = groupMovies(data?.data || [], 4)

    return (
        <>
            <div className="flex mb-6 mt-20 px-10">
                <div className="w-3/4 mb">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-xl font-bold flex items-center">
                            <span className="text-red-500 mr-2">
                                ●
                            </span>
                            Danh sách phim
                        </h2>
                    </div>

                    {movieGroups.map((group, index) => (
                        <div className="flex space-x-4 mt-6 mb-5">
                            {group.map((item) => {
                                const allMovieGenres = item.movie_genres.map((genre) => genre.ten_loai_phim).join(', ');
                                return (
                                    <NavLink className="w-1/4" to={config.routes.web.phim + `/` + item.id}>
                                        <div key={item.id}>
                                            <img
                                                alt={`Movie poster of ${item.ten_phim}`}
                                                className="rounded-2xl hover-zoom mb-2"
                                                style={{ height: 350, width: '100%' }}
                                                src={`http://localhost:8000${item.anh_phim}` || ImageMovie} // Giả sử `item.image` chứa đường dẫn hình ảnh
                                            />
                                            <p className="text-gray-400">
                                                {allMovieGenres}
                                            </p>
                                            <p className="text-gray-400">
                                                04/10/2024
                                            </p>
                                            <p className="font-bold">
                                                {item.ten_phim}
                                            </p>
                                        </div>
                                    </NavLink>
                                );
                            })}
                        </div>
                    ))}
                </div>

                <Voucher />
            </div>
        </>
    );
}

export default MovieGernes;