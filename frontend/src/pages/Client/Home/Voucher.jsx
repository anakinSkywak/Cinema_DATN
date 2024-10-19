const Voucher = () => {
    return (
        <div className="w-1/4 pl-4">
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-bold">
                    Khuyến mãi
                </h2>
                <a className="text-blue-400" href="#">
                    Xem tất cả
                </a>
            </div>
            <div className="space-y-4">
                <div className="w-full">
                    <img
                        alt="Promotion banner for popcorn mix"
                        className="rounded-lg hover-zoom"
                        style={{ height: 150, width: 400 }}
                        src="https://storage.googleapis.com/a1aa/image/wWeeo8RYgQoaPkTicOW9Vu9aK0HBI7h7QDk4iq2AGw7iKFjTA.jpg"
                    />
                </div>
                <div className="w-full">
                    <img
                        alt="Promotion banner for combo deals"
                        className="rounded-lg hover-zoom"
                        style={{ height: 150, width: 400 }}
                        src="https://storage.googleapis.com/a1aa/image/wWeeo8RYgQoaPkTicOW9Vu9aK0HBI7h7QDk4iq2AGw7iKFjTA.jpg"
                    />
                </div>
                <div className="w-full">
                    <img
                        alt="Promotion banner for special offers"
                        className="rounded-lg hover-zoom"
                        style={{ height: 150, width: 400 }}
                        src="https://storage.googleapis.com/a1aa/image/VkLeWNJvaZyNVCLNREicYz04Nnx2fp8OhCf8HyssduhLVKGnA.jpg"
                    />
                </div>
            </div>
        </div>
    )
}

export default Voucher;