<?php

namespace App\Utilities;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Elasticsearch;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    public function storeEmails(array $emails): void
    {
        $apiParams = [];

        $batchLimit = (int)config('elasticsearch.bulk_index_batch_limit');

        foreach ($emails as $index => $email) {
            $apiParams = $this->updateBulkIndexParamsWithEmailDetails($email, $apiParams);

            if (($index + 1) % $batchLimit == 0) {
                $this->bulkIndex($apiParams);

                $apiParams = [];
            }
        }

        if (!empty($params['body'])) {
            $this->bulkIndex($apiParams);
        }
    }

    private function updateBulkIndexParamsWithEmailDetails(
        array $email,
        array $apiParams
    ): array {
        $apiParams['body'][] = [
            'index' => [
                '_index' => config('elasticsearch.default_index'),
                '_id' => $email['email_address']
            ]
        ];

        $apiParams['body'][] = $email;

        return $apiParams;
    }

    private function bulkIndex(array $params): void
    {
        try {
            Elasticsearch::bulk($params);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
