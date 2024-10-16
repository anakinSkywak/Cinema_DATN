import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function AutoApproveHead() {
    return (
        <Head
            isAdd={false}
            title={'Quản lý duyệt tự động'}
        />
    );
}

export default AutoApproveHead;