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
use Cake\Validation\Validator;
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

    public function validationDefault(Validator $validator) {
        return $validator
            ->notEmpty('name', __d('wasabi_core', 'Please enter the name of the language.'))
            ->notEmpty('iso2', __d('wasabi_core', 'Please enter the ISO 639-1 code of the language (e.g. "en")'))
            ->add('iso2', 'lengthBetween', [
                'rule' => ['lengthBetween', 2, 2],
                'message' => __d('wasbai_core', 'The ISO 639-1 code must consist of exactly 2 characters.')
            ])
            ->notEmpty('iso3', __d('wasabi_core', 'Please enter the ISO 639-2/T code of the language (e.g. "eng")'))
            ->add('iso3', 'lengthBetween', [
                'rule' => ['lengthBetween', 3, 3],
                'message' => __d('wasbai_core', 'The ISO 639-2/T code must consist of exactly 3 characters.')
            ])
            ->notEmpty('lang', __d('wasabi_core', 'Please enter the HTML lang code of the language (e.g. "en_US")'))
            ->add('lang', 'lengthBetween', [
                'rule' => ['lengthBetween', 2, 5],
                'message' => __d('wasbai_core', 'The HTML lang code must consist of 2 to 5 characters.')
            ]);
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
