<?php

namespace Spray\Serializer;

use DateTime;

class DateTimeSerializer extends AbstractObjectSerializer
{
    /**
     * @var string
     */
    private $format;
    
    /**
     * @param string $format
     */
    public function __construct($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
        parent::__construct(
            function($subject, array &$data, SerializerInterface $serializer) use ($format) {
                $data = $subject->format($format);
            },
            function($subject, &$data, SerializerInterface $serializer) use ($format) {
                return $subject;
            },
            'DateTime'
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function construct($subject, &$data = array())
    {
        return DateTime::createFromFormat($this->format, $data);
    }
}
