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

namespace Pim\Services;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Templates\Services\Base;
use Espo\ORM\EntityCollection;

/**
 * Service of Category
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class Category extends Base
{
    /**
     * @var array
     */
    protected $linkSelectParams
        = [
            'categoryImages' => [
                'order' => 'ASC',
                'orderBy' => 'category_image_category.sort_order',
                'additionalColumns' => [
                    'sortOrder' => 'sortOrder',
                    'scope' => 'scope'
                ]
            ]
        ];

    /**
     * Get category entity
     *
     * @param string $id
     *
     * @return array
     * @throws Forbidden
     */
    public function getEntity($id = null)
    {
        // call parent
        $entity = parent::getEntity($id);

        // set hasChildren param
        $entity->set('hasChildren', $entity->hasChildren());

        return $entity;
    }

    /**
     * Is child category
     *
     * @param string $categoryId
     * @param string $selectedCategoryId
     *
     * @return bool
     */
    public function isChildCategory(string $categoryId, string $selectedCategoryId): bool
    {
        // get category
        if (empty($category = $this->getEntityManager()->getEntity('Category', $selectedCategoryId))) {
            return false;
        }

        return in_array($categoryId, explode("|", (string)$category->get('categoryRoute')));
    }

    /**
     * Get id parent category and ids children category
     *
     * @param string $id
     *
     * @return array
     * @throws \Espo\Core\Exceptions\Error
     */
    public function getIdsTree(string $id): array
    {
        /** @var \Pim\Entities\Category $category */
        $category = $this->getEntityManager()->getEntity('Category', $id);

        $categoriesIds = [];
        $categoriesChild = $category->getChildren()->toArray();

        if (!empty($categoriesChild)) {
            $categoriesChild = $category->getChildren()->toArray();
            $categoriesIds = array_column($categoriesChild, 'id');
        }

        $categoriesIds[] = $category->id;

        return $categoriesIds;
    }

    /**
     * Remove ProductCategory by ID category
     *
     * @param string $categoryId
     */
    public function removeProductCategoryByCategory(string $categoryId): void
    {
        $productsCategory = $this
            ->getEntityManager()
            ->getRepository('ProductCategory')
            ->where(['categoryId' => $categoryId])
            ->find()
            ->toArray();

        $serviceProduct = $this->getServiceFactory()->create('ProductCategory');

        foreach ($productsCategory as $productCategory) {
            $serviceProduct->deleteEntity($productCategory['id']);
        }
    }
}
