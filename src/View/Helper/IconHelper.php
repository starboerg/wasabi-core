<?php

namespace Wasabi\Core\View\Helper;

use Cake\View\Helper;

class IconHelper extends Helper
{
    /**
     * Add icon plus.
     *
     * @return string
     */
    public function addPlus()
    {
        return '<i class="icon-plus"></i><span class="sr-only">' . __d('wasabi_core', 'Add') . '</span>';
    }

    /**
     * Edit icon.
     *
     * @return string
     */
    public function edit()
    {
        return '<i class="wicon-edit"></i><span class="sr-only">' . __d('wasabi_core', 'Edit') . '</span>';
    }

    /**
     * Delete icon.
     *
     * @return string
     */
    public function delete()
    {
        return '<i class="wicon-remove"></i><span class="sr-only">' . __d('wasabi_core', 'Delete') . '</span>';
    }

    /**
     * First page icon.
     *
     * @return string
     */
    public function firstPage()
    {
        return '«';
    }

    /**
     * Previous page icon.
     *
     * @return string
     */
    public function previousPage()
    {
        return '‹';
    }

    /**
     * Next page icon.
     *
     * @return string
     */
    public function nextPage()
    {
        return '›';
    }

    /**
     * Last page icon.
     *
     * @return string
     */
    public function lastPage()
    {
        return '»';
    }
}
