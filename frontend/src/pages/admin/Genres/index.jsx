import Data from "./Data";
import { useState } from "react";
import GenreHead from "./GenreHead";

function GenrePage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <GenreHead />
            <Data params={params} setParams={setParams} />
        </div>
    );
}

export default GenrePage;