<?php namespace MyKatsana\WaterLevel\Providers\InfoBanjir;

use Illuminate\Support\Fluent;
use MyKatsana\WaterLevel\Contracts\Data as DataContract;

class Data extends Fluent implements DataContract
{
    /**
     * Get station ID.
     *
     * @return int|string
     */
    public function getStationId()
    {
        return data_get($this->attributes, 'station.id');
    }

    /**
     * Get station information.
     *
     * @return array
     */
    public function getStation()
    {
        return $this->get('station');
    }

    /**
     * Get meta/raw data.
     *
     * @return mixed
     */
    public function getMeta()
    {
        return $this->get('meta');
    }

    /**
     * Get actual river level.
     *
     * @return float
     */
    public function getWaterLevel()
    {
        return $this->get('water');
    }

    /**
     * Get river status, either "normal", "alert", "warning" or "danger".
     *
     * @return string
     */
    public function getStatus()
    {
        $water = $this->get('water');

        foreach (['danger', 'warning', 'alert', 'normal'] as $type) {
            if ($water >= $this->getMeta()[$type]) {
                return $type;
            }
        }

        return 'normal';
    }

    /**
     * Get last updated at.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->get('updated_at');
    }
}
