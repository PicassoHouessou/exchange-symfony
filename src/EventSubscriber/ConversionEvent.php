<?php


namespace App\EventSubscriber;

use App\Entity\Conversion;
use Symfony\Contracts\EventDispatcher\Event;

class ConversionEvent extends Event
{
    const CONVERSION_REQUEST = 'conversion.add';
    protected $conversion;

    public function __construct(Conversion $conversion)
    {
        $this->conversion = $conversion ;
    }

    /**
     * @return Conversion
     */
    public function getConversion(): Conversion
    {
        return $this->conversion;
    }


}