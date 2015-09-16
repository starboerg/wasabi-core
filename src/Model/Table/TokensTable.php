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
namespace Wasabi\Core\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use DateTime;
use Wasabi\Core\Model\Entity\Token;

/**
 * Class TokensTable
 * @package Wasabi\Core\Model\Table
 */
class TokensTable extends Table
{

    const TYPE_EMAIL_VERIFICATION = 'email_verification';
    const TYPE_LOST_PASSWORD = 'lost_password';

    /**
     * Holds the configuration for all token types
     * and tells how long a token is valid to be used.
     *
     * @var array
     */
    public $timeToLive = [
        self::TYPE_EMAIL_VERIFICATION => '+2 days',
        self::TYPE_LOST_PASSWORD => '+2 days'
    ];

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->belongsTo('Users', [
            'className' => 'Wasabi/Core.Users'
        ]);

        $this->addBehavior('Timestamp');
    }


    /**
     * Check if a token already exists.
     *
     * @param $token
     * @return Token The Token Entity or an empty Entity if none is found
     */
    public function tokenExists($token)
    {
        return $this->find()
            ->where([
                $this->alias() . '.token' => $token
            ])
            ->first();
    }

    /**
     * Generate a new unique token for an existing user.
     *
     * @param Entity $user
     * @param string $tokenType The type of token to be generated. Always make sure to supply this param as a constant
     *                                                             e.g. Token::TYPE_EMAIL_VERIFICATION
     * @return string A 32 character long randomized token
     */
    public function generateToken(Entity $user, $tokenType)
    {
        if (!isset($this->timeToLive[$tokenType])) {
            user_error('Please specify a timeToLive for the tokenType "' . $tokenType . '" at Token::$timeToLive.', E_USER_ERROR);
        }

        do {
            $token = md5(microtime(true) . Configure::read('Security.salt') . $user->get('id') . $user->get('email'));
        } while ((boolean)$this->tokenExists($token));

        $this->save(
            new Entity([
                'user_id' => $user->get('id'),
                'token' => $token,
                'token_type' => $tokenType,
                'expires' => new DateTime($this->timeToLive[$tokenType])
            ])
        );

        return $token;
    }

    /**
     * Check if the supplied token is valid and exists in the database.
     *
     * @param string $token
     * @return array|Entity The Token Entity if it is valid and exists, an empty array otherwise.
     */
    public function isValid($token)
    {
        if (strlen($token) !== 32) {
            return false;
        }
        return $this->tokenExists($token);
    }

    /**
     * Mark a token as "used".
     *
     * @property Token $token
     * @return EntityInterface
     */
    public function useToken($token)
    {
        $token->used = true;
        return $this->save($token);
    }
}
