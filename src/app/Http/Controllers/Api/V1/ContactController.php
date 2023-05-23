<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use App\Http\Resources\V1\ContactCollection;
use App\Http\Resources\V1\ProfileResource;
use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ContactController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        try {
            return $this->successResponse(new ContactCollection(Contact::all()), '');

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
            $contact = Contact::create($request->all());

            return $this->successResponse(
                new ProfileResource($contact),
                __('Contact successfully created.'),
                [],
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {

            Contact::destroy($id);

            return $this->successResponse(
                null,
                __('Message deleted.')
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
