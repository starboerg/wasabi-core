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

use Cake\Collection\Iterator\FilterIterator;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Wasabi\Core\Model\Entity\Language;

class LanguagesTable extends Table
{
    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config Configuration options passed to the constructor
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
    }

    /**
     * Find all frontend and backend languages.
     *
     * @param Query $query
     * @param array $options
     * @return $this
     */
    public function findAllFrontendBackend(Query $query, array $options) {
        return $query
            ->select([
                'id',
                'name',
                'iso2',
                'iso3',
                'lang',
                'available_at_frontend',
                'available_at_backend',
                'position'
            ])
            ->order(['position ASC']);
    }

    /**
     * Filter the provided result set by languages that are available at the frontend.
     *
     * @param ResultSetInterface $results
     * @return FilterIterator
     */
    public function filterFrontend(ResultSetInterface $results) {
        return $results->filter(function(Language $language) {
            return $language->available_at_frontend === true;
        });
    }

    /**
     * Filter the provided result set by languages that are available at the backend.
     *
     * @param ResultSetInterface $results
     * @return FilterIterator
     */
    public function filterBackend(ResultSetInterface $results) {
        return $results->filter(function(Language $language) {
            return $language->available_at_backend === true;
        });
    }
}
