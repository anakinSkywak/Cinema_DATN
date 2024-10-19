import Data from "./Data";
import './category.scss'
import { useState } from "react";
import GenreHead from "./GenreHead";

function  GenrePage(){
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return <div className="category-container">
        <GenreHead />
        <Data params={params} setParams={setParams}/>
    </div>
}

export default  GenrePage;