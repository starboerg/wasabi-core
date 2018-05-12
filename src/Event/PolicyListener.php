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

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Wasabi\Core\Model\Entity\Group;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Policy\GroupPolicy;
use Wasabi\Core\Policy\PolicyManager;
use Wasabi\Core\Policy\UserPolicy;

class PolicyListener implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Wasabi.Policies.register' => [
                'callable' => 'registerPolicies',
                'priority' => 1000
            ]
        ];
    }

    /**
     * Register all policies.
     *
     * @param Event $event An event instance.
     * @return void
     */
    public function registerPolicies(Event $event)
    {
        /** @var PolicyManager $policyManager */
        $policyManager = $event->getSubject();

        $policyManager
            ->addPolicy(User::class, UserPolicy::class)
            ->addPolicy(Group::class, GroupPolicy::class);
    }
}
