<?php namespace MyKatsana\WaterLevel\Contracts;

interface Client
{
    /**
     * Get water level information on all state.
     *
     * @return array
     */
    public function execute();

    /**
     * Get water level information based on state.
     *
     * @param  string  $code
     * @return \MyKatsana\WaterLevel\Contracts\Data[]
     */
    public function executeByState($code);

    /**
     * Get available state code.
     *
     * @return string[]
     */
    public function getAvailableStateCode();
}
