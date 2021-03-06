<?php

namespace Vision;

use Vision\Annotation\ImageContext;
use Vision\Request\Image\ImageInterface;
use Vision\Request\VisionRequest;
use Vision\Response\AnnotateImageResponse;

class Vision
{
    const RESPONSE_TYPE_JSON = 'JSON';
    const RESPONSE_TYPE_OBJECT = 'OBJECT';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $features = [];

    /**
     * @var VisionRequest
     */
    protected $visionRequest;

    /**
     * @var ImageContext
     */
    protected $imageContext;

    /**
     * @var string
     */
    protected $version;

    /**
     * @param string $apiKey
     * @param Feature[] $features
     * @param ImageContext|null $imageContext
     * @param string $version
     */
    public function __construct(
        $apiKey,
        array $features = [],
        ImageContext $imageContext = null,
        $version = VisionRequest::VISION_VERSION
    ) {
        $this->apiKey = $apiKey;
        $this->version = $version;
        $this->setFeatures($features);
        $this->setImageContext($imageContext);
    }

    /**
     * @param ImageInterface $image
     * @param string $responseType
     * @return string|AnnotateImageResponse
     */
    public function request(
        ImageInterface $image,
        $responseType = self::RESPONSE_TYPE_OBJECT
    ) {
        $this->visionRequest = new VisionRequest($this->apiKey, $image, $this->features, $this->imageContext);
        $this->visionRequest->setVersion($this->version);
        $this->visionRequest->send();

        return $this->getResponseForType($responseType);
    }

    /**
     * @param string $responseType
     * @return AnnotateImageResponse|string
     */
    public function getResponseForType($responseType)
    {
        return $responseType === self::RESPONSE_TYPE_JSON
            ? $this->visionRequest->getRawResponse()
            : $this->visionRequest->getAnnotateImageResponse();
    }

    /**
     * @return Feature[]
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param Feature[] $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * @param Feature $feature
     */
    public function addFeature($feature)
    {
        $this->features[] = $feature;
    }

    /**
     * @return ImageContext
     */
    public function getImageContext()
    {
        return $this->imageContext;
    }

    /**
     * @param ImageContext $imageContext
     */
    public function setImageContext($imageContext)
    {
        $this->imageContext = $imageContext ?: new ImageContext;
    }

    /**
     * @deprecated
     *
     * @param Image $image
     * @param string $responseType
     * @return string|AnnotateImageResponse
     */
    public function getRequest(Image $image, $responseType = self::RESPONSE_TYPE_OBJECT)
    {
        return $this->request($image, $responseType);
    }
}
