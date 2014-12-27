<?php namespace spec\MyKatsana\WaterLevel\Providers\InfoBanjir;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class DataSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('MyKatsana\WaterLevel\Providers\InfoBanjir\Data');
    }

    public function it_should_return_correct_status()
    {
        $this->beConstructedWith([
            'water' => 4.4,
            'meta' => [
                'normal'  => 4.0,
                'alert'   => 5.0,
                'warning' => 6.0,
                'danger'  => 7.0,
            ]
        ]);

        $this->getStatus()->shouldBe('normal');
    }
}
