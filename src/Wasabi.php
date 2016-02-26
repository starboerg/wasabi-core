<?php

namespace Wasabi\Core;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Wasabi\Cms\Model\Entity\Page;
use Wasabi\Cms\View\Theme\Theme;
use Wasabi\Cms\View\Theme\ThemeManager;
use Wasabi\Core\Model\Entity\Language;
use Wasabi\Core\Model\Entity\User;
use Wasabi\Core\Model\Table\LanguagesTable;

class Wasabi
{
    /**
     * Holds the currently logged in user.
     *
     * @var User
     */
    protected static $_user;

    /**
     * Holds the current page entity.
     *
     * @var Page
     */
    protected static $_page;

    /**
     * Get the currently active content language.
     *
     * @return Language
     */
    public static function contentLanguage()
    {
        return Configure::read('contentLanguage');
    }

    /**
     * Get or set the currently logged in user.
     *
     * @param User $user
     * @return User
     */
    public static function user($user = null)
    {
        if ($user !== null) {
            self::$_user = $user;
        }
        if (!self::$_user) {
            return null;
        }
        return self::$_user;
    }

    /**
     * Get or set the current page.
     *
     * @param null $page
     * @return null|Page
     */
    public static function page($page = null)
    {
        if ($page !== null) {
            self::$_page = $page;
        }
        return self::$_page;
    }

    /**
     * Get the theme instance.
     *
     * @return Theme
     */
    public static function getTheme()
    {
        return ThemeManager::theme(Wasabi::setting('Cms.Theme.id'));
    }

    /**
     * Get a setting stored in Wasabi Settings.
     *
     * @param string $path the array path to access the setting, using Hash::get() syntax
     * @param null|mixed $default a default value to return when the setting is not found
     * @return mixed|null
     */
    public static function setting($path, $default = null)
    {
        $setting = Configure::read('Settings.' . $path);

        if ($setting === null) {
            return $default;
        }

        return $setting;
    }

    /**
     * Load and setup all languages.
     *
     * @param null $langId If set, then the language with id = $langId will be set as the content language.
     * @param bool|false $backend If true also initializes all backend languages
     */
    public static function loadLanguages($langId = null, $backend = false)
    {
        // Configure all available frontend and backend languages.
        $languages = Cache::remember('languages', function() {
            /** @var LanguagesTable $Languages */
            $Languages = TableRegistry::get('Wasabi/Core.Languages');
            $langs = $Languages->find('allFrontendBackend')->all();

            return [
                'frontend' => array_values($Languages->filterFrontend($langs)->toArray()),
                'backend' => array_values($Languages->filterBackend($langs)->toArray())
            ];
        }, 'wasabi/core/longterm');
        Configure::write('languages', $languages);

        if ($backend === true) {
            // Backend
            $request = Router::getRequest();

            // Setup the users backend language.
            $backendLanguage = $languages['backend'][0];
            if ($request->session()->check('Auth.User.language_id')) {
                $backendLanguageId = $request->session()->read('Auth.User.language_id');

                if ($backendLanguageId !== null) {
                    foreach ($languages['backend'] as $lang) {
                        if ($lang->id === $backendLanguageId) {
                            $backendLanguage = $lang;
                            break;
                        }
                    }
                }
            }

            // Setup the users content language.
            $contentLanguage = $languages['frontend'][0];
            if ($request->session()->check('contentLanguageId')) {
                $contentLanguageId = $request->session()->read('contentLanguageId');
                foreach ($languages['frontend'] as $lang) {
                    if ($lang->id === $contentLanguageId) {
                        $contentLanguage = $lang;
                        break;
                    }
                }
            }
            Configure::write('contentLanguage', $contentLanguage);
            Configure::write('backendLanguage', $backendLanguage);
            I18n::locale($backendLanguage->iso2);
        } else {
            // Frontend
            if ($langId !== null) {
                foreach ($languages['frontend'] as $frontendLanguage) {
                    if ($frontendLanguage->id === $langId) {
                        Configure::write('contentLanguage', $frontendLanguage);
                        I18n::locale($frontendLanguage->iso2);
                        break;
                    }
                }
            } else {
                Configure::write('contentLanguage', $languages['frontend'][0]);
                I18n::locale($languages['frontend'][0]->iso2);
            }
        }
    }

    /**
     * Get the instance name of this wasabi instance.
     *
     * @return string
     */
    public static function getInstanceName()
    {
        return self::setting('Core.instance_name');
    }

    /**
     * Get the html title suffix of this wasabi instance.
     * @return string
     */
    public static function getHtmlTitleSuffix()
    {
        return self::setting('Core.html_title_suffix');
    }

    /**
     * Get the plugin controller action url array from the request.
     *
     * @return array
     */
    public static function getCurrentUrlArray()
    {
        $request = Router::getRequest();

        return [
            'plugin' => $request->params['plugin'],
            'controller' => $request->params['controller'],
            'action' => $request->params['action']
        ];
    }

    /**
     * Get the email address that is used as sender for
     * all backend emails.
     *
     * @return string
     */
    public static function getSenderEmail()
    {
        return self::setting('Core.Email.email_sender');
    }

    /**
     * Get the name that is used as sender for
     * all backend emails.
     *
     * @return string
     */
    public static function getSenderName()
    {
        return self::setting('Core.Email.email_sender_name');
    }
}
