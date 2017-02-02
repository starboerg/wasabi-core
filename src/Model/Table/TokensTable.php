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
namespace Wasabi\Core\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use DateTime;
use Wasabi\Core\Model\Entity\Token;
use Wasabi\Core\Model\Entity\User;

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
     * @param array $config Configuration options passed to the constructor.
     * @return void
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
     * @param string $token The token to check for.
     * @param bool $returnQuery Whether to return or execute the query.
     * @return Query The Token Query or an empty Entity if none is found
     */
    public function tokenExists($token, $returnQuery = false)
    {
        $query = $this->find()
            ->where([
                $this->alias() . '.token' => $token
            ]);
        if ($returnQuery) {
            return $query;
        }
        return $query->first();
    }

    /**
     * Generate a new unique token for an existing user.
     *
     * @param User $user The user to generate a token for.
     * @param string $tokenType The type of token to be generated. Always make sure to supply this param as a constant
     *                                                             e.g. Token::TYPE_EMAIL_VERIFICATION
     * @return string A 32 character long randomized token
     */
    public function generateToken(User $user, $tokenType)
    {
        if (!isset($this->timeToLive[$tokenType])) {
            user_error('Please specify a timeToLive for the tokenType "' . $tokenType . '" at Token::$timeToLive.', E_USER_ERROR);
        }

        do {
            $token = md5(microtime(true) . Configure::read('Security.salt') . $user->get('id') . $user->get('email'));
        } while ((bool)$this->tokenExists($token));

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
     * @param string $token The token to check.
     * @param bool $returnQuery Whether to return or execute the query.
     * @return bool|Query The Token Query if it is valid and exists, an empty array otherwise.
     */
    public function isValid($token, $returnQuery = false)
    {
        if (strlen($token) !== 32) {
            return false;
        }
        return $this->tokenExists($token, $returnQuery);
    }

    /**
     * Find a token entity by the given token string. (including User entity)
     *
     * @param string $token The token to find.
     * @return Token|mixed
     */
    public function findByToken($token)
    {
        /** @var Query $query */
        $query = $this->isValid($token, true);
        if ($query === false) {
            return $query;
        }
        return $query->contain(['Users'])->first();
    }

    /**
     * Mark a token as "used".
     *
     * @param Token $token The token to mark as "used".
     * @return bool|EntityInterface
     */
    public function useToken($token)
    {
        $token->used = true;
        return $this->save($token);
    }

    /**
     * Marks every token of type ($tokenType) for a specific user ($userId)
     * as used (used => true).
     *
     * @param int|string $userId The user id.
     * @param string $tokenType The token type.
     * @return int Number of updated rows.
     */
    public function invalidateExistingTokens($userId, $tokenType)
    {
        return $this->updateAll(
            [
                'used' => true
            ],
            [
                'user_id' => $userId,
                'token_type' => $tokenType
            ]
        );
    }
}
