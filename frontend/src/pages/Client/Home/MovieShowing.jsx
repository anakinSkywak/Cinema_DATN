import ImageMovie from "../../../assets/image/joker.webp";
import { useGetMovies } from "../../../hooks/api/useMovieApi";
import Voucher from "./Voucher";

const MovieShowing = () => {
    const { data, isLoading } = useGetMovies();
    return (
        <>
            <div className="w-3/4 mb">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-xl font-bold flex items-center">
                        <span className="text-red-500 mr-2">
                            ●
                        </span>
                        Phim đang chiếu
                    </h2>
                    <a className="text-blue-400" href="#">
                        Xem tất cả
                    </a>
                </div>

                <div className="flex space-x-4 mt-6 mb-5">
                    {
                        data?.data?.map((item) => {
                            const allMovieGenres = item.movie_genres.map((genre) => genre.ten_loai_phim).join(', ');
                            return (
                                <div className="w-1/4">
                                    <img
                                        alt="Movie poster of Joker: Folie à Deux"
                                        className="rounded-2xl hover-zoom mb-2 hover-zoom"
                                        style={{ height: 350, width: '100%' }}
                                        src={ImageMovie}
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
                            )
                        }
                        )}
                </div>

                <hr />
            </div>

            <Voucher />
        </>
    );
}

export default MovieShowing;