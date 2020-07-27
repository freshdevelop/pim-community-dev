<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2020 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Automation\DataQualityInsights\Infrastructure\Persistence\Query\ProductEvaluation;

use Akeneo\Pim\Automation\DataQualityInsights\Domain\Query\ProductEvaluation\HasUpToDateEvaluationQueryInterface;
use Akeneo\Pim\Automation\DataQualityInsights\Domain\ValueObject\ProductId;
use Doctrine\DBAL\Connection;

final class HasUpToDateProductModelEvaluationQuery implements HasUpToDateEvaluationQueryInterface
{
    /** @var Connection */
    private $dbConnection;

    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function forProductId(ProductId $productId): bool
    {
        $upToDateProducts = $this->forProductIds([$productId]);

        return !empty($upToDateProducts);
    }

    public function forProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $productIds = array_map(function (ProductId $productId) {
            return $productId->toInt();
        }, $productIds);

        $query = <<<SQL
SELECT product_model.id
FROM pim_catalog_product_model AS product_model
         LEFT JOIN pim_catalog_product_model AS parent ON parent.id = product_model.parent_id
WHERE product_model.id IN (:product_ids)
  AND EXISTS(
        SELECT 1 FROM pimee_data_quality_insights_product_model_criteria_evaluation AS evaluation
        WHERE evaluation.product_id = product_model.id
          AND evaluation.evaluated_at >=
              IF(parent.updated > product_model.updated, parent.updated, product_model.updated)
    )
SQL;

        $stmt = $this->dbConnection->executeQuery(
            $query,
            ['product_ids' => $productIds],
            ['product_ids' => Connection::PARAM_INT_ARRAY]
        );

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_array($result)) {
            return [];
        }

        return array_map(function ($resultRow) {
            return new ProductId(intval($resultRow['id']));
        }, $result);
    }
}
