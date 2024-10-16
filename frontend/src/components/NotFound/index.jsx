import React from 'react';
import { Result, Button } from 'antd';
import { Link } from 'react-router-dom';

function NotFound() {
  return(
    <Result
            className='text-white'
            status="404"
            title="404"
            subTitle="Xin lỗi, trang bạn tìm kiếm không tồn tại."
            extra={
                <Link to="/">
                    <Button type="primary">Quay lại trang chính</Button>
                </Link>
            }
        />
  )
}

export default NotFound;