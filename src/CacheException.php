<?php

namespace EcomDev\MagentoPsr6Bridge;

/**
 * Generic cache exception
 *
 * It is going to be thrown when something
 * went wrong during saving the object
 */
class CacheException extends \RuntimeException implements \Psr\Cache\CacheException
{

}
