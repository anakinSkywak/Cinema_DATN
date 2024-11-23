import { NavLink } from "react-router-dom";
import ImageMovie from "../../../assets/image/joker.webp";
import config from "../../../config";

const MovieCommingSoon = () => {
    return (
        <>
            <div className="w-3/4 mb">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-xl font-bold flex items-center">
                        <span className="text-red-500 mr-2">
                            ●
                        </span>
                        Phim sắp chiếu
                    </h2>
                    <a className="text-blue-400" href="#">
                        Xem tất cả
                    </a>
                </div>
                <div className="flex space-x-4 mt-6 mb-5">
                <NavLink className="w-1/4" to={config.routes.web.phim + `/2`}>
                    <div>
                        <img
                            alt="Movie poster of Joker: Folie à Deux"
                            className="rounded-2xl hover-zoom mb-2 hover-zoom"
                            style={{ height: 350, width: '100%' }}
                            src={ImageMovie}
                        />
                        <p className="text-gray-400">
                            Kinh dị, Tâm lý, tình cảm, Nhạc kịch
                        </p>
                        <p className="text-gray-400">
                            04/10/2024
                        </p>
                        <p className="font-bold">
                            JOKER: FOLIE À DEUX ĐIÊN CÓ ĐÔI-T18
                        </p>
                    </div>
                    </NavLink>
                    <div className="w-1/4">
                        <img
                            alt="Movie poster of Hẹn Hò Với Sát Nhân"
                            className="rounded-2xl hover-zoom mb-2 hover-zoom"
                            style={{ height: 350, width: '100%' }}
                            src={ImageMovie}
                        />
                        <p className="text-gray-400">
                            04/10/2024
                        </p>
                        <p className="font-bold">
                            HẸN HÒ VỚI SÁT NHÂN-T16
                        </p>
                    </div>
                    <div className="w-1/4">
                        <img
                            alt="Movie poster of Mộ Đom Đóm"
                            className="rounded-2xl hover-zoom mb-2 hover-zoom"
                            style={{ height: 350, width: '100%' }}
                            src={ImageMovie}
                        />
                        <p className="text-gray-400">
                            04/10/2024
                        </p>
                        <p className="font-bold">
                            MỘ ĐOM ĐÓM-K
                        </p>
                    </div>
                    <div className="w-1/4">
                        <img
                            alt="Movie poster of Kumanthong: Chiêu Hồn Vong Nhi"
                            className="rounded-2xl hover-zoom mb-2 hover-zoom"
                            style={{ height: 350, width: '100%' }}
                            src={ImageMovie}
                        />
                        <p className="text-gray-400">
                            04/10/2024
                        </p>
                        <p className="font-bold">
                            KUMANTHONG: CHIÊU HỒN VONG NHI-T18
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}

export default MovieCommingSoon;