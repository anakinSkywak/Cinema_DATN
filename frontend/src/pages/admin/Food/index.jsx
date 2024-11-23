import FoodData from "./FoodData";
import { useState } from "react";
import FoodHead from "./FoodHead";

function FoodPage() {
    const [params, setParams] = useState({
        pageNo: 1,
        pageSize: 5,
    });
    return (
        <div className="p-4 bg-gray-900 mb-3 flex flex-col rounded-lg">
            <FoodHead />
            <FoodData params={params} setParams={setParams} />
        </div>
    );
}

export default FoodPage;