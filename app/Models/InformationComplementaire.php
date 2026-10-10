<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformationComplementaire extends Model
{
    use HasFactory;

    protected $table = 'informations_complementaires';

    protected $fillable = [
        'cv_id',
        'type',
        'intitule',
        'etablissement',
        'date_obtention',
        'description',
    ];

    protected $casts = [
        'date_obtention' => 'date:Y-m-d',
    ];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}