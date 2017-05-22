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
namespace Wasabi\Core\View\Cell;

use Cake\View\Cell;
use Wasabi\Core\Model\Table\UsersTable;

/**
 * Class DashboardCell
 *
 * @property UsersTable Users
 */
class DashboardCell extends Cell
{

    /**
     * Render summary box with the number of active users.
     *
     * @param array $options
     * @return void
     */
    public function activeUsers(array $options)
    {
        $this->Users = $this->loadModel('Wasabi/Core.Users');
        $userCount = $this->Users->find('active')->count();

        $this->set('value', $userCount);
        $this->_render($options);
    }

    /**
     * Render summary box with the number of users awaiting activation by an admin.
     *
     * @param array $options
     * @return void
     */
    public function usersAwaitingActivation(array $options)
    {
        $this->Users = $this->loadModel('Wasabi/Core.Users');
        $usersAwaitingActivation = $this->Users->find('awaitingActivation')->count();

        $this->set('value', $usersAwaitingActivation);
        $this->_render($options);
    }

    /**
     * Prepare the additional view variables and set the template.
     *
     * @param array $options
     * @param string $template
     * @return void
     */
    protected function _render(array $options, $template = 'summarybox')
    {
        $convertableKeys = ['render', 'title', 'link', 'linkTitle', 'class', 'iconClass', 'unit'];
        foreach ($convertableKeys as $key) {
            if (isset($options[$key])) {
                $this->set($key, $options[$key]);
            }
        }

        $this->template = $template;
    }
}
