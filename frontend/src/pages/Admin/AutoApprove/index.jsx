import Data from "./Data";
import AutoApproveHead from "./AutoApproveHead";
import './autoapprove.scss'

function AutoApprovePage(){
    return <div className="AutoApprove-container">
        <AutoApproveHead />
        <Data />
    </div>
}

export default AutoApprovePage;