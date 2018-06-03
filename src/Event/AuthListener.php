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
namespace Wasabi\Core\Event;

use Cake\Database\Expression\QueryExpression;
use Wasabi\Core\Model\Table\UsersGroupsTable;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Wasabi\Core\Wasabi;

class AuthListener implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing.
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => [
                'callable' => 'setupUser',
                'priority' => 1000
            ],
            'Auth.failedLogin' => [
                'callable' => 'onFailedLogin',
                'priority' => 1000
            ]
        ];
    }

    /**
     * Setup the group ids and set the password of the logged in users to store it in the session.
     * Reset failed login attempts.
     *
     * @param Event $event The Auth.afterIdentify event that was fired.
     * @param array $user The user array of the authenticated user.
     * @return array
     */
    public function setupUser(Event $event, $user)
    {
        /** @var UsersGroupsTable $UsersGroups */
        $UsersGroups = TableRegistry::getTableLocator()->get('Wasabi/Core.UsersGroups');

        // setup the group ids for the given user
        $user['group_id'] = $UsersGroups->getGroupIds($user['id']);

        $user['password_hashed'] = $UsersGroups->Users->get($user['id'], ['fields' => ['password']])->get('password');
        return $user;
    }

    /**
     * On a failed login attempt increase the failed login attempt of the corresponding user and update
     * the last failed login attempt datetime.
     *
     * @param Event $event
     * @param string $clientIp
     * @param string $loginField
     * @param string $loginFieldValue
     */
    public function onFailedLogin(Event $event, $clientIp, $loginField, $loginFieldValue)
    {
        $recognitionTime = Wasabi::setting('Core.Auth.failed_login_recognition_time');
        $maxLoginAttempts = Wasabi::setting('Core.Auth.max_login_attempts');
        $past = (new \DateTime())->modify('-' . $recognitionTime . ' minutes');

        $LoginLogs = TableRegistry::getTableLocator()->get('Wasabi/Core.LoginLogs');

        $loginLog = $LoginLogs->newEntity([
            'client_ip' => $clientIp,
            'login_field' => $loginField,
            'login_field_value' => $loginFieldValue,
            'success' => false
        ]);

        $failedLogins = $LoginLogs->find()
            ->where([
                'client_ip' => $clientIp,
                'success' => false
            ])
            ->andWhere(function (QueryExpression $exp) use ($past) {
                return $exp->gt('created', $past->format('Y-m-d H:i:s'));
            })
            ->count();

        if (($failedLogins + 1) >= $maxLoginAttempts) {
            $loginLog->set('blocked', true);
        }

        $LoginLogs->save($loginLog);
    }
}
