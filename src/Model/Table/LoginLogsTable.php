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

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Table;
use Wasabi\Core\Wasabi;

/**
 * Class LoginLogsTable
 */
class LoginLogsTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    /**
     * Check if the given $clientIp address is blocked from logging in.
     *
     * @param string $clientIp
     * @return bool
     */
    public function ipIsBlocked($clientIp)
    {
        $blockTime = Wasabi::setting('Core.Auth.block_time');
        $blockEnd = (new \DateTime())->modify('-' . $blockTime . ' minutes');

        return (bool)$this->find()
            ->where([
                'blocked' => true,
                'client_ip' => $clientIp
            ])
            ->andWhere(function (QueryExpression $exp) use ($blockEnd) {
                return $exp->gt('created', $blockEnd->format('Y-m-d H:i:s'));
            })
            ->count();
    }
}
