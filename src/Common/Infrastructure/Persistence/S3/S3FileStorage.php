<?php

namespace Jefero\Bot\Common\Infrastructure\Persistence\S3;

use Jefero\Bot\Common\Domain\Entity\FileInfo;
use Jefero\Bot\Common\DTO\S3Object;
use Aws\Result;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class S3FileStorage
{
    public const BUCKET_DOCUMENT = 'ads-photo';
    public const BUCKET_TEMP = 'temp';
    public const BUCKET_AVATAR = 'customer-avatar';
    public const BUCKETS_FOR_AUTO_CREATE = [
        self::BUCKET_DOCUMENT => true,
        self::BUCKET_TEMP => true,
        self::BUCKET_AVATAR => true,
    ];

    private S3Client $s3Client;
    private bool $isTempSaving = false;

    public function __construct(
        string $host,
        string $key,
        string $secret,
        string $tempSaving
    ) {

        $this->s3Client = new S3Client(
            [
                'region' => 'us-east-1',
                'bucket' => '',
                'version' => 'latest',
                'endpoint' => $host,
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ]
            ]
        );

        if ($tempSaving === '1') {
            $this->isTempSaving = true;
        }
    }

    /**
     * @param FileInfo $fileInfo
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws FileUploadWasFailedException
     */
    public function uploadFile(FileInfo $fileInfo, UploadedFile $uploadedFile): string
    {
        $result = $this->putObject($fileInfo->getPath(), $uploadedFile->getContent(), $fileInfo::getBucket());

        $objectUrl = $result->get('ObjectURL');
        if (!is_string($objectUrl)) {
            throw new FileUploadWasFailedException();
        }

        return $objectUrl;
    }

    /**
     * @throws FileUploadWasFailedException
     */
    public function uploadImage(FileInfo $fileInfo, UploadedFile $uploadedFile): string
    {
        $bucket = $this->isTempSaving ? self::BUCKET_TEMP : $fileInfo::getBucket();
        $result = $this->putObject($fileInfo->getPath(), $uploadedFile->getContent(), $bucket);

        $objectUrl = $result->get('ObjectURL');
        if (!is_string($objectUrl)) {
            throw new FileUploadWasFailedException();
        }

        return $objectUrl;
    }

    /**
     * @param FileInfo $fileInfo
     * @return void
     */
    public function removeFile(FileInfo $fileInfo): void
    {
        $this->deleteObject($fileInfo->getPath(), $fileInfo::getBucket());
    }

    public function getObject(string $bucket, string $key): S3Object
    {
        try {
            $result = $this->s3Client->getObject(
                [
                    'Bucket' => $bucket,
                    'Key' => $key,
                ]
            );
        } catch (\Exception $exception) {
            try {
                $result = $this->s3Client->getObject(
                    [
                        'Bucket' => self::BUCKET_TEMP,
                        'Key' => $key,
                    ]
                );
            } catch (\Exception $exception) {
                throw new FileNotFoundException();
            }
        }

        return new S3Object(
            [
                'body' => (string)$result->get('Body'),
                'contentType' => (string)$result->get('ContentType'),
            ]
        );
    }

    public function getAllFileKeys(string $bucket): array
    {
        $keys = [];

        $result = $this->s3Client->listObjects(
            [
                'Bucket' => $bucket
            ]
        );

        if (!empty($result['Contents']) && is_array($result['Contents'])) {
            foreach ($result['Contents'] as $content) {
                if (!empty($content['Key'])) {
                    $keys[] = $content['Key'];
                }
            }
        }

        return $keys;
    }

    public function putObject(string $key, string $body, string $bucket): Result
    {
        return $this->s3Client->putObject(
            [
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $body,
            ]
        );
    }

    public function deleteObject(string $key, string $bucket): void
    {
        $this->s3Client->deleteObject(
            [
                'Bucket' => $bucket,
                'Key' => $key,
            ]
        );
    }

    public function listBuckets(): array
    {
        $response = $this->s3Client->listBuckets();
        if (empty($response['Buckets'])) {
            return [];
        }

        $result = [];
        foreach ($response['Buckets'] as $bucket) {
            $result[$bucket['Name']] = true;
        }

        return $result;
    }

    public function createBucket(string $name): void
    {
        $this->s3Client->createBucket(
            [
                'Bucket' => $name
            ]
        );
    }

    public static function getVehiclePhotoFullUrl(string $path): string
    {
        return $_ENV['IMAGE_VEHICLE_PHOTO_BASE_URL'] . $path;
    }

    public static function getCustomerAvatarFullUrl(string $path): string
    {
        return $_ENV['IMAGE_CUSTOMER_AVATAR_BASE_URL'] . $path;
    }
}
