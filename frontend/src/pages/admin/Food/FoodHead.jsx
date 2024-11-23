import Head from '../../../layouts/Admin/components/Head';
import config from '../../../config';

function FoodHead() {
    return (
        <Head
            title={'Quản lý món ăn'}
            route={config.routes.admin.food + '/create'}
        />
    );
}

export default FoodHead;