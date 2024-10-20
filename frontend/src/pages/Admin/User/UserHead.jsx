import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function UserHead() {
    return (
        <Head
            isAdd={false}
            title={'Quản lý tài khoản'}
        />
    );
}

export default UserHead;