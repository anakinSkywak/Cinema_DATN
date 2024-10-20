import Data from "./Data";
import './category.scss'
import { useState } from "react";
import VoucherHead from "./VoucherHead";

function  VoucherPage(){
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return <div className="category-container">
        <VoucherHead />
        <Data params={params} setParams={setParams}/>
    </div>
}

export default  VoucherPage;