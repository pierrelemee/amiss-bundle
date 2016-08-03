<?php

namespace Amiss\Bundle\AmissBundle;

use Amiss\Bundle\AmissBundle\DependencyInjection\AmissExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle.
 *
 * @author Pierre Lemée <pierre@pierrelemee.fr>
 */
class AmissBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new AmissExtension();
    }
}
