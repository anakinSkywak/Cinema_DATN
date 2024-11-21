<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).





<?php
namespace App\Http\Controllers\Api;

use App\Models\MemberShips;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class MemberShipsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả thẻ hội viên.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng Membership
        $data = Memberships::with('registerMember')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Membership nào'
            ], 200);
        }

        // Dữ liệu đã có status tự động qua Accessor
        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $membership = Memberships::with('registerMember')->find($id);
    
        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }
    
        // Xác định trạng thái thẻ dựa trên ngày hết hạn
        $currentDate = now();
        $expirationDate = Carbon::parse($membership->ngay_het_han);
    
        // Kiểm tra nếu thẻ đã hết hạn
        if ($expirationDate && $expirationDate < $currentDate) {
            $membership->trang_thai = 1;  // Thẻ hết hạn => Trạng thái = 1
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
            
            // Kiểm tra nếu thẻ đã hết hạn và không gia hạn trong vòng 2 ngày
            if ($expirationDate->addDays(2)->isBefore($currentDate)) {
                // Nếu đã qua 2 ngày sau khi hết hạn mà không gia hạn, xóa thẻ
                $membership->delete();
                return response()->json([
                    'message' => 'Thẻ hội viên đã hết hạn hơn 2 ngày và đã bị xóa!',
                ], 200);
            }
        } 
        // Thẻ còn hạn và cần thông báo trước khi hết hạn 2 ngày
        elseif ($expirationDate->diffInDays($currentDate) <= 2 && $expirationDate->diffInDays($currentDate) > 0) {
            $membership->trang_thai = 0;  // Thẻ còn hạn => Trạng thái = 0
            $membership->renewal_message = "Thẻ hội viên sẽ hết hạn trong vòng 2 ngày. Vui lòng gia hạn thẻ để tiếp tục sử dụng dịch vụ!";
        } else {
            $membership->trang_thai = 0;  // Thẻ còn hạn => Trạng thái = 0
            $membership->renewal_message = null;  // Không có thông báo nếu thẻ còn hạn
        }
    
        // Cập nhật thẻ nếu có thay đổi trạng thái
        $membership->save();
    
        return response()->json([
            'message' => 'Hiển thị thông tin thẻ hội viên thành công',
            'data' => $membership
        ], 200); // Trả về 200 nếu tìm thấy
    }
    

    /**
     * Xóa thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $membership = Memberships::find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        $membership->delete();

        return response()->json([
            'message' => 'Xóa thẻ hội viên thành công!'
        ], 200); // Trả về 200 khi xóa thành công
    }
}
