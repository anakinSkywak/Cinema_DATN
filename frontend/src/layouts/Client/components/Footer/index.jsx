import { NavLink } from 'react-router-dom'

const Footer = () => {
    return (
        <>
            <footer className="bg-black text-center py-8">
                <div className="flex justify-center space-x-4 mb-4">
                    <a className="text-white" href="#">
                        Chính Sách
                    </a>
                    <a className="text-white" href="#">
                        Lịch Chiếu
                    </a>
                    <a className="text-white" href="#">
                        Tin Tức
                    </a>
                    <a className="text-white" href="#">
                        Giá Vé
                    </a>
                    <a className="text-white" href="#">
                        Hỏi Đáp
                    </a>
                    <a className="text-white" href="#">
                        Liên Hệ
                    </a>
                </div>
                <div className="flex justify-center space-x-4 mb-4">
                    <a href="#">
                        <img
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Ffacebook.png&w=32&q=75"
                            alt=""
                        />
                    </a>
                    <a href="#">
                        <img
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Fzalo.webp&w=32&q=75"
                            alt=""
                        />
                    </a>
                    <a href="#">
                        <img
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Fyoutube2.png&w=32&q=75"
                            alt=""
                        />
                    </a>
                    <a href="#">
                        <img
                            alt="Google Play"
                            className="inline"
                            height="100"
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Fgoogleplay.png&w=256&q=75"
                            width="150"
                        />
                    </a>
                    <a href="#">
                        <img
                            alt="App Store"
                            className="inline"
                            height="100"
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Fappstore.png&w=256&q=75"
                            width="150"
                        />
                    </a>
                    <a href="#">
                        <img
                            alt="Certification"
                            className="inline"
                            height="100"
                            src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Fcertification.png&w=256&q=75"
                            width="150"
                        />
                    </a>
                </div>
                <p className="text-white mb-2">
                    Cơ quan chủ quản: BỘ VĂN HÓA, THỂ THAO VÀ DU LỊCH
                </p>
                <p className="text-white mb-2">
                    Bản quyền thuộc Trung tâm Chiếu phim Quốc gia.
                </p>
                <p className="text-white mb-2">
                    Giấy phép số: 224/GP- TTĐT ngày 31/8/2010 - Chịu trách nhiệm: Vũ Đức Tùng - Giám đốc.
                </p>
                <p className="text-white mb-2">
                    Địa chỉ: 87 Láng Hạ, Quận Ba Đình, Tp. Hà Nội - Điện thoại: 024.35141791
                </p>
                <p className="text-white">
                    Copyright 2023. NCC All Rights Reserved. Dev by Anvui.vn
                </p>
            </footer>
        </>
    );
};

export default Footer;