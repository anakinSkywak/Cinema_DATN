import Data from "./Data";
import './category.scss'
import { useState } from "react";
import BlogsHead from "./BlogsHead";

function  BlogsPage(){
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return <div className="category-container">
        <BlogsHead />
        <Data params={params} setParams={setParams}/>
    </div>
}

export default  BlogsPage;