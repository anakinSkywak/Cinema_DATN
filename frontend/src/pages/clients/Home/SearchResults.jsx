import { NavLink, useLocation } from "react-router-dom";
import Voucher from "./Voucher";
import { Empty } from "antd";
import { useGetMovieFilterByKeyword } from "../../../hooks/api/useMovieApi";
import { useState } from "react";
import config from "../../../config";
import Search from "./Search";

const SearchResults = () => {
    const location = useLocation();
    const queryParams = new URLSearchParams(location.search);
    const searchTerm = queryParams.get('query');
    const [param, setParam] = useState({
        keyword: searchTerm
    });
    const { data, isLoading } = useGetMovieFilterByKeyword(param);

    console.log(data)


    const groupMovies = (movies, groupSize) => {
        const groups = [];
        for (let i = 0; i < movies.length; i += groupSize) {
            groups.push(movies.slice(i, i + groupSize));
        }
        return groups;
    };

    const movieGroups = groupMovies(data?.data || [], 4)

    return (

        <div >
            <div className="mt-24 px-10">
                <Search />
            </div>

            <div className="flex mb-6 mt-20 px-10">
                <div className="w-3/4 mb">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-xl font-bold flex items-center">
                            <span className="text-red-500 mr-2">
                                ●
                            </span>
                            Kết quả tìm kiếm cho: {searchTerm}
                        </h2>
                    </div>
                    {movieGroups.length == 0 &&
                        <Empty className="text-white" />
                    }

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
                                                src={`http://localhost:8000${item.anh_phim}` || ImageMovie}
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
        </div>
    );
};

export default SearchResults;