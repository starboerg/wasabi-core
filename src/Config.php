<?php
/**
 * Wasabi Core Plugin Config
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
namespace Wasabi\Core;

use Cake\Core\Configure;

class Config
{
    /**
     * The global Event priority for all Wasabi Core EventListners.
     *
     * @var integer
     */
    public static $priority = 1000;

    /**
     * Get the instance name of this wasabi instance.
     *
     * @return string
     */
    public static function getInstanceName()
    {
        return Configure::read('Settings.Core.instance_name');
    }

    /**
     * Get the email address that is used as sender for
     * all backend emails.
     *
     * @return string
     */
    public static function getEmailSender()
    {
        return Configure::read('Settings.Core.Email.email_sender');
    }

    public static function getActivationEmailSubject()
    {
        return Configure::read('Settings.Core.Email.Activation.subject');
    }

    public static function getVerifiedByAdminEmailSubject()
    {
        return Configure::read('Settings.Core.Email.Verification.subject_admin');
    }
}
