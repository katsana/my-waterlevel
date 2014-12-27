<?php namespace MyKatsana\WaterLevel\Contracts;

interface Data
{
    /**
     * Get station ID.
     *
     * @return int|string
     */
    public function getStationId();

    /**
     * Get station information.
     *
     * @return array
     */
    public function getStation();

    /**
     * Get meta/raw data.
     *
     * @return mixed
     */
    public function getMeta();

    /**
     * Get actual river level.
     *
     * @return float
     */
    public function getWaterLevel();

    /**
     * Get river status, either "normal", "alert", "warning" or "danger".
     *
     * @return string
     */
    public function getStatus();

    /**
     * Get last updated at.
     *
     * @return string
     */
    public function getUpdatedAt();
}
