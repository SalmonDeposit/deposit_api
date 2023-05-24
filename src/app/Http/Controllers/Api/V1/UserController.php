<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\UserQueryFilter;
use App\Http\Controllers\ApiController;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Http\Resources\V1\DocumentCollection;
use App\Http\Resources\V1\FolderCollection;
use App\Http\Resources\V1\ProfileCollection;
use App\Http\Resources\V1\SocialCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\UserResource;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Profile;
use App\Models\Social;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends ApiController
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
            $filter = new UserQueryFilter();
            $filterItems = $filter->transform($request);

            $includeDocuments = $request->query('includeDocuments');
            $includeProfiles = $request->query('includeProfiles');

            $users = User::where($filterItems ?? []);

            if (!empty($includeDocuments)) {
                $users->with('documents');
            }

            if (!empty($includeProfiles)) {
                $users->with('profiles');
            }

            return $this->successResponse(
                new UserCollection($users->paginate()->appends($request->query()))
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function show(Request $request, User $user): JsonResponse
    {
        try {
            $includeDocuments = $request->query('includeDocuments');
            $includeProfiles = $request->query('includeProfiles');

            if (!empty($includeDocuments)) {
                $user->loadMissing('documents');
            }

            if (!empty($includeProfiles)) {
                $user->loadMissing('profiles');
            }

            return $this->successResponse(new UserResource($user));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->all());

            return $this->successResponse(new UserResource($user->refresh()));
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id !== Auth::id()) {
            return $this->errorResponse('', 403);
        }

        // @TODO Make it asynchronous and put the logic within an artisan command

        try {
            $profiles = $user->profiles();
            $documents = $user->documents();
            $folders = $user->folders();
            $socials = $user->socials();

            if ($profiles->count() > 0) {
                foreach ($profiles->getResults() as $profile) {
                    /** @var Profile $profile */
                    $profile->anonymize();
                }
            }

            if ($documents->count() > 0) {
                foreach($documents->getResults() as $document) {
                    /** @var Document $document */
                    $document->anonymize();
                }
            }

            if ($folders->count() > 0) {
                foreach ($folders->getResults() as $folder) {
                    /** @var Folder $folder */
                    $folder->anonymize();
                }
            }

            if ($socials->count() > 0) {
                foreach ($socials->getResults() as $social) {
                    /** @var Social $social */
                    Social::destroy($social->id);
                }
            }

            $user->anonymize();

            return $this->successResponse(
                [],
                __('Your request has been taken into account. You will be redirected shortly.')
            );
        } catch (Exception $e) {
            return $this->errorResponse(__('An error occurred when trying to delete your account.'));
        }
    }

    /**
     * RGPD Purpose
     *
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        try {
            $profiles = Auth::user()->profiles();
            $documents = Auth::user()->documents();
            $folders = Auth::user()->folders();
            $socials = Auth::user()->socials();

            $data = [
                'user' => new UserResource(Auth::user()),
                'documents' => new DocumentCollection($documents->getResults()),
                'folders' => new FolderCollection($folders->getResults()),
                'profiles' => new ProfileCollection($profiles->getResults()),
                'socials' => new SocialCollection($socials->getResults())
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return $this->errorResponse('An error occurred when fetching your data. Please try again later.');
        }
    }
}
