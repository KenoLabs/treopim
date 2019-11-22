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

namespace Pim\Controllers;

use Espo\Core\Exceptions;
use Slim\Http\Request;

/**
 * Attribute controller
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class Attribute extends AbstractController
{

    /**
     * @ApiDescription(description="Get filters data for product entity")
     * @ApiMethod(type="GET")
     * @ApiRoute(name="/Markets/Attribute/filtersData")
     * @ApiReturn(sample="'json'")
     *
     * @param array   $params
     * @param array   $data
     * @param Request $request
     *
     * @return bool
     * @throws Exceptions\BadRequest
     * @throws Exceptions\Error
     * @throws Exceptions\Forbidden
     */
    public function actionGetFiltersData($params, $data, Request $request): array
    {
        if ($this->isReadAction($request, $params)) {
            return $this->getRecordService()->getFiltersData();
        }

        throw new Exceptions\Error();
    }
}
