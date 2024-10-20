import Data from "./Data";
import SettingHead from "./SettingHead";
import './setting.scss'

function SettingPage(){
    return <div className="Setting-container">
        <SettingHead />
        <Data />
    </div>
}

export default SettingPage;