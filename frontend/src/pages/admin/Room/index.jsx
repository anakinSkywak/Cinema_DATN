import RoomData from "./RoomData";
import { useState } from "react";
import RoomHead from "./RoomHead";

function RoomPage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <RoomHead />
            <RoomData params={params} setParams={setParams} />
        </div>
    );
}

export default RoomPage;