<?php
/**
 * Flash Component (used for code completion)
 *
 * Wasabi CMS
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Controller\Component;

use \Cake\Controller\Component\FlashComponent as CakeFlashComponent;

/**
 * Class FlashComponent
 *
 * @method void error() error(string $message, $options = [])
 * @method void success() success(string $message, $options = [])
 * @method void warning() warning(string $message, $options = [])
 */
class FlashComponent extends CakeFlashComponent
{
}
