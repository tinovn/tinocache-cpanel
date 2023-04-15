<?php
namespace tinocachePlugin\Model\Api;

abstract class AbstractApi
{

    /**
     *
     * @var \tinocachePlugin\Model\Api\AbstractDetailsProvider $detailsProvider
     */
    public $detailsProvider;

    /**
     *
     * @var \tinocachePlugin\Model\Api\ResponseValidator $responseValidator
     */
    private $responseValidator;

    /**
     *
     * @var \tinocachePlugin\Model\Api\AbstractCurl $curl
     */
    private $curl;

    /**
     *
     * @param \tinocachePlugin\Model\Api\AbstractDetailsProvider $detailsProvider
     * @param \tinocachePlugin\Model\Api\ResponseValidator $responseValidator
     * @param \tinocachePlugin\Model\Api\AbstractCurl $curl
     */
    public function __construct(AbstractDetailsProvider $detailsProvider, ResponseValidator $responseValidator, AbstractCurl $curl)
    {
        $this->detailsProvider = $detailsProvider;
        $this->responseValidator = $responseValidator;
        $this->curl = $curl;
    }

    /**
     * Communicate with Api facade
     * @param array $data
     * @return object
     */
    public function request($data = '', $errors = false)
    {
        $response = $this->curl->exec($data, $this);

        $this->responseValidator->validate(json_decode($response), $errors);

        return $this->handleResponse(json_decode($response));
    }
}
