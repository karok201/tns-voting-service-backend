<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voting extends Model
{
    public const TABLE_NAME = 'votings';

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_STATUS = 'status';
    public const FIELD_END_DATE = 'end_date';
    public const FIELD_TYPE = 'type';
    public const FIELD_WAY = 'way';
    public const FIELD_CREATED_AT = self::CREATED_AT;
    public const FIELD_UPDATED_AT = self::UPDATED_AT;

    protected $table = self::TABLE_NAME;

    protected $guarded = [];
}
