<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Debbuger extends Model
{
    protected $fillable = [
        'instance',
        'channel',
        'message',
        'level',
        'context'
    ];

    protected $casts = [
        'context' => 'array',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    const UPDATED_AT = null;

    public function __construct(array $attributes = array())
    {
        $this->table      = config('debbuger.channels.database.table');
        $this->connection = config('debbuger.channels.database.connection');

        parent::__construct($attributes);
    }

    public function changeConnection(string $db_connection)
    {
        $this->connection = $db_connection;
    }

    public function changeTable(string $table_name)
    {
        $this->table = $table_name;
    }
}


