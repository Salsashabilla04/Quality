<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintItem extends Model
{
    protected $fillable = [
        'complaint_id',
        'jenis_ketidaksesuaian',
        'detail_ketidaksesuaian',
        'penyebab',
        'detail_penyebab',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
