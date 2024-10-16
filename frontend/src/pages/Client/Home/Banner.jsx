import { Swiper, SwiperSlide } from 'swiper/react';
import  BannerImage from "../../../assets/image/banner.webp";
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

// import required modules
import { Autoplay, Pagination, Navigation } from 'swiper/modules';

const Banner = () => {
    return (
        <>
            <Swiper
                spaceBetween={30}
                centeredSlides={true}
                autoplay={{
                    delay: 4500,
                    disableOnInteraction: false,
                }}
                pagination={{
                    clickable: true,
                }}
                navigation={true}
                modules={[Autoplay, Pagination, Navigation]}
                className="mySwiper"
            >
                <SwiperSlide><img alt="Banner image" src={BannerImage} /></SwiperSlide>
                <SwiperSlide><img alt="Banner image" src={BannerImage} /></SwiperSlide>
                <SwiperSlide><img alt="Banner image" src={BannerImage} /></SwiperSlide>
                <SwiperSlide><img alt="Banner image" src={BannerImage} /></SwiperSlide>

            </Swiper>
        </>
    )
}

export default Banner;