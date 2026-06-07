<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'description',
        'type_no',
        'trans_no',
        'unique_name',
        'tran_date',
        'filename',
        'filesize',
        'filetype',
    ];

    protected $casts = [
        'tran_date' => 'date',
        'filesize' => 'integer',
    ];
}
