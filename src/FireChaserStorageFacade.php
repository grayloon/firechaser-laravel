<?php

namespace GrayLoon\FireChaser;

use Illuminate\Support\Facades\Facade;

/**
 * Class LaravelBackboneStorageFacade.
 */
class FireChaserStorageFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor(): string
	{
		return 'fireChaser';
	}
}
