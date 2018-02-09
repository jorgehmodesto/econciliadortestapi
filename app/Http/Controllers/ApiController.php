<?php

namespace App\Http\Controllers;

use App\Commit;
use App\Helpers\ApiMessageFilter;
use App\Helpers\ApiRequestValidator;
use App\Owner;
use App\Repository;
use GuzzleHttp\Client;


/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * ApiController constructor.
     */
    public function __construct()
    {}

    /**
     * @param $owner
     * @param $repo
     * @return \Illuminate\Http\JsonResponse
     */
    public function import($owner, $repo)
    {
        try {
            $baseUri = env('GITHUB_BASEURI_API');
            $url = "{$baseUri}repos/{$owner}/{$repo}/commits";
            $monthsBefore = env('GITHUB_HISTORY_LIMIT_BY_MONTH');
            $sinceDate = date('Y-m-d', mktime(0,0,0,date('m') - $monthsBefore,date('d'), date('Y')));

            $client = new Client([
                'base_uri' => $baseUri, [
                    'since' => $sinceDate
                ]
            ]);

            $response = $client->request('GET', $url);

            $responseBodyContents = $response->getBody()->getContents();
            $aResponseBodyContents = \GuzzleHttp\json_decode($responseBodyContents);

            $commitsModel = new Commit();
            $commitsModel->importRecords($aResponseBodyContents, $owner, $repo);

            return response()
                ->json([
                    'success' => true
                ]);
        } catch(\Exception $e) {

            $apiMessageFilter = new ApiMessageFilter();
            $message = $apiMessageFilter->exception($e);

            return response()
                ->json([
                    'success' => false,
                    'message' => $message
                ]);
        }
    }

    public function export($owner, $repo)
    {
        try {

            $owner = Owner::where('username', $owner)->first();

            if(empty($owner)) {
                throw new \Exception('not_found');
            }

            $repository = Repository::where([
                'name' => $repo,
                'owner_id' => $owner->id
            ])->first();

            if(empty($repository)) {
                throw new \Exception('not_found');
            }

            $commitsRepository = new Commit();

            $records = $commitsRepository->exportRecords($repository, $owner);

            return response()
                ->json([
                    'success' => true,
                    'records' => $records
                ]);
        } catch(\Exception $e) {
            return response()
                ->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        }
    }
}