<?php
/**
 * Wasabi Core
 * Copyright (c) Frank Förster (http://frankfoerster.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Frank Förster (http://frankfoerster.com)
 * @link          https://github.com/wasabi-cms/core Wasabi Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Wasabi\Core\Permission;

use Cake\Core\Exception\Exception;
use Cake\Utility\Text;
use Wasabi\Core\Enum\EnumTrait;

class Permission
{
    use EnumTrait;

    const NO = [
        'name' => 'N',
        'value' => 0
    ];

    const OWN = [
        'name' => 'E',
        'value' => 1
    ];

    const ALL = [
        'name' => 'A',
        'value' => 2
    ];

    /**
     * The identifier of this permission.
     *
     * @var string
     */
    protected $_id;

    /**
     * The name of this permission.
     *
     * @var string
     */
    protected $_name;

    /**
     * Holds the controller action paths matching this permission.
     *
     * @var array
     */
    protected $_paths;

    /**
     * The priority of this permission in a permission group.
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Permission constructor.
     */
    public function __construct()
    {
        $this->_paths = [];
        $this->_priority = 999999;
    }

    /**
     * Get the id of this permission.
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the id of this permission.
     *
     * @param string $id
     * @return Permission
     */
    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Get the name of this permission.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the name of this permission.
     *
     * @param String $name
     * @return Permission
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get the priority of this permission.
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Set the priority of this permission.
     *
     * @param integer $priority
     * @return Permission
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;

        return $this;
    }

    /**
     * Get the paths of this permission.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->_paths;
    }

    /**
     * Set the paths of this permission.
     *
     * @param array $paths
     * @return Permission
     */
    public function setPaths(array $paths)
    {
        $this->_paths = $paths;

        return $this;
    }

    /**
     * Add the given path.
     *
     * @param string $path
     * @return Permission
     */
    public function addPath($path)
    {
        if (in_array($path, $this->_paths)) {
            throw new Exception(Text::insert('The path :path already exists.', ['path' => $path]));
        }

        $this->_paths[] = $path;

        return $this;
    }

    /**
     * Add the given paths.
     *
     * @param array $paths
     * @return Permission
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }

        return $this;
    }
}
