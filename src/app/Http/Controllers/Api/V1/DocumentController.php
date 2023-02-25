<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\DocumentCollection;
use App\Http\Resources\V1\DocumentResource;
use App\Models\Document;
use App\Filters\V1\DocumentQueryFilter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

class DocumentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filter = new DocumentQueryFilter();
            $filterItems = $filter->transform($request);

            $documents = Auth::user()->documents();

            if (!empty($filterItems)) {
                $documents->where($filterItems);
            }

            return $this->successResponse(
                new DocumentCollection(
                    $documents->orderByDesc('created_at')
                        ->paginate()
                        ->appends($request->query())
                )
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if (!$request->hasFile('document'))
                throw new Exception(__('No file named "document" found in the request.'));

            $fileName  = (string) $request->document->getClientOriginalName();
            $mimeType  = (string) $request->document->getClientMimeType();
            $fileSize  = (int) $request->document->getMaxFilesize();

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
                $request->document->getContent(), $options
            );

            if (!$result->getRequestServerEncrypted())
                throw new Exception(__('Unable to upload file to Azure Storage'));

            // Retrieve blob URL
            $url = $client->getBlobUrl($container, $targetPath);

            $document = Document::create([
                'name' => $fileName,
                'type' => $mimeType,
                'storage_link' => $url,
                'size' => $fileSize
            ]);

            return $this->successResponse(
                new DocumentResource($document),
                __('Document successfully stored.'),
                [],
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Document $document
     * @return JsonResponse
     */
    public function show(Document $document): JsonResponse
    {
        try {
            if (!$document->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            return $this->successResponse(new DocumentResource($document));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Document $document
     * @return JsonResponse
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        try {
            if (!$document->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            $document->update($request->all());

            return $this->successResponse(
                new DocumentResource($document),
                __('Document successfully updated.'),
                []
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Document $document
     * @return JsonResponse
     */
    public function destroy(Document $document): JsonResponse
    {
        try {
            if (!$document->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            Document::destroy($document->id);

            return $this->successResponse(
                null,
                __('Document deleted.')
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
