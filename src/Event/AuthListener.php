<?php

namespace Wasabi\Core\Event;

use Wasabi\Core\Model\Table\UsersGroupsTable;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

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
            ]
        ];
    }

    /**
     * Setup the group ids and set the password of the logged in users.
     *
     * @param Event $event The Auth.afterIdentify event that was fired.
     * @param array $user The user array of the authenticated user.
     *
     * @return array
     */
    public function setupUser(Event $event, $user)
    {
        $UsersGroups = TableRegistry::get('Wasabi/Core.UsersGroups');

        if (Configure::read('Wasabi.User.belongsToManyGroups')) {
            /** @var UsersGroupsTable $UsersGroups */
            $user['group_id'] = $UsersGroups->getGroupIds($user['id']);
        }

        $user['password_hashed'] = $UsersGroups->Users->get($user['id'], ['fields' => ['password']])->get('password');
        return $user;
    }
}
