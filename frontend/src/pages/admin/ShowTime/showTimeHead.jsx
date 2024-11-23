import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function ShowTimeHead() {
    return (
        <Head
            title={'Quản lý thời gian chiếu'}
            route={config.routes.admin.showTime + '/create'}
        />
    );
}

export default ShowTimeHead;