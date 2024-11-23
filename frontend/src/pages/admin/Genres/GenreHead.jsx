import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function GenreHead() {
    return (
        <Head
            title={'Quản lý thể loại'}
            route={config.routes.admin.genres + '/create'}
        />
    );
}

export default GenreHead;