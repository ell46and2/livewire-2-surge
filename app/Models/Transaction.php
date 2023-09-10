<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const STATUSES = [
        'success' => 'Success',
        'failed' => 'Failed',
        'processing' => 'Processing',
    ];

    protected $fillable = [
        'title',
        'amount',
        'status',
        'date',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    protected $appends = [
        'date_for_editing'
    ];

    public function getStatusColorAttribute(): string
    {
        return [
            'success' => 'green',
            'failed' => 'red',
        ][ $this->status ] ?? 'gray';
    }

    public function getDateForHumansAttribute(): string
    {
        return $this->date->format('M, d Y');
    }

    public function getDateForEditingAttribute(): string
    {
        return $this->date->format('m/d/Y');
    }

    public function setDateForEditingAttribute($value)
    {
        $this->date = Carbon::parse($value);
    }
}
