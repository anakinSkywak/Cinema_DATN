import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function SeatHead() {
    return (
        <Head
            title={'Quản lý ghế'}
            route={config.routes.admin.seat + '/create'}
        />
    );
}

export default SeatHead;