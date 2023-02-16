<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\Helpers;

use Exception;
use Symfony\Component\Console\Input\StreamableInputInterface;

trait StreamableInput
{
    /**
     * @var resource
     */
    protected $inputStream;

    /**
     * @return bool|resource
     */
    protected function getInputStream()
    {
        if (! $this->input instanceof StreamableInputInterface) {
            throw new Exception('Streamable interface not found');
        }

        if (empty($this->inputStream)) {
            $this->inputStream = $this->input->getStream() ?: STDIN;
        }

        return $this->inputStream;
    }
}
