<?php

declare(strict_types=1);

namespace TheBachtiarz\Party\Bmkg\Libraries\Live;

use Illuminate\Http\Client\Response;
use TheBachtiarz\Base\App\Libraries\Curl\CurlInterface;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponse;
use TheBachtiarz\Base\App\Libraries\Curl\Data\CurlResponseInterface;
use TheBachtiarz\Party\Bmkg\Libraries\AbstractBmkgLibrary;
use Throwable;

use function assert;

class EarthQuakeInfo extends AbstractBmkgLibrary implements CurlInterface
{
    // ? Public Methods

    public function execute(array $data = []): CurlResponseInterface
    {
        return $this->setSubUrl('DataMKG/TEWS/autogempa.json')->setBody($data)->get();
    }

    // ? Protected Methods

    protected function customResponse(Response $response): CurlResponseInterface
    {
        $result = new CurlResponse();
        assert($result instanceof CurlResponseInterface);

        try {
            $response = $response->json();

            $response['status']  = 'success';
            $response['message'] = '';
            $response['data']    = $response['Infogempa']['gempa'];

            $result = new CurlResponse($response);
        } catch (Throwable $th) {
            $this->logInstance()->log($th, 'curl');

            $result->setMessage($th->getMessage());
        } finally {
            // $this->logInstance()->log(json_encode($result->toArray()), 'curl');

            return $result;
        }
    }

    // ? Private Methods

    // ? Getter Modules

    // ? Setter Modules
}
