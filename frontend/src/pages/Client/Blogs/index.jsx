const BlogPage = () => {
    return (
        <>

            <main classNameName="flex flex-col items-center py-8 px-10" >
                <div className="text-center mb-8">
                    <h1 className="text-2xl font-bold">Giá vé</h1>
                    <p>( Áp dụng từ ngày 01/06/2023 )</p>
                </div>

                <div className="mb-8">
                    <h2 className="text-xl font-bold mb-4">1. GIÁ VÉ XEM PHIM 2D</h2>
                    <table className="w-full text-center">
                        <thead className="table-header">
                            <tr>
                                <th className="table-cell p-2"></th>
                                <th className="table-cell p-2" colspan="3">Từ thứ 2 đến thứ 5<br/>From Monday to Thursday</th>
                                <th className="table-cell p-2" colspan="3">Thứ 6, 7, CN và ngày Lễ<br/>Friday, Saturday, Sunday & public holiday</th>
                            </tr>
                            <tr>
                                <th className="table-cell p-2">Thời gian</th>
                                <th className="table-cell p-2">Ghế thường</th>
                                <th className="table-cell p-2 text-yellow">Ghế VIP</th>
                                <th className="table-cell p-2 text-red">Ghế đôi</th>
                                <th className="table-cell p-2">Ghế thường</th>
                                <th className="table-cell p-2 text-yellow">Ghế VIP</th>
                                <th className="table-cell p-2 text-red">Ghế đôi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="table-cell p-2">Trước 12h</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 12:00 đến trước 17:00</td>
                                <td className="table-cell p-2">70.000đ</td>
                                <td className="table-cell p-2 text-yellow">75.000đ</td>
                                <td className="table-cell p-2 text-red">160.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 17:00 đến trước 23:00</td>
                                <td className="table-cell p-2">80.000đ</td>
                                <td className="table-cell p-2 text-yellow">85.000đ</td>
                                <td className="table-cell p-2 text-red">180.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 23:00</td>
                                <td className="table-cell p-2">65.000đ</td>
                                <td className="table-cell p-2 text-yellow">70.000đ</td>
                                <td className="table-cell p-2 text-red">150.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <h2 className="text-xl font-bold mb-4">2. GIÁ VÉ XEM PHIM 3D</h2>
                    <table className="w-full text-center">
                        <thead className="table-header">
                            <tr>
                                <th className="table-cell p-2"></th>
                                <th className="table-cell p-2" colspan="3">Từ thứ 2 đến thứ 5<br/>From Monday to Thursday</th>
                                <th className="table-cell p-2" colspan="3">Thứ 6, 7, CN và ngày Lễ<br/>Friday, Saturday, Sunday & public holiday</th>
                            </tr>
                            <tr>
                                <th className="table-cell p-2">Thời gian</th>
                                <th className="table-cell p-2">Ghế thường</th>
                                <th className="table-cell p-2 text-yellow">Ghế VIP</th>
                                <th className="table-cell p-2 text-red">Ghế đôi</th>
                                <th className="table-cell p-2">Ghế thường</th>
                                <th className="table-cell p-2 text-yellow">Ghế VIP</th>
                                <th className="table-cell p-2 text-red">Ghế đôi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="table-cell p-2">Trước 12h</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 12:00 đến trước 17:00</td>
                                <td className="table-cell p-2">70.000đ</td>
                                <td className="table-cell p-2 text-yellow">75.000đ</td>
                                <td className="table-cell p-2 text-red">160.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 17:00 đến trước 23:00</td>
                                <td className="table-cell p-2">80.000đ</td>
                                <td className="table-cell p-2 text-yellow">85.000đ</td>
                                <td className="table-cell p-2 text-red">180.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                            <tr>
                                <td className="table-cell p-2">Từ 23:00</td>
                                <td className="table-cell p-2">65.000đ</td>
                                <td className="table-cell p-2 text-yellow">70.000đ</td>
                                <td className="table-cell p-2 text-red">150.000đ</td>
                                <td className="table-cell p-2">55.000đ</td>
                                <td className="table-cell p-2 text-yellow">65.000đ</td>
                                <td className="table-cell p-2 text-red">140.000đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>

        </>
    );
};

export default BlogPage;