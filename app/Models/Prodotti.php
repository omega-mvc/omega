<?php

declare(strict_types=1);

namespace App\Models;

use Omega\Database\Model\Model;

class Prodotti extends Model
{
    protected string $tableName  = 'prodotti';
    protected string $primaryKey  = 'id';
}
