import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function SettingHead() {
    return (
        <Head
            isAdd={false}
            title={'Quản lý cài đặt'}
        />
    );
}

export default SettingHead;