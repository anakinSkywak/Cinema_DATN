import SeatData from "./SeatData";
import { useState } from "react";
import SeatHead from "./SeatHead";

function SeatPage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <SeatHead />
            <SeatData params={params} setParams={setParams} />
        </div>
    );
}

export default SeatPage;