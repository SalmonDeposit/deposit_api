<?php

namespace App\Services;

use App\Models\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

class AzureStorage
{
    /**
     * @param Request $request
     * @param string $inputFileName
     * @return Document
     * @throws Exception
     */
    public function uploadRequestedFile(Request $request, string $inputFileName): Document
    {
        $fileName  = (string) $request->$inputFileName->getClientOriginalName();
        $mimeType  = (string) $request->$inputFileName->getClientMimeType();
        $fileSize  = (int) $request->$inputFileName->getSize();

        $targetPath = Auth::id() . '/' . $fileName;

        $connectionString = (string) env('AZURE_STORAGE_CONNECTION_STRING');
        $container = (string) env('AZURE_STORAGE_CONTAINER');

        // Connect to Azure SDK
        $client = BlobRestProxy::createBlobService($connectionString);
        // Setup options
        $options = new CreateBlockBlobOptions();
        $options->setContentType($mimeType);

        // Upload file to Azure
        $result = $client->createBlockBlob(
            $container,
            $targetPath,
            $request->$inputFileName->getContent(), $options
        );

        if (!$result->getRequestServerEncrypted())
            throw new Exception(__('Unable to upload file.'));

        // Retrieve blob URL
        $url = $client->getBlobUrl($container, $targetPath);

        return Document::create([
            'name' => $fileName,
            'type' => $mimeType,
            'storage_link' => $url,
            'size' => $fileSize
        ]);
    }

    /**
     * Upload extracts to Azure Storage using the AZURE_STORAGE_EXTRACT_CONTAINER container
     *
     * @param string $filePath
     * @param string $filename
     * @throws Exception
     * @return void
     */
    public function uploadExtract(string $filePath, string $filename): void
    {
        $file = new File($filePath, true);

        $connectionString = (string) env('AZURE_STORAGE_CONNECTION_STRING');
        $container = (string) env('AZURE_STORAGE_EXTRACT_CONTAINER');

        $client = BlobRestProxy::createBlobService($connectionString);

        $options = new CreateBlockBlobOptions();
        $options->setContentType($file->getMimeType());

        $result = $client->createBlockBlob($container, $filename, $file->getContent(), $options);

        if (!$result->getRequestServerEncrypted())
            throw new Exception('Unable to upload file ' . $filename);
    }
}
