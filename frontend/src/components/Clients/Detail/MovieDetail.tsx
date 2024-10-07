import React, { useState } from 'react';
import DetailModal from './DetailModal'; // Import the new modal component

interface MovieDetailProps {
  onOpenTrailer: (url: string) => void;
}

const MovieDetail: React.FC<MovieDetailProps> = ({ onOpenTrailer }) => {
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
  const [detailContent] = useState('This is the detailed movie content. It will contain the full movie description.');

  const openDetailModal = () => {
    setIsDetailModalOpen(true);
  };

  const closeDetailModal = () => {
    setIsDetailModalOpen(false);
  };

  return (
    <>
      <main
        className="relative bg-cover bg-center bg-no-repeat text-white h-[600px]"
        style={{ backgroundImage: "url('../../../public/images/movies/movie1.png')" }}
      >
        <div className="absolute inset-0 bg-black opacity-70"></div>

        <div className="relative z-10 h-full flex items-center">
          <div className="max-w-5xl mx-auto p-6">
            <div className="bg-opacity-90 p-6 rounded-lg shadow-lg flex flex-col md:flex-row">
              {/* Movie Poster */}
              <div className="flex-shrink-0">
                <img
                  src="../../../public/images/movies/movie1.png"
                  alt="Transformers Poster"
                  className="w-64 rounded-md"
                />
              </div>

              {/* Movie Details */}
              <div className="md:ml-8 mt-6 md:mt-0 flex flex-col justify-between">
                <h1 className="text-4xl font-bold mb-4">TRANSFORMERS MỘT-T13 (Phụ đề)</h1>

                <div className="mb-4">
                  <p><strong>Thể loại:</strong> Hành động, Mỹ</p>
                  <p><strong>Thời gian:</strong> 104 phút</p>
                  <p><strong>Đạo diễn:</strong> Josh Cooley</p>
                  <p><strong>Diễn viên:</strong> Chris Hemsworth, Brian Tyree Henry, Scarlett Johansson</p>
                  <p><strong>Khởi chiếu:</strong> 27/09/2024</p>
                </div>

                <p className="mb-6">
                  Câu chuyện về nguồn gốc chưa từng được hé lộ của Optimus Prime và Megatron...
                </p>

                <p className="text-red-400 mb-4">
                  Kiểm duyệt: T13 - Phim được phổ biến đến người xem từ đủ 13 tuổi trở lên (13+)
                </p>

                {/* Action Buttons */}
                <div className="flex space-x-4">
                  <button
                    onClick={openDetailModal} // Open Detail Modal
                    className="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg"
                  >
                    Chi tiết nội dung
                  </button>
                  <button
                    onClick={() => onOpenTrailer('https://www.youtube.com/embed/ad5_EY2P6Vg')}
                    className="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
                    id="trailerButton"
                  >
                    Xem trailer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      {/* Detail Modal */}
      <DetailModal
        isOpen={isDetailModalOpen}
        content={detailContent}
        onClose={closeDetailModal}
      />
    </>
  );
};

export default MovieDetail;
