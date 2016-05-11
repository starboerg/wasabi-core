<?php
/**
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
namespace Wasabi\Core\Model\Entity;

use Cake\ORM\Entity;
use DateTime;

/**
 * Class Media
 * @package Wasabi\Core\Model\Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $ext
 * @property string $fullname
 * @property string $mime_type
 * @property string $type
 * @property int $size
 * @property int $width
 * @property int $height
 * @property string $upload_path
 * @property string $target_path
 * @property DateTime $created
 * @property DateTime $modified
 *
 * @property User $user
 */
class Media extends Entity
{
    /**
     * Compile the upload path of a media.
     * - replace :root with ROOT
     * - convert backslashes to slahes
     *
     * @return string
     */
    public function compileUploadPath()
    {
        $uploadPath = str_replace(':root', ROOT, $this->upload_path);
        $uploadPath = preg_replace('/\\\/', '/', $uploadPath);

        return $uploadPath;
    }

    /**
     * Compile the target path of a media.
     * - replace :www_root with WWW_ROOT
     * - convert backslashes to slashes
     *
     * @return string
     */
    public function compileTargetPath()
    {
        $targetPath = str_replace(':www_root/', WWW_ROOT, $this->target_path);
        $targetPath = preg_replace('/\\\/', '/', $targetPath);

        return $targetPath;
    }
}
