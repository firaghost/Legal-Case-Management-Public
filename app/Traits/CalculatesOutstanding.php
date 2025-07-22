<?php

namespace App\Traits;

trait CalculatesOutstanding
{
    public static function bootCalculatesOutstanding(): void
    {
        static::saving(function ($model) {
            if ($model->claimed_amount !== null && $model->recovered_amount !== null) {
                $model->outstanding_amount = $model->claimed_amount - $model->recovered_amount;
            }
        });
    }
}






