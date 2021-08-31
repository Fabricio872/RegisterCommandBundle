<?php

namespace Fabricio872\RegisterCommand\Services;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

class UserEditor implements UserEditorInterface
{
    /** @var Input */
    private $input;
    /** @var Output */
    private $output;

    public function __construct(
        Input  $input,
        Output $output
    ) {
        $this->input = $input;
        $this->output = $output;
    }

    public function drawEdiTable(): void
    {
    }
}
