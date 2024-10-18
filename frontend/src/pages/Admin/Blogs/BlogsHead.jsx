import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function BlogsHead() {
    return (
        <Head
            title={'Quản lý thể loại'}
            route={config.routes.admin.blogs + '/create'}
        />
    );
}

export default BlogsHead;