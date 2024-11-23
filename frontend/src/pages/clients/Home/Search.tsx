import React, { useState } from 'react';
import { FaSearch, FaFilm, FaUser, FaVideo, FaFilter, FaTimes } from 'react-icons/fa';
import { useNavigate } from 'react-router-dom';

const Search = () => {
    const [searchTerm, setSearchTerm] = useState('');
    const navigate = useNavigate();

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        navigate(`/search?query=${encodeURIComponent(searchTerm)}`);
    };

    return (
        <div className="w-full mx-auto mb-8 p-6 bg-gradient-to-br from-gray-900 via-gray-800 to-blue-900 rounded-3xl shadow-2xl">
            <form onSubmit={handleSubmit} className="relative mb-6">
                <input
                    type="text"
                    placeholder="Tìm kiếm tên phim..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full py-4 px-6 pr-14 rounded-full bg-gray-700 focus:bg-gray-600 border-2 border-gray-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/50 transition duration-300 ease-in-out text-gray-200 text-lg placeholder-gray-400 shadow-inner"
                />
                <button
                    type="submit"
                    className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition duration-300 ease-in-out group"
                >
                    <FaSearch className="text-xl group-hover:scale-110 transition-transform duration-300" />
                </button>
            </form>
        </div>
    );
};

export default Search;