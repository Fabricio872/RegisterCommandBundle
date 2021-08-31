<?php

namespace Fabricio872\RegisterCommand\Services;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

interface UserEditorInterface
{
    public function __construct(
        Input  $input,
        Output $output
    );

    public function drawEdiTable(): void;
}
