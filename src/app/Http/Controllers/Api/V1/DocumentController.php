<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentCollection;
use App\Http\Resources\V1\DocumentResource;
use App\Models\Document;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return DocumentCollection
     */
    public function index(): DocumentCollection
    {
        return new DocumentCollection(
            Auth::user()->documents()
                ->orderByDesc('created_at')
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return DocumentResource
     */
    public function store(Request $request): DocumentResource
    {
        $document = Document::create($request->all());

        return new DocumentResource($document);
    }

    /**
     * Display the specified resource.
     *
     * @param Document $document
     * @return DocumentResource|JsonResponse
     */
    public function show(Document $document)
    {
        if ($document->belongsTo(Auth::user())) {
            return new DocumentResource($document);
        }

        return Response::json([], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Document $document
     * @return DocumentResource|JsonResponse
     */
    public function update(Request $request, Document $document)
    {
        if ($document->belongsTo(Auth::user())) {
            $document->update($request->all());
            return new DocumentResource($document);
        }

        return Response::json([], 404);
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
            if ($document->belongsTo(Auth::user())) {
                $result = (bool) Document::destroy($document->id);
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        } finally {
            return Response::json([
                'data' => $result ?? false
            ]);
        }
    }
}
