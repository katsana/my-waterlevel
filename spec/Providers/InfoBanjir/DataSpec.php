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
        $meta = [
            'normal'  => 4.0,
            'alert'   => 5.0,
            'warning' => 6.0,
            'danger'  => 7.0,
        ];

        $this->beConstructedWith(['meta' => $meta]);

        $tests = [
            ['value' => 3.2, 'expected' => 'normal'],
            ['value' => 4.4, 'expected' => 'normal'],
            ['value' => 5.4, 'expected' => 'alert'],
            ['value' => 6.4, 'expected' => 'warning'],
            ['value' => 7.4, 'expected' => 'danger'],
        ];

        foreach ($tests as $test) {
            $this->water($test['value']);

            $this->getStatus()->shouldBe($test['expected']);
        }
    }
}
