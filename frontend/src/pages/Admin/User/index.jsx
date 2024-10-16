import Data from "./Data";
import UserHead from "./UserHead";
import './user.scss'

function UserPage(){
    return <div className="user-container">
        <UserHead />
        <Data />
    </div>
}

export default UserPage;