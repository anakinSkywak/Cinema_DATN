const ScoreHistory = () => {
    return (
        <>
            <div className="bg-black p-4 rounded w-3/4">
                <table className="w-full text-left">
                    <thead className="border-b border-gray-700">
                        <tr>
                            <th className="py-2">
                                Ngày giao dịch
                            </th>
                            <th className="py-2">
                                Tên phim
                            </th>
                            <th className="py-2">
                                Số vé
                            </th>
                            <th className="py-2">
                                Số điểm
                            </th>
                        </tr>
                    </thead>
                    <tbody style={{height: "200px"}}>
                        <tr>
                            <td className="py-4 text-center" colspan="4">
                                Không có dữ liệu
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </>
    );
}

export default ScoreHistory;