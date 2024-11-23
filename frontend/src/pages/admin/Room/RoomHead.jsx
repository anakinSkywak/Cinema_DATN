import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function RoomHead() {
    return (
        <Head
            title={'Quản lý phòng'}
            route={config.routes.admin.room + '/create'}
        />
    );
}

export default RoomHead;