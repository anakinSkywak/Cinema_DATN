<?php

namespace App\Listeners;

use App\Models\Membership;
use Illuminate\Support\Carbon;

class UpdateMembershipStatus
{
    public function handle($event)
    {
        $currentDate = Carbon::now();

        // Lấy danh sách các thẻ đã hết hạn
        $memberships = Membership::where('ngay_het_han', '<', $currentDate)
                                  ->where('trang_thai', 0)
                                  ->get();

        foreach ($memberships as $membership) {
            $membership->trang_thai = 1;
            $membership->renewal_message = "Thẻ hội viên đã hết hạn.";
            $membership->save();
        }
    }
}
