import { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import ModalTrailerPage from "./ModalTrailer";
import Box from "./Box";
import { useGetMovieById } from "../../../hooks/api/useMovieApi";
import { Spin } from "antd";

const MovieDetailsPage = () => {
    const [showTrailerModal, setShowTrailerModal] = useState(false);
    const { id } = useParams();
    const { isLoading: isLoadingMovie, data: movie } = id
        ? useGetMovieById(id)
        : { isLoading: null, data: null };
    console.log("Movie", movie)
    
    useEffect(() => {
        if(!movie) return;
    }, [movie]);
    const allMovieGenres = movie?.data?.movie_genres.map((genre) => genre.ten_loai_phim).join(', ');
    const closeTrailerModal = () => {
        setShowTrailerModal(false);
    };

    const openTrailerModal = () => {
        setShowTrailerModal(true);
    };

    if (!movie) {
        return <Spin size="large" className='flex items-center justify-center mt-20'></Spin>;
    }

    return (
        <>
            <main className="main-content py-16 px-8 mt-20" style={{ backgroundImage: `url(http://localhost:8000${movie.data.anh_phim})` }}>
                <div className="flex px-64" >
                    <img alt="Movie Poster" className="mr-8 rounded-lg" height="450" src={`http://localhost:8000${movie.data.anh_phim}`} width="300" />
                    <div>
                        <h1 className="text-4xl font-bold mb-4">
                            {movie.data.ten_phim}
                        </h1>
                        <p className="mb-2">
                            {`${allMovieGenres} | Nước Ngoài | ${movie.data.thoi_gian_phim} phút | Đạo diễn: ${movie.data.dao_dien}`}
                        </p>
                        <p className="mb-2">
                            Diễn viên: {movie.data.dien_vien}
                        </p>
                        <p className="mb-2">
                            Gía vé: {Number(movie.data.gia_ve).toLocaleString()} VNĐ
                        </p>
                        <p className="mb-4">
                            {movie.data.noi_dung}
                        </p>
                        <p className="mb-4 text-red-500">
                            Kiểm duyệt: T18: Phim được phổ biến đến khán giả từ đủ 18 tuổi trở lên.
                        </p>
                        <div className="flex space-x-4">
                            <button className="bg-gray-800 text-white py-2 px-4 rounded-full hover-zoom">
                                Chi tiết nội dung
                            </button>
                            <button className="btn-primary text-yellow-400 py-2 px-4 rounded-full hover-zoom" id="trailerBtn" onClick={openTrailerModal}>
                                Xem Trailer
                            </button>
                        </div>
                    </div>
                </div>
            </main>
            <Box detail={movie?.data} />
            {showTrailerModal && (
                <ModalTrailerPage
                    closeModal={closeTrailerModal}
                    trailerUrl={movie.data.trailer}
                />
            )}
        </>
    );
}

export default MovieDetailsPage;