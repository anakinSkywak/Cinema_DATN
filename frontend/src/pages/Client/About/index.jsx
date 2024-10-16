const AboutPage = () => {
    return (
        <>

            <main classNameName="flex flex-col items-center py-8 px-10" >
                <div class="container mx-auto p-4 mt-16 px-32">
                    <h1 class="text-3xl font-bold mb-4 text-center">
                        Giới thiệu
                    </h1>
                    <div class="flex space-x-4 mb-6 justify-center">
                        <button class="bg-red-500 text-white px-4 py-2 rounded-full hover-zoom">
                            Giới thiệu
                        </button>
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-full hover-zoom">
                            Dịch vụ
                        </button>
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-full hover-zoom">
                            Phòng chiếu - Nhà hát
                        </button>
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-full hover-zoom">
                            NCC - Điểm hẹn cuối tuần
                        </button>
                    </div>
                    <div class="mb-6">
                        <p>
                            Trung tâm Chiếu phim Quốc gia (tên giao dịch quốc tế là National Cinema Center) là đơn vị sự nghiệp công lập, trực thuộc Bộ Văn hóa, Thể thao và Du lịch, có chức năng tổ chức chiếu phim phục vụ các nhiệm vụ chính trị, xã hội, hợp tác quốc tế; trưng bày điện ảnh; điều tra xã hội học về nhu cầu khán giả để phục vụ cho công tác định hướng phát triển ngành điện ảnh.
                        </p>
                        <p>
                            Ngày thành lập: 29/12/1997
                        </p>
                        <p>
                            Trụ sở: 87 Láng Hạ, quận Ba Đình, thành phố Hà Nội.
                        </p>
                        <p>
                            Website: www.chieuphimquocgia.com.vn
                        </p>
                        <p>
                            Email: pdichvuncc@gmail.com
                        </p>
                        <p>
                            Số điện thoại: 024.3514 1791 / 024.3514 8647
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <img alt="Image of the National Cinema Center building" class="w-full h-auto" height="400" src="https://storage.googleapis.com/a1aa/image/68twhC2KAo5ebiia2seg2KdBePzmn0CagdGo3VwVNbsZHPOnA.jpg" width="600" />
                        <img alt="Another view of the National Cinema Center building" class="w-full h-auto" height="400" src="https://storage.googleapis.com/a1aa/image/cszOf0oOXN0RC6OdHOBcqpJnTrGf7VkKcR3DOYqAWrnrjHnTA.jpg" width="600" />
                    </div>
                </div>
            </main>

        </>
    );
};

export default AboutPage;