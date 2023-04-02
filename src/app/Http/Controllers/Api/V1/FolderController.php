<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\DocumentCollection;
use App\Http\Resources\V1\FolderCollection;
use App\Http\Resources\V1\FolderResource;
use App\Models\Folder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            new FolderCollection(Auth::user()->folders)
        );
    }

    public function files(): JsonResponse
    {
        try {
            $folders = Auth::user()->folders->whereNull('folder_id');
            $documents = Auth::user()->documents->whereNull('folder_id');

            return $this->successResponse(
                [],
                '',
                [
                    'documents' => new DocumentCollection($documents),
                    'folders' => new FolderCollection($folders)]
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
            $folder = Folder::create($request->all());

            return $this->successResponse(
                new FolderResource($folder),
                __('Folder successfully created.'),
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
     * @param Folder $folder
     * @return JsonResponse
     */
    public function show(Folder $folder): JsonResponse
    {
        try {
            if (!$folder->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            return $this->successResponse(new FolderResource($folder->load('documents', 'folders')));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Folder $folder
     * @return JsonResponse
     */
    public function update(Request $request, Folder $folder): JsonResponse
    {
        try {
            if (!$folder->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            $folder->update($request->all());

            return $this->successResponse(
                new FolderResource($folder),
                __('Folder successfully updated.'),
                []
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @return JsonResponse
     */
    public function destroy(Folder $folder): JsonResponse
    {
        try {
            if (!$folder->belongsTo(Auth::user()))
                throw new Exception(__('Resource not found.'));

            Folder::destroy($folder->id);

            return $this->successResponse(
                null,
                __('Folder deleted.')
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
