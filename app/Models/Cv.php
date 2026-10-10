<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template',
        'titre_poste',
        'template',
        'nom',
        'prenom',
        'photo',
        'telephone',
        'email',
        'adresse',
        'linkedin',
        'github',
        'portfolio',
        'profil_professionnel',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class);
    }

    public function competences(): HasMany
    {
        return $this->hasMany(Competence::class);
    }

    public function savoirFaires(): HasMany
    {
        return $this->hasMany(SavoirFaire::class);
    }

    public function logiciels(): HasMany
    {
        return $this->hasMany(Logiciel::class);
    }

    public function langues(): HasMany
    {
        return $this->hasMany(Langue::class);
    }

    public function interets(): HasMany
    {
        return $this->hasMany(Interet::class);
    }

    public function informationsComplementaires(): HasMany
{
    return $this->hasMany(InformationComplementaire::class);
}
}