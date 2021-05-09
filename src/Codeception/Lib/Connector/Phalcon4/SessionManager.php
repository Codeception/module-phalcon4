<?php

declare(strict_types=1);

namespace Codeception\Lib\Connector\Phalcon4;

use Phalcon\Session\Manager;

class SessionManager extends Manager
{
    /**
     * We have to override this as otherwise nothing working correctly in testing.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return true;
    }
}
