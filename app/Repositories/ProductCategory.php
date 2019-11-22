<?php
/**
 * Pim
 * Free Extension
 * Copyright (c) TreoLabs GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Pim\Repositories;

use Espo\Core\Templates\Repositories\Base;
use Espo\ORM\Entity;

/**
 * Class ProductCategory
 *
 * @author r.ratsun@treolabs.com
 */
class ProductCategory extends Base
{
    /**
     * @inheritDoc
     */
    public function beforeSave(Entity $entity, array $options = [])
    {
        // call parent action
        parent::beforeSave($entity, $options);

        // set sort order
        if (is_null($entity->get('sorting'))) {
            $entity->set('sorting', (int)$this->max('sorting') + 1);
        }

        if (!$entity->isNew() && $entity->isAttributeChanged('sorting')) {
            $this->updateSortOrder($entity);
        }
    }

    /**
     * @inheritDoc
     */
    public function max($field)
    {
        $data = $this
            ->getEntityManager()
            ->nativeQuery("SELECT MAX(sorting) AS max FROM product_category WHERE deleted=0")
            ->fetch(\PDO::FETCH_ASSOC);

        return $data['max'];
    }

    /**
     * @param Entity $entity
     */
    protected function updateSortOrder(Entity $entity): void
    {
        $data = $this
            ->select(['id'])
            ->where(
                [
                    'id!='       => $entity->get('id'),
                    'sorting>='  => $entity->get('sorting'),
                    'categoryId' => $entity->get('categoryId')
                ]
            )
            ->order('sorting')
            ->find()
            ->toArray();

        if (!empty($data)) {
            // create max
            $max = $entity->get('sorting');

            // prepare sql
            $sql = '';
            foreach ($data as $row) {
                // increase max
                $max++;

                // prepare id
                $id = $row['id'];

                // prepare sql
                $sql .= "UPDATE product_category SET sorting='$max' WHERE id='$id';";
            }

            // execute sql
            $this->getEntityManager()->nativeQuery($sql);
        }
    }
}
