import React, { useState } from 'react';
import { FaSearch, FaFilm, FaUser, FaVideo, FaFilter, FaTimes } from 'react-icons/fa';

const Search = () => {
    const [searchTerm, setSearchTerm] = useState('');
    const [activeCategory, setActiveCategory] = useState('all');
    const [showFilters, setShowFilters] = useState(false);

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        console.log("Searching for:", searchTerm, "in category:", activeCategory);
    };

    return (
        <div className="w-full mx-auto mb-8 p-6 bg-gradient-to-br from-gray-900 via-gray-800 to-blue-900 rounded-3xl shadow-2xl">
            <form onSubmit={handleSubmit} className="relative mb-6">
                <input
                    type="text"
                    placeholder="Tìm kiếm phim, diễn viên, đạo diễn..."
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
            <div className="flex flex-wrap justify-between items-center mb-6">
                <div className="flex flex-wrap gap-2 mb-2 md:mb-0">
                    {[
                        { key: 'all', icon: FaSearch, label: 'Tất cả' },
                        { key: 'movies', icon: FaFilm, label: 'Phim' },
                        { key: 'actors', icon: FaUser, label: 'Diễn viên' },
                        { key: 'directors', icon: FaVideo, label: 'Đạo diễn' }
                    ].map(({ key, icon: Icon, label }) => (
                        <button
                            key={key}
                            onClick={() => setActiveCategory(key)}
                            className={`flex items-center px-4 py-2 rounded-full transition duration-300 ease-in-out text-sm ${
                                activeCategory === key
                                    ? 'bg-blue-600 text-white shadow-lg'
                                    : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white'
                            }`}
                        >
                            <Icon className="mr-1 text-base" />
                            {label}
                        </button>
                    ))}
                </div>
                <button
                    onClick={() => setShowFilters(!showFilters)}
                    className={`flex items-center px-4 py-2 rounded-full transition duration-300 ease-in-out text-sm ${
                        showFilters ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white'
                    }`}
                >
                    {showFilters ? <FaTimes className="mr-1 text-base" /> : <FaFilter className="mr-1 text-base" />}
                    {showFilters ? 'Đóng bộ lọc' : 'Mở bộ lọc'}
                </button>
            </div>
            {showFilters && (
                <div className="bg-gray-700 p-4 rounded-2xl mb-4 shadow-lg animate-fadeIn">
                    <h3 className="text-white font-bold text-lg mb-3">Bộ lọc nâng cao</h3>
                    <div className="grid grid-cols-2 gap-4">
                        {[
                            { label: 'Thể loại', options: ['Hành động', 'Tình cảm', 'Kinh dị', 'Hài'] },
                            { label: 'Năm phát hành', options: ['2023', '2022', '2021', '2020'] },
                            { label: 'Quốc gia', options: ['Việt Nam', 'Mỹ', 'Hàn Quốc', 'Nhật Bản'] },
                            { label: 'Đánh giá', options: ['5 sao', '4 sao trở lên', '3 sao trở lên'] }
                        ].map(({ label, options }) => (
                            <div key={label}>
                                <label className="block text-gray-300 mb-1 font-semibold text-sm">{label}</label>
                                <select className="w-full bg-gray-800 text-white rounded-lg p-2 text-sm border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                                    <option>Tất cả</option>
                                    {options.map(option => (
                                        <option key={option}>{option}</option>
                                    ))}
                                </select>
                            </div>
                        ))}
                    </div>
                    <button className="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out text-sm font-semibold flex items-center justify-center">
                        <FaFilter className="mr-1" />
                        Áp dụng bộ lọc
                    </button>
                </div>
            )}
        </div>
    );
};

export default Search;