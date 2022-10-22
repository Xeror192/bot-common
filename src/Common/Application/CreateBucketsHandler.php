<?php

namespace Jefero\Bot\Common\Application;

use Jefero\Bot\Common\Infrastructure\Persistence\S3\S3FileStorage;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBucketsHandler
{
    private S3FileStorage $s3FileStorage;

    public function __construct(S3FileStorage $s3FileStorage)
    {
        $this->s3FileStorage = $s3FileStorage;
    }

    public function handle(OutputInterface $output): void
    {
        $buckets = $this->s3FileStorage->listBuckets();
        foreach (S3FileStorage::BUCKETS_FOR_AUTO_CREATE as $bucketName => $bool) {
            if (!isset($buckets[$bucketName])) {
                $this->s3FileStorage->createBucket($bucketName);
                $output->writeln('created bucket: ' . $bucketName);
            }
        }
    }
}
