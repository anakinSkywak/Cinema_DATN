import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';
import { Button } from 'antd';

function MovieHead() {
    return (
        <Head
            title={'Quản lý phim'}
            route={config.routes.admin.movies + '/create'}
        >
            <Button type="primary" href={config.routes.admin.movies + '/create'}>
                Thêm phim mới
            </Button>
        </Head>
    );
}

export default MovieHead;