import ShowTimeData from "./showTimeData";
import { useState } from "react";
import ShowTimeHead from "./showTimeHead";

function ShowTimePage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <ShowTimeHead />
            <ShowTimeData params={params} setParams={setParams} />
        </div>
    );
}

export default ShowTimePage;