import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function VoucherHead() {
    return (
        <Head
            title={'Quản lý khuyễn mãi'}
            route={config.routes.admin.voucher + '/create'}
        />
    );
}

export default VoucherHead;