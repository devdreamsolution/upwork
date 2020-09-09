<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RemoteIOTDevice
 * @package App\Models
 */
class DeviceType extends Model
{
    /**
     * @var string
     */
    protected $table = 'device_type';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id', 'name', 'attributes'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'attributes' => 'json'
    ];

    /**
     * Get actual device of this type
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function deviceGroups()
    {
        return $this->hasMany(DeviceGroup::class, 'device_type_id');
    }
}