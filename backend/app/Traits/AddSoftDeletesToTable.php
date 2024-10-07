<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait AddSoftDeletesToTable
{
    public function addSoftDeletes(Blueprint $table)
    {
        $table->softDeletes();
    }
}
