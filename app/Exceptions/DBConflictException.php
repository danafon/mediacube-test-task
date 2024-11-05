<?php

namespace App\Exceptions;

class DBConflictException extends CustomHttpException
{
    public function __construct(string $parent, string $related)
    {
        parent::__construct(
            "Provided $parent has at least one $related related. Aborting.",
            409
        );
    }
}
