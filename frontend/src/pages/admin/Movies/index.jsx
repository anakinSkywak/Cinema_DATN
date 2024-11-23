import MovieData from "./MovieData";
import { useState } from "react";
import MovieHead from "./MovieHead";

function MoviePage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <MovieHead />
            <MovieData params={params} setParams={setParams} />
        </div>
    );
}

export default MoviePage;