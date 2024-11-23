import UserList from "./UserList";
import UserHead from "./UserHead";
import './user.scss'

function UserPage(){
    return (
        <div className="user-container h-full flex flex-col">
            <UserHead />
            <UserList />
        </div>
    );
}

export default UserPage;