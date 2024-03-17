<?php
/*
 * @package    Joomla - PortableUtf8 Patch Plugin
 * @version    1.0.0
 * @author     Igor Berdicheskiy - septdir.ru
 * @copyright  Copyright (c) 2013 - 2024 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://septdir.ru
 */

namespace Joomla\Plugin\System\PortableUtf8Patch\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Filesystem\Path;

class PortableUtf8Patch extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterInitialise' => 'patchFiles',
		];
	}

	/**
	 * Method to patch files.
	 *
	 * @since 1.0.0
	 */
	public function patchFiles()
	{
		$files = [
			JPATH_ROOT . '/libraries/vendor/voku/portable-utf8/src/voku/helper/UTF8.php',
			JPATH_ROOT . '/libraries/vendor/voku/portable-ascii/src/voku/helper/ASCII.php',

		];
		foreach ($files as $path)
		{
			$path = Path::clean($path);
			if (is_file($path))
			{
				$contents = file_get_contents($path);
				if (strpos($contents, '## 🇺🇸 To people') !== false)
				{
					$pattern     = '/(namespace voku\\\\helper;\\s*\\/\\*\\*).*?(\\* @psalm-immutable\\s*\\*\\/\\s*final class)/s';
					$replacement = '$1' . PHP_EOL . ' $2';
					$contents    = preg_replace($pattern, $replacement, $contents);

					$pattern  = '/(namespace voku\\\\helper;\\s*\\/\\*\\*).*?(\\* @immutable\\s*\\*\\/\\s*final class)/s';
					$contents = preg_replace($pattern, $replacement, $contents);

					file_put_contents($path, $contents);
				}
			}
		}
	}
}